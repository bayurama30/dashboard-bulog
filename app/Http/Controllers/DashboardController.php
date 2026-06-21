<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
}
