<div id="svApp">
  <div class="sv-stats">
    <span class="score-box">❤️ 生命 <b id="svHp">50</b></span>
    <span class="score-box">🍖 食物 <b id="svFood">50</b></span>
    <span class="score-box">🧠 理智 <b id="svSan">50</b></span>
    <span class="score-box">🍀 运气 <b id="svLuck">50</b></span>
    <span class="score-box">📅 第 <b id="svDay">1</b> 天</span>
  </div>

  <div class="sv-day" id="svDayBanner">第 1 天 · 废土苏醒</div>

  <div class="sv-event" id="svEvent">
    <p class="sv-text" id="svText"></p>
    <div class="sv-opts" id="svOpts"></div>
  </div>

  <div class="sv-aftermath" id="svAfter" style="display:none">
    <p class="sv-aftermath-text" id="svAfterText"></p>
    <button class="btn" id="svNext">度过这一夜 →</button>
  </div>

  <div class="q-result" id="svEnd" style="display:none">
    <p class="fl">你的结局</p>
    <div class="mbti-code" id="svEndDays"></div>
    <p class="mbti-nick" id="svEndTitle"></p>
    <p class="mbti-desc" id="svEndDesc"></p>
    <button class="btn" id="svRetry">重新苏醒</button>
  </div>
</div>

<script>
(function(){
  /* 事件格式：[文本, [选项文本, dHP, dFood, dSan, dLuck], ×3]  正=好事件池 */
  var GOOD = [
    ["你在废墟里翻出一罐没过期的罐头","自己吃掉|[0,25,0,0]","分给同行的流浪猫|[0,10,8,5]","留着以备不时之需|[0,5,0,10]"],
    ["你发现一处未被搜刮的便利店","搬空所有能吃的|[5,35,-5,-10]|货架太满搬不动，累但值","只拿走最需要的|[0,15,0,5]|低调才活得久","在店里过夜避风头|[10,5,5,0]|久违的安稳觉"],
    ["一位老猎人邀请你同行","接受，多个照应|[10,10,5,5]|人多力量大","婉拒，独行侠|[0,-5,0,10]|自由但孤单","跟他交换情报|[0,10,0,10]|信息也是资源"],
    ["你捡到一台还能用的收音机","收听广播了解外界|[0,0,10,10]|知道世界还在就好","拆了零件做陷阱|[0,10,-5,0]|实用主义","放着当摆设陪自己|[0,0,8,0]|有个声音不孤单"],
    ["一群商队路过你的藏身处","用食物换药品|[15,-20,0,5]|健康最重要","用情报换补给|[0,20,0,5]|消息值钱","挥手目送|[0,0,5,5]|不贪就是赚"],
    ["你找到一本《废土生存手册》","通宵研读|[5,0,5,15]|知识就是命","撕了当引火物|[0,10,-8,0]|先活过今晚再说","抄下关键几页|[0,0,5,8]|务实"],
    ["下雨了，废土难得的淡水","接雨水囤起来|[0,20,0,5]|未雨绸缪","淋个痛快洗个澡|[10,0,10,0]|爽","收集雨水顺便省水|[0,15,0,0]|稳"],
    ["一只受过伤的狗狗向你走来","收留它|[5,5,15,10]|它给你的比你要的多","帮它包扎后放走|[0,0,8,5]|善缘","无视继续赶路|[0,0,-5,-5]|心硬了"],
    ["你发现一处山泉眼","痛快喝个够|[15,0,5,0]|甘甜","装满所有容器|[5,20,0,5]|囤","在此地扎营两日|[10,10,5,0]|休整"],
  ];
  var BAD = [
    ["废土武装队拦住了你的去路","交出部分食物保命|[0,-25,0,-5]|破财免灾","硬闯|[ -20,0,-10,-10]|搏一把","绕远路|[0,-10,-5,0]|多走一天"],
    ["你在废楼里踩到了碎玻璃","咬牙自己处理|[ -12,0,-5,0]|忍着疼","找布条简单包扎|[ -6,-5,0,0]|省着资源","呼叫附近营地帮忙|[-4,-10,0,5]|人情贵"],
    ["夜晚狼群围住了你的营地","点火把驱赶|[ -5,-5,-5,0]|赌它们怕火","上树躲一宿|[-8,0,-8,0]|狼走了你还没睡","扔食物引开|[-3,-20,0,5]|舍财保命"],
    ["你连续两天没找到像样的食物","啃树皮草根|[-5,10,-5,0]|能吃但不美味","饿着硬扛|[-10,-5,-5,0]|省下的都是赚的","冒险去闹市搜|[-8,25,-10,-10]|富贵险中求"],
    ["沙暴即将来袭","原地挖掩体躲三天|[-5,-15,-5,0]|憋屈但安全","顶着沙暴赶路|[-15,-8,-8,0]|快但伤","找废弃车壳暂避|[-8,-10,0,5]|看运气"],
    ["有人在暗处窥视你的物资","先下手为强吓走他|[ -5,0,-10,5]|气势不能输","装作不知道|[0,-10,-8,0]|熬","分他一半示好|[0,-25,5,10]|也许能交个朋友"],
    ["你出现了发烧的症状","翻找仅剩的药品|[ -8,-10,0,5]|药不能省","硬扛过去|[-18,-5,-5,0]|年轻就是资本","用土法子发汗|[ -12,-5,-3,3]|偏方赌命"],
    ["一群劫匪盯上了你的营地","连夜转移|[ -8,-10,-5,0]|辛苦但不亏","正面对峙|[-20,0,-5,-5]|狭路相逢勇者胜","主动分享换取和平|[0,-30,0,10]|破财免灾"],
    ["你迷路了，地图也丢了","凭记忆原路返回|[-5,-5,-8,0]|稳","往任意方向探路|[-10,-8,0,-5]|走出去再说","爬上高处辨认方向|[-8,-5,5,10]|站得高看得远"],
    ["物资被雨淋湿了一半","全部铺开晾晒|[-3,-5,0,0]|小心驶得万年船","吃掉快坏的|[5,10,-3,0]|不能浪费","不管它|[-8,-15,-3,0]|摆烂"],
  ];

  var DEATHS = {
    hp:'❤️ 你的伤势再也没能好起来。',
    food:'🍖 弹尽粮绝，你倒在了寻找食物的路上。',
    san:'🧠 你忘记了自己是谁，走进了雾里，再也没出来。',
    luck:'🍀 厄运缠身，一次意外夺走了一切。'
  };

  var day = 1;
  var st = {hp:50, food:50, san:50, luck:50};
  var current = null;

  var $ = function(id){ return document.getElementById(id); };

  function hud(){
    $('svHp').textContent = st.hp;
    $('svFood').textContent = st.food;
    $('svSan').textContent = st.san;
    $('svLuck').textContent = st.luck;
    $('svDay').textContent = day;
  }

  function pickEvent(){
    var goodChance = day <= 10 ? 0.5 : day <= 25 ? 0.35 : 0.22;
    return Math.random() < goodChance ? GOOD : BAD;
  }

  function render(){
    var pool = pickEvent();
    var ev = pool[Math.floor(Math.random() * pool.length)];
    current = ev;
    $('svDayBanner').textContent = '第 ' + day + ' 天 · ' + (pool === GOOD ? '风平浪静的一天' : '危机四伏的一天');
    $('svText').textContent = ev[0] + '。';
    var opts = $('svOpts');
    opts.innerHTML = '';
    for(var i = 0; i < 3; i++){
      (function(i){
        var parts = ev[1 + i][1].split('|');
        var b = document.createElement('button');
        b.className = 'q-opt';
        b.textContent = parts[0];
        b.addEventListener('click', function(){ choose(i); });
        opts.appendChild(b);
      })(i);
    }
  }

  function choose(i){
    var parts = current[1 + i].split('|');
    var text = parts[0];
    var eff = (parts[1] || '').match(/-?\d+/g);
    var d = eff ? eff.map(Number) : [0,0,0,0];
    // 结算：d = [hp, food, san, luck]
    st.hp = clamp(st.hp + d[0]);
    st.food = clamp(st.food + d[1]);
    st.san = clamp(st.san + d[2]);
    st.luck = clamp(st.luck + d[3]);
    hud();

    var deltas = [];
    var names = ['❤️','🍖','🧠','🍀'];
    for(var k = 0; k < 4; k++){
      if(d[k]) deltas.push(names[k] + (d[k] > 0 ? '+' : '') + d[k]);
    }
    $('svAfterText').textContent = text + (deltas.length ? '　（' + deltas.join('　') + '）' : '');
    $('svEvent').style.display = 'none';
    $('svAfter').style.display = 'block';

    // 死亡判定在「度过这一夜」时进行，先检查当场归零
    var dead = null;
    if(st.hp <= 0) dead = 'hp';
    else if(st.food <= 0) dead = 'food';
    else if(st.san <= 0) dead = 'san';
    else if(st.luck <= 0) dead = 'luck';
    $('svNext').textContent = dead ? '……' : '度过这一夜 →';
    $('svNext').dataset.dead = dead || '';
  }

  function clamp(v){ return Math.max(0, Math.min(100, v)); }

  $('svNext').addEventListener('click', function(){
    var dead = this.dataset.dead;
    if(dead){ end(dead); return; }
    day++;
    // 每天基础消耗：饥饿 -2
    st.food = clamp(st.food - 2);
    if(st.food <= 0){ st.hp = clamp(st.hp - 5); }
    hud();
    if(st.hp <= 0){ end('hp'); return; }
    $('svAfter').style.display = 'none';
    $('svEvent').style.display = 'block';
    render();
  });

  function end(cause){
    $('svEvent').style.display = 'none';
    $('svAfter').style.display = 'none';
    $('svDayBanner').textContent = '生存结束';
    $('svEndDays').textContent = day + ' 天';
    var lv = day >= 40 ? ['废土传说', '你的故事在幸存者营地口口相传。']
      : day >= 25 ? ['生存大师', '废土上没有几人能活得比你久。']
      : day >= 15 ? ['老练幸存者', '你已经摸清了这片废土的脾气。']
      : day >= 8 ? ['勉强立足', '活下去，正在变成一种习惯。']
      : ['出师未捷', '废土不给人适应的时间——再试一次。'];
    $('svEndTitle').textContent = '「' + lv[0] + '」 · ' + (DEATHS[cause] || '');
    $('svEndDesc').textContent = lv[1];
    $('svEnd').style.display = 'block';
  }

  $('svRetry').addEventListener('click', function(){
    day = 1;
    st = {hp:50, food:50, san:50, luck:50};
    $('svEnd').style.display = 'none';
    $('svEvent').style.display = 'block';
    render(); hud();
  });

  render(); hud();
})();
</script>

<style>
#svApp .sv-day{font-family:var(--font-mono);font-size:0.78rem;color:var(--accent);margin-bottom:1rem}
#svApp .sv-text{font-family:var(--font-display);font-weight:700;font-size:1.25rem;margin-bottom:1.4rem}
#svApp .sv-opts{display:grid;gap:0.75rem}
#svApp .q-opt{width:100%;text-align:left;font-family:var(--font-body);font-size:1rem;padding:1rem 1.3rem;border:1.5px solid var(--line);background:var(--panel);color:var(--ink);border-radius:12px;cursor:pointer;transition:border-color 160ms ease,background-color 160ms ease,color 160ms ease}
#svApp .q-opt:hover{border-color:var(--accent);background:var(--accent);color:var(--accent-ink)}
#svApp .sv-aftermath{border:1px solid var(--line);border-left:3px solid var(--accent);background:var(--panel);border-radius:0 10px 10px 0;padding:1.2rem 1.4rem}
#svApp .sv-aftermath-text{font-size:0.95rem;color:var(--ink-2);margin-bottom:1rem}
#svApp .q-result{border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:2rem;text-align:center}
#svApp .mbti-code{font-family:var(--font-display);font-weight:700;font-size:2.6rem;color:var(--accent)}
#svApp .mbti-nick{font-size:0.95rem;color:var(--ink);margin-top:0.4rem}
#svApp .mbti-desc{color:var(--ink-2);margin:0.8rem auto 1.4rem;max-width:42ch}
</style>
