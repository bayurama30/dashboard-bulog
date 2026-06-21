@extends('layouts.app')

@section('content')

{{-- ================ GKP TAB ================ --}}
<div id="tab-gkp" class="tab-content" style="display:{{ $activeTab === 'gkp' ? 'block' : 'none' }}">
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Total Pengadaan GKP</div><div class="value" style="color:var(--accent)">{{ number_format($data['gkp']['total'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Rata per Bulan</div><div class="value" style="color:var(--blue)">{{ number_format(round($data['gkp']['total']/count($data['gkp']['by_month'])), 0, ',', '.') }}</div><div class="sub">kg</div></div>
    @php $topW = array_key_first($data['gkp']['by_wilayah']); @endphp
    <div class="kpi"><div class="label">Wilayah Terbesar</div><div class="value" style="color:var(--green)">{{ $topW }}</div><div class="sub">{{ number_format($data['gkp']['by_wilayah'][$topW], 0, ',', '.') }} kg</div></div>
    @php $topM = array_key_first($data['gkp']['by_pemasok']); $topMshort = implode(' ', array_slice(explode(' ', $topM), 0, 2)); @endphp
    <div class="kpi"><div class="label">Mitra Teratas</div><div class="value" style="color:var(--purple)">{{ $topMshort }}</div><div class="sub">{{ number_format($data['gkp']['by_pemasok'][$topM], 0, ',', '.') }} kg</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-card"><h3>Tren Kuantum per Bulan</h3><div class="chart-wrap"><canvas id="gkp-monthly"></canvas></div></div>
    <div class="chart-card"><h3>Distribusi per Wilayah</h3><div class="chart-wrap"><canvas id="gkp-wilayah"></canvas></div></div>
    <div class="chart-card full"><h3>Top 15 Mitra</h3><div class="chart-wrap"><canvas id="gkp-mitra"></canvas></div></div>
  </div>
</div>

{{-- ================ JAGUNG TAB ================ --}}
<div id="tab-jagung" class="tab-content" style="display:{{ $activeTab === 'jagung' ? 'block' : 'none' }}">
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Total Pengadaan Jagung</div><div class="value" style="color:var(--orange)">{{ number_format($data['jagung']['total'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    @php $jtw = array_key_first($data['jagung']['by_wilayah']); @endphp
    <div class="kpi"><div class="label">Wilayah Terbesar</div><div class="value" style="color:var(--green)">{{ $jtw }}</div><div class="sub">{{ number_format($data['jagung']['by_wilayah'][$jtw], 0, ',', '.') }} kg</div></div>
    @php $jtm = array_reduce(array_keys($data['jagung']['by_month']), function($c, $i) use ($data) { $v = $data['jagung']['by_month'][$i]; return !$c || $v > $data['jagung']['by_month'][$c] ? $i : $c; }); @endphp
    <div class="kpi"><div class="label">Bulan Puncak</div><div class="value" style="color:var(--blue)">{{ $jtm }}</div><div class="sub">{{ number_format($data['jagung']['by_month'][$jtm], 0, ',', '.') }} kg</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-card"><h3>Tren Kuantum per Bulan</h3><div class="chart-wrap"><canvas id="jagung-monthly"></canvas></div></div>
    <div class="chart-card"><h3>Distribusi per Wilayah</h3><div class="chart-wrap"><canvas id="jagung-wilayah"></canvas></div></div>
  </div>
</div>

{{-- ================ BERAS PSO TAB ================ --}}
<div id="tab-beras_pso" class="tab-content" style="display:{{ $activeTab === 'beras_pso' ? 'block' : 'none' }}">
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Total Beras PSO</div><div class="value" style="color:var(--blue)">{{ number_format($data['beras_pso']['total'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    @php $btw = array_key_first($data['beras_pso']['by_wilayah']); $btwshort = str_replace('KOMPLEKS PERGUDANGAN ', '', $btw); @endphp
    <div class="kpi"><div class="label">Gudang Terbesar</div><div class="value" style="color:var(--green)">{{ $btwshort }}</div><div class="sub">{{ number_format($data['beras_pso']['by_wilayah'][$btw], 0, ',', '.') }} kg</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-card"><h3>Tren per Bulan</h3><div class="chart-wrap"><canvas id="beras-monthly"></canvas></div></div>
    <div class="chart-card"><h3>Distribusi per Gudang</h3><div class="chart-wrap"><canvas id="beras-wilayah"></canvas></div></div>
  </div>
</div>

{{-- ================ PENGOLAHAN TAB ================ --}}
<div id="tab-pengolahan" class="tab-content" style="display:{{ $activeTab === 'pengolahan' ? 'block' : 'none' }}">
  <div class="kpi-grid">
    <div class="kpi"><div class="label">Total GKP Diadakan</div><div class="value" style="color:var(--accent)">{{ number_format($data['pengolahan']['total_pengadaan'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Sudah Diolah</div><div class="value" style="color:var(--green)">{{ number_format($data['pengolahan']['total_olah'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Sisa Belum Diolah</div><div class="value" style="color:var(--red)">{{ number_format($data['pengolahan']['total_sisa'], 0, ',', '.') }}</div><div class="sub">kg</div></div>
    <div class="kpi"><div class="label">Rasio Pengolahan</div><div class="value" style="color:var(--yellow)">{{ $data['pengolahan']['rasio'] }}%</div><div class="sub">%</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-card"><h3>Perbandingan Pengadaan vs Diolah (Top 10)</h3><div class="chart-wrap"><canvas id="olah-mitra"></canvas></div></div>
    <div class="chart-card"><h3>Progress Gauge</h3><div class="chart-wrap short"><canvas id="olah-gauge"></canvas></div></div>
    <div class="chart-card full">
      <h3>Detail per Mitra ({{ count($data['pengolahan']['mitra']) }} mitra)</h3>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Mitra</th><th>Pengadaan (kg)</th><th>Diolah (kg)</th><th>Sisa (kg)</th><th>Rasio</th></tr></thead>
          <tbody>
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

  // GKP Charts
  new Chart(document.getElementById('gkp-monthly'), {type:'bar',data:{labels:MONTHS_SHORT,datasets:[{label:'Kuantum (kg)',data:MONTHS.map(m=>d.gkp.by_month[m]||0),backgroundColor:'#6366f1',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>fmtKg(v)}}}}});
  new Chart(document.getElementById('gkp-wilayah'), {type:'doughnut',data:{labels:Object.keys(d.gkp.by_wilayah),datasets:[{data:Object.values(d.gkp.by_wilayah),backgroundColor:COLORS,borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}});
  new Chart(document.getElementById('gkp-mitra'), {type:'bar',data:{labels:Object.keys(d.gkp.by_pemasok).map(n=>n.replace('CV. ','').replace('PD. ','')),datasets:[{label:'Kuantum (kg)',data:Object.values(d.gkp.by_pemasok),backgroundColor:COLORS,borderRadius:4}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{ticks:{callback:v=>fmtKg(v)}}}}});

  // Jagung Charts
  new Chart(document.getElementById('jagung-monthly'), {type:'bar',data:{labels:MONTHS_SHORT,datasets:[{label:'Kuantum (kg)',data:MONTHS.map(m=>d.jagung.by_month[m]||0),backgroundColor:'#f97316',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>fmtKg(v)}}}}});
  new Chart(document.getElementById('jagung-wilayah'), {type:'doughnut',data:{labels:Object.keys(d.jagung.by_wilayah),datasets:[{data:Object.values(d.jagung.by_wilayah),backgroundColor:['#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5','#fff7ed'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}});

  // Beras PSO Charts
  new Chart(document.getElementById('beras-monthly'), {type:'bar',data:{labels:MONTHS_SHORT.slice(0,3),datasets:[{label:'Kuantum (kg)',data:MONTHS.slice(0,3).map(m=>d.beras_pso.by_month[m]||0),backgroundColor:'#3b82f6',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>fmtKg(v)}}}}});
  new Chart(document.getElementById('beras-wilayah'), {type:'doughnut',data:{labels:Object.keys(d.beras_pso.by_wilayah).map(w=>w.replace('KOMPLEKS PERGUDANGAN ','')),datasets:[{data:Object.values(d.beras_pso.by_wilayah),backgroundColor:['#3b82f6','#60a5fa','#93c5fd','#bfdbfe'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{color:'#8b90a0',padding:12}}}}});

  // Pengolahan Charts
  const top10 = d.pengolahan.mitra.slice(0,10);
  new Chart(document.getElementById('olah-mitra'), {type:'bar',data:{labels:top10.map(m=>m.nama.replace('CV. ','').replace('PD. ','')),datasets:[{label:'Pengadaan',data:top10.map(m=>m.pengadaan),backgroundColor:'#6366f1',borderRadius:4},{label:'Diolah',data:top10.map(m=>m.pengolahan),backgroundColor:'#22c55e',borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',plugins:{legend:{labels:{color:'#8b90a0'}}},scales:{x:{ticks:{callback:v=>fmtKg(v)}}}}});
  new Chart(document.getElementById('olah-gauge'), {type:'doughnut',data:{datasets:[{data:[d.pengolahan.rasio,100-d.pengolahan.rasio],backgroundColor:['#eab308','#2a2d3a'],borderWidth:0,circumference:180,rotation:270}]},options:{responsive:true,maintainAspectRatio:false,cutout:'75%',plugins:{legend:{display:false},tooltip:{enabled:false}}}});
})();
</script>
@endsection
