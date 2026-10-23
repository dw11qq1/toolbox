<div id="bsApp">
  <div class="score-row">
    <button class="btn" id="bsPlay">▶ 播放</button>
    <button class="btn" id="bsRock">🎸 电子</button>
    <button class="btn" id="bsHiphop">🎤 嘻哈</button>
    <button class="btn" id="bsRandom">🎲 随机</button>
    <button class="btn" id="bsClear">清空</button>
    <label class="bpm-label">速度 <input type="range" id="bsBpm" min="70" max="170" value="110"> <b id="bsBpmVal">110</b> BPM</label>
  </div>
  <div id="bsGrid"></div>
  <p class="fl" style="margin-top:0.7rem">四组打击乐 + 四件旋律乐器：点亮格子叠出你的节奏。试试先放「电子」预置，再改动几个格子。</p>
</div>

<script>
(function(){
  /* 8 轨：4 打击乐（噪声/合成鼓）+ 4 旋律乐器（固定音高） */
  var TRACKS = [
    {id:'kick',  label:'🥁 底鼓'},
    {id:'snare', label:'🥢 军鼓'},
    {id:'hat',   label:'🎩 踩镲'},
    {id:'clap',  label:'👏 拍手'},
    {id:'bass',  label:'🎸 贝斯', f:82.41},   // E2
    {id:'synth', label:'🎹 合成', f:164.81},  // E3
    {id:'bell',  label:'🔔 铃铛', f:659.25},  // E5
    {id:'pluck', label:'🪕 拨弦', f:329.63},  // E4
  ];
  var STEPS = 16;
  var grid = document.getElementById('bsGrid');
  var cells = [], on = [], audioCtx = null;
  var playing = false, step = 0, timer = null, noiseBuf = null;

  function ctxGet(){
    audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
    if(audioCtx.state === 'suspended') audioCtx.resume();
    return audioCtx;
  }

  function noise(){
    if(!noiseBuf){
      var c = ctxGet();
      noiseBuf = c.createBuffer(1, c.sampleRate * 0.5, c.sampleRate);
      var d = noiseBuf.getChannelData(0);
      for(var i = 0; i < d.length; i++) d[i] = Math.random() * 2 - 1;
    }
    return noiseBuf;
  }

  function playTrack(id, f){
    var c = ctxGet(), t = c.currentTime, g = c.createGain(), src;
    g.connect(c.destination);
    if(id === 'kick'){
      src = c.createOscillator();
      src.type = 'sine';
      src.frequency.setValueAtTime(150, t);
      src.frequency.exponentialRampToValueAtTime(45, t + 0.12);
      g.gain.setValueAtTime(0.5, t);
      g.gain.exponentialRampToValueAtTime(0.001, t + 0.18);
      src.connect(g); src.start(t); src.stop(t + 0.2);
    } else if(id === 'snare' || id === 'hat' || id === 'clap'){
      src = c.createBufferSource();
      src.buffer = noise();
      var flt = c.createBiquadFilter();
      flt.connect(g); src.connect(flt);
      if(id === 'snare'){ flt.type = 'highpass'; flt.frequency.value = 1200; g.gain.setValueAtTime(0.3, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.16); src.start(t); src.stop(t + 0.18); }
      if(id === 'hat'){ flt.type = 'highpass'; flt.frequency.value = 7000; g.gain.setValueAtTime(0.22, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.05); src.start(t); src.stop(t + 0.06); }
      if(id === 'clap'){ flt.type = 'bandpass'; flt.frequency.value = 1800; g.gain.setValueAtTime(0.35, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.12); src.start(t); src.stop(t + 0.14); }
    } else {
      src = c.createOscillator();
      src.type = id === 'bass' ? 'sawtooth' : id === 'bell' ? 'sine' : 'square';
      src.frequency.value = f;
      var vol = id === 'bass' ? 0.22 : id === 'synth' ? 0.12 : 0.15;
      var dur = id === 'bell' ? 0.5 : id === 'bass' ? 0.18 : 0.25;
      g.gain.setValueAtTime(vol, t);
      g.gain.exponentialRampToValueAtTime(0.001, t + dur);
      if(id === 'bell'){ /* 铃铛加一个高八度泛音更像铃 */
        var o2 = c.createOscillator(), g2 = c.createGain();
        o2.type = 'sine'; o2.frequency.value = f * 2;
        g2.gain.setValueAtTime(0.06, t);
        g2.gain.exponentialRampToValueAtTime(0.001, t + 0.4);
        o2.connect(g2).connect(c.destination);
        o2.start(t); o2.stop(t + 0.42);
      }
      src.connect(g); src.start(t); src.stop(t + dur + 0.05);
    }
  }

  // 建网格
  TRACKS.forEach(function(tr, r){
    on[r] = [];
    var rowEl = document.createElement('div');
    rowEl.className = 'mb-row';
    var label = document.createElement('span');
    label.className = 'mb-row-label';
    label.textContent = tr.label;
    rowEl.appendChild(label);
    cells[r] = [];
    for(var s = 0; s < STEPS; s++){
      on[r][s] = false;
      (function(r, s){
        var cell = document.createElement('button');
        cell.className = 'mb-cell';
        cell.addEventListener('click', function(){
          on[r][s] = !on[r][s];
          cell.classList.toggle('on', on[r][s]);
          if(on[r][s] && !playing) playTrack(TRACKS[r].id, TRACKS[r].f);
        });
        cells[r][s] = cell;
        rowEl.appendChild(cell);
      })(r, s);
    }
    grid.appendChild(rowEl);
  });
  for(var r2 = 0; r2 < TRACKS.length; r2++)
    for(var s2 = 0; s2 < STEPS; s2++)
      if(s2 % 4 === 0) cells[r2][s2].classList.add('mb-beat');

  function stepPlay(){
    for(var r = 0; r < TRACKS.length; r++){
      if(on[r][step]) playTrack(TRACKS[r].id, TRACKS[r].f);
    }
    for(var r2 = 0; r2 < TRACKS.length; r2++)
      for(var s2 = 0; s2 < STEPS; s2++)
        cells[r2][s2].classList.toggle('cursor', s2 === step);
    step = (step + 1) % STEPS;
  }

  function setPattern(grid16){
    // grid16: {trackIndex: '1000100010001000'}
    clearAll();
    Object.keys(grid16).forEach(function(k){
      var r = +k;
      for(var s = 0; s < STEPS; s++){
        if(grid16[k][s] === '1'){ on[r][s] = true; cells[r][s].classList.add('on'); }
      }
    });
  }

  function clearAll(){
    for(var r = 0; r < TRACKS.length; r++)
      for(var s = 0; s < STEPS; s++){
        on[r][s] = false;
        cells[r][s].classList.remove('on');
      }
  }

  document.getElementById('bsPlay').addEventListener('click', function(){
    playing = !playing;
    this.textContent = playing ? '⏸ 停止' : '▶ 播放';
    if(playing){
      step = 0;
      timer = setInterval(stepPlay, 60000 / +document.getElementById('bsBpm').value / 2);
    } else {
      clearInterval(timer);
      for(var r = 0; r < TRACKS.length; r++)
        for(var s = 0; s < STEPS; s++)
          cells[r][s].classList.remove('cursor');
    }
  });

  document.getElementById('bsBpm').addEventListener('input', function(){
    document.getElementById('bsBpmVal').textContent = this.value;
    if(playing){ clearInterval(timer); timer = setInterval(stepPlay, 60000 / +this.value / 2); }
  });

  document.getElementById('bsClear').addEventListener('click', clearAll);

  document.getElementById('bsRock').addEventListener('click', function(){
    setPattern({0:'1000000010000000', 1:'0000100000001000', 2:'0010001000100010', 5:'1000100010001000', 7:'0000100000001001'});
  });

  document.getElementById('bsHiphop').addEventListener('click', function(){
    setPattern({0:'1000000000100000', 1:'0000100000001001', 2:'0010101000101010', 3:'0000000000000010', 4:'1000100010001000', 6:'0000100000001000'});
  });

  document.getElementById('bsRandom').addEventListener('click', function(){
    clearAll();
    for(var r = 0; r < TRACKS.length; r++){
      var density = r < 4 ? 0.18 : 0.14;
      for(var s = 0; s < STEPS; s++){
        if(Math.random() < density){ on[r][s] = true; cells[r][s].classList.add('on'); }
      }
    }
  });
})();
</script>

<style>
#bsApp .mb-row{display:flex;gap:4px;margin-bottom:4px;align-items:stretch}
#bsApp .mb-row-label{font-family:var(--font-mono);font-size:0.68rem;color:var(--ink-3);width:3.6rem;display:grid;place-items:center;flex-shrink:0;text-align:center}
#bsApp .mb-cell{flex:1;aspect-ratio:1.4;max-width:52px;border:1px solid var(--line);background:var(--panel);border-radius:6px;cursor:pointer;transition:background-color 120ms ease,border-color 120ms ease}
#bsApp .mb-cell:hover{border-color:var(--accent)}
#bsApp .mb-cell.mb-beat{border-left-width:2px;border-left-color:var(--ink-3)}
#bsApp .mb-cell.on{background:var(--accent);border-color:var(--accent)}
#bsApp .mb-cell.cursor:not(.on){background:var(--line)}
#bsApp .bpm-label{display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:var(--ink-2)}
#bsApp .bpm-label b{font-family:var(--font-mono);color:var(--accent)}
@media (max-width:600px){#bsApp .mb-cell{max-width:none;aspect-ratio:1;border-radius:4px}#bsApp .mb-row-label{font-size:0.6rem;width:3rem}}
</style>
