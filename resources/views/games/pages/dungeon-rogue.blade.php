<div id="rgApp">
  <div class="score-row">
    <span class="score-box">❤️ 血量 <b id="rgHp">30</b></span>
    <span class="score-box">⚔️ 攻击 <b id="rgAtk">5</b></span>
    <span class="score-box">🪙 金币 <b id="rgGold">0</b></span>
    <span class="score-box">🏔️ 第 <b id="rgFloor">1</b> 层</span>
    <button class="btn" id="rgNew">重新开始</button>
  </div>
  <div class="rg-map" id="rgMap" aria-label="地牢地图"></div>
  <div class="rg-pad">
    <span></span><button class="rg-btn" data-d="up">↑</button><span></span>
    <button class="rg-btn" data-d="left">←</button><button class="rg-btn" data-d="down">↓</button><button class="rg-btn" data-d="right">→</button>
  </div>
  <div class="rg-log" id="rgLog"></div>

  <div class="rg-over" id="rgOver" style="display:none">
    <p class="r-big">💀 你倒在了第 <span id="ovFloor"></span> 层</p>
    <p class="r-sub" id="ovStats"></p>
    <button class="btn" id="ovRetry">再来一局</button>
  </div>
</div>

<style>
#rgApp .rg-map{display:grid;grid-template-columns:repeat(11,1fr);gap:2px;max-width:462px;background:var(--panel);border:1px solid var(--line);border-radius:10px;padding:6px;user-select:none}
#rgApp .rg-cell{aspect-ratio:1;display:grid;place-items:center;font-size:clamp(12px,3vw,20px);border-radius:4px;background:var(--bg)}
#rgApp .rg-cell.wall{background:var(--ink);opacity:0.85}
#rgApp .rg-cell.exit{background:var(--accent)}
#rgApp .rg-pad{display:grid;grid-template-columns:repeat(3,56px);gap:6px;justify-content:center;margin:1rem auto 0.75rem}
#rgApp .rg-btn{aspect-ratio:1;border:1.5px solid var(--ink);background:var(--panel);color:var(--ink);border-radius:10px;font-size:1.1rem;cursor:pointer}
#rgApp .rg-btn:active{background:var(--accent);color:var(--accent-ink);border-color:var(--accent)}
#rgApp .rg-log{max-width:462px;height:96px;overflow-y:auto;font-family:var(--font-mono);font-size:0.75rem;color:var(--ink-3);background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:0.6rem 0.9rem;margin:0 auto}
#rgApp .rg-log p{margin-bottom:0.2rem}
#rgApp .rg-over{border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:2.5rem;text-align:center;margin-top:1rem}
#rgApp .r-big{font-family:var(--font-display);font-weight:700;font-size:1.6rem;color:var(--ink)}
#rgApp .r-sub{font-family:var(--font-mono);font-size:0.85rem;color:var(--ink-3);margin:0.6rem 0 1.4rem}
</style>

<script>
(function(){
  var N = 11;
  var EMPTY=0, WALL=1, MOB=2, POTION=3, CHEST=4, EXIT=5;
  var MOBS = ['👹','👻','🐺','🦂','🐙','🐲'];
  var map, player, logEl = document.getElementById('rgLog');
  var mapEl = document.getElementById('rgMap');

  function log(msg){
    var p = document.createElement('p');
    p.textContent = msg;
    logEl.appendChild(p);
    logEl.scrollTop = logEl.scrollHeight;
  }

  /* 生成一层：随机墙 + BFS 校验「出生点 → 楼梯」连通 */
  function genFloor(){
    for(var attempt = 0; attempt < 30; attempt++){
      var m = Array.from({length:N}, function(){ return Array(N).fill(EMPTY); });
      // 外圈墙
      for(var i = 0; i < N; i++){ m[0][i] = m[N-1][i] = m[i][0] = m[i][N-1] = WALL; }
      // 内部随机墙
      for(var r = 1; r < N-1; r++) for(var c = 1; c < N-1; c++){
        if(Math.random() < 0.16) m[r][c] = WALL;
      }
      var start = [1, 1];
      var exit = [N-2, N-2];
      m[start[0]][start[1]] = EMPTY;
      m[exit[0]][exit[1]] = EXIT;
      if(bfs(m, start, exit)){
        // 放怪 / 药水 / 宝箱（只放空地）
        var spots = [];
        for(var r2 = 1; r2 < N-1; r2++) for(var c2 = 1; c2 < N-1; c2++){
          if(m[r2][c2] === EMPTY && !(r2===1 && c2===1) && !(r2===exit[0] && c2===exit[1])) spots.push([r2,c2]);
        }
        shuffle(spots);
        var f = player.floor;
        var mobCount = Math.min(3 + f, 7);
        for(var k = 0; k < mobCount && spots.length; k++){
          var s = spots.pop();
          m[s[0]][s[1]] = MOB;
        }
        for(var p = 0; p < 2 && spots.length; p++){
          var s2 = spots.pop();
          m[s2[0]][s2[1]] = POTION;
        }
        if(spots.length){
          var s3 = spots.pop();
          m[s3[0]][s3[1]] = CHEST;
        }
        return m;
      }
    }
    // 兜底：无墙层
    var m2 = Array.from({length:N}, function(){ return Array(N).fill(EMPTY); });
    for(var j = 0; j < N; j++){ m2[0][j] = m2[N-1][j] = m2[j][0] = m2[j][N-1] = WALL; }
    m2[N-2][N-2] = EXIT;
    return m2;
  }

  function bfs(m, from, to){
    var seen = {}; seen[from[0]+','+from[1]] = true;
    var queue = [from];
    while(queue.length){
      var cur = queue.shift();
      if(cur[0] === to[0] && cur[1] === to[1]) return true;
      [[0,1],[0,-1],[1,0],[-1,0]].forEach(function(d){
        var nr = cur[0]+d[0], nc = cur[1]+d[1];
        var key = nr+','+nc;
        if(nr<0||nc<0||nr>=N||nc>=N||seen[key]||m[nr][nc]===WALL) return;
        seen[key] = true;
        queue.push([nr,nc]);
      });
    }
    return false;
  }

  function shuffle(a){
    for(var i = a.length-1; i > 0; i--){
      var j = Math.floor(Math.random()*(i+1));
      var t = a[i]; a[i] = a[j]; a[j] = t;
    }
    return a;
  }

  function newGame(){
    player = {r:1, c:1, hp:30, maxHp:30, atk:5, gold:0, floor:1};
    logEl.innerHTML = '';
    log('你走进了地牢第 1 层…');
    document.getElementById('rgOver').style.display = 'none';
    mapEl.style.display = '';
    document.querySelector('.rg-pad').style.display = '';
    nextFloor(false);
  }

  function nextFloor(announce){
    if(announce) log('⬇️ 下了楼梯，来到第 ' + (player.floor) + ' 层。');
    map = genFloor();
    player.r = 1; player.c = 1;
    draw();
    hud();
  }

  function hud(){
    document.getElementById('rgHp').textContent = player.hp;
    document.getElementById('rgAtk').textContent = player.atk;
    document.getElementById('rgGold').textContent = player.gold;
    document.getElementById('rgFloor').textContent = player.floor;
  }

  function draw(){
    mapEl.innerHTML = '';
    for(var r = 0; r < N; r++) for(var c = 0; c < N; c++){
      var cell = document.createElement('span');
      cell.className = 'rg-cell';
      var v = map[r][c];
      if(r === player.r && c === player.c){ cell.textContent = '🧑'; }
      else if(v === WALL){ cell.classList.add('wall'); }
      else if(v === EXIT){ cell.classList.add('exit'); cell.textContent = '🪜'; }
      else if(v === MOB){ cell.textContent = MOBS[Math.min(player.floor-1 + (r+c)%2, MOBS.length-1)]; }
      else if(v === POTION){ cell.textContent = '💧'; }
      else if(v === CHEST){ cell.textContent = '💰'; }
      mapEl.appendChild(cell);
    }
  }

  function move(dr, dc){
    if(!map) return;
    var nr = player.r + dr, nc = player.c + dc;
    if(nr < 0 || nc < 0 || nr >= N || nc >= N) return;
    var v = map[nr][nc];
    if(v === WALL) return;

    if(v === MOB){
      var mobHp = 6 + player.floor * 2;
      var mobAtk = 2 + Math.floor(player.floor * 0.8);
      var roll = Math.floor(Math.random() * 3); // 每只怪有点浮动
      mobHp += roll; mobAtk += Math.floor(roll / 2);
      mobHp = mobHp - player.atk;
      if(mobHp > 0){
        player.hp -= mobAtk;
        log('⚔️ 战斗！怪物还剩血量，你受到 ' + mobAtk + ' 点伤害。');
        if(player.hp <= 0){ die(); return; }
        return; // 怪没死，站住不动，下回合再打
      } else {
        map[nr][nc] = EMPTY;
        var gain = 5 + player.floor * 2;
        player.gold += gain;
        log('✅ 击败怪物！获得 ' + gain + ' 金币。');
      }
    }
    else if(v === POTION){
      map[nr][nc] = EMPTY;
      player.hp = Math.min(player.maxHp, player.hp + 10);
      log('💧 喝下药水，恢复 10 点血量。');
    }
    else if(v === CHEST){
      map[nr][nc] = EMPTY;
      var g = 10 + Math.floor(Math.random() * 10) + player.floor * 3;
      player.gold += g;
      player.atk += 1;
      log('💰 打开宝箱：+' + g + ' 金币，攻击 +1！');
    }
    else if(v === EXIT){
      player.floor++;
      nextFloor(true);
      return;
    }

    player.r = nr; player.c = nc;
    draw(); hud();
  }

  function die(){
    player.hp = 0; hud();
    mapEl.style.display = 'none';
    document.querySelector('.rg-pad').style.display = 'none';
    document.getElementById('ovFloor').textContent = player.floor;
    document.getElementById('ovStats').textContent = '战绩：金币 ' + player.gold + ' · 攻击 ' + player.atk;
    document.getElementById('rgOver').style.display = 'block';
    log('💀 你倒下了…');
  }

  var DIRS = {up:[-1,0], down:[1,0], left:[0,-1], right:[0,1]};
  document.querySelectorAll('.rg-btn').forEach(function(b){
    b.addEventListener('click', function(){ move.apply(null, DIRS[b.dataset.d]); });
  });
  document.addEventListener('keydown', function(e){
    var map2 = {ArrowUp:'up', ArrowDown:'down', ArrowLeft:'left', ArrowRight:'right', KeyW:'up', KeyS:'down', KeyA:'left', KeyD:'right'};
    if(map2[e.key] || map2[e.code]){
      e.preventDefault();
      var d = DIRS[map2[e.key] || map2[e.code]];
      move(d[0], d[1]);
    }
  });
  document.getElementById('rgNew').addEventListener('click', newGame);
  document.getElementById('ovRetry').addEventListener('click', newGame);
  newGame();
})();
</script>
