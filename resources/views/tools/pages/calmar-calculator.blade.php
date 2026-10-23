<div class="field">
  <span class="fl">每月净值 / 收益率（%），逗号或换行分隔，按时间顺序</span>
  <textarea id="cmReturns" rows="4" placeholder="1.5, -2.0, 3.2, ...">1.5, -2.0, 3.2, 0.8, -1.2, 4.1, -3.5, 2.6, 1.1, -0.9, 2.3, 3.8</textarea>
</div>
<div class="btn-row">
  <button class="btn" id="cmCalc">计算卡玛比率</button>
</div>
<div id="cmResult" style="display:none">
  <table class="si-table">
    <tbody>
      <tr><td>累计收益</td><td id="cmCum"></td></tr>
      <tr><td>年化收益率</td><td id="cmAnnRet"></td></tr>
      <tr><td>最大回撤</td><td id="cmMdd"></td></tr>
    </tbody>
    <tfoot><tr><td>卡玛比率 Calmar Ratio</td><td id="cmVal"></td></tr></tfoot>
  </table>
  <div class="si-summary" id="cmVerdict"></div>
</div>

<style>
#cmResult .si-table td:last-child{color:var(--accent);font-weight:700}
#cmResult tfoot td{font-size:1.05rem}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  $('cmCalc').addEventListener('click', function(){
    var raw = $('cmReturns').value.trim();
    var rets = raw.split(/[,，\s]+/).map(Number).filter(function(v){ return !isNaN(v); });
    if(rets.length < 3){ $('cmVerdict').textContent = '至少需要 3 个月收益率数据。'; $('cmResult').style.display = 'block'; return; }

    var n = rets.length;
    // 净值曲线与累计收益（几何连乘）
    var equity = 1, peak = 1, mdd = 0;
    rets.forEach(function(r){
      equity *= (1 + r / 100);
      if(equity > peak) peak = equity;
      var dd = (peak - equity) / peak;
      if(dd > mdd) mdd = dd;
    });
    var cum = equity - 1;
    var years = n / 12;
    var annRet = Math.pow(equity, 1 / years) - 1;
    var calmar = mdd === 0 ? null : annRet / mdd;

    $('cmCum').textContent = (cum * 100).toFixed(2) + '%';
    $('cmAnnRet').textContent = (annRet * 100).toFixed(2) + '%';
    $('cmMdd').textContent = (mdd * 100).toFixed(2) + '%';
    $('cmVal').textContent = calmar === null ? '∞（没有回撤）' : calmar.toFixed(3);

    var verdict;
    if(calmar === null) verdict = '一路新高没有回撤，样本可能太短。';
    else if(calmar >= 3) verdict = '卡玛 ≥ 3：赚得多、回撤小，曲线走得非常舒服。';
    else if(calmar >= 1) verdict = '卡玛 ≥ 1：良好，年化收益能覆盖最大回撤。';
    else if(calmar >= 0.5) verdict = '卡玛 0.5~1：一般，回撤相对收益偏大，拿着比较煎熬。';
    else verdict = '卡玛 < 0.5：为了赚这点收益要忍受的回撤太大了，性价比不足。';
    $('cmVerdict').textContent = verdict;
    $('cmResult').style.display = 'block';
  });
})();
</script>

<div class="explain">
  <h3>公式 & 这个指标是什么意思</h3>
  <p class="ex-formula">Calmar = 年化收益率 / 最大回撤</p>
  <p class="ex-text">卡玛比率回答：<b>为了赚到每年的收益，最多要忍受多深的浮亏</b>。年化 30% 但最大回撤 50%（卡玛 0.6）的曲线，大部分人半路就割肉了，最终根本拿不到那个年化；而年化 15%、回撤 10%（卡玛 1.5）的曲线反而能拿得住、赚得到。所以卡玛比的不只是数字，更是<b>「能不能拿得住」</b>——回撤是拿不住的根源，卡玛高的策略才真正可执行。经验参考：1 以上良好，3 以上优秀。</p>
</div>
