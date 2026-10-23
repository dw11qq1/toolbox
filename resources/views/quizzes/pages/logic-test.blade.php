<div id="logicApp">
  <div class="q-progress"><span id="gNow">1</span> / 10 · 得分 <span id="gScore">0</span></div>
  <div class="q-bar"><div id="gBar" style="width:2%"></div></div>
  <div class="q-box" id="gBox">
    <p class="q-text" id="gText"></p>
    <div class="q-opts" id="gOpts"></div>
  </div>
  <div class="q-explain" id="gExplain" style="display:none">
    <p id="gExplainText"></p>
    <button class="btn" id="gNext">下一题 →</button>
  </div>
  <div class="q-result" id="gResult" style="display:none">
    <p class="fl">最终得分</p>
    <div class="mbti-code" id="rScore"></div>
    <p class="mbti-nick" id="rLevel"></p>
    <p class="mbti-desc" id="rLevelDesc"></p>
    <button class="btn" id="rRetry">再测一次</button>
  </div>
</div>

<script>
(function(){
  var Q = [
    ["数列接龙：2, 6, 12, 20, 30, ？", ["40","42","36"], "B", "相邻差为 4、6、8、10，下一个差 12，30+12=42。"],
    ["所有的猫都怕水。小花是一只猫。所以","小花怕水|A","小花不怕水|B","无法确定|C","A", "标准三段论：大前提 + 小前提直接推出结论。"],
    ["数列接龙：1, 1, 2, 3, 5, 8, ？", ["11","13","12"], "B", "斐波那契数列：前两项之和等于后一项，5+8=13。"],
    ["甲比乙高，丙比甲高。三人中最矮的是","甲|A","乙|B","丙|C","B", "丙 > 甲 > 乙，最矮的是乙。"],
    ["数列接龙：81, 27, 9, 3, ？", ["1","0","2"], "A", "每次除以 3：3÷3=1。"],
    ["钟表 3 点整，时针与分针的夹角是多少度","90 度|A","120 度|B","60 度|C","A", "3 点整时针指 12，分针指 3，每大格 30 度，3×30=90。"],
    ["一些学生参加了考试。所有参加考试的人都通过了。小明没通过。所以","小明不是学生|A","题目有矛盾|B","小明没参加考试|C","C", "由「所有参加的都通过」的逆否命题：没通过 → 没参加。注意 A 不成立，他可能是学生但没参加考试。"],
    ["数列接龙：1, 4, 9, 16, 25, ？", ["30","36","49"], "B", "完全平方数列：6²=36。"],
    ["5 个人排队，小红不在两端，小红左边恰有 2 人。小红排第几","第 2 位|A","第 3 位|B","第 4 位|C","B", "左边 2 人 → 第 3 位，且不在两端，符合条件。"],
    ["一个数加上 8 再乘 2 等于 44，这个数是","14|A","18|B","22|C","A", "逆运算：44÷2−8=14。"]
  ];

  var idx = 0, correct = 0;
  var gText = document.getElementById('gText');
  var gOpts = document.getElementById('gOpts');

  function render(){
    document.getElementById('gNow').textContent = idx + 1;
    document.getElementById('gBar').style.width = (idx / Q.length * 100) + '%';
    gText.textContent = Q[idx][0];
    gOpts.innerHTML = '';
    ['A','B','C'].forEach(function(letter, i){
      var b = document.createElement('button');
      b.className = 'q-opt';
      b.textContent = letter + ' . ' + Q[idx][1][i];
      b.addEventListener('click', function(){ pick(letter, b); });
      gOpts.appendChild(b);
    });
  }

  function pick(letter, btn){
    var right = letter === Q[idx][3];
    if(right) correct++;
    document.getElementById('gScore').textContent = correct;
    Array.prototype.forEach.call(gOpts.children, function(el){ el.disabled = true; });
    btn.style.borderColor = right ? 'var(--accent)' : '#c0492f';
    btn.style.color = right ? 'var(--accent)' : '#c0492f';
    var ex = document.getElementById('gExplain');
    document.getElementById('gExplainText').textContent = (right ? '✓ 答对了。' : '✗ 正确答案是 ' + Q[idx][3] + '。') + ' 解析：' + Q[idx][4];
    ex.style.display = 'block';
  }

  document.getElementById('gNext').addEventListener('click', function(){
    document.getElementById('gExplain').style.display = 'none';
    idx++;
    if(idx < Q.length) render(); else finish();
  });

  function finish(){
    document.getElementById('gBox').style.display = 'none';
    document.getElementById('gBar').style.width = '100%';
    document.getElementById('rScore').textContent = correct + ' / 10';
    var lv = correct >= 9 ? ['逻辑超群', '几乎没有陷阱能困住你，去做算法题吧。']
      : correct >= 7 ? ['逻辑优秀', '推理链抓得很稳，偶尔在细节上打个滑。']
      : correct >= 5 ? ['逻辑在线', '基础扎实，多做几轮会有明显提升。']
      : ['逻辑待机中', '别灰心——逻辑是练出来的，看看每题解析再战一轮。'];
    document.getElementById('rLevel').textContent = '「' + lv[0] + '」';
    document.getElementById('rLevelDesc').textContent = lv[1];
    document.getElementById('gResult').style.display = 'block';
  }

  document.getElementById('rRetry').addEventListener('click', function(){
    idx = 0; correct = 0;
    document.getElementById('gScore').textContent = 0;
    document.getElementById('gResult').style.display = 'none';
    document.getElementById('gBox').style.display = 'block';
    render();
  });

  render();
})();
</script>

<style>
#logicApp .q-progress{font-family:var(--font-mono);font-size:0.8rem;color:var(--ink-3);margin-bottom:0.5rem}
#logicApp .q-bar{height:4px;background:var(--panel);border-radius:99px;overflow:hidden;margin-bottom:2rem}
#logicApp .q-bar div{height:100%;background:var(--accent);transition:width 200ms ease}
#logicApp .q-text{font-family:var(--font-display);font-weight:700;font-size:1.25rem;margin-bottom:1.5rem}
#logicApp .q-opts{display:grid;gap:0.75rem}
#logicApp .q-opt{width:100%;text-align:left;font-family:var(--font-body);font-size:1rem;padding:1rem 1.3rem;border:1.5px solid var(--line);background:var(--panel);color:var(--ink);border-radius:12px;cursor:pointer;transition:border-color 160ms ease}
#logicApp .q-opt:hover:not(:disabled){border-color:var(--accent)}
#logicApp .q-opt:disabled{cursor:default;opacity:0.75}
#logicApp .q-explain{border:1px solid var(--line);border-left:3px solid var(--accent);background:var(--panel);border-radius:0 10px 10px 0;padding:1rem 1.3rem;margin-top:1.25rem}
#logicApp .q-explain p{font-size:0.92rem;color:var(--ink-2);margin-bottom:0.75rem}
#logicApp .q-result{border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:2rem;text-align:center}
#logicApp .mbti-code{font-family:var(--font-display);font-weight:700;font-size:2.6rem;color:var(--accent)}
#logicApp .mbti-nick{font-size:1.05rem;color:var(--ink)}
#logicApp .mbti-desc{color:var(--ink-2);margin:0.8rem auto 1.4rem;max-width:40ch}
</style>
