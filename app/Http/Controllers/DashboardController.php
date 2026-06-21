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
        if (!file_exists($path)) {
            return ['error' => 'Data file not found. Run: python3 fetch-sheets-data.py'];
        }
        return json_decode(file_get_contents($path), true);
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
        $script = base_path('fetch-sheets-data.py');
        $process = new Process(['python3', $script]);
        $process->setTimeout(60);
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
