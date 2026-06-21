# 📊 Dashboard Monitoring Bulog Kancab Ciamis 2026

Dashboard web untuk monitoring pengadaan BULOG (GKP, Jagung, Beras PSO, Pengolahan) 
berbasis **Laravel 13** + **Chart.js**, data real-time dari **Google Sheets**.

## 🏗 Struktur Project

```
dashboard-bulog/
├── app/Http/Controllers/
│   └── DashboardController.php    # Controller utama
├── resources/views/
│   ├── layouts/app.blade.php      # Template layout
│   └── dashboard/index.blade.php  # View dashboard (4 tab)
├── routes/web.php                 # Route definitions
├── fetch-sheets-data.py           # Python script: fetch data dari Sheets
├── refresh.sh                     # Shortcut refresh data
├── storage/app/
│   └── dashboard-data.json        # Cache data (auto-generated)
└── vendor/                        # Laravel dependencies
```

## 🚀 Cara Pakai

### 1. Jalankan server:
```bash
cd ~/Projects/dashboard-bulog
php artisan serve --port=8080
```

### 2. Buka di browser:
```
http://localhost:8080
```

### 3. Refresh data terbaru dari Sheets:
```bash
./refresh.sh
# atau
python3 fetch-sheets-data.py
```

## 📊 4 Tab Dashboard

| Tab | Sumber Sheet | Data |
|-----|-------------|------|
| 🌾 GKP | `data dashboard GKP` | 4.532 baris, total 108M kg |
| 🌽 Jagung | `data dashboard Jagung` | 485 baris, total 1.3M kg |
| 🍚 Beras PSO | `data dashboard beras PSO` | 66 baris, total 1.5M kg |
| 🏭 Pengolahan | `dashboard pengolahan` | 40 mitra, rasio 34.5% |

## 🔧 Konfigurasi

- **Google Sheets ID:** `16G1AOk9NPkr8qvOmz22bW00V9_WsKWPE66izsoz038E`
- **OAuth Token:** `storage/app/private/google_token_pengadaan.json`
- **Akun Google:** `pengadaanbulogciamis@gmail.com`

## 🔄 Auto-Refresh Data

Untuk update data otomatis, tambahkan cron job:
```bash
# Tiap jam
0 * * * * cd ~/Projects/dashboard-bulog && python3 fetch-sheets-data.py
```

## 🛠 Dependencies

- PHP 8.x + Composer
- Python 3.x + `google-api-python-client`
- Chart.js (CDN)
- Laravel 13

## 📝 Catatan

- Data di-cache di `storage/app/dashboard-data.json`
- Token OAuth auto-refresh via `google_api.py`
- Untuk edit UI: buka folder ini dengan OpenCode / editor favorit
