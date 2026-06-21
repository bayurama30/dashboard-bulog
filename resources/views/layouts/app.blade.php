<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monitoring Bulog Kancab Ciamis 2026</title>
  <script>(function(){var t=localStorage.getItem('theme')||'dark';document.documentElement.setAttribute('data-theme',t);window.__theme=t})()</script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
  <style>
    :root,[data-theme="dark"] {--bg:#0f1117;--card:#1a1d27;--border:#2a2d3a;--text:#e1e4ed;--sub:#8b90a0;--accent:#6366f1;--green:#22c55e;--yellow:#eab308;--red:#ef4444;--blue:#3b82f6;--orange:#f97316;--purple:#a855f7;--btn-reset-hover:#3a3d4a;--hover-bg:rgba(255,255,255,.03);--hover-table:rgba(255,255,255,.02);--th-bg:rgba(99,102,241,.05)}
    [data-theme="light"] {--bg:#f4f5f7;--card:#ffffff;--border:#dde0e6;--text:#1a1d2e;--sub:#6b7080;--accent:#4f46e5;--green:#16a34a;--yellow:#ca8a04;--red:#dc2626;--blue:#2563eb;--orange:#ea580c;--purple:#7c3aed;--btn-reset-hover:#d1d5db;--hover-bg:rgba(0,0,0,.03);--hover-table:rgba(0,0,0,.01);--th-bg:rgba(79,70,229,.06)}
    *{margin:0;padding:0;box-sizing:border-box}
    body{background:var(--bg);color:var(--text);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;min-height:100vh;transition:background .3s,color .3s}
    .header{background:var(--card);border-bottom:1px solid var(--border);padding:16px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
    .header h1{font-size:1.4em;font-weight:700}
    .header .meta{color:var(--sub);font-size:.85em}
    .header-actions{display:flex;gap:8px;align-items:center}
    .tabs{display:flex;gap:4px;padding:12px 24px 0;border-bottom:1px solid var(--border)}
    .tab{padding:10px 20px;border:none;background:none;color:var(--sub);cursor:pointer;font-size:.95em;font-weight:500;border-radius:8px 8px 0 0;transition:all .2s;text-decoration:none}
    .tab:hover{color:var(--text);background:var(--hover-bg)}
    .tab.active{color:var(--accent);background:var(--card);border:1px solid var(--border);border-bottom-color:var(--card);margin-bottom:-1px}
    .content{padding:24px;max-width:1400px;margin:0 auto}
    .kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px}
    .kpi{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px;text-align:center;transition:background .3s}
    .kpi .label{color:var(--sub);font-size:.85em;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
    .kpi .value{font-size:1.8em;font-weight:700}
    .kpi .sub{font-size:.85em;margin-top:4px;color:var(--sub)}
    .chart-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(450px,1fr));gap:20px}
    .chart-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px;transition:background .3s}
    .chart-card.full{grid-column:1/-1}
    .chart-card h3{font-size:1em;margin-bottom:16px;color:var(--sub)}
    .chart-wrap{position:relative;width:100%;height:350px}
    .chart-wrap.short{height:300px}
    canvas{width:100%!important;height:100%!important}
    .table-wrap{overflow-x:auto}
    table{width:100%;border-collapse:collapse;font-size:.9em}
    th,td{padding:10px 14px;text-align:left;border-bottom:1px solid var(--border)}
    th{color:var(--sub);font-weight:600;background:var(--th-bg)}
    tr:hover{background:var(--hover-table)}
    .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.8em;font-weight:600}
    .badge-gkp{background:rgba(99,102,241,.15);color:var(--accent)}
    .badge-jagung{background:rgba(249,115,22,.15);color:var(--orange)}
    .badge-beras{background:rgba(59,130,246,.15);color:var(--blue)}
    .btn-refresh{background:var(--accent);color:#fff;border:none;padding:8px 18px;border-radius:50px;cursor:pointer;font-size:.85em;font-weight:600;box-shadow:0 2px 8px rgba(99,102,241,.3);transition:all .2s}
    .btn-refresh:hover{box-shadow:0 4px 16px rgba(99,102,241,.45);transform:translateY(-1px)}
    .btn-refresh:active{transform:translateY(0)}
    .btn-refresh:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}
    .theme-toggle{display:flex;align-items:center;gap:8px}
    .theme-toggle .switch{position:relative;width:48px;height:26px;background:var(--border);border-radius:13px;cursor:pointer;transition:background .3s;border:none;padding:0;outline:none}
    .theme-toggle .switch::after{content:'';position:absolute;top:3px;left:3px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform .3s}
    .theme-toggle.light .switch{background:var(--accent)}
    .theme-toggle.light .switch::after{transform:translateX(22px)}
    .theme-toggle .icon{font-size:1.1em;transition:opacity .3s}
    .filter-bar{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;align-items:center}
    .filter-bar select,.filter-bar input{background:var(--card);color:var(--text);border:1px solid var(--border);padding:8px 12px;border-radius:8px;font-size:.85em;min-width:150px;transition:background .3s}
    .filter-bar select:focus,.filter-bar input:focus{outline:none;border-color:var(--accent)}
    .filter-bar .btn-reset{background:var(--border);color:var(--text);border:none;padding:8px 14px;border-radius:8px;cursor:pointer;font-size:.85em}
    .filter-bar .btn-reset:hover{background:var(--btn-reset-hover)}
    .toast-container{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px}
    .toast{display:flex;align-items:center;gap:10px;padding:14px 20px;border-radius:10px;color:#fff;font-size:.9em;font-weight:500;box-shadow:0 8px 32px rgba(0,0,0,.4);animation:slideIn .3s ease;max-width:420px}
    .toast.success{background:#16a34a}
    .toast.error{background:#dc2626}
    .toast .toast-icon{font-size:1.2em}
    .toast .toast-close{margin-left:auto;cursor:pointer;opacity:.7;font-size:1.1em;background:none;border:none;color:#fff}
    .toast .toast-close:hover{opacity:1}
    @keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}
    @media(max-width:768px){.chart-grid{grid-template-columns:1fr}.kpi-grid{grid-template-columns:repeat(2,1fr)}}
  </style>
</head>
<body>
  <div class="toast-container" id="toastContainer"></div>
  <div class="header">
    <div>
      <h1>📊 Monitoring Bulog Kancab Ciamis 2026</h1>
      <div class="meta">Data: <span id="fetchDate">{{ $data['fetched_at'] ?? 'N/A' }}</span> · <span class="badge badge-gkp">GKP</span> <span class="badge badge-jagung">Jagung</span> <span class="badge badge-beras">Beras PSO</span></div>
    </div>
    <div class="header-actions">
      <label class="theme-toggle" id="themeToggle">
        <span class="icon">🌙</span>
        <button type="button" class="switch" onclick="toggleTheme()" title="Ubah tema"></button>
        <span class="icon">☀️</span>
      </label>
      <button class="btn-refresh" id="btnRefresh" onclick="refreshData()">🔄 Refresh Data</button>
    </div>
  </div>
  <script>if(window.__theme==='light')document.getElementById('themeToggle').classList.add('light')</script>
  <div class="tabs">
    @foreach($tabs as $key => $tab)
      <a href="?tab={{ $key }}" class="tab {{ $activeTab === $key ? 'active' : '' }}">{{ $tab['label'] }}</a>
    @endforeach
  </div>
  <div class="content">@yield('content')</div>
  <script>
    const DATA=@json($data);const MONTHS=['Januari','Februari','Maret','April','Mei','Juni'];const MONTHS_SHORT=['Jan','Feb','Mar','Apr','Mei','Jun'];const COLORS=['#6366f1','#8b5cf6','#a855f7','#c084fc','#e879f9','#f0abfc','#22c55e','#3b82f6','#f97316','#eab308','#ef4444','#ec4899','#14b8a6','#f59e0b','#84cc16'];
    function fmt(n){return n.toLocaleString('id-ID')}
    function fmtKg(n){return n>=1000?Math.round(n/1000).toLocaleString('id-ID')+'K':n.toLocaleString('id-ID')}
    document.getElementById('fetchDate').textContent=DATA.fetched_at?new Date(DATA.fetched_at).toLocaleString('id-ID',{day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}):'N/A';

    function getTheme(){return document.documentElement.getAttribute('data-theme')||'dark'}
    function setTheme(t){
      var cur=document.documentElement.getAttribute('data-theme');
      if(cur===t) return;
      document.documentElement.setAttribute('data-theme',t);
      var tg=document.getElementById('themeToggle');
      if(t==='light'){tg.classList.add('light')}else{tg.classList.remove('light')}
      localStorage.setItem('theme',t);
    }
    function toggleTheme(){setTheme(getTheme()==='dark'?'light':'dark')}
    async function refreshData(){
      const btn=document.getElementById('btnRefresh');
      btn.disabled=true;btn.textContent='⏳ Fetching...';
      try{
        const res=await fetch('/refresh',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}});
        const json=await res.json();
        if(!json.ok){showToast('error','Gagal refresh data','Silakan coba beberapa saat lagi');btn.disabled=false;btn.textContent='🔄 Refresh';return}
        showToast('success','Data berhasil diperbarui','Silakan refresh halaman untuk melihat data terbaru');
        setTimeout(()=>location.reload(),2500);
      }catch(e){
        showToast('error','Gagal terhubung','Tidak dapat menghubungi server');btn.disabled=false;btn.textContent='🔄 Refresh';
      }
    }
    function showToast(type,title,msg){
      const container=document.getElementById('toastContainer');
      const icons={success:'✅',error:'❌'};
      const toast=document.createElement('div');
      toast.className='toast '+type;
      toast.innerHTML='<span class="toast-icon">'+icons[type]+'</span><div><strong>'+title+'</strong><div style="font-size:.85em;opacity:.85">'+msg+'</div></div><button class="toast-close" onclick="this.parentElement.remove()">✕</button>';
      container.appendChild(toast);
      setTimeout(()=>{if(toast.parentElement)toast.remove()},8000);
    }
  </script>
  @yield('scripts')
</body>
</html>
