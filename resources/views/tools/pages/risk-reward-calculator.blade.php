<div class="field">
  <span class="fl">入场价（元）</span>
  <input type="text" id="rrEntry" value="10.00">
</div>
<div class="field">
  <span class="fl">止损价（元）</span>
  <input type="text" id="rrStop" value="9.50">
</div>
<div class="field">
  <span class="fl">止盈价（元）</span>
  <input type="text" id="rrTarget" value="11.50">
</div>
<div class="btn-row">
  <button class="btn" id="rrCalc">计算风险回报比</button>
</div>
<div id="rrResult" style="display:none">
  <table class="si-table">
    <tbody>
      <tr><td>每股风险（入场 → 止损）</td><td id="rrRisk"></td></tr>
      <tr><td>每股回报（入场 → 止盈）</td><td id="rrReward"></td></tr>
      <tr><td>打平所需胜率</td><td id="rrBE"></td></tr>
      <tr><td>止损幅度 / 止盈幅度</td><td id="rrPct"></td></tr>
    </tbody>
    <tfoot><tr><td>风险回报比 R:R</td><td id="rrVal"></td></tr></tfoot>
  </table>
  <div class="si-summary" id="rrVerdict"></div>
</div>

<style>
#rrResult .si-table td:last-child{color:var(--accent);font-weight:700}
#rrResult tfoot td{font-size:1.05rem}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  $('rrCalc').addEventListener('click', function(){
    var entry = parseFloat($('rrEntry').value) || 0;
    var stop = parseFloat($('rrStop').value) || 0;
    var target = parseFloat($('rrTarget').value) || 0;
    var risk = Math.abs(entry - stop);
    var reward = Math.abs(target - entry);

    if(entry <= 0 || risk === 0 || reward === 0){
      $('rrVerdict').textContent = '请检查输入：三个价格都需大于 0，且入场价不能与止损价或止盈价相同。';
      $('rrResult').style.display = 'block';
      return;
    }

    var rr = reward / risk;
    var be = 1 / (1 + rr) * 100;
    var stopPct = risk / entry * 100, tgtPct = reward / entry * 100;

    $('rrRisk').textContent = risk.toFixed(4).replace(/0+$/,'').replace(/\.$/,'') + ' 元（-' + stopPct.toFixed(2) + '%）';
    $('rrReward').textContent = reward.toFixed(4).replace(/0+$/,'').replace(/\.$/,'') + ' 元（+' + tgtPct.toFixed(2) + '%）';
    $('rrBE').textContent = be.toFixed(1) + '%';
    $('rrPct').textContent = stopPct.toFixed(2) + '% / ' + tgtPct.toFixed(2) + '%';
    $('rrVal').textContent = '1 : ' + rr.toFixed(2);

    var verdict;
    if(rr >= 3) verdict = 'R:R ≥ 1:3：性价比很高，胜率只需 ' + be.toFixed(0) + '% 就不亏，值得考虑。';
    else if(rr >= 2) verdict = 'R:R ≥ 1:2：不错，胜率 33% 以上就能打平，专业交易者最常选的区间。';
    else if(rr >= 1) verdict = 'R:R 1:1~1:2：一般，需要 50% 以上的胜率撑着，胜率没把握就别进。';
    else verdict = 'R:R < 1:1：冒大的风险博小的回报，这种机会通常不值得下单。';
    $('rrVerdict').textContent = verdict;
    $('rrResult').style.display = 'block';
  });
})();
</script>

<div class="explain">
  <h3>公式 & 这个指标是什么意思</h3>
  <p class="ex-formula">R:R = |止盈价 − 入场价| / |入场价 − 止损价|<br>打平所需胜率 = 1 / (1 + R:R)</p>
  <p class="ex-text">R:R = 2 意思是「冒 1 元的风险，博 2 元的空间」。它是下单前最后一道闸门：<b>先看 R:R 值不值得，再谈方向判断</b>。配合「打平胜率」一起看就明白专业交易者为什么可以经常错——R:R 为 2 时胜率只需 33% 就不亏，为 3 时只需 25%。反过来，追求高 R:R 的代价是胜率下降（止盈更远更难打到），所以<b> R:R、胜率、盈亏比三者要放在一起权衡</b>，没有单独成立的指标。</p>
</div>
