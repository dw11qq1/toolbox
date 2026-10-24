<div id="bsApp">
  <div class="bs-tray" id="bsTray">
    <span class="bs-tray-label">乐器架<span>拖到舞台，或先点乐器再点空格</span></span>
    <div class="bs-chips" id="bsChips"></div>
  </div>

  <div class="score-row">
    <button class="btn" id="bsPlay">▶ 播放</button>
    <button class="btn" id="bsRock">⚡ 电子组合</button>
    <button class="btn" id="bsHiphop">🎤 嘻哈组合</button>
    <button class="btn" id="bsRandom">🎲 随机组队</button>
    <button class="btn" id="bsClear">清空舞台</button>
    <label class="bpm-label">速度 <input type="range" id="bsBpm" min="70" max="170" value="110"> <b id="bsBpmVal">110</b> BPM</label>
  </div>

  <div class="bs-stage" id="bsStage"></div>

  <p class="fl" style="margin-top:0.8rem">每件乐器上台后都会按自己的节奏自动演奏——底鼓踩正拍、踩镲走八分、贝斯铺底、铃铛点缀，钢琴按五声音阶走旋律。13 件合成乐器（含 Beatbox 人声四件套）任意组合，点卡片上的「换节奏」可以切换它的演奏型态。</p>
</div>

<script>
(function(){
  /* 乐器库：打击乐 + Beatbox 人声打击 + 旋律乐器，全部 Web Audio 实时合成 */
  var INSTRUMENTS = [
    {id:'kick',   label:'🥁 底鼓',  group:'打击乐', patterns:['1000000010000000','1000100010001000','1000001000010000']},
    {id:'snare',  label:'🥢 军鼓',  group:'打击乐', patterns:['0000100000001000','0010001000100010','0000100000100010']},
    {id:'hat',    label:'🎩 踩镲',  group:'打击乐', patterns:['0010001000100010','0010101000101010','1010101010101010']},
    {id:'clap',   label:'👏 拍手',  group:'打击乐', patterns:['0000000000001000','0000000000100000','0000000000000010']},
    {id:'bbBass', label:'🎤 低音 B', group:'Beatbox', patterns:['1000000010000000','1000000001000010','1000010000100000']},
    {id:'bbK',    label:'🎤 军鼓 K', group:'Beatbox', patterns:['0000100000001000','0000100000100000','0010000000100010']},
    {id:'bbTss',  label:'🎤 镲 tss', group:'Beatbox', patterns:['0010001000100010','0010101000101010','0000001010000010']},
    {id:'bbPsh',  label:'🎤 喷气 P', group:'Beatbox', patterns:['0000000000001000','0000001000000010','0000000010000000']},
    {id:'bass',   label:'🎸 贝斯',  group:'旋律', patterns:['1000100010001000','1000000010000000','1000101010001010'], f:82.41},
    {id:'synth',  label:'🎹 合成',  group:'旋律', patterns:['1000100010001001','0000100000001000','1000000110000000'], f:164.81},
    {id:'bell',   label:'🔔 铃铛',  group:'旋律', patterns:['0000000000010000','0000000001000000','0000000000001000'], f:659.25},
    {id:'pluck',  label:'🪕 拨弦',  group:'旋律', patterns:['0000100000001001','0010001000100010','0000000010000000'], f:329.63},
    {id:'piano',  label:'🎹 钢琴',  group:'旋律',
      notes:[261.63, 293.66, 329.63, 392.00, 440.00], // C D E G A 五声音阶，随便弹都好听
      patterns:['1000100010001000','1001001010010010','0001001000010010']},
  ];
  var byId = {};
  INSTRUMENTS.forEach(function(i){ byId[i.id] = i; });

  var SLOTS = 8, STEPS = 16;
  var stage = document.getElementById('bsStage');
  var chipsBox = document.getElementById('bsChips');
  var slots = [], slotEls = [], dots = [];
  var audioCtx = null, noiseBuf = null;
  var playing = false, step = 0, timer = null;
  var picked = null; // 点选待放置的乐器 id

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

  /* 合成引擎：底鼓扫频、鼓组噪声、旋律振荡器、Beatbox 人声打击、钢琴 */
  function playTrack(id, f, step){
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
    } else if(id === 'snare' || id === 'hat' || id === 'clap' || id === 'bbK' || id === 'bbTss' || id === 'bbPsh'){
      src = c.createBufferSource();
      src.buffer = noise();
      var flt = c.createBiquadFilter();
      flt.connect(g); src.connect(flt);
      if(id === 'snare'){ flt.type = 'highpass'; flt.frequency.value = 1200; g.gain.setValueAtTime(0.3, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.16); src.start(t); src.stop(t + 0.18); }
      if(id === 'hat'){ flt.type = 'highpass'; flt.frequency.value = 7000; g.gain.setValueAtTime(0.22, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.05); src.start(t); src.stop(t + 0.06); }
      if(id === 'clap'){ flt.type = 'bandpass'; flt.frequency.value = 1800; g.gain.setValueAtTime(0.35, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.12); src.start(t); src.stop(t + 0.14); }
      if(id === 'bbK'){ /* Beatbox 军鼓 K：带 2kHz 共振峰的短促噪声 */
        flt.type = 'bandpass'; flt.frequency.value = 2000; flt.Q.value = 3;
        g.gain.setValueAtTime(0.42, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.1);
        src.start(t); src.stop(t + 0.12);
      }
      if(id === 'bbTss'){ /* Beatbox 镲 tss：极短高频噪声 */
        flt.type = 'highpass'; flt.frequency.value = 8200;
        g.gain.setValueAtTime(0.18, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.035);
        src.start(t); src.stop(t + 0.045);
      }
      if(id === 'bbPsh'){ /* Beatbox 喷气 P：扫频带通噪声 */
        flt.type = 'bandpass'; flt.frequency.setValueAtTime(900, t);
        flt.frequency.exponentialRampToValueAtTime(400, t + 0.09);
        flt.Q.value = 1.2;
        g.gain.setValueAtTime(0.3, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.11);
        src.start(t); src.stop(t + 0.13);
      }
    } else if(id === 'bbBass'){ /* Beatbox 低音 B：带低通的锯齿，喉音感 */
      src = c.createOscillator();
      src.type = 'sawtooth';
      src.frequency.setValueAtTime(110, t);
      src.frequency.exponentialRampToValueAtTime(48, t + 0.14);
      var lp = c.createBiquadFilter();
      lp.type = 'lowpass'; lp.frequency.value = 420; lp.Q.value = 6;
      g.gain.setValueAtTime(0.4, t);
      g.gain.exponentialRampToValueAtTime(0.001, t + 0.16);
      src.connect(lp).connect(g); src.start(t); src.stop(t + 0.18);
    } else if(id === 'piano'){
      var ins = byId['piano'];
      var freq = (ins.notes && ins.notes.length ? ins.notes : [261.63])[(step || 0) % ins.notes.length];
      var o1 = c.createOscillator(); o1.type = 'triangle'; o1.frequency.value = freq;
      var o2 = c.createOscillator(); o2.type = 'sine'; o2.frequency.value = freq * 2.01; // 微微失谐的八度泛音
      var g1 = c.createGain(), g2 = c.createGain();
      g1.gain.setValueAtTime(0.22, t);
      g1.gain.exponentialRampToValueAtTime(0.001, t + 0.85);
      g2.gain.setValueAtTime(0.07, t);
      g2.gain.exponentialRampToValueAtTime(0.001, t + 0.4);
      o1.connect(g1).connect(c.destination); o2.connect(g2).connect(c.destination);
      o1.start(t); o1.stop(t + 0.9); o2.start(t); o2.stop(t + 0.45);
    } else {
      src = c.createOscillator();
      src.type = id === 'bass' ? 'sawtooth' : id === 'bell' ? 'sine' : 'square';
      src.frequency.value = f;
      var vol = id === 'bass' ? 0.22 : id === 'synth' ? 0.12 : 0.15;
      var dur = id === 'bell' ? 0.5 : id === 'bass' ? 0.18 : 0.25;
      g.gain.setValueAtTime(vol, t);
      g.gain.exponentialRampToValueAtTime(0.001, t + dur);
      if(id === 'bell'){
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

  /* ---------- 乐器架 chips（按分组） ---------- */
  var GROUPS = ['打击乐', 'Beatbox', '旋律'];
  GROUPS.forEach(function(grp){
    var list = INSTRUMENTS.filter(function(i){ return i.group === grp; });
    if(!list.length) return;
    var tag = document.createElement('span');
    tag.className = 'bs-group';
    tag.textContent = grp;
    chipsBox.appendChild(tag);
    list.forEach(function(ins){
      var chip = document.createElement('button');
      chip.className = 'bs-chip';
      chip.draggable = true;
      chip.dataset.id = ins.id;
      chip.innerHTML = '<b>' + ins.label + '</b>';
      chip.addEventListener('dragstart', function(e){
        e.dataTransfer.setData('text/plain', ins.id);
        e.dataTransfer.effectAllowed = 'copy';
        chip.classList.add('dragging');
      });
      chip.addEventListener('dragend', function(){ chip.classList.remove('dragging'); });
      chip.addEventListener('click', function(){
        picked = (picked === ins.id) ? null : ins.id;
        syncChips();
      });
      chipsBox.appendChild(chip);
    });
  });

  /* ---------- 舞台 ---------- */
  function buildSlot(i){
    var el = document.createElement('div');
    el.className = 'bs-slot';
    el.dataset.i = i;

    var empty = document.createElement('div');
    empty.className = 'bs-empty';
    empty.textContent = '+';
    el.appendChild(empty);

    el.addEventListener('dragover', function(e){ e.preventDefault(); el.classList.add('over'); });
    el.addEventListener('dragleave', function(){ el.classList.remove('over'); });
    el.addEventListener('drop', function(e){
      e.preventDefault();
      el.classList.remove('over');
      place(i, e.dataTransfer.getData('text/plain'));
    });
    el.addEventListener('click', function(){
      if(picked){ place(i, picked); picked = null; syncChips(); }
    });
    stage.appendChild(el);
    slotEls[i] = el;
    dots[i] = [];
  }

  function place(i, id){
    var ins = byId[id];
    if(!ins) return;
    slots[i] = {id: id, pattern: ins.patterns[0], muted: false};
    renderSlot(i);
    if(!playing) playTrack(id, ins.f, 0); // 放下即试听一声
  }

  function renderSlot(i){
    var s = slots[i], el = slotEls[i];
    el.innerHTML = '';
    dots[i] = [];
    if(!s){
      var empty = document.createElement('div');
      empty.className = 'bs-empty';
      empty.textContent = '+';
      el.appendChild(empty);
      el.classList.remove('filled');
      return;
    }
    var ins = byId[s.id];
    el.classList.add('filled');

    var head = document.createElement('div');
    head.className = 'bs-card-head';
    head.innerHTML = '<span class="bs-card-name">' + ins.label + '</span>';

    var del = document.createElement('button');
    del.className = 'bs-card-x';
    del.textContent = '×';
    del.title = '移下舞台';
    del.addEventListener('click', function(e){
      e.stopPropagation();
      slots[i] = null; renderSlot(i);
    });
    head.appendChild(del);
    el.appendChild(head);

    var dotsRow = document.createElement('div');
    dotsRow.className = 'bs-dots';
    for(var st = 0; st < STEPS; st++){
      (function(st){
        var d = document.createElement('i');
        if(s.pattern[st] === '1') d.className = 'on';
        if(st % 4 === 0) d.classList.add('beat');
        dots[i].push(d);
        dotsRow.appendChild(d);
      })(st);
    }
    el.appendChild(dotsRow);

    var tools = document.createElement('div');
    tools.className = 'bs-card-tools';

    var swap = document.createElement('button');
    swap.className = 'bs-mini';
    swap.textContent = '换节奏';
    swap.addEventListener('click', function(e){
      e.stopPropagation();
      var idx = ins.patterns.indexOf(s.pattern);
      s.pattern = ins.patterns[(idx + 1) % ins.patterns.length];
      renderSlot(i);
    });

    var mute = document.createElement('button');
    mute.className = 'bs-mini' + (s.muted ? ' off' : '');
    mute.textContent = s.muted ? '已静音' : '试听';
    mute.addEventListener('click', function(e){
      e.stopPropagation();
      if(s.muted){
        s.muted = false; renderSlot(i);
      } else {
        playTrack(s.id, ins.f, 0);
      }
    });

    var silent = document.createElement('button');
    silent.className = 'bs-mini' + (s.muted ? '' : ' off');
    silent.textContent = '静音';
    silent.addEventListener('click', function(e){
      e.stopPropagation();
      s.muted = !s.muted; renderSlot(i);
    });

    tools.appendChild(swap); tools.appendChild(mute); tools.appendChild(silent);
    el.appendChild(tools);
  }

  function syncChips(){
    Array.prototype.forEach.call(chipsBox.children, function(el){
      el.classList.toggle('picked', el.dataset.id === picked);
    });
  }

  /* ---------- 播放循环 ---------- */
  function stepPlay(){
    for(var i = 0; i < SLOTS; i++){
      var s = slots[i];
      if(!s || s.muted) continue;
      if(s.pattern[step] === '1'){
        playTrack(s.id, byId[s.id].f, step);
        if(slotEls[i]) {
          slotEls[i].classList.remove('hit');
          void slotEls[i].offsetWidth;
          slotEls[i].classList.add('hit');
        }
      }
    }
    for(var i2 = 0; i2 < SLOTS; i2++){
      if(dots[i2]) for(var d = 0; d < dots[i2].length; d++){
        dots[i2][d].classList.toggle('cur', d === step);
      }
    }
    step = (step + 1) % STEPS;
  }

  function clearStage(){
    for(var i = 0; i < SLOTS; i++){ slots[i] = null; renderSlot(i); }
  }

  function fill(list){ // list: [{id, pi}]
    clearStage();
    list.forEach(function(item, idx){
      var ins = byId[item.id];
      if(!ins || idx >= SLOTS) return;
      slots[idx] = {id: item.id, pattern: ins.patterns[item.pi || 0], muted: false};
      renderSlot(idx);
    });
  }

  for(var i = 0; i < SLOTS; i++) buildSlot(i);

  /* ---------- 控件 ---------- */
  document.getElementById('bsPlay').addEventListener('click', function(){
    playing = !playing;
    this.textContent = playing ? '⏸ 停止' : '▶ 播放';
    if(playing){
      step = 0;
      timer = setInterval(stepPlay, 60000 / +document.getElementById('bsBpm').value / 4);
    } else {
      clearInterval(timer);
      for(var i2 = 0; i2 < SLOTS; i2++)
        if(dots[i2]) for(var d = 0; d < dots[i2].length; d++) dots[i2][d].classList.remove('cur');
    }
  });

  document.getElementById('bsBpm').addEventListener('input', function(){
    document.getElementById('bsBpmVal').textContent = this.value;
    if(playing){ clearInterval(timer); timer = setInterval(stepPlay, 60000 / +this.value / 4); }
  });

  document.getElementById('bsClear').addEventListener('click', clearStage);

  document.getElementById('bsRock').addEventListener('click', function(){
    fill([
      {id:'kick', pi:1}, {id:'snare', pi:0}, {id:'hat', pi:1}, {id:'bass', pi:0}, {id:'synth', pi:0}, {id:'clap', pi:0},
    ]);
  });

  document.getElementById('bsHiphop').addEventListener('click', function(){
    fill([
      {id:'kick', pi:2}, {id:'snare', pi:2}, {id:'hat', pi:0}, {id:'bass', pi:2}, {id:'bell', pi:0}, {id:'pluck', pi:1},
    ]);
  });

  document.getElementById('bsRandom').addEventListener('click', function(){
    var pool = INSTRUMENTS.slice();
    var count = 3 + Math.floor(Math.random() * 3); // 3~5 件
    var list = [];
    for(var k = 0; k < count; k++){
      var pick = pool.splice(Math.floor(Math.random() * pool.length), 1)[0];
      list.push({id: pick.id, pi: Math.floor(Math.random() * pick.patterns.length)});
    }
    fill(list);
  });
})();
</script>

<style>
#bsApp .bs-tray{margin-bottom:0.9rem}
#bsApp .bs-tray-label{display:flex;align-items:baseline;gap:0.6rem;font-family:var(--font-mono);font-size:0.68rem;color:var(--ink-3);letter-spacing:0.08em;margin-bottom:0.5rem}
#bsApp .bs-tray-label span{font-family:var(--font-sans);letter-spacing:0}
#bsApp .bs-chips{display:flex;flex-wrap:wrap;gap:0.45rem;align-items:center}
#bsApp .bs-group{font-family:var(--font-mono);font-size:0.62rem;color:var(--ink-3);letter-spacing:0.1em;padding:0.1rem 0.35rem;border-left:2px solid var(--line);margin-left:0.2rem}
#bsApp .bs-chip{border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:999px;padding:0.35rem 0.8rem;font-size:0.85rem;cursor:grab;transition:transform 120ms ease,border-color 120ms ease,background-color 120ms ease}
#bsApp .bs-chip:hover{border-color:var(--accent);transform:translateY(-2px)}
#bsApp .bs-chip.dragging{opacity:0.5}
#bsApp .bs-chip.picked{border-color:var(--accent);background:var(--accent);color:#fff}
#bsApp .bs-stage{display:grid;grid-template-columns:repeat(4,1fr);gap:0.6rem}
#bsApp .bs-slot{border:1.5px dashed var(--line);border-radius:12px;min-height:118px;display:flex;align-items:center;justify-content:center;background:var(--panel);transition:border-color 140ms ease,background-color 140ms ease}
#bsApp .bs-slot.over{border-color:var(--accent);background:var(--accent-soft,rgba(37,99,235,0.08))}
#bsApp .bs-empty{font-size:1.4rem;color:var(--ink-3)}
#bsApp .bs-slot.filled{border-style:solid;border-color:var(--line);background:var(--bg,#fff);flex-direction:column;align-items:stretch;padding:0.55rem 0.6rem;cursor:default}
#bsApp .bs-slot.hit{box-shadow:0 0 0 2px var(--accent)}
#bsApp .bs-card-head{display:flex;justify-content:space-between;align-items:center;gap:0.3rem}
#bsApp .bs-card-name{font-size:0.85rem;font-weight:600;color:var(--ink)}
#bsApp .bs-card-x{border:none;background:none;color:var(--ink-3);font-size:1rem;line-height:1;cursor:pointer;padding:0 0.2rem}
#bsApp .bs-card-x:hover{color:var(--accent)}
#bsApp .bs-dots{display:flex;gap:2px;margin:0.45rem 0}
#bsApp .bs-dots i{flex:1;height:6px;border-radius:2px;background:var(--line)}
#bsApp .bs-dots i.on{background:var(--accent)}
#bsApp .bs-dots i.beat{margin-left:0}
#bsApp .bs-dots i.cur{box-shadow:0 0 0 1.5px var(--accent)}
#bsApp .bs-card-tools{display:flex;gap:0.3rem;flex-wrap:wrap}
#bsApp .bs-mini{border:1px solid var(--line);background:var(--panel);color:var(--ink-2);font-size:0.68rem;border-radius:999px;padding:0.15rem 0.5rem;cursor:pointer;transition:all 120ms ease}
#bsApp .bs-mini:hover{border-color:var(--accent);color:var(--accent)}
#bsApp .bs-mini.off{opacity:0.55}
#bsApp .bpm-label{display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:var(--ink-2)}
#bsApp .bpm-label b{font-family:var(--font-mono);color:var(--accent)}
@media (max-width:600px){
  #bsApp .bs-stage{grid-template-columns:repeat(2,1fr)}
  #bsApp .bs-slot{min-height:104px}
  #bsApp .score-row{flex-wrap:wrap}
}
</style>

<div class="explain">
  <h3>玩法 & 这个工具在做什么</h3>
  <p class="ex-text">这是个「搭乐队」的沙盒：<b>乐器架</b>里 8 件乐器（4 件打击乐 + 4 件旋律乐器）全部由 Web Audio 实时合成，不加载任何音频文件。把乐器拖到舞台，它就会按自己的节奏型自动演奏——底鼓踩正拍、踩镲走八分音符、贝斯铺底、铃铛和拨弦做点缀。多件乐器叠加时，每件的节奏型在时间轴上互补，就组成了完整的律动；每张卡片上的小圆点显示它的 16 步节奏，播放时会跟着走光标。想换手感就点「换节奏」（每件乐器预置 3 种型态），嫌吵就「静音」，试听单件就「试听」。六件以上同台时建议先把 BPM 降到 80 左右，层次会更清楚。</p>
</div>
