# 📊 Dashboard Monitoring Bulog Kancab Ciamis 2026

Dashboard web untuk monitoring pengadaan BULOG (GKP, Jagung, Beras PSO, Pengolahan) 
berbasis **Laravel 13** + **Chart.js**, data real-time dari **Google Sheets**.

## 🚀 Fitur

- **4 Tab Dashboard**: GKP, Jagung, Beras PSO, Pengolahan
- **Export Data**: CSV, Excel (xlsx), dan PDF dengan filter
- **Filter Interaktif**: Bulan, Semester, Wilayah, Pemasok, Gudang
- **Data Lengkap**: Semua kolom dari spreadsheet tersedia di export
- **Format Angka Benar**: Kolom kuantum di Excel menggunakan format number
- **Refresh Data**: Update data real-time dari Google Sheets
- **Responsive Design**: Support mobile dan desktop
- **Tema Dark/Light**: Toggle tema di header

## 🏗 Struktur Project

```
dashboard-bulog/
├── app/Http/Controllers/
│   └── DashboardController.php    # Controller utama + export
├── app/Console/Commands/
│   └── FetchSheetsData.php        # Command: fetch data dari Sheets
├── resources/views/
│   ├── layouts/app.blade.php      # Template layout
│   └── dashboard/index.blade.php  # View dashboard (4 tab)
├── routes/web.php                 # Route definitions
├── fetch-sheets-data.py           # Python script: fetch data dari Sheets
├── get-refresh-token.py           # Script: dapatkan Google refresh token
├── setup-google-credentials.sh    # Setup kredensial Google
├── refresh.sh                     # Shortcut refresh data
├── CHANGELOG.md                   # Riwayat perubahan
├── storage/app/
│   └── dashboard-data.json        # Cache data (auto-generated)
└── vendor/                        # Laravel dependencies
```

## 🚀 Cara Pakai

### 1. Setup Kredensial Google (sekali saja)
```bash
# Jalankan script setup
./setup-google-credentials.sh

# Atau jalankan manual
php artisan sheets:fetch
```

### 2. Jalankan server:
```bash
php artisan serve --port=8080
```

### 3. Buka di browser:
```
http://localhost:8080
```

### 4. Export Data:
Klik tombol **📥 CSV**, **📊 Excel**, atau **📄 PDF** di setiap tab untuk export data yang sedang ditampilkan (termasuk filter).

## 📊 4 Tab Dashboard

| Tab | Sumber Sheet | Data |
|-----|-------------|------|
| 🌾 GKP | `data dashboard GKP` | 4.928 baris, total 118.6M kg |
| 🌽 Jagung | `data dashboard Jagung` | 572 baris, total 1.67M kg |
| 🍚 Beras PSO | `data dashboard beras PSO` | 65 baris, total 1.51M kg |
| 🏭 Pengolahan | `dashboard pengolahan` | 42 mitra, rasio 45.8% |

## 📥 Export Data

### Format yang Didukung
- **CSV**: Text file, bisa dibuka di Excel
- **Excel (xlsx)**: File Excel yang sudah rapi dengan format number
- **PDF**: HTML yang bisa dicetak

### Kolom Export
Semua kolom dari spreadsheet tersedia di export:
- **GKP**: Nomor PO, Nama Pemasok, No IN, QC, Tanggal PO, Kuantum, Wilayah, Semester, Bulan
- **Jagung**: Nomor PO, Nama Pemasok, No IN, QC, Tanggal PO, Kuantum, Wilayah, Semester, Bulan, Polres, Polsek
- **Beras PSO**: Nomor PO, Nama Pemasok, Tanggal PO, Kuantum, Wilayah, Semester, Bulan
- **Pengolahan**: Nama Mitra, Tonase Pengadaan, Tonase Pengolahan, Sisa, Rasio

## 🔧 Konfigurasi

### Google Sheets API
- **Spreadsheet ID:** `16G1AOk9NPkr8qvOmz22bW00V9_WsKWPE66izsoz038E`
- **Sheets:** `data dashboard GKP`, `data dashboard Jagung`, `data dashboard beras PSO`, `dashboard pengolahan`

### Environment Variables (.env)
```bash
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REFRESH_TOKEN=your-refresh-token
```

## 🔄 Auto-Refresh Data

Untuk update data otomatis, tambahkan cron job:
```bash
# Tiap jam
0 * * * * cd ~/Projects/dashboard-bulog && php artisan sheets:fetch
```

## 🛠 Dependencies

- PHP 8.x + Composer
- Python 3.x (untuk setup kredensial)
- PhpSpreadsheet (untuk export Excel)
- Chart.js (CDN)
- Laravel 13

## 📝 Catatan

- Data di-cache di `storage/app/dashboard-data.json`
- Export hanya menampilkan data mentah (tanpa ringkasan)
- Kolom kuantum di Excel menggunakan format number (`#,##0`)
- Untuk edit UI: buka folder ini dengan editor favorit
