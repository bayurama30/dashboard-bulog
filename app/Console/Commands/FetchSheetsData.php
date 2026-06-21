<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Client;
use Google\Service\Sheets;

class FetchSheetsData extends Command
{
    protected $signature = 'sheets:fetch';
    protected $description = 'Fetch data from Google Sheets and save as JSON';

    public function handle(): int
    {
        $client = new Client;

        // Auth from env vars (Railway) or local token file
        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $refreshToken = env('GOOGLE_REFRESH_TOKEN');

        if ($clientId && $clientSecret && $refreshToken) {
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->refreshToken($refreshToken);
        } else {
            $tokenPath = getenv('HOME') ?: exec('echo $HOME');
            $tokenPath = rtrim($tokenPath ?: '/root', '/') . '/.hermes/google_token_pengadaan.json';
            if (!file_exists($tokenPath)) {
                $this->error('No Google credentials found. Set GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REFRESH_TOKEN env vars.');
                return 1;
            }
            $tokenData = json_decode(file_get_contents($tokenPath), true);
            $client->setClientId($tokenData['client_id']);
            $client->setClientSecret($tokenData['client_secret']);
            $client->refreshToken($tokenData['refresh_token']);
        }

        $client->addScope(Sheets::SPREADSHEETS_READONLY);
        $service = new Sheets($client);

        $sid = '16G1AOk9NPkr8qvOmz22bW00V9_WsKWPE66izsoz038E';
        $output = storage_path('app/dashboard-data.json');

        // Fetch helper
        $fetch = function (string $range) use ($service, $sid): array {
            $result = $service->spreadsheets_values->get($sid, $range);
            $rows = $result->getValues() ?? [];
            return [count($rows) > 0 ? $rows[0] : [], array_slice($rows, 1)];
        };

        // Month order
        $monthOrder = array_flip([
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ]);

        $sortByMonth = function (array $map) use ($monthOrder): array {
            uksort($map, fn($a, $b) => ($monthOrder[$a] ?? 99) <=> ($monthOrder[$b] ?? 99));
            return $map;
        };

        $parseNum = fn(string $s): float => (float) str_replace(['.', ','], ['', '.'], $s);

        // === GKP ===
        [, $data] = $fetch("'data dashboard GKP'");
        $byMonth = []; $byWilayah = []; $byPemasok = []; $raw = [];
        foreach ($data as $row) {
            if (count($row) < 9) continue;
            try {
                $qty = $parseNum($row[5]);
                $bulan = $row[8]; $wilayah = $row[6]; $pemasok = $row[1];
                $byMonth[$bulan] = ($byMonth[$bulan] ?? 0) + $qty;
                $byWilayah[$wilayah] = ($byWilayah[$wilayah] ?? 0) + $qty;
                $byPemasok[$pemasok] = ($byPemasok[$pemasok] ?? 0) + $qty;
                $raw[] = ['bulan' => $bulan, 'wilayah' => $wilayah, 'pemasok' => $pemasok, 'qty' => $qty];
            } catch (\Throwable) {}
        }
        arsort($byWilayah);
        arsort($byPemasok);
        $gkp = [
            'by_month' => $sortByMonth($byMonth),
            'by_wilayah' => $byWilayah,
            'by_pemasok' => array_slice($byPemasok, 0, 15),
            'total' => array_sum($byMonth),
            'raw' => $raw,
        ];

        // === JAGUNG ===
        [, $data] = $fetch("'data dashboard Jagung'");
        $jm = []; $jw = []; $jraw = [];
        foreach ($data as $row) {
            if (count($row) < 9) continue;
            try {
                $qty = $parseNum($row[5]);
                $bulan = $row[8]; $wilayah = $row[6];
                $jm[$bulan] = ($jm[$bulan] ?? 0) + $qty;
                $jw[$wilayah] = ($jw[$wilayah] ?? 0) + $qty;
                $jraw[] = ['bulan' => $bulan, 'wilayah' => $wilayah, 'qty' => $qty];
            } catch (\Throwable) {}
        }
        arsort($jw);
        $jagung = [
            'by_month' => $sortByMonth($jm),
            'by_wilayah' => $jw,
            'total' => array_sum($jm),
            'raw' => $jraw,
        ];

        // === BERAS PSO ===
        [, $data] = $fetch("'data dashboard beras PSO'");
        $bm = []; $bw = []; $braw = [];
        foreach ($data as $row) {
            if (count($row) < 7) continue;
            try {
                $qty = $parseNum($row[3]);
                $bulan = $row[6]; $gudang = $row[4];
                $bm[$bulan] = ($bm[$bulan] ?? 0) + $qty;
                $bw[$gudang] = ($bw[$gudang] ?? 0) + $qty;
                $braw[] = ['bulan' => $bulan, 'gudang' => $gudang, 'qty' => $qty];
            } catch (\Throwable) {}
        }
        arsort($bw);
        $beras = [
            'by_month' => $sortByMonth($bm),
            'by_wilayah' => $bw,
            'total' => array_sum($bm),
            'raw' => $braw,
        ];

        // === PENGOLAHAN ===
        [, $data] = $fetch("'dashboard pengolahan'");
        $mitra = []; $tp = $to = $ts = 0.0;
        foreach ($data as $row) {
            try {
                $nama = $row[0];
                $tonP = $parseNum($row[1]);
                $tonO = $parseNum($row[3]);
                $tonS = $parseNum($row[5]);
                $tp += $tonP; $to += $tonO; $ts += $tonS;
                $mitra[] = [
                    'nama' => $nama,
                    'pengadaan' => $tonP,
                    'pengolahan' => $tonO,
                    'sisa' => $tonS,
                    'rasio' => $tonP > 0 ? round($tonO / $tonP * 100, 1) : 0,
                ];
            } catch (\Throwable) {}
        }
        usort($mitra, fn($a, $b) => $b['pengolahan'] <=> $a['pengolahan']);
        $pengolahan = [
            'mitra' => $mitra,
            'total_pengadaan' => $tp,
            'total_olah' => $to,
            'total_sisa' => $ts,
            'rasio' => $tp > 0 ? round($to / $tp * 100, 1) : 0,
        ];

        $outputData = [
            'fetched_at' => now()->toIso8601String(),
            'gkp' => $gkp,
            'jagung' => $jagung,
            'beras_pso' => $beras,
            'pengolahan' => $pengolahan,
        ];

        $dir = dirname($output);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        file_put_contents($output, json_encode($outputData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info(sprintf(
            'OK: %d mitra, %s kg GKP, %s kg Jagung, %s kg Beras',
            count($mitra),
            number_format($gkp['total'], 0, ',', '.'),
            number_format($jagung['total'], 0, ',', '.'),
            number_format($beras['total'], 0, ',', '.'),
        ));
        $this->info("Saved to: $output");

        return 0;
    }
}
