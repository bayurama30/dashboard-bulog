# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **AI Chat Assistant** — floating button chat di kanan bawah untuk bertanya tentang data dashboard. Menggunakan opencode zen API (MiMo V2.5 Free). Fitur: markdown rendering, conversation history, dark/light theme, mobile responsive.
- **PO Hari Ini** — tabel di tab GKP menampilkan daftar PO yang tanggal PO-nya sama dengan hari ini (berdasarkan waktu refresh data), di-grouping per mitra dengan total kuantum, wilayah, dan status Input Gudang Virtual (Selesai/Belum Input berdasarkan kolom No IN).
- **GKP Target Gauge Chart** — gauge chart di tab GKP menampilkan progress target pengadaan (74,692,000 kg) dengan color coding: hijau (100%+), ungu (75-99%), kuning (50-74%), merah (<50%).
- **GKP Target & Total Scorecards** — scorecard di samping gauge chart menampilkan target (74,692,000 kg) dan total pengadaan real-time.
- **Rendaman Tonak KPI card** di tab Pengolahan — menampilkan rata-rata rendaman tonak pengolahan (weighted average) per mitra.
- **Sisa Beras KPI card** di tab Pengolahan — menampilkan total sisa belum pengolahan setara beras.
- **Interactive chart click-to-filter** — klik pada elemen chart (bar/doughnut) otomatis memfilter data:
  - Tab Pengolahan: klik bar di `olah-mitra`, `olah-fisik`, `olah-rendeman` → filter pencarian mitra
  - Tab GKP: klik segment `gkp-wilayah` → filter wilayah; klik bar `gkp-mitra` → filter pemasok; klik bar bulan → filter bulan
  - Tab Jagung: klik segment `jagung-wilayah` → filter wilayah; klik bar bulan → filter bulan
  - Tab Beras: klik segment `beras-wilayah` → filter gudang; klik bar bulan → filter bulan
- **Toast notification** saat chart diklik — menampilkan info filter yang diterapkan (warna biru untuk info, hijau untuk sukses).
- **Rendeman numerik** ke data mitra di `FetchSheetsData.php` — memungkinkan perhitungan rata-rata tertimbang di JavaScript saat filter diterapkan.
- **`avg_rendeman`** ke output data pengolahan di `FetchSheetsData.php`.
- **`GOOGLE_SPREADSHEET_ID`** environment variable di `.env` dan `.env.example`.
- **`config/google.php`** — file konfigurasi terpusat untuk spreadsheet ID dan sheet names.
- **`CHANGELOG.md`** — dokumentasi perubahan proyek.

### Changed
- **Label KPI tab Pengolahan** — semua label diperpendek dan diperjelas:
  - "Tonase Pengadaan GKP" → "Pengadaan GKP"
  - "Tonase Pengadaan Setara Beras" → "Pengadaan Setara Beras"
  - "Tonase Pengolahan GKP" → "Pengolahan GKP"
  - "Tonase Pengolahan Setara Beras" → "Pengolahan Setara Beras"
  - "Sisa Belum Pengolahan GKP" → "Belum Pengolahan GKP"
  - "Sisa Belum Pengolahan Setara Beras" → "Belum Pengolahan Setara Beras"
- **Angka KPI** — format compact (K/M/B) diterapkan via `fmtNum()` pada page load dan saat filter diterapkan.
- **Layout kartu KPI tab Pengolahan** — 8 kartu sejajar dalam satu baris dengan ukuran yang diperkecil:
  - Grid: `repeat(8, 1fr)` di desktop
  - Font label: 0.52em, font angka: 1.6em
  - Responsive: 2 kolom di mobile (≤768px)
- **`fmtNum()` JavaScript** — diperbarui untuk format compact: <1K as-is, K untuk ribu, M untuk juta, B untuk miliar.
- **`FetchSheetsData.php`** — parsing kolom `rendeman_tonak_pengolahan` (kolom 7) dari format string "51,05%" ke angka.
- **`fallbackData()` di `DashboardController.php`** — ditambahkan nilai `avg_rendeman`, `total_sisa_beras`, dan `rendeman` per mitra.
- **`generate-fallback-data.py`** — konsisten dengan struktur data PHP, menambahkan `rendeman` per mitra dan `avg_rendeman`.
- **`fetch-sheets-data.py`** — menggunakan `os.environ.get('GOOGLE_SPREADSHEET_ID', ...)` untuk spreadsheet ID.
- **`composer.json`** — typo `laravel/pao` → `laravel/pail`.

### Fixed
- **Warna persentase gauge chart** — angka persentase di GKP gauge chart sekarang mengikuti warna gauge (hijau/ungu/kuning/merah).
- **Chart tidak muncul** — syntax error pada Chart constructor (kurung `}` hilang sebelum `)`) di 4 chart: `olah-mitra`, `olah-fisik`, `olah-rendeman`, `gkp-mitra`.
- **Chart click handler tidak berfungsi di Chart.js v4** — diupdate signature onClick callback ke `(event, elements, chart)`.
- **Monthly chart click filter** — klik bar bulan sekarang otomatis set dropdown bulan dan filter data (sebelumnya hanya toast).
- **`showToast` type 'info' undefined** — ditambahkan icon `ℹ️` dan CSS `.toast.info` (warna biru).
- **XSS vulnerability di `exportPdf()`** — `htmlspecialchars()` ditambahkan pada header, filter label, dan tab heading.
- **XSS vulnerability di `exportData()` JavaScript** — fungsi `esc()` ditambahkan dan diterapkan pada semua nilai header/sel CSV, XLSX, PDF.
- **`exportExcel()`** — diganti dari `header()` + `exit` ke `response()->streamDownload()`.
- **Client-side `exportData()` XLSX** — sekarang redirect ke server-side `/export/xlsx/{tab}?filters` untuk file .xlsx yang sesuai via PhpSpreadsheet.
- **Duplicate directory** `dashboard-bulog/dashboard-bulog/` — dihapus.

### Removed
- Direktori duplikat `dashboard-bulog/` (nested).
