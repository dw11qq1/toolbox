<div id="soupApp">
  <div class="score-row">
    <span class="score-box">第 <b id="spNo">1</b> / <span id="spTotal">8</span> 汤</span>
    <span class="score-box">已用提示 <b id="spHints">0</b></span>
    <button class="btn" id="spHint">💡 解锁线索</button>
  </div>

  <div class="soup-bowl">
    <p class="fl">汤面 · 谜题</p>
    <p class="soup-q" id="spQ"></p>
  </div>

  <div class="soup-clues" id="spClues"></div>

  <button class="btn" id="spReveal" style="margin-top:1rem">揭开汤底 →</button>

  <div class="q-result" id="spResult" style="display:none">
    <p class="fl">汤底 · 真相</p>
    <p class="mbti-desc" id="spA" style="text-align:left"></p>
    <p class="mbti-nick" id="spVerdict"></p>
    <button class="btn" id="spNext">下一碗汤 →</button>
  </div>
</div>

<script>
(function(){
  var ALL = [
    {q:'一个男人从十几层的高楼一跃而下，却没有受伤，为什么？',
     c:['他落在了楼下另一栋楼的屋顶上。','他所在楼层比对面楼低。','注意题目没说他跳下后落地了。'],
     a:'他站的是矮楼顶，跳到了相邻更高楼的屋顶上——所以毫发无伤。经典的「跳出思维定势」汤。'},
    {q:'一个男人在餐厅点了一碗海龟汤，喝了一口就冲出餐厅自杀了，为什么？',
     c:['他多年前遭遇过海难。','同行的人都死了，只有他活下来。','他曾经喝过「海龟汤」。'],
     a:'海难时同伴给他喝的「海龟汤」其实是死去同伴的肉。今天尝到真海龟汤，他发现味道完全不同，才明白当年的真相，崩溃自杀。这就是「海龟汤」名字的由来。'},
    {q:'一个女人买了一双新鞋，当天就死了，为什么？',
     c:['她是马戏团的工作人员。','刀是从她头顶穿下来的。','她的工作需要躺在一个箱子里。'],
     a:'她是飞刀表演的助理，躺箱子里当活靶子。新鞋有高跟，身高变了，搭档的飞刀精确计算的是原来身高——刀正好扎中了她。'},
    {q:'男人推开家门就大喊「妻子快跑」，然后妻子死了，为什么？',
     c:['家里当时没有人回应他。','危险来自天上。','他喊「快跑」是因为看到了危险，而妻子在危险来的方向。'],
     a:'他们住在山谷，男人看到山洪/山体滑坡正冲向房屋后山一侧，大喊让在那一侧的妻子快跑，但已经来不及了。'},
    {q:'双胞胎兄弟，哥哥在 1990 年出生，弟弟在 1989 年出生，为什么？',
     c:['他们确实是同一位母亲所生。','和渡轮有关。','生日月份很关键。'],
     a:'母亲在怀孕时乘船跨越国际日期变更线（比如从西往东），哥哥在变更线西侧的 1 月 1 日（1990）出生，弟弟随后在东侧的 12 月 31 日（1989）出生——先出生的弟弟反而「年份更早」。'},
    {q:'一个男人住在 10 楼。每天他坐电梯到 1 楼上班。回来时只坐到 7 楼，再爬 3 层楼梯回家。下雨天他则直接坐到 10 楼。为什么？',
     c:['他和身高有关。','下雨天他有带东西。','电梯按钮他够不到。'],
     a:'他是侏儒，只能按到 7 楼的按钮。下雨天带了伞，可以用伞尖捅 10 楼的按钮。'},
    {q:'一个女人在葬礼上遇见一个男人，几天后她杀死了自己的姐姐，为什么？',
     c:['她希望再参加一次葬礼。','她爱上了那个男人。','那个男人和葬礼有关。'],
     a:'这是心理学著名的「社会学测试题」：她爱上了那个男人，以为再办一场葬礼他还会出现，于是杀死了也会出席葬礼的姐姐。答对的人被认为有变态杀手潜质——当然只是个都市传说。'},
    {q:'深夜，一个女人听到敲门声，开门却没人；连续几次都如此，第二天她报了警，警察立刻抓走了楼上的邻居，为什么？',
     c:['敲门的人没有手。','敲门声其实来自她家。','楼上邻居的房子和她是上下关系。'],
     a:'楼上邻居瘫痪的室友从轮椅上摔下来，爬到门口用头撞门求救，女人住在楼下听到的是自己家天花板方向的「敲门声」，开门自然没人。报警后警察上楼发现了坠瘫的人，而邻居见死不救。'},
  ];

  /* 每局随机打乱顺序 */
  var PUZZLES = ALL.slice();
  for(var i = PUZZLES.length - 1; i > 0; i--){
    var j = Math.floor(Math.random() * (i + 1));
    var t = PUZZLES[i]; PUZZLES[i] = PUZZLES[j]; PUZZLES[j] = t;
  }

  var idx = 0, hintUsed = 0, revealed = 0;
  var cluesBox = document.getElementById('spClues');

  function render(){
    document.getElementById('spNo').textContent = idx + 1;
    document.getElementById('spTotal').textContent = PUZZLES.length;
    document.getElementById('spHints').textContent = hintUsed;
    document.getElementById('spQ').textContent = PUZZLES[idx].q;
    cluesBox.innerHTML = '';
    document.getElementById('spHint').disabled = false;
    document.getElementById('spReveal').style.display = '';
    document.getElementById('spResult').style.display = 'none';
    revealed = 0;
  }

  document.getElementById('spHint').addEventListener('click', function(){
    if(revealed >= PUZZLES[idx].c.length) return;
    var p = document.createElement('p');
    p.className = 'soup-clue';
    p.textContent = '线索 ' + (++revealed) + '：' + PUZZLES[idx].c[revealed - 1];
    cluesBox.appendChild(p);
    hintUsed++;
    document.getElementById('spHints').textContent = hintUsed;
    if(revealed >= PUZZLES[idx].c.length) this.disabled = true;
  });

  document.getElementById('spReveal').addEventListener('click', function(){
    this.style.display = 'none';
    document.getElementById('spHint').disabled = true;
    // 把剩余线索全部展示
    while(revealed < PUZZLES[idx].c.length){
      var p = document.createElement('p');
      p.className = 'soup-clue';
      p.textContent = '线索 ' + (++revealed) + '：' + PUZZLES[idx].c[revealed - 1];
      cluesBox.appendChild(p);
    }
    document.getElementById('spA').textContent = PUZZLES[idx].a;
    document.getElementById('spVerdict').textContent = hintUsed === 0
      ? '零提示直指真相——你是推理鬼才！'
      : hintUsed <= 2 ? '用的提示不多，推理直觉相当在线。'
      : '汤底出乎意料吧？这正是海龟汤的魅力。';
    document.getElementById('spResult').style.display = 'block';
  });

  document.getElementById('spNext').addEventListener('click', function(){
    idx = (idx + 1) % PUZZLES.length;
    render();
  });

  render();
})();
</script>

<style>
#soupApp .soup-bowl{border:1px solid var(--line);border-left:3px solid var(--accent);background:var(--panel);border-radius:0 10px 10px 0;padding:1.2rem 1.4rem}
#soupApp .soup-q{font-family:var(--font-display);font-weight:700;font-size:1.2rem;line-height:1.5}
#soupApp .soup-clues{margin-top:1rem}
#soupApp .soup-clue{font-size:0.92rem;color:var(--ink-2);border-bottom:1px dashed var(--line);padding:0.6rem 0;margin:0}
#soupApp .q-result{border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:2rem;margin-top:1rem}
#soupApp .mbti-desc{font-size:0.95rem}
#soupApp .mbti-nick{color:var(--accent);font-family:var(--font-display);font-weight:700;margin-top:0.8rem}
</style>
