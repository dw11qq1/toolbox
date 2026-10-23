<div class="field">
  <span class="fl">贷款金额（元）</span>
  <input type="text" id="lnAmount" value="500000">
</div>
<div class="field">
  <span class="fl">贷款期限（年）</span>
  <input type="text" id="lnYears" value="30">
</div>
<div class="field">
  <span class="fl">年利率（%）</span>
  <input type="text" id="lnRate" value="3.6">
</div>
<div class="btn-row">
  <button class="btn" id="lnCalc">计算</button>
</div>
<div id="lnResult" style="display:none">
  <table class="si-table">
    <thead><tr><th>方式</th><th>首月月供</th><th>末月月供</th><th>总利息</th><th>还款总额</th></tr></thead>
    <tbody id="lnBody"></tbody>
  </table>
  <p style="font-size:0.8rem;color:var(--ink-3);margin-top:0.6rem">等额本息：每月还款固定，前期利息占比高。等额本金：月供逐月递减，总利息更少，前期压力大。</p>
</div>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };
  function yuan(v){ return v.toLocaleString('zh-CN', {minimumFractionDigits:2, maximumFractionDigits:2}); }

  $('lnCalc').addEventListener('click', function(){
    var P = parseFloat($('lnAmount').value) || 0;
    var years = parseInt($('lnYears').value) || 0;
    var r = (parseFloat($('lnRate').value) || 0) / 100 / 12;
    var n = years * 12;
    if(P <= 0 || n <= 0){ return; }

    // 等额本息
    var m = r === 0 ? P / n : P * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
    var totalM = m * n;

    // 等额本金
    var first = P / n + P * r;
    var last = P / n + (P / n) * r;
    var totalB = P + P * r * (n + 1) / 2;

    $('lnBody').innerHTML =
      '<tr><td>等额本息</td><td>' + yuan(m) + '</td><td>' + yuan(m) + '</td><td>' + yuan(totalM - P) + '</td><td>' + yuan(totalM) + '</td></tr>' +
      '<tr><td>等额本金</td><td>' + yuan(first) + '</td><td>' + yuan(last) + '</td><td>' + yuan(totalB - P) + '</td><td>' + yuan(totalB) + '</td></tr>';
    var diff = totalM - totalB;
    if(diff > 0){
      $('lnBody').innerHTML += '<tr><td colspan="5" style="text-align:left;color:var(--accent)">等额本金比等额本息省利息 ' + yuan(diff) + '（但前期每月多还约 ' + yuan(first - m) + '）</td></tr>';
    }
    $('lnResult').style.display = 'block';
  });
})();
</script>

<div class="explain">
  <h3>公式 & 这个指标是什么意思</h3>
  <p class="ex-formula">等额本息月供 M = P·r·(1+r)ⁿ / ((1+r)ⁿ − 1)<br>等额本金总利息 = P·r·(n+1) / 2<br>（P 为本金，r 为月利率，n 为总月数）</p>
  <p class="ex-text">两种方式没有绝对好坏，只有适不适合：<b>等额本息</b>每月还一样的钱，好规划，适合收入稳定、前期现金流紧张的人；<b>等额本金</b>总利息更少，但前期月供明显更高，适合当前收入宽裕、想省利息的人。另外记住一个防坑常识：推销话术里的「月息 X 厘」「日息万几」，先换算成年利率再判断贵不贵。</p>
</div>
