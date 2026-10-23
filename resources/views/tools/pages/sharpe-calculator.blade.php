<div class="field">
  <span class="fl">每月收益率（%），逗号或换行分隔，如：2.1, -0.8, 3.4, 1.2</span>
  <textarea id="shReturns" rows="4" placeholder="2.1, -0.8, 3.4, 1.2, 0.5">2.1, -0.8, 3.4, 1.2, 0.5, -1.6, 2.8, 1.9</textarea>
</div>
<div class="field">
  <span class="fl">无风险年利率（%，默认 2）</span>
  <input type="text" id="shRiskFree" value="2">
</div>
<div class="btn-row">
  <button class="btn" id="shCalc">计算夏普比率</button>
</div>
<div id="shResult" style="display:none">
  <table class="si-table">
    <tbody>
      <tr><td>月均收益</td><td id="shMean"></td></tr>
      <tr><td>月度波动率</td><td id="shVolM"></td></tr>
      <tr><td>年化收益率</td><td id="shAnnRet"></td></tr>
      <tr><td>年化波动率</td><td id="shAnnVol"></td></tr>
    </tbody>
    <tfoot><tr><td>夏普比率 Sharpe Ratio</td><td id="shVal"></td></tr></tfoot>
  </table>
  <div class="si-summary" id="shVerdict"></div>
</div>

<style>
#shResult .si-table td:last-child{color:var(--accent);font-weight:700}
#shResult tfoot td{font-size:1.05rem}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  $('shCalc').addEventListener('click', function(){
    var raw = $('shReturns').value.trim();
    var rets = raw.split(/[,，\s]+/).map(Number).filter(function(v){ return !isNaN(v); });
    if(rets.length < 3){ $('shVerdict').textContent = '至少需要 3 个月收益率数据。'; $('shResult').style.display = 'block'; return; }

    var n = rets.length;
    var mean = rets.reduce(function(a,b){ return a+b; }, 0) / n;
    var variance = rets.reduce(function(a,b){ return a + Math.pow(b - mean, 2); }, 0) / (n - 1);
    var vol = Math.sqrt(variance);

    var rf = (parseFloat($('shRiskFree').value) || 0) / 100;
    var annRet = mean / 100 * 12;
    var annVol = vol / 100 * Math.sqrt(12);
    var sharpe = annVol === 0 ? null : (annRet - rf) / annVol;

    $('shMean').textContent = mean.toFixed(3) + '%';
    $('shVolM').textContent = vol.toFixed(3) + '%';
    $('shAnnRet').textContent = (annRet * 100).toFixed(2) + '%';
    $('shAnnVol').textContent = (annVol * 100).toFixed(2) + '%';
    $('shVal').textContent = sharpe === null ? '∞（波动率为 0）' : sharpe.toFixed(3);

    var verdict;
    if(sharpe === null) verdict = '没有波动，无风险也无意义。';
    else if(sharpe >= 2) verdict = '夏普 ≥ 2：风险调整后收益非常优秀，策略质量很高。';
    else if(sharpe >= 1) verdict = '夏普 ≥ 1：相当不错，每承担 1 份风险换来超过 1 份超额收益。';
    else if(sharpe >= 0.5) verdict = '夏普 0.5~1：一般水平，收益有但性价比不高。';
    else if(sharpe > 0) verdict = '夏普 < 0.5：收益跑不赢承担的风险，策略需要优化。';
    else verdict = '夏普为负：还不如直接买无风险理财，策略需要重做。';
    $('shVerdict').textContent = verdict;
    $('shResult').style.display = 'block';
  });
})();
</script>

<div class="explain">
  <h3>公式 & 这个指标是什么意思</h3>
  <p class="ex-formula">Sharpe = (R_p − R_f) / σ<br>（R_p 为组合年化收益，R_f 为无风险利率，σ 为年化波动率）</p>
  <p class="ex-text">夏普比率回答的是：<b>每承担一份波动，换回多少超额收益</b>。它是「性价比」指标——两个都年赚 20% 的组合，波动小的那个夏普更高、质量更好。经验参考值：低于 0.5 偏弱，1 以上良好，2 以上优秀。它的缺点是把「上涨的波动」也当风险惩罚，而没有人嫌赚钱太颠簸——这正是索提诺比率改进的方向。</p>
</div>
