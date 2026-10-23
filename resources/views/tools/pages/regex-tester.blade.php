<div class="field">
  <span class="fl">正则表达式</span>
  <input type="text" id="rePat" value="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}">
</div>
<div class="field">
  <span class="fl">标志符</span>
  <div class="btn-row" style="gap:0.9rem;flex-wrap:wrap;font-size:0.85rem;color:var(--ink-2)">
    <label><input type="checkbox" id="reG" checked> g 全局</label>
    <label><input type="checkbox" id="reI"> i 忽略大小写</label>
    <label><input type="checkbox" id="reM"> m 多行</label>
    <label><input type="checkbox" id="reS"> s 点匹配换行</label>
    <label><input type="checkbox" id="reU"> u Unicode</label>
  </div>
</div>
<div class="field">
  <span class="fl">测试文本</span>
  <textarea id="reText" rows="6">联系我：boss@example.com 备用 spare@test.org
手机号 13812345678，官网 https://www.example.com
日期 2026-10-23，IP 192.168.1.1
无效邮箱 hello@、@nope.com</textarea>
</div>
<div class="field">
  <span class="fl">常用正则速查（点击填入）</span>
  <div class="btn-row" style="gap:0.4rem;flex-wrap:wrap" id="rePresets"></div>
</div>
<div id="reResult" style="display:none">
  <span class="fl">高亮结果（<b id="reCount">0</b> 处匹配）</span>
  <div id="reHighlight" style="font-family:var(--font-mono);font-size:0.85rem;line-height:1.9;background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:0.8rem 1rem;margin-top:0.3rem;white-space:pre-wrap;word-break:break-all"></div>
  <span class="fl" style="margin-top:0.9rem">匹配明细</span>
  <div id="reMatches" style="margin-top:0.3rem"></div>
</div>

<style>
#reHighlight mark{background:rgba(37,99,235,0.18);color:var(--accent);border-radius:4px;padding:0 2px;font-weight:600}
.re-match{font-family:var(--font-mono);font-size:0.82rem;border:1px solid var(--line);border-radius:8px;padding:0.5rem 0.8rem;margin-bottom:0.4rem;color:var(--ink-2)}
.re-match b{color:var(--ink)}
.re-err{color:#d64545;font-size:0.85rem}
.re-chip{font-family:var(--font-mono);font-size:0.75rem;padding:0.25rem 0.7rem;border:1px solid var(--line);border-radius:999px;cursor:pointer;background:var(--panel);color:var(--ink-2)}
.re-chip:hover{border-color:var(--accent);color:var(--accent)}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  var PRESETS = [
    ['邮箱', '[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}'],
    ['手机号', '1[3-9]\\d{9}'],
    ['URL', 'https?://[^\\s，。]+'],
    ['IPv4', '\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}'],
    ['日期', '\\d{4}-\\d{2}-\\d{2}'],
    ['中文', '[\\u4e00-\\u9fa5]+'],
    ['数字', '-?\\d+(\\.\\d+)?'],
  ];
  $('rePresets').innerHTML = PRESETS.map(function(p, i){
    return '<span class="re-chip" data-i="' + i + '">' + p[0] + '</span>';
  }).join('');
  $('rePresets').addEventListener('click', function(e){
    var chip = e.target.closest('.re-chip');
    if(!chip) return;
    $('rePat').value = PRESETS[+chip.getAttribute('data-i')][1];
    render();
  });

  function esc(s){
    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function flags(){
    var f = '';
    if($('reG').checked) f += 'g';
    if($('reI').checked) f += 'i';
    if($('reM').checked) f += 'm';
    if($('reS').checked) f += 's';
    if($('reU').checked) f += 'u';
    return f;
  }

  function render(){
    var pat = $('rePat').value;
    var text = $('reText').value;
    var box = $('reResult');
    if(!pat){ box.style.display = 'none'; return; }

    var re;
    try{
      re = new RegExp(pat, flags().indexOf('g') === -1 ? flags() + 'g' : flags());
    }catch(err){
      box.style.display = 'block';
      $('reHighlight').innerHTML = '<span class="re-err">正则语法错误：' + esc(err.message) + '</span>';
      $('reCount').textContent = '—';
      $('reMatches').innerHTML = '';
      return;
    }

    var matches = [], m, guard = 0;
    while((m = re.exec(text)) !== null){
      matches.push({ i: m.index, s: m[0], groups: m.slice(1) });
      if(m[0] === ''){ re.lastIndex++; if(++guard > 10000) break; }
      if(++guard > 100000) break;
    }

    var html = '', pos = 0;
    matches.forEach(function(m){
      html += esc(text.slice(pos, m.i)) + '<mark>' + esc(m.s) + '</mark>';
      pos = m.i + m.s.length;
    });
    html += esc(text.slice(pos));
    $('reHighlight').innerHTML = html || '<span style="color:var(--ink-3)">（空）</span>';
    $('reCount').textContent = matches.length;

    $('reMatches').innerHTML = matches.slice(0, 50).map(function(m, i){
      var gs = m.groups && m.groups.length
        ? '　<span style="color:var(--ink-3)">分组: ' + m.groups.map(function(g){ return g === undefined ? '—' : esc(g); }).join(' | ') + '</span>'
        : '';
      return '<div class="re-match">#' + (i + 1) + ' 位置 ' + m.i + '：<b>' + (esc(m.s) || '(空)') + '</b>' + gs + '</div>';
    }).join('') || '<div class="re-match">没有匹配</div>';
    box.style.display = 'block';
  }

  ['rePat', 'reText'].forEach(function(id){ $(id).addEventListener('input', render); });
  ['reG', 'reI', 'reM', 'reS', 'reU'].forEach(function(id){ $(id).addEventListener('change', render); });
  render();
})();
</script>

<div class="explain">
  <h3>玩法 & 常用技巧</h3>
  <p class="ex-formula">字符组 [abc] 任一匹配 · \d 数字 \w 字母数字下划线 \s 空白<br>量词 * 0+ · + 1+ · ? 0或1 · {2,5} 2到5 次 · (?:…) 不捕获分组</p>
  <p class="ex-text">左边写正则、右边贴文本，<b>匹配结果实时高亮</b>，下方列出每处位置和捕获分组。写接口校验时最常用的套路：<b>先用速查里的现成表达式改</b>，别从零硬写。两个高频坑：一是 <b>点号要写 \.</b>（裸点号能匹配任何字符，邮箱正则裸写会放过 hello#qq.com）；二是别用正则解析 HTML 或 JSON——那是字符串的地基，正则只适合「在一堆文本里捞出符合形状的片段」。</p>
</div>
