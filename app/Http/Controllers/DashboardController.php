<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DashboardController extends Controller
{
    protected function loadData()
    {
        $path = storage_path('app/dashboard-data.json');

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            if (isset($data['fetched_at']) && strtotime($data['fetched_at']) > strtotime('-5 minutes')) {
                return $data;
            }
        }

        $fresh = $this->fetchFromSheets();
        if ($fresh !== null) {
            return $fresh;
        }

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
                'header' => ['nomor_po', 'nama_pemasok', 'no_in', 'qc', 'tanggal_po', 'kuantum', 'wilayah', 'semester', 'bulan'],
                'raw' => $raw,
            ],
            'jagung' => [
                'by_month' => ['Januari' => 150000, 'Februari' => 280000, 'Maret' => 320000, 'April' => 250000, 'Mei' => 210000, 'Juni' => 175550],
                'by_wilayah' => ['Kota Tasikmalaya' => 338200, 'Kab. Garut' => 321750, 'Kab. Tasikmalaya' => 289650, 'Kab. Ciamis' => 195400],
                'total' => 1385550,
                'header' => ['nomor_po', 'nama_pemasok', 'no_in', 'qc', 'tanggal_po', 'kuantum', 'wilayah', 'semester', 'bulan', 'polres', 'polsek'],
                'raw' => $raw,
            ],
            'beras_pso' => [
                'by_month' => ['Januari' => 500000, 'Februari' => 600000, 'Maret' => 411750],
                'by_wilayah' => ['KOMPLEKS PERGUDANGAN LINGGA JAYA' => 1023650, 'KOMPLEKS PERGUDANGAN PAMALAYAN' => 337650, 'KOMPLEKS PERGUDANGAN BANJAR' => 120450],
                'total' => 1511750,
                'header' => ['nomor_po', 'nama_pemasok', 'tanggal_po', 'kuantum', 'wilayah', 'semester', 'bulan'],
                'raw' => $raw,
            ],
            'pengolahan' => [
                'mitra' => [
                    ['nama' => 'CV. Berkah Abadi CMS', 'pengadaan' => 25000000, 'pengadaan_beras' => 12500000, 'pengolahan' => 12000000, 'pengolahan_beras' => 6000000, 'sisa' => 13000000, 'sisa_beras' => 6500000, 'rasio' => 48.0],
                    ['nama' => 'CV. Mitra Tani', 'pengadaan' => 20000000, 'pengadaan_beras' => 10000000, 'pengolahan' => 8000000, 'pengolahan_beras' => 4000000, 'sisa' => 12000000, 'sisa_beras' => 6000000, 'rasio' => 40.0],
                    ['nama' => 'PD. Sumber Pangan', 'pengadaan' => 18000000, 'pengadaan_beras' => 9000000, 'pengolahan' => 6000000, 'pengolahan_beras' => 3000000, 'sisa' => 12000000, 'sisa_beras' => 6000000, 'rasio' => 33.3],
                ],
                'header' => ['nama_mitra', 'tonase_pengadaan_gkp', 'tonase_pengadaan_setara_beras', 'tonase_pengolahan_gkp', 'tonase_pengolahan_setara_beras', 'sisa_belum_pengolahan_gkp', 'sisa_belum_pengolahan_setara_beras'],
                'total_pengadaan' => 108662094,
                'total_pengadaan_beras' => 54331047,
                'total_olah' => 37466468,
                'total_olah_beras' => 18733234,
                'total_sisa' => 66196804,
                'total_sisa_beras' => 33098402,
                'rasio' => 34.5,
                'avg_rendeman' => 51.1,
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
            $error = trim($process->getErrorOutput());
            if (empty($error)) {
                $error = trim($process->getOutput());
            }
            return response()->json([
                'ok' => false,
                'error' => $error ?: 'Gagal memperbarui data. Pastikan kredensial Google tersedia.',
                'data' => $this->loadData(),
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'message' => trim($process->getOutput()),
            'data' => $this->loadData(),
        ]);
    }

    public function export($type, $tab)
    {
        $data = $this->loadData();
        $filters = request()->all();

        if ($type === 'csv') {
            return $this->exportCsv($data, [], $tab, $filters);
        } elseif ($type === 'xlsx') {
            return $this->exportExcel($data, [], $tab, $filters);
        }

        return $this->exportPdf($data, [], $tab, $filters);
    }

    public function exportWithFilters($type, $tab, $filters = '')
    {
        $data = $this->loadData();
        $filterArray = [];

        if ($filters) {
            parse_str($filters, $filterArray);
        }

        if ($type === 'csv') {
            return $this->exportCsv($data, [], $tab, $filterArray);
        } elseif ($type === 'xlsx') {
            return $this->exportExcel($data, [], $tab, $filterArray);
        }

        return $this->exportPdf($data, [], $tab, $filterArray);
    }

    protected function applyFiltersToRaw(array $raw, string $tab, array $filters): array
    {
        if (empty($filters) || empty($raw)) {
            return $raw;
        }

        $rows = $raw;

        $bulan = $filters['bulan'] ?? '';
        $semester = $filters['semester'] ?? '';
        $wilayah = $filters['wilayah'] ?? '';
        $pemasok = $filters['pemasok'] ?? '';
        $gudang = $filters['gudang'] ?? '';

        $semesterMonths = [
            '1' => ['Januari','Februari','Maret','April','Mei','Juni'],
            '2' => ['Juli','Agustus','September','Oktober','November','Desember']
        ];

        foreach ($rows as $key => $row) {
            $include = true;

            if ($bulan && ($row['bulan'] ?? '') !== $bulan) {
                $include = false;
            } elseif ($semester && isset($semesterMonths[$semester]) && !in_array($row['bulan'] ?? '', $semesterMonths[$semester])) {
                $include = false;
            }

            if ($include && $wilayah && ($row['wilayah'] ?? '') !== $wilayah) {
                $include = false;
            }

            if ($include && $pemasok && ($row['pemasok'] ?? '') !== $pemasok) {
                $include = false;
            }

            if ($include && $gudang && ($row['gudang'] ?? '') !== $gudang) {
                $include = false;
            }

            if (!$include) {
                unset($rows[$key]);
            }
        }

        return array_values($rows);
    }

    /**
     * Get raw data for a given tab, handling pengolahan specially
     * (which stores data in 'mitra' instead of 'raw').
     */
    protected function getTabRawData(array $data, string $tab, array $filters): array
    {
        if ($tab === 'pengolahan') {
            $raw = $data['pengolahan']['mitra'] ?? [];
            $search = $filters['search'] ?? '';
            $mitra = $filters['mitra'] ?? '';
            if (!empty($mitra)) {
                $raw = array_filter($raw, function ($m) use ($mitra) {
                    return ($m['nama'] ?? '') === $mitra;
                });
            } elseif (!empty($search)) {
                $raw = array_filter($raw, function ($m) use ($search) {
                    return stripos($m['nama'] ?? '', $search) !== false;
                });
            }
            return array_values($raw);
        }

        return $this->applyFiltersToRaw($data[$tab]['raw'] ?? [], $tab, $filters);
    }

    protected function exportExcel(array $data, array $summary, string $tab, array $filters = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [];
        $rows = [];

        if (in_array($tab, ['gkp', 'jagung', 'beras_pso', 'pengolahan'])) {
            $header = $data[$tab]['header'] ?? [];
            $headers = array_map(function($h) {
                return $this->humanizeHeader($h);
            }, $header);

            $raw = $this->getTabRawData($data, $tab, $filters);

            foreach ($raw as $row) {
                $rowData = [];
                foreach ($headers as $idx => $h) {
                    $colKey = isset($header[$idx]) ? $this->sanitizeHeader($header[$idx]) : 'col_' . $idx;
                    $val = $row[$colKey] ?? '';
                    // Format angka: hapus titik pemisah ribuan
                    if (is_numeric($val) && strpos($val, '.') !== false) {
                        $val = str_replace('.', '', $val);
                    }
                    $rowData[] = $val;
                }
                $rows[] = $rowData;
            }
        }

        $colLetter = 'A';
        $numericColumns = [];
        foreach ($headers as $idx => $header) {
            $sheet->getCell($colLetter . '1')->setValue($header);
            $sheet->getStyle($colLetter . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            ]);
            // Identify numeric columns (kuantum, pengadaan, diolah, sisa, rasio)
            $lowerHeader = strtolower($header);
            if (in_array($lowerHeader, ['kuantum', 'tonase_pengadaan_gkp', 'tonase_pengadaan_setara_beras', 'tonase_pengolahan_gkp', 'tonase_pengolahan_setara_beras', 'sisa_belum_pengolahan_gkp', 'sisa_belum_pengolahan_setara_beras', 'rasio'])) {
                $numericColumns[$colLetter] = $lowerHeader === 'rasio' ? '#,##0.0' : '#,##0';
            }
            $colLetter++;
        }

        $rowNum = 2;
        foreach ($rows as $rowData) {
            $colLetter = 'A';
            foreach ($rowData as $cellValue) {
                $cell = $sheet->getCell($colLetter . $rowNum);
                if (isset($numericColumns[$colLetter])) {
                    // Use numeric type for numeric columns
                    $numericValue = is_numeric($cellValue) ? (float) $cellValue : 0;
                    $cell->setValue($numericValue);
                    $cell->getStyle()->getNumberFormat()->setFormatCode($numericColumns[$colLetter]);
                    $sheet->getStyle($colLetter . $rowNum)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                } else {
                    $cell->setValueExplicit($cellValue, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->getStyle($colLetter . $rowNum)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ]);
                }
                $colLetter++;
            }
            $rowNum++;
        }

        $maxCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        foreach (range('A', $maxCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A2');
        $sheet->setTitle(ucfirst($tab));

        $filename = 'dashboard-' . $tab . '-' . now()->format('Y-m-d') . '.xlsx';
        if (!empty($filters)) {
            $filterStr = implode('-', array_filter($filters));
            $filename = 'dashboard-' . $tab . '-' . $filterStr . '-' . now()->format('Y-m-d') . '.xlsx';
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    protected function sanitizeHeader(string $header): string
    {
        $header = trim($header);
        $header = strtolower($header);
        $header = preg_replace('/[^a-z0-9\s_]/', '', $header);
        $header = preg_replace('/\s+/', '_', $header);
        return $header ?: 'col';
    }

    protected function humanizeHeader(string $header): string
    {
        $header = trim($header);
        $header = preg_replace('/[_-]/', ' ', $header);
        $header = ucwords($header);
        return $header;
    }

    protected function exportCsv(array $data, array $summary, string $tab, array $filters = [])
    {
        $filename = 'dashboard-' . $tab . '-' . now()->format('Y-m-d') . '.csv';
        if (!empty($filters)) {
            $filterStr = implode('-', array_filter($filters));
            $filename = 'dashboard-' . $tab . '-' . $filterStr . '-' . now()->format('Y-m-d') . '.csv';
        }

        $header = $data[$tab]['header'] ?? [];
        $headers = array_map(function($h) {
            return $this->humanizeHeader($h);
        }, $header);

        $raw = $this->getTabRawData($data, $tab, $filters);

        $rows = [];
        if (!empty($headers)) {
            $rows[] = $headers;
            foreach ($raw as $row) {
                $rowData = [];
                foreach ($headers as $idx => $h) {
                    $colKey = isset($header[$idx]) ? $this->sanitizeHeader($header[$idx]) : 'col_' . $idx;
                    $val = $row[$colKey] ?? '';
                    // Format angka: hapus titik pemisah ribuan
                    if (is_numeric($val) && strpos($val, '.') !== false) {
                        $val = str_replace('.', '', $val);
                    }
                    $rowData[] = $val;
                }
                $rows[] = $rowData;
            }
        } else {
            $rows[] = ['Bulan', 'Wilayah', 'Pemasok', 'Kuantum (kg)'];
            foreach ($raw as $row) {
                $rows[] = [
                    $row['bulan'] ?? '',
                    $row['wilayah'] ?? '',
                    $row['pemasok'] ?? '',
                    $row['qty'] ?? 0
                ];
            }
        }

        $callback = function() use ($rows) {
            $file = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    protected function exportPdf(array $data, array $summary, string $tab, array $filters = [])
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
            body{font-family:Arial,sans-serif;margin:20px;color:#333;font-size:12px}
            h1,h2,h3{color:#4f46e5;margin:10px 0}
            table{border-collapse:collapse;width:100%;margin:10px 0}
            th,td{border:1px solid #ddd;padding:8px;text-align:left;font-size:11px}
            th{background:#f3f4f6;font-weight:bold}
            tr:nth-child(even){background:#fafafa}
            .filter-info{background:#e0f2fe;border:1px solid #0284c7;border-radius:8px;padding:10px;margin:10px 0;font-size:11px}
        </style></head><body>';

        $html .= '<h1>Dashboard Monitoring Bulog Kancab Ciamis 2026</h1>';
        $html .= '<p>Tanggal Export: ' . now()->format('d F Y H:i') . '</p>';

        if (!empty($filters)) {
            $html .= '<div class="filter-info">';
            $html .= '<strong>Filter yang diterapkan:</strong><br>';
            foreach ($filters as $key => $val) {
                if (!empty($val)) {
                    $labels = [
                        'bulan' => 'Bulan',
                        'semester' => 'Semester',
                        'wilayah' => 'Wilayah',
                        'pemasok' => 'Pemasok',
                        'gudang' => 'Gudang',
                        'mitra' => 'Mitra',
                        'search' => 'Pencarian',
                    ];
                    $label = $labels[$key] ?? $key;
                    $displayVal = $val;
                    if ($key === 'semester') {
                        $displayVal = 'Semester ' . $val . ' (' . ($val == 1 ? 'Jan-Jun' : 'Jul-Des') . ')';
                    }
                    $html .= htmlspecialchars($label) . ': ' . htmlspecialchars($displayVal) . '<br>';
                }
            }
            $html .= '</div>';
        }

        $header = $data[$tab]['header'] ?? [];
        $headers = array_map(function($h) {
            return $this->humanizeHeader($h);
        }, $header);

        $raw = $this->getTabRawData($data, $tab, $filters);

        $html .= '<h2>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $tab))) . '</h2>';
        $html .= '<table><tr>';
        foreach ($headers as $h) {
            $html .= '<th>' . htmlspecialchars($h) . '</th>';
        }
        $html .= '</tr>';

        foreach ($raw as $row) {
            $html .= '<tr>';
            foreach ($headers as $idx => $h) {
                $colKey = isset($header[$idx]) ? $this->sanitizeHeader($header[$idx]) : 'col_' . $idx;
                $val = $row[$colKey] ?? '';
                // Format angka: hapus titik pemisah ribuan
                if (is_numeric($val) && strpos($val, '.') !== false) {
                    $val = str_replace('.', '', $val);
                }
                $html .= '<td>' . htmlspecialchars($val) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        $html .= '</body></html>';

        $filename = 'dashboard-' . $tab . '-' . now()->format('Y-m-d') . '.pdf';
        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
