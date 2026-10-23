<div class="field">
  <span class="fl">输入时间（时间戳 秒/毫秒自动识别，或点下面选日期）</span>
  <input type="text" id="ntInput" style="font-family:var(--font-mono)">
  <div class="btn-row" style="gap:0.6rem;margin-top:0.5rem;flex-wrap:wrap;align-items:center">
    <input type="datetime-local" id="ntPick" style="font-size:0.9rem;padding:0.35rem 0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px">
    <button class="btn" id="ntNow" style="padding:0.35rem 0.9rem">用当前时间</button>
  </div>
</div>

<div id="ntResult" style="display:none">
  <div class="nt-hero" id="ntHero"></div>
  <table class="nt-table">
    <tbody id="ntRows"></tbody>
  </table>
</div>

<style>
.nt-hero{margin:0.4rem 0 0.9rem;padding:1.1rem 1.2rem;background:var(--panel);border:1px solid var(--line);border-radius:12px}
.nt-hero b{font-size:1.6rem;color:var(--accent);font-weight:700}
.nt-hero span{display:block;margin-top:0.3rem;font-size:0.85rem;color:var(--ink-3);font-family:var(--font-mono)}
.nt-table{width:100%;border-collapse:collapse;font-size:0.88rem}
.nt-table td{padding:0.55rem 0.4rem;border-bottom:1px solid var(--line);vertical-align:top}
.nt-table td:first-child{color:var(--ink-3);width:7.5em;white-space:nowrap}
.nt-table td:last-child{font-family:var(--font-mono);color:var(--ink-2)}
.nt-table td b{color:var(--ink)}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };
  var WEEK = ['日', '一', '二', '三', '四', '五', '六'];

  function pad(n){ return (n < 10 ? '0' : '') + n; }
  function fmt(d, withTime){
    var s = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) + '（周' + WEEK[d.getDay()] + '）';
    if(withTime !== false) s += ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
    return s;
  }

  // 相对人话
  function rel(diffMs){
    var diff = Math.abs(diffMs);
    var past = diffMs < 0;
    var min = 60 * 1000, hr = 60 * min, day = 24 * hr;
    var suffix = past ? '前' : '后';
    if(diff < 45 * 1000) return past ? '刚刚' : '马上';
    if(diff < hr) return Math.round(diff / min) + ' 分钟' + suffix;
    if(diff < day){
      var h = Math.round(diff / hr);
      if(past && h <= 12) return h + ' 小时前';
      if(!past && h <= 12) return h + ' 小时后';
      return past ? '半天多之前' : '半天多之后';
    }
    var days = Math.round(diff / day);
    if(days === 1) return past ? '昨天' : '明天';
    if(days === 2) return past ? '前天' : '后天';
    if(days <= 7) return days + ' 天' + suffix;
    if(days <= 30) return Math.round(days / 7) + ' 周' + suffix;
    if(days <= 365) return Math.round(days / 30) + ' 个月' + suffix;
    return (diff / day / 365).toFixed(1) + ' 年' + suffix;
  }

  // 与常见节点的距离
  function row(name, html){ return '<tr><td>' + name + '</td><td>' + html + '</td></tr>'; }

  function render(){
    var raw = $('ntInput').value.trim();
    var d = null;
    if(raw){
      if(/^\d{10}$/.test(raw)) d = new Date(parseInt(raw) * 1000);
      else if(/^\d{13}$/.test(raw)) d = new Date(parseInt(raw));
      else if(/^\d+$/.test(raw)) d = new Date(parseInt(raw) * (raw.length >= 13 ? 1 : 1000));
      else{
        var t = Date.parse(raw);
        if(!isNaN(t)) d = new Date(t);
      }
    }
    if(!d && $('ntPick').value){
      d = new Date($('ntPick').value);
    }
    var box = $('ntResult');
    if(!d || isNaN(d.getTime())){ box.style.display = 'none'; return; }

    var now = new Date();
    var diffMs = d.getTime() - now.getTime();

    $('ntHero').innerHTML = '<b>' + rel(diffMs) + '</b><span>' + fmt(d) + '　·　Unix 秒 ' + Math.floor(d.getTime() / 1000) + '　·　毫秒 ' + d.getTime() + '</span>';

    var absDiff = Math.abs(diffMs);
    var dayMs = 86400000;
    var days = Math.round(absDiff / dayMs);
    var html = '';
    html += row('绝对时间', '<b>' + fmt(d) + '</b>');
    html += row('相差', days >= 1 ? days.toLocaleString() + ' 天（约 ' + (days / 30.44).toFixed(1) + ' 个月 / ' + (days / 365.25).toFixed(2) + ' 年）'
                                : Math.round(absDiff / 60000).toLocaleString() + ' 分钟');
    // 目标那天的说法
    var sameYear = d.getFullYear() === now.getFullYear();
    html += row('口头说法', past ? (sameYear ? '今年 ' : d.getFullYear() + ' 年 ') + (d.getMonth() + 1) + ' 月 ' + d.getDate() + ' 日，星期' + WEEK[d.getDay()]
                                  : (sameYear ? '今年 ' : d.getFullYear() + ' 年 ') + (d.getMonth() + 1) + ' 月 ' + d.getDate() + ' 日，星期' + WEEK[d.getDay()]);
    // 最近整点
    var nearestHr = new Date(Math.round(d.getTime() / 3600000) * 3600000);
    html += row('最近整点', fmt(nearestHr));
    // 一年后的今天
    var y1 = new Date(d); y1.setFullYear(y1.getFullYear() + 1);
    var w1 = Math.round((y1.getTime() - d.getTime()) / dayMs);
    html += row('一年后的今天', fmt(y1) + '（相差 ' + w1 + ' 天）');
    // 年底倒计时
    var nye = new Date(d.getFullYear(), 11, 31, 23, 59, 59);
    var toNye = Math.ceil((nye.getTime() - d.getTime()) / dayMs);
    html += row('距那年年底', toNye >= 0 ? '还有 <b>' + toNye + '</b> 天' : '已过去 ' + (-toNye) + ' 天');
    html += row('Unix 时间戳', '秒 ' + Math.floor(d.getTime() / 1000) + '　·　毫秒 ' + d.getTime());

    $('ntRows').innerHTML = html;
    box.style.display = 'block';
  }

  $('ntInput').addEventListener('input', function(){ render(); });
  $('ntPick').addEventListener('change', function(){
    var d = new Date(this.value);
    if(!isNaN(d.getTime())) $('ntInput').value = String(d.getTime());
    render();
  });
  $('ntNow').addEventListener('click', function(){
    var d = new Date();
    $('ntInput').value = String(Math.floor(d.getTime() / 1000));
    render();
  });

  $('ntNow').click();
})();
</script>

<div class="explain">
  <h3>玩法 & 为什么有用</h3>
  <p class="ex-formula">相对描述 = 当前时间与目标时间的差值，按 分钟/小时/天/周/月/年 分档取整<br>Unix 时间戳：秒 = 毫秒 ÷ 1000（10 位是秒，13 位是毫秒）</p>
  <p class="ex-text">写博客、发动态、做日志系统时，<b>「3 天前」永远比「2026-10-20T14:32:08Z」好读</b>——这个工具就是给你这种人话翻译：贴时间戳（10 位秒、13 位毫秒都认）或选个日期，立刻得到「3 个月后」「昨天」这类描述，附带星期几、年底倒计时和两种格式的时间戳，前端后端对着抄。顺带记住一个常识：<b>10 位时间戳是秒、13 位是毫秒</b>，混用是最常见的时间 bug 来源。</p>
</div>
