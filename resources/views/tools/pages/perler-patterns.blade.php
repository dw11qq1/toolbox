<div class="field">
  <span class="fl">底板尺寸</span>
  <div class="btn-row" style="gap:0.6rem;flex-wrap:wrap">
    <select id="pbSize" style="font-size:0.9rem;padding:0.35rem 0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px">
      <option value="16">16 × 16（小底板）</option>
      <option value="20">20 × 20</option>
      <option value="29" selected>29 × 29（标准大方板）</option>
      <option value="40">40 × 40（拼两块）</option>
    </select>
    <button class="btn" id="pbHeart" style="padding:0.35rem 0.9rem">示例：爱心</button>
    <button class="btn" id="pbClear" style="padding:0.35rem 0.9rem">清空</button>
    <button class="btn" id="pbSave" style="padding:0.35rem 0.9rem">保存到本地</button>
    <button class="btn" id="pbLoad" style="padding:0.35rem 0.9rem">载入</button>
    <button class="btn" id="pbExport" style="padding:0.35rem 0.9rem">导出 PNG</button>
  </div>
</div>
<div class="field">
  <span class="fl">拼豆色板（点击选色 · 当前：<b id="pbCurName">大红</b>）</span>
  <div id="pbPalette"></div>
</div>
<div class="field">
  <span class="fl">图纸（按住拖动点豆 · 右键擦除）</span>
  <canvas id="pbCanvas" style="border:1px solid var(--line);border-radius:8px;touch-action:none;cursor:crosshair;max-width:100%"></canvas>
</div>
<div class="field" id="pbCountWrap" style="display:none">
  <span class="fl">用豆清单（按颜色统计，照着买豆就行）</span>
  <div id="pbCounts"></div>
</div>

<style>
#pbPalette{display:flex;flex-wrap:wrap;gap:6px;max-width:420px}
.pb-swatch{width:28px;height:28px;border-radius:50%;cursor:pointer;border:2px solid rgba(0,0,0,0.12);box-sizing:border-box;transition:transform .12s}
.pb-swatch:hover{transform:scale(1.15)}
.pb-swatch.on{border:2px solid var(--ink);transform:scale(1.15);box-shadow:0 0 0 2px var(--panel),0 0 0 4px var(--ink)}
#pbCounts{display:flex;flex-wrap:wrap;gap:0.5rem}
.pb-count{display:inline-flex;align-items:center;gap:0.4rem;font-size:0.8rem;color:var(--ink-2);border:1px solid var(--line);border-radius:999px;padding:0.25rem 0.7rem 0.25rem 0.35rem}
.pb-count i{width:16px;height:16px;border-radius:50%;display:inline-block;border:1px solid rgba(0,0,0,0.12)}
.pb-count b{font-family:var(--font-mono)}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  // 常见拼豆色号（近似色）
  var COLORS = [
    ['白色', '#ffffff'], ['奶油', '#f2ead8'], ['浅黄', '#ffe08a'], ['明黄', '#ffd21f'],
    ['橙', '#ff9a3c'], ['橘红', '#ff6b35'], ['大红', '#e63946'], ['深红', '#b3202e'],
    ['粉', '#ff9ecb'], ['桃粉', '#ff7fa5'], ['玫红', '#e04f8f'], ['浅紫', '#c5a3f5'],
    ['紫', '#9b5de5'], ['深紫', '#6a2ca0'], ['浅蓝', '#9bd4ff'], ['天蓝', '#35a7ff'],
    ['深蓝', '#1d6fd1'], ['藏青', '#274690'], ['浅绿', '#a8e6a1'], ['草绿', '#3ec46d'],
    ['深绿', '#1f7a4d'], ['棕', '#8d5a3a'], ['深棕', '#5b3a24'], ['浅灰', '#b0b6c0'],
    ['深灰', '#6b7280'], ['黑', '#22252a'],
  ];

  var rows = 29, cols = 29, cell = 18;
  var grid = [];
  var current = '#e63946';
  var canvas = $('pbCanvas'), ctx = canvas.getContext('2d');
  var dpr = window.devicePixelRatio || 1;
  var drawing = false, eraseMode = false;

  function initGrid(r, c, keep){
    var old = grid;
    grid = [];
    for(var y = 0; y < r; y++){
      var rowArr = [];
      for(var x = 0; x < c; x++){
        rowArr.push(keep && old[y] && old[y][x] ? old[y][x] : '');
      }
      grid.push(rowArr);
    }
  }

  function setupCanvas(){
    canvas.width = cols * cell * dpr;
    canvas.height = rows * cell * dpr;
    canvas.style.width = (cols * cell) + 'px';
    canvas.style.height = (rows * cell) + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  }

  function draw(){
    ctx.fillStyle = '#fdfdfb';
    ctx.fillRect(0, 0, cols * cell, rows * cell);
    ctx.strokeStyle = 'rgba(0,0,0,0.07)';
    ctx.lineWidth = 1;
    for(var x = 0; x <= cols; x++){
      ctx.beginPath(); ctx.moveTo(x * cell + 0.5, 0); ctx.lineTo(x * cell + 0.5, rows * cell); ctx.stroke();
    }
    for(var y = 0; y <= rows; y++){
      ctx.beginPath(); ctx.moveTo(0, y * cell + 0.5); ctx.lineTo(cols * cell, y * cell + 0.5); ctx.stroke();
    }
    var r = cell * 0.42;
    for(var gy = 0; gy < rows; gy++){
      for(var gx = 0; gx < cols; gx++){
        var color = grid[gy][gx];
        if(!color) continue;
        var cx = gx * cell + cell / 2, cy = gy * cell + cell / 2;
        ctx.beginPath();
        ctx.arc(cx, cy, r, 0, Math.PI * 2);
        ctx.fillStyle = color;
        ctx.fill();
        ctx.lineWidth = 1;
        ctx.strokeStyle = 'rgba(0,0,0,0.15)';
        ctx.stroke();
        // 高光
        ctx.beginPath();
        ctx.arc(cx - r * 0.3, cy - r * 0.3, r * 0.25, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(255,255,255,0.35)';
        ctx.fill();
      }
    }
    countBeads();
  }

  function countBeads(){
    var tally = {};
    var total = 0;
    for(var y = 0; y < rows; y++){
      for(var x = 0; x < cols; x++){
        var c = grid[y][x];
        if(c){ tally[c] = (tally[c] || 0) + 1; total++; }
      }
    }
    var wrap = $('pbCountWrap'), box = $('pbCounts');
    if(!total){ wrap.style.display = 'none'; return; }
    var html = '';
    Object.keys(tally).forEach(function(hex){
      var name = '色 ' + hex.toUpperCase();
      for(var i = 0; i < COLORS.length; i++){ if(COLORS[i][1] === hex) name = COLORS[i][0]; }
      html += '<span class="pb-count"><i style="background:' + hex + '"></i>' + name + ' <b>× ' + tally[hex] + '</b></span>';
    });
    box.innerHTML = html;
    wrap.style.display = 'block';
  }

  function buildPalette(){
    var html = '';
    COLORS.forEach(function(c, i){
      html += '<span class="pb-swatch' + (c[1] === current ? ' on' : '') + '" data-hex="' + c[1] + '" data-name="' + c[0] + '" style="background:' + c[1] + '"></span>';
    });
    $('pbPalette').innerHTML = html;
    $('pbPalette').addEventListener('click', function(e){
      var s = e.target.closest('.pb-swatch');
      if(!s) return;
      current = s.getAttribute('data-hex');
      $('pbCurName').textContent = s.getAttribute('data-name');
      document.querySelectorAll('.pb-swatch').forEach(function(el){ el.classList.toggle('on', el === s); });
    });
  }

  function cellAt(e){
    var rect = canvas.getBoundingClientRect();
    var x = Math.floor((e.clientX - rect.left) / (rect.width / cols));
    var y = Math.floor((e.clientY - rect.top) / (rect.height / rows));
    if(x < 0 || y < 0 || x >= cols || y >= rows) return null;
    return [x, y];
  }

  function apply(e){
    var pos = cellAt(e);
    if(!pos) return;
    grid[pos[1]][pos[0]] = (e.buttons === 2 || eraseMode) ? '' : current;
    draw();
  }

  canvas.addEventListener('contextmenu', function(e){ e.preventDefault(); });
  canvas.addEventListener('pointerdown', function(e){
    drawing = true; eraseMode = (e.button === 2);
    apply(e); canvas.setPointerCapture(e.pointerId);
  });
  canvas.addEventListener('pointermove', function(e){ if(drawing) apply(e); });
  canvas.addEventListener('pointerup', function(){ drawing = false; eraseMode = false; });

  $('pbSize').addEventListener('change', function(){
    var n = parseInt(this.value);
    rows = cols = n;
    initGrid(rows, cols, true);
    setupCanvas(); draw();
  });

  $('pbClear').addEventListener('click', function(){
    initGrid(rows, cols, false); draw();
  });

  $('pbHeart').addEventListener('click', function(){
    initGrid(rows, cols, false);
    var s = 3.1, c0 = (cols - 1) / 2, r0 = (rows - 1) / 2 + 1.2;
    for(var y = 0; y < rows; y++){
      for(var x = 0; x < cols; x++){
        var dx = (x - c0) / cols * s;
        var dy = -(y - r0) / rows * s;
        var v = Math.pow(dx * dx + dy * dy - 0.32, 3) - dx * dx * Math.pow(dy, 3);
        if(v <= 0) grid[y][x] = '#e63946';
      }
    }
    draw();
  });

  $('pbSave').addEventListener('click', function(){
    try{
      localStorage.setItem('toolbox_perler', JSON.stringify({ r: rows, c: cols, g: grid }));
      $('pbCurName').textContent = '已保存 ✓';
      setTimeout(function(){ $('pbCurName').textContent = '大红'; }, 1500);
    }catch(err){}
  });

  $('pbLoad').addEventListener('click', function(){
    try{
      var data = JSON.parse(localStorage.getItem('toolbox_perler'));
      if(!data || !data.g) return;
      rows = data.r; cols = data.c; grid = data.g;
      $('pbSize').value = String(rows);
      setupCanvas(); draw();
    }catch(err){}
  });

  $('pbExport').addEventListener('click', function(){
    var out = document.createElement('canvas');
    var px = 24;
    out.width = cols * px; out.height = rows * px;
    var c2 = out.getContext('2d');
    c2.fillStyle = '#ffffff'; c2.fillRect(0, 0, out.width, out.height);
    var r = px * 0.42;
    for(var y = 0; y < rows; y++){
      for(var x = 0; x < cols; x++){
        var color = grid[y][x];
        if(!color) continue;
        c2.beginPath();
        c2.arc(x * px + px / 2, y * px + px / 2, r, 0, Math.PI * 2);
        c2.fillStyle = color; c2.fill();
        c2.lineWidth = 1.5; c2.strokeStyle = 'rgba(0,0,0,0.15)'; c2.stroke();
      }
    }
    var a = document.createElement('a');
    a.download = 'perler-' + cols + 'x' + rows + '.png';
    a.href = out.toDataURL('image/png');
    a.click();
  });

  initGrid(rows, cols, false);
  buildPalette();
  setupCanvas();
  draw();
})();
</script>

<div class="explain">
  <h3>玩法 & 怎么用</h3>
  <p class="ex-formula">用豆数 = 图纸上该颜色占据的格子数<br>总豆数 = 所有颜色用量之和（建议多备 5%~10% 损耗）</p>
  <p class="ex-text">拼豆（Perler Beads / 熨斗豆）是把彩色小豆放在带凸点的底板上、用熨斗加热熔合成型的手工。在这里<b>逐格点出图案</b>——可以直接照着喜欢的像素画临摹，也能用「示例：爱心」起手再改造。页面上用的是接近真实市售色号的调色板，画完自动统计<b>每种颜色需要多少颗</b>，照着清单买豆不浪费；「导出 PNG」得到干净的图纸，打印出来铺在底板旁边一格一格对就行。技巧：大面积背景色最后补，熨的时候垫熨斗纸，中温 30 秒左右再压平定型。</p>
</div>
