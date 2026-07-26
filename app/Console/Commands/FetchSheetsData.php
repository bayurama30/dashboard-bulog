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

        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $refreshToken = env('GOOGLE_REFRESH_TOKEN');

        if (!$clientId || !$clientSecret || !$refreshToken) {
            $this->error('No Google credentials found!');
            $this->line('');
            $this->line('Please set the following environment variables in your .env file:');
            $this->line('  GOOGLE_CLIENT_ID=your-client-id');
            $this->line('  GOOGLE_CLIENT_SECRET=your-client-secret');
            $this->line('  GOOGLE_REFRESH_TOKEN=your-refresh-token');
            $this->line('');
            $this->line('Or run: ./setup-google-credentials.sh');
            $this->line('');
            $this->line('Spreadsheet: https://docs.google.com/spreadsheets/d/16G1AOk9NPkr8qvOmz22bW00V9_WsKWPE66izsoz038E/edit');
            $this->line('Sheets: data dashboard GKP, data dashboard Jagung, data dashboard beras PSO, dashboard pengolahan');
            $this->line('');
            $this->line('Using cached data instead...');
            return 1;
        }

        try {
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->refreshToken($refreshToken);
        } catch (\Throwable $e) {
            $this->error('Failed to authenticate with Google: ' . $e->getMessage());
            $this->line('Using cached data instead...');
            return 1;
        }

        $client->addScope(Sheets::SPREADSHEETS_READONLY);
        $service = new Sheets($client);

        $sid = '16G1AOk9NPkr8qvOmz22bW00V9_WsKWPE66izsoz038E';
        $output = storage_path('app/dashboard-data.json');

        $sanitizeHeader = function (string $header): string {
            $header = trim($header);
            $header = strtolower($header);
            $header = preg_replace('/[^a-z0-9\s_]/', '', $header);
            $header = preg_replace('/\s+/', '_', $header);
            return $header ?: 'col';
        };

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
        [$gkpHeader, $data] = $fetch("'data dashboard GKP'");
        $byMonth = []; $byWilayah = []; $byPemasok = []; $raw = [];

        // Define column mappings for GKP (based on spreadsheet structure)
        $gkpColumns = [
            0 => 'nomor_po',
            1 => 'nama_pemasok',
            2 => 'no_in',
            3 => 'qc',
            4 => 'tanggal_po',
            5 => 'kuantum',
            6 => 'wilayah',
            7 => 'semester',
            8 => 'bulan',
        ];

        // Use actual header if available, otherwise use default column names
        $gkpHeaderNames = [];
        for ($i = 0; $i < 9; $i++) {
            $gkpHeaderNames[$i] = isset($gkpHeader[$i]) && $gkpHeader[$i] !== 'Nomor PO'
                ? $gkpHeader[$i]
                : ($gkpColumns[$i] ?? 'col_' . $i);
        }

        foreach ($data as $row) {
            if (count($row) < 9) continue;
            try {
                $qty = $parseNum($row[5]);
                $bulan = $row[8]; $wilayah = $row[6]; $pemasok = $row[1];
                $byMonth[$bulan] = ($byMonth[$bulan] ?? 0) + $qty;
                $byWilayah[$wilayah] = ($byWilayah[$wilayah] ?? 0) + $qty;
                $byPemasok[$pemasok] = ($byPemasok[$pemasok] ?? 0) + $qty;

                $rawRow = [];
                for ($i = 0; $i < count($row); $i++) {
                    $colName = $gkpColumns[$i] ?? 'col_' . $i;
                    $rawRow[$colName] = $row[$i] ?? '';
                }
                $rawRow['bulan'] = $bulan;
                $rawRow['wilayah'] = $wilayah;
                $rawRow['pemasok'] = $pemasok;
                $rawRow['qty'] = $qty;
                $raw[] = $rawRow;
            } catch (\Throwable) {}
        }
        arsort($byWilayah);
        arsort($byPemasok);
        $gkp = [
            'by_month' => $sortByMonth($byMonth),
            'by_wilayah' => $byWilayah,
            'by_pemasok' => array_slice($byPemasok, 0, 15),
            'total' => array_sum($byMonth),
            'header' => array_values($gkpHeaderNames),
            'raw' => $raw,
        ];

        // === JAGUNG ===
        [$jagungHeader, $data] = $fetch("'data dashboard Jagung'");
        $jm = []; $jw = []; $jraw = [];

        $jagungColumns = [
            0 => 'nomor_po',
            1 => 'nama_pemasok',
            2 => 'no_in',
            3 => 'qc',
            4 => 'tanggal_po',
            5 => 'kuantum',
            6 => 'wilayah',
            7 => 'semester',
            8 => 'bulan',
            9 => 'polres',
            10 => 'polsek',
        ];

        $jagungHeaderNames = [];
        for ($i = 0; $i < 11; $i++) {
            $jagungHeaderNames[$i] = isset($jagungHeader[$i]) && $jagungHeader[$i] !== 'Nomor PO'
                ? $jagungHeader[$i]
                : ($jagungColumns[$i] ?? 'col_' . $i);
        }

        foreach ($data as $row) {
            if (count($row) < 9) continue;
            try {
                $qty = $parseNum($row[5]);
                $bulan = $row[8]; $wilayah = $row[6];
                $jm[$bulan] = ($jm[$bulan] ?? 0) + $qty;
                $jw[$wilayah] = ($jw[$wilayah] ?? 0) + $qty;

                $rawRow = [];
                for ($i = 0; $i < count($row); $i++) {
                    $colName = $jagungColumns[$i] ?? 'col_' . $i;
                    $rawRow[$colName] = $row[$i] ?? '';
                }
                $rawRow['bulan'] = $bulan;
                $rawRow['wilayah'] = $wilayah;
                $rawRow['qty'] = $qty;
                $jraw[] = $rawRow;
            } catch (\Throwable) {}
        }
        arsort($jw);
        $jagung = [
            'by_month' => $sortByMonth($jm),
            'by_wilayah' => $jw,
            'total' => array_sum($jm),
            'header' => array_values($jagungHeaderNames),
            'raw' => $jraw,
        ];

        // === BERAS PSO ===
        [$berasHeader, $data] = $fetch("'data dashboard beras PSO'");
        $bm = []; $bw = []; $braw = [];

        $berasColumns = [
            0 => 'nomor_po',
            1 => 'nama_pemasok',
            2 => 'tanggal_po',
            3 => 'kuantum',
            4 => 'wilayah',
            5 => 'semester',
            6 => 'bulan',
        ];

        $berasHeaderNames = [];
        for ($i = 0; $i < 7; $i++) {
            $berasHeaderNames[$i] = isset($berasHeader[$i]) && $berasHeader[$i] !== 'Nomor PO'
                ? $berasHeader[$i]
                : ($berasColumns[$i] ?? 'col_' . $i);
        }

        foreach ($data as $row) {
            if (count($row) < 7) continue;
            try {
                $qty = $parseNum($row[3]);
                $bulan = $row[6]; $gudang = $row[4];
                $bm[$bulan] = ($bm[$bulan] ?? 0) + $qty;
                $bw[$gudang] = ($bw[$gudang] ?? 0) + $qty;

                $rawRow = [];
                for ($i = 0; $i < count($row); $i++) {
                    $colName = $berasColumns[$i] ?? 'col_' . $i;
                    $rawRow[$colName] = $row[$i] ?? '';
                }
                $rawRow['bulan'] = $bulan;
                $rawRow['gudang'] = $gudang;
                $rawRow['qty'] = $qty;
                $braw[] = $rawRow;
            } catch (\Throwable) {}
        }
        arsort($bw);
        $beras = [
            'by_month' => $sortByMonth($bm),
            'by_wilayah' => $bw,
            'total' => array_sum($bm),
            'header' => array_values($berasHeaderNames),
            'raw' => $braw,
        ];

        // === PENGOLAHAN ===
        [$pengolahanHeader, $data] = $fetch("'dashboard pengolahan'");
        $mitra = []; $tp = $to = $ts = 0.0;

        // Dynamic column mapping - use actual headers from spreadsheet
        $pengolahanColumns = [
            0 => 'nama_mitra',
            1 => 'tonase_pengadaan_gkp',
            2 => 'tonase_pengadaan_setara_beras',
            3 => 'pemasukan_fisik_hgl',
            4 => 'selisih_tonase_fisik',
            5 => 'tonase_pengolahan_gkp',
            6 => 'tonase_pengolahan_setara_beras',
            7 => 'rendeman_tonak_pengolahan',
            8 => 'sisa_belum_pengolahan_gkp',
            9 => 'sisa_belum_pengolahan_setara_beras',
            10 => 'pengadaan_beras_pso',
            11 => 'kuantum_gkp_laporan_hasil_giling',
            12 => 'kuantum_gkp_belum_laporan_hasil_giling',
        ];

        // Build header names from actual spreadsheet headers
        $pengolahanHeaderNames = [];
        for ($i = 0; $i < max(count($pengolahanHeader), count($pengolahanColumns)); $i++) {
            $pengolahanHeaderNames[$i] = isset($pengolahanHeader[$i]) && $pengolahanHeader[$i] !== 'Nomor PO'
                ? $pengolahanHeader[$i]
                : ($pengolahanColumns[$i] ?? 'col_' . $i);
        }

        foreach ($data as $row) {
            try {
                $nama = $row[0];
                $tonP = $parseNum($row[1]);
                $tonO = $parseNum($row[5]);
                $tonS = $parseNum($row[8]);
                $tp += $tonP; $to += $tonO; $ts += $tonS;

                $rawRow = [];
                for ($i = 0; $i < count($row); $i++) {
                    $colName = $pengolahanColumns[$i] ?? 'col_' . $i;
                    $rawRow[$colName] = $row[$i] ?? '';
                }
                $rawRow['nama'] = $nama;
                $rawRow['pengadaan'] = $tonP;
                $rawRow['pengolahan'] = $tonO;
                $rawRow['sisa'] = $tonS;
                $rawRow['rasio'] = $tonP > 0 ? round($tonO / $tonP * 100, 1) : 0;
                $mitra[] = $rawRow;
            } catch (\Throwable) {}
        }
        usort($mitra, fn($a, $b) => $b['pengolahan'] <=> $a['pengolahan']);
        $pengolahan = [
            'mitra' => $mitra,
            'header' => array_values($pengolahanHeaderNames),
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
