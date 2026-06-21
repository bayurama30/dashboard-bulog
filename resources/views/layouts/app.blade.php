<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monitoring Bulog Kancab Ciamis 2026</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
  <style>
    :root {--bg:#0f1117;--card:#1a1d27;--border:#2a2d3a;--text:#e1e4ed;--sub:#8b90a0;--accent:#6366f1;--green:#22c55e;--yellow:#eab308;--red:#ef4444;--blue:#3b82f6;--orange:#f97316;--purple:#a855f7}
    *{margin:0;padding:0;box-sizing:border-box}
    body{background:var(--bg);color:var(--text);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;min-height:100vh}
    .header{background:var(--card);border-bottom:1px solid var(--border);padding:16px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
    .header h1{font-size:1.4em;font-weight:700}
    .header .meta{color:var(--sub);font-size:.85em}
    .tabs{display:flex;gap:4px;padding:12px 24px 0;border-bottom:1px solid var(--border)}
    .tab{padding:10px 20px;border:none;background:none;color:var(--sub);cursor:pointer;font-size:.95em;font-weight:500;border-radius:8px 8px 0 0;transition:all .2s;text-decoration:none}
    .tab:hover{color:var(--text);background:rgba(255,255,255,.03)}
    .tab.active{color:var(--accent);background:var(--card);border:1px solid var(--border);border-bottom-color:var(--card);margin-bottom:-1px}
    .content{padding:24px;max-width:1400px;margin:0 auto}
    .kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px}
    .kpi{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px;text-align:center}
    .kpi .label{color:var(--sub);font-size:.85em;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
    .kpi .value{font-size:1.8em;font-weight:700}
    .kpi .sub{font-size:.85em;margin-top:4px;color:var(--sub)}
    .chart-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(450px,1fr));gap:20px}
    .chart-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px}
    .chart-card.full{grid-column:1/-1}
    .chart-card h3{font-size:1em;margin-bottom:16px;color:var(--sub)}
    .chart-wrap{position:relative;width:100%;height:350px}
    .chart-wrap.short{height:300px}
    canvas{width:100%!important;height:100%!important}
    .table-wrap{overflow-x:auto}
    table{width:100%;border-collapse:collapse;font-size:.9em}
    th,td{padding:10px 14px;text-align:left;border-bottom:1px solid var(--border)}
    th{color:var(--sub);font-weight:600;background:rgba(99,102,241,.05)}
    tr:hover{background:rgba(255,255,255,.02)}
    .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.8em;font-weight:600}
    .badge-gkp{background:rgba(99,102,241,.15);color:var(--accent)}
    .badge-jagung{background:rgba(249,115,22,.15);color:var(--orange)}
    .badge-beras{background:rgba(59,130,246,.15);color:var(--blue)}
    .btn-refresh{background:var(--accent);color:#fff;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-size:.85em}
    @media(max-width:768px){.chart-grid{grid-template-columns:1fr}.kpi-grid{grid-template-columns:repeat(2,1fr)}}
  </style>
</head>
<body>
  <div class="header">
    <div>
      <h1>📊 Monitoring Bulog Kancab Ciamis 2026</h1>
      <div class="meta">Data: <span id="fetchDate">{{ $data['fetched_at'] ?? 'N/A' }}</span> · <span class="badge badge-gkp">GKP</span> <span class="badge badge-jagung">Jagung</span> <span class="badge badge-beras">Beras PSO</span></div>
    </div>
    <button class="btn-refresh" onclick="location.reload()">🔄 Refresh</button>
  </div>
  <div class="tabs">
    @foreach($tabs as $key => $tab)
      <a href="?tab={{ $key }}" class="tab {{ $activeTab === $key ? 'active' : '' }}">{{ $tab['label'] }}</a>
    @endforeach
  </div>
  <div class="content">@yield('content')</div>
  <script>
    const DATA=@json($data);const MONTHS=['01-Januari','02-Februari','03-Maret','04-April','05-Mei','06-Juni'];const MONTHS_SHORT=['Jan','Feb','Mar','Apr','Mei','Jun'];const COLORS=['#6366f1','#8b5cf6','#a855f7','#c084fc','#e879f9','#f0abfc','#22c55e','#3b82f6','#f97316','#eab308','#ef4444','#ec4899','#14b8a6','#f59e0b','#84cc16'];
    function fmt(n){return n.toLocaleString('id-ID')}
    function fmtKg(n){return n>=1000?Math.round(n/1000).toLocaleString('id-ID')+'K':n.toLocaleString('id-ID')}
    document.getElementById('fetchDate').textContent=DATA.fetched_at?new Date(DATA.fetched_at).toLocaleString('id-ID',{day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}):'N/A';
  </script>
  @yield('scripts')
</body>
</html>
