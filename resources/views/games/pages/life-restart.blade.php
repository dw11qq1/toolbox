<div id="lrApp">
  <div id="lrSetup">
    <p class="fl">第一步 · 天赋三选一（共抽 3 轮）</p>
    <div class="lr-talents" id="lrTalents"></div>
    <p class="fl" style="margin-top:1.5rem">第二步 · 分配 20 点属性（剩余 <b id="lrLeft">20</b>）</p>
    <div class="lr-attrs" id="lrAttrs"></div>
    <button class="btn" id="lrStart" style="margin-top:1.5rem">开始人生 →</button>
  </div>

  <div id="lrLife" style="display:none">
    <div class="lr-timeline" id="lrTimeline"></div>
    <div class="lr-fast" id="lrFast">人生推进中…</div>
    <div class="q-result" id="lrEnd" style="display:none">
      <p class="fl">人生落幕</p>
      <div class="mbti-code" id="lrAge"></div>
      <p class="mbti-nick" id="lrTitle"></p>
      <p class="mbti-desc" id="lrDesc"></p>
      <button class="btn" id="lrRetry">再开一局</button>
    </div>
  </div>
</div>

<style>
#lrApp .lr-talents{display:grid;gap:0.6rem}
#lrApp .lr-talent{width:100%;text-align:left;font-size:0.95rem;padding:0.85rem 1.2rem;border:1.5px solid var(--line);background:var(--panel);color:var(--ink);border-radius:10px;cursor:pointer;transition:border-color 150ms ease}
#lrApp .lr-talent:hover:not(:disabled){border-color:var(--accent)}
#lrApp .lr-talent:disabled{cursor:default;opacity:0.45}
#lrApp .lr-talent.picked{border-color:var(--accent);color:var(--accent);opacity:1}
#lrApp .lr-attr{display:flex;align-items:center;gap:0.75rem;margin-bottom:0.6rem}
#lrApp .lr-attr .n{width:3.5rem;font-size:0.9rem;color:var(--ink-2)}
#lrApp .lr-attr button{width:30px;height:30px;border:1px solid var(--line);background:var(--panel);border-radius:8px;cursor:pointer;font-size:1rem;color:var(--ink)}
#lrApp .lr-attr .v{font-family:var(--font-mono);width:2rem;text-align:center;color:var(--accent);font-weight:700}
#lrApp .lr-timeline{border:1px solid var(--line);border-radius:10px;background:var(--panel);padding:1.2rem 1.4rem;height:300px;overflow-y:auto;font-size:0.9rem}
#lrApp .lr-timeline p{margin-bottom:0.35rem;color:var(--ink-2)}
#lrApp .lr-timeline p b{color:var(--accent);font-family:var(--font-mono);font-size:0.8rem;margin-right:0.5rem}
#lrApp .lr-timeline p.big{color:var(--ink);font-weight:500}
#lrApp .lr-fast{font-family:var(--font-mono);font-size:0.78rem;color:var(--ink-3);margin-top:0.8rem;text-align:center}
</style>

<script>
(function(){
  /* 天赋池：id, 名称, 效果 */
  var TALENTS = [
    {n:'天生丽质', d:'颜值 +3', e:{look:3}},
    {n:'天选之子', d:'运气极好，凶事件概率大减', e:{luck:true}},
    {n:'学霸基因', d:'智力 +3', e:{iq:3}},
    {n:'铁人体质', d:'体质 +3，更长寿', e:{body:3}},
    {n:'家境优渥', d:'家境 +3', e:{rich:3}},
    {n:'乐天派', d:'几乎不会郁郁而终', e:{happy:true}},
    {n:'风险偏好', d:'大起大落：好事坏事都更多', e:{wild:true}},
    {n:'长寿血脉', d:'预期寿命 +15 年', e:{life:15}},
    {n:'贵人运', d:'人生关键节点总有相助', e:{mentor:true}},
    {n:'艺术细胞', d:'有概率成为艺术家', e:{artist:true}},
    {n:'商业头脑', d:'成年后财富事件大幅变好', e:{biz:true}},
    {n:'病弱之躯', d:'体质 -2，但智力 +2', e:{body:-2, iq:2}},
  ];

  var ATTRS = [
    {k:'look', n:'颜值'},
    {k:'iq', n:'智力'},
    {k:'body', n:'体质'},
    {k:'rich', n:'家境'},
  ];

  var T2 = {
    look:['小时候就是团宠','校花校草级别的存在','颜值带来不少人生捷径'],
    iq:['跳级了','考上了顶尖大学','在科研领域崭露头角'],
    body:['体育课永远第一名','百病不侵','八十岁还能爬山'],
    rich:['家境优渥，从不为钱发愁','家里支持你创业','财富自由提前退休'],
  };

  var EVENTS = [
    /* [最小年龄, 最大年龄, 文本函数(props) 返回 string 或 null(不触发), 是否重要事件] */
    [0, 3, function(p){ return p.look >= 7 ? '你出生时护士都夸你漂亮，全家把你当宝贝。' : null; }, true],
    [0, 3, function(p){ return p.rich <= 2 ? '你出生在偏远山村，家境贫寒。' : null; }, true],
    [4, 6, function(p){ return p.iq >= 7 ? '你 4 岁就能背古诗，邻里都说你是神童。' : null; }, true],
    [4, 8, function(){ return '你在幼儿园交到了第一个朋友。'; }, false],
    [7, 12, function(p){ return p.iq >= 6 ? '你参加奥数比赛拿了市级一等奖。' : null; }, true],
    [7, 12, function(p){ return p.look >= 7 ? '你因为长得好看成了小童星。' : null; }, true],
    [7, 12, function(p){ return p.body <= 2 ? '你体弱多病，经常请假住院。' : null; }, false],
    [7, 12, function(){ return '小学毕业，你交到了一群死党。'; }, false],
    [13, 15, function(p){ return p.look >= 6 ? '你收到了人生第一封情书。' : null; }, false],
    [13, 15, function(p){ return p.iq <= 3 ? '中考在即，你完全学不进去。' : null; }, false],
    [13, 18, function(p){ return p.iq >= 8 ? '你被顶尖大学保送录取！' : null; }, true],
    [16, 18, function(p){ return p.iq >= 5 ? '高考发挥不错，你考上了一本。' : null; }, true],
    [16, 18, function(){ return '高考成绩平平，你进了一所普通大学。'; }, true],
    [19, 22, function(p){ return p.rich >= 7 ? '大学期间你创业，家里给了启动资金。' : null; }, true],
    [19, 22, function(){ return '大学四年，你学会了独立生活。'; }, false],
    [19, 22, function(p){ return p.look >= 7 ? '你成了校园风云人物。' : null; }, false],
    [23, 27, function(p){ return p.iq >= 7 ? '你进入大厂，起薪远超同龄人。' : null; }, true],
    [23, 27, function(){ return '你开始了第一份工作，薪资勉强够花。'; }, true],
    [23, 27, function(p){ return p.talents.artist ? '你坚持创作，作品开始被市场注意。' : null; }, true],
    [28, 35, function(p){ return p.talents.biz ? '你的生意做起来了，赚到了第一桶金。' : null; }, true],
    [28, 35, function(){ return '你遇到了想共度一生的人。'; }, true],
    [28, 35, function(p){ return p.look >= 8 && p.iq >= 6 ? '你在事业和爱情上双双丰收。' : null; }, true],
    [30, 40, function(){ return '你买了自己的房子，背上了一些贷款。'; }, false],
    [35, 45, function(p){ return p.body >= 6 ? '你坚持健身，体能堪比年轻人。' : null; }, false],
    [35, 45, function(){ return '你升到了管理层，每天忙得脚不沾地。'; }, false],
    [40, 50, function(){ return '你的孩子开始上学了，你体会到了父母当年的不易。'; }, false],
    [45, 55, function(p){ return p.iq >= 7 ? '你主持的项目拿了行业大奖。' : null; }, true],
    [50, 60, function(){ return '你开始注重养生，保温杯里泡起了枸杞。'; }, false],
    [55, 65, function(){ return '你退休了，领到了第一笔养老金。'; }, true],
    [60, 75, function(p){ return p.body >= 5 ? '你每天晨练遛弯，身体硬朗。' : null; }, false],
    [60, 75, function(){ return '你儿孙绕膝，安享晚年。'; }, false],
    [70, 85, function(){ return '你坐在摇椅上晒太阳，回忆起漫长的一生。'; }, false],
    /* 凶事件（wild/非 luck 时更易出） */
    [20, 70, function(p){ return {bad:true, t:'你生了一场大病，休养了半年。'}; }, false],
    [25, 60, function(p){ return {bad:true, t:'你被最好的朋友骗了一笔钱。'}; }, false],
    [30, 65, function(p){ return {bad:true, t:'行业寒冬，你失业了一年。'}; }, true],
    [35, 75, function(p){ return {bad:true, t:'你遭遇了一场车祸，所幸无大碍。'}; }, false],
  ];

  var state = {};

  function rand(a){ return a[Math.floor(Math.random()*a.length)]; }
  function $id(id){ return document.getElementById(id); }

  /* ---------- 开局 ---------- */
  var pickedTalents = [], attrVals = {look:5, iq:5, body:5, rich:5}, left = 20;

  function rollTalents(){
    var pool = TALENTS.slice();
    var opts = [];
    for(var i = 0; i < 3; i++) opts.push(pool.splice(Math.floor(Math.random()*pool.length), 1)[0]);
    var box = $id('lrTalents');
    box.innerHTML = '';
    opts.forEach(function(t){
      var b = document.createElement('button');
      b.className = 'lr-talent';
      b.innerHTML = '<b>' + t.n + '</b> — ' + t.d;
      b.addEventListener('click', function(){
        if(pickedTalents.length >= 3 || pickedTalents.indexOf(t) >= 0) return;
        pickedTalents.push(t);
        b.classList.add('picked');
        b.disabled = true;
        if(pickedTalents.length === 3){
          Array.prototype.forEach.call(box.children, function(el){ el.disabled = true; });
          applyTalents();
        }
      });
      box.appendChild(b);
    });
  }

  function applyTalents(){
    pickedTalents.forEach(function(t){
      Object.keys(t.e).forEach(function(k){
        if(ATTRS.some(function(a){ return a.k === k; })){
          attrVals[k] = Math.max(0, Math.min(10, attrVals[k] + t.e[k]));
        }
      });
    });
    left = 20;
    ATTRS.forEach(function(a){ left -= attrVals[a.k]; });
    renderAttrs();
  }

  function renderAttrs(){
    $id('lrLeft').textContent = left;
    var box = $id('lrAttrs');
    box.innerHTML = '';
    ATTRS.forEach(function(a){
      var row = document.createElement('div');
      row.className = 'lr-attr';
      row.innerHTML = '<span class="n">' + a.n + '</span>';
      var minus = document.createElement('button'); minus.textContent = '−';
      var plus = document.createElement('button'); plus.textContent = '+';
      var v = document.createElement('span'); v.className = 'v'; v.textContent = attrVals[a.k];
      minus.addEventListener('click', function(){ if(attrVals[a.k] > 0){ attrVals[a.k]--; left++; renderAttrs(); } });
      plus.addEventListener('click', function(){ if(left > 0 && attrVals[a.k] < 10){ attrVals[a.k]++; left--; renderAttrs(); } });
      row.appendChild(minus); row.appendChild(v); row.appendChild(plus);
      box.appendChild(row);
    });
  }

  /* ---------- 人生推进 ---------- */
  function startLife(){
    state = {age:0, alive:true, timeline:[], talents:pickedTalents, props:{}};
    ATTRS.forEach(function(a){ state.props[a.k] = attrVals[a.k]; });
    state.props.talents = pickedTalents.map(function(t){ return t.n; });
    state.props.luck = pickedTalents.some(function(t){ return t.e.luck; });
    state.props.wild = pickedTalents.some(function(t){ return t.e.wild; });
    state.props.happy = pickedTalents.some(function(t){ return t.e.happy; });
    state.props.mentor = pickedTalents.some(function(t){ return t.e.mentor; });
    state.maxAge = 65 + state.props.body * 4 + (pickedTalents.some(function(t){ return t.e.life; }) ? 15 : 0) + Math.floor(Math.random()*15);

    $id('lrSetup').style.display = 'none';
    $id('lrLife').style.display = 'block';
    $id('lrTimeline').innerHTML = '';
    addLine(0, '你出生了。' + (state.props.rich >= 7 ? '家境优渥。' : state.props.rich <= 2 ? '家境普通偏下。' : '家境普通。'), true);
    tick();
  }

  function addLine(age, text, big){
    var tl = $id('lrTimeline');
    var p = document.createElement('p');
    if(big) p.classList.add('big');
    p.innerHTML = '<b>' + age + '岁</b>' + text;
    tl.appendChild(p);
    tl.scrollTop = tl.scrollHeight;
  }

  function tick(){
    if(!state.alive) return;
    state.age++;
    var age = state.age;
    var lines = 0;

    // 事件触发
    EVENTS.forEach(function(ev){
      if(age >= ev[0] && age <= ev[1]){
        var r = ev[2](state.props);
        if(r === null || r === undefined) return;
        var isBadObj = typeof r === 'object';
        var text = isBadObj ? r.t : r;
        // 凶事件概率控制
        if(isBadObj){
          var badChance = state.props.wild ? 0.35 : state.props.luck ? 0.1 : 0.22;
          if(Math.random() > badChance) return;
        }
        addLine(age, text, ev[3]);
        lines++;
      }
    });

    // 自然死亡
    if(age >= state.maxAge){
      die('寿终正寝');
      return;
    }
    // 非自然风险随年龄增长
    var risk = 0.004 + age * 0.0004;
    if(state.props.happy) risk *= 0.6;
    if(Math.random() < risk){
      die(age < 40 ? '意外离世' : '因病医治无效');
      return;
    }

    if(lines === 0){
      var quiet = ['平平淡淡的一年。','没什么特别的事发生。','日子如水般流过。'];
      addLine(age, rand(quiet), false);
    }

    $id('lrFast').textContent = age + ' 岁…';
    setTimeout(tick, 260);
  }

  function die(cause){
    state.alive = false;
    addLine(state.age, '〰️ ' + cause + '。', true);
    $id('lrFast').textContent = '';
    var excellent = state.props.look >= 7 || state.props.iq >= 7 || state.props.rich >= 7 || state.age >= 85;
    $id('lrAge').textContent = state.age + ' 岁';
    $id('lrTitle').textContent = '「' + cause + '」';
    $id('lrDesc').textContent = excellent
      ? '这一局相当精彩——属性和事件配合得不错，再开一局试试别的天赋组合？'
      : '人生没有白走的路，换一批天赋重新开一局，体验完全不同的剧本。';
    $id('lrEnd').style.display = 'block';
  }

  $id('lrStart').addEventListener('click', startLife);
  $id('lrRetry').addEventListener('click', function(){
    pickedTalents = [];
    attrVals = {look:5, iq:5, body:5, rich:5};
    $id('lrEnd').style.display = 'none';
    $id('lrLife').style.display = 'none';
    $id('lrSetup').style.display = 'block';
    rollTalents(); renderAttrs();
  });

  rollTalents(); renderAttrs();
})();
</script>
