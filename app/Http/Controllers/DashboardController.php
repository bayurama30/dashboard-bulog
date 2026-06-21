<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;

class DashboardController extends Controller
{
    protected function loadData()
    {
        $path = storage_path('app/dashboard-data.json');

        // Try cached data first
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            // Refresh if older than 5 minutes
            if (isset($data['fetched_at']) && strtotime($data['fetched_at']) > strtotime('-5 minutes')) {
                return $data;
            }
        }

        // Try fetching fresh data
        $fresh = $this->fetchFromSheets();
        if ($fresh !== null) {
            return $fresh;
        }

        // Return cached even if stale, or fallback
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }

        return $this->fallbackData();
    }

    protected function fetchFromSheets(): ?array
    {
        try {
            $process = new Process(['php', 'artisan', 'sheets:fetch']);
            $process->setTimeout(60);
            $process->setWorkingDirectory(base_path());
            $process->run();

            if ($process->isSuccessful()) {
                $path = storage_path('app/dashboard-data.json');
                if (file_exists($path)) {
                    return json_decode(file_get_contents($path), true);
                }
            }
        } catch (\Throwable) {}

        return null;
    }

    protected function fallbackData()
    {
        $raw = [[
            'bulan' => 'Januari', 'wilayah' => 'Garut', 
            'pemasok' => 'CV. Mitra Tani', 'qty' => 5000000.0
        ]];
        return [
            'fetched_at' => now()->toIso8601String(),
            'gkp' => [
                'by_month' => ['Januari' => 4329764, 'Februari' => 19783468, 'Maret' => 20461859, 'April' => 28336945, 'Mei' => 29387463, 'Juni' => 6362595],
                'by_wilayah' => ['Garut' => 50710000, 'Tasikmalaya' => 24313731, 'Ciamis' => 21869707, 'Pangandaran' => 7393661, 'Banjar' => 4224995],
                'by_pemasok' => ['CV. Berkah Abadi CMS' => 25000000, 'CV. Mitra Tani' => 20000000, 'PD. Sumber Pangan' => 18000000],
                'total' => 108662094,
                'raw' => $raw,
            ],
            'jagung' => [
                'by_month' => ['Januari' => 150000, 'Februari' => 280000, 'Maret' => 320000, 'April' => 250000, 'Mei' => 210000, 'Juni' => 175550],
                'by_wilayah' => ['Kota Tasikmalaya' => 338200, 'Kab. Garut' => 321750, 'Kab. Tasikmalaya' => 289650, 'Kab. Ciamis' => 195400],
                'total' => 1385550,
                'raw' => $raw,
            ],
            'beras_pso' => [
                'by_month' => ['Januari' => 500000, 'Februari' => 600000, 'Maret' => 411750],
                'by_wilayah' => ['KOMPLEKS PERGUDANGAN LINGGA JAYA' => 1023650, 'KOMPLEKS PERGUDANGAN PAMALAYAN' => 337650, 'KOMPLEKS PERGUDANGAN BANJAR' => 120450],
                'total' => 1511750,
                'raw' => $raw,
            ],
            'pengolahan' => [
                'mitra' => [
                    ['nama' => 'CV. Berkah Abadi CMS', 'pengadaan' => 25000000, 'pengolahan' => 12000000, 'sisa' => 13000000, 'rasio' => 48.0],
                    ['nama' => 'CV. Mitra Tani', 'pengadaan' => 20000000, 'pengolahan' => 8000000, 'sisa' => 12000000, 'rasio' => 40.0],
                    ['nama' => 'PD. Sumber Pangan', 'pengadaan' => 18000000, 'pengolahan' => 6000000, 'sisa' => 12000000, 'rasio' => 33.3],
                ],
                'total_pengadaan' => 108662094,
                'total_olah' => 37466468,
                'total_sisa' => 66196804,
                'rasio' => 34.5,
            ],
        ];
    }

    public function index(Request $request)
    {
        $data = $this->loadData();
        $activeTab = $request->query('tab', 'gkp');
        $tabs = [
            'gkp'        => ['label' => '🌾 GKP',        'icon' => 'gkp'],
            'jagung'     => ['label' => '🌽 Jagung',      'icon' => 'jagung'],
            'beras_pso'  => ['label' => '🍚 Beras PSO',   'icon' => 'beras'],
            'pengolahan' => ['label' => '🏭 Pengolahan',  'icon' => 'pengolahan'],
        ];
        return view('dashboard.index', compact('data', 'tabs', 'activeTab'));
    }

    public function data()
    {
        return response()->json($this->loadData());
    }

    public function refresh()
    {
        $process = new Process(['php', 'artisan', 'sheets:fetch']);
        $process->setTimeout(60);
        $process->setWorkingDirectory(base_path());
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json([
                'ok' => false,
                'error' => trim($process->getErrorOutput()),
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'message' => trim($process->getOutput()),
            'data' => $this->loadData(),
        ]);
    }
}
