<div id="msApp">
  <div class="score-row">
    <span class="score-box">❤️ 你 <b id="msHp">50</b>/50</span>
    <span class="score-box">🛡️ 格挡 <b id="msBlock">0</b></span>
    <span class="score-box">⚡ 能量 <b id="msEnergy">3</b>/3</span>
    <span class="score-box">🗼 第 <b id="msFloor">1</b>/3 层 · <b id="msStage">1</b>/3 战</span>
    <button class="btn" id="msNew">重新开始</button>
  </div>

  <div class="ms-enemy" id="msEnemy">
    <div class="ms-enemy-sprite" id="msSprite">👹</div>
    <div class="ms-enemy-hp"><div id="msEhpBar" style="width:100%"></div></div>
    <p class="ms-intent" id="msIntent"></p>
  </div>

  <p class="msg" id="msLog" style="margin:0.75rem 0;min-height:1.4em"></p>

  <div class="ms-hand" id="msHand"></div>
  <button class="btn" id="msEnd">结束回合</button>

  <div id="msReward" style="display:none">
    <p class="fl" style="margin-top:1.5rem">战斗胜利！选择一张卡加入牌组：</p>
    <div class="ms-rewards" id="msRewards"></div>
  </div>

  <div class="q-result" id="msOver" style="display:none">
    <p class="fl">冒险结束</p>
    <div class="mbti-code" id="msResult"></div>
    <p class="mbti-nick" id="msResultTitle"></p>
    <button class="btn" id="msRetry">再爬一次</button>
  </div>
</div>

<script>
(function(){
  /* 卡池：[名称, 费用, 类型(attack/skill), 数值, 描述, 升级加成] */
  var CARDS = {
    strike:  {n:'打击',   cost:1, type:'attack', v:6,  d:'造成 6 点伤害'},
    defend:  {n:'防御',   cost:1, type:'block',  v:5,  d:'获得 5 点格挡'},
    bash:    {n:'重击',   cost:2, type:'attack', v:10, d:'造成 10 点伤害'},
    guard:   {n:'铁壁',   cost:2, type:'block',  v:11, d:'获得 11 点格挡'},
    double:  {n:'连斩',   cost:1, type:'attack', v:4,  d:'造成 4 点伤害 ×2'},
    drain:   {n:'吸血',   cost:2, type:'attack', v:6,  d:'造成 6 点伤害并回复等量生命', drain:true},
    improve: {n:'蓄力',   cost:0, type:'skill',  v:2,  d:'本场战斗攻击永久 +2', buff:true},
    heal:    {n:'急救',   cost:1, type:'skill',  v:6,  d:'回复 6 点生命'},
  };
  var REWARDS = ['bash','double','drain','improve','heal','guard'];
  var ENEMIES = [
    ['🦇','蝙蝠'],['🐛','毒虫'],['👻','小鬼'],['🐺','野狼'],
    ['🦂','巨蝎'],['👹','恶鬼'],
  ];
  var BOSSES = [['👹','塔主'],['🐲','尖塔之龙'],['💀','死灵王']];

  var S = null;
  var $ = function(id){ return document.getElementById(id); };

  function shuffle(a){
    for(var i = a.length-1; i > 0; i--){
      var j = Math.floor(Math.random()*(i+1));
      var t = a[i]; a[i] = a[j]; a[j] = t;
    }
    return a;
  }

  function newGame(){
    S = {
      hp:50, maxHp:50, floor:1, stage:1,
      deck:['strike','strike','strike','strike','defend','defend','defend','defend','bash','heal'],
      enemy:null, hand:[], drawPile:[], discard:[], block:0, energy:3, maxEnergy:3, atkBuff:0, busy:false,
    };
    $('msReward').style.display = 'none';
    $('msOver').style.display = 'none';
    spawnEnemy();
    startCombat();
  }

  function spawnEnemy(){
    var isBoss = S.stage === 3;
    var e = isBoss ? BOSSES[Math.floor(Math.random()*BOSSES.length)] : ENEMIES[Math.floor(Math.random()*ENEMIES.length)];
    var hp = 14 + S.floor * 10 + (isBoss ? 15 : 0) + S.stage * 4;
    S.enemy = {icon:e[0], name:e[1], hp:hp, maxHp:hp, atk:3 + S.floor * 2 + (isBoss ? 2 : 0)};
  }

  function rollIntent(){
    var r = Math.random();
    if(r < 0.6) S.enemy.intent = {t:'attack', v:S.enemy.atk + Math.floor(Math.random()*3)};
    else if(r < 0.85) S.enemy.intent = {t:'block', v:S.enemy.atk};
    else S.enemy.intent = {t:'strong', v:S.enemy.atk + 4};
  }

  function startCombat(){
    S.block = 0; S.energy = S.maxEnergy; S.atkBuff = 0;
    S.drawPile = shuffle(S.deck.slice());
    S.discard = []; S.hand = [];
    rollIntent();
    drawCards(5);
    render('遭遇 ' + S.enemy.name + '！打出卡牌应对。');
  }

  function drawCards(n){
    for(var i = 0; i < n; i++){
      if(!S.drawPile.length){
        if(!S.discard.length) break;
        S.drawPile = shuffle(S.discard);
        S.discard = [];
      }
      if(S.hand.length < 7) S.hand.push(S.drawPile.pop());
    }
  }

  function playCard(idx){
    if(S.busy) return;
    var key = S.hand[idx], card = CARDS[key];
    if(card.cost > S.energy) { logMsg('能量不足！'); return; }
    S.energy -= card.cost;
    S.hand.splice(idx, 1);
    S.discard.push(key);

    if(card.type === 'attack'){
      var dmg = card.v + S.atkBuff;
      var times = key === 'double' ? 2 : 1;
      for(var t = 0; t < times; t++){
        S.enemy.hp -= dmg;
        logMsg('你打出「' + card.n + '」，造成 ' + dmg + ' 点伤害！');
      }
      if(card.drain){
        S.hp = Math.min(S.maxHp, S.hp + dmg);
        logMsg('吸血回复了 ' + dmg + ' 点生命。');
      }
    } else if(card.type === 'block'){
      S.block += card.v;
      logMsg('你打出「' + card.n + '」，获得 ' + card.v + ' 点格挡。');
    } else if(card.buff){
      S.atkBuff += card.v;
      logMsg('蓄力完成！本场攻击 +' + card.v + '。');
    } else if(key === 'heal'){
      S.hp = Math.min(S.maxHp, S.hp + card.v);
      logMsg('回复了 ' + card.v + ' 点生命。');
    }

    if(S.enemy.hp <= 0){ winCombat(); return; }
    render();
  }

  $('msEnd').addEventListener('click', function(){
    if(S.busy || !S.enemy) return;
    enemyTurn();
  });

  function enemyTurn(){
    S.busy = true;
    var it = S.enemy.intent;
    setTimeout(function(){
      if(it.t === 'attack' || it.t === 'strong'){
        var dmg = it.v;
        var absorbed = Math.min(S.block, dmg);
        S.block -= absorbed;
        dmg -= absorbed;
        S.hp -= dmg;
        logMsg(S.enemy.name + ' 攻击了你！' + (absorbed ? '格挡抵消 ' + absorbed + ' 点。' : '') + (dmg ? '你受到 ' + dmg + ' 点伤害。' : '未被击穿。'));
      } else {
        S.enemy.hp = Math.min(S.enemy.maxHp, S.enemy.hp + it.v);
        logMsg(S.enemy.name + ' 进入防御姿态，回复了生命。');
      }
      if(S.hp <= 0){ gameOver(); return; }

      // 敌人偶尔回血时受伤衰减略过；进入新回合
      S.block = 0;
      S.energy = S.maxEnergy;
      drawCards(5 - S.hand.length > 0 ? 5 - S.hand.length : 1);
      // 补抽到 5 张
      while(S.hand.length < 5 && (S.drawPile.length || S.discard.length)){
        if(!S.drawPile.length){ S.drawPile = shuffle(S.discard); S.discard = []; }
        S.hand.push(S.drawPile.pop());
      }
      rollIntent();
      S.busy = false;
      render();
    }, 450);
  }

  function winCombat(){
    logMsg('🎉 击败了 ' + S.enemy.name + '！');
    S.busy = true;
    render();
    setTimeout(function(){
      if(S.floor === 3 && S.stage === 3){
        victory();
        return;
      }
      if(S.stage === 3){
        S.floor++; S.stage = 1;
        S.maxHp += 8; S.hp = Math.min(S.maxHp, S.hp + 15);
        logMsg('🗼 进入第 ' + S.floor + ' 层！最大生命 +8。');
      } else {
        S.stage++;
      }
      showReward();
    }, 500);
  }

  function showReward(){
    $('msReward').style.display = 'block';
    var box = $('msRewards');
    box.innerHTML = '';
    var pool = shuffle(REWARDS.slice()).slice(0, 3);
    pool.forEach(function(key){
      var c = CARDS[key];
      var b = document.createElement('button');
      b.className = 'q-opt';
      b.innerHTML = '<b>' + c.n + '</b>（' + c.cost + ' 费）— ' + c.d;
      b.addEventListener('click', function(){
        S.deck.push(key);
        $('msReward').style.display = 'none';
        spawnEnemy();
        startCombat();
      });
      box.appendChild(b);
    });
  }

  function victory(){
    S.busy = true;
    $('msEnemy').style.display = 'none';
    $('msHand').style.display = 'none';
    $('msEnd').style.display = 'none';
    $('msResult').textContent = '🏆 登顶！';
    $('msResultTitle').textContent = '你征服了尖塔，' + S.hp + '/' + S.maxHp + ' 血量登顶。牌组共 ' + S.deck.length + ' 张卡。';
    $('msOver').style.display = 'block';
  }

  function gameOver(){
    S.busy = true;
    $('msEnemy').style.display = 'none';
    $('msHand').style.display = 'none';
    $('msEnd').style.display = 'none';
    $('msResult').textContent = '💀 倒在 ' + S.floor + ' 层';
    $('msResultTitle').textContent = '差一点就过了——调整牌组策略再来。';
    $('msOver').style.display = 'block';
  }

  function logMsg(t){ $('msLog').textContent = t; }

  function render(msg){
    if(msg) logMsg(msg);
    $('msHp').textContent = S.hp;
    $('msBlock').textContent = S.block;
    $('msEnergy').textContent = S.energy;
    $('msFloor').textContent = S.floor;
    $('msStage').textContent = S.stage;

    $('msEnemy').style.display = S.enemy ? '' : 'none';
    if(S.enemy){
      $('msSprite').textContent = S.enemy.icon;
      $('msEhpBar').style.width = Math.max(0, S.enemy.hp / S.enemy.maxHp * 100) + '%';
      var it = S.enemy.intent;
      $('msIntent').textContent = it.t === 'attack' ? '⚔️ 下回合攻击 ' + it.v : it.t === 'block' ? '🛡️ 下回合防御' : '💥 下回合强力攻击 ' + it.v;
    }

    var hand = $('msHand');
    hand.innerHTML = '';
    S.hand.forEach(function(key, i){
      var c = CARDS[key];
      var b = document.createElement('button');
      b.className = 'ms-card' + (c.cost > S.energy ? ' ms-disabled' : '');
      b.innerHTML = '<span class="ms-card-cost">' + c.cost + '</span><b>' + c.n + '</b><span class="ms-card-d">' + c.d + '</span>';
      b.addEventListener('click', function(){ playCard(i); });
      hand.appendChild(b);
    });
  }

  $('msNew').addEventListener('click', newGame);
  $('msRetry').addEventListener('click', function(){
    $('msEnemy').style.display = '';
    $('msHand').style.display = '';
    $('msEnd').style.display = '';
    newGame();
  });
  newGame();
})();
</script>

<style>
#msApp .ms-enemy{text-align:center;border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:1.5rem 1rem 1rem;max-width:320px;margin:0 auto}
#msApp .ms-enemy-sprite{font-size:3rem}
#msApp .ms-enemy-hp{height:8px;background:var(--bg);border-radius:99px;overflow:hidden;margin-top:0.5rem}
#msApp .ms-enemy-hp div{height:100%;background:#c0492f;transition:width 250ms ease}
#msApp .ms-intent{font-family:var(--font-mono);font-size:0.8rem;color:var(--accent);margin-top:0.5rem}
#msApp .ms-hand{display:flex;gap:0.6rem;flex-wrap:wrap;margin:1rem 0;min-height:120px}
#msApp .ms-card{width:118px;text-align:left;border:1.5px solid var(--line);background:var(--panel);color:var(--ink);border-radius:10px;padding:0.7rem 0.8rem;cursor:pointer;display:flex;flex-direction:column;gap:0.25rem;transition:border-color 150ms ease,transform 150ms ease}
#msApp .ms-card:hover:not(.ms-disabled){border-color:var(--accent);transform:translateY(-3px)}
#msApp .ms-card.ms-disabled{opacity:0.45;cursor:default}
#msApp .ms-card-cost{position:absolute;margin-top:-0.4rem;font-family:var(--font-mono);font-size:0.7rem;color:var(--accent)}
#msApp .ms-card b{font-family:var(--font-display);font-size:0.95rem}
#msApp .ms-card-d{font-size:0.72rem;color:var(--ink-3);line-height:1.4}
#msApp .ms-rewards{display:grid;gap:0.75rem}
#msApp .q-result{border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:2rem;text-align:center}
#msApp .mbti-code{font-family:var(--font-display);font-weight:700;font-size:2.2rem;color:var(--accent)}
#msApp .mbti-nick{color:var(--ink-2);margin-top:0.6rem}
</style>
