<div class="score-row">
  <span class="score-box">得分 <b id="gScore">0</b></span>
  <span class="score-box">最高分 <b id="gBest">0</b></span>
  <button class="btn" id="gNew">新开一局</button>
</div>
<canvas id="gBoard" width="440" height="440" style="max-width:100%;border:1px solid var(--line);border-radius:10px;background:var(--panel);touch-action:none" aria-label="2048 棋盘"></canvas>

<script src="{{ asset('js/games/2048.js') }}"></script>
