<div class="field">
  <span class="fl">总交易笔数</span>
  <input type="text" id="wrTotal" value="50">
</div>
<div class="field">
  <span class="fl">其中盈利笔数</span>
  <input type="text" id="wrWins" value="28">
</div>
<div class="btn-row">
  <button class="btn" id="wrCalc">计算胜率</button>
</div>
<div id="wrResult" style="display:none">
  <table class="si-table">
    <tbody>
      <tr><td>盈利 / 亏损笔数</td><td id="wrSplit"></td></tr>
      <tr><td>连错 3 次的概率（参考）</td><td id="wrStreak3"></td></tr>
      <tr><td>连错 5 次的概率（参考）</td><td id="wrStreak5"></td></tr>
    </tbody>
    <tfoot><tr><td>胜率 Win Rate</td><td id="wrVal"></td></tr></tfoot>
  </table>
  <div class="si-summary" id="wrVerdict"></div>
</div>

<style>
#wrResult .si-table td:last-child{color:var(--accent);font-weight:700}
#wrResult tfoot td{font-size:1.05rem}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  $('wrCalc').addEventListener('click', function(){
    var total = parseInt($('wrTotal').value) || 0;
    var wins = parseInt($('wrWins').value) || 0;
    if(total <= 0 || wins > total){ $('wrVerdict').textContent = '请检查输入：总笔数需大于 0，盈利笔数不能超过总笔数。'; $('wrResult').style.display = 'block'; return; }

    var p = wins / total;
    var losses = total - wins;
    var s3 = Math.pow(1 - p, 3) * 100;
    var s5 = Math.pow(1 - p, 5) * 100;

    $('wrSplit').textContent = wins + ' 笔 / ' + losses + ' 笔';
    $('wrStreak3').textContent = s3.toFixed(1) + '%';
    $('wrStreak5').textContent = s5.toFixed(1) + '%';
    $('wrVal').textContent = (p * 100).toFixed(1) + '%';

    var verdict;
    if(p >= 0.8) verdict = '胜率 ≥ 80%：很高，但务必检查盈亏比——是不是亏一次就亏掉很多次利润。';
    else if(p >= 0.6) verdict = '胜率 60%~80%：优秀水平，配合 1:1 左右的盈亏比就有正期望。';
    else if(p >= 0.45) verdict = '胜率 45%~60%：正常水平，需要盈亏比大于 1 才能稳定盈利。';
    else verdict = '胜率 < 45%：偏低，必须依靠高盈亏比（1:2 以上）才能长期为正。';
    $('wrVerdict').textContent = verdict;
    $('wrResult').style.display = 'block';
  });
})();
</script>

<div class="explain">
  <h3>公式 & 这个指标是什么意思</h3>
  <p class="ex-formula">胜率 = 盈利笔数 / 总交易笔数 × 100%<br>连错 k 次的概率 = (1 − 胜率)ᵏ</p>
  <p class="ex-text">胜率是最直观、也最容易被高估的指标：<b>胜率 90% 不代表赚钱</b>，还得看每次亏多少；而胜率 35% 的趋势策略也可能稳定盈利，因为每次都赢很大。表中附带的「连错概率」是它更实用的用法——哪怕胜率 50%，连续错 5 次的概率也有约 3%，配上过重的仓位照样爆仓。单看胜率没有意义，必须和盈亏比一起读：<b>胜率 × 盈亏比 &gt; 1，长期期望才是正的</b>。</p>
</div>
