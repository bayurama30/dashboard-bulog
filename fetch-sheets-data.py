#!/usr/bin/env python3
"""Fetch data dari Google Sheets dan output JSON untuk Laravel dashboard."""
import sys, json, os
sys.path.insert(0, os.path.expanduser('~/.hermes/skills/productivity/google-workspace/scripts'))
from google_api import get_credentials, _set_account
from googleapiclient.discovery import build
from collections import defaultdict

_set_account('pengadaan')
creds = get_credentials()
service = build('sheets', 'v4', credentials=creds)

# Month order mapping (for sorting without number prefix)
MONTH_ORDER = {m: i for i, m in enumerate([
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
])}

def sort_by_month(items_dict):
    """Sort dict by month name chronologically."""
    return dict(sorted(items_dict.items(), key=lambda x: MONTH_ORDER.get(x[0], 99)))

SID = '16G1AOk9NPkr8qvOmz22bW00V9_WsKWPE66izsoz038E'
OUTPUT = os.path.join(os.path.dirname(__file__), 'storage', 'app', 'dashboard-data.json')

def fetch(name, range_spec=None):
    rng = range_spec or name
    result = service.spreadsheets().values().get(spreadsheetId=SID, range=f"'{rng}'").execute()
    rows = result.get('values', [])
    return rows[0] if rows else [], rows[1:] if len(rows) > 1 else []

# === GKP ===
_, data = fetch('data dashboard GKP')
by_month = defaultdict(float)
by_wilayah = defaultdict(float)
by_pemasok = defaultdict(float)
raw = []
for row in data:
    if len(row) >= 9:
        try:
            qty = float(row[5].replace('.','').replace(',','.'))
            bulan = row[8]
            wilayah = row[6]
            pemasok = row[1]
            by_month[bulan] += qty
            by_wilayah[wilayah] += qty
            by_pemasok[pemasok] += qty
            raw.append({'bulan': bulan, 'wilayah': wilayah, 'pemasok': pemasok, 'qty': qty})
        except: pass

gkp = {
    "by_month": sort_by_month(by_month),
    "by_wilayah": dict(sorted(by_wilayah.items(), key=lambda x: x[1], reverse=True)),
    "by_pemasok": dict(sorted(by_pemasok.items(), key=lambda x: x[1], reverse=True)[:15]),
    "total": sum(by_month.values()),
    "raw": raw
}

# === JAGUNG ===
_, data = fetch('data dashboard Jagung')
jm = defaultdict(float)
jw = defaultdict(float)
jraw = []
for row in data:
    if len(row) >= 9:
        try:
            qty = float(row[5].replace('.','').replace(',','.'))
            bulan = row[8]
            wilayah = row[6]
            jm[bulan] += qty
            jw[wilayah] += qty
            jraw.append({'bulan': bulan, 'wilayah': wilayah, 'qty': qty})
        except: pass

jagung = {
    "by_month": sort_by_month(jm),
    "by_wilayah": dict(sorted(jw.items(), key=lambda x: x[1], reverse=True)),
    "total": sum(jm.values()),
    "raw": jraw
}

# === BERAS PSO ===
_, data = fetch('data dashboard beras PSO')
bm = defaultdict(float)
bw = defaultdict(float)
braw = []
for row in data:
    if len(row) >= 7:
        try:
            qty = float(row[3].replace('.','').replace(',','.'))
            bulan = row[6]
            gudang = row[4]
            bm[bulan] += qty
            bw[gudang] += qty
            braw.append({'bulan': bulan, 'gudang': gudang, 'qty': qty})
        except: pass

beras = {
    "by_month": sort_by_month(bm),
    "by_wilayah": dict(sorted(bw.items(), key=lambda x: x[1], reverse=True)),
    "total": sum(bm.values()),
    "raw": braw
}

# === PENGOLAHAN ===
_, data = fetch('dashboard pengolahan')
mitra = []
tp = to = ts = 0
for row in data:
    try:
        nama = row[0]
        ton_p = float(row[1].replace('.','').replace(',','.'))
        ton_o = float(row[3].replace('.','').replace(',','.'))
        ton_s = float(row[5].replace('.','').replace(',','.'))
        tp += ton_p; to += ton_o; ts += ton_s
        mitra.append({
            "nama": nama,
            "pengadaan": ton_p,
            "pengolahan": ton_o,
            "sisa": ton_s,
            "rasio": round(ton_o/ton_p*100,1) if ton_p > 0 else 0
        })
    except: pass

mitra.sort(key=lambda x: x['pengolahan'], reverse=True)
pengolahan = {
    "mitra": mitra,
    "total_pengadaan": tp,
    "total_olah": to,
    "total_sisa": ts,
    "rasio": round(to/tp*100,1) if tp > 0 else 0
}

output = {
    "fetched_at": __import__('datetime').datetime.now().isoformat(),
    "gkp": gkp, "jagung": jagung, "beras_pso": beras, "pengolahan": pengolahan
}

os.makedirs(os.path.dirname(OUTPUT), exist_ok=True)
with open(OUTPUT, 'w') as f:
    json.dump(output, f, indent=2, ensure_ascii=False)

print(f"OK: {len(mitra)} mitra, {gkp['total']:,.0f} kg GKP, {jagung['total']:,.0f} kg Jagung, {beras['total']:,.0f} kg Beras")
print(f"Saved to: {OUTPUT}")
