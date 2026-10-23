<div class="score-row">
  <span class="score-box">得分 <b id="rtScore">0</b></span>
  <span class="score-box">连击 <b id="rtCombo">0</b></span>
  <span class="score-box">最高连击 <b id="rtMax">0</b></span>
  <button class="btn" id="rtStart">开始</button>
</div>
<canvas id="rtCanvas" width="440" height="520" style="max-width:100%;border:1px solid var(--line);border-radius:10px;background:var(--panel);touch-action:none" aria-label="节奏游戏画布"></canvas>
<p class="fl" style="margin-top:0.6rem">键盘 D · F · J · K，或直接点击下方四个区域</p>

<script>
(function(){
  var LANES = 4, W = 440, H = 520;
  var LANE_W = W / LANES, LINE_Y = H - 90, NOTE_H = 26;
  var SPEED = 0.28; // px per ms
  var KEYS = {KeyD:0, KeyF:1, KeyJ:2, KeyK:3};
  var COLORS = ['#2563eb', '#7c3aed', '#0ea5e9', '#22c55e'];

  var canvas = document.getElementById('rtCanvas');
  var ctx = canvas.getContext('2d');
  var notes = [], effects = [], running = false, raf = null;
  var score = 0, combo = 0, maxCombo = 0, spawned = 0, lastSpawn = 0, totalCount = 60;
  var audioCtx = null;

  function beep(freq, dur){
    if(!audioCtx) return;
    var o = audioCtx.createOscillator(), g = audioCtx.createGain();
    o.type = 'triangle'; o.frequency.value = freq;
    g.gain.setValueAtTime(0.12, audioCtx.currentTime);
    g.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + dur);
    o.connect(g).connect(audioCtx.destination);
    o.start(); o.stop(audioCtx.currentTime + dur);
  }

  function spawn(now){
    // 均匀节拍 + 偶发双押
    var lane = spawned % 4;
    notes.push({lane: lane, y: -NOTE_H, hit: false});
    if(spawned % 8 === 7){
      notes.push({lane: (lane + 2) % 4, y: -NOTE_H, hit: false});
    }
    spawned++;
    lastSpawn = now;
  }

  function start(){
    notes = []; effects = [];
    score = 0; combo = 0; maxCombo = 0; spawned = 0;
    running = true;
    document.getElementById('rtScore').textContent = 0;
    document.getElementById('rtCombo').textContent = 0;
    document.getElementById('rtMax').textContent = 0;
    audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
    if(audioCtx.state === 'suspended') audioCtx.resume();
    lastSpawn = performance.now() + 400;
    if(raf) cancelAnimationFrame(raf);
    loop(performance.now());
  }

  function loop(now){
    if(!running) return;
    // 生成音符：每 550ms 一个
    if(now - lastSpawn > 550 && spawned < totalCount){ spawn(now); }

    ctx.clearRect(0, 0, W, H);

    // 轨道分隔线
    ctx.strokeStyle = 'rgba(128,128,140,0.25)';
    for(var l = 1; l < LANES; l++){
      ctx.beginPath();
      ctx.moveTo(l * LANE_W, 0); ctx.lineTo(l * LANE_W, H);
      ctx.stroke();
    }

    // 判定线
    ctx.fillStyle = '#2563eb';
    ctx.fillRect(0, LINE_Y - 2, W, 4);

    // 音符
    var dt = 16;
    for(var i = notes.length - 1; i >= 0; i--){
      var n = notes[i];
      n.y += SPEED * dt;
      if(!n.hit && n.y > H){ // miss
        notes.splice(i, 1);
        combo = 0;
        continue;
      }
      if(n.hit) continue;
      ctx.fillStyle = COLORS[n.lane];
      var x = n.lane * LANE_W + 10;
      roundRect(x, n.y, LANE_W - 20, NOTE_H, 6);
      ctx.fill();
    }

    // 命中特效
    for(var j = effects.length - 1; j >= 0; j--){
      var e = effects[j];
      e.life -= 16;
      if(e.life <= 0){ effects.splice(j, 1); continue; }
      ctx.fillStyle = 'rgba(37,99,235,' + (e.life / 300 * 0.5) + ')';
      roundRect(e.lane * LANE_W + 10, LINE_Y - NOTE_H / 2, LANE_W - 20, NOTE_H, 6);
      ctx.fill();
    }

    // 键位标签
    ctx.fillStyle = 'rgba(128,128,140,0.7)';
    ctx.font = '700 16px Sora, sans-serif';
    ctx.textAlign = 'center';
    ['D','F','J','K'].forEach(function(k, l){
      ctx.fillText(k, l * LANE_W + LANE_W / 2, H - 30);
    });

    if(spawned >= totalCount && notes.length === 0){
      finish();
      return;
    }

    raf = requestAnimationFrame(loop);
  }

  function roundRect(x, y, w, h, r){
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + h, r);
    ctx.arcTo(x + w, y + h, x, y + h, r);
    ctx.arcTo(x, y + h, x, y, r);
    ctx.arcTo(x, y, x + w, y, r);
    ctx.closePath();
  }

  function judge(lane){
    if(!running) return;
    var best = null, bestDist = 1e9;
    notes.forEach(function(n){
      if(n.lane !== lane || n.hit) return;
      var d = Math.abs(n.y + NOTE_H / 2 - LINE_Y);
      if(d < bestDist){ bestDist = d; best = n; }
    });
    if(best && bestDist < 50){
      best.hit = true;
      notes.splice(notes.indexOf(best), 1);
      combo++;
      maxCombo = Math.max(maxCombo, combo);
      score += bestDist < 22 ? 300 : bestDist < 35 ? 150 : 50;
      effects.push({lane: lane, life: 300});
      beep(440 + lane * 110, 0.12);
    } else {
      combo = 0;
      beep(160, 0.08);
    }
    document.getElementById('rtScore').textContent = score;
    document.getElementById('rtCombo').textContent = combo;
    document.getElementById('rtMax').textContent = maxCombo;
  }

  function finish(){
    running = false;
    cancelAnimationFrame(raf);
    ctx.fillStyle = 'rgba(21,23,28,0.75)';
    ctx.fillRect(0, 0, W, H);
    ctx.fillStyle = '#fff';
    ctx.textAlign = 'center';
    ctx.font = '700 30px Sora, sans-serif';
    ctx.fillText('演奏结束', W / 2, H / 2 - 30);
    ctx.font = '400 18px "Noto Sans SC", sans-serif';
    ctx.fillText('得分 ' + score + ' · 最高连击 ' + maxCombo, W / 2, H / 2 + 10);
    ctx.fillText('点「开始」再来一局', W / 2, H / 2 + 44);
  }

  document.addEventListener('keydown', function(e){
    if(KEYS[e.code] !== undefined && !e.repeat){
      e.preventDefault();
      judge(KEYS[e.code]);
    }
  });

  // 触屏/鼠标：点击画布下方 1/3 按轨道判定
  canvas.addEventListener('pointerdown', function(e){
    if(!running) return;
    var rect = canvas.getBoundingClientRect();
    var x = (e.clientX - rect.left) * (W / rect.width);
    judge(Math.min(3, Math.floor(x / LANE_W)));
  });

  document.getElementById('rtStart').addEventListener('click', start);
})();
</script>
