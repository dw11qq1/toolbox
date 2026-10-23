<div id="mboxApp">
  <div class="score-row">
    <button class="btn" id="mbPlay">▶ 播放</button>
    <button class="btn" id="mbRandom">随机灵感</button>
    <button class="btn" id="mbClear">清空</button>
    <label class="bpm-label">速度 <input type="range" id="mbBpm" min="60" max="180" value="120"> <b id="mbBpmVal">120</b> BPM</label>
  </div>
  <div id="mbGrid"></div>
  <p class="fl" style="margin-top:0.7rem">C 大调五声音阶 —— 随便点都好听。点亮格子后按播放，循环播放你的旋律。</p>
</div>

<script>
(function(){
  /* 八声音高（高→低），C 大调五声音阶跨八度 */
  var ROWS = [
    {f: 659.25, label: 'E5'},
    {f: 587.33, label: 'D5'},
    {f: 523.25, label: 'C5'},
    {f: 440.00, label: 'A4'},
    {f: 392.00, label: 'G4'},
    {f: 329.63, label: 'E4'},
    {f: 293.66, label: 'D4'},
    {f: 261.63, label: 'C4'},
  ];
  var STEPS = 16;
  var grid = document.getElementById('mbGrid');
  var cells = [];        // cells[row][step] = DOM
  var on = [];           // on[row][step] = bool
  var playing = false, step = 0, timer = null;
  var audioCtx = null;

  // 建网格：行头(音名) + 16 格
  for(var r = 0; r < ROWS.length; r++){
    on[r] = [];
    var rowEl = document.createElement('div');
    rowEl.className = 'mb-row';
    var label = document.createElement('span');
    label.className = 'mb-row-label';
    label.textContent = ROWS[r].label;
    rowEl.appendChild(label);
    cells[r] = [];
    for(var s = 0; s < STEPS; s++){
      on[r][s] = false;
      (function(r, s){
        var cell = document.createElement('button');
        cell.className = 'mb-cell';
        cell.setAttribute('aria-label', ROWS[r].label + ' 第' + (s + 1) + '格');
        cell.addEventListener('click', function(){
          on[r][s] = !on[r][s];
          cell.classList.toggle('on', on[r][s]);
          if(on[r][s] && !playing) pluck(ROWS[r].f); // 关着的时候点了也能试听
        });
        cells[r][s] = cell;
        rowEl.appendChild(cell);
      })(r, s);
    }
    grid.appendChild(rowEl);
  }

  // 每 4 格一条浅色分组线
  for(var r2 = 0; r2 < ROWS.length; r2++){
    for(var s2 = 0; s2 < STEPS; s2++){
      if(s2 % 4 === 0) cells[r2][s2].classList.add('mb-beat');
    }
  }

  function pluck(freq){
    audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
    if(audioCtx.state === 'suspended') audioCtx.resume();
    var t = audioCtx.currentTime;
    var o = audioCtx.createOscillator(), g = audioCtx.createGain();
    o.type = 'triangle';
    o.frequency.value = freq;
    g.gain.setValueAtTime(0.18, t);
    g.gain.exponentialRampToValueAtTime(0.001, t + 0.6);
    o.connect(g).connect(audioCtx.destination);
    o.start(t); o.stop(t + 0.6);
  }

  function playStep(){
    for(var r = 0; r < ROWS.length; r++){
      if(on[r][step]) pluck(ROWS[r].f);
    }
    // 当前步高亮列
    for(var r2 = 0; r2 < ROWS.length; r2++){
      for(var s2 = 0; s2 < STEPS; s2++){
        cells[r2][s2].classList.toggle('cursor', s2 === step);
      }
    }
    step = (step + 1) % STEPS;
  }

  document.getElementById('mbPlay').addEventListener('click', function(){
    playing = !playing;
    this.textContent = playing ? '⏸ 停止' : '▶ 播放';
    if(playing){
      step = 0;
      var bpm = +document.getElementById('mbBpm').value;
      timer = setInterval(playStep, 60000 / bpm / 2); // 八分音符
    } else {
      clearInterval(timer);
      for(var r = 0; r < ROWS.length; r++)
        for(var s = 0; s < STEPS; s++)
          cells[r][s].classList.remove('cursor');
    }
  });

  document.getElementById('mbBpm').addEventListener('input', function(){
    document.getElementById('mbBpmVal').textContent = this.value;
    if(playing){
      clearInterval(timer);
      timer = setInterval(playStep, 60000 / +this.value / 2);
    }
  });

  document.getElementById('mbClear').addEventListener('click', function(){
    for(var r = 0; r < ROWS.length; r++)
      for(var s = 0; s < STEPS; s++){
        on[r][s] = false;
        cells[r][s].classList.remove('on');
      }
  });

  document.getElementById('mbRandom').addEventListener('click', function(){
    for(var r = 0; r < ROWS.length; r++){
      var count = 1 + Math.floor(Math.random() * 3); // 每行 1~3 个音
      var used = {};
      for(var c = 0; c < count; c++){
        var s = Math.floor(Math.random() * STEPS);
        if(!used[s]){ used[s] = true; on[r][s] = true; cells[r][s].classList.add('on'); }
      }
    }
  });
})();
</script>

<style>
#mboxApp .mb-row{display:flex;gap:4px;margin-bottom:4px;align-items:stretch}
#mboxApp .mb-row-label{font-family:var(--font-mono);font-size:0.7rem;color:var(--ink-3);width:2rem;display:grid;place-items:center;flex-shrink:0}
#mboxApp .mb-cell{flex:1;aspect-ratio:1.4;max-width:52px;border:1px solid var(--line);background:var(--panel);border-radius:6px;cursor:pointer;transition:background-color 120ms ease,border-color 120ms ease}
#mboxApp .mb-cell:hover{border-color:var(--accent)}
#mboxApp .mb-cell.mb-beat{border-left-width:2px;border-left-color:var(--ink-3)}
#mboxApp .mb-cell.on{background:var(--accent);border-color:var(--accent)}
#mboxApp .mb-cell.cursor:not(.on){background:var(--line)}
#mboxApp .bpm-label{display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:var(--ink-2)}
#mboxApp .bpm-label b{font-family:var(--font-mono);color:var(--accent)}
@media (max-width:600px){#mboxApp .mb-cell{max-width:none;aspect-ratio:1;border-radius:4px}}
</style>
