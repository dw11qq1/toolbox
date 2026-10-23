<div class="field">
  <span class="fl">胜率（%）</span>
  <input type="text" id="evWinRate" value="45">
</div>
<div class="field">
  <span class="fl">平均单笔盈利（元）</span>
  <input type="text" id="evAvgWin" value="600">
</div>
<div class="field">
  <span class="fl">平均单笔亏损（元）</span>
  <input type="text" id="evAvgLoss" value="300">
</div>
<div class="btn-row">
  <button class="btn" id="evCalc">计算期望值</button>
</div>
<div id="evResult" style="display:none">
  <table class="si-table">
    <tbody>
      <tr><td>盈利贡献（胜率 × 平均盈利）</td><td id="evGain"></td></tr>
      <tr><td>亏损拖累（败率 × 平均亏损）</td><td id="evLoss"></td></tr>
      <tr><td>每 100 笔预估盈亏</td><td id="ev100"></td></tr>
    </tbody>
    <tfoot><tr><td>每笔期望值 EV</td><td id="evVal"></td></tr></tfoot>
  </table>
  <div class="si-summary" id="evVerdict"></div>
</div>

<style>
#evResult .si-table td:last-child{color:var(--accent);font-weight:700}
#evResult tfoot td{font-size:1.05rem}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  $('evCalc').addEventListener('click', function(){
    var p = (parseFloat($('evWinRate').value) || 0) / 100;
    var win = parseFloat($('evAvgWin').value) || 0;
    var loss = Math.abs(parseFloat($('evAvgLoss').value) || 0);
    if(p <= 0 || p >= 1 || win <= 0 || loss <= 0){ $('evVerdict').textContent = '请检查输入：胜率需在 0~100 之间，盈亏金额需大于 0。'; $('evResult').style.display = 'block'; return; }

    var ev = p * win - (1 - p) * loss;
    var gain = p * win, drag = (1 - p) * loss;

    $('evGain').textContent = '+' + gain.toFixed(2) + ' 元';
    $('evLoss').textContent = '-' + drag.toFixed(2) + ' 元';
    $('ev100').textContent = (ev * 100 >= 0 ? '+' : '') + (ev * 100).toFixed(0) + ' 元';
    $('evVal').textContent = (ev >= 0 ? '+' : '') + ev.toFixed(2) + ' 元';

    var verdict;
    if(ev > 0) verdict = '期望值为正：交易次数越多，越接近稳定盈利（大数定律站在你这边）。';
    else if(ev === 0) verdict = '期望值为零：纯抛硬币，扣掉手续费和滑点其实是亏的。';
    else verdict = '期望值为负：每做一笔都在预期亏损，再高的胜率也只是慢性失血，先别加仓。';
    $('evVerdict').textContent = verdict;
    $('evResult').style.display = 'block';
  });
})();
</script>

<div class="explain">
  <h3>公式 & 这个指标是什么意思</h3>
  <p class="ex-formula">EV = 胜率 × 平均盈利 − (1 − 胜率) × 平均亏损</p>
  <p class="ex-text">期望值是交易系统的「发动机」：<b>只要 EV 为正，做得越多赚得越稳（大数定律）；EV 为负，任何资金管理都救不了</b>。很多人输在只盯着胜率不看亏损端——胜率 80% 很漂亮，但如果那 20% 的亏损是盈利的 6 倍，EV 依然是负的。试着算一算：每笔哪怕只赚 10 元的期望，做 500 笔也是 5000 元，<b>正期望的小系统远比「一夜暴富」的幻想可靠</b>。</p>
</div>
