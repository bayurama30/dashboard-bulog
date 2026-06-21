@extends('layouts.app')

@section('content')

{{-- ================ GKP TAB ================ --}}
<div id="tab-gkp" class="tab-content" style="display:{{ $activeTab === 'gkp' ? 'block' : 'none' }}">
  <div class="filter-bar">
    <select id="gkp-filter-bulan" onchange="applyFilters('gkp')"><option value="">Semua Bulan</option></select>
    <select id="gkp-filter-semester" onchange="applyFilters('gkp')"><option value="">Semua Semester</option><option value="1">Semester 1 (Jan-Jun)</option><option value="2">Semester 2 (Jul-Des)</option></select>
    <select id="gkp-filter-wilayah" onchange="applyFilters('gkp')"><option value="">Semua Wilayah</option></select>
    <select id="gkp-filter-pemasok" onchange="applyFilters('gkp')"><option value="">Semua Pemasok</option></select>
    <button class="btn-reset" onclick="resetFilters('gkp')">Reset</button>
  </div>
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Total Pengadaan GKP</div><div class="value" style="color:var(--accent)" id="gkp-total">{{ number_format($data['gkp']['total'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Rata per Bulan</div><div class="value" style="color:var(--blue)" id="gkp-rata">{{ number_format(round($data['gkp']['total']/count($data['gkp']['by_month'])), 0, ',', '.') }}</div><div class="sub">kg</div></div>
    @php $topW = array_key_first($data['gkp']['by_wilayah']); @endphp
    <div class="kpi"><div class="label">Wilayah Terbesar</div><div class="value" style="color:var(--green)" id="gkp-top-wilayah-name">{{ $topW }}</div><div class="sub" id="gkp-top-wilayah-val">{{ number_format($data['gkp']['by_wilayah'][$topW], 0, ',', '.') }} kg</div></div>
    @php $topM = array_key_first($data['gkp']['by_pemasok']); $topMshort = implode(' ', array_slice(explode(' ', $topM), 0, 2)); @endphp
    <div class="kpi"><div class="label">Mitra Teratas</div><div class="value" style="color:var(--purple)" id="gkp-top-mitra-name">{{ $topMshort }}</div><div class="sub" id="gkp-top-mitra-val">{{ number_format($data['gkp']['by_pemasok'][$topM], 0, ',', '.') }} kg</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-card"><h3>Tren Kuantum per Bulan</h3><div class="chart-wrap"><canvas id="gkp-monthly"></canvas></div></div>
    <div class="chart-card"><h3>Distribusi per Wilayah</h3><div class="chart-wrap"><canvas id="gkp-wilayah"></canvas></div></div>
    <div class="chart-card full"><h3>Top 15 Mitra</h3><div class="chart-wrap"><canvas id="gkp-mitra"></canvas></div></div>
  </div>
</div>

{{-- ================ JAGUNG TAB ================ --}}
<div id="tab-jagung" class="tab-content" style="display:{{ $activeTab === 'jagung' ? 'block' : 'none' }}">
  <div class="filter-bar">
    <select id="jagung-filter-bulan" onchange="applyFilters('jagung')"><option value="">Semua Bulan</option></select>
    <select id="jagung-filter-semester" onchange="applyFilters('jagung')"><option value="">Semua Semester</option><option value="1">Semester 1 (Jan-Jun)</option><option value="2">Semester 2 (Jul-Des)</option></select>
    <select id="jagung-filter-wilayah" onchange="applyFilters('jagung')"><option value="">Semua Wilayah</option></select>
    <button class="btn-reset" onclick="resetFilters('jagung')">Reset</button>
  </div>
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Total Pengadaan Jagung</div><div class="value" style="color:var(--orange)" id="jagung-total">{{ number_format($data['jagung']['total'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    @php $jtw = array_key_first($data['jagung']['by_wilayah']); @endphp
    <div class="kpi"><div class="label">Wilayah Terbesar</div><div class="value" style="color:var(--green)" id="jagung-top-wilayah-name">{{ $jtw }}</div><div class="sub" id="jagung-top-wilayah-val">{{ number_format($data['jagung']['by_wilayah'][$jtw], 0, ',', '.') }} kg</div></div>
    @php $jtm = array_reduce(array_keys($data['jagung']['by_month']), function($c, $i) use ($data) { $v = $data['jagung']['by_month'][$i]; return !$c || $v > $data['jagung']['by_month'][$c] ? $i : $c; }); @endphp
    <div class="kpi"><div class="label">Bulan Puncak</div><div class="value" style="color:var(--blue)" id="jagung-bulan-puncak-name">{{ $jtm }}</div><div class="sub" id="jagung-bulan-puncak-val">{{ number_format($data['jagung']['by_month'][$jtm], 0, ',', '.') }} kg</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-card"><h3>Tren Kuantum per Bulan</h3><div class="chart-wrap"><canvas id="jagung-monthly"></canvas></div></div>
    <div class="chart-card"><h3>Distribusi per Wilayah</h3><div class="chart-wrap"><canvas id="jagung-wilayah"></canvas></div></div>
  </div>
</div>

{{-- ================ BERAS PSO TAB ================ --}}
<div id="tab-beras_pso" class="tab-content" style="display:{{ $activeTab === 'beras_pso' ? 'block' : 'none' }}">
  <div class="filter-bar">
    <select id="beras-filter-bulan" onchange="applyFilters('beras')"><option value="">Semua Bulan</option></select>
    <select id="beras-filter-semester" onchange="applyFilters('beras')"><option value="">Semua Semester</option><option value="1">Semester 1 (Jan-Jun)</option><option value="2">Semester 2 (Jul-Des)</option></select>
    <select id="beras-filter-gudang" onchange="applyFilters('beras')"><option value="">Semua Gudang</option></select>
    <button class="btn-reset" onclick="resetFilters('beras')">Reset</button>
  </div>
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Total Beras PSO</div><div class="value" style="color:var(--blue)" id="beras-total">{{ number_format($data['beras_pso']['total'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    @php $btw = array_key_first($data['beras_pso']['by_wilayah']); $btwshort = str_replace('KOMPLEKS PERGUDANGAN ', '', $btw); @endphp
    <div class="kpi"><div class="label">Gudang Terbesar</div><div class="value" style="color:var(--green)" id="beras-top-gudang-name">{{ $btwshort }}</div><div class="sub" id="beras-top-gudang-val">{{ number_format($data['beras_pso']['by_wilayah'][$btw], 0, ',', '.') }} kg</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-card"><h3>Tren per Bulan</h3><div class="chart-wrap"><canvas id="beras-monthly"></canvas></div></div>
    <div class="chart-card"><h3>Distribusi per Gudang</h3><div class="chart-wrap"><canvas id="beras-wilayah"></canvas></div></div>
  </div>
</div>

{{-- ================ PENGOLAHAN TAB ================ --}}
<div id="tab-pengolahan" class="tab-content" style="display:{{ $activeTab === 'pengolahan' ? 'block' : 'none' }}">
  <div class="filter-bar">
    <input type="text" id="olah-search" placeholder="Cari mitra..." oninput="applyFilters('pengolahan')">
    <button class="btn-reset" onclick="resetFilters('pengolahan')">Reset</button>
  </div>
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Total GKP Diadakan</div><div class="value" style="color:var(--accent)" id="olah-total-pengadaan">{{ number_format($data['pengolahan']['total_pengadaan'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Sudah Diolah</div><div class="value" style="color:var(--green)" id="olah-total-diolah">{{ number_format($data['pengolahan']['total_olah'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Sisa Belum Diolah</div><div class="value" style="color:var(--red)" id="olah-total-sisa">{{ number_format($data['pengolahan']['total_sisa'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Rasio Pengolahan</div><div class="value" style="color:var(--yellow)" id="olah-rasio">{{ $data['pengolahan']['rasio'] }}%</div><div class="sub">%</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-card"><h3>Perbandingan Pengadaan vs Diolah (Top 10)</h3><div class="chart-wrap"><canvas id="olah-mitra"></canvas></div></div>
    <div class="chart-card"><h3>Progress Gauge</h3><div class="chart-wrap short"><canvas id="olah-gauge"></canvas></div></div>
    <div class="chart-card full">
      <h3>Detail per Mitra (<span id="olah-mitra-count">{{ count($data['pengolahan']['mitra']) }}</span> mitra)</h3>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Mitra</th><th>Pengadaan (kg)</th><th>Diolah (kg)</th><th>Sisa (kg)</th><th>Rasio</th></tr></thead>
          <tbody id="olah-tbody">
            @foreach($data['pengolahan']['mitra'] as $m)
            <tr>
              <td>{{ $m['nama'] }}</td>
              <td>{{ number_format($m['pengadaan'], 0, ',', '.') }}</td>
              <td>{{ number_format($m['pengolahan'], 0, ',', '.') }}</td>
              <td>{{ number_format($m['sisa'], 0, ',', '.') }}</td>
              <td style="@if($m['rasio']>40) color:var(--green) @elseif($m['rasio']>20) color:var(--yellow) @else color:var(--red) @endif;font-weight:700">{{ $m['rasio'] }}%</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
(function() {
  const d = DATA;
  const charts = {};
  const semesterMonths = {1:['Januari','Februari','Maret','April','Mei','Juni'],2:['Juli','Agustus','September','Oktober','November','Desember']};
  const MONTH_ORDER = {'Januari':1,'Februari':2,'Maret':3,'April':4,'Mei':5,'Juni':6,'Juli':7,'Agustus':8,'September':9,'Oktober':10,'November':11,'Desember':12};

  function sortMonths(arr){
    return arr.sort(function(a,b){return (MONTH_ORDER[a]||99)-(MONTH_ORDER[b]||99)});
  }

  function dataKey(tab){return tab==='beras'?'beras_pso':tab}
  function fmtNum(n){return n.toLocaleString('id-ID')}
  function shortName(n){return n.replace('CV. ','').replace('PD. ','').replace('KOMPLEKS PERGUDANGAN ','')}

  function getFilteredRaw(tab){
    const raw = d[dataKey(tab)].raw || [];
    let rows = [...raw];
    if(tab==='gkp'){
      const bulan=document.getElementById('gkp-filter-bulan').value;
      const semester=document.getElementById('gkp-filter-semester').value;
      const wilayah=document.getElementById('gkp-filter-wilayah').value;
      const pemasok=document.getElementById('gkp-filter-pemasok').value;
      if(bulan) rows=rows.filter(r=>r.bulan===bulan);
      else if(semester) rows=rows.filter(r=>semesterMonths[semester].includes(r.bulan));
      if(wilayah) rows=rows.filter(r=>r.wilayah===wilayah);
      if(pemasok) rows=rows.filter(r=>r.pemasok===pemasok);
    }else if(tab==='jagung'){
      const bulan=document.getElementById('jagung-filter-bulan').value;
      const semester=document.getElementById('jagung-filter-semester').value;
      const wilayah=document.getElementById('jagung-filter-wilayah').value;
      if(bulan) rows=rows.filter(r=>r.bulan===bulan);
      else if(semester) rows=rows.filter(r=>semesterMonths[semester].includes(r.bulan));
      if(wilayah) rows=rows.filter(r=>r.wilayah===wilayah);
    }else if(tab==='beras'){
      const bulan=document.getElementById('beras-filter-bulan').value;
      const semester=document.getElementById('beras-filter-semester').value;
      const gudang=document.getElementById('beras-filter-gudang').value;
      if(bulan) rows=rows.filter(r=>r.bulan===bulan);
      else if(semester) rows=rows.filter(r=>semesterMonths[semester].includes(r.bulan));
      if(gudang) rows=rows.filter(r=>r.gudang===gudang);
    }
    return rows;
  }

  function aggregateRows(rows, keyField, qtyField){
    const map={};
    rows.forEach(r=>{const k=r[keyField]||'Lainnya';map[k]=(map[k]||0)+r[qtyField];});
    return Object.fromEntries(Object.entries(map).sort((a,b)=>b[1]-a[1]));
  }

  function destroyChart(id){if(charts[id]){charts[id].destroy();delete charts[id];}}

  // --- Expose to global for inline onchange handlers ---
  window.applyFilters = function(tab){
    if(tab==='pengolahan'){
      const q=document.getElementById('olah-search').value.toLowerCase();
      const mitra=d.pengolahan.mitra;
      const filtered=q?mitra.filter(m=>m.nama.toLowerCase().includes(q)):mitra;
      const tp=filtered.reduce((s,m)=>s+m.pengadaan,0);
      const to=filtered.reduce((s,m)=>s+m.pengolahan,0);
      const ts=filtered.reduce((s,m)=>s+m.sisa,0);
      const rasio=tp>0?Math.round(to/tp*1000)/10:0;
      document.getElementById('olah-total-pengadaan').textContent=fmtNum(tp);
      document.getElementById('olah-total-diolah').textContent=fmtNum(to);
      document.getElementById('olah-total-sisa').textContent=fmtNum(ts);
      document.getElementById('olah-rasio').textContent=rasio+'%';
      document.getElementById('olah-mitra-count').textContent=filtered.length;
      const tbody=document.getElementById('olah-tbody');
      tbody.innerHTML=filtered.map(m=>{
        let c=m.rasio>40?'var(--green)':m.rasio>20?'var(--yellow)':'var(--red)';
        return '<tr><td>'+m.nama+'</td><td>'+fmtNum(m.pengadaan)+'</td><td>'+fmtNum(m.pengolahan)+'</td><td>'+fmtNum(m.sisa)+'</td><td style="color:'+c+';font-weight:700">'+m.rasio+'%</td></tr>';
      }).join('');
      const top10=filtered.slice(0,10);
      destroyChart('olah-mitra');
      charts['olah-mitra']=new Chart(document.getElementById('olah-mitra'),{
        type:'bar',data:{labels:top10.map(m=>shortName(m.nama)),datasets:[
          {label:'Pengadaan',data:top10.map(m=>m.pengadaan),backgroundColor:'#6366f1',borderRadius:4},
          {label:'Diolah',data:top10.map(m=>m.pengolahan),backgroundColor:'#22c55e',borderRadius:4}
        ]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',plugins:{legend:{labels:{color:'#8b90a0'}}},scales:{x:{ticks:{callback:v=>fmtKg(v)}}}}
      });
      destroyChart('olah-gauge');
      try{
        charts['olah-gauge']=new Chart(document.getElementById('olah-gauge'),{
          type:'doughnut',data:{labels:['Diolah','Sisa'],datasets:[{data:[rasio,100-rasio],backgroundColor:['#eab308','#2a2d3a'],borderWidth:0,circumference:180,rotation:270}]},
          options:{responsive:true,maintainAspectRatio:false,cutout:'75%',plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12,usePointStyle:true}},tooltip:{callbacks:{label:function(ctx){return ctx.label+': '+ctx.raw+'%'}}}}},
          plugins:[{id:'gaugeText',afterDraw(chart){const{ctx,chartArea:{top,bottom,left,right}}=chart;const x=(left+right)/2,y=(top+bottom)/2.6;ctx.save();ctx.font='bold 32px -apple-system,sans-serif';ctx.fillStyle='#e1e4ed';ctx.textAlign='center';ctx.textBaseline='middle';ctx.fillText(rasio+'%',x,y-8);ctx.font='14px -apple-system,sans-serif';ctx.fillStyle='#8b90a0';ctx.fillText('Diolah '+fmtKg(to)+' / Total '+fmtKg(tp)+' kg',x,y+22);ctx.restore()}}]
        });
      }catch(e){console.warn('Gauge error:',e);}
      return;
    }
    const rows=getFilteredRaw(tab);
    const byMonth=aggregateRows(rows,'bulan','qty');
    const byWilayah=aggregateRows(rows,tab==='beras'?'gudang':'wilayah','qty');
    const total=Object.values(byMonth).reduce((a,b)=>a+b,0);
    const months=sortMonths(Object.keys(byMonth));
    const bulanCount=months.length||1;

    if(tab==='gkp'){
      const byPemasok=aggregateRows(rows,'pemasok','qty');
      document.getElementById('gkp-total').textContent=fmtNum(total);
      document.getElementById('gkp-rata').textContent=fmtNum(Math.round(total/bulanCount));
      const tw=Object.entries(byWilayah)[0];
      document.getElementById('gkp-top-wilayah-name').textContent=tw?tw[0]:'-';
      document.getElementById('gkp-top-wilayah-val').textContent=tw?fmtNum(tw[1])+' kg':'-';
      const tm=Object.entries(byPemasok)[0];
      const tmn=tm?shortName(tm[0]).split(' ').slice(0,2).join(' '):'-';
      document.getElementById('gkp-top-mitra-name').textContent=tmn;
      document.getElementById('gkp-top-mitra-val').textContent=tm?fmtNum(tm[1])+' kg':'-';
      destroyChart('gkp-monthly');
      charts['gkp-monthly']=new Chart(document.getElementById('gkp-monthly'),{
        type:'bar',data:{labels:months.map(m=>MONTHS_SHORT[parseInt(m.substring(0,2))-1]||m),datasets:[{label:'Kuantum (kg)',data:months.map(m=>byMonth[m]||0),backgroundColor:'#6366f1',borderRadius:6}]},
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>fmtKg(v)}}}}
      });
      destroyChart('gkp-wilayah');
      const wl=Object.keys(byWilayah);if(wl.length){
        charts['gkp-wilayah']=new Chart(document.getElementById('gkp-wilayah'),{
          type:'doughnut',data:{labels:wl,datasets:[{data:Object.values(byWilayah),backgroundColor:COLORS,borderWidth:0}]},
          options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}
        });
      }
      destroyChart('gkp-mitra');
      const mitra=Object.entries(byPemasok).slice(0,15);if(mitra.length){
        charts['gkp-mitra']=new Chart(document.getElementById('gkp-mitra'),{
          type:'bar',data:{labels:mitra.map(function(a){return shortName(a[0])}),datasets:[{label:'Kuantum (kg)',data:mitra.map(function(a){return a[1]}),backgroundColor:COLORS,borderRadius:4}]},
          options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{ticks:{callback:v=>fmtKg(v)}}}}
        });
      }
    }else if(tab==='jagung'){
      document.getElementById('jagung-total').textContent=fmtNum(total);
      const tw=Object.entries(byWilayah)[0];
      document.getElementById('jagung-top-wilayah-name').textContent=tw?tw[0]:'-';
      document.getElementById('jagung-top-wilayah-val').textContent=tw?fmtNum(tw[1])+' kg':'-';
      const bp=Object.entries(byMonth).reduce(function(c,item){var m=item[0],v=item[1];return !c||v>byMonth[c]?m:c;},null);
      document.getElementById('jagung-bulan-puncak-name').textContent=bp||'-';
      document.getElementById('jagung-bulan-puncak-val').textContent=bp?fmtNum(byMonth[bp])+' kg':'-';
      destroyChart('jagung-monthly');
      charts['jagung-monthly']=new Chart(document.getElementById('jagung-monthly'),{
        type:'bar',data:{labels:months.map(m=>MONTHS_SHORT[parseInt(m.substring(0,2))-1]||m),datasets:[{label:'Kuantum (kg)',data:months.map(m=>byMonth[m]||0),backgroundColor:'#f97316',borderRadius:6}]},
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>fmtKg(v)}}}}
      });
      destroyChart('jagung-wilayah');
      const wl=Object.keys(byWilayah);if(wl.length){
        charts['jagung-wilayah']=new Chart(document.getElementById('jagung-wilayah'),{
          type:'doughnut',data:{labels:wl,datasets:[{data:Object.values(byWilayah),backgroundColor:['#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5','#fff7ed'],borderWidth:0}]},
          options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}
        });
      }
    }else if(tab==='beras'){
      document.getElementById('beras-total').textContent=fmtNum(total);
      const tw=Object.entries(byWilayah)[0];
      document.getElementById('beras-top-gudang-name').textContent=tw?shortName(tw[0]):'-';
      document.getElementById('beras-top-gudang-val').textContent=tw?fmtNum(tw[1])+' kg':'-';
      destroyChart('beras-monthly');
      charts['beras-monthly']=new Chart(document.getElementById('beras-monthly'),{
        type:'bar',data:{labels:months.map(m=>MONTHS_SHORT[parseInt(m.substring(0,2))-1]||m),datasets:[{label:'Kuantum (kg)',data:months.map(m=>byMonth[m]||0),backgroundColor:'#3b82f6',borderRadius:6}]},
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>fmtKg(v)}}}}
      });
      destroyChart('beras-wilayah');
      const wl=Object.keys(byWilayah);if(wl.length){
        charts['beras-wilayah']=new Chart(document.getElementById('beras-wilayah'),{
          type:'doughnut',data:{labels:wl.map(w=>shortName(w)),datasets:[{data:Object.values(byWilayah),backgroundColor:['#3b82f6','#60a5fa','#93c5fd','#bfdbfe'],borderWidth:0}]},
          options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}
        });
      }
    }
  };

  window.resetFilters = function(tab){
    if(tab==='pengolahan'){
      document.getElementById('olah-search').value='';
      window.applyFilters('pengolahan');
      return;
    }
    const selects=document.querySelectorAll('[id^="'+tab+'-filter-"]');
    for(var i=0;i<selects.length;i++){if(selects[i].tagName==='SELECT')selects[i].value='';}
    window.applyFilters(tab);
  };

  function populateFilters(tab){
    const raw = d[dataKey(tab)].raw || [];
    if(tab==='gkp'){
      const bulan=sortMonths([...new Set(raw.map(r=>r.bulan))]);
      const wilayah=[...new Set(raw.map(r=>r.wilayah))].sort();
      const pemasok=[...new Set(raw.map(r=>r.pemasok))].sort();
      fillSelect('gkp-filter-bulan',bulan);
      fillSelect('gkp-filter-wilayah',wilayah);
      fillSelect('gkp-filter-pemasok',pemasok);
    }else if(tab==='jagung'){
      const bulan=sortMonths([...new Set(raw.map(r=>r.bulan))]);
      const wilayah=[...new Set(raw.map(r=>r.wilayah))].sort();
      fillSelect('jagung-filter-bulan',bulan);
      fillSelect('jagung-filter-wilayah',wilayah);
    }else if(tab==='beras'){
      const bulan=sortMonths([...new Set(raw.map(r=>r.bulan))]);
      const gudang=[...new Set(raw.map(r=>r.gudang))].sort();
      fillSelect('beras-filter-bulan',bulan);
      fillSelect('beras-filter-gudang',gudang);
    }
  }

  function fillSelect(id,options){
    const sel=document.getElementById(id);
    const current=sel.value;
    const first=sel.options[0];
    sel.innerHTML='';
    sel.appendChild(first);
    options.forEach(function(o){var opt=document.createElement('option');opt.value=o;opt.textContent=o;sel.appendChild(opt);});
    sel.value=current;
  }

  function chartOrError(canvasId, createFn){
    try {
      var canvas = document.getElementById(canvasId);
      if(!canvas) { console.warn(canvasId+' canvas not found'); return null; }
      var c = createFn(canvas);
      charts[canvasId] = c;
      return c;
    }catch(e){
      console.error(canvasId+' chart error:', e);
      var parent = document.getElementById(canvasId).parentNode;
      if(parent){ parent.style.outline='2px solid red'; parent.title=e.message; }
      return null;
    }
  }

  function drawAll(){
    chartOrError('gkp-monthly', function(c){return new Chart(c, {type:'bar',data:{labels:MONTHS_SHORT,datasets:[{label:'Kuantum (kg)',data:MONTHS.map(function(m){return d.gkp.by_month[m]||0}),backgroundColor:'#6366f1',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});
    chartOrError('gkp-wilayah', function(c){return new Chart(c, {type:'doughnut',data:{labels:Object.keys(d.gkp.by_wilayah),datasets:[{data:Object.values(d.gkp.by_wilayah),backgroundColor:COLORS}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}})});
    chartOrError('gkp-mitra', function(c){return new Chart(c, {type:'bar',data:{labels:Object.keys(d.gkp.by_pemasok).map(function(n){return shortName(n)}),datasets:[{label:'Kuantum (kg)',data:Object.values(d.gkp.by_pemasok),backgroundColor:COLORS,borderRadius:4}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});

    chartOrError('jagung-monthly', function(c){return new Chart(c, {type:'bar',data:{labels:MONTHS_SHORT,datasets:[{label:'Kuantum (kg)',data:MONTHS.map(function(m){return d.jagung.by_month[m]||0}),backgroundColor:'#f97316',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});
    chartOrError('jagung-wilayah', function(c){return new Chart(c, {type:'doughnut',data:{labels:Object.keys(d.jagung.by_wilayah),datasets:[{data:Object.values(d.jagung.by_wilayah),backgroundColor:['#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5','#fff7ed']}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}})});

    chartOrError('beras-monthly', function(c){return new Chart(c, {type:'bar',data:{labels:MONTHS_SHORT.slice(0,3),datasets:[{label:'Kuantum (kg)',data:MONTHS.slice(0,3).map(function(m){return d.beras_pso.by_month[m]||0}),backgroundColor:'#3b82f6',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});
    chartOrError('beras-wilayah', function(c){return new Chart(c, {type:'doughnut',data:{labels:Object.keys(d.beras_pso.by_wilayah).map(function(w){return shortName(w)}),datasets:[{data:Object.values(d.beras_pso.by_wilayah),backgroundColor:['#3b82f6','#60a5fa','#93c5fd','#bfdbfe'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}})});

    var top10 = d.pengolahan.mitra.slice(0,10);
    chartOrError('olah-mitra', function(c){return new Chart(c, {type:'bar',data:{labels:top10.map(function(m){return shortName(m.nama)}),datasets:[{label:'Pengadaan',data:top10.map(function(m){return m.pengadaan}),backgroundColor:'#6366f1',borderRadius:4},{label:'Diolah',data:top10.map(function(m){return m.pengolahan}),backgroundColor:'#22c55e',borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',plugins:{legend:{labels:{color:'#8b90a0'}}},scales:{x:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});
    var rasio=d.pengolahan.rasio, gto=d.pengolahan.total_olah, gtp=d.pengolahan.total_pengadaan;
    try{
      charts['olah-gauge'] = new Chart(document.getElementById('olah-gauge'), {type:'doughnut',data:{labels:['Diolah','Sisa'],datasets:[{data:[rasio,100-rasio],backgroundColor:['#eab308','#2a2d3a'],borderWidth:0,circumference:180,rotation:270}]},      options:{responsive:true,maintainAspectRatio:false,cutout:'75%',plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12,usePointStyle:true}},tooltip:{callbacks:{label:function(ctx){return ctx.label+': '+ctx.raw+'%'}}}}},plugins:[{id:'gaugeText',afterDraw:function(chart){var ctx=chart.ctx,ca=chart.chartArea;var x=(ca.left+ca.right)/2,y=(ca.top+ca.bottom)/2.6;ctx.save();ctx.font='bold 32px -apple-system,sans-serif';ctx.fillStyle='#e1e4ed';ctx.textAlign='center';ctx.textBaseline='middle';ctx.fillText(rasio+'%',x,y-8);ctx.font='14px -apple-system,sans-serif';ctx.fillStyle='#8b90a0';ctx.fillText('Diolah '+fmtKg(gto)+' / Total '+fmtKg(gtp)+' kg',x,y+22);ctx.restore()}}]});
    }catch(e){console.warn('Gauge error:',e);}

    if(d.gkp.raw) populateFilters('gkp');
    if(d.jagung.raw) populateFilters('jagung');
    if(d.beras_pso.raw) populateFilters('beras');
  }

  drawAll();
})();
</script>
@endsection
