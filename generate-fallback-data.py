#!/usr/bin/env python3
"""Generate fallback dashboard data when Google Sheets is unavailable."""
import json, os
from datetime import datetime

OUTPUT = os.path.join(os.path.dirname(__file__), 'storage', 'app', 'dashboard-data.json')

# Sample data for demo
gkp_raw = []
for bulan in ['Januari','Februari','Maret','April','Mei','Juni']:
    for wilayah, qty in [('Garut',50000000),('Tasikmalaya',24000000),('Ciamis',21000000),('Pangandaran',7000000),('Banjar',4000000)]:
        gkp_raw.append({'bulan':bulan,'wilayah':wilayah,'pemasok':'CV. Mitra Tani','qty':float(qty/6)})

gkp = {
    "by_month": {"Januari":4329764,"Februari":19783468,"Maret":20461859,"April":28336945,"Mei":29387463,"Juni":6362595},
    "by_wilayah": {"Garut":50710000,"Tasikmalaya":24313731,"Ciamis":21869707,"Pangandaran":7393661,"Banjar":4224995},
    "by_pemasok": {"CV. Berkah Abadi CMS":25000000,"CV. Mitra Tani":20000000,"PD. Sumber Pangan":18000000,"CV. Agro Mandiri":15000000,"CV. Karya Tani":12000000},
    "total": 108662094,
    "raw": gkp_raw
}

jagung = {
    "by_month": {"Januari":150000,"Februari":280000,"Maret":320000,"April":250000,"Mei":210000,"Juni":175550},
    "by_wilayah": {"Kota Tasikmalaya":338200,"Kab. Garut":321750,"Kab. Tasikmalaya":289650,"Kab. Ciamis":195400,"Kab. Pangandaran":153600,"Kota Banjar":86950},
    "total": 1385550,
    "raw": [{'bulan':'Januari','wilayah':'Kota Tasikmalaya','qty':150000.0},{'bulan':'Februari','wilayah':'Kab. Garut','qty':280000.0},{'bulan':'Maret','wilayah':'Kab. Tasikmalaya','qty':320000.0}]
}

beras = {
    "by_month": {"Januari":500000,"Februari":600000,"Maret":411750},
    "by_wilayah": {"KOMPLEKS PERGUDANGAN LINGGA JAYA":1023650,"KOMPLEKS PERGUDANGAN PAMALAYAN":337650,"KOMPLEKS PERGUDANGAN BANJAR":120450,"KOMPLEKS PERGUDANGAN SUKAGALIH":30000},
    "total": 1511750,
    "raw": [{'bulan':'Januari','gudang':'KOMPLEKS PERGUDANGAN LINGGA JAYA','qty':500000.0},{'bulan':'Februari','gudang':'KOMPLEKS PERGUDANGAN PAMALAYAN','qty':600000.0}]
}

pengolahan = {
    "mitra": [
        {"nama":"CV. Berkah Abadi CMS","pengadaan":25000000,"pengolahan":12000000,"sisa":13000000,"rasio":48.0},
        {"nama":"CV. Mitra Tani","pengadaan":20000000,"pengolahan":8000000,"sisa":12000000,"rasio":40.0},
        {"nama":"PD. Sumber Pangan","pengadaan":18000000,"pengolahan":6000000,"sisa":12000000,"rasio":33.3},
        {"nama":"CV. Agro Mandiri","pengadaan":15000000,"pengolahan":5000000,"sisa":10000000,"rasio":33.3},
        {"nama":"CV. Karya Tani","pengadaan":12000000,"pengolahan":4000000,"sisa":8000000,"rasio":33.3},
    ],
    "total_pengadaan": 108662094,
    "total_olah": 37466468,
    "total_sisa": 66196804,
    "rasio": 34.5
}

output = {
    "fetched_at": datetime.now().isoformat(),
    "gkp": gkp, "jagung": jagung, "beras_pso": beras, "pengolahan": pengolahan
}

os.makedirs(os.path.dirname(OUTPUT), exist_ok=True)
with open(OUTPUT, 'w') as f:
    json.dump(output, f, indent=2, ensure_ascii=False)

print(f"Sample data generated. Saved to: {OUTPUT}")
