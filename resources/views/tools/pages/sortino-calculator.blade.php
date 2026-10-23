<div class="field">
  <span class="fl">每月收益率（%），逗号或换行分隔</span>
  <textarea id="soReturns" rows="4" placeholder="2.1, -0.8, 3.4, 1.2">2.1, -0.8, 3.4, 1.2, 0.5, -1.6, 2.8, 1.9</textarea>
</div>
<div class="field">
  <span class="fl">无风险年利率（%，默认 2）</span>
  <input type="text" id="soRiskFree" value="2">
</div>
<div class="field">
  <span class="fl">目标收益率 / 最低可接受收益 MAR（%月，默认 0）</span>
  <input type="text" id="soMar" value="0">
</div>
<div class="btn-row">
  <button class="btn" id="soCalc">计算索提诺比率</button>
</div>
<div id="soResult" style="display:none">
  <table class="si-table">
    <tbody>
      <tr><td>月均收益</td><td id="soMean"></td></tr>
      <tr><td>下行标准差（月）</td><td id="soDd"></td></tr>
      <tr><td>年化收益率</td><td id="soAnnRet"></td></tr>
      <tr><td>年化下行波动</td><td id="soAnnDd"></td></tr>
    </tbody>
    <tfoot><tr><td>索提诺比率 Sortino Ratio</td><td id="soVal"></td></tr></tfoot>
  </table>
  <div class="si-summary" id="soVerdict"></div>
</div>

<style>
#soResult .si-table td:last-child{color:var(--accent);font-weight:700}
#soResult tfoot td{font-size:1.05rem}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  $('soCalc').addEventListener('click', function(){
    var raw = $('soReturns').value.trim();
    var rets = raw.split(/[,，\s]+/).map(Number).filter(function(v){ return !isNaN(v); });
    if(rets.length < 3){ $('soVerdict').textContent = '至少需要 3 个月收益率数据。'; $('soResult').style.display = 'block'; return; }

    var n = rets.length;
    var mar = (parseFloat($('soMar').value) || 0);
    var mean = rets.reduce(function(a,b){ return a+b; }, 0) / n;

    // 下行标准差：只统计低于 MAR 的部分
    var downSum = 0;
    rets.forEach(function(r){ var d = Math.min(0, r - mar); downSum += d * d; });
    var dd = Math.sqrt(downSum / n);

    var rf = (parseFloat($('soRiskFree').value) || 0) / 100;
    var annRet = mean / 100 * 12;
    var annDd = dd / 100 * Math.sqrt(12);
    var sortino = annDd === 0 ? null : (annRet - rf) / annDd;

    $('soMean').textContent = mean.toFixed(3) + '%';
    $('soDd').textContent = dd.toFixed(3) + '%';
    $('soAnnRet').textContent = (annRet * 100).toFixed(2) + '%';
    $('soAnnDd').textContent = (annDd * 100).toFixed(2) + '%';
    $('soVal').textContent = sortino === null ? '∞（没有下行波动）' : sortino.toFixed(3);

    var verdict;
    if(sortino === null) verdict = '从未跌破目标收益，下行风险为零。';
    else if(sortino >= 3) verdict = '索提诺 ≥ 3：下行风险控制得非常好，亏钱月份又小又少。';
    else if(sortino >= 1.5) verdict = '索提诺 ≥ 1.5：良好，回撤可控，收益质量不错。';
    else if(sortino >= 0.5) verdict = '索提诺 0.5~1.5：一般，下跌月份拖了后腿。';
    else verdict = '索提诺 < 0.5：亏损月份的杀伤力太大，重点检查回撤控制。';
    $('soVerdict').textContent = verdict;
    $('soResult').style.display = 'block';
  });
})();
</script>

<div class="explain">
  <h3>公式 & 这个指标是什么意思</h3>
  <p class="ex-formula">Sortino = (R_p − R_f) / 下行标准差<br>下行标准差 = √( Σ min(0, r − MAR)² / n )<br>（只统计低于目标收益 MAR 的月份）</p>
  <p class="ex-text">索提诺是夏普比率的改良版。夏普把<b>上涨和下跌都算作风险</b>，但没有人嫌赚钱太颠簸——真正让人睡不着觉的是亏损。索提诺只惩罚「跌下去」的波动，更贴近真实的风险感受。同一个策略，索提诺通常比夏普高；两者差距越大，说明收益主要靠「向上的波动」驱动，质量越高。经验参考：1.5 以上良好，3 以上优秀。</p>
</div>
