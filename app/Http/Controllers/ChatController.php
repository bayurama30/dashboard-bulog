<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

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

    /**
     * Fetch fresh data from Google Sheets via artisan command.
     */
    protected function fetchFresh(): ?array
    {
        try {
            $process = new Process(['php', 'artisan', 'sheets:fetch']);
            $process->setTimeout(30);
            $process->setWorkingDirectory(base_path());
            $process->run();

            if ($process->isSuccessful()) {
                $path = storage_path('app/dashboard-data.json');
                if (file_exists($path)) {
                    return json_decode(file_get_contents($path), true);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('AI Chat: Gagal fetch fresh data: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Load data with auto-refresh if older than 1 minute.
     * Returns: ['data' => array, 'is_fresh' => bool, 'fetched_at' => string]
     */
    protected function loadDataWithFreshness(): array
    {
        $path = storage_path('app/dashboard-data.json');
        $isFresh = false;

        // If file doesn't exist, fetch fresh
        if (!file_exists($path)) {
            $data = $this->fetchFresh();
            if ($data) {
                return ['data' => $data, 'is_fresh' => true, 'fetched_at' => $data['fetched_at'] ?? now()->toIso8601String()];
            }
            return ['data' => null, 'is_fresh' => false, 'fetched_at' => null];
        }

        $data = json_decode(file_get_contents($path), true);
        $fetchedAt = $data['fetched_at'] ?? null;

        // If no timestamp or data older than 1 minute, fetch fresh
        if (!$fetchedAt || strtotime($fetchedAt) < strtotime('-1 minute')) {
            $fresh = $this->fetchFresh();
            if ($fresh) {
                return ['data' => $fresh, 'is_fresh' => true, 'fetched_at' => $fresh['fetched_at'] ?? now()->toIso8601String()];
            }
            // Fallback to cached data
            return ['data' => $data, 'is_fresh' => false, 'fetched_at' => $fetchedAt];
        }

        // Data is fresh (< 1 minute)
        return ['data' => $data, 'is_fresh' => false, 'fetched_at' => $fetchedAt];
    }

    protected function formatNumber($n): string
    {
        return number_format($n, 0, ',', '.');
    }

    protected function buildSystemPrompt(array $data): string
    {
        $gkp = $data['gkp'] ?? [];
        $jagung = $data['jagung'] ?? [];
        $beras = $data['beras_pso'] ?? [];
        $pengolahan = $data['pengolahan'] ?? [];
        $fetchedAt = $data['fetched_at'] ?? 'N/A';

        // Build comprehensive data for each commodity
        $gkpDetail = $this->buildCommodityDetail($gkp['raw'] ?? [], 'GKP', $gkp, 74692000);
        $jagungDetail = $this->buildCommodityDetail($jagung['raw'] ?? [], 'Jagung', $jagung, 0);
        $berasDetail = $this->buildCommodityDetail($beras['raw'] ?? [], 'Beras PSO', $beras, 0);

        // Pengolahan detail
        $pengolahanDetail = $this->buildPengolahanDetail($pengolahan);

        // PO Today - group by nomor_po
        $today = now()->format('j/n/Y');
        $todayDate = now()->format('d F Y');
        $todayRows = collect($gkp['raw'] ?? [])->filter(fn($r) => ($r['tanggal_po'] ?? '') === $today);
        $todayPOs = $todayRows->groupBy('nomor_po')->map(function ($rows, $po) {
            return [
                'nomor_po' => $po,
                'nama_pemasok' => $rows->first()['nama_pemasok'] ?? '-',
                'wilayah' => $rows->first()['wilayah'] ?? '-',
                'qty' => $rows->sum('qty'),
                'has_in' => $rows->contains(fn($r) => !empty($r['no_in'])),
            ];
        });
        $poToday = "PO HARI INI ({$todayDate}):\n";
        if ($todayPOs->isNotEmpty()) {
            $poToday .= "Ada " . $todayPOs->count() . " PO hari ini, total " . $this->formatNumber($todayPOs->sum('qty')) . " kg\n";
            $poToday .= $todayPOs->map(fn($p) => "- [{$p['nomor_po']}] {$p['nama_pemasok']} ({$p['wilayah']}): " . $this->formatNumber($p['qty']) . " kg, No IN: " . ($p['has_in'] ? 'Ada' : 'Belum'))->implode("\n");
        } else {
            $poToday .= "Tidak ada PO hari ini.\n";
            $latestDate = collect($gkp['raw'] ?? [])->pluck('tanggal_po')->sort()->last();
            $poToday .= "Tanggal PO terakhir: {$latestDate}";
        }

        return <<<PROMPT
Kamu adalah asisten data Dashboard Monitoring Bulog Kancab Ciamis 2026.
Data dari Google Spreadsheet, diperbarui: {$fetchedAt}

PENTING TENTANG PO:
- 1 PO = 1 nomor PO (kolom nomor_po)
- 1 nomor PO biasanya untuk 1 mitra per hari
- Data spreadsheet bisa punya beberapa baris untuk 1 nomor PO (duplikat), jangan hitung sebagai PO terpisah
- Selalu kelompokkan berdasarkan nomor_po saat menghitung jumlah PO

ATURAN:
- Jawab SINGKAT, TO THE POINT (2-4 kalimat)
- Format angka: titik sebagai pemisah ribuan (contoh: 1.234.567)
- Selalu sebut "Berdasarkan data dashboard..." atau "Dari spreadsheet..."
- Untuk data spesifik (PO tertentu, mitra tertentu), cari di data mentah yang diberikan
- Untuk pertanyaan PO per tanggal: cek bagian "PER TANGGAL LENGKAP" di setiap komoditas
- Jika data tidak ditemukan, katakan "Data tidak ditemukan di spreadsheet"
- Gunakan Bahasa Indonesia

{$poToday}

{$gkpDetail}

{$jagungDetail}

{$berasDetail}

{$pengolahanDetail}
PROMPT;
    }

    protected function buildCommodityDetail(array $raw, string $label, array $summary, int $target): string
    {
        $poCount = collect($raw)->pluck('nomor_po')->unique()->count();
        $totalQty = collect($raw)->sum('qty');

        // Unique values
        $wilayah = collect($raw)->pluck('wilayah')->unique()->filter()->sort()->values();
        $pemasok = collect($raw)->pluck('pemasok')->unique()->filter()->sort()->values();

        // By month - count unique POs
        $byMonth = collect($raw)->groupBy('bulan')->map(function ($rows, $bulan) {
            $po = $rows->pluck('nomor_po')->unique()->count();
            return "{$bulan}: {$po} PO, " . $this->formatNumber($rows->sum('qty')) . " kg";
        })->implode("\n");

        // By wilayah - count unique POs
        $byWilayah = collect($raw)->groupBy('wilayah')->map(function ($rows, $w) {
            $po = $rows->pluck('nomor_po')->unique()->count();
            return "{$w}: {$po} PO, " . $this->formatNumber($rows->sum('qty')) . " kg";
        })->implode("\n");

        // Top 10 mitra by qty
        $topMitra = collect($raw)->groupBy('pemasok')->map(function ($rows, $p) {
            return ['nama' => $p, 'po' => $rows->pluck('nomor_po')->unique()->count(), 'qty' => $rows->sum('qty')];
        })->sortByDesc('qty')->take(10)->map(function ($p) {
            return "{$p['nama']}: {$p['po']} PO, " . $this->formatNumber($p['qty']) . " kg";
        })->implode("\n");

        // Stats
        $avgQty = $poCount > 0 ? round($totalQty / $poCount) : 0;
        $withoutIN = collect($raw)->filter(fn($r) => empty($r['no_in']))->pluck('nomor_po')->unique()->count();

        $targetInfo = '';
        if ($target > 0) {
            $pct = round($totalQty / $target * 100, 1);
            $targetInfo = " | Target: " . $this->formatNumber($target) . " kg ({$pct}%)";
        }

        return <<<DETAIL
=== {$label} ===
Total: {$poCount} PO, {$this->formatNumber($totalQty)} kg{$targetInfo}
Rata-rata: {$this->formatNumber($avgQty)} kg/PO
Wilayah: {$wilayah->implode(', ')}
Mitra: {$pemasok->count()} mitra | Belum input No IN: {$withoutIN} PO

Per Bulan:
{$byMonth}

Per Wilayah:
{$byWilayah}

Top 10 Mitra:
{$topMitra}
DETAIL;
    }

    protected function buildPengolahanDetail(array $pengolahan): string
    {
        $mitraList = collect($pengolahan['mitra'] ?? [])->map(function ($m) {
            return "- {$m['nama']}: Pengadaan " . $this->formatNumber($m['pengadaan']) . " kg, Diolah " . $this->formatNumber($m['pengolahan']) . " kg, Sisa " . $this->formatNumber($m['sisa']) . " kg, Rasio {$m['rasio']}%, Rendeman " . ($m['rendeman'] ?? 0) . "%";
        })->implode("\n");

        $totalPengadaan = $pengolahan['total_pengadaan'] ?? 0;
        $totalOlah = $pengolahan['total_olah'] ?? 0;
        $totalSisa = $pengolahan['total_sisa'] ?? 0;

        return <<<PENGOLAHAN
=== PENGOLAHAN ===
Total Pengadaan: {$this->formatNumber($totalPengadaan)} kg
Total Diolah: {$this->formatNumber($totalOlah)} kg
Total Sisa: {$this->formatNumber($totalSisa)} kg
Rasio: {$pengolahan['rasio']}%
Rendeman: {$pengolahan['avg_rendeman']}%

DETAIL MITRA ({$this->formatNumber(count($pengolahan['mitra'] ?? []))} mitra):
{$mitraList}
PENGOLAHAN;
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

        // Load data with auto-refresh
        $result = $this->loadDataWithFreshness();
        $data = $result['data'];
        $dataRefreshed = $result['is_fresh'];
        $fetchedAt = $result['fetched_at'];

        if (!$data) {
            return response()->json([
                'ok' => false,
                'error' => 'Data belum tersedia. Silakan refresh data terlebih dahulu.',
            ], 500);
        }

        $messages = [
            ['role' => 'system', 'content' => $this->buildSystemPrompt($data)],
        ];

        foreach ($history as $h) {
            $messages[] = ['role' => $h['role'], 'content' => $h['content']];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiBase . '/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => config('ai.temperature', 0.5),
                    'max_tokens' => config('ai.max_tokens', 1536),
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
                'data_refreshed' => $dataRefreshed,
                'data_updated' => $fetchedAt ? date('d M Y H:i', strtotime($fetchedAt)) : null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => 'Gagal menghubungi AI: ' . $e->getMessage(),
            ], 500);
        }
    }
}
