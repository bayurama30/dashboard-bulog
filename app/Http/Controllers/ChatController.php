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
        $this->apiBase = config('ai.api_url');
        $this->apiKey = config('ai.api_key');
        $this->model = config('ai.model');
    }

    protected function loadData()
    {
        $path = storage_path('app/dashboard-data.json');
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }
        return null;
    }

    protected function formatNumber($n): string
    {
        return number_format($n, 0, ',', '.');
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
        $fetchedAt = $data['fetched_at'] ?? 'N/A';

        // Format summary data
        $format = fn($arr) => collect($arr ?? [])->map(fn($v, $k) => "- $k: " . $this->formatNumber($v) . ' kg')->implode("\n");

        // Format mitra detail
        $mitraList = collect($pengolahan['mitra'] ?? [])->map(function ($m) {
            return "- {$m['nama']}: Pengadaan " . $this->formatNumber($m['pengadaan']) . " kg, Diolah " . $this->formatNumber($m['pengolahan']) . " kg, Sisa " . $this->formatNumber($m['sisa']) . " kg, Rasio {$m['rasio']}%";
        })->implode("\n");

        // Build raw data summaries
        $gkpRawSummary = $this->buildRawSummary($gkp['raw'] ?? [], 'GKP');
        $jagungRawSummary = $this->buildRawSummary($jagung['raw'] ?? [], 'Jagung');
        $berasRawSummary = $this->buildRawSummary($beras['raw'] ?? [], 'Beras PSO');

        // PO Today - always include, even if empty
        $today = now()->format('j/n/Y'); // Format tanpa leading zero sesuai data
        $todayDate = now()->format('d F Y');
        $todayPOs = collect($gkp['raw'] ?? [])->filter(fn($r) => ($r['tanggal_po'] ?? '') === $today);
        $poToday = "\n\n=== PO HARI INI ({$todayDate}) ===\n";
        if ($todayPOs->isNotEmpty()) {
            $poToday .= "Jumlah: " . $todayPOs->count() . " PO\n";
            $poToday .= "Total: " . $this->formatNumber($todayPOs->sum('qty')) . " kg\n";
            $poToday .= $todayPOs->map(function ($r) {
                return "- {$r['nama_pemasok']} ({$r['wilayah']}): " . $this->formatNumber($r['qty'] ?? 0) . " kg | No IN: " . (($r['no_in'] ?? '') ? 'Ada' : 'Belum Input') . " | PO: {$r['nomor_po']}";
            })->implode("\n");
        } else {
            $poToday .= "Tidak ada PO dengan tanggal {$today}.\n";
            $poToday .= "Catatan: PO Hari Ini di dashboard menampilkan PO berdasarkan tanggal PO yang cocok dengan hari ini. ";
            $poToday .= "Jika tidak ada, berarti belum ada PO baru hari ini atau data belum diperbarui.\n";
            // Show latest available date
            $latestDate = collect($gkp['raw'] ?? [])->pluck('tanggal_po')->sort()->last();
            if ($latestDate) {
                $poToday .= "Tanggal PO terakhir yang tersedia: {$latestDate}";
            }
        }

        $pctTarget = round(($gkp['total'] ?? 0) / 74692000 * 100, 1);

        return <<<PROMPT
Kamu adalah asisten data untuk Dashboard Monitoring Bulog Kancab Ciamis 2026.
Tugasmu menjawab pertanyaan tentang data pengadaan komoditas (GKP, Jagung, Beras PSO) dan pengolahan.

ATURAN JAWABAN:
- Jawab SINGKAT dan TO THE POINT (maksimal 3-4 kalimat untuk pertanyaan sederhana)
- Gunakan format angka dengan pemisah ribuan (titik)
- Selalu sebutkan sumber data: "Berdasarkan data dashboard..." atau "Dari spreadsheet..."
- Jika ditanya detail, berikan data dalam format list singkat
- Jika tidak tahu pasti, katakan "Data tidak tersedia di dashboard"
- Gunakan Bahasa Indonesia yang natural
- Untuk pertanyaan "PO Hari Ini": cek bagian "PO HARI INI" di system prompt. Jika "Tidak ada PO", jelaskan bahwa tidak ada PO hari ini dan sebutkan tanggal PO terakhir yang tersedia.
- Selalu refer ke data dashboard dan spreadsheet sebagai sumber informasi

=== INFO DASHBOARD ===
Data diperbarui: {$fetchedAt}
Sumber: Google Spreadsheet Bulog Kancab Ciamis 2026

=== DATA GKP (Gabah Kering Panen) ===
Total: {$this->formatNumber($gkp['total'])} kg
Target: 74.692.000 kg (Capai: {$pctTarget}%)

Per Bulan:
{$format($gkp['by_month'] ?? [])}

Per Wilayah:
{$format($gkp['by_wilayah'] ?? [])}

Top Mitra ({$this->formatNumber(count($gkp['by_pemasok'] ?? []))} mitra):
{$format($gkp['by_pemasok'] ?? [])}

{$gkpRawSummary}

=== DATA JAGUNG ===
Total: {$this->formatNumber($jagung['total'])} kg

Per Bulan:
{$format($jagung['by_month'] ?? [])}

Per Wilayah:
{$format($jagung['by_wilayah'] ?? [])}

{$jagungRawSummary}

=== DATA BERAS PSO ===
Total: {$this->formatNumber($beras['total'])} kg

Per Bulan:
{$format($beras['by_month'] ?? [])}

Per Gudang:
{$format($beras['by_wilayah'] ?? [])}

{$berasRawSummary}

=== DATA PENGOLAHAN ===
Total Pengadaan: {$this->formatNumber($pengolahan['total_pengadaan'])} kg
Total Diolah: {$this->formatNumber($pengolahan['total_olah'])} kg
Total Sisa: {$this->formatNumber($pengolahan['total_sisa'])} kg
Rasio: {$pengolahan['rasio']}%
Rendeman: {$pengolahan['avg_rendeman']}%

Detail Mitra:
{$mitraList}
{$poToday}
PROMPT;
    }

    protected function buildRawSummary(array $raw, string $label): string
    {
        if (empty($raw)) {
            return "Data mentah {$label}: Tidak tersedia";
        }

        $count = count($raw);
        $totalQty = collect($raw)->sum('qty');
        $uniqueWilayah = collect($raw)->pluck('wilayah')->unique()->filter()->values();
        $uniquePemasok = collect($raw)->pluck('pemasok')->unique()->filter()->values();

        // Group by date
        $byDate = collect($raw)->groupBy('tanggal_po')->sortKeys()->map(function ($rows, $date) {
            $total = $rows->sum('qty');
            return "- {$date}: " . $rows->count() . " PO, " . $this->formatNumber($total) . " kg";
        })->implode("\n");

        // Get all POs with full detail (limit to 30 to save tokens)
        $allPOs = collect($raw)->sortByDesc('tanggal_po')->take(30);
        $allPOList = $allPOs->map(function ($r) {
            return "- [{$r['tanggal_po']}] {$r['nama_pemasok']} ({$r['wilayah']}): " . $this->formatNumber($r['qty'] ?? 0) . " kg | No IN: " . (($r['no_in'] ?? '') ? 'Ada' : 'Kosong') . " | PO: {$r['nomor_po']}";
        })->implode("\n");

        // Get POs without No IN (need input)
        $withoutIN = collect($raw)->filter(fn($r) => empty($r['no_in']));
        $withoutINList = $withoutIN->take(15)->map(function ($r) {
            return "- [{$r['tanggal_po']}] {$r['nama_pemasok']} ({$r['wilayah']}): " . $this->formatNumber($r['qty'] ?? 0) . " kg";
        })->implode("\n");

        return <<<RAW

DATA MENTAH {$label} ({$count} PO, {$this->formatNumber($totalQty)} kg):
Wilayah: {$uniqueWilayah->implode(', ')}
Mitra: {$uniquePemasok->implode(', ')}

PO PER TANGGAL:
{$byDate}

DAFTAR PO (30 terbaru):
{$allPOList}

PO BELUM INPUT No IN ({$withoutIN->count()} PO):
{$withoutINList}
RAW;
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
                    'temperature' => config('ai.temperature', 0.7),
                    'max_tokens' => config('ai.max_tokens', 1024),
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
