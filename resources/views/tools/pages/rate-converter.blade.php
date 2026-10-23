<div class="field">
  <span class="fl">输入一个利率（%）</span>
  <input type="text" id="rtInput" value="10">
</div>
<div class="btn-row">
  <label>这个利率是：<select id="rtKind" style="font-size:0.9rem;padding:0.35rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px">
    <option value="year">年利率</option>
    <option value="month">月利率</option>
    <option value="day">日利率（万分之一计）</option>
  </select></label>
</div>
<div class="btn-row">
  <button class="btn" id="rtCalc">换算</button>
</div>
<div class="field">
  <span class="fl">换算结果（单利口径）</span>
  <output id="rtSimple"></output>
  <span class="fl" style="margin-top:1rem">换算结果（复利口径）</span>
  <output id="rtCompound"></output>
  <span class="fl" style="margin-top:1rem">万五日息参考（民间借贷常用表述）</span>
  <output id="rtWan5"></output>
</div>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };
  function pct(v){ return (v * 100).toFixed(4).replace(/\.?0+$/, '') + '%'; }

  $('rtCalc').addEventListener('click', function(){
    var v = (parseFloat($('rtInput').value) || 0) / 100;
    var kind = $('rtKind').value;
    if(v <= 0){ return; }
    // 统一折算成年利率
    var yearRate = kind === 'year' ? v : kind === 'month' ? v * 12 : v * 365;
    var monthRate = yearRate / 12;
    var dayRate = yearRate / 365;

    $('rtSimple').textContent =
      '年利率 ' + pct(yearRate) + '　·　月利率 ' + pct(monthRate) + '　·　日利率 ' + pct(dayRate);
    var cy = Math.pow(1 + dayRate, 365) - 1;
    var cm = Math.pow(1 + monthRate, 12) - 1;
    $('rtCompound').textContent =
      '按日复利滚一年 = ' + pct(cy) + '　·　按月复利滚一年 = ' + pct(cm);
    $('rtWan5').textContent = '日利率万五 = 年化 ' + pct(5 / 10000 * 365) + '（单利）；你的利率相当于日息万' + (dayRate * 10000).toFixed(1);
  });
})();
</script>

<div class="explain">
  <h3>公式 & 这个指标是什么意思</h3>
  <p class="ex-formula">年利率 = 月利率 × 12 = 日利率 × 365<br>复利实际年化 APR = (1 + 期利率)^期数 − 1</p>
  <p class="ex-text">为什么必须换算？因为小数字会骗人：网贷广告说「<b>日息万五</b>」，一天借 1 万只还 5 元，听着很少——换算成年化是 18.25%，远超大部分理财收益。花呗、信用卡分期的「月费率」也要警惕：名义月费 0.6% 不等于年化 7.2%，因为本金在逐月归还而利息按全额计，实际年化通常在 13%~15%。借之前先算年化，是避开高息坑的第一步。</p>
</div>
