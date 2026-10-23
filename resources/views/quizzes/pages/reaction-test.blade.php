<div id="reactApp">
  <div class="r-stage" id="rStage">
    <p class="r-big" id="rBig">点击开始</p>
    <p class="r-sub" id="rSub">5 轮测试 · 红变绿的瞬间点击 · 越快越好</p>
  </div>
  <div class="q-result" id="rResult" style="display:none">
    <p class="fl">平均反应速度</p>
    <div class="mbti-code" id="rMs"></div>
    <p class="mbti-nick" id="rLevel"></p>
    <p class="mbti-desc" id="rLevelDesc"></p>
    <p class="mbti-dims" id="rRounds"></p>
    <button class="btn" id="rRetry">再测一次</button>
  </div>
</div>

<script>
(function(){
  var stage = document.getElementById('rStage');
  var big = document.getElementById('rBig');
  var sub = document.getElementById('rSub');
  var state = 'idle'; // idle | waiting | go | between | done
  var rounds = [], greenAt = 0, timer = null;
  var TOTAL = 5;

  function setState(bg, bigText, subText){
    stage.style.background = bg;
    big.textContent = bigText;
    sub.textContent = subText;
  }

  function arm(){
    state = 'waiting';
    setState('#c0492f', '等待变绿…', '第 ' + (rounds.length + 1) + ' / ' + TOTAL + ' 轮 · 提前点击本轮作废');
    timer = setTimeout(function(){
      greenAt = performance.now();
      state = 'go';
      setState('var(--accent)', '点！', '');
    }, 1200 + Math.random() * 2800);
  }

  stage.addEventListener('click', function(){
    if(state === 'done') return;

    if(state === 'idle' || state === 'between'){
      arm();
      return;
    }

    if(state === 'waiting'){
      // 抢跑
      clearTimeout(timer);
      rounds = [];
      state = 'idle';
      setState('#c0492f', '抢跑啦！重来', '点击任意处重新开始');
      return;
    }

    if(state === 'go'){
      var ms = Math.round(performance.now() - greenAt);
      rounds.push(ms);
      state = 'go' === 'go' && rounds.length < TOTAL ? 'between' : 'done';
      if(state === 'between'){
        setState('var(--panel)', ms + ' ms', '点击继续 · ' + rounds.length + ' / ' + TOTAL);
      } else {
        finish();
      }
    }
  });

  function finish(){
    state = 'done';
    var avg = Math.round(rounds.reduce(function(a,b){ return a+b; }, 0) / rounds.length);
    document.getElementById('rMs').textContent = avg + ' ms';
    var lv = avg <= 220 ? ['闪电之手', '人类平均 250ms，你已经接近职业电竞选手的水准。']
      : avg <= 280 ? ['反应迅捷', '明显高于平均水平，手眼协调相当不错。']
      : avg <= 350 ? ['正常水准', '标准人类反应速度，别谦虚也别灰心。']
      : ['慢热型', '今天状态一般？喝口水，再战一轮。'];
    document.getElementById('rLevel').textContent = '「' + lv[0] + '」';
    document.getElementById('rLevelDesc').textContent = lv[1];
    document.getElementById('rRounds').textContent = '各轮成绩：' + rounds.join(' · ');
    document.getElementById('rResult').style.display = 'block';
    stage.style.display = 'none';
  }

  document.getElementById('rRetry').addEventListener('click', function(){
    rounds = [];
    document.getElementById('rResult').style.display = 'none';
    stage.style.display = 'flex';
    state = 'idle';
    setState('var(--panel)', '点击开始', '5 轮测试 · 红变绿的瞬间点击 · 越快越好');
  });
})();
</script>

<style>
#reactApp .r-stage{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.75rem;border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:5rem 1.5rem;cursor:pointer;user-select:none;transition:background-color 120ms ease}
#reactApp .r-big{font-family:var(--font-display);font-weight:700;font-size:2rem;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,0.25)}
#reactApp .r-sub{font-family:var(--font-mono);font-size:0.8rem;color:rgba(255,255,255,0.85)}
#reactApp .q-result{border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:2rem;text-align:center}
#reactApp .mbti-code{font-family:var(--font-display);font-weight:700;font-size:2.8rem;color:var(--accent)}
#reactApp .mbti-nick{font-size:1.05rem;color:var(--ink)}
#reactApp .mbti-desc{color:var(--ink-2);margin:0.8rem auto 1rem;max-width:42ch}
#reactApp .mbti-dims{font-family:var(--font-mono);font-size:0.75rem;color:var(--ink-3);margin-bottom:1.4rem}
</style>
