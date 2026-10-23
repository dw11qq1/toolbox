/* ToolBox · 2048 — 原生 Canvas 实现，最高分存 localStorage */
(function(){
  var SIZE = 4, PAD = 10, CELL = 100;
  var canvas = document.getElementById('gBoard');
  var ctx = canvas.getContext('2d');
  var scoreEl = document.getElementById('gScore');
  var bestEl = document.getElementById('gBest');

  var grid, score, over, moved;
  var best = +(localStorage.getItem('tb2048best') || 0);
  bestEl.textContent = best;

  /* 配色：明快蓝系，高数值渐深，4096+ 用橙红区分 */
  var COLORS = {
    2:'#e3e9f4', 4:'#c9d7f2', 8:'#9dc0f5', 16:'#74a7f8',
    32:'#4f8df9', 64:'#2563eb', 128:'#3f7ef0', 256:'#3169e0',
    512:'#2563eb', 1024:'#1d4fc4', 2048:'#173f9e',
    4096:'#f97316', 8192:'#c2410c'
  };

  function reset(){
    grid = Array.from({length:SIZE}, function(){ return Array(SIZE).fill(0); });
    score = 0; over = false;
    addTile(); addTile();
    draw();
  }

  function addTile(){
    var empty = [];
    for(var r=0;r<SIZE;r++) for(var c=0;c<SIZE;c++) if(!grid[r][c]) empty.push([r,c]);
    if(!empty.length) return;
    var cell = empty[Math.floor(Math.random()*empty.length)];
    grid[cell[0]][cell[1]] = Math.random() < 0.9 ? 2 : 4;
  }

  function slide(row){
    var arr = row.filter(function(v){ return v; });
    for(var i=0;i<arr.length-1;i++){
      if(arr[i] === arr[i+1]){ arr[i]*=2; score+=arr[i]; arr.splice(i+1,1); }
    }
    while(arr.length < SIZE) arr.push(0);
    return arr;
  }

  function move(dir){
    if(over) return;
    moved = false;
    var before = JSON.stringify(grid);
    for(var i=0;i<SIZE;i++){
      var line = extract(i, dir);
      line = slide(line);
      putBack(i, dir, line);
    }
    if(JSON.stringify(grid) !== before){
      addTile(); draw(); saveBest();
      if(!canMove()) { over = true; drawOver(); }
    }
  }

  function extract(i, dir){
    var line = [];
    for(var j=0;j<SIZE;j++){
      if(dir==='left') line.push(grid[i][j]);
      else if(dir==='right') line.push(grid[i][SIZE-1-j]);
      else if(dir==='up') line.push(grid[j][i]);
      else line.push(grid[SIZE-1-j][i]);
    }
    return line;
  }

  function putBack(i, dir, line){
    for(var j=0;j<SIZE;j++){
      if(dir==='left') grid[i][j] = line[j];
      else if(dir==='right') grid[i][SIZE-1-j] = line[j];
      else if(dir==='up') grid[j][i] = line[j];
      else grid[SIZE-1-j][i] = line[j];
    }
  }

  function canMove(){
    for(var r=0;r<SIZE;r++) for(var c=0;c<SIZE;c++){
      if(!grid[r][c]) return true;
      if(c<SIZE-1 && grid[r][c]===grid[r][c+1]) return true;
      if(r<SIZE-1 && grid[r][c]===grid[r+1][c]) return true;
    }
    return false;
  }

  function saveBest(){
    if(score > best){ best = score; localStorage.setItem('tb2048best', best); }
    bestEl.textContent = best;
  }

  function roundRect(x,y,w,h,r){
    ctx.beginPath();
    ctx.moveTo(x+r,y);
    ctx.arcTo(x+w,y,x+w,y+h,r);
    ctx.arcTo(x+w,y+h,x,y+h,r);
    ctx.arcTo(x,y+h,x,y,r);
    ctx.arcTo(x,y,x+w,y,r);
    ctx.closePath();
  }

  function draw(){
    ctx.clearRect(0,0,440,440);
    for(var r=0;r<SIZE;r++) for(var c=0;c<SIZE;c++){
      var x = PAD + c*(CELL+PAD*0.9);
      var y = PAD + r*(CELL+PAD*0.9);
      var v = grid[r][c];
      ctx.fillStyle = v ? (COLORS[v] || '#173f9e') : 'rgba(0,0,0,0.05)';
      roundRect(x, y, CELL, CELL, 8);
      ctx.fill();
      if(v){
        ctx.fillStyle = v <= 4 ? '#1a1c1a' : '#f4f6f2';
        ctx.font = '700 ' + (v >= 1024 ? 30 : v >= 128 ? 36 : 42) + 'px Sora, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(v, x + CELL/2, y + CELL/2 + 2);
      }
    }
    scoreEl.textContent = score;
  }

  function drawOver(){
    ctx.fillStyle = 'rgba(26,28,26,0.55)';
    roundRect(PAD, PAD, 440-PAD*2, 440-PAD*2, 10);
    ctx.fill();
    ctx.fillStyle = '#f4f6f2';
    ctx.font = '700 34px Sora, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('游戏结束', 220, 205);
    ctx.font = '400 16px "Noto Sans SC", sans-serif';
    ctx.fillText('按「新开一局」再来', 220, 240);
  }

  document.addEventListener('keydown', function(e){
    var map = {ArrowLeft:'left', ArrowRight:'right', ArrowUp:'up', ArrowDown:'down'};
    if(map[e.key]){ e.preventDefault(); move(map[e.key]); }
  });

  /* 触摸滑动 */
  var tx, ty;
  canvas.addEventListener('touchstart', function(e){
    tx = e.touches[0].clientX; ty = e.touches[0].clientY;
  }, {passive:true});
  canvas.addEventListener('touchend', function(e){
    var dx = e.changedTouches[0].clientX - tx;
    var dy = e.changedTouches[0].clientY - ty;
    if(Math.abs(dx) < 24 && Math.abs(dy) < 24) return;
    if(Math.abs(dx) > Math.abs(dy)) move(dx > 0 ? 'right' : 'left');
    else move(dy > 0 ? 'down' : 'up');
  }, {passive:true});

  document.getElementById('gNew').addEventListener('click', reset);
  reset();
})();
