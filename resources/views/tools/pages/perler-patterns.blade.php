{{-- 工具匣 · 拼豆工坊（Almanac 编辑风，沿用全站设计令牌） --}}
{{-- 功能与交互保持独立实现，仅统一视觉层 --}}
<div class="pbw-app" id="pbwApp">

  <header class="pbw-top">
    <div class="pbw-brand">
      <span class="pbw-logo" aria-hidden="true">▰</span>
      <span class="pbw-brandname">工具匣<i>·</i>拼豆工坊</span>
      <span class="pbw-tag" aria-hidden="true">PERLER STUDIO</span>
    </div>
    <div class="pbw-title-wrap">
      <input class="pbw-tasktitle" id="pbwTaskTitle" value="我的拼豆作品" maxlength="24" spellcheck="false" aria-label="作品标题">
    </div>
    <div class="pbw-actions">
      <button class="pbw-btn pbw-btn-ghost" id="pbwSaveBtn" title="保存到本机">保存</button>
      <div class="pbw-export">
        <button class="pbw-btn pbw-btn-red" id="pbwExportBtn">导出 ▾</button>
        <div class="pbw-export-menu" id="pbwExportMenu">
          <button data-x="png">PNG 图纸</button>
          <button data-x="svg">SVG 矢量</button>
          <button data-x="pdf">PDF 图纸</button>
        </div>
      </div>
    </div>
  </header>

  <div class="pbw-body">
    {{-- 左侧控制面板 --}}
    <aside class="pbw-dock">
      <section class="pbw-card">
        <div class="pbw-card-h"><span><span class="pbw-num">01</span>导入画面</span></div>
        <label class="pbw-upload" id="pbwUploadLabel">
          <input type="file" id="pbwFile" accept="image/*" class="pbw-file">
          <span class="pbw-upload-ico" aria-hidden="true">↑</span>
          <span class="pbw-upload-txt">上传图片转图纸</span>
          <span class="pbw-upload-sub">自动按调色板匹配豆色</span>
        </label>
        <button class="pbw-btn pbw-btn-line pbw-full" id="pbwClearBtn">清空画布</button>
      </section>

      <section class="pbw-card">
        <div class="pbw-card-h"><span><span class="pbw-num">02</span>拼豆板尺寸</span></div>
        <div class="pbw-seg" id="pbwSizeSeg">
          <button data-w="29" data-h="29">29×29</button>
          <button data-w="52" data-h="52" class="on">52×52</button>
          <button data-w="78" data-h="78">78×78</button>
          <button data-w="104" data-h="104">104×104</button>
        </div>
        <div class="pbw-row">
          <label class="pbw-mini">宽 <input type="number" id="pbwW" value="52" min="8" max="200"></label>
          <label class="pbw-mini">高 <input type="number" id="pbwH" value="52" min="8" max="200"></label>
          <button class="pbw-btn pbw-btn-line" id="pbwApplySize">应用</button>
        </div>
      </section>

      <section class="pbw-card">
        <div class="pbw-card-h"><span><span class="pbw-num">03</span>调色板</span><span class="pbw-hint" id="pbwPalName">—</span></div>
        <div class="pbw-pal" id="pbwPal"></div>
        <div class="pbw-row pbw-row-mid">
          <button class="pbw-swatch pbw-sel-none" id="pbwEraser" title="橡皮（留空）">⌫ 橡皮</button>
          <button class="pbw-swatch" id="pbwPicker" title="取色">◉ 取色</button>
        </div>
      </section>

    </aside>

    {{-- 主舞台 --}}
    <main class="pbw-stage">
      <div class="pbw-stage-bar">
        <div class="pbw-viewtabs" id="pbwViewTabs">
          <button data-view="spec" class="on">规格</button>
          <button data-view="pattern">图纸</button>
          <button data-view="assist">辅助拼豆</button>
        </div>
        <div class="pbw-modebar">
          <span class="pbw-mode-label">视角</span>
          <div class="pbw-modeseg" id="pbwModeSeg">
            <div class="pbw-modeseg-btns">
              <button data-mode="flat" class="on">平面</button>
              <button data-mode="3d">3D</button>
              <span class="pbw-knob" id="pbwKnob"></span>
            </div>
          </div>
        </div>
      </div>

      <div class="pbw-canvas-area">
        <div class="pbw-canvas-wrap" id="pbwCanvasWrap">
          <div class="pbw-canvas-inner">
            <canvas id="pbwCanvas" class="pbw-canvas"></canvas>
          </div>
          <div class="pbw-v3" id="pbwV3Wrap"><canvas id="pbwV3"></canvas></div>
        </div>
        <div class="pbw-meltbar" id="pbwMeltbar">
          <button id="pbwMeltPlay">▶ 模拟烫豆</button>
          <input type="range" id="pbwMeltRange" min="0" max="100" value="100" disabled aria-label="烫豆程度">
          <button id="pbwMeltStyle" data-style="flat">烫法：平烫</button>
        </div>
        <div class="pbw-empty" id="pbwEmpty">上传一张图片，或直接在格子上点画开始创作</div>
      </div>

      {{-- 规格视图 --}}
      <div class="pbw-spec" id="pbwSpecView">
        <div class="pbw-spec-grid">
          <div class="pbw-spec-item"><span class="k">板尺寸</span><b id="pbwSpecSize">52 × 52</b></div>
          <div class="pbw-spec-item"><span class="k">总格数</span><b id="pbwSpecCells">2704</b></div>
          <div class="pbw-spec-item"><span class="k">已填格</span><b id="pbwSpecFilled">0</b></div>
          <div class="pbw-spec-item"><span class="k">用色数</span><b id="pbwSpecColors">0</b></div>
          <div class="pbw-spec-item"><span class="k">预估豆量</span><b id="pbwSpecBeads">0</b></div>
          <div class="pbw-spec-item"><span class="k">图层数</span><b id="pbwSpecLayers">1</b></div>
        </div>
        <div class="pbw-spec-pal">
          <div class="pbw-card-h"><span>本图使用色</span></div>
          <div class="pbw-pal" id="pbwSpecPal"></div>
        </div>
      </div>

      {{-- 辅助拼豆视图 --}}
      <div class="pbw-assist" id="pbwAssistView">
        <div class="pbw-assist-bar">
          <span class="pbw-assist-tip">按颜色顺序逐块拼：勾掉已完成的颜色，画布只显示当前颜色区域</span>
          <div class="pbw-assist-nav">
            <button class="pbw-btn pbw-btn-line" id="pbwAssistPrev">← 上一色</button>
            <span id="pbwAssistIdx">1 / 1</span>
            <button class="pbw-btn pbw-btn-line" id="pbwAssistNext">下一色 →</button>
          </div>
        </div>
        <div class="pbw-assist-canvas-wrap" id="pbwAssistWrap">
          <div class="pbw-canvas-inner">
            <canvas id="pbwAssistCanvas" class="pbw-canvas"></canvas>
          </div>
        </div>
      </div>

      {{-- 拼豆进度记录表 --}}
      <section class="pbw-progress">
        <div class="pbw-card-h"><span>拼豆进度记录表</span><span class="pbw-hint" id="pbwProgDone">0 / 0 色完成</span></div>
        <div class="pbw-prog-list" id="pbwProgList"></div>
      </section>
    </main>

    {{-- 右侧工具面板：图层 / 视图 / 缩放（贴近画布，常驻易点） --}}
    <aside class="pbw-dock pbw-dock-right">
      <section class="pbw-card">
        <div class="pbw-card-h"><span><span class="pbw-num">04</span>图层</span></div>
        <div class="pbw-layers" id="pbwLayers"></div>
        <button class="pbw-btn pbw-btn-line pbw-full" id="pbwAddLayer">+ 新建图层</button>
      </section>

      <section class="pbw-card">
        <div class="pbw-card-h"><span><span class="pbw-num">05</span>视图</span></div>
        <div class="pbw-seg pbw-seg-col" id="pbwViewSeg">
          <button data-view="spec" class="on">规格总览</button>
          <button data-view="pattern">图纸编辑</button>
          <button data-view="assist">辅助拼豆</button>
        </div>
      </section>

      <section class="pbw-card">
        <div class="pbw-card-h"><span><span class="pbw-num">06</span>缩放</span></div>
        <div class="pbw-zoombox">
          <button class="pbw-btn pbw-btn-line pbw-zoombtn" id="pbwZoomOut" aria-label="缩小">－</button>
          <span class="pbw-zoom" id="pbwZoomLabel">100%</span>
          <button class="pbw-btn pbw-btn-line pbw-zoombtn" id="pbwZoomIn" aria-label="放大">＋</button>
        </div>
      </section>
    </aside>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
(function(){
  "use strict";
  // ============ 设计系统（Almanac：纸面 / 墨色 / 印章红，见 public/css/tokens.css） ============
  var CSS_VARS_LOADED = true; // 见下方 <style>

  // ============ 调色板：Perler 风格标准豆色（名称 + hex） ============
  var PALETTE = [
    {n:"白",      v:"#FFFFFF"}, {n:"骨白",    v:"#FDFBFF"}, {n:"米白",   v:"#FFE2CE"},
    {n:"浅黄",    v:"#FBED56"}, {n:"鹅黄",    v:"#FBE68A"}, {n:"姜黄",   v:"#EDB045"},
    {n:"橙",      v:"#FE8B4C"}, {n:"深橙",    v:"#FE6A2C"}, {n:"红",     v:"#E7002F"},
    {n:"深红",    v:"#C8323F"}, {n:"玫红",    v:"#E8649E"}, {n:"粉",     v:"#FEC0DF"},
    {n:"浅粉",    v:"#FBD3E6"}, {n:"紫",      v:"#8854B3"}, {n:"浅紫",   v:"#D5B9F8"},
    {n:"靛",      v:"#5B6BBA"}, {n:"蓝",      v:"#3E6FD0"}, {n:"天蓝",   v:"#A0E2FB"},
    {n:"青",      v:"#00C8C8"}, {n:"青绿",    v:"#3FD6A6"}, {n:"绿",     v:"#54B948"},
    {n:"浅绿",    v:"#9EF780"}, {n:"橄榄",    v:"#9D5B3E"}, {n:"棕",     v:"#8A5A2B"},
    {n:"深棕",    v:"#5C3A1E"}, {n:"灰",      v:"#9B9B9B"}, {n:"深灰",   v:"#5C5C5C"},
    {n:"黑",      v:"#1B1A19"}, {n:"银",      v:"#B6B1BA"}, {n:"炭",     v:"#48464E"},
    {n:"玫瑰金",  v:"#FFB6A8"}, {n:"薄荷",    v:"#BFF0E0"}, {n:"雾蓝",   v:"#8FA3B3"},
    {n:"丁香",    v:"#C9B6E8"}, {n:"南瓜",    v:"#FEAC4C"}, {n:"番茄",   v:"#FF5A4D"}
  ];
  function palByName(name){ for(var i=0;i<PALETTE.length;i++) if(PALETTE[i].n===name) return PALETTE[i].v; return "#FFFFFF"; }

  // ============ 状态 ============
  var state = {
    w:52, h:52,
    zoom:14,            // 每格像素
    layers:[],          // [{visible:true, locked:false, cells:Array(w*h) of color|null}]
    active:0,
    curColor: PALETTE[10].v,  // 当前笔色（默认红）
    eraser:false, picker:false,
    view:"pattern", mode:"flat",
    history:[], future:[],
    melt:100, meltStyle:"flat",
    assistIdx:0
  };
  function blankLayer(){ var a=new Array(state.w*state.h); for(var i=0;i<a.length;i++) a[i]=null; return a; }
  function initLayers(){ state.layers=[{visible:true,locked:false,cells:blankLayer()}]; state.active=0; }

  // ============ 颜色工具 ============
  function hexToRgb(h){ h=h.replace('#',''); if(h.length===3) h=h[0]+h[0]+h[1]+h[1]+h[2]+h[2]; return [parseInt(h.substr(0,2),16),parseInt(h.substr(2,2),16),parseInt(h.substr(4,2),16)]; }
  function rgbToHex(r,g,b){ function t(x){var s=Math.max(0,Math.min(255,Math.round(x))).toString(16); return s.length<2?'0'+s:s;} return '#'+t(r)+t(g)+t(b); }
  function lighten(h,f){ var c=hexToRgb(h); return rgbToHex(c[0]+(255-c[0])*f, c[1]+(255-c[1])*f, c[2]+(255-c[2])*f); }
  function darken(h,f){ var c=hexToRgb(h); return rgbToHex(c[0]*(1-f), c[1]*(1-f), c[2]*(1-f)); }
  function nearestColor(r,g,b){
    var best=null, bd=1e9;
    for(var i=0;i<PALETTE.length;i++){
      var c=hexToRgb(PALETTE[i].v);
      var d=(c[0]-r)*(c[0]-r)+(c[1]-g)*(c[1]-g)+(c[2]-b)*(c[2]-b);
      if(d<bd){bd=d; best=PALETTE[i].v;}
    }
    return best;
  }

  // ============ 画布：方形像素块（对齐参考「拼豆专家」2D 默认视图） ============
  var canvas=document.getElementById('pbwCanvas');
  var ctx=canvas.getContext('2d');
  var assistCanvas=document.getElementById('pbwAssistCanvas');
  var actx=assistCanvas.getContext('2d');

  // 板面纸色与 --pbw-board 同步：深色主题下压暗，避免大面积高亮
  var BOARD_FILL={ light:'#F5F2EA', dark:'#E2DCD0' };
  function boardFill(){ return document.documentElement.getAttribute('data-theme')==='dark' ? BOARD_FILL.dark : BOARD_FILL.light; }

  // 单颗豆的画法：对齐参考「拼豆专家」2D 默认视图
  //  - 方形像素块（非圆豆，无高光、无中心孔）
  //  - 默认「未烫」：方块之间留缝，像一颗颗摆在板上（meltRatio=1）
  //  - 「烫豆」：缝隙闭合、豆子融合成片（meltRatio→0，由 模拟烫豆 驱动）
  //  - 中心孔只在辅助拼豆 / 烫豆成品视角才出现（此处不做）
  function drawBead(c, px, py, s, color, meltRatio){
    meltRatio = (meltRatio==null)?1:meltRatio; // 1=未烫(分离), 0=烫平(融合)
    // 方形像素块：未烫时与邻格留缝，烫平后缝隙闭合
    var gap = meltRatio * Math.max(0, s*0.12);
    c.fillStyle=color;
    c.fillRect(px+gap, py+gap, s-gap*2, s-gap*2);
  }

  function mergedCell(x,y){
    for(var i=state.layers.length-1;i>=0;i--){
      var L=state.layers[i];
      if(!L.visible) continue;
      var v=L.cells[y*state.w+x];
      if(v) return v;
    }
    return null;
  }

  function render(){
    var s=state.zoom;
    var W=state.w*s, H=state.h*s;
    canvas.width=W; canvas.height=H;
    ctx.clearRect(0,0,W,H);
    // 纸张底
    ctx.fillStyle=boardFill(); ctx.fillRect(0,0,W,H);
    // 豆（方形像素块）/ 底板钉柱
    var any=false;
    for(var y=0;y<state.h;y++) for(var x=0;x<state.w;x++){
      var v=mergedCell(x,y);
      if(v){ drawBead(ctx, x*s, y*s, s, v, state.melt/100); any=true; }
      else if(s>=9 && state.melt>=100){ // 未烫：空位显示底板钉柱小方块（参考 2D 视图）
        var ps=Math.max(2,s*0.16);
        ctx.fillStyle='rgba(15,25,35,0.10)';
        ctx.fillRect(x*s+s/2-ps/2, y*s+s/2-ps/2, ps, ps);
      }
    }
    // 格线（叠在豆上，呈现像素缝隙）
    if(s>=5){
      ctx.strokeStyle='rgba(15,25,35,0.16)'; ctx.lineWidth=1;
      for(var x2=0;x2<=state.w;x2++){ ctx.beginPath(); ctx.moveTo(x2*s+0.5,0); ctx.lineTo(x2*s+0.5,H); ctx.stroke(); }
      for(var y2=0;y2<=state.h;y2++){ ctx.beginPath(); ctx.moveTo(0,y2*s+0.5); ctx.lineTo(W,y2*s+0.5); ctx.stroke(); }
    }
    document.getElementById('pbwEmpty').style.display = any? 'none':'flex';
    syncSpec(); syncProgress(); buildSpecPalette(); syncLayerCounts();
  }

  function syncLayerCounts(){
    var rows=document.querySelectorAll('#pbwLayers .pbw-layer');
    if(rows.length!==state.layers.length) return;
    state.layers.forEach(function(L,i){
      var cnt=rows[i]&&rows[i].querySelector('.pbw-layer-cnt');
      if(!cnt) return;
      var c=0; for(var k=0;k<L.cells.length;k++) if(L.cells[k]) c++;
      cnt.textContent=c;
    });
  }

  function fitZoom(){
    var wrap=document.getElementById('pbwCanvasWrap');
    var avail=(wrap.clientWidth||0)-2;
    if(avail<=0) return;
    state.zoom=Math.max(4,Math.min(14,Math.floor(avail/state.w)));
    document.getElementById('pbwZoomLabel').textContent=Math.round(state.zoom/14*100)+'%';
  }

  // ============ 辅助拼豆渲染 ============
  function distinctColors(){
    var set={};
    for(var y=0;y<state.h;y++) for(var x=0;x<state.w;x++){ var v=mergedCell(x,y); if(v) set[v]=1; }
    return Object.keys(set);
  }
  function renderAssist(){
    var s=state.zoom;
    var W=state.w*s, H=state.h*s;
    assistCanvas.width=W; assistCanvas.height=H;
    actx.clearRect(0,0,W,H);
    actx.fillStyle=boardFill(); actx.fillRect(0,0,W,H);
    var cols=distinctColors();
    var activeColor = cols.length? cols[Math.min(state.assistIdx, cols.length-1)] : null;
    document.getElementById('pbwAssistIdx').textContent = cols.length? (Math.min(state.assistIdx,cols.length-1)+1)+' / '+cols.length : '0 / 0';
    for(var y=0;y<state.h;y++) for(var x=0;x<state.w;x++){
      var v=mergedCell(x,y);
      if(!v) continue;
      if(v===activeColor){ drawBead(actx, x*s, y*s, s, v, 1); }
      else { // 其他颜色画灰底小方块，弱化
        var ps=s*0.36;
        actx.fillStyle='rgba(120,120,120,0.12)';
        actx.fillRect(x*s+s/2-ps/2, y*s+s/2-ps/2, ps, ps);
      }
    }
  }

  // ============ 规格 / 进度 ============
  function countStats(){
    var filled=0, colors={}, total=state.w*state.h;
    for(var y=0;y<state.h;y++) for(var x=0;x<state.w;x++){ var v=mergedCell(x,y); if(v){ filled++; colors[v]=(colors[v]||0)+1; } }
    return {filled:filled, total:total, colors:colors, nColors:Object.keys(colors).length};
  }
  function syncSpec(){
    var st=countStats();
    document.getElementById('pbwSpecSize').textContent=state.w+' × '+state.h;
    document.getElementById('pbwSpecCells').textContent=st.total;
    document.getElementById('pbwSpecFilled').textContent=st.filled;
    document.getElementById('pbwSpecColors').textContent=st.nColors;
    document.getElementById('pbwSpecBeads').textContent=st.filled;
    document.getElementById('pbwSpecLayers').textContent=state.layers.length;
  }
  function buildSpecPalette(){
    var st=countStats(); var keys=Object.keys(st.colors);
    var box=document.getElementById('pbwSpecPal'); box.innerHTML='';
    keys.sort(function(a,b){return st.colors[b]-st.colors[a];});
    keys.forEach(function(k){
      var d=document.createElement('div'); d.className='pbw-swatch'; d.style.background=k;
      d.title=st.colors[k]+' 颗'; box.appendChild(d);
    });
  }
  function syncProgress(){
    var st=countStats(); var keys=Object.keys(st.colors);
    keys.sort(function(a,b){return st.colors[b]-st.colors[a];});
    var box=document.getElementById('pbwProgList'); box.innerHTML='';
    var done=0;
    keys.forEach(function(k,idx){
      var row=document.createElement('label'); row.className='pbw-prog-row';
      var cb=document.createElement('input'); cb.type='checkbox'; cb.checked=false;
      cb.addEventListener('change', function(){ row.classList.toggle('done', cb.checked); refreshProgDone(); });
      var sw=document.createElement('span'); sw.className='pbw-prog-sw'; sw.style.background=k;
      var nm=document.createElement('span'); nm.className='pbw-prog-name'; nm.textContent=colorName(k);
      var ct=document.createElement('span'); ct.className='pbw-prog-ct'; ct.textContent=st.colors[k]+' 颗';
      row.appendChild(cb); row.appendChild(sw); row.appendChild(nm); row.appendChild(ct);
      box.appendChild(row);
    });
    document.getElementById('pbwProgDone').textContent='0 / '+keys.length+' 色完成';
  }
  function refreshProgDone(){
    var rows=document.querySelectorAll('#pbwProgList .pbw-prog-row');
    var done=0; rows.forEach(function(r){ if(r.querySelector('input').checked) done++; });
    document.getElementById('pbwProgDone').textContent=done+' / '+rows.length+' 色完成';
  }
  function colorName(hex){
    for(var i=0;i<PALETTE.length;i++) if(PALETTE[i].v.toLowerCase()===hex.toLowerCase()) return PALETTE[i].n;
    return hex;
  }

  // ============ 调色板 UI ============
  function buildPalette(){
    var box=document.getElementById('pbwPal'); box.innerHTML='';
    PALETTE.forEach(function(p){
      var d=document.createElement('button'); d.className='pbw-swatch'; d.type='button'; d.style.background=p.v;
      d.title=p.n+' '+p.v; d.setAttribute('aria-label', p.n);
      d.addEventListener('click', function(){
        state.eraser=false; state.picker=false; state.curColor=p.v;
        document.querySelectorAll('#pbwPal .pbw-swatch').forEach(function(e){e.classList.remove('on');});
        d.classList.add('on');
        document.getElementById('pbwPalName').textContent=p.n;
        document.getElementById('pbwEraser').classList.remove('on');
      });
      box.appendChild(d);
    });
    // 默认选中红
    var def=box.children[10]; if(def) def.classList.add('on');
  }

  // ============ 图层 UI ============
  function buildLayers(){
    var box=document.getElementById('pbwLayers'); box.innerHTML='';
    state.layers.forEach(function(L,i){
      var row=document.createElement('div'); row.className='pbw-layer'+(i===state.active?' on':'');
      var vis=document.createElement('button'); vis.className='pbw-layer-vis'+(L.visible?' on':''); vis.type='button';
      vis.textContent=L.visible?'●':'○';
      vis.title=L.visible?'隐藏图层':'显示图层';
      vis.setAttribute('aria-label', vis.title);
      vis.addEventListener('click', function(e){ e.stopPropagation(); L.visible=!L.visible; vis.textContent=L.visible?'●':'○'; vis.title=L.visible?'隐藏图层':'显示图层'; vis.setAttribute('aria-label', vis.title); vis.classList.toggle('on',L.visible); render(); if(state.view==='assist') renderAssist(); });
      var nm=document.createElement('span'); nm.className='pbw-layer-name'; nm.textContent='图层 '+(i+1);
      var cnt=document.createElement('span'); cnt.className='pbw-layer-cnt';
      var c=0; for(var k=0;k<L.cells.length;k++) if(L.cells[k]) c++;
      cnt.textContent=c;
      row.appendChild(vis); row.appendChild(nm); row.appendChild(cnt);
      row.addEventListener('click', function(){ state.active=i; buildLayers(); });
      box.appendChild(row);
    });
  }

  // ============ 编辑 ============
  function snapshot(){ state.history.push(JSON.stringify(state.layers.map(function(L){return L.cells;}))); if(state.history.length>60) state.history.shift(); state.future=[]; }
  function applyAt(px,py,val){
    var L=state.layers[state.active];
    if(!L||L.locked) return;
    L.cells[py*state.w+px]=val;
  }
  function canvasToCell(ev){
    var rect=canvas.getBoundingClientRect();
    var x=Math.floor((ev.clientX-rect.left)/(rect.width/state.w));
    var y=Math.floor((ev.clientY-rect.top)/(rect.height/state.h));
    if(x<0||y<0||x>=state.w||y>=state.h) return null;
    return {x:x,y:y};
  }
  var drawing=false;
  canvas.addEventListener('mousedown', function(ev){
    var c=canvasToCell(ev); if(!c) return;
    if(state.picker){
      var v=mergedCell(c.x,c.y);
      if(v){
        state.curColor=v; state.picker=false;
        document.getElementById('pbwPicker').classList.remove('on');
        buildPalette();
        var sw=[].slice.call(document.querySelectorAll('#pbwPal .pbw-swatch')).filter(function(e){
          return e.style.background && rgbEq(e.style.background, v);
        })[0];
        if(sw){
          document.querySelectorAll('#pbwPal .pbw-swatch').forEach(function(e){ e.classList.remove('on'); });
          sw.classList.add('on');
          document.getElementById('pbwPalName').textContent=colorName(v);
        }
        render();
        return;
      }
    }
    snapshot();
    drawing=true;
    applyAt(c.x,c.y, state.eraser?null:state.curColor);
    render(); if(state.view==='assist') renderAssist();
  });
  window.addEventListener('mouseup', function(){ drawing=false; });
  canvas.addEventListener('mousemove', function(ev){
    if(!drawing) return;
    var c=canvasToCell(ev); if(!c) return;
    applyAt(c.x,c.y, state.eraser?null:state.curColor);
    render(); if(state.view==='assist') renderAssist();
  });
  function rgbEq(a,b){ return a.replace(/\s/g,'').toLowerCase()===b.toLowerCase(); }

  // ============ 上传转图纸 ============
  document.getElementById('pbwFile').addEventListener('change', function(e){
    var f=e.target.files[0]; if(!f) return;
    var img=new Image();
    img.onload=function(){
      snapshot();
      var w=state.w, h=state.h;
      var off=document.createElement('canvas'); off.width=w; off.height=h;
      var o=off.getContext('2d');
      var scale=Math.max(w/img.width, h/img.height);
      var dw=img.width*scale, dh=img.height*scale;
      o.fillStyle='#FFFFFF'; o.fillRect(0,0,w,h);
      o.drawImage(img,(w-dw)/2,(h-dh)/2,dw,dh);
      var data=o.getImageData(0,0,w,h).data;
      var L=state.layers[state.active];
      for(var y=0;y<h;y++) for(var x=0;x<w;x++){
        var i=(y*w+x)*4;
        var a=data[i+3];
        L.cells[y*w+x] = a<20? null : nearestColor(data[i],data[i+1],data[i+2]);
      }
      render(); if(state.view==='assist') renderAssist();
      e.target.value='';
    };
    img.src=URL.createObjectURL(f);
  });

  document.getElementById('pbwClearBtn').addEventListener('click', function(){
    snapshot();
    state.layers[state.active].cells=blankLayer();
    render(); if(state.view==='assist') renderAssist();
  });

  // ============ 尺寸 ============
  document.getElementById('pbwSizeSeg').addEventListener('click', function(e){
    var b=e.target.closest('button'); if(!b) return;
    document.querySelectorAll('#pbwSizeSeg button').forEach(function(x){x.classList.remove('on');});
    b.classList.add('on');
    state.w=+b.dataset.w; state.h=+b.dataset.h;
    document.getElementById('pbwW').value=state.w; document.getElementById('pbwH').value=state.h;
    snapshot(); initLayers(); buildLayers(); fitZoom(); render(); if(state.view==='assist') renderAssist();
  });
  document.getElementById('pbwApplySize').addEventListener('click', function(){
    var w=Math.max(8,Math.min(200,+document.getElementById('pbwW').value||52));
    var h=Math.max(8,Math.min(200,+document.getElementById('pbwH').value||52));
    state.w=w; state.h=h;
    document.querySelectorAll('#pbwSizeSeg button').forEach(function(x){x.classList.remove('on');});
    snapshot(); initLayers(); buildLayers(); fitZoom(); render(); if(state.view==='assist') renderAssist();
  });

  // ============ 缩放 ============
  document.getElementById('pbwZoomIn').addEventListener('click', function(){ state.zoom=Math.min(40,state.zoom+2); document.getElementById('pbwZoomLabel').textContent=Math.round(state.zoom/14*100)+'%'; render(); if(state.view==='assist') renderAssist(); });
  document.getElementById('pbwZoomOut').addEventListener('click', function(){ state.zoom=Math.max(4,state.zoom-2); document.getElementById('pbwZoomLabel').textContent=Math.round(state.zoom/14*100)+'%'; render(); if(state.view==='assist') renderAssist(); });

  // ============ 橡皮 / 取色 ============
  document.getElementById('pbwEraser').addEventListener('click', function(){
    state.eraser=!state.eraser; state.picker=false;
    this.classList.toggle('on',state.eraser);
    document.getElementById('pbwPicker').classList.remove('on');
  });
  document.getElementById('pbwPicker').addEventListener('click', function(){
    state.picker=!state.picker; state.eraser=false;
    this.classList.toggle('on',state.picker);
    document.getElementById('pbwEraser').classList.remove('on');
  });

  // ============ 图层操作 ============
  document.getElementById('pbwAddLayer').addEventListener('click', function(){
    state.layers.push({visible:true,locked:false,cells:blankLayer()});
    state.active=state.layers.length-1; buildLayers(); render(); if(state.view==='assist') renderAssist();
  });

  // ============ 视图切换 ============
  function setView(v){
    state.view=v;
    document.querySelectorAll('#pbwViewSeg button, #pbwViewTabs button').forEach(function(b){ b.classList.toggle('on', b.dataset.view===v); });
    document.getElementById('pbwSpecView').style.display = v==='spec'?'block':'none';
    document.getElementById('pbwAssistView').style.display = v==='assist'?'block':'none';
    document.getElementById('pbwCanvasWrap').style.display = (v==='pattern')?'block':'none';
    document.getElementById('pbwV3Wrap').style.display = (v==='pattern'&&state.mode==='3d')?'block':'none';
    document.getElementById('pbwMeltbar').style.display = (v==='pattern')?'flex':'none';
    if(v!=='pattern') document.getElementById('pbwEmpty').style.display='none';
    if(v==='assist') renderAssist();
    if(v==='pattern'){ render(); }
  }
  document.getElementById('pbwViewSeg').addEventListener('click', function(e){ if(e.target.dataset.view) setView(e.target.dataset.view); });
  document.getElementById('pbwViewTabs').addEventListener('click', function(e){ if(e.target.dataset.view) setView(e.target.dataset.view); });

  // ============ 视角：平面 / 3D ============
  document.getElementById('pbwModeSeg').addEventListener('click', function(e){
    var b=e.target.closest('button'); if(!b) return;
    state.mode=b.dataset.mode;
    document.querySelectorAll('#pbwModeSeg button').forEach(function(x){x.classList.toggle('on',x===b);});
    var knob=document.getElementById('pbwKnob');
    knob.style.transform = state.mode==='3d'? 'translateX(100%)': 'translateX(0)';
    document.getElementById('pbwV3Wrap').style.display = (state.view==='pattern'&&state.mode==='3d')?'block':'none';
    document.getElementById('pbwCanvas').style.display = state.mode==='3d'?'none':'block';
    if(state.mode==='3d') initV3(); else stopV3();
  });

  // ============ 烫豆 ============
  var meltPlaying=false;
  document.getElementById('pbwMeltPlay').addEventListener('click', function(){
    if(state.mode!=='3d'){ // 2D 下用滑条控制凸起比例
      var r=document.getElementById('pbwMeltRange'); r.disabled=false;
      meltPlaying=!meltPlaying; this.textContent= meltPlaying? '⏸ 暂停':'▶ 模拟烫豆';
      if(meltPlaying) meltTick();
    }
  });
  function meltTick(){
    if(!meltPlaying) return;
    var r=document.getElementById('pbwMeltRange');
    var v=+r.value - 2; if(v<0){v=0; meltPlaying=false; document.getElementById('pbwMeltPlay').textContent='▶ 模拟烫豆';}
    r.value=v; state.melt=v; render();
    if(meltPlaying) requestAnimationFrame(meltTick);
  }
  document.getElementById('pbwMeltRange').addEventListener('input', function(){ state.melt=+this.value; render(); });
  document.getElementById('pbwMeltStyle').addEventListener('click', function(){
    state.meltStyle = state.meltStyle==='flat'?'iron':'flat';
    this.textContent = '烫法：'+(state.meltStyle==='flat'?'平烫':'熨烫');
  });

  // ============ 辅助拼豆导航 ============
  document.getElementById('pbwAssistPrev').addEventListener('click', function(){ var c=distinctColors().length; state.assistIdx=(state.assistIdx-1+c)%c; renderAssist(); });
  document.getElementById('pbwAssistNext').addEventListener('click', function(){ var c=distinctColors().length; state.assistIdx=(state.assistIdx+1)%c; renderAssist(); });

  // ============ 导出 ============
  document.getElementById('pbwExportBtn').addEventListener('click', function(){
    document.getElementById('pbwExportMenu').classList.toggle('show');
  });
  document.getElementById('pbwExportMenu').addEventListener('click', function(e){
    var x=e.target.dataset.x; if(!x) return;
    document.getElementById('pbwExportMenu').classList.remove('show');
    if(x==='png') exportPNG();
    else if(x==='svg') exportSVG();
    else if(x==='pdf') exportPDF();
  });
  function download(href, name){ var a=document.createElement('a'); a.href=href; a.download=name; document.body.appendChild(a); a.click(); a.remove(); }
  function exportPNG(){
    var prev=state.zoom; state.zoom=Math.max(state.zoom,14); render();
    var title=document.getElementById('pbwTaskTitle').value||'perler';
    download(canvas.toDataURL('image/png'), title+'.png');
    state.zoom=prev; render();
  }
  function exportSVG(){
    var s=Math.max(state.zoom,14), W=state.w*s, H=state.h*s;
    var svg='<svg xmlns="http://www.w3.org/2000/svg" width="'+W+'" height="'+H+'" viewBox="0 0 '+W+' '+H+'">';
    svg+='<rect width="'+W+'" height="'+H+'" fill="#F5F2EA"/>';
    for(var y=0;y<state.h;y++) for(var x=0;x<state.w;x++){
      var v=mergedCell(x,y); if(!v) continue;
      svg+='<rect x="'+(x*s)+'" y="'+(y*s)+'" width="'+s+'" height="'+s+'" fill="'+v+'"/>';
    }
    svg+='</svg>';
    download('data:image/svg+xml;charset=utf-8,'+encodeURIComponent(svg), (document.getElementById('pbwTaskTitle').value||'perler')+'.svg');
  }
  function exportPDF(){
    if(!window.jspdf || !window.jspdf.jsPDF){ alert('PDF 组件未加载，请稍后重试或改用 PNG/SVG'); return; }
    var prev=state.zoom; state.zoom=Math.max(state.zoom,14); render();
    var title=document.getElementById('pbwTaskTitle').value||'perler';
    var jsPDF=window.jspdf.jsPDF;
    var pdf=new jsPDF({unit:'px', format:[canvas.width, canvas.height]});
    pdf.addImage(canvas.toDataURL('image/png'),'PNG',0,0,canvas.width,canvas.height);
    pdf.save(title+'.pdf');
    state.zoom=prev; render();
  }

  // ============ 保存（localStorage） ============
  document.getElementById('pbwSaveBtn').addEventListener('click', function(){
    try{
      var data={w:state.w,h:state.h,zoom:state.zoom,layers:state.layers,title:document.getElementById('pbwTaskTitle').value};
      localStorage.setItem('pbw_'+titleKey(), JSON.stringify(data));
      flashSave(this,'已保存');
    }catch(e){ alert('保存失败：'+e.message); }
  });
  function titleKey(){ return (document.getElementById('pbwTaskTitle').value||'default').replace(/\s+/g,'_'); }
  function flashSave(btn,txt){ var o=btn.textContent; btn.textContent=txt; setTimeout(function(){btn.textContent=o;},1200); }
  function loadSave(){
    try{
      var raw=localStorage.getItem('pbw_'+titleKey());
      if(!raw) return;
      var d=JSON.parse(raw);
      state.w=d.w; state.h=d.h; state.zoom=d.zoom||14; state.layers=d.layers; state.active=0;
      document.getElementById('pbwW').value=state.w; document.getElementById('pbwH').value=state.h;
      buildLayers(); render();
    }catch(e){}
  }

  // ============ 3D（three.js，自写轨道旋转） ============
  var v3={raf:null, scene:null, cam:null, renderer:null, mesh:null, theta:0.7, phi:0.9, dist:400, dragging:false, lx:0, ly:0};
  function initV3(){
    if(typeof THREE==='undefined'){ document.getElementById('pbwV3Wrap').innerHTML='<div style="color:var(--color-ink-3);padding:2rem">3D 组件未加载（需联网加载 three.js）</div>'; return; }
    var wrap=document.getElementById('pbwV3Wrap');
    var W=wrap.clientWidth||800, H=wrap.clientHeight||600;
    var renderer=new THREE.WebGLRenderer({canvas:document.getElementById('pbwV3'), antialias:true});
    renderer.setSize(W,H,false); renderer.setClearColor(parseInt(boardFill().slice(1),16),1);
    var scene=new THREE.Scene();
    var cam=new THREE.PerspectiveCamera(45, W/H, 1, 5000);
    var group=new THREE.Group(); scene.add(group);
    var step=10; // 豆间距
    var beads=[];
    for(var y=0;y<state.h;y++) for(var x=0;x<state.w;x++){
      var v=mergedCell(x,y); if(!v) continue;
      var geo=new THREE.CylinderGeometry(step*0.46, step*0.46, step*0.5, 20);
      var mat=new THREE.MeshStandardMaterial({color:new THREE.Color(v), roughness:0.55, metalness:0.05});
      var m=new THREE.Mesh(geo,mat);
      m.position.set((x-state.w/2)*step, 0, (y-state.h/2)*step);
      group.add(m); beads.push(m);
    }
    // 板底
    var baseGeo=new THREE.BoxGeometry(state.w*step, 2, state.h*step);
    var baseMat=new THREE.MeshStandardMaterial({color:0x2B2621, roughness:0.9});
    var base=new THREE.Mesh(baseGeo,baseMat); base.position.y=-step*0.4; group.add(base);
    var light=new THREE.DirectionalLight(0xffffff,0.95); light.position.set(200,400,300); scene.add(light);
    scene.add(new THREE.AmbientLight(0x99aabb,0.6));
    v3.scene=scene; v3.cam=cam; v3.renderer=renderer; v3.group=group; v3.beads=beads; v3.step=step;
    v3.canvas=document.getElementById('pbwV3');
    v3.canvas.addEventListener('mousedown', function(e){ v3.dragging=true; v3.lx=e.clientX; v3.ly=e.clientY; });
    window.addEventListener('mouseup', function(){ v3.dragging=false; });
    window.addEventListener('mousemove', function(e){
      if(!v3.dragging) return;
      v3.theta += (e.clientX-v3.lx)*0.01; v3.phi += (e.clientY-v3.ly)*0.01;
      v3.phi=Math.max(0.2,Math.min(1.5,v3.phi)); v3.lx=e.clientX; v3.ly=e.clientY;
    });
    document.getElementById('pbwV3').addEventListener('wheel', function(e){ e.preventDefault(); v3.dist=Math.max(120,Math.min(1200,v3.dist+e.deltaY*0.3)); }, {passive:false});
    stopV3(); v3.raf=requestAnimationFrame(v3Loop);
  }
  function v3Loop(){
    if(!v3.scene) return;
    var r=v3.dist;
    v3.cam.position.set(r*Math.sin(v3.phi)*Math.cos(v3.theta), r*Math.cos(v3.phi), r*Math.sin(v3.phi)*Math.sin(v3.theta));
    v3.cam.lookAt(0,0,0);
    v3.renderer.render(v3.scene, v3.cam);
    v3.raf=requestAnimationFrame(v3Loop);
  }
  function stopV3(){ if(v3.raf){ cancelAnimationFrame(v3.raf); v3.raf=null; } }

  // ============ 初始化 ============
  function initApp(){
    buildPalette(); initLayers(); buildLayers(); fitZoom(); render();
    setView('pattern');
    loadSave();
  }
  if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', initApp); }
  else { initApp(); }
  // 主题切换时重绘板面（canvas 的纸色不随 CSS 变量自动更新）
  if(window.MutationObserver){
    new MutationObserver(function(){
      render();
      if(state.view==='assist') renderAssist();
      if(v3 && v3.renderer) v3.renderer.setClearColor(parseInt(boardFill().slice(1),16),1);
    }).observe(document.documentElement,{attributes:true,attributeFilter:['data-theme']});
  }
  document.getElementById('pbwTaskTitle').addEventListener('change', function(){ /* 切换标题即视为新作品，不自动载入，避免覆盖 */ });
})();
</script>

<style>
/* 拼豆工坊 · Almanac 编辑风：沿用 design.md 令牌，仅在本页定义板面纸色 */
.pbw-app{
  --dockw:264px; --pbw-board:#f5f2ea; --pbw-board-ink:oklch(43% 0.012 70);
  font-family:var(--font-body); color:var(--color-ink); background:var(--color-paper);
  border:var(--rule-hair) solid var(--color-rule); min-width:0; min-height:72vh;
}
[data-theme="dark"] .pbw-app{ --pbw-board:#e2dcd0; }
.pbw-app *{ box-sizing:border-box; }
.pbw-app :focus-visible{ outline:2px solid var(--color-focus); outline-offset:2px; }

.pbw-top{ display:flex; align-items:center; gap:var(--space-md); padding:var(--space-sm) var(--space-md); background:var(--color-paper-2); border-bottom:var(--rule-hair) solid var(--color-rule); }
.pbw-brand{ display:flex; align-items:center; gap:var(--space-xs); min-width:0; }
.pbw-logo{ color:var(--color-accent); font-family:var(--font-outlier); font-size:15px; line-height:1; }
.pbw-brandname{ font-family:var(--font-display); font-weight:700; font-size:var(--text-md); letter-spacing:.01em; white-space:nowrap; }
.pbw-brandname i{ color:var(--color-rule-strong); font-style:normal; margin:0 2px; }
.pbw-tag{ font-family:var(--font-outlier); font-size:10px; letter-spacing:.18em; color:var(--color-ink-3); white-space:nowrap; }
.pbw-title-wrap{ flex:1; display:flex; justify-content:center; min-width:0; }
.pbw-tasktitle{ width:min(340px,50%); padding:4px 8px; background:transparent; border:0; border-bottom:var(--rule-hair) solid var(--color-rule-strong); border-radius:0; color:var(--color-ink); font-family:var(--font-display); font-weight:600; font-size:var(--text-md); text-align:center; letter-spacing:.01em; }
.pbw-tasktitle:hover{ border-bottom-color:var(--color-ink-3); }
.pbw-tasktitle:focus{ outline:2px solid var(--color-focus); outline-offset:3px; border-bottom-color:var(--color-accent); }
.pbw-actions{ display:flex; align-items:center; gap:var(--space-xs); }

.pbw-btn{ display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:7px 14px; min-height:34px; border:var(--rule-hair) solid var(--color-rule-strong); border-radius:var(--radius-sm); background:transparent; color:var(--color-ink); font-family:var(--font-body); font-weight:500; font-size:var(--text-sm); line-height:1; letter-spacing:.01em; white-space:nowrap; cursor:pointer; transition:color var(--dur-micro) var(--ease-out),background-color var(--dur-micro) var(--ease-out),border-color var(--dur-micro) var(--ease-out),transform var(--dur-micro) var(--ease-out); }
.pbw-btn:hover{ color:var(--color-accent); border-color:var(--color-accent); }
.pbw-btn:active{ transform:translateY(1px); }
.pbw-btn-red{ background:var(--color-accent); border-color:var(--color-accent); color:var(--color-accent-ink); }
.pbw-btn-red:hover{ background:var(--color-accent-deep); border-color:var(--color-accent-deep); color:var(--color-accent-ink); }
.pbw-btn-ghost{ border-color:var(--color-rule); }
.pbw-btn-line{ border-color:var(--color-rule); color:var(--color-ink-2); }
.pbw-btn-line:hover{ color:var(--color-accent); border-color:var(--color-accent); }
.pbw-full{ width:100%; }

.pbw-export{ position:relative; }
.pbw-export-menu{ position:absolute; right:0; top:calc(100% + 6px); z-index:var(--z-dropdown); display:none; flex-direction:column; min-width:150px; padding:4px; background:var(--color-paper); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-md); box-shadow:var(--shadow-whisper); }
.pbw-export-menu.show{ display:flex; }
.pbw-export-menu button{ padding:8px 10px; background:transparent; border:0; border-radius:var(--radius-sm); color:var(--color-ink-2); font-family:var(--font-body); font-size:var(--text-sm); text-align:left; cursor:pointer; }
.pbw-export-menu button:hover{ background:var(--color-paper-2); color:var(--color-accent); }

.pbw-body{ display:grid; grid-template-columns:var(--dockw) minmax(0,1fr) var(--dockw); min-height:64vh; min-width:0; }
.pbw-dock{ display:flex; flex-direction:column; gap:0; padding:var(--space-md); background:var(--color-paper-2); border-right:var(--rule-hair) solid var(--color-rule); overflow:auto; min-width:0; }
.pbw-dock-right{ border-right:0; border-left:var(--rule-hair) solid var(--color-rule); }
.pbw-card{ padding:0 0 var(--space-md); border-bottom:var(--rule-hair) solid var(--color-rule); }
.pbw-card + .pbw-card{ padding-top:var(--space-md); }
.pbw-card:last-child{ padding-bottom:0; border-bottom:0; }
.pbw-card-h{ display:flex; align-items:baseline; justify-content:space-between; gap:var(--space-xs); margin-bottom:var(--space-sm); color:var(--color-ink-3); font-family:var(--font-outlier); font-size:11px; font-weight:500; letter-spacing:.08em; }
.pbw-num{ margin-right:6px; color:var(--color-accent); font-size:10px; letter-spacing:.06em; }
.pbw-hint{ color:var(--color-ink-3); font-family:var(--font-outlier); font-size:10px; letter-spacing:.02em; }

.pbw-upload{ position:relative; display:flex; flex-direction:column; align-items:center; gap:4px; padding:var(--space-lg) var(--space-md); background:var(--color-paper); border:1px dashed var(--color-rule-strong); border-radius:var(--radius-md); text-align:center; cursor:pointer; transition:border-color var(--dur-micro) var(--ease-out),background-color var(--dur-micro) var(--ease-out); }
.pbw-upload:hover{ border-color:var(--color-accent); background:var(--color-accent-soft); }
.pbw-upload:focus-within{ outline:2px solid var(--color-focus); outline-offset:2px; border-color:var(--color-focus); }
.pbw-upload + .pbw-full{ margin-top:var(--space-sm); }
.pbw-file{ position:absolute; width:1px; height:1px; margin:-1px; padding:0; border:0; overflow:hidden; clip-path:inset(50%); }
.pbw-upload-ico{ color:var(--color-accent); font-family:var(--font-outlier); font-size:18px; line-height:1; }
.pbw-upload-txt{ color:var(--color-ink); font-family:var(--font-body); font-weight:500; font-size:var(--text-sm); }
.pbw-upload-sub{ color:var(--color-ink-3); font-size:11px; }

.pbw-seg{ display:grid; grid-template-columns:1fr 1fr; gap:1px; overflow:hidden; background:var(--color-rule); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-sm); }
.pbw-seg-col{ grid-template-columns:1fr; }
.pbw-seg button{ padding:8px 4px; background:var(--color-paper); border:0; color:var(--color-ink-2); font-family:var(--font-outlier); font-size:11px; letter-spacing:.02em; cursor:pointer; transition:background-color var(--dur-micro) var(--ease-out),color var(--dur-micro) var(--ease-out); }
.pbw-seg button:hover{ background:var(--color-paper-3); color:var(--color-accent); }
.pbw-seg button.on{ background:var(--color-ink); color:var(--color-paper); }
.pbw-seg-col button{ padding:9px 10px; text-align:left; }
.pbw-row{ display:flex; align-items:center; gap:var(--space-xs); margin-top:var(--space-sm); flex-wrap:wrap; }
.pbw-row-mid{ justify-content:center; }
.pbw-mini{ display:flex; align-items:center; gap:6px; flex:1 1 0; min-width:0; color:var(--color-ink-3); font-family:var(--font-outlier); font-size:10px; letter-spacing:.06em; }
.pbw-app .pbw-row .pbw-mini input{ width:100%; min-width:0; height:30px; padding:4px 8px; background:var(--color-paper); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-sm); color:var(--color-ink); font-family:var(--font-outlier); font-size:12px; line-height:1.2; box-sizing:border-box; }
.pbw-app .pbw-row .pbw-mini input:focus{ outline:2px solid var(--color-focus); outline-offset:1px; border-color:var(--color-focus); }
.pbw-app .pbw-row > .pbw-btn{ height:30px; min-height:30px; padding:0 var(--space-sm); font-size:11px; }

.pbw-pal{ display:grid; grid-template-columns:repeat(8,minmax(0,1fr)); gap:4px; }
.pbw-swatch{ width:100%; aspect-ratio:1/1; padding:0; border:0; border-radius:2px; box-shadow:inset 0 0 0 1px var(--color-rule-strong); cursor:pointer; }
.pbw-swatch:hover{ outline:1px solid var(--color-ink-3); outline-offset:1px; }
.pbw-swatch.on{ outline:2px solid var(--color-accent); outline-offset:2px; }
.pbw-app .pbw-swatch.pbw-sel-none,
.pbw-app .pbw-swatch#pbwEraser,
.pbw-app .pbw-swatch#pbwPicker{ aspect-ratio:auto; width:auto; flex:1; padding:7px 10px; background:var(--color-paper); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-sm); box-shadow:none; color:var(--color-ink-2); font-family:var(--font-body); font-weight:500; font-size:12px; }
.pbw-app .pbw-swatch.pbw-sel-none:hover,
.pbw-app .pbw-swatch#pbwEraser:hover,
.pbw-app .pbw-swatch#pbwPicker:hover{ border-color:var(--color-accent); color:var(--color-accent); outline:none; }
.pbw-app .pbw-swatch.pbw-sel-none.on,
.pbw-app .pbw-swatch#pbwEraser.on,
.pbw-app .pbw-swatch#pbwPicker.on{ background:var(--color-ink); border-color:var(--color-ink); color:var(--color-paper); outline:none; }

.pbw-layers{ display:flex; flex-direction:column; gap:4px; margin-bottom:var(--space-sm); }
.pbw-layer{ display:flex; align-items:center; gap:var(--space-xs); padding:6px 8px; background:var(--color-paper); border:var(--rule-hair) solid var(--color-rule); border-left:2px solid transparent; border-radius:var(--radius-sm); cursor:pointer; transition:border-color var(--dur-micro) var(--ease-out),background-color var(--dur-micro) var(--ease-out); }
.pbw-layer:hover{ border-color:var(--color-rule-strong); }
.pbw-layer.on{ background:var(--color-paper-3); border-left-color:var(--color-accent); }
.pbw-layer-vis{ display:grid; place-items:center; width:24px; height:24px; padding:0; background:transparent; border:0; border-radius:var(--radius-sm); color:var(--color-ink-3); font-size:12px; line-height:1; cursor:pointer; }
.pbw-layer-vis:hover{ background:var(--color-paper-2); color:var(--color-ink); }
.pbw-layer-vis.on{ color:var(--color-accent); }
.pbw-layer-name{ flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--color-ink); font-family:var(--font-body); font-weight:500; font-size:12px; }
.pbw-layer-cnt{ color:var(--color-ink-3); font-family:var(--font-outlier); font-size:10px; }

.pbw-zoombox{ display:flex; align-items:center; justify-content:center; gap:var(--space-xs); }
.pbw-zoom{ min-width:56px; color:var(--color-ink); font-family:var(--font-outlier); font-weight:500; font-size:var(--text-sm); font-variant-numeric:tabular-nums; text-align:center; }
.pbw-zoombtn{ flex:1; min-height:34px; padding:0; font-size:16px; line-height:1; }

.pbw-stage{ display:flex; flex-direction:column; gap:var(--space-sm); padding:var(--space-md); background:var(--color-paper); min-width:0; }
.pbw-stage-bar{ display:flex; align-items:center; justify-content:space-between; gap:var(--space-sm); flex-wrap:wrap; }
.pbw-viewtabs{ display:flex; gap:2px; padding:2px; background:var(--color-paper-2); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-sm); }
.pbw-viewtabs button{ padding:6px 14px; background:transparent; border:0; border-radius:2px; color:var(--color-ink-2); font-family:var(--font-body); font-weight:500; font-size:12px; cursor:pointer; transition:background-color var(--dur-micro) var(--ease-out),color var(--dur-micro) var(--ease-out); }
.pbw-viewtabs button:hover{ color:var(--color-accent); }
.pbw-viewtabs button.on{ background:var(--color-ink); color:var(--color-paper); }
.pbw-modebar{ display:flex; align-items:center; gap:var(--space-xs); }
.pbw-mode-label{ color:var(--color-ink-3); font-family:var(--font-outlier); font-size:10px; letter-spacing:.08em; }
.pbw-modeseg{ position:relative; overflow:hidden; background:var(--color-paper-2); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-sm); }
.pbw-modeseg-btns{ position:relative; z-index:2; display:grid; grid-auto-flow:column; grid-auto-columns:1fr; }
.pbw-modeseg button{ position:relative; z-index:2; padding:6px 16px; background:transparent; border:0; color:var(--color-ink-2); font-family:var(--font-body); font-weight:500; font-size:12px; cursor:pointer; transition:color var(--dur-short) var(--ease-out); }
.pbw-modeseg button.on{ color:var(--color-accent-ink); }
.pbw-knob{ position:absolute; top:0; bottom:0; left:0; z-index:1; width:50%; background:var(--color-accent); transition:transform var(--dur-short) var(--ease-out); }
/* 深色主题下墨色反相会把选中段变成大白块，改用印章红保持层级 */
[data-theme="dark"] .pbw-seg button.on,
[data-theme="dark"] .pbw-viewtabs button.on{ background:var(--color-accent); color:var(--color-accent-ink); }
[data-theme="dark"] .pbw-seg button.on:hover,
[data-theme="dark"] .pbw-viewtabs button.on:hover{ background:var(--color-accent-deep); color:var(--color-accent-ink); }

.pbw-canvas-area{ position:relative; }
.pbw-canvas-wrap{ position:relative; min-width:0; min-height:420px; max-height:78vh; overflow:auto; background:var(--color-paper-3); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-sm); }
.pbw-canvas-inner{ width:max-content; min-height:100%; margin:0 auto; }
.pbw-canvas-wrap > .pbw-canvas-inner{ padding-bottom:52px; }
.pbw-canvas{ display:block; background:var(--pbw-board); image-rendering:pixelated; }
.pbw-v3{ position:absolute; inset:0; display:none; }
.pbw-v3 canvas{ display:block; width:100%; height:100%; }
.pbw-empty{ position:absolute; inset:0; display:none; align-items:center; justify-content:center; padding:var(--space-lg); color:var(--pbw-board-ink); font-family:var(--font-display); font-size:var(--text-md); letter-spacing:.01em; text-align:center; pointer-events:none; }
.pbw-meltbar{ position:absolute; left:50%; bottom:var(--space-sm); z-index:var(--z-raised); transform:translateX(-50%); display:none; align-items:center; justify-content:center; gap:var(--space-xs); max-width:calc(100% - 24px); padding:5px 8px; background:var(--color-paper); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-pill); box-shadow:var(--shadow-whisper); }
.pbw-meltbar button{ padding:4px 8px; background:transparent; border:0; border-radius:var(--radius-pill); color:var(--color-ink-2); font-family:var(--font-body); font-weight:500; font-size:12px; white-space:nowrap; cursor:pointer; }
.pbw-meltbar button:hover{ color:var(--color-accent); }
.pbw-meltbar input[type=range]{ flex:0 1 240px; accent-color:var(--color-accent); }

.pbw-spec{ display:none; padding:var(--space-md); background:transparent; border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-sm); }
.pbw-spec-grid{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1px; margin-bottom:var(--space-md); overflow:hidden; background:var(--color-rule); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-sm); }
.pbw-spec-item{ padding:var(--space-sm) var(--space-md); background:var(--color-paper); }
.pbw-spec-item .k{ display:block; margin-bottom:4px; color:var(--color-ink-3); font-family:var(--font-outlier); font-size:10px; letter-spacing:.06em; }
.pbw-spec-item b{ color:var(--color-ink); font-family:var(--font-outlier); font-weight:500; font-size:var(--text-lg); font-variant-numeric:tabular-nums; }
.pbw-spec-pal .pbw-pal{ margin-top:var(--space-xs); }

.pbw-assist{ display:none; }
.pbw-assist-bar{ display:flex; align-items:center; justify-content:space-between; gap:var(--space-sm); margin-bottom:var(--space-sm); flex-wrap:wrap; }
.pbw-assist-tip{ color:var(--color-ink-3); font-size:12px; }
.pbw-assist-nav{ display:flex; align-items:center; gap:var(--space-xs); color:var(--color-ink-3); font-family:var(--font-outlier); font-size:11px; }
.pbw-assist-canvas-wrap{ min-width:0; min-height:420px; max-height:78vh; overflow:auto; background:var(--color-paper-3); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-sm); }

.pbw-progress{ padding:var(--space-md); border:var(--rule-hair) solid var(--color-rule); border-radius:var(--radius-sm); }
.pbw-prog-list{ display:flex; flex-direction:column; max-height:240px; overflow:auto; border-top:var(--rule-hair) solid var(--color-rule); }
.pbw-prog-row{ display:flex; align-items:center; gap:var(--space-sm); padding:7px 4px; border-bottom:var(--rule-hair) solid var(--color-rule); cursor:pointer; transition:background-color var(--dur-micro) var(--ease-out); }
.pbw-prog-row:hover{ background:var(--color-paper-2); }
.pbw-prog-row.done{ opacity:.55; }
.pbw-prog-row.done .pbw-prog-name{ text-decoration:line-through; }
.pbw-prog-sw{ flex:0 0 auto; width:16px; height:16px; border-radius:2px; box-shadow:inset 0 0 0 1px var(--color-rule-strong); }
.pbw-prog-name{ flex:1; color:var(--color-ink); font-family:var(--font-body); font-size:12px; }
.pbw-prog-ct{ color:var(--color-ink-3); font-family:var(--font-outlier); font-size:10px; }

/* 平板：保留左栏 + 舞台，右栏收成底部三列，避免控件被拉成整行 */
@media (max-width:980px) and (min-width:641px){
  .pbw-body{ grid-template-columns:240px minmax(0,1fr); }
  .pbw-dock{ border-bottom:0; }
  .pbw-dock-right{ grid-column:1 / -1; display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:var(--space-lg); padding:var(--space-md); border-left:0; border-top:var(--rule-hair) solid var(--color-rule); }
  .pbw-dock-right .pbw-card{ padding:0; border-bottom:0; }
  .pbw-dock-right .pbw-card + .pbw-card{ padding-top:0; padding-left:var(--space-lg); border-left:var(--rule-hair) solid var(--color-rule); }
  .pbw-spec-grid{ grid-template-columns:repeat(2,minmax(0,1fr)); }
  .pbw-title-wrap{ flex:1 1 160px; }
  .pbw-tasktitle{ width:100%; }
}
@media (max-width:640px){
  .pbw-body{ grid-template-columns:1fr; }
  .pbw-dock{ border-right:0; border-left:0; border-bottom:var(--rule-hair) solid var(--color-rule); }
  .pbw-dock-right{ border-left:0; border-top:var(--rule-hair) solid var(--color-rule); border-bottom:0; }
  .pbw-spec-grid{ grid-template-columns:repeat(2,minmax(0,1fr)); }
  .pbw-title-wrap{ flex:1 1 160px; }
  .pbw-tasktitle{ width:100%; }
}
@media (max-width:600px){
  .pbw-top{ flex-wrap:wrap; gap:var(--space-xs); padding:var(--space-xs) var(--space-sm); }
  .pbw-tag{ display:none; }
  .pbw-brandname{ font-size:var(--text-base); }
  .pbw-title-wrap{ order:3; flex:1 1 100%; }
  .pbw-actions{ margin-left:auto; }
  .pbw-btn{ min-height:36px; padding:6px 10px; font-size:12px; }
  .pbw-dock{ padding:var(--space-sm); }
  .pbw-stage{ padding:var(--space-sm); gap:var(--space-sm); }
  .pbw-viewtabs button{ padding:6px 10px; font-size:12px; }
  .pbw-mode-label{ display:none; }
  .pbw-canvas-wrap,
  .pbw-assist-canvas-wrap{ min-height:300px; max-height:58vh; }
  .pbw-meltbar{ bottom:var(--space-xs); gap:2px; padding:4px 6px; }
  .pbw-meltbar button{ padding:4px 6px; font-size:11px; }
  .pbw-meltbar input[type=range]{ flex:1 1 60px; min-width:60px; }
}
@media (prefers-reduced-motion:reduce){
  .pbw-app *{ transition:none !important; }
}
</style>
