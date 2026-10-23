<div class="field">
  <span class="fl">每笔交易盈亏（元或 %），盈利为正、亏损为负，逗号或换行分隔</span>
  <textarea id="plTrades" rows="4" placeholder="300, -150, 500, -200, 250">300, -150, 500, -200, 250, -100, 800, -150, 400, -300</textarea>
</div>
<div class="btn-row">
  <button class="btn" id="plCalc">计算盈亏比</button>
</div>
<div id="plResult" style="display:none">
  <table class="si-table">
    <tbody>
      <tr><td>盈利笔数 / 亏损笔数</td><td id="plCount"></td></tr>
      <tr><td>平均单笔盈利</td><td id="plAvgWin"></td></tr>
      <tr><td>平均单笔亏损</td><td id="plAvgLoss"></td></tr>
      <tr><td>胜率（参考）</td><td id="plWinRate"></td></tr>
    </tbody>
    <tfoot><tr><td>盈亏比 盈利均值 ÷ 亏损均值</td><td id="plVal"></td></tr></tfoot>
  </table>
  <div class="si-summary" id="plVerdict"></div>
</div>

<style>
#plResult .si-table td:last-child{color:var(--accent);font-weight:700}
#plResult tfoot td{font-size:1.05rem}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  $('plCalc').addEventListener('click', function(){
    var raw = $('plTrades').value.trim();
    var trades = raw.split(/[,，\s]+/).map(Number).filter(function(v){ return !isNaN(v) && v !== 0; });
    if(trades.length < 2){ $('plVerdict').textContent = '至少需要 2 笔交易记录。'; $('plResult').style.display = 'block'; return; }

    var wins = [], losses = [];
    trades.forEach(function(t){ (t > 0 ? wins : losses).push(t); });
    if(!wins.length || !losses.length){ $('plVerdict').textContent = '需要同时包含盈利和亏损的交易记录。'; $('plResult').style.display = 'block'; return; }

    var avgWin = wins.reduce(function(a,b){ return a+b; }, 0) / wins.length;
    var avgLoss = Math.abs(losses.reduce(function(a,b){ return a+b; }, 0) / losses.length);
    var ratio = avgLoss === 0 ? null : avgWin / avgLoss;
    var winRate = wins.length / trades.length * 100;

    $('plCount').textContent = wins.length + ' 笔 / ' + losses.length + ' 笔';
    $('plAvgWin').textContent = avgWin.toFixed(2);
    $('plAvgLoss').textContent = avgLoss.toFixed(2);
    $('plWinRate').textContent = winRate.toFixed(1) + '%';
    $('plVal').textContent = ratio === null ? '∞（没有亏损记录）' : ratio.toFixed(2) + ' : 1';

    var verdict;
    if(ratio === null) verdict = '从没亏过，样本不具备参考性。';
    else if(ratio >= 3) verdict = '盈亏比 ≥ 3：赚大亏小，即使胜率不高也能稳定盈利。';
    else if(ratio >= 1.5) verdict = '盈亏比 ≥ 1.5：不错，配合 40% 以上的胜率就是正期望系统。';
    else if(ratio >= 1) verdict = '盈亏比 1~1.5：偏弱，需要较高的胜率才能打平。';
    else verdict = '盈亏比 < 1：赚小亏大，这是最危险的结构，胜率再高也容易被一次亏损抹平。';
    $('plVerdict').textContent = verdict;
    $('plResult').style.display = 'block';
  });
})();
</script>

<div class="explain">
  <h3>公式 & 这个指标是什么意思</h3>
  <p class="ex-formula">盈亏比 = 平均单笔盈利金额 / 平均单笔亏损金额</p>
  <p class="ex-text">盈亏比衡量<b>「赚的时候赚多大，亏的时候亏多小」</b>。10 次小亏换 1 次大赚的趋势交易系统，盈亏比可以达到 5:1，胜率不到 40% 也照样长期赚钱；反过来，胜率 90% 但每次亏损都比盈利大得多，一次意外就能抹掉全部利润。它和胜率是一对搭档：<b>胜率低就得靠高盈亏比来补，盈亏比低就得靠高胜率来撑</b>——单独看任何一个都没有意义。</p>
</div>
