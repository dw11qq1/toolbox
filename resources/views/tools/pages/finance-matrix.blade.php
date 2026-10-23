<div class="field">
  <span class="fl">月度收益率（%，每行一个或逗号/空格分隔，盈为正亏为负）</span>
  <textarea id="fmReturns" rows="6" placeholder="2.1&#10;-0.8&#10;3.4&#10;1.2&#10;-2.0&#10;..." style="width:100%;font-family:var(--font-mono);font-size:0.85rem;padding:0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px"></textarea>
</div>

<div class="field">
  <span class="fl">参数</span>
  <div style="display:flex;gap:1.2rem;flex-wrap:wrap">
    <label style="font-size:0.85rem">年化无风险利率（%）<input id="fmRf" type="number" value="2" step="0.1" style="width:90px;margin-left:0.4rem;padding:0.3rem 0.5rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px;font-family:var(--font-mono)"></label>
    <label style="font-size:0.85rem">最低可接受收益 MAR（%）<input id="fmMar" type="number" value="0" step="0.1" style="width:90px;margin-left:0.4rem;padding:0.3rem 0.5rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px;font-family:var(--font-mono)"></label>
    <button class="btn" id="fmRun" style="padding:0.35rem 1.2rem">计算全部指标</button>
  </div>
</div>

<div class="field" id="fmOut" style="display:none">
  <span class="fl">指标矩阵（一次输入，全指标输出）</span>
  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;font-size:0.88rem" id="fmTable"></table>
  </div>
  <p id="fmVerdict" style="margin-top:0.8rem;font-size:0.88rem;line-height:1.7"></p>
</div>

<style>
#fmTable th{ text-align:left; padding:0.45rem 0.7rem; border-bottom:2px solid var(--line); font-size:0.8rem; color:var(--ink-3) }
#fmTable td{ padding:0.45rem 0.7rem; border-bottom:1px solid var(--line) }
#fmTable td.num{ font-family:var(--font-mono); white-space:nowrap }
#fmTable .pos{ color:#c0392b; font-weight:600 }
#fmTable .neg{ color:#1e7f4f; font-weight:600 }
</style>

<div class="explain">
  <h3>公式 & 这个矩阵是什么意思</h3>
  <p class="ex-formula">年化收益 = (Π(1+rᵢ))^(12/n) − 1<br>夏普 = (年化 − 无风险) ÷ 年化波动<br>索提诺 = (年化 − 无风险) ÷ 下行波动<br>卡玛 = 年化收益 ÷ 最大回撤<br>期望值(月) = 胜率 × 均盈 − 败率 × |均亏|</p>
  <p class="ex-text">单看一个指标容易自欺：<b>收益高可能靠运气，波动小可能靠不交易</b>。矩阵的价值在于横向对照——比如夏普 1.5 看着不错，但索提诺也是 1.5 说明下跌少；卡玛 0.3 却暴露了「赚 30% 要忍受回撤近 100%」的难拿。经验参考：夏普 &lt;0.5 偏弱、&gt;1 良好、&gt;2 优秀；卡玛 &gt;1 才算拿得住；月胜率 60% + 盈亏比 1.2 的组合往往比胜率 90% + 盈亏比 0.2 活得更久。</p>
</div>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };
  function fmt(v, d){ return Number(v).toFixed(d === undefined ? 2 : d); }
  function cls(v){ return v > 0 ? 'pos' : (v < 0 ? 'neg' : ''); }

  $('fmRun').addEventListener('click', function(){
    var raw = $('fmReturns').value.replace(/%/g, ' ');
    var arr = raw.split(/[\s,;]+/).filter(function(s){ return s !== ''; }).map(Number);
    var bad = arr.some(function(v){ return isNaN(v); });
    if(!arr.length || bad){
      $('fmOut').style.display = 'block';
      $('fmTable').innerHTML = '<tr><td style="color:#c0392b">请输入有效的收益率数字（例如 2.1、-0.8），每行一个或逗号分隔。</td></tr>';
      $('fmVerdict').textContent = '';
      return;
    }
    var rf = Number($('fmRf').value) || 0, mar = Number($('fmMar').value) || 0;
    var n = arr.length;
    var mean = arr.reduce(function(a,b){ return a+b; }, 0) / n;
    var win = arr.filter(function(v){ return v > 0; });
    var loss = arr.filter(function(v){ return v < 0; });
    var winRate = win.length / n * 100;
    var avgWin = win.length ? win.reduce(function(a,b){ return a+b; }, 0) / win.length : 0;
    var avgLoss = loss.length ? loss.reduce(function(a,b){ return a+b; }, 0) / loss.length : 0;
    var ev = (win.length/n) * avgWin - (loss.length/n) * Math.abs(avgLoss);

    var growth = 1;
    arr.forEach(function(r){ growth *= (1 + r/100); });
    var annRet = (Math.pow(growth, 12/n) - 1) * 100;

    var sq = arr.reduce(function(a,r){ return a + Math.pow(r - mean, 2); }, 0);
    var vol = Math.sqrt(sq / (n - 1 || 1)) * Math.sqrt(12);

    var ds = arr.filter(function(r){ return r < mar/12; });
    var dsVar = ds.length ? ds.reduce(function(a,r){ return a + Math.pow(r - mar/12, 2); }, 0) / (ds.length ? ds.length : 1) : 0;
    var downVol = Math.sqrt(dsVar) * Math.sqrt(12);

    var nav = 1, peak = 1, maxDD = 0;
    arr.forEach(function(r){ nav *= (1 + r/100); if(nav > peak) peak = nav; var dd = (peak - nav)/peak; if(dd > maxDD) maxDD = dd; });
    maxDD *= 100;

    var sharpe = vol > 0 ? (annRet - rf) / vol : NaN;
    var sortino = downVol > 0 ? (annRet - rf) / downVol : NaN;
    var calmar = maxDD > 0 ? annRet / maxDD : NaN;
    var plr = avgLoss !== 0 ? avgWin / Math.abs(avgLoss) : NaN;
    var var95 = (function(){ var s = arr.slice().sort(function(a,b){ return a-b; }); var i = Math.max(0, Math.floor(n * 0.05) - 1); return s[i]; })();

    function rate(v){ return isNaN(v) ? '—' : fmt(v); }
    function rateC(v){ return isNaN(v) ? '—' : '<span class="'+cls(v)+'">'+fmt(v)+'%</span>'; }

    var rows = [
      ['月份数', fmt(n, 0), '样本越多结论越稳，建议 ≥ 24 个月'],
      ['月胜率', rateC(winRate), winRate >= 55 ? '过半月份在赚钱' : '赚的月份不到一半，靠盈亏比撑'],
      ['月均收益', rateC(mean) + '%', '简单算术平均'],
      ['年化收益', rateC(annRet) + '%', '几何复利口径，比 12×月均更真实'],
      ['年化波动率 σ', fmt(vol) + '%', '波动是夏普的分母'],
      ['下行波动率', fmt(downVol) + '%', '只统计低于 MAR 的月份，索提诺的分母'],
      ['最大回撤', '<span class="neg">' + fmt(maxDD) + '%</span>', '净值从峰值最深跌了多少'],
      ['夏普比率', rate(sharpe), (sharpe >= 1 ? '≥ 1，' : sharpe >= 0.5 ? '0.5~1 之间，' : '< 0.5，') + '每份波动换到的超额收益'],
      ['索提诺比率', rate(sortino), sortino > sharpe ? '大于夏普，说明亏损月少且浅' : '不高于夏普，下跌端贡献了主要波动'],
      ['卡玛比率', rate(calmar), isNaN(calmar) ? '无回撤无法计算' : (calmar >= 1 ? '≥ 1，回撤拿得住' : '< 1，为赚钱忍受的浮亏偏深')],
      ['盈亏比（月）', rate(plr), isNaN(plr) ? '没有亏损月可作分母' : (plr >= 1.5 ? '赚大亏小，健康' : plr >= 1 ? '勉强打平偏正' : '赚小亏大，危险信号')],
      ['月度期望值', rateC(ev) + '%', ev > 0 ? '为正：这个序列的每月在数学上是赚钱的' : '为负：长期做下去大概率亏'],
      ['95% 月度 VaR', rateC(var95) + '%', '最差的 5% 月份里，最浅的那个亏损'],
    ];
    var html = '<tr><th>指标</th><th>数值</th><th>判读 / 备注</th></tr>';
    rows.forEach(function(r){
      html += '<tr><td>' + r[0] + '</td><td class="num">' + r[1] + '</td><td style="font-size:0.8rem;color:var(--ink-3)">' + r[2] + '</td></tr>';
    });
    $('fmTable').innerHTML = html;
    $('fmOut').style.display = 'block';

    var good = (sharpe >= 1 ? 1 : 0) + (calmar >= 1 ? 1 : 0) + (ev > 0 ? 1 : 0) + (plr >= 1 ? 1 : 0);
    var verdicts = [
      '四项核心（夏普≥1 / 卡玛≥1 / 期望为正 / 盈亏比≥1）命中 <b>0/4</b>：这个序列整体偏弱，先解决「不亏钱」再谈收益。',
      '四项核心命中 <b>1/4</b>：有亮点但短板明显，对照表格找出拖后腿的那一项重点改。',
      '四项核心命中 <b>2/4</b>：中规中矩，波动控制或止盈止损还有优化空间。',
      '四项核心命中 <b>3/4</b>：结构健康，注意样本量是否足够下结论。',
      '四项核心命中 <b>4/4</b>：收益、回撤、期望、盈亏比全绿——记得看样本月份够不够 24，别被短期行情骗了。'
    ];
    $('fmVerdict').innerHTML = '📋 综合判读：' + verdicts[good] + ' 样本 ' + n + ' 个月。';
  });
})();
</script>
