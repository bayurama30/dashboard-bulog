<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    protected $apiBase;
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->apiBase = env('OPENROUTER_API_URL', 'https://opencode.ai/zen/v1/chat/completions');
        $this->apiKey = env('OPENROUTER_API_KEY');
        $this->model = env('OPENROUTER_MODEL', 'deepseek-v4-flash-free');
    }

    protected function loadData()
    {
        $path = storage_path('app/dashboard-data.json');
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }
        return null;
    }

    protected function buildSystemPrompt(): string
    {
        $data = $this->loadData();

        if (!$data) {
            return 'Kamu adalah asisten data untuk Dashboard Monitoring Bulog Kancab Ciamis 2026. Data belum tersedia. Beritahu user untuk refresh data terlebih dahulu.';
        }

        $gkp = $data['gkp'] ?? [];
        $jagung = $data['jagung'] ?? [];
        $beras = $data['beras_pso'] ?? [];
        $pengolahan = $data['pengolahan'] ?? [];

        $format = fn($arr) => collect($arr ?? [])->map(fn($v, $k) => "- $k: " . number_format($v, 0, ',', '.') . ' kg')->implode("\n");

        $mitraList = collect($pengolahan['mitra'] ?? [])->map(function ($m) {
            return "- {$m['nama']}: Pengadaan " . number_format($m['pengadaan'], 0, ',', '.') . " kg, Diolah " . number_format($m['pengolahan'], 0, ',', '.') . " kg, Sisa " . number_format($m['sisa'], 0, ',', '.') . " kg, Rasio {$m['rasio']}%";
        })->implode("\n");

        $poToday = '';
        if ($gkp['raw'] ?? []) {
            $today = now()->format('d/m/Y');
            $todayPOs = collect($gkp['raw'])->filter(fn($r) => ($r['tanggal_po'] ?? '') === $today);
            if ($todayPOs->isNotEmpty()) {
                $poToday = "\n\nPO HARI INI (" . $todayPOs->count() . " PO):\n";
                $poToday .= $todayPOs->map(fn($r) => "- {$r['nama_pemasok']} ({$r['wilayah']}): " . number_format($r['qty'] ?? 0, 0, ',', '.') . " kg, Status: " . (($r['no_in'] ?? '') ? 'Selesai' : 'Belum Input'))->implode("\n");
            }
        }

        $pctTarget = round(($gkp['total'] ?? 0) / 74692000 * 100, 1);

        return <<<PROMPT
Kamu adalah asisten data untuk Dashboard Monitoring Bulog Kancab Ciamis 2026.
Tugasmu menjawab pertanyaan tentang data pengadaan komoditas (GKP, Jagung, Beras PSO) dan pengolahan.
Jawab dalam Bahasa Indonesia. Gunakan format angka dengan pemisah ribuan (titik).
Jika ditanya hal di luar data dashboard, katakan bahwa kamu hanya bisa menjawab tentang data dashboard.

=== DATA GKP ===
Total: {$gkp['total']} kg
Target: 74.692.000 kg
Persentase target: {$pctTarget}%

Per Bulan:
{$format($gkp['by_month'] ?? [])}

Per Wilayah:
{$format($gkp['by_wilayah'] ?? [])}

Top Mitra:
{$format($gkp['by_pemasok'] ?? [])}

=== DATA JAGUNG ===
Total: {$jagung['total']} kg

Per Bulan:
{$format($jagung['by_month'] ?? [])}

Per Wilayah:
{$format($jagung['by_wilayah'] ?? [])}

=== DATA BERAS PSO ===
Total: {$beras['total']} kg

Per Bulan:
{$format($beras['by_month'] ?? [])}

Per Gudang:
{$format($beras['by_wilayah'] ?? [])}

=== DATA PENGOLAHAN ===
Total Pengadaan: {$pengolahan['total_pengadaan']} kg
Total Diolah: {$pengolahan['total_olah']} kg
Total Sisa: {$pengolahan['total_sisa']} kg
Rasio Pengolahan: {$pengolahan['rasio']}%
Rata-rata Rendeman: {$pengolahan['avg_rendeman']}%

Detail per Mitra:
{$mitraList}
{$poToday}
PROMPT;
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array|max:20',
            'history.*.role' => 'required|in:user,assistant',
            'history.*.content' => 'required|string|max:2000',
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []);

        $messages = [
            ['role' => 'system', 'content' => $this->buildSystemPrompt()],
        ];

        foreach ($history as $h) {
            $messages[] = ['role' => $h['role'], 'content' => $h['content']];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiBase . '/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 1024,
                ]);

            if ($response->failed()) {
                return response()->json([
                    'ok' => false,
                    'error' => 'API request failed: ' . $response->body(),
                ], 500);
            }

            $body = $response->json();
            $content = $body['choices'][0]['message']['content'] ?? 'Maaf, tidak ada respons dari AI.';

            return response()->json([
                'ok' => true,
                'message' => $content,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => 'Gagal menghubungi AI: ' . $e->getMessage(),
            ], 500);
        }
    }
}
