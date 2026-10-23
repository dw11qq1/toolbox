<div class="field">
  <span class="fl">月工资基数（元）</span>
  <input type="text" id="siBase" value="8000">
</div>
<div class="field">
  <span class="fl">所在地缴费比例（默认全国常用比例，可改）</span>
  <div class="si-rates" id="siRates">
    <label>养老 个人 <input type="number" id="rPensionP" value="8" step="0.5">%</label>
    <label>养老 单位 <input type="number" id="rPensionC" value="16" step="0.5">%</label>
    <label>医疗 个人 <input type="number" id="rMedP" value="2" step="0.5">%</label>
    <label>医疗 单位 <input type="number" id="rMedC" value="8" step="0.5">%</label>
    <label>失业 个人 <input type="number" id="rUnempP" value="0.5" step="0.1">%</label>
    <label>失业 单位 <input type="number" id="rUnempC" value="0.5" step="0.1">%</label>
    <label>公积金 个人 <input type="number" id="rFundP" value="12" step="1">%</label>
    <label>公积金 单位 <input type="number" id="rFundC" value="12" step="1">%</label>
  </div>
</div>
<div class="btn-row">
  <button class="btn" id="siCalc">计算</button>
</div>
<div id="siResult" style="display:none">
  <table class="si-table">
    <thead><tr><th>险种</th><th>个人 %</th><th>个人月缴</th><th>单位 %</th><th>单位月缴</th></tr></thead>
    <tbody id="siBody"></tbody>
    <tfoot id="siFoot"></tfoot>
  </table>
  <div class="si-summary" id="siSummary"></div>
</div>

<style>
#siRates{display:grid;grid-template-columns:1fr 1fr;gap:0.5rem}
#siRates label{display:flex;align-items:center;gap:0.4rem;font-size:0.8rem;color:var(--ink-2)}
#siRates input{width:64px;font-family:var(--font-mono);font-size:0.85rem;padding:0.3rem 0.5rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px}
.si-table{width:100%;border-collapse:collapse;font-size:0.85rem;margin-top:0.5rem}
.si-table th,.si-table td{border-bottom:1px solid var(--line);padding:0.5rem 0.6rem;text-align:right;font-variant-numeric:tabular-nums}
.si-table th{color:var(--ink-3);font-weight:400;font-family:var(--font-mono);font-size:0.72rem}
.si-table td:first-child,.si-table th:first-child{text-align:left}
.si-table tfoot td{font-weight:700;color:var(--accent);border-bottom:none}
.si-summary{margin-top:0.9rem;font-size:0.95rem;color:var(--ink-2);line-height:1.9}
.si-summary b{color:var(--ink)}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };
  function num(id){ return parseFloat($(id).value) || 0; }
  function yuan(v){ return v.toLocaleString('zh-CN', {minimumFractionDigits:2, maximumFractionDigits:2}); }

  $('siCalc').addEventListener('click', function(){
    var base = num('siBase');
    if(base <= 0){ return; }
    var items = [
      ['养老保险', num('rPensionP'), num('rPensionC')],
      ['医疗保险', num('rMedP'), num('rMedC')],
      ['失业保险', num('rUnempP'), num('rUnempC')],
      ['公积金', num('rFundP'), num('rFundC')],
    ];
    var body = '', sumP = 0, sumC = 0;
    items.forEach(function(it){
      var p = base * it[1] / 100, c = base * it[2] / 100;
      sumP += p; sumC += c;
      body += '<tr><td>' + it[0] + '</td><td>' + it[1] + '%</td><td>' + yuan(p) + '</td><td>' + it[2] + '%</td><td>' + yuan(c) + '</td></tr>';
    });
    $('siBody').innerHTML = body;
    $('siFoot').innerHTML = '<tr><td>合计</td><td></td><td>' + yuan(sumP) + '</td><td></td><td>' + yuan(sumC) + '</td></tr>';
    var total = sumP + sumC;
    $('siSummary').innerHTML =
      '个人月缴合计 <b>¥' + yuan(sumP) + '</b>（占工资 ' + (sumP / base * 100).toFixed(1) + '%）<br>' +
      '单位月缴合计 <b>¥' + yuan(sumC) + '</b>（占工资 ' + (sumC / base * 100).toFixed(1) + '%）<br>' +
      '单单位合计支出 <b>¥' + yuan(total) + '</b> · 你的税前到手约 <b>¥' + yuan(base - sumP) + '</b><br>' +
      '<span style="font-size:0.8rem;color:var(--ink-3)">注：公积金个人+单位部分都进你的账户，实际总收益 = 到手 + 公积金账户。各地比例略有差异，请以当地政策为准。</span>';
    $('siResult').style.display = 'block';
  });
})();
</script>
