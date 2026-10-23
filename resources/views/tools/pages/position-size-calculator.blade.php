<div class="field">
  <span class="fl">账户资金（元）</span>
  <input type="text" id="psCapital" value="100000">
</div>
<div class="field">
  <span class="fl">单笔风险比例（%，默认 2）</span>
  <input type="text" id="psRisk" value="2">
</div>
<div class="field">
  <span class="fl">入场价（元）</span>
  <input type="text" id="psEntry" value="10.00">
</div>
<div class="field">
  <span class="fl">止损价（元）</span>
  <input type="text" id="psStop" value="9.50">
</div>
<div class="btn-row">
  <button class="btn" id="psCalc">计算仓位</button>
</div>
<div id="psResult" style="display:none">
  <table class="si-table">
    <tbody>
      <tr><td>本笔最大亏损额</td><td id="psMaxLoss"></td></tr>
      <tr><td>每股（单位）风险</td><td id="psPerUnit"></td></tr>
      <tr><td>应买数量</td><td id="psQty"></td></tr>
      <tr><td>动用资金</td><td id="psUsed"></td></tr>
      <tr><td>仓位占比</td><td id="psPosPct"></td></tr>
      <tr><td>打满止损后账户剩余</td><td id="psLeft"></td></tr>
    </tbody>
    <tfoot><tr><td>结论</td><td id="psVal"></td></tr></tfoot>
  </table>
  <div class="si-summary" id="psVerdict"></div>
</div>

<style>
#psResult .si-table td:last-child{color:var(--accent);font-weight:700}
#psResult tfoot td{font-size:1.05rem}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };
  function yuan(v){ return v.toLocaleString('zh-CN', {minimumFractionDigits:2, maximumFractionDigits:2}); }

  $('psCalc').addEventListener('click', function(){
    var capital = parseFloat($('psCapital').value) || 0;
    var riskPct = (parseFloat($('psRisk').value) || 0) / 100;
    var entry = parseFloat($('psEntry').value) || 0;
    var stop = parseFloat($('psStop').value) || 0;
    var perUnit = Math.abs(entry - stop);

    if(capital <= 0 || riskPct <= 0 || entry <= 0 || perUnit <= 0){
      $('psVerdict').textContent = '请检查输入：资金、风险比例、入场价需大于 0，且入场价不能等于止损价。';
      $('psResult').style.display = 'block';
      return;
    }

    var maxLoss = capital * riskPct;
    var qty = Math.floor(maxLoss / perUnit);
    var used = qty * entry;
    var posPct = used / capital * 100;

    $('psMaxLoss').textContent = yuan(maxLoss) + ' 元';
    $('psPerUnit').textContent = perUnit.toFixed(4).replace(/0+$/,'').replace(/\.$/,'') + ' 元';
    $('psQty').textContent = qty.toLocaleString('zh-CN') + ' 股（手：' + Math.floor(qty / 100) + ' 手 + ' + (qty % 100) + ' 股）';
    $('psUsed').textContent = yuan(used) + ' 元';
    $('psPosPct').textContent = posPct.toFixed(1) + '%';
    $('psLeft').textContent = yuan(capital - maxLoss) + ' 元';
    $('psVal').textContent = qty > 0 ? '买入 ' + qty.toLocaleString('zh-CN') : '数量为 0';

    var verdict;
    if(qty <= 0) verdict = '按这个止损距离，单笔风险额度买不起最小单位，要么放宽风险比例，要么换止损更近的机会。';
    else if(posPct > 80) verdict = '注意：虽然单笔风险只有 ' + (riskPct * 100).toFixed(1) + '%，但止损太近导致仓位占比高达 ' + posPct.toFixed(0) + '%，实际杠杆不小，注意极端行情下跳空止损会亏超额度。';
    else verdict = '打满止损也只亏账户的 ' + (riskPct * 100).toFixed(1) + '%，连续错 10 次也还有底气开下一单。';
    $('psVerdict').textContent = verdict;
    $('psResult').style.display = 'block';
  });
})();
</script>

<div class="explain">
  <h3>公式 & 这个指标是什么意思</h3>
  <p class="ex-formula">应买数量 = (账户资金 × 风险比例) / |入场价 − 止损价|<br>动用资金 = 应买数量 × 入场价</p>
  <p class="ex-text">这个公式把「这笔交易最多亏多少」变成一个<b>事先确定的数字</b>，顺序和新手正好相反：不是先决定买多少、再祈祷别跌，而是<b>先定死可承受的亏损，再反推能买多少</b>。单笔风险一般控制在账户的 1%~2%——这样连续错 10 次也只亏 10%~20%，永远有下一局的资本。爆仓的人大多不是看错方向，而是仓位太重，<b>一次错误就伤筋动骨，没有下一次了</b>。</p>
</div>
