<div id="bsApp">
  <div class="bs-tray" id="bsTray">
    <span class="bs-tray-label">乐器架<span>拖到舞台即自动开始演奏，每件乐器一条独立时间线</span></span>
    <div class="bs-chips" id="bsChips"></div>
  </div>

  <div class="score-row">
    <button class="btn" id="bsPlayAll">▶ 全部开始</button>
    <button class="btn" id="bsPauseAll">⏸ 全部暂停</button>
    <button class="btn" id="bsRock">⚡ 电子组合</button>
    <button class="btn" id="bsHiphop">🎤 嘻哈组合</button>
    <button class="btn" id="bsRandom">🎲 随机组队</button>
    <button class="btn" id="bsSave">💾 保存</button>
    <button class="btn" id="bsLoad">📂 载入</button>
    <button class="btn" id="bsExport">⬇ 导出音乐</button>
    <button class="btn" id="bsClear">清空舞台</button>
    <label class="bpm-label">基准速度 <input type="range" id="bsBpm" min="70" max="170" value="110"> <b id="bsBpmVal">110</b> BPM</label>
  </div>

  <div class="bs-stage" id="bsStage"></div>
  <div class="bs-toast" id="bsToast"></div>

  <p class="fl" style="margin-top:0.8rem">每件乐器上台后立刻开始自己的循环，<b>各自一条独立时间线、速率略有差异</b>——所以多件乐器叠加后会不断错动、演化，像真乐队在互相呼应而不是齐步走。卡片上的小圆点是它自己的 16 步进度，各走各的。</p>
</div>

<script>
(function(){
  /* 乐器库：打击乐 + Beatbox 人声打击 + 旋律乐器，全部 Web Audio 实时合成
     rate = 该乐器相对基准速度的倍率（各自不同 → 异步错动） */
  var INSTRUMENTS = [
    {id:'kick',   label:'🥁 底鼓',  group:'打击乐', rate:1.00, patterns:['1000000010000000','1000100010001000','1000001000010000']},
    {id:'snare',  label:'🥢 军鼓',  group:'打击乐', rate:1.06, patterns:['0000100000001000','0010001000100010','0000100000100010']},
    {id:'hat',    label:'🎩 踩镲',  group:'打击乐', rate:0.97, patterns:['0010001000100010','0010101000101010','1010101010101010']},
    {id:'clap',   label:'👏 拍手',  group:'打击乐', rate:1.03, patterns:['0000000000001000','0000000000100000','0000000000000010']},
    {id:'bbBass', label:'🎤 低音 B', group:'Beatbox', rate:0.95, patterns:['1000000010000000','1000000001000010','1000010000100000']},
    {id:'bbK',    label:'🎤 军鼓 K', group:'Beatbox', rate:1.08, patterns:['0000100000001000','0000100000100000','0010000000100010']},
    {id:'bbTss',  label:'🎤 镲 tss', group:'Beatbox', rate:1.02, patterns:['0010001000100010','0010101000101010','0000001010000010']},
    {id:'bbPsh',  label:'🎤 喷气 P', group:'Beatbox', rate:0.99, patterns:['0000000000001000','0000001000000010','0000000010000000']},
    {id:'bass',   label:'🎸 贝斯',  group:'旋律', rate:1.05, patterns:['1000100010001000','1000000010000000','1000101010001010'], f:82.41},
    {id:'synth',  label:'🎹 合成',  group:'旋律', rate:0.93, patterns:['1000100010001001','0000100000001000','1000000110000000'], f:164.81},
    {id:'bell',   label:'🔔 铃铛',  group:'旋律', rate:1.10, patterns:['0000000000010000','0000000001000000','0000000000001000'], f:659.25},
    {id:'pluck',  label:'🪕 拨弦',  group:'旋律', rate:1.01, patterns:['0000100000001001','0010001000100010','0000000010000000'], f:329.63},
    {id:'piano',  label:'🎹 钢琴',  group:'旋律', rate:0.96,
      notes:[261.63, 293.66, 329.63, 392.00, 440.00], // C D E G A 五声音阶
      patterns:['1000100010001000','1001001010010010','0001001000010010']},
  ];
  var byId = {};
  INSTRUMENTS.forEach(function(i){ byId[i.id] = i; });

  var SLOTS = 8, STEPS = 16;
  var stage = document.getElementById('bsStage');
  var chipsBox = document.getElementById('bsChips');
  var slots = [], slotEls = [], dots = [];
  var audioCtx = null, noiseBuf = null, master = null;
  var picked = null;

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

  function ensureMaster(){
    var c = ctxGet();
    if(!master){
      master = c.createGain();
      master.gain.value = 1;
      master.connect(c.destination);
    }
    return master;
  }

  /* 合成引擎 */
  function playTrack(id, f, step){
    var c = ctxGet(), t = c.currentTime, g = c.createGain(), src;
    g.connect(ensureMaster());
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
      if(id === 'bbK'){
        flt.type = 'bandpass'; flt.frequency.value = 2000; flt.Q.value = 3;
        g.gain.setValueAtTime(0.42, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.1);
        src.start(t); src.stop(t + 0.12);
      }
      if(id === 'bbTss'){
        flt.type = 'highpass'; flt.frequency.value = 8200;
        g.gain.setValueAtTime(0.18, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.035);
        src.start(t); src.stop(t + 0.045);
      }
      if(id === 'bbPsh'){
        flt.type = 'bandpass'; flt.frequency.setValueAtTime(900, t);
        flt.frequency.exponentialRampToValueAtTime(400, t + 0.09);
        flt.Q.value = 1.2;
        g.gain.setValueAtTime(0.3, t); g.gain.exponentialRampToValueAtTime(0.001, t + 0.11);
        src.start(t); src.stop(t + 0.13);
      }
    } else if(id === 'bbBass'){
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
      var o2 = c.createOscillator(); o2.type = 'sine'; o2.frequency.value = freq * 2.01;
      var g1 = c.createGain(), g2 = c.createGain();
      g1.gain.setValueAtTime(0.22, t);
      g1.gain.exponentialRampToValueAtTime(0.001, t + 0.85);
      g2.gain.setValueAtTime(0.07, t);
      g2.gain.exponentialRampToValueAtTime(0.001, t + 0.4);
      o1.connect(g1).connect(ensureMaster()); o2.connect(g2).connect(ensureMaster());
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
        var o2b = c.createOscillator(), g2b = c.createGain();
        o2b.type = 'sine'; o2b.frequency.value = f * 2;
        g2b.gain.setValueAtTime(0.06, t);
        g2b.gain.exponentialRampToValueAtTime(0.001, t + 0.4);
        o2b.connect(g2b).connect(ensureMaster());
        o2b.start(t); o2b.stop(t + 0.42);
      }
      src.connect(g); src.start(t); src.stop(t + dur + 0.05);
    }
  }

  /* ---------- 乐器架（按分组） ---------- */
  var GROUPS = ['打击乐', 'Beatbox', '旋律'];
  GROUPS.forEach(function(grp){
    INSTRUMENTS.filter(function(i){ return i.group === grp; }).forEach(function(ins){
      if(!chipsBox.querySelector('.bs-group')){
        var tag0 = document.createElement('span');
        tag0.className = 'bs-group';
        tag0.textContent = grp;
        chipsBox.appendChild(tag0);
      }
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

  function syncChips(){
    Array.prototype.forEach.call(chipsBox.querySelectorAll('.bs-chip'), function(el){
      el.classList.toggle('picked', el.dataset.id === picked);
    });
  }

  /* ---------- 舞台 ---------- */
  function buildSlot(i){
    var el = document.createElement('div');
    el.className = 'bs-slot';
    el.dataset.i = i;

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
    if(slots[i]) stopSlot(i);
    slots[i] = {id: id, pattern: ins.patterns[0], muted: false, step: 0, timer: null, rate: ins.rate};
    renderSlot(i);
    startSlot(i); // 上台即开始自己的循环
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
      stopSlot(i);
      slots[i] = null;
      renderSlot(i);
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
    silent.className = 'bs-mini' + (s.muted ? '' : 'off');
    silent.textContent = '静音';
    silent.addEventListener('click', function(e){
      e.stopPropagation();
      s.muted = !s.muted; renderSlot(i);
    });

    tools.appendChild(swap); tools.appendChild(mute); tools.appendChild(silent);
    el.appendChild(tools);
  }

  /* ---------- 每件乐器自己的时钟 ---------- */
  function slotInterval(s){
    var base = 60000 / (+document.getElementById('bsBpm').value) / 4;
    return base * s.rate;
  }

  function startSlot(i){
    var s = slots[i];
    if(!s || s.timer) return;
    s.timer = setInterval(function(){ slotStep(i); }, slotInterval(s));
  }

  function stopSlot(i){
    var s = slots[i];
    if(!s) return;
    clearInterval(s.timer);
    s.timer = null;
    setCursor(i, -1);
  }

  function slotStep(i){
    var s = slots[i];
    if(!s) return;
    if(!s.muted && s.pattern[s.step] === '1'){
      playTrack(s.id, byId[s.id].f, s.step);
      var el = slotEls[i];
      el.classList.remove('hit');
      void el.offsetWidth;
      el.classList.add('hit');
    }
    setCursor(i, s.step);
    s.step = (s.step + 1) % STEPS;
  }

  function setCursor(i, st){
    if(!dots[i]) return;
    for(var d = 0; d < dots[i].length; d++) dots[i][d].classList.toggle('cur', d === st);
  }

  for(var i = 0; i < SLOTS; i++) buildSlot(i);

  /* ---------- 全局控制 ---------- */
  document.getElementById('bsPlayAll').addEventListener('click', function(){
    var started = 0;
    for(var i2 = 0; i2 < SLOTS; i2++) if(slots[i2]){ startSlot(i2); started++; }
    toast(started ? '全部开始演奏' : '舞台是空的，先拖乐器上来');
  });

  document.getElementById('bsPauseAll').addEventListener('click', function(){
    for(var i2 = 0; i2 < SLOTS; i2++) stopSlot(i2);
    toast('已全部暂停');
  });

  document.getElementById('bsBpm').addEventListener('input', function(){
    document.getElementById('bsBpmVal').textContent = this.value;
    // 基准速度变化：重挂在跑的循环，保持各自速率差异
    for(var i2 = 0; i2 < SLOTS; i2++){
      var s = slots[i2];
      if(s && s.timer){ clearInterval(s.timer); s.timer = null; startSlot(i2); }
    }
  });

  function clearStage(){
    for(var i2 = 0; i2 < SLOTS; i2++){
      stopSlot(i2);
      slots[i2] = null;
      renderSlot(i2);
    }
  }

  document.getElementById('bsClear').addEventListener('click', clearStage);

  function fill(list){
    clearStage();
    list.forEach(function(item, idx){
      var ins = byId[item.id];
      if(!ins || idx >= SLOTS) return;
      slots[idx] = {id: item.id, pattern: ins.patterns[item.pi || 0], muted: false, step: 0, timer: null, rate: ins.rate};
      renderSlot(idx);
      startSlot(idx);
    });
  }

  document.getElementById('bsRock').addEventListener('click', function(){
    fill([
      {id:'kick', pi:1}, {id:'snare', pi:0}, {id:'hat', pi:1}, {id:'bass', pi:0}, {id:'synth', pi:0}, {id:'clap', pi:0},
    ]);
  });

  document.getElementById('bsHiphop').addEventListener('click', function(){
    fill([
      {id:'bbBass', pi:1}, {id:'bbK', pi:0}, {id:'bbTss', pi:1}, {id:'bass', pi:2}, {id:'bell', pi:0}, {id:'pluck', pi:1},
    ]);
  });

  document.getElementById('bsRandom').addEventListener('click', function(){
    var pool = INSTRUMENTS.slice();
    var count = 3 + Math.floor(Math.random() * 3);
    var list = [];
    for(var k = 0; k < count; k++){
      var pick = pool.splice(Math.floor(Math.random() * pool.length), 1)[0];
      list.push({id: pick.id, pi: Math.floor(Math.random() * pick.patterns.length)});
    }
    fill(list);
  });

  /* ---------- 本地保存 / 载入 ---------- */
  var SAVE_KEY = 'bs.combo.v1';

  function toast(msg){
    var el = document.getElementById('bsToast');
    el.textContent = msg;
    el.classList.add('show');
    clearTimeout(el._t);
    el._t = setTimeout(function(){ el.classList.remove('show'); }, 2200);
  }

  document.getElementById('bsSave').addEventListener('click', function(){
    var data = {
      bpm: +document.getElementById('bsBpm').value,
      slots: slots.map(function(s){ return s ? {id: s.id, pattern: s.pattern, muted: !!s.muted} : null; })
    };
    try{
      localStorage.setItem(SAVE_KEY, JSON.stringify(data));
      toast('已保存到本地，下次打开自动恢复');
    }catch(e){
      toast('保存失败：浏览器存储不可用');
    }
  });

  function loadCombo(silent){
    var raw = null;
    try{ raw = localStorage.getItem(SAVE_KEY); }catch(e){}
    if(!raw){ if(!silent) toast('本地没有保存过的编曲'); return false; }
    var data;
    try{ data = JSON.parse(raw); }catch(e){ return false; }
    document.getElementById('bsBpm').value = data.bpm || 110;
    document.getElementById('bsBpmVal').textContent = document.getElementById('bsBpm').value;
    (data.slots || []).forEach(function(s, i){
      if(s && byId[s.id] && i < SLOTS){
        slots[i] = {id: s.id, pattern: s.pattern, muted: !!s.muted, step: 0, timer: null, rate: byId[s.id].rate};
        renderSlot(i);
        startSlot(i);
      }
    });
    if(!silent) toast('已载入本地编曲');
    return true;
  }

  document.getElementById('bsLoad').addEventListener('click', function(){ loadCombo(false); });
  loadCombo(true); // 打开页面自动恢复

  /* ---------- 导出音乐：录 8 秒自动下载 ---------- */
  var exportBtn = document.getElementById('bsExport');
  var recDest = null, mediaRec = null, recChunks = [], recording = false, recTimer = null;

  exportBtn.addEventListener('click', function(){
    if(recording){ stopRecording(); return; }
    if(!window.MediaRecorder){ toast('当前浏览器不支持录音导出，请用 Chrome / Edge'); return; }
    if(!slots.some(function(s){ return s; })){ toast('舞台是空的，先拖几件乐器上来'); return; }

    var c = ctxGet();
    try{
      recDest = c.createMediaStreamDestination();
      ensureMaster().connect(recDest);
    }catch(e){
      toast('录音初始化失败，换个浏览器试试');
      return;
    }

    var mime = ['audio/webm;codecs=opus', 'audio/webm', 'audio/ogg;codecs=opus'].filter(function(m){
      return MediaRecorder.isTypeSupported(m);
    })[0];

    recChunks = [];
    try{
      mediaRec = new MediaRecorder(recDest.stream, mime ? {mimeType: mime} : undefined);
    }catch(e){
      try{ master.disconnect(recDest); }catch(err){}
      toast('录音器启动失败，请用 Chrome / Edge');
      return;
    }

    mediaRec.ondataavailable = function(e){ if(e.data && e.data.size) recChunks.push(e.data); };
    mediaRec.onstop = function(){
      try{ master.disconnect(recDest); }catch(e){}
      var type = (mediaRec && mediaRec.mimeType) || 'audio/webm';
      var blob = new Blob(recChunks, {type: type});
      var ext = type.indexOf('ogg') >= 0 ? 'ogg' : 'webm';
      var a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = 'toolbox-beat-' + Date.now() + '.' + ext;
      document.body.appendChild(a);
      a.click();
      a.remove();
      setTimeout(function(){ URL.revokeObjectURL(a.href); }, 8000);
      recording = false;
      exportBtn.textContent = '⬇ 导出音乐';
      exportBtn.classList.remove('rec');
    };

    recording = true;
    mediaRec.start();
    exportBtn.textContent = '■ 停止并导出';
    exportBtn.classList.add('rec');
    // 确保所有乐器都在跑
    for(var i2 = 0; i2 < SLOTS; i2++) if(slots[i2]) startSlot(i2);
    toast('录制中（8 秒后自动下载），也可点按钮提前停止');
    recTimer = setTimeout(stopRecording, 8000);
  });

  function stopRecording(){
    clearTimeout(recTimer);
    if(mediaRec && mediaRec.state === 'recording') mediaRec.stop();
  }
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
#bsApp .bs-dots i.cur{box-shadow:0 0 0 1.5px var(--accent)}
#bsApp .bs-card-tools{display:flex;gap:0.3rem;flex-wrap:wrap}
#bsApp .bs-mini{border:1px solid var(--line);background:var(--panel);color:var(--ink-2);font-size:0.68rem;border-radius:999px;padding:0.15rem 0.5rem;cursor:pointer;transition:all 120ms ease}
#bsApp .bs-mini:hover{border-color:var(--accent);color:var(--accent)}
#bsApp .bs-mini.off{opacity:0.55}
#bsApp .btn.rec{background:var(--accent);color:#fff;border-color:var(--accent)}
#bsApp .bs-toast{position:fixed;left:50%;bottom:2rem;transform:translate(-50%,1rem);background:var(--ink);color:var(--bg,#fff);
  font-size:0.82rem;padding:0.5rem 1rem;border-radius:999px;opacity:0;pointer-events:none;transition:opacity 200ms ease,transform 200ms ease;z-index:99}
#bsApp .bs-toast.show{opacity:0.95;transform:translate(-50%,0)}
#bsApp .bpm-label{display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:var(--ink-2)}
#bsApp .bpm-label b{font-family:var(--font-mono);color:var(--accent)}
@media (max-width:600px){
  #bsApp .bs-stage{grid-template-columns:repeat(2,1fr)}
  #bsApp .bs-slot{min-height:104px}
  #bsApp .score-row{flex-wrap:wrap}
}
</style>

<div class="explain">
  <h3>玩法 &amp; 这个工具在做什么</h3>
  <p class="ex-text">这是个「一个人就是一支乐队」的沙盒：乐器架里 13 件乐器（4 件打击乐 + 4 件 Beatbox 人声打击 + 5 件旋律乐器）全部由 Web Audio 实时合成，不加载任何音频文件。把乐器拖到舞台，<b>它会立刻开始自己的循环，每件乐器一条独立的时间线</b>——而且每件乐器的速率都做了细微差别（0.93~1.10 倍），所以多件叠加后会不断错动、互相呼应，而不是齐步走，听感更像真乐队在即兴。卡片上的小圆点是它自己的 16 步进度，各走各的；触屏上先点乐器再点空格即可上台。「换节奏」切换该乐器的三种演奏型态，「试听」单听一件，「静音」把它从合奏里摘出来。「保存」把当前编曲存到浏览器本地（刷新、关页面都不丢，下次打开自动恢复），「导出音乐」用 MediaRecorder 录 8 秒实时混音，自动下载成音频文件（Chrome / Edge 效果最好）。</p>
</div>
