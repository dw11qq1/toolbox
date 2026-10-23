<div id="loveApp">
  <div class="q-progress"><span id="lNow">1</span> / 12</div>
  <div class="q-bar"><div id="lBar" style="width:2%"></div></div>
  <div class="q-box" id="lBox">
    <p class="q-text" id="lText"></p>
    <div class="q-opts">
      <button class="q-opt" id="lA"></button>
      <button class="q-opt" id="lB"></button>
      <button class="q-opt" id="lC"></button>
    </div>
  </div>
  <div class="q-result" id="lResult" style="display:none">
    <p class="fl">你的依恋风格</p>
    <div class="mbti-code" id="lCode"></div>
    <p class="mbti-nick" id="lNick"></p>
    <p class="mbti-desc" id="lDesc"></p>
    <p class="mbti-dims" id="lDims"></p>
    <button class="btn" id="lRetry">再测一次</button>
  </div>
</div>

<script>
(function(){
  /* 三选项分别对应 S 安全型 / A 专注焦虑型 / V 回避型 */
  var Q = [
    ["喜欢的人没有及时回消息，你会","等有空再回，该干嘛干嘛|S","忍不住反复看手机|A","松了口气，正好干自己的事|V"],
    ["关系稳定一段时间后，你通常","感到踏实安心|S","偶尔担心对方变心|A","隐隐想拉开点距离|V"],
    ["对方想和你聊聊感情里的问题，你","认真一起聊开|S","很紧张，怕听到不好的话|A","能拖就拖，聊了也尴尬|V"],
    ["你对「分享日常」的看法","愿意主动分享|S","希望对方主动来问|A","觉得没什么可汇报的|V"],
    ["吵架之后你一般","冷静后主动沟通|S","道歉求和，怕关系破裂|A","先各自冷静，别来找我|V"],
    ["你理想中的亲密是","亲密但各有空间|S","时时刻刻在一起|A","保持适度独立|V"],
    ["对方和朋友聚会到很晚，你","相信对方，睡自己的觉|S","会胡思乱想到很晚|A","完全不关心，最好别吵我|V"],
    ["表达爱意这件事你","习惯说出来也做得到|S","很需要也说出口|A","心里有，嘴上说不出|V"],
    ["遇到更优秀的人示好，你","明确拒绝，我有人了|S","会动摇，需要对方反复确认|A","开始重新评估这段关系|V"],
    ["你觉得依赖伴侣","彼此依赖很正常|S","希望自己更被依赖|A","尽量谁都别依赖谁|V"],
    ["回忆上一段感情，你最常想到","相处舒服的日常|S","患得患失的瞬间|A","被束缚的感觉|V"],
    ["「无条件被爱」对你来说","我值得，也这样被爱着|S","是我一直想确认的事|A","有点不真实|V"],
  ];

  var TYPE = {
    S:['安全型', '你对亲密关系有稳定的信任感：既能投入，也能保有自我。和你在一起的人会觉得很安心。你需要留意的是——别把「不吵不闹」当成一切都好，偶尔主动表达需求会让关系更近一步。'],
    A:['专注型（焦虑型）', '你在感情里投入度高、感受力强，但也容易患得患失。你值得被稳定地爱，而你需要的往往只是一句明确的确认。找一个回应及时、表达直接的伴侣，你的焦虑会大幅下降。'],
    V:['回避型', '你独立、自洽，但亲密会让你下意识后退。其实你渴望连接，只是害怕失去自我。试着把「我需要空间」提前说出来，比突然冷淡更能保护关系。'],
    M:['混合型', '你在安全与不安之间摇摆：有时从容，有时敏感。这很常见——你的依恋风格会随着遇到的人而变化。一段稳定的关系，就是最好的疗愈。']
  };

  var idx = 0, score = {S:0, A:0, V:0};
  var lText = document.getElementById('lText');
  var lBox = document.getElementById('lBox');

  function render(){
    document.getElementById('lNow').textContent = idx + 1;
    document.getElementById('lBar').style.width = (idx / Q.length * 100) + '%';
    lText.textContent = Q[idx][0] + '？';
    for(var i = 0; i < 3; i++){
      document.getElementById(['lA','lB','lC'][i]).textContent = Q[idx][1].split('|')[i];
    }
  }

  function answer(i){
    score[Q[idx][1].split('|')[i].split('|')[1]]++;
    idx++;
    if(idx < Q.length) render(); else finish();
  }

  function finish(){
    lBox.style.display = 'none';
    document.getElementById('lBar').style.width = '100%';
    var keys = Object.keys(score);
    var max = keys[0];
    keys.forEach(function(k){ if(score[k] > score[max]) max = k; });
    var allSame = score.S === score.A && score.A === score.V;
    var t = allSame ? 'M' : max;
    document.getElementById('lCode').textContent = t === 'M' ? '混合型' : TYPE[t][0];
    document.getElementById('lNick').textContent = allSame ? '三种特质都有一些' : '';
    document.getElementById('lDesc').textContent = TYPE[t][1];
    document.getElementById('lDims').textContent = '安全 ' + score.S + ' · 专注 ' + score.A + ' · 回避 ' + score.V;
    document.getElementById('lResult').style.display = 'block';
  }

  document.getElementById('lA').addEventListener('click', function(){ answer(0); });
  document.getElementById('lB').addEventListener('click', function(){ answer(1); });
  document.getElementById('lC').addEventListener('click', function(){ answer(2); });
  document.getElementById('lRetry').addEventListener('click', function(){
    idx = 0; score = {S:0, A:0, V:0};
    document.getElementById('lResult').style.display = 'none';
    lBox.style.display = 'block';
    render();
  });

  render();
})();
</script>

<style>
#loveApp .q-progress{font-family:var(--font-mono);font-size:0.8rem;color:var(--ink-3);margin-bottom:0.5rem}
#loveApp .q-bar{height:4px;background:var(--panel);border-radius:99px;overflow:hidden;margin-bottom:2rem}
#loveApp .q-bar div{height:100%;background:var(--accent);transition:width 200ms ease}
#loveApp .q-text{font-family:var(--font-display);font-weight:700;font-size:1.25rem;margin-bottom:1.5rem}
#loveApp .q-opts{display:grid;gap:0.75rem}
#loveApp .q-opt{width:100%;text-align:left;font-family:var(--font-body);font-size:1rem;padding:1rem 1.3rem;border:1.5px solid var(--line);background:var(--panel);color:var(--ink);border-radius:12px;cursor:pointer;transition:border-color 160ms ease,background-color 160ms ease,color 160ms ease}
#loveApp .q-opt:hover{border-color:var(--accent);background:var(--accent);color:var(--accent-ink)}
#loveApp .q-result{border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:2rem;text-align:center}
#loveApp .mbti-code{font-family:var(--font-display);font-weight:700;font-size:2.2rem;color:var(--accent)}
#loveApp .mbti-nick{color:var(--ink-3);font-size:0.9rem}
#loveApp .mbti-desc{color:var(--ink-2);margin:1rem auto 0;max-width:46ch;text-align:left}
#loveApp .mbti-dims{font-family:var(--font-mono);font-size:0.75rem;color:var(--ink-3);margin:1.2rem 0 1.5rem}
</style>
