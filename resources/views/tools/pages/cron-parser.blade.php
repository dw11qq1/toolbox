<div class="field">
  <span class="fl">Cron 表达式（分 时 日 月 周）</span>
  <input type="text" id="crExpr" value="30 8 * * 1-5">
</div>
<div class="btn-row">
  <button class="btn" id="crParse">解析</button>
</div>
<div class="field">
  <span class="fl">人话解读</span>
  <output id="crHuman"></output>
</div>
<div class="field">
  <span class="fl">最近 5 次执行时间</span>
  <div id="crNext" style="margin-top:0.3rem"></div>
</div>
<div class="field">
  <span class="fl">常用示例（点击填入）</span>
  <div class="btn-row" style="gap:0.4rem;flex-wrap:wrap" id="crPresets"></div>
</div>

<style>
.cr-next{font-family:var(--font-mono);font-size:0.88rem;padding:0.35rem 0;border-bottom:1px dashed var(--line);color:var(--ink-2)}
.cr-next b{color:var(--accent)}
.cr-chip{font-size:0.78rem;padding:0.25rem 0.7rem;border:1px solid var(--line);border-radius:999px;cursor:pointer;background:var(--panel);color:var(--ink-2)}
.cr-chip:hover{border-color:var(--accent);color:var(--accent)}
.cr-err{color:#d64545;font-size:0.88rem}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  var PRESETS = [
    ['每分钟', '* * * * *'],
    ['每小时第 0 分', '0 * * * *'],
    ['每天 08:30', '30 8 * * *'],
    ['工作日 08:30', '30 8 * * 1-5'],
    ['每周一 09:00', '0 9 * * 1'],
    ['每月 1 号零点', '0 0 1 * *'],
    ['每 15 分钟', '*/15 * * * *'],
    ['每天 22:00', '0 22 * * *'],
  ];
  $('crPresets').innerHTML = PRESETS.map(function(p, i){
    return '<span class="cr-chip" data-i="' + i + '">' + p[0] + '</span>';
  }).join('');
  $('crPresets').addEventListener('click', function(e){
    var chip = e.target.closest('.cr-chip');
    if(!chip) return;
    $('crExpr').value = PRESETS[+chip.getAttribute('data-i')][1];
    run();
  });

  var WEEK = ['日', '一', '二', '三', '四', '五', '六'];

  // 解析单个字段 → 该字段允许的值集合（null 表示 *）
  function parseField(str, min, max){
    if(str === undefined || str === '') return null;
    if(str === '*') return null;
    var set = new Set();
    str.split(',').forEach(function(part){
      var step = 1;
      var seg = part;
      if(seg.indexOf('/') !== -1){
        var pp = seg.split('/');
        seg = pp[0];
        step = parseInt(pp[1]);
        if(isNaN(step) || step < 1) throw new Error('步长无效: ' + part);
      }
      var a = min, b = max;
      if(seg !== '*' && seg !== ''){
        if(seg.indexOf('-') !== -1){
          var rr = seg.split('-');
          a = parseInt(rr[0]); b = parseInt(rr[1]);
          if(isNaN(a) || isNaN(b)) throw new Error('范围无效: ' + part);
        }else{
          a = b = parseInt(seg);
          if(isNaN(a)) throw new Error('数值无效: ' + part);
        }
      }
      if(a < min || b > max || a > b) throw new Error('值超出范围: ' + part + '（允许 ' + min + '-' + max + '）');
      for(var v = a; v <= b; v += step) set.add(v);
    });
    if(!set.size) throw new Error('字段为空');
    return set;
  }

  function humanField(set, min, max, unit){
    if(set === null) return '每' + unit;
    var arr = Array.from(set).sort(function(a, b){ return a - b; });
    if(arr.length === max - min + 1) return '每' + unit;
    if(arr.length === 1) return '第 ' + arr[0] + ' ' + unit;
    // 连续区间
    var isRange = arr.length > 1 && arr[arr.length - 1] - arr[0] === arr.length - 1;
    if(isRange) return '第 ' + arr[0] + ' 到 ' + arr[arr.length - 1] + ' ' + unit;
    return '第 ' + arr.slice(0, 8).join('、') + (arr.length > 8 ? ' 等' : '') + ' ' + unit;
  }

  function describe(f){
    var parts = [];
    // 月
    if(f.mon !== null){
      var mons = Array.from(f.mon).sort(function(a, b){ return a - b; });
      parts.push((mons.length === 1 ? mons[0] + ' 月' : mons.join('、') + ' 月') + '的');
    }
    // 日 / 周
    var dayDesc;
    if(f.dom !== null && f.dow !== null){
      dayDesc = humanField(f.dom, 1, 31, '号') + ' 且' + humanField(f.dow, 0, 6, '周') + '（注意：日和周同时限制时，多数实现按「或」处理）';
    }else if(f.dom !== null){
      dayDesc = humanField(f.dom, 1, 31, '号');
    }else if(f.dow !== null){
      var dows = Array.from(f.dow).sort(function(a, b){ return a - b; }).map(function(d){ return '周' + WEEK[d]; });
      dayDesc = dows.length === 7 ? '每天' : '每' + dows.join('、');
    }else{
      dayDesc = '每天';
    }
    parts.push(dayDesc);
    // 时:分
    var hDesc, mDesc;
    if(f.hour === null && f.min === null){
      return parts.join('') + '每分钟执行';
    }
    if(f.hour === null){
      hDesc = '每小时';
      mDesc = f.min === null ? '第 0 分' : '第 ' + Array.from(f.min).sort(function(a, b){ return a - b; }).join('、') + ' 分';
      return parts.join('') + hDesc + '的' + mDesc + '执行';
    }
    var hs = Array.from(f.hour).sort(function(a, b){ return a - b; });
    var ms = f.min === null ? [0] : Array.from(f.min).sort(function(a, b){ return a - b; });
    if(f.min === null && hs.length > 1){
      return parts.join('') + '每天 ' + hs.map(function(h){ return pad(h) + ':00'; }).join('、') + ' 执行';
    }
    if(hs.length === 1 && ms.length === 1) return parts.join('') + '每天 ' + pad(hs[0]) + ':' + pad(ms[0]) + ' 执行';
    if(hs.length <= 6 && ms.length === 1) return parts.join('') + '每天 ' + hs.map(function(h){ return pad(h) + ':' + pad(ms[0]); }).join('、') + ' 执行';
    return parts.join('') + '在 ' + humanField(f.hour, 0, 23, '点') + '的' + (f.min === null ? '第 0 分' : humanField(f.min, 0, 59, '分')) + '执行';
  }

  function pad(n){ return (n < 10 ? '0' : '') + n; }

  function matches(d, f){
    if(f.min !== null && !f.min.has(d.getMinutes())) return false;
    if(f.hour !== null && !f.hour.has(d.getHours())) return false;
    if(f.mon !== null && !f.mon.has(d.getMonth() + 1)) return false;
    var domOk = f.dom === null || f.dom.has(d.getDate());
    var dowOk = f.dow === null || f.dow.has(d.getDay());
    if(f.dom !== null && f.dow !== null) return domOk || dowOk;   // 标准 cron 行为：取或
    return domOk && dowOk;
  }

  function run(){
    var raw = $('crExpr').value.trim();
    try{
      var fields = raw.split(/\s+/);
      if(fields.length !== 5) throw new Error('需要 5 个字段（分 时 日 月 周），现在是 ' + fields.length + ' 个');
      var f = {
        min: parseField(fields[0], 0, 59),
        hour: parseField(fields[1], 0, 23),
        dom: parseField(fields[2], 1, 31),
        mon: parseField(fields[3], 1, 12),
        dow: parseField(fields[4], 0, 6),
      };
      $('crHuman').textContent = describe(f);

      // 最近 5 次：逐分钟向前搜，最多搜 2 年
      var out = [];
      var d = new Date();
      d.setSeconds(0, 0);
      d.setMinutes(d.getMinutes() + 1);
      var limit = new Date(d.getTime() + 2 * 366 * 24 * 3600 * 1000);
      while(out.length < 5 && d < limit){
        if(matches(d, f)){
          out.push(d);
        }
        d.setMinutes(d.getMinutes() + 1);
      }
      $('crNext').innerHTML = out.length
        ? out.map(function(x, i){
            return '<div class="cr-next">#' + (i + 1) + '　' + x.getFullYear() + '-' + pad(x.getMonth() + 1) + '-' + pad(x.getDate()) +
              '（周' + WEEK[x.getDay()] + '）<b> ' + pad(x.getHours()) + ':' + pad(x.getMinutes()) + '</b></div>';
          }).join('')
        : '<div class="cr-err">两年内没有匹配的执行时间，检查一下表达式。</div>';
    }catch(err){
      $('crHuman').innerHTML = '<span class="cr-err">' + err.message + '</span>';
      $('crNext').innerHTML = '';
    }
  }

  $('crParse').addEventListener('click', run);
  $('crExpr').addEventListener('input', run);
  run();
})();
</script>

<div class="explain">
  <h3>字段含义 & 常见坑</h3>
  <p class="ex-formula">分(0-59)　时(0-23)　日(1-31)　月(1-12)　周(0-6，0 是周日)<br>* 任意 · */n 每 n · a-b 范围 · a-b/n 范围内每 n · 逗号分隔多个值</p>
  <p class="ex-text">最经典的坑就一个：<b>「日」和「周」同时写了具体值时，标准 cron 按「或」执行</b>——<code>0 0 1 * 1</code> 不是「1 号且周一」，而是「1 号<b>或</b>周一」，一个月可能跑四五次。想要「每月 1 号」就别写周字段。另外 <b>*/n 是从字段起点开始数</b>：<code>*/10</code> 在「分」字段是 0、10、20…，而 <code>5-59/10</code> 才是 5、15、25…。配置宝塔或服务器的定时任务前，先在这里预览最近 5 次执行时间，能避开 90% 的「怎么没跑」问题。</p>
</div>
