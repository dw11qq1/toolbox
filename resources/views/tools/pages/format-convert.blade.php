<div class="field">
  <span class="fl">输入</span>
  <div style="display:flex;gap:0.8rem;align-items:center;margin-bottom:0.5rem;flex-wrap:wrap">
    <select id="fcSrc" style="font-size:0.9rem;padding:0.3rem 0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px">
      <option value="json">JSON</option>
      <option value="yaml">YAML</option>
      <option value="xml">XML</option>
      <option value="csv">CSV</option>
    </select>
    <button class="btn" id="fcSwap" style="padding:0.25rem 0.8rem">⇄ 交换方向</button>
    <span style="font-size:0.8rem;color:var(--ink-3)">→</span>
    <select id="fcDst" style="font-size:0.9rem;padding:0.3rem 0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px">
      <option value="yaml">YAML</option>
      <option value="json">JSON</option>
      <option value="xml">XML</option>
      <option value="csv">CSV</option>
    </select>
  </div>
  <textarea id="fcInput" rows="9" placeholder='粘贴 JSON / YAML / XML / CSV，任意两种格式互转' style="width:100%;font-family:var(--font-mono);font-size:0.85rem;padding:0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px"></textarea>
  <button class="btn" id="fcRun" style="margin-top:0.5rem;padding:0.35rem 1.2rem">转换</button>
</div>

<div class="field" id="fcOut" style="display:none">
  <span class="fl">输出</span>
  <textarea id="fcResult" rows="9" readonly style="width:100%;font-family:var(--font-mono);font-size:0.85rem;padding:0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px;white-space:pre"></textarea>
  <button class="btn" id="fcCopy" style="margin-top:0.5rem;padding:0.3rem 1rem">复制</button>
</div>

<div class="explain">
  <h3>公式 & 这个工具是什么意思</h3>
  <p class="ex-formula">转换链路：源格式 → JS 对象（中间表示）→ 目标格式<br>CSV 规则：首行为表头，RFC 4180 引号转义<br>XML 规则：同名兄弟标签 → 数组，其余 → 嵌套对象</p>
  <p class="ex-text">这四种格式的典型场景：<b>YAML</b> 是 Docker Compose、GitHub Actions、宝塔配置的语法；<b>CSV</b> 是 Excel 导入导出的通用语；<b>XML</b> 还活跃在老系统接口和 Android 布局里；<b>JSON</b> 是现代 API 的标配。工具的思路是「源 → 中间对象 → 目标」，所以支持任意方向互转。注意两个边界：XML 属性（<code>&lt;a id="1"&gt;</code> 里的 id）会被展平为 <code>@id</code> 键；CSV 只适合<b>扁平</b>的数组数据，嵌套对象转 CSV 会先 JSON 字符串化保平安。</p>
</div>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  /* ---------- CSV（RFC 4180） ---------- */
  function parseCsv(text){
    var rows = [], row = [], cell = '', inQ = false;
    for(var i = 0; i < text.length; i++){
      var c = text[i];
      if(inQ){
        if(c === '"'){ if(text[i+1] === '"'){ cell += '"'; i++; } else inQ = false; }
        else cell += c;
      } else {
        if(c === '"') inQ = true;
        else if(c === ','){ row.push(cell); cell = ''; }
        else if(c === '\n' || c === '\r'){ if(c === '\r' && text[i+1] === '\n') i++; row.push(cell); cell = ''; if(row.length > 1 || row[0] !== '') rows.push(row); row = []; }
        else cell += c;
      }
    }
    if(cell !== '' || row.length){ row.push(cell); rows.push(row); }
    if(!rows.length) return [];
    var head = rows[0];
    return rows.slice(1).map(function(r){
      var o = {};
      head.forEach(function(h, i){ o[h.trim()] = i < r.length ? smart(r[i]) : ''; });
      return o;
    });
  }
  function smart(v){
    if(v === '') return '';
    if(/^-?\d+(\.\d+)?$/.test(v) && v.length < 16) return Number(v);
    if(v === 'true') return true;
    if(v === 'false') return false;
    return v;
  }
  function toCsv(data){
    var arr = Array.isArray(data) ? data : [data];
    if(!arr.length || typeof arr[0] !== 'object' || arr[0] === null) return '数据需为对象数组才能转 CSV';
    var keys = [];
    arr.forEach(function(o){ Object.keys(o).forEach(function(k){ if(keys.indexOf(k) < 0) keys.push(k); }); });
    function esc(v){
      var s = (v === null || v === undefined) ? '' : (typeof v === 'object' ? JSON.stringify(v) : String(v));
      return /[",\n\r]/.test(s) ? '"' + s.split('"').join('""') + '"' : s;
    }
    return keys.join(',') + '\n' + arr.map(function(o){ return keys.map(function(k){ return esc(o[k]); }).join(','); }).join('\n');
  }

  /* ---------- XML（浏览器 DOMParser / 手写序列化） ---------- */
  function xmlToObj(text){
    var doc = new DOMParser().parseFromString(text, 'application/xml');
    if(doc.querySelector('parsererror')) throw new Error('XML 解析失败：' + doc.querySelector('parsererror').textContent.slice(0, 120));
    function walk(el){
      var obj = {};
      Array.prototype.forEach.call(el.attributes || [], function(a){ obj['@' + a.name] = a.value; });
      var kids = {};
      Array.prototype.forEach.call(el.children, function(child){
        (kids[child.nodeName] = kids[child.nodeName] || []).push(child);
      });
      Object.keys(kids).forEach(function(name){
        var list = kids[name];
        var vals = list.map(walk);
        if(list.length === 1){
          var v = vals[0];
          var isLeaf = !list[0].children.length && !list[0].attributes.length;
          if(isLeaf){
            var txt = list[0].textContent.trim();
            obj[name] = smart(txt);
          } else obj[name] = v;
        } else obj[name] = vals;
      });
      if(!Object.keys(obj).length) return smart(el.textContent.trim());
      return obj;
    }
    var root = doc.documentElement;
    var out = {};
    out[root.nodeName] = walk(root);
    return out;
  }
  function objToXml(data, rootName){
    function esc(s){ return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function build(name, v, indent){
      var pad = new Array(indent + 1).join('  ');
      if(Array.isArray(v)) return v.map(function(item){ return build(name, item, indent); }).join('\n');
      if(v !== null && typeof v === 'object'){
        var inner = '';
        Object.keys(v).forEach(function(k){
          if(k[0] === '@'){ return; }
          inner += (inner ? '\n' : '') + build(k, v[k], indent + 1);
        });
        var attrs = Object.keys(v).filter(function(k){ return k[0] === '@'; }).map(function(k){ return ' ' + k.slice(1) + '="' + esc(v[k]) + '"'; }).join('');
        if(!inner) return pad + '<' + name + attrs + '/>';
        return pad + '<' + name + attrs + '>\n' + inner + '\n' + pad + '</' + name + '>';
      }
      return pad + '<' + name + '>' + esc(v) + '</' + name + '>';
    }
    var root = rootName || (data && typeof data === 'object' && !Array.isArray(data) ? Object.keys(data)[0] : 'root');
    var body = (data && typeof data === 'object' && !Array.isArray(data)) ? build(root, data[root], 0) : build(root, data, 0);
    return '<?xml version="1.0" encoding="UTF-8"?>\n' + body;
  }

  /* ---------- YAML（js-yaml 懒加载 + 手写降级序列化） ---------- */
  var yamlReady = false;
  function loadYaml(cb){
    if(window.jsyaml){ yamlReady = true; cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/js-yaml@4.1.0/dist/js-yaml.min.js';
    s.onload = function(){ yamlReady = true; cb(); };
    s.onerror = function(){ cb(new Error('YAML 组件加载失败（需联网一次），请先在 JSON/XML/CSV 间转换')); };
    document.head.appendChild(s);
  }
  function yamlToObj(text){
    if(!yamlReady) throw new Error('YAML 组件尚未就绪，请再点一次');
    return jsyaml.load(text);
  }
  function toYaml(data){
    if(!yamlReady) throw new Error('YAML 组件尚未就绪，请再点一次');
    return jsyaml.dump(data, { indent: 2, lineWidth: 120, noRefs: true });
  }

  /* ---------- 调度 ---------- */
  function parseInput(fmt, text){
    if(fmt === 'json'){
      var v = JSON.parse(text);
      return v;
    }
    if(fmt === 'yaml') return yamlToObj(text);
    if(fmt === 'xml') return xmlToObj(text);
    if(fmt === 'csv') return parseCsv(text);
  }
  function renderOutput(fmt, data){
    if(fmt === 'json') return JSON.stringify(data, null, 2);
    if(fmt === 'yaml') return toYaml(data);
    if(fmt === 'xml') return objToXml(data, null);
    if(fmt === 'csv') return toCsv(data);
  }

  $('fcSwap').addEventListener('click', function(){
    var s = $('fcSrc').value;
    $('fcSrc').value = $('fcDst').value;
    $('fcDst').value = s;
    if($('fcOut').style.display === 'block' && $('fcResult').value){
      $('fcInput').value = $('fcResult').value;
      $('fcRun').click();
    }
  });

  $('fcRun').addEventListener('click', function(){
    var src = $('fcSrc').value, dst = $('fcDst').value, text = $('fcInput').value;
    if(!text.trim()){ alert('先贴内容'); return; }
    $('fcOut').style.display = 'block';
    var go = function(){
      try {
        $('fcResult').value = renderOutput(dst, parseInput(src, text));
      } catch(e){
        $('fcResult').value = '// 转换失败：' + e.message;
      }
    };
    if(src === 'yaml' || dst === 'yaml') loadYaml(function(err){ go(); });
    else go();
  });

  $('fcCopy').addEventListener('click', function(){
    $('fcResult').select();
    navigator.clipboard.writeText($('fcResult').value);
    $('fcCopy').textContent = '已复制 ✓';
    setTimeout(function(){ $('fcCopy').textContent = '复制'; }, 1200);
  });
})();
</script>
