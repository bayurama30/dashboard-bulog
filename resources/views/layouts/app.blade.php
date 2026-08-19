<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monitoring Bulog Kancab Ciamis 2026</title>
  <script>(function(){var t=localStorage.getItem('theme')||'dark';document.documentElement.setAttribute('data-theme',t);window.__theme=t})()</script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>
    // Base path: '/bulog' jika app di-serve di bawah prefix /bulog, '' jika di root.
    // Dinamis agar semua panggilan AJAX (refresh/data/export/api-chat) selalu
    // menargetkan route Laravel yang valid, terlepas ada/tidaknya reverse-proxy
    // penambah prefix /bulog di depan.
    var BASE_PATH = (function(){
      var p = window.location.pathname;
      return (p.replace(/\/[^\/]+$/, '').replace(/\/+$/, '')) || '';
    })();
</script>
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
    .pengolahan-kpi{grid-template-columns:repeat(4,1fr);gap:12px}
    .pengolahan-kpi .kpi{padding:16px 10px;border-radius:10px;min-height:80px}
    .pengolahan-kpi .kpi .label{font-size:.7em;margin-bottom:4px;letter-spacing:.3px}
    .pengolahan-kpi .kpi .value{font-size:1.8em;font-weight:700}
    .pengolahan-kpi .kpi .sub{font-size:.7em;margin-top:4px}
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
    canvas{width:100%!important;height:100%!important;cursor:pointer;transition:opacity .2s}
    canvas:active{opacity:.8}
    .chart-card.clickable:hover{border-color:var(--accent);box-shadow:0 0 0 1px var(--accent)}
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
    .btn-export{background:var(--blue);color:#fff;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-size:.8em;font-weight:600;transition:all .2s;display:inline-flex;align-items:center;gap:6px}
    .btn-export:hover{box-shadow:0 4px 16px rgba(59,130,246,.45);transform:translateY(-1px)}
    .btn-export:active{transform:translateY(0)}
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
    .toast.warning{background:#d97706}
    .toast.info{background:#3b82f6}
    .toast .toast-icon{font-size:1.2em}
    .toast .toast-close{margin-left:auto;cursor:pointer;opacity:.7;font-size:1.1em;background:none;border:none;color:#fff}
    .toast .toast-close:hover{opacity:1}
    @keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}
    @media(max-width:768px){
      .header{padding:12px 16px;flex-direction:column;align-items:stretch;gap:10px}
      .header h1{font-size:1.15em}
      .header .meta{font-size:.78em}
      .header-actions{justify-content:space-between;width:100%}
      .tabs{padding:8px 16px 0;gap:2px;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none}
      .tabs::-webkit-scrollbar{display:none}
      .tab{padding:8px 14px;font-size:.82em;white-space:nowrap;border-radius:6px 6px 0 0;flex-shrink:0}
      .content{padding:16px 12px}
      .kpi-grid{grid-template-columns:repeat(2,1fr);gap:10px}
      .pengolahan-kpi{grid-template-columns:repeat(2,1fr);gap:10px}
      .pengolahan-kpi .kpi{padding:14px 8px;border-radius:8px;min-height:72px}
      .pengolahan-kpi .kpi .label{font-size:.65em}
      .pengolahan-kpi .kpi .value{font-size:1.5em}
      .pengolahan-kpi .kpi .sub{font-size:.65em}
      .kpi{padding:14px 10px;border-radius:10px}
      .kpi .label{font-size:.72em;margin-bottom:4px}
      .kpi .value{font-size:1.35em}
      .kpi .sub{font-size:.72em}
      .chart-grid{grid-template-columns:1fr;gap:14px}
      .chart-card{padding:14px;border-radius:10px}
      .chart-card h3{font-size:.9em;margin-bottom:12px}
      .chart-wrap{height:280px}
      .chart-wrap.short{height:240px}
      .filter-bar{gap:6px;margin-bottom:14px}
      .filter-bar select,.filter-bar input{min-width:0;flex:1 1 auto;font-size:.8em;padding:8px 10px}
      .filter-bar .btn-reset{font-size:.8em;padding:8px 12px;flex-shrink:0}
      table{font-size:.78em}
      th,td{padding:8px 10px}
      .btn-refresh{font-size:.8em;padding:8px 14px}
      .badge{font-size:.72em;padding:2px 8px}
      .toast-container{top:12px;right:12px;left:12px}
      .toast{max-width:100%;font-size:.82em;padding:12px 16px}
      .theme-toggle .switch{width:42px;height:23px;border-radius:12px}
      .theme-toggle .switch::after{width:17px;height:17px}
      .theme-toggle.light .switch::after{transform:translateX(19px)}
      .theme-toggle .icon{font-size:1em}
      [style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr!important}
    }
    @media(max-width:420px){
      .kpi-grid{grid-template-columns:1fr}
      .kpi .value{font-size:1.25em}
      .chart-wrap{height:250px}
      .chart-wrap.short{height:210px}
      .btn-refresh{font-size:.75em;padding:7px 12px}
    }
    .ai-chat-btn{position:fixed;bottom:24px;right:24px;width:56px;height:56px;border-radius:50%;background:var(--accent);color:#fff;border:none;cursor:pointer;font-size:1.5em;box-shadow:0 4px 16px rgba(99,102,241,.4);z-index:9998;transition:all .3s;display:flex;align-items:center;justify-content:center}
    .ai-chat-btn:hover{transform:scale(1.1);box-shadow:0 6px 24px rgba(99,102,241,.5)}
    .ai-chat-btn.active{background:var(--red);transform:rotate(90deg)}
    .ai-chat-panel{position:fixed;bottom:92px;right:24px;width:380px;height:520px;background:var(--card);border:1px solid var(--border);border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.3);z-index:9998;display:none;flex-direction:column;overflow:hidden;animation:chatSlideUp .3s ease}
    .ai-chat-panel.open{display:flex}
    @keyframes chatSlideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    .ai-chat-header{padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--accent);color:#fff}
    .ai-chat-header h4{margin:0;font-size:.95em;font-weight:600}
    .ai-chat-header button{background:none;border:none;color:#fff;cursor:pointer;font-size:1.1em;opacity:.8;padding:0}
    .ai-chat-header button:hover{opacity:1}
    .ai-chat-messages{flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:10px}
    .ai-chat-messages::-webkit-scrollbar{width:4px}
    .ai-chat-messages::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px}
    .ai-msg{max-width:85%;padding:10px 14px;border-radius:12px;font-size:.88em;line-height:1.5;word-wrap:break-word}
    .ai-msg.user{align-self:flex-end;background:var(--accent);color:#fff;border-bottom-right-radius:4px}
    .ai-msg.assistant{align-self:flex-start;background:var(--hover-bg);color:var(--text);border:1px solid var(--border);border-bottom-left-radius:4px}
    .ai-msg.assistant p{margin:0 0 8px}.ai-msg.assistant p:last-child{margin:0}
    .ai-msg.assistant ul,.ai-msg.assistant ol{margin:4px 0;padding-left:20px}
    .ai-msg.assistant code{background:var(--border);padding:1px 4px;border-radius:3px;font-size:.85em}
    .ai-msg.typing{align-self:flex-start;background:var(--hover-bg);border:1px solid var(--border);padding:12px 18px}
    .ai-typing-dots{display:flex;gap:4px}.ai-typing-dots span{width:6px;height:6px;background:var(--sub);border-radius:50%;animation:typingDot 1.2s infinite}
    .ai-typing-dots span:nth-child(2){animation-delay:.2s}.ai-typing-dots span:nth-child(3){animation-delay:.4s}
    @keyframes typingDot{0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-6px)}}
    .ai-chat-input{padding:12px;border-top:1px solid var(--border);display:flex;gap:8px;background:var(--card)}
    .ai-chat-input textarea{flex:1;background:var(--bg);color:var(--text);border:1px solid var(--border);border-radius:10px;padding:10px 12px;font-size:.88em;resize:none;font-family:inherit;outline:none;min-height:40px;max-height:100px}
    .ai-chat-input textarea:focus{border-color:var(--accent)}
    .ai-chat-input button{background:var(--accent);color:#fff;border:none;border-radius:10px;width:40px;cursor:pointer;font-size:1em;transition:opacity .2s;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .ai-chat-input button:hover{opacity:.85}
    .ai-chat-input button:disabled{opacity:.4;cursor:not-allowed}
    @media(max-width:768px){
      .ai-chat-panel{width:calc(100vw - 32px);right:16px;bottom:88px;height:60vh}
      .ai-chat-btn{bottom:16px;right:16px;width:50px;height:50px;font-size:1.3em}
    }
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
    var DATA = @json($data);
    var MONTHS=['Januari','Februari','Maret','April','Mei','Juni'];
    var MONTHS_SHORT=['Jan','Feb','Mar','Apr','Mei','Jun'];
    var COLORS=['#6366f1','#8b5cf6','#a855f7','#c084fc','#e879f9','#f0abfc','#22c55e','#3b82f6','#f97316','#eab308','#ef4444','#ec4899','#14b8a6','#f59e0b','#84cc16'];
    function fmt(n){return n.toLocaleString('id-ID')}
    function fmtKg(n){return Math.round(n).toLocaleString('id-ID')}
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
        const res=await fetch(BASE_PATH + '/refresh',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}});
        const json=await res.json();
        if(!json.ok){
          var errMsg=json.error||'Silakan coba beberapa saat lagi';
          showToast('error','Gagal refresh data',errMsg);
          btn.disabled=false;btn.textContent='🔄 Refresh';
          return;
        }
        showToast('success','Data berhasil diperbarui','Silakan refresh halaman untuk melihat data terbaru');
        setTimeout(function(){location.reload()},2500);
      }catch(e){
        showToast('error','Gagal terhubung','Tidak dapat menghubungi server');
        btn.disabled=false;btn.textContent='🔄 Refresh';
      }
    }
    function showToast(type,title,msg){
      const container=document.getElementById('toastContainer');
      const icons={success:'✅',error:'❌',warning:'⚠️',info:'ℹ️'};
      const toast=document.createElement('div');
      toast.className='toast '+type;
      toast.innerHTML='<span class="toast-icon">'+icons[type]+'</span><div><strong>'+title+'</strong><div style="font-size:.85em;opacity:.85">'+msg+'</div></div><button class="toast-close" onclick="this.parentElement.remove()">✕</button>';
      container.appendChild(toast);
      setTimeout(function(){if(toast.parentElement)toast.remove()},10000);
    }
  </script>

  <button class="ai-chat-btn" id="aiChatBtn" onclick="toggleAiChat()" title="AI Assistant">
    <span id="aiChatIcon">💬</span>
  </button>

  <div class="ai-chat-panel" id="aiChatPanel">
    <div class="ai-chat-header">
      <h4>🤖 AI Assistant</h4>
      <button onclick="toggleAiChat()" title="Tutup">✕</button>
    </div>
    <div class="ai-chat-messages" id="aiChatMessages">
      <div class="ai-msg assistant">Halo! Saya AI Assistant Dashboard Bulog. Tanyakan apa saja tentang data pengadaan GKP, Jagung, Beras PSO, atau Pengolahan.</div>
    </div>
    <div class="ai-chat-input">
      <textarea id="aiChatInput" placeholder="Tanya tentang data..." rows="1" onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendAiMessage()}"></textarea>
      <button id="aiChatSend" onclick="sendAiMessage()">➤</button>
    </div>
  </div>

  <script>
    var aiChatOpen=false;
    var aiChatHistory=[];
    var aiChatLoading=false;

    function toggleAiChat(){
      var panel=document.getElementById('aiChatPanel');
      var btn=document.getElementById('aiChatBtn');
      var icon=document.getElementById('aiChatIcon');
      aiChatOpen=!aiChatOpen;
      if(aiChatOpen){
        panel.classList.add('open');
        btn.classList.add('active');
        icon.textContent='✕';
        document.getElementById('aiChatInput').focus();
      }else{
        panel.classList.remove('open');
        btn.classList.remove('active');
        icon.textContent='💬';
      }
    }

    function addChatMessage(role,content){
      var msgs=document.getElementById('aiChatMessages');
      var div=document.createElement('div');
      div.className='ai-msg '+role;
      if(role==='assistant'){
        div.innerHTML=formatAiMarkdown(content);
      }else{
        div.textContent=content;
      }
      msgs.appendChild(div);
      msgs.scrollTop=msgs.scrollHeight;
      return div;
    }

    function showTyping(customMessage){
      var msgs=document.getElementById('aiChatMessages');
      var div=document.createElement('div');
      div.className='ai-msg typing';
      div.id='aiTyping';
      var html='';
      if(customMessage){
        html='<div style="font-size:.82em;color:var(--sub);margin-bottom:6px">'+customMessage+'</div>';
      }
      html+='<div class="ai-typing-dots"><span></span><span></span><span></span></div>';
      div.innerHTML=html;
      msgs.appendChild(div);
      msgs.scrollTop=msgs.scrollHeight;
    }

    function removeTyping(){
      var t=document.getElementById('aiTyping');
      if(t)t.remove();
    }

    function formatAiMarkdown(text){
      text=text.replace(/</g,'&lt;').replace(/>/g,'&gt;');
      text=text.replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>');
      text=text.replace(/\*(.*?)\*/g,'<em>$1</em>');
      text=text.replace(/`([^`]+)`/g,'<code>$1</code>');
      text=text.replace(/^(\d+)\.\s+(.*)/gm,'<li>$2</li>');
      text=text.replace(/^[-•]\s+(.*)/gm,'<li>$1</li>');
      text=text.replace(/(<li>.*<\/li>)/gs,function(m){return '<ul>'+m+'</ul>'});
      text=text.replace(/\n/g,'<br>');
      text=text.replace(/<br><ul>/g,'<ul>').replace(/<\/ul><br>/g,'</ul>');
      return text;
    }

    async function sendAiMessage(){
      var input=document.getElementById('aiChatInput');
      var msg=input.value.trim();
      if(!msg||aiChatLoading)return;
      input.value='';
      input.style.height='auto';
      addChatMessage('user',msg);
      aiChatHistory.push({role:'user',content:msg});
      if(aiChatHistory.length>10)aiChatHistory=aiChatHistory.slice(-10);
      aiChatLoading=true;
      document.getElementById('aiChatSend').disabled=true;
      var isFirstQuery=aiChatHistory.length<=1;
      if(isFirstQuery){
        showTyping('Mengambil data terbaru dari spreadsheet, mohon tunggu...');
      }else{
        showTyping();
      }
      try{
        var res=await fetch(BASE_PATH + '/api/chat',{
          method:'POST',
          headers:{'Content-Type':'application/json','Accept':'application/json'},
          body:JSON.stringify({message:msg,history:aiChatHistory.slice(0,-1)})
        });
        var json=await res.json();
        removeTyping();
        if(json.ok){
          addChatMessage('assistant',json.message);
          aiChatHistory.push({role:'assistant',content:json.message});
          if(json.data_refreshed){
            showToast('info','Data Diperbarui','Data spreadsheet telah di-refresh ke versi terbaru');
          }
          if(json.data_updated){
            var infoDiv=document.createElement('div');
            infoDiv.style.cssText='font-size:.72em;color:var(--sub);text-align:center;padding:4px 0';
            infoDiv.textContent='Data spreadsheet: '+json.data_updated;
            document.getElementById('aiChatMessages').appendChild(infoDiv);
          }
        }else{
          addChatMessage('assistant','⚠️ Error: '+(json.error||'Terjadi kesalahan'));
          showToast('error','AI Error',json.error||'Gagal mendapat respons');
        }
      }catch(e){
        removeTyping();
        addChatMessage('assistant','⚠️ Gagal menghubungi server. Coba lagi.');
        showToast('error','Connection Error',e.message);
      }
      aiChatLoading=false;
      document.getElementById('aiChatSend').disabled=false;
      input.focus();
    }

    var aiInput=document.getElementById('aiChatInput');
    aiInput.addEventListener('input',function(){this.style.height='auto';this.style.height=Math.min(this.scrollHeight,100)+'px'});
  </script>

  @yield('scripts')
</body>
</html>
