<div class="score-row">
  <span class="score-box">步数 <b id="mMoves">0</b></span>
  <span class="score-box">用时 <b id="mTime">0s</b></span>
  <span class="score-box">最佳 <b id="mBest">--</b></span>
  <button class="btn" id="mNew">重新开始</button>
</div>
<div class="flip-grid" id="mGrid"></div>
<p class="msg ok" id="mWin" style="display:none">🎉 全部配对完成！</p>

<script>
(function(){
  var ICONS = ['🎮','🚀','🐱','🍕','🎧','⚽','🌙','💡'];
  var grid = document.getElementById('mGrid');
  var movesEl = document.getElementById('mMoves');
  var timeEl = document.getElementById('mTime');
  var bestEl = document.getElementById('mBest');
  var first = null, lock = false, moves = 0, matched = 0;
  var startAt = null, tick = null;
  var best = localStorage.getItem('tbFlipBest');
  if(best) bestEl.textContent = best + 's';

  function shuffle(arr){
    for(var i = arr.length - 1; i > 0; i--){
      var j = Math.floor(Math.random() * (i + 1));
      var t = arr[i]; arr[i] = arr[j]; arr[j] = t;
    }
    return arr;
  }

  function newGame(){
    clearInterval(tick);
    first = null; lock = false; moves = 0; matched = 0; startAt = null;
    movesEl.textContent = 0; timeEl.textContent = '0s';
    document.getElementById('mWin').style.display = 'none';
    var deck = shuffle(ICONS.concat(ICONS));
    grid.innerHTML = '';
    deck.forEach(function(icon){
      var card = document.createElement('button');
      card.className = 'flip-card';
      card.innerHTML = '<span class="flip-inner"><span class="flip-front"></span><span class="flip-back">' + icon + '</span></span>';
      card.dataset.icon = icon;
      card.addEventListener('click', function(){ flip(card); });
      grid.appendChild(card);
    });
  }

  function flip(card){
    if(lock || card.classList.contains('flipped') || card.classList.contains('done')) return;
    if(!startAt){ startAt = Date.now(); tick = setInterval(function(){
      timeEl.textContent = Math.floor((Date.now() - startAt) / 1000) + 's';
    }, 500); }
    card.classList.add('flipped');

    if(!first){ first = card; return; }

    moves++;
    movesEl.textContent = moves;
    if(first.dataset.icon === card.dataset.icon){
      first.classList.add('done'); card.classList.add('done');
      first.classList.remove('flipped'); card.classList.remove('flipped');
      first = null;
      matched++;
      if(matched === ICONS.length) win();
    } else {
      lock = true;
      var a = first, b = card;
      first = null;
      setTimeout(function(){
        a.classList.remove('flipped'); b.classList.remove('flipped');
        lock = false;
      }, 700);
    }
  }

  function win(){
    clearInterval(tick);
    var secs = Math.floor((Date.now() - startAt) / 1000);
    document.getElementById('mWin').style.display = 'block';
    if(!best || secs < +best){
      localStorage.setItem('tbFlipBest', secs);
      bestEl.textContent = secs + 's';
      document.getElementById('mWin').textContent = '🎉 新纪录！用时 ' + secs + ' 秒 / ' + moves + ' 步';
    }
  }

  document.getElementById('mNew').addEventListener('click', newGame);
  newGame();
})();
</script>

<style>
.flip-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:0.6rem;max-width:440px}
.flip-card{aspect-ratio:1;border:none;background:transparent;cursor:pointer;padding:0}
.flip-inner{position:relative;display:block;width:100%;height:100%;transition:transform 300ms ease;transform-style:preserve-3d}
.flip-card.flipped .flip-inner,.flip-card.done .flip-inner{transform:rotateY(180deg)}
.flip-front,.flip-back{position:absolute;inset:0;display:grid;place-items:center;border-radius:10px;backface-visibility:hidden;font-size:1.8rem}
.flip-front{background:var(--accent);border:1px solid var(--line)}
.flip-front::after{content:'?';color:var(--accent-ink);font-family:var(--font-display);font-weight:700;font-size:1.4rem;opacity:0.85}
.flip-back{background:var(--panel);border:1px solid var(--line);transform:rotateY(180deg)}
.flip-card.done .flip-back{background:var(--panel);opacity:0.55}
@media (max-width:480px){.flip-grid{gap:0.4rem}.flip-front,.flip-back{font-size:1.3rem}}
</style>
