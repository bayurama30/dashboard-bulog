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
    <button class="btn-export" onclick="exportData('csv','gkp')">📥 CSV</button>
    <button class="btn-export" onclick="exportData('xlsx','gkp')">📊 Excel</button>
    <button class="btn-export" onclick="exportData('pdf','gkp')">📄 PDF</button>
  </div>
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Rata per Bulan</div><div class="value" style="color:var(--blue)" id="gkp-rata">{{ number_format(round($data['gkp']['total']/count($data['gkp']['by_month'])), 0, ',', '.') }}</div><div class="sub">kg</div></div>
    @php $topW = array_key_first($data['gkp']['by_wilayah']); @endphp
    <div class="kpi"><div class="label">Wilayah Terbesar</div><div class="value" style="color:var(--green)" id="gkp-top-wilayah-name">{{ $topW }}</div><div class="sub" id="gkp-top-wilayah-val">{{ number_format($data['gkp']['by_wilayah'][$topW], 0, ',', '.') }} kg</div></div>
    @php $topM = array_key_first($data['gkp']['by_pemasok']); $topMshort = implode(' ', array_slice(explode(' ', $topM), 0, 2)); @endphp
    <div class="kpi"><div class="label">Mitra Teratas</div><div class="value" style="color:var(--purple)" id="gkp-top-mitra-name">{{ $topMshort }}</div><div class="sub" id="gkp-top-mitra-val">{{ number_format($data['gkp']['by_pemasok'][$topM], 0, ',', '.') }} kg</div></div>
  </div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
    <div class="chart-card"><h3>Progress Target Pengadaan</h3><div class="chart-wrap short"><canvas id="gkp-gauge"></canvas></div></div>
    <div style="display:flex;flex-direction:column;gap:12px;justify-content:space-between">
      <div class="kpi" style="text-align:center;padding:18px">
        <div class="label">Target Pengadaan GKP</div>
        <div style="display:flex;align-items:baseline;justify-content:center;gap:6px">
          <div class="value" style="color:var(--yellow);font-size:2em">74,692,000</div>
          <div style="font-size:1em;font-weight:600;color:var(--sub)">kg</div>
        </div>
      </div>
      <div class="kpi" style="text-align:center;padding:18px">
        <div class="label">Total Pengadaan GKP</div>
        <div style="display:flex;align-items:baseline;justify-content:center;gap:6px">
          <div class="value" style="color:var(--accent);font-size:2em" id="gkp-total">{{ number_format($data['gkp']['total'], 0, ',', '.') }}</div>
          <div style="font-size:1em;font-weight:600;color:var(--sub)">kg</div>
        </div>
      </div>
      <div class="chart-card" style="padding:12px;flex:1;overflow:auto">
        <h3 style="font-size:.85em;margin-bottom:8px">PO Hari Ini (<span id="gkp-po-count">0</span> · <span id="gkp-po-total">0</span> kg)</h3>
        <div class="table-wrap" style="max-height:140px;overflow-y:auto">
          <table style="font-size:.8em">
            <thead><tr><th style="padding:6px 8px">No</th><th style="padding:6px 8px">Mitra</th><th style="padding:6px 8px">Wilayah</th><th style="padding:6px 8px">Qty (kg)</th></tr></thead>
            <tbody id="gkp-po-tbody"></tbody>
          </table>
        </div>
      </div>
    </div>
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
    <button class="btn-export" onclick="exportData('csv','jagung')">📥 CSV</button>
    <button class="btn-export" onclick="exportData('xlsx','jagung')">📊 Excel</button>
    <button class="btn-export" onclick="exportData('pdf','jagung')">📄 PDF</button>
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
    <button class="btn-export" onclick="exportData('csv','beras_pso')">📥 CSV</button>
    <button class="btn-export" onclick="exportData('xlsx','beras_pso')">📊 Excel</button>
    <button class="btn-export" onclick="exportData('pdf','beras_pso')">📄 PDF</button>
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
    <button class="btn-export" onclick="exportData('csv','pengolahan')">📥 CSV</button>
    <button class="btn-export" onclick="exportData('xlsx','pengolahan')">📊 Excel</button>
    <button class="btn-export" onclick="exportData('pdf','pengolahan')">📄 PDF</button>
  </div>
  <div class="kpi-grid pengolahan-kpi">
    <div class="kpi"><div class="label">Pengadaan GKP</div><div class="value" style="color:var(--accent)" id="olah-total-pengadaan">{{ number_format($data['pengolahan']['total_pengadaan'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Pengadaan Setara Beras</div><div class="value" style="color:var(--blue)" id="olah-total-pengadaan-beras">{{ number_format($data['pengolahan']['total_pengadaan_beras'] ?? 0, 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Pengolahan GKP</div><div class="value" style="color:var(--green)" id="olah-total-diolah">{{ number_format($data['pengolahan']['total_olah'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Pengolahan Setara Beras</div><div class="value" style="color:var(--orange)" id="olah-total-diolah-beras">{{ number_format($data['pengolahan']['total_olah_beras'] ?? 0, 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Belum Pengolahan GKP</div><div class="value" style="color:var(--red)" id="olah-total-sisa">{{ number_format($data['pengolahan']['total_sisa'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Belum Pengolahan Setara Beras</div><div class="value" style="color:var(--red)" id="olah-total-sisa-beras">{{ number_format($data['pengolahan']['total_sisa_beras'] ?? 0, 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Rasio</div><div class="value" style="color:var(--yellow)" id="olah-rasio">{{ $data['pengolahan']['rasio'] }}%</div><div class="sub">%</div></div>
    <div class="kpi"><div class="label">Rendaman Tonak</div><div class="value" style="color:var(--purple)" id="olah-rendaman">{{ ($data['pengolahan']['avg_rendeman'] ?? 0) }}%</div><div class="sub">avg</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-card"><h3>Perbandingan Pengadaan vs Diolah (Top 10)</h3><div class="chart-wrap"><canvas id="olah-mitra"></canvas></div></div>
    <div class="chart-card"><h3>Progress Gauge</h3><div class="chart-wrap short"><canvas id="olah-gauge"></canvas></div></div>
    <div class="chart-card"><h3>Pemasukan Fisik vs Tonase Pengolahan (Top 10)</h3><div class="chart-wrap"><canvas id="olah-fisik"></canvas></div></div>
    <div class="chart-card"><h3>Rendeman per Mitra (Top 10)</h3><div class="chart-wrap"><canvas id="olah-rendeman"></canvas></div></div>
    <div class="chart-card full">
      <h3>Detail per Mitra (<span id="olah-mitra-count">{{ count($data['pengolahan']['mitra']) }}</span> mitra)</h3>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Mitra</th><th>Pengadaan (kg)</th><th>Diolah (kg)</th><th>Sisa (kg)</th><th>Rasio (%)</th></tr></thead>
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
  var d = DATA;
  var charts = {};
  var semesterMonths = {1:['Januari','Februari','Maret','April','Mei','Juni'],2:['Juli','Agustus','September','Oktober','November','Desember']};
  var MONTH_ORDER = {'Januari':1,'Februari':2,'Maret':3,'April':4,'Mei':5,'Juni':6,'Juli':7,'Agustus':8,'September':9,'Oktober':10,'November':11,'Desember':12};

  function sortMonths(arr){
    return arr.sort(function(a,b){return (MONTH_ORDER[a]||99)-(MONTH_ORDER[b]||99)});
  }

  function dataKey(tab){return tab==='beras'?'beras_pso':tab}
  function fmtNum(n){
    if(n >= 1000000000) return (n/1000000000).toFixed(1).replace(/\.0$/,'')+'B';
    if(n >= 1000000) return (n/1000000).toFixed(1).replace(/\.0$/,'')+'M';
    if(n >= 1000) return (n/1000).toFixed(1).replace(/\.0$/,'')+'K';
    return n.toLocaleString('id-ID');
  }
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
  // Click-to-filter helper: bar chart → set search/pemasok filter
  function makeBarClickHandler(fullNames,searchId,tab){
    return function(evt,elements,chart){
      if(elements.length>0){
        var idx=elements[0].index;
        var fn=fullNames[idx];
        if(fn){
          if(searchId)document.getElementById(searchId).value=fn;
          applyFilters(tab);
          showToast('success','Filter diterapkan','Menampilkan data untuk: '+fn);
        }
      }
    };
  }
  // Click-to-filter helper: doughnut chart → set wilayah/gudang dropdown
  function makeDoughnutClickHandler(fullLabels,filterId,tab){
    return function(evt,elements,chart){
      if(elements.length>0){
        var idx=elements[0].index;
        var label=fullLabels[idx];
        if(label){
          var sel=document.getElementById(filterId);
          if(sel){sel.value=label;applyFilters(tab);
            showToast('success','Filter diterapkan','Menampilkan data untuk: '+label);
          }
        }
      }
    };
  }
  // Click-to-filter helper: monthly bar chart → set bulan dropdown
  function makeMonthlyClickHandler(monthNames,filterId,tab,monthValues){
    return function(evt,elements,chart){
      if(elements.length>0){
        var idx=elements[0].index;
        var bulan=monthNames[idx];
        var val=monthValues?fmtKg(monthValues[idx]):'';
        if(bulan){
          var sel=document.getElementById(filterId);
          if(sel){sel.value=bulan;applyFilters(tab);
            showToast('info','Filter Bulan '+MONTHS_SHORT[idx],val?'Total: '+val+' kg':'');
          }
        }
      }
    };
  }
  window.applyFilters = function(tab){
    if(tab==='pengolahan'){
      var q=document.getElementById('olah-search').value.toLowerCase();
      var mitra=d.pengolahan.mitra;
      var filtered=q?mitra.filter(function(m){return m.nama.toLowerCase().indexOf(q)!==-1}):mitra;
      var tp=filtered.reduce(function(s,m){return s+m.pengadaan},0);
      var tpb=filtered.reduce(function(s,m){return s+(m.pengadaan_beras||0)},0);
      var to=filtered.reduce(function(s,m){return s+m.pengolahan},0);
      var tob=filtered.reduce(function(s,m){return s+(m.pengolahan_beras||0)},0);
      var ts=filtered.reduce(function(s,m){return s+m.sisa},0);
      var tsb=filtered.reduce(function(s,m){return s+(m.sisa_beras||0)},0);
      var rasio=tp>0?Math.round(to/tp*1000)/10:0;
      var rendeman=tp>0?Math.round(filtered.reduce(function(s,m){return s+((m.rendeman||0)*m.pengadaan)},0)/tp,1):0;
      document.getElementById('olah-total-pengadaan').textContent=fmtNum(tp);
      document.getElementById('olah-total-pengadaan-beras').textContent=fmtNum(tpb);
      document.getElementById('olah-total-diolah').textContent=fmtNum(to);
      document.getElementById('olah-total-diolah-beras').textContent=fmtNum(tob);
      document.getElementById('olah-total-sisa').textContent=fmtNum(ts);
      document.getElementById('olah-total-sisa-beras').textContent=fmtNum(tsb);
      document.getElementById('olah-rasio').textContent=rasio+'%';
      document.getElementById('olah-rendaman').textContent=rendeman+'%';
      document.getElementById('olah-mitra-count').textContent=filtered.length;
      var tbody=document.getElementById('olah-tbody');
      tbody.innerHTML='';
      filtered.forEach(function(m){
        var rVal = typeof m.rasio==='number' ? m.rasio : 0;
        var c = rVal>40?'var(--green)':rVal>20?'var(--yellow)':'var(--red)';
        var tr=document.createElement('tr');
        tr.innerHTML='<td>'+m.nama+'</td><td>'+fmtNum(m.pengadaan)+'</td><td>'+fmtNum(m.pengolahan)+'</td><td>'+fmtNum(m.sisa)+'</td><td style="color:'+c+';font-weight:700">'+rVal+'%</td>';
        tbody.appendChild(tr);
      });
      var top10=filtered.slice(0,10);
      var labels=top10.map(function(m){return shortName(m.nama)});
      var olahFullNames=top10.map(function(m){return m.nama});
      destroyChart('olah-mitra');
      try{
        charts['olah-mitra']=new Chart(document.getElementById('olah-mitra'),{
          type:'bar',data:{labels:labels,datasets:[
            {label:'Pengadaan',data:top10.map(function(m){return m.pengadaan}),backgroundColor:'#6366f1',borderRadius:4},
            {label:'Diolah',data:top10.map(function(m){return m.pengolahan}),backgroundColor:'#22c55e',borderRadius:4}
          ]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',onClick:makeBarClickHandler(olahFullNames,'olah-search','pengolahan'),plugins:{legend:{labels:{color:'#8b90a0'}}},scales:{x:{ticks:{callback:function(v){return fmtKg(v)}}}}}
        });
      }catch(e){console.warn('olah-mitra error:',e);}
      destroyChart('olah-gauge');
      try{
        charts['olah-gauge']=new Chart(document.getElementById('olah-gauge'),{
          type:'doughnut',data:{labels:['Diolah','Sisa'],datasets:[{data:[rasio,100-rasio],backgroundColor:['#eab308','#2a2d3a'],borderWidth:0,circumference:180,rotation:270}]},
          options:{responsive:true,maintainAspectRatio:false,cutout:'75%',plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12,usePointStyle:true}},tooltip:{callbacks:{label:function(ctx){return ctx.label+': '+ctx.raw+'%'}}}}},
          plugins:[{id:'gaugeText2',afterDraw:function(chart){var ctx2=chart.ctx,ca2=chart.chartArea;var x2=(ca2.left+ca2.right)/2,y2=(ca2.top+ca2.bottom)/2.6;ctx2.save();ctx2.font='bold 32px -apple-system,sans-serif';ctx2.fillStyle='#e1e4ed';ctx2.textAlign='center';ctx2.textBaseline='middle';ctx2.fillText(rasio+'%',x2,y2-8);ctx2.font='14px -apple-system,sans-serif';ctx2.fillStyle='#8b90a0';ctx2.fillText('Diolah '+fmtKg(to)+' / Total '+fmtKg(tp)+' kg',x2,y2+22);ctx2.restore()}}]
        });
      }catch(e){console.warn('olah-gauge error:',e);}
      destroyChart('olah-fisik');
      try{
        charts['olah-fisik']=new Chart(document.getElementById('olah-fisik'),{
          type:'bar',data:{labels:labels,datasets:[
            {label:'Pengadaan GKP',data:top10.map(function(m){return m.pengadaan}),backgroundColor:'#6366f1',borderRadius:4},
            {label:'Pemasukan Fisik',data:top10.map(function(m){return m.pengolahan}),backgroundColor:'#22c55e',borderRadius:4}
          ]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',onClick:makeBarClickHandler(olahFullNames,'olah-search','pengolahan'),plugins:{legend:{labels:{color:'#8b90a0'}}},scales:{x:{ticks:{callback:function(v){return fmtKg(v)}}}}}
        });
      }catch(e){console.warn('olah-fisik error:',e);}
      destroyChart('olah-rendeman');
      try{
        charts['olah-rendeman']=new Chart(document.getElementById('olah-rendeman'),{
          type:'bar',data:{labels:labels,datasets:[{label:'Rasio (%)',data:top10.map(function(m){return m.rasio}),backgroundColor:top10.map(function(m){return m.rasio>40?'#22c55e':m.rasio>20?'#eab308':'#ef4444'}),borderRadius:4}]},
          options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',onClick:makeBarClickHandler(olahFullNames,'olah-search','pengolahan'),plugins:{legend:{display:false}},scales:{x:{min:0,max:100,ticks:{callback:function(v){return v+'%'}}}}}
        });
      }catch(e){console.warn('olah-rendeman error:',e);}
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
      drawGkpGauge(total);
      destroyChart('gkp-monthly');
      var gkpMonthLabels=months.map(m=>MONTHS_SHORT[parseInt(m.substring(0,2))-1]||m);
      var gkpMonthValues=months.map(m=>byMonth[m]||0);
      charts['gkp-monthly']=new Chart(document.getElementById('gkp-monthly'),{
        type:'bar',data:{labels:gkpMonthLabels,datasets:[{label:'Kuantum (kg)',data:gkpMonthValues,backgroundColor:'#6366f1',borderRadius:6}]},
        options:{responsive:true,maintainAspectRatio:false,onClick:makeMonthlyClickHandler(months,'gkp-filter-bulan','gkp',gkpMonthValues),plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>fmtKg(v)}}}}
      });
      destroyChart('gkp-wilayah');
      const wl=Object.keys(byWilayah);if(wl.length){
        charts['gkp-wilayah']=new Chart(document.getElementById('gkp-wilayah'),{
          type:'doughnut',data:{labels:wl,datasets:[{data:Object.values(byWilayah),backgroundColor:COLORS,borderWidth:0}]},
          options:{responsive:true,maintainAspectRatio:false,onClick:makeDoughnutClickHandler(wl,'gkp-filter-wilayah','gkp'),plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}
        });
      }
      destroyChart('gkp-mitra');
      const mitra=Object.entries(byPemasok).slice(0,15);if(mitra.length){
        charts['gkp-mitra']=new Chart(document.getElementById('gkp-mitra'),{
          type:'bar',data:{labels:mitra.map(function(a){return shortName(a[0])}),datasets:[{label:'Kuantum (kg)',data:mitra.map(function(a){return a[1]}),backgroundColor:COLORS,borderRadius:4}]},
          options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,onClick:makeBarClickHandler(mitra.map(a=>a[0]),'gkp-filter-pemasok','gkp'),plugins:{legend:{display:false}},scales:{x:{ticks:{callback:v=>fmtKg(v)}}}}
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
      var jagungMonthLabels=months.map(m=>MONTHS_SHORT[parseInt(m.substring(0,2))-1]||m);
      var jagungMonthValues=months.map(m=>byMonth[m]||0);
      charts['jagung-monthly']=new Chart(document.getElementById('jagung-monthly'),{
        type:'bar',data:{labels:jagungMonthLabels,datasets:[{label:'Kuantum (kg)',data:jagungMonthValues,backgroundColor:'#f97316',borderRadius:6}]},
        options:{responsive:true,maintainAspectRatio:false,onClick:makeMonthlyClickHandler(months,'jagung-filter-bulan','jagung',jagungMonthValues),plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>fmtKg(v)}}}}
      });
      destroyChart('jagung-wilayah');
      const wl=Object.keys(byWilayah);if(wl.length){
        charts['jagung-wilayah']=new Chart(document.getElementById('jagung-wilayah'),{
          type:'doughnut',data:{labels:wl,datasets:[{data:Object.values(byWilayah),backgroundColor:['#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5','#fff7ed'],borderWidth:0}]},
          options:{responsive:true,maintainAspectRatio:false,onClick:makeDoughnutClickHandler(wl,'jagung-filter-wilayah','jagung'),plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}
        });
      }
    }else if(tab==='beras'){
      document.getElementById('beras-total').textContent=fmtNum(total);
      const tw=Object.entries(byWilayah)[0];
      document.getElementById('beras-top-gudang-name').textContent=tw?shortName(tw[0]):'-';
      document.getElementById('beras-top-gudang-val').textContent=tw?fmtNum(tw[1])+' kg':'-';
      destroyChart('beras-monthly');
      var berasMonthLabels=months.map(m=>MONTHS_SHORT[parseInt(m.substring(0,2))-1]||m);
      var berasMonthValues=months.map(m=>byMonth[m]||0);
      charts['beras-monthly']=new Chart(document.getElementById('beras-monthly'),{
        type:'bar',data:{labels:berasMonthLabels,datasets:[{label:'Kuantum (kg)',data:berasMonthValues,backgroundColor:'#3b82f6',borderRadius:6}]},
        options:{responsive:true,maintainAspectRatio:false,onClick:makeMonthlyClickHandler(months,'beras-filter-bulan','beras',berasMonthValues),plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>fmtKg(v)}}}}
      });
      destroyChart('beras-wilayah');
      const wl=Object.keys(byWilayah);if(wl.length){
        charts['beras-wilayah']=new Chart(document.getElementById('beras-wilayah'),{
          type:'doughnut',data:{labels:wl.map(w=>shortName(w)),datasets:[{data:Object.values(byWilayah),backgroundColor:['#3b82f6','#60a5fa','#93c5fd','#bfdbfe'],borderWidth:0}]},
          options:{responsive:true,maintainAspectRatio:false,onClick:makeDoughnutClickHandler(wl,'beras-filter-gudang','beras'),plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}
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

  var GKP_TARGET=74692000;
  function drawGkpGauge(total){
    destroyChart('gkp-gauge');
    var pct=Math.round(total/GKP_TARGET*1000)/10;
    var remaining=Math.max(0,GKP_TARGET-total);
    var color=pct>=100?'#22c55e':pct>=75?'#6366f1':pct>=50?'#eab308':'#ef4444';
    try{
      charts['gkp-gauge']=new Chart(document.getElementById('gkp-gauge'),{
        type:'doughnut',
        data:{labels:['Tercapai','Sisa'],datasets:[{data:[Math.min(pct,100),Math.max(0,100-pct)],backgroundColor:[color,'#2a2d3a'],borderWidth:0,circumference:180,rotation:270}]},
        options:{responsive:true,maintainAspectRatio:false,cutout:'75%',plugins:{legend:{display:false},tooltip:{callbacks:{label:function(ctx){return ctx.label+': '+ctx.raw+'%'}}}}},
        plugins:[{id:'gkpGaugeText',afterDraw:function(chart){
          var ctx=chart.ctx,ca=chart.chartArea;
          var x=(ca.left+ca.right)/2,y=(ca.top+ca.bottom)/2.6;
          ctx.save();
          ctx.font='bold 36px -apple-system,sans-serif';ctx.fillStyle=color;ctx.textAlign='center';ctx.textBaseline='middle';
          ctx.fillText(pct+'%',x,y-8);
          ctx.font='13px -apple-system,sans-serif';ctx.fillStyle='#8b90a0';
          ctx.fillText(fmtKg(total)+' / '+fmtKg(GKP_TARGET)+' kg',x,y+22);
          ctx.restore();
        }}]
      });
    }catch(e){console.warn('gkp-gauge error:',e);}
  }
  function parseTanggal(s){
    if(!s)return null;
    var parts=s.split('/');
    if(parts.length!==3)return null;
    return new Date(parseInt(parts[2]),parseInt(parts[1])-1,parseInt(parts[0]));
  }
  function isSameDay(d1,d2){
    return d1.getFullYear()===d2.getFullYear()&&d1.getMonth()===d2.getMonth()&&d1.getDate()===d2.getDate();
  }
  function populatePoHariIni(){
    var raw=d.gkp.raw||[];
    var today=new Date();
    var poToday=raw.filter(function(r){
      var dt=parseTanggal(r.tanggal_po);
      return dt&&isSameDay(dt,today);
    });
    var tbody=document.getElementById('gkp-po-tbody');
    tbody.innerHTML='';
    var totalQty=0;
    poToday.forEach(function(r,i){
      totalQty+=r.qty||0;
      var tr=document.createElement('tr');
      tr.innerHTML='<td>'+(i+1)+'</td><td>'+r.nama_pemasok+'</td><td>'+r.wilayah+'</td><td>'+fmtNum(r.qty||0)+'</td>';
      tbody.appendChild(tr);
    });
    document.getElementById('gkp-po-count').textContent=poToday.length;
    document.getElementById('gkp-po-total').textContent=fmtNum(Math.round(totalQty));
    if(poToday.length===0){
      var tr=document.createElement('tr');
      tr.innerHTML='<td colspan="4" style="text-align:center;color:var(--sub);padding:20px">Tidak ada PO hari ini</td>';
      tbody.appendChild(tr);
    }
  }
  function drawAll(){
    var gkpFullNames=Object.keys(d.gkp.by_pemasok);
    var gkpMonthData=MONTHS.map(function(m){return d.gkp.by_month[m]||0});
    drawGkpGauge(d.gkp.total);
    populatePoHariIni();
    chartOrError('gkp-monthly', function(c){return new Chart(c, {type:'bar',data:{labels:MONTHS_SHORT,datasets:[{label:'Kuantum (kg)',data:gkpMonthData,backgroundColor:'#6366f1',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,onClick:makeMonthlyClickHandler(MONTHS,'gkp-filter-bulan','gkp',gkpMonthData),plugins:{legend:{display:false}},scales:{y:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});
    var gkpWL=Object.keys(d.gkp.by_wilayah);
    chartOrError('gkp-wilayah', function(c){return new Chart(c, {type:'doughnut',data:{labels:gkpWL,datasets:[{data:Object.values(d.gkp.by_wilayah),backgroundColor:COLORS}]},options:{responsive:true,maintainAspectRatio:false,onClick:makeDoughnutClickHandler(gkpWL,'gkp-filter-wilayah','gkp'),plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}})});
    chartOrError('gkp-mitra', function(c){return new Chart(c, {type:'bar',data:{labels:gkpFullNames.map(function(n){return shortName(n)}),datasets:[{label:'Kuantum (kg)',data:Object.values(d.gkp.by_pemasok),backgroundColor:COLORS,borderRadius:4}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,onClick:makeBarClickHandler(gkpFullNames,'gkp-filter-pemasok','gkp'),plugins:{legend:{display:false}},scales:{x:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});

    var jagungWL=Object.keys(d.jagung.by_wilayah);
    var jagungMonthData=MONTHS.map(function(m){return d.jagung.by_month[m]||0});
    chartOrError('jagung-monthly', function(c){return new Chart(c, {type:'bar',data:{labels:MONTHS_SHORT,datasets:[{label:'Kuantum (kg)',data:jagungMonthData,backgroundColor:'#f97316',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,onClick:makeMonthlyClickHandler(MONTHS,'jagung-filter-bulan','jagung',jagungMonthData),plugins:{legend:{display:false}},scales:{y:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});
    chartOrError('jagung-wilayah', function(c){return new Chart(c, {type:'doughnut',data:{labels:jagungWL,datasets:[{data:Object.values(d.jagung.by_wilayah),backgroundColor:['#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5','#fff7ed']}]},options:{responsive:true,maintainAspectRatio:false,onClick:makeDoughnutClickHandler(jagungWL,'jagung-filter-wilayah','jagung'),plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}})});

    var berasWL=Object.keys(d.beras_pso.by_wilayah).map(function(w){return shortName(w)});
    var berasFullWL=Object.keys(d.beras_pso.by_wilayah);
    var berasMonthData=MONTHS.slice(0,3).map(function(m){return d.beras_pso.by_month[m]||0});
    chartOrError('beras-monthly', function(c){return new Chart(c, {type:'bar',data:{labels:MONTHS_SHORT.slice(0,3),datasets:[{label:'Kuantum (kg)',data:berasMonthData,backgroundColor:'#3b82f6',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,onClick:makeMonthlyClickHandler(MONTHS.slice(0,3),'beras-filter-bulan','beras',berasMonthData),plugins:{legend:{display:false}},scales:{y:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});
    chartOrError('beras-wilayah', function(c){return new Chart(c, {type:'doughnut',data:{labels:berasWL,datasets:[{data:Object.values(d.beras_pso.by_wilayah),backgroundColor:['#3b82f6','#60a5fa','#93c5fd','#bfdbfe'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,onClick:makeDoughnutClickHandler(berasFullWL,'beras-filter-gudang','beras'),plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}})});

    var top10 = d.pengolahan.mitra.slice(0,10);
    var olahLabels = top10.map(function(m){return shortName(m.nama)});
    var olahFullNames = top10.map(function(m){return m.nama});
    chartOrError('olah-mitra', function(c){return new Chart(c, {type:'bar',data:{labels:olahLabels,datasets:[{label:'Pengadaan',data:top10.map(function(m){return m.pengadaan}),backgroundColor:'#6366f1',borderRadius:4},{label:'Diolah',data:top10.map(function(m){return m.pengolahan}),backgroundColor:'#22c55e',borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',onClick:makeBarClickHandler(olahFullNames,'olah-search','pengolahan'),plugins:{legend:{labels:{color:'#8b90a0'}}},scales:{x:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});
    chartOrError('olah-fisik', function(c){return new Chart(c, {type:'bar',data:{labels:olahLabels,datasets:[{label:'Pengadaan GKP',data:top10.map(function(m){return m.pengadaan}),backgroundColor:'#6366f1',borderRadius:4},{label:'Pemasukan Fisik',data:top10.map(function(m){return m.pengolahan}),backgroundColor:'#22c55e',borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',onClick:makeBarClickHandler(olahFullNames,'olah-search','pengolahan'),plugins:{legend:{labels:{color:'#8b90a0'}}},scales:{x:{ticks:{callback:function(v){return fmtKg(v)}}}}}})});
    chartOrError('olah-rendeman', function(c){return new Chart(c, {type:'bar',data:{labels:olahLabels,datasets:[{label:'Rasio (%)',data:top10.map(function(m){return m.rasio}),backgroundColor:top10.map(function(m){return m.rasio>40?'#22c55e':m.rasio>20?'#eab308':'#ef4444'}),borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',onClick:makeBarClickHandler(olahFullNames,'olah-search','pengolahan'),plugins:{legend:{display:false}},scales:{x:{min:0,max:100,ticks:{callback:function(v){return v+'%'}}}}}})});

    var rasio=d.pengolahan.rasio, gto=d.pengolahan.total_olah, gtp=d.pengolahan.total_pengadaan;
    chartOrError('olah-gauge', function(c){var gaugePlugin={id:'gaugeTextMain',afterDraw:function(chart){var ctx3=chart.ctx,ca3=chart.chartArea;var x3=(ca3.left+ca3.right)/2,y3=(ca3.top+ca3.bottom)/2.6;ctx3.save();ctx3.font='bold 32px -apple-system,sans-serif';ctx3.fillStyle='#e1e4ed';ctx3.textAlign='center';ctx3.textBaseline='middle';ctx3.fillText(rasio+'%',x3,y3-8);ctx3.font='14px -apple-system,sans-serif';ctx3.fillStyle='#8b90a0';ctx3.fillText('Diolah '+fmtKg(gto)+' / Total '+fmtKg(gtp)+' kg',x3,y3+22);ctx3.restore()}};return new Chart(c, {type:'doughnut',data:{labels:['Diolah','Sisa'],datasets:[{data:[rasio,100-rasio],backgroundColor:['#eab308','#2a2d3a'],borderWidth:0,circumference:180,rotation:270}]},options:{responsive:true,maintainAspectRatio:false,cutout:'75%',plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12,usePointStyle:true}},tooltip:{callbacks:{label:function(ctx){return ctx.label+': '+ctx.raw+'%'}}}}},plugins:[gaugePlugin]})});

    if(d.gkp.raw) populateFilters('gkp');
    if(d.jagung.raw) populateFilters('jagung');
    if(d.beras_pso.raw) populateFilters('beras');
  }

  drawAll();

  // Format pengolahan KPI cards with compact numbers on load
  (function formatPengolahanKPIs(){
    var ids = ['olah-total-pengadaan','olah-total-pengadaan-beras','olah-total-diolah','olah-total-diolah-beras','olah-total-sisa','olah-total-sisa-beras'];
    ids.forEach(function(id){
      var el = document.getElementById(id);
      if(!el) return;
      var txt = el.textContent.trim();
      var num = parseFloat(txt.replace(/\./g,'').replace(/,/g,'.'));
      if(!isNaN(num)) el.textContent = fmtNum(num);
    });
  })();

  window.exportData = function(type, tab){
    var key = tab === 'beras_pso' ? 'beras_pso' : tab;
    var tabData = d[key];
    if(!tabData){ showToast('error','Error','Data tidak ditemukan'); return; }

    function esc(s){ return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

    // Sanitize header to match raw data keys (mirrors PHP sanitizeHeader)
    function sanitizeHeader(h){
      return String(h).trim().toLowerCase().replace(/[^a-z0-9\s_]/g,'').replace(/\s+/g,'_');
    }

    var headers = (tabData.header || []).map(function(h){ return h.replace(/_/g,' ').replace(/\b\w/g,function(c){return c.toUpperCase()}); });
    var raw = [];
    if(tab === 'pengolahan'){
      raw = (tabData.mitra || []).map(function(m){
        var row = {};
        (tabData.header||[]).forEach(function(h,i){ row[sanitizeHeader(h)] = m[sanitizeHeader(h)] || ''; });
        return row;
      });
      var q = document.getElementById('olah-search').value.toLowerCase();
      if(q) raw = raw.filter(function(r){ return (r[sanitizeHeader('nama_mitra')]||'').toLowerCase().indexOf(q)!==-1; });
    } else {
      raw = getFilteredRaw(key);
    }

    if(type === 'csv'){
      var csvRows = [headers.join(',')];
      raw.forEach(function(row){
        var vals = (tabData.header||[]).map(function(h){ var v=row[sanitizeHeader(h)]||''; return '"'+String(v).replace(/"/g,'""')+'"'; });
        csvRows.push(vals.join(','));
      });
      var blob = new Blob(['\uFEFF'+csvRows.join('\n')], {type:'text/csv;charset=utf-8;'});
      var a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='dashboard-'+tab+'.csv'; a.click();
    } else if(type === 'xlsx'){
      // Use server-side export for real .xlsx format (via PhpSpreadsheet)
      var exportTab = tab;
      var params = new URLSearchParams();

      if(tab === 'pengolahan'){
        var q = document.getElementById('olah-search').value;
        if(q) params.set('search', q);
      } else {
        var prefix = tab === 'beras_pso' ? 'beras' : tab;
        var bulan = document.getElementById(prefix+'-filter-bulan').value;
        var semester = document.getElementById(prefix+'-filter-semester').value;
        if(bulan) params.set('bulan', bulan);
        if(semester) params.set('semester', semester);

        if(tab === 'gkp'){
          var wil = document.getElementById(prefix+'-filter-wilayah').value;
          var pem = document.getElementById(prefix+'-filter-pemasok').value;
          if(wil) params.set('wilayah', wil);
          if(pem) params.set('pemasok', pem);
        } else if(tab === 'jagung'){
          var wil = document.getElementById(prefix+'-filter-wilayah').value;
          if(wil) params.set('wilayah', wil);
        } else if(tab === 'beras_pso'){
          var gud = document.getElementById(prefix+'-filter-gudang').value;
          if(gud) params.set('gudang', gud);
        }
      }

      var url = '/export/xlsx/' + exportTab;
      if(params.toString()) url += '?' + params.toString();
      window.location.href = url;
    } else if(type === 'pdf'){
      var w = window.open('','_blank');
      var html = '<html><head><title>Dashboard '+esc(tab)+'</title><style>body{font-family:Arial;margin:20px}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ddd;padding:8px;text-align:left;font-size:12px}th{background:#f3f4f6;font-weight:bold}</style></head><body>';
      html += '<h1>Dashboard Bulog - '+esc(tab.toUpperCase())+'</h1><p>'+new Date().toLocaleString('id-ID')+'</p>';
      html += '<table><tr>'+headers.map(function(h){return '<th>'+esc(h)+'</th>'}).join('')+'</tr>';
      raw.forEach(function(row){
        html += '<tr>'+(tabData.header||[]).map(function(h){return '<td>'+esc(row[sanitizeHeader(h)]||'')+'</td>'}).join('')+'</tr>';
      });
      html += '</table></body></html>';
      w.document.write(html); w.document.close(); w.print();
    }
  };
})();
</script>
@endsection
