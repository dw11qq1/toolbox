<div class="field">
  <span class="fl">初始本金（元）</span>
  <input type="text" id="cpPrincipal" value="100000">
</div>
<div class="field">
  <span class="fl">每月定投（元，可填 0）</span>
  <input type="text" id="cpMonthly" value="2000">
</div>
<div class="field">
  <span class="fl">年化收益率（%）</span>
  <input type="text" id="cpRate" value="6">
</div>
<div class="field">
  <span class="fl">投资年限（年）</span>
  <input type="text" id="cpYears" value="20">
</div>
<div class="btn-row">
  <button class="btn" id="cpCalc">计算</button>
</div>

<div id="cpResult" style="display:none">
  <div class="si-summary" id="cpSummary"></div>
  <canvas id="cpChart" height="260" style="width:100%;border:1px solid var(--line);border-radius:10px;background:var(--panel);margin-top:1rem"></canvas>
  <p class="fl" style="margin-top:0.5rem">蓝线 = 总资产 · 灰线 = 累计投入本金 · 两线之间就是复利赚到的</p>
</div>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };
  function w(v){ return v.toLocaleString('zh-CN', {maximumFractionDigits:0}); }

  $('cpCalc').addEventListener('click', function(){
    var P = parseFloat($('cpPrincipal').value) || 0;
    var M = parseFloat($('cpMonthly').value) || 0;
    var rate = (parseFloat($('cpRate').value) || 0) / 100;
    var years = parseInt($('cpYears').value) || 0;
    if(years <= 0){ return; }

    // 逐年推演
    var data = [{total:P, invested:P}];
    var total = P, invested = P;
    for(var y = 1; y <= years; y++){
      for(var m = 0; m < 12; m++){
        total = total * (1 + rate / 12) + M;
        invested += M;
      }
      data.push({total:total, invested:invested});
    }

    var profit = total - invested;
    $('cpSummary').innerHTML =
      '投资年限结束：<b>¥' + w(total) + '</b>　·　累计投入 <b>¥' + w(invested) + '</b>　·　复利收益 <b>¥' + w(profit) + '</b>（' + (invested ? (profit / invested * 100).toFixed(0) : 0) + '%）';

    drawChart(data);
    $('cpResult').style.display = 'block';
  });

  function drawChart(data){
    var canvas = $('cpChart');
    var dpr = window.devicePixelRatio || 1;
    var W = canvas.clientWidth || 600;
    var H = 260;
    canvas.width = W * dpr; canvas.height = H * dpr;
    var ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);
    ctx.clearRect(0, 0, W, H);

    var pad = {l: 70, r: 16, t: 16, b: 28};
    var maxV = Math.max.apply(null, data.map(function(d){ return d.total; }));
    var iw = W - pad.l - pad.r, ih = H - pad.t - pad.b;
    var n = data.length - 1;
    if(n < 1) return;

    function x(i){ return pad.l + i / n * iw; }
    function y(v){ return pad.t + ih - v / maxV * ih; }

    // 网格 + 纵轴标签
    ctx.strokeStyle = 'rgba(128,135,150,0.2)';
    ctx.fillStyle = '#88879f';
    ctx.font = '10px "JetBrains Mono", monospace';
    ctx.textAlign = 'right';
    for(var g = 0; g <= 4; g++){
      var v = maxV / 4 * g;
      var gy = y(v);
      ctx.beginPath();
      ctx.moveTo(pad.l, gy); ctx.lineTo(W - pad.r, gy);
      ctx.stroke();
      ctx.fillText(v >= 10000 ? (v / 10000).toFixed(0) + '万' : v.toFixed(0), pad.l - 6, gy + 3);
    }

    // 横轴（每 1/5 处标年份）
    ctx.textAlign = 'center';
    for(var t = 0; t <= 5; t++){
      var i = Math.round(n * t / 5);
      ctx.fillText('第' + i + '年', x(i), H - 8);
    }

    // 投入本金线（灰）
    ctx.strokeStyle = 'rgba(128,135,150,0.7)';
    ctx.lineWidth = 1.5;
    ctx.beginPath();
    data.forEach(function(d, i){
      i ? ctx.lineTo(x(i), y(d.invested)) : ctx.moveTo(x(i), y(d.invested));
    });
    ctx.stroke();

    // 总资产线（accent 蓝）
    ctx.strokeStyle = '#2563eb';
    ctx.lineWidth = 2.5;
    ctx.beginPath();
    data.forEach(function(d, i){
      i ? ctx.lineTo(x(i), y(d.total)) : ctx.moveTo(x(i), y(d.total));
    });
    ctx.stroke();

    // 图例
    ctx.fillStyle = '#2563eb';
    ctx.fillRect(pad.l + 8, pad.t + 2, 14, 3);
    ctx.fillStyle = '#3f4450';
    ctx.textAlign = 'left';
    ctx.fillText('总资产', pad.l + 26, pad.t + 7);
    ctx.fillStyle = 'rgba(128,135,150,0.7)';
    ctx.fillRect(pad.l + 84, pad.t + 2, 14, 3);
    ctx.fillStyle = '#3f4450';
    ctx.fillText('累计投入', pad.l + 102, pad.t + 7);
  }
})();
</script>
