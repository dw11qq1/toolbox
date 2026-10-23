<div id="mbtiApp">
  <div class="q-progress"><span id="qNow">1</span> / 48</div>
  <div class="q-bar"><div id="qBar" style="width:2%"></div></div>
  <div class="q-box">
    <p class="q-text" id="qText"></p>
    <div class="q-opts">
      <button class="q-opt" id="optA"></button>
      <button class="q-opt" id="optB"></button>
    </div>
  </div>
  <div class="q-result" id="qResult" style="display:none">
    <p class="fl">你的类型</p>
    <div class="mbti-code" id="rCode"></div>
    <p class="mbti-nick" id="rNick"></p>
    <p class="mbti-desc" id="rDesc"></p>
    <p class="mbti-dims" id="rDims"></p>
    <button class="btn" id="rRetry">再测一次</button>
  </div>
</div>

<script>
(function(){
  /* 四维度：E/I 精力来源 · S/N 信息获取 · T/F 决策方式 · J/P 生活态度（各 12 题） */
  var Q = [
    ["聚会结束后你通常","能量满满，还想再玩一会|E","只想安静待着回血|I"],
    ["周末更愿意","约朋友出门|E","一个人宅家|I"],
    ["新环境里你一般","主动认识新朋友|E","等别人来搭话|I"],
    ["团队讨论时你","想到就说|E","想清楚再说|I"],
    ["长时间独处会让你","有点闷得慌|E","非常舒服|I"],
    ["你的朋友圈","广而杂|E","少而深|I"],
    ["解决问题时你倾向","边说边理清思路|E","先自己想好再说|I"],
    ["出差旅行你更兴奋的是","认识路上的人|E","看没看过的风景|I"],
    ["电话响了你","顺手就接|E","看一眼再决定|I"],
    ["别人评价你","热情外向|E","沉稳内敛|I"],
    ["忙碌一天后的社交","让你更兴奋|E","让你更疲惫|I"],
    ["你更喜欢的工作方式","多人协作讨论|E","独立专注推进|I"],

    ["你更容易注意到","具体的细节|S","背后的可能性|N"],
    ["别人觉得你","脚踏实地|S","天马行空|N"],
    ["学新东西你偏好","按步骤实操|S","先搞懂整体原理|N"],
    ["你更擅长记住","发生过的事实|S","听过的新想法|N"],
    ["你喜欢的书或剧","写实具体|S","脑洞大开|N"],
    ["做计划时你","参考过去的经验|S","设想未来的可能|N"],
    ["你更相信","眼见为实|S","直觉判断|N"],
    ["描述一件事时你","按顺序讲细节|S","先讲大意和联想|N"],
    ["你更欣赏的人","务实可靠|S","创新大胆|N"],
    ["面对新工具你","先看说明书|S","先随便点点试|N"],
    ["你的想法更多关于","现实怎么做|S","未来会怎样|N"],
    ["你觉得自己","活在当下|S","活在未来|N"],

    ["朋友倾诉烦恼时你先","帮分析问题|T","先安慰情绪|F"],
    ["做决定时你更看重","逻辑合理|T","感受和价值观|F"],
    ["别人说你","讲道理|T","有同理心|F"],
    ["被批评时你先想","说得对不对|T","语气伤不伤人|F"],
    ["团队合作里你最在意","公平高效的规则|T","氛围和每个人的感受|F"],
    ["电影里最打动你的是","精彩的逻辑反转|T","人物的悲欢离合|F"],
    ["你认为温柔","是效率的敌人|T","是必要的事|F"],
    ["争论时你","对事不对人，直说|T","先照顾对方面子|F"],
    ["择业时你更看重","回报与前景|T","意义与热爱|F"],
    ["你给建议的方式","直接指出问题|T","先肯定再委婉提|F"],
    ["你更难接受","被说不讲理|T","被说冷血|F"],
    ["评价方案时你","只看优缺点|T","也看对人的影响|F"],

    ["旅行前你会","做好详细攻略|J","说走就走|P"],
    ["你的桌面通常","整齐有序|J","比较随意|P"],
    ["截止日期对你","提前完成才安心|J","最后冲刺才有状态|P"],
    ["计划有变时你","有点烦躁|J","无所谓，顺势而为|P"],
    ["你更喜欢","把事情定下来|J","保留多个选项|P"],
    ["收拾东西你","物归原位|J","放着再说的|P"],
    ["别人约你","越早定越好|J","到时候看|P"],
    ["你的人生信条","有备无患|J","船到桥头自然直|P"],
    ["工作清单你","逐项打勾|J","清单常常吃灰|P"],
    ["面对选择你","快点决定很轻松|J","多留着选择更舒服|P"],
    ["你的日常","规律可预测|J","随性多变|P"],
    ["任务来了你","先排计划|J","先干起来再说|P"],
  ];

  var NICK = {
    INTJ:'建筑师', INTP:'逻辑学家', ENTJ:'指挥官', ENTP:'辩论家',
    INFJ:'提倡者', INFP:'调停者', ENFJ:'主人公', ENFP:'竞选者',
    ISTJ:'物流师', ISFJ:'守卫者', ESTJ:'总经理', ESFJ:'执政官',
    ISTP:'鉴赏家', ISFP:'探险家', ESTP:'企业家', ESFP:'表演者'
  };
  var DESC = {
    INTJ:'独立、有战略眼光，喜欢用体系解决问题，天生规划者。',
    INTP:'安静的思考者，痴迷于理解世界的运行逻辑。',
    ENTJ:'果断有领导力，擅长把愿景拆成执行计划。',
    ENTP:'点子多、爱辩论，越有挑战越兴奋。',
    INFJ:'理想主义者，洞察人心，追求有意义的人生。',
    INFP:'内心温柔的调停者，忠于自己的价值观。',
    ENFJ:'天生带动他人成长，热情而有感染力。',
    ENFP:'热情洋溢的创意派，把生活过成冒险。',
    ISTJ:'靠谱的代名词，说到做到，秩序感强。',
    ISFJ:'默默照顾身边人的守卫者，温和而坚定。',
    ESTJ:'天生的管理者，重规则、重效率。',
    ESFJ:'热心肠的组织者，最在意身边人的冷暖。',
    ISTP:'冷静的动手派，问题来了再出手，出手就解决。',
    ISFP:'审美在线的探险家，活在自己的节奏里。',
    ESTP:'行动派，敢闯敢试，享受当下的刺激。',
    ESFP:'天生的舞台中心，把快乐带给所有人。'
  };

  var idx = 0, dims = {E:0,I:0,S:0,N:0,T:0,F:0,J:0,P:0};
  var qText = document.getElementById('qText');
  var optA = document.getElementById('optA');
  var optB = document.getElementById('optB');
  var qNow = document.getElementById('qNow');
  var qBar = document.getElementById('qBar');
  var qBox = document.querySelector('.q-box');
  var qResult = document.getElementById('qResult');

  function render(){
    qNow.textContent = idx + 1;
    qBar.style.width = (idx / Q.length * 100) + '%';
    qText.textContent = Q[idx][0] + '？';
    var pair = Q[idx][1].split('|');
    optA.textContent = pair[0];
    optB.textContent = pair[1];
  }

  function answer(which){
    var tag = Q[idx][1].split('|')[which].split('|')[1];
    dims[tag]++;
    idx++;
    if(idx < Q.length){ render(); }
    else{ finish(); }
  }

  function finish(){
    qBox.style.display = 'none';
    qBar.style.width = '100%';
    var code =
      (dims.E >= dims.I ? 'E' : 'I') +
      (dims.S >= dims.N ? 'S' : 'N') +
      (dims.T >= dims.F ? 'T' : 'F') +
      (dims.J >= dims.P ? 'J' : 'P');
    document.getElementById('rCode').textContent = code;
    document.getElementById('rNick').textContent = '「' + NICK[code] + '」';
    document.getElementById('rDesc').textContent = DESC[code];
    document.getElementById('rDims').textContent =
      '精力 ' + dims.E + ':' + dims.I + '（E/I）· 认知 ' + dims.S + ':' + dims.N + '（S/N）· 决策 ' + dims.T + ':' + dims.F + '（T/F）· 生活 ' + dims.J + ':' + dims.P + '（J/P）';
    qResult.style.display = 'block';
  }

  optA.addEventListener('click', function(){ answer(0); });
  optB.addEventListener('click', function(){ answer(1); });
  document.getElementById('rRetry').addEventListener('click', function(){
    idx = 0; dims = {E:0,I:0,S:0,N:0,T:0,F:0,J:0,P:0};
    qResult.style.display = 'none';
    qBox.style.display = 'block';
    render();
  });

  render();
})();
</script>

<style>
#mbtiApp .q-progress{font-family:var(--font-mono);font-size:0.8rem;color:var(--ink-3);margin-bottom:0.5rem}
#mbtiApp .q-bar{height:4px;background:var(--panel);border-radius:99px;overflow:hidden;margin-bottom:2rem}
#mbtiApp .q-bar div{height:100%;background:var(--accent);transition:width 200ms ease}
#mbtiApp .q-text{font-family:var(--font-display);font-weight:700;font-size:1.3rem;margin-bottom:1.5rem}
#mbtiApp .q-opts{display:grid;gap:0.75rem}
#mbtiApp .q-opt{width:100%;text-align:left;font-family:var(--font-body);font-size:1rem;padding:1rem 1.3rem;border:1.5px solid var(--line);background:var(--panel);color:var(--ink);border-radius:12px;cursor:pointer;transition:border-color 160ms ease,background-color 160ms ease,color 160ms ease}
#mbtiApp .q-opt:hover{border-color:var(--accent);background:var(--accent);color:var(--accent-ink)}
#mbtiApp .q-result{border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:2rem;text-align:center}
#mbtiApp .mbti-code{font-family:var(--font-display);font-weight:700;font-size:3.2rem;color:var(--accent);letter-spacing:0.05em}
#mbtiApp .mbti-nick{font-size:1.1rem;color:var(--ink);margin-top:0.25rem}
#mbtiApp .mbti-desc{color:var(--ink-2);margin:1rem auto 0;max-width:42ch}
#mbtiApp .mbti-dims{font-family:var(--font-mono);font-size:0.75rem;color:var(--ink-3);margin:1.2rem 0 1.5rem}
</style>
