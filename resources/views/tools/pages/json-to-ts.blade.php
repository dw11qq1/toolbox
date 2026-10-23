<div class="field">
  <span class="fl">JSON 输入（接口名、选项可调）</span>
  <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;margin-bottom:0.5rem">
    <label style="font-size:0.85rem">根接口名<input id="jtName" value="Root" style="width:120px;margin-left:0.4rem;padding:0.3rem 0.5rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px;font-family:var(--font-mono)"></label>
    <label style="font-size:0.85rem"><input id="jtReadonly" type="checkbox" checked> readonly 字段</label>
    <label style="font-size:0.85rem"><input id="jtOptional" type="checkbox" checked> 数组元素缺失的字段标 <code>?</code></label>
  </div>
  <textarea id="jtInput" rows="8" placeholder='{"id": 1, "name": "张三", "tags": ["php", "laravel"], "profile": {"city": "杭州", "vip": true}}' style="width:100%;font-family:var(--font-mono);font-size:0.85rem;padding:0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px"></textarea>
  <button class="btn" id="jtRun" style="margin-top:0.5rem;padding:0.35rem 1.2rem">生成 TypeScript</button>
</div>

<div class="field" id="jtOut" style="display:none">
  <span class="fl">TypeScript 接口</span>
  <textarea id="jtCode" rows="14" readonly style="width:100%;font-family:var(--font-mono);font-size:0.82rem;padding:0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px;white-space:pre"></textarea>
  <button class="btn" id="jtCopy" style="margin-top:0.5rem;padding:0.3rem 1rem">复制</button>
</div>

<div class="explain">
  <h3>公式 & 这个工具是什么意思</h3>
  <p class="ex-formula">类型推断规则：<br>对象 → interface（嵌套对象自动拆独立接口）<br>数组 → 元素类型联合（T1 | T2）或数组类型<br>缺失字段 → 可选属性 <code>?:</code></p>
  <p class="ex-text">对接第三方 API 时，手写 interface 又慢又容易抄错。这个工具把响应 JSON 一次性转成 TypeScript 类型定义：<b>嵌套对象</b>会拆成独立命名的 interface 并自动去重（同结构的字段共用一个类型）；<b>数组里元素结构不一致</b>时，取所有元素字段的并集，缺失的自动标可选 <code>?</code>——这正是接口文档没写清楚、靠真实响应反推类型时最需要的语义。</p>
</div>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };
  function pascal(s){
    return String(s).replace(/[^a-zA-Z0-9]+(.)?/g, function(_, c){ return c ? c.toUpperCase() : ''; })
      .replace(/^(.)/, function(c){ return c.toUpperCase(); }) || 'Item';
  }
  function scalarType(v){
    if(v === null) return 'null';
    if(Array.isArray(v)) return 'array';
    return typeof v === 'object' ? 'object' : (typeof v === 'number' ? 'number' : typeof v === 'boolean' ? 'boolean' : 'string');
  }

  function generate(root, rootName, readonly, optional){
    var out = [];
    var cache = {};
    function interfaceName(key){
      return pascal(key);
    }
    function emitInterface(name, obj, keyHint){
      var sig = JSON.stringify(Object.keys(obj));
      if(cache[sig] && cache[sig] !== name){
        return cache[sig];
      }
      cache[sig] = name;
      var lines = ['interface ' + name + ' {'];
      Object.keys(obj).forEach(function(k){
        var v = obj[k];
        var prop = /^[A-Za-z_$][A-Za-z0-9_$]*$/.test(k) ? k : JSON.stringify(k);
        var t = typeOf(v, k, keyHint ? keyHint + ' ' + k : k);
        lines.push('  ' + (readonly ? 'readonly ' : '') + prop + (optional ? '?:' : ':') + ' ' + t + ';');
      });
      lines.push('}');
      out.push(lines.join('\n'));
      return name;
    }
    function typeOf(v, key, path){
      var t = scalarType(v);
      if(t === 'object'){
        return emitInterface(interfaceName(path || key || 'Nested'), v, path);
      }
      if(t === 'array'){
        if(!v.length) return 'unknown[]';
        var types = [];
        v.forEach(function(el){ var et = typeOf(el, key, path); if(types.indexOf(et) < 0) types.push(et); });
        if(types.length === 1) return types[0] + '[]';
        return '(' + types.join(' | ') + ')[]';
      }
      return t;
    }
    // 根节点：对象拆 interface；数组/标量给 type 别名
    var rootType = scalarType(root);
    var result;
    if(rootType === 'object'){
      emitInterface(rootName, root, '');
      result = out.join('\n\n');
    } else {
      result = 'type ' + rootName + ' = ' + typeOf(root, rootName, '') + ';';
    }
    return result;
  }

  $('jtRun').addEventListener('click', function(){
    var src = $('jtInput').value.trim();
    if(!src){ alert('先贴一段 JSON'); return; }
    var data;
    try { data = JSON.parse(src); }
    catch(e){ $('jtOut').style.display = 'block'; $('jtCode').value = '// JSON 解析失败：' + e.message; return; }
    var code = generate(data, $('jtName').value.trim() || 'Root', $('jtReadonly').checked, $('jtOptional').checked);
    $('jtCode').value = code;
    $('jtOut').style.display = 'block';
  });

  $('jtCopy').addEventListener('click', function(){
    $('jtCode').select();
    navigator.clipboard.writeText($('jtCode').value);
    $('jtCopy').textContent = '已复制 ✓';
    setTimeout(function(){ $('jtCopy').textContent = '复制'; }, 1200);
  });
})();
</script>
