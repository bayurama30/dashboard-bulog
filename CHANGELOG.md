# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Export Data**: Fitur export data ke tiga format (CSV, Excel, PDF)
  - Export dengan filter (bulan, wilayah, pemasok, semester, gudang)
  - Export hanya data mentah lengkap dari spreadsheet
  - Format Excel dengan kolom kuantum sebagai format number
  - Header yang di-bold dan berwarna di export Excel
  - Auto-size kolom dan freeze header row di Excel
- **Refresh Data**: Perbaikan error handling dan pesan error yang jelas
  - Script setup kredensial Google (`setup-google-credentials.sh`)
  - Script OAuth untuk mendapatkan refresh token (`get-refresh-token.py`)
  - Error message informatif ketika kredensial Google tidak tersedia

### Changed
- **Command `sheets:fetch`**: 
  - Menyimpan semua kolom dari spreadsheet (bukan hanya subset)
  - Menyimpan header spreadsheet ke data JSON
  - Mapping kolom berdasarkan posisi di spreadsheet
- **Export Excel**: 
  - Semua nilai disimpan sebagai string untuk mencegah format angka salah
  - Kolom kuantum dan tonase menggunakan format number (`#,##0`)
  - Kolom rasio menggunakan format number dengan 1 desimal (`#,##0.0`)
- **Export CSV**: Format angka diperbaiki (hapus titik pemisah ribuan)
- **Export PDF**: Semua kolom dari spreadsheet ditampilkan, filter yang diterapkan ditampilkan di atas tabel

### Removed
- **Tab Ringkasan Eksekutif**: Dihapus dari dashboard
  - Method `summaryData()` di controller
  - Method `alertsData()` di controller
  - Tab "📊 Ringkasan" di view
  - Variabel `SUMMARY` di layout
  - JavaScript `drawSummary()` di view
- **Notifikasi/Alert**: Dihapus dari dashboard
  - Deteksi penurunan kuantum
  - Deteksi rasio pengolahan rendah
  - Deteksi mitra rasio rendah

## [1.0.0] - 2026-07-26

### Added
- Dashboard monitoring Bulog Kancab Ciamis 2026
- 4 tab: GKP, Jagung, Beras PSO, Pengolahan
- Data real-time dari Google Sheets
- Chart.js untuk visualisasi data
- Filter interaktif (bulan, semester, wilayah, pemasok, gudang)
- Tema dark/light
- Refresh data manual
- Responsive design
