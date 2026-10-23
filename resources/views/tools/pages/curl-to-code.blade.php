<div class="field">
  <span class="fl">cURL 命令（从浏览器 DevTools / 文档直接复制粘贴）</span>
  <textarea id="ccInput" rows="6" placeholder="curl -X POST https://api.example.com/v1/users -H 'Content-Type: application/json' -H 'Authorization: Bearer xxx' -d '{&quot;name&quot;:&quot;汪洋&quot;}'" style="width:100%;font-family:var(--font-mono);font-size:0.85rem;padding:0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px"></textarea>
  <button class="btn" id="ccRun" style="margin-top:0.5rem;padding:0.35rem 1.2rem">生成请求代码</button>
</div>

<div class="field" id="ccOut" style="display:none">
  <span class="fl">目标语言</span>
  <div class="btn-row" style="gap:0.4rem;flex-wrap:wrap" id="ccTabs"></div>
  <textarea id="ccCode" rows="16" readonly style="width:100%;margin-top:0.5rem;font-family:var(--font-mono);font-size:0.8rem;padding:0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px;white-space:pre"></textarea>
  <button class="btn" id="ccCopy" style="margin-top:0.5rem;padding:0.3rem 1rem">复制</button>
</div>

<div class="explain">
  <h3>公式 & 这个工具是什么意思</h3>
  <p class="ex-formula">解析对象：URL · 方法(-X) · 请求头(-H) · 请求体(-d/--data-raw) · Basic 认证(-u)</p>
  <p class="ex-text">调第三方接口的第一步永远是「先用 cURL 跑通」，但落地到项目里要写成项目语言的代码。这个工具把 cURL 命令解析成结构化请求（方法、URL、请求头、请求体、认证），一键生成 <b>JavaScript fetch / axios、PHP cURL、Laravel Http、Python requests、Go net/http</b> 六种写法，认证方式自动识别（Bearer 进 Authorization 头，-u 转 Basic Auth）。省掉的是每次查「PHP curl 设置 header 是哪个函数」这种纯体力活。</p>
</div>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  function parseCurl(cmd){
    var s = cmd.replace(/\\\n/g, ' ').replace(/\\"/g, '\u0001').replace(/\\'/g, '\u0002');
    var tokens = s.match(/("(?:[^"])*"|'(?:[^'])*'|\S+)/g) || [];
    tokens = tokens.map(function(t){
      return t.replace(/^"(.*)"$/, '$1').replace(/^'(.*)$/, '$1').replace(/"$/, '')
        .replace(/\u0001/g, '"').replace(/\u0002/g, "'");
    });
    var req = { method: 'GET', url: '', headers: {}, body: null, auth: null };
    for(var i = 0; i < tokens.length; i++){
      var t = tokens[i];
      if(/^-X$|^--request$/.test(t)){ req.method = (tokens[++i] || 'GET').toUpperCase(); }
      else if(/^-H$|^--header$/.test(t)){
        var h = tokens[++i] || '';
        var ci = h.indexOf(':');
        if(ci > 0) req.headers[h.slice(0, ci).trim()] = h.slice(ci + 1).trim();
      }
      else if(/^-d$|^--data$|^--data-raw$|^--data-binary$|^--data-urlencode$/.test(t)){
        req.body = tokens[++i] || '';
        if(req.method === 'GET') req.method = 'POST';
      }
      else if(/^-u$|^--user$/.test(t)){ req.auth = tokens[++i] || ''; }
      else if(/^-b$|^--cookie$/.test(t)){ var ck = tokens[++i] || ''; if(ck.indexOf('=') > -1 && !req.headers['Cookie']) req.headers['Cookie'] = ck; }
      else if(/^(--compressed|--insecure|-k|-s|--silent|-L|--location|-v)$/.test(t)){ /* 忽略 */ }
      else if(!t.startsWith('-') && !req.url && i > 0 && t !== 'curl'){ req.url = t; }
    }
    if(!req.url && tokens[1]) req.url = tokens[1];
    return req;
  }

  var LANGS = ['JavaScript fetch', 'JavaScript axios', 'PHP cURL', 'Laravel Http', 'Python requests', 'Go net/http'];

  function q(str, lang){
    var quote = lang === 'go' ? '"' : (lang === 'py' || lang === 'php' ? "'" : '`');
    if(quote !== '`' && str.indexOf(quote) > -1) str = str.split(quote).join(quote === '"' ? '\\"' : "\\'");
    if(quote === '`' && str.indexOf('`') > -1) str = str.split('`').join('\\`');
    return quote + str + quote;
  }
  function phpStr(str){ return "'" + str.replace(/\\/g, '\\\\').replace(/'/g, "\\'") + "'"; }

  function gen(req, lang){
    var url = req.url, m = req.method, hs = req.headers, body = req.body;
    if(lang === 'JavaScript fetch'){
      var L = "const res = await fetch(" + q(url, 'js') + ", {\n  method: '" + m + "',\n";
      var hk = Object.keys(hs);
      if(hk.length) L += "  headers: {\n" + hk.map(function(k){ return "    '" + k + "': '" + hs[k] + "'"; }).join(',\n') + "\n  },\n";
      if(body !== null && m !== 'GET') L += "  body: " + q(body, 'js') + ",\n";
      L += "});\nconst data = await res.json();";
      return L;
    }
    if(lang === 'JavaScript axios'){
      var L2 = "const { data } = await axios({\n  method: '" + m.toLowerCase() + "',\n  url: '" + url + "'";
      var hk2 = Object.keys(hs);
      if(hk2.length) L2 += ",\n  headers: {\n" + hk2.map(function(k){ return "    '" + k + "': '" + hs[k] + "'"; }).join(',\n') + "\n  }";
      if(body !== null && m !== 'GET') L2 += ",\n  data: " + q(body, 'js');
      return L2 + "\n});";
    }
    if(lang === 'PHP cURL'){
      var L3 = "$ch = curl_init(" + phpStr(url) + ");\ncurl_setopt_array($ch, [\n    CURLOPT_RETURNTRANSFER => true,\n    CURLOPT_CUSTOMREQUEST  => " + phpStr(m) + ",\n";
      var hk3 = Object.keys(hs);
      if(hk3.length) L3 += "    CURLOPT_HTTPHEADER => [\n" + hk3.map(function(k){ return "        " + phpStr(k + ': ' + hs[k]); }).join(',\n') + ",\n    ],\n";
      if(req.auth) L3 += "    CURLOPT_USERPWD => " + phpStr(req.auth) + ",\n";
      if(body !== null && m !== 'GET') L3 += "    CURLOPT_POSTFIELDS => " + phpStr(body) + ",\n";
      L3 += "]);\n$response = curl_exec($ch);\ncurl_close($ch);";
      return L3;
    }
    if(lang === 'Laravel Http'){
      var L4 = "use Illuminate\\Support\\Facades\\Http;\n\n$response = Http::" + m.toLowerCase() + "(" + phpStr(url) + ")";
      var hk4 = Object.keys(hs);
      if(hk4.length) L4 += "\n    ->withHeaders([\n" + hk4.map(function(k){ return "        " + phpStr(k) + " => " + phpStr(hs[k]); }).join(',\n') + ",\n    ])";
      if(req.auth) { var ap = req.auth.split(':'); L4 += "\n    ->withBasicAuth(" + phpStr(ap[0] || '') + ", " + phpStr(ap[1] || '') + ")"; }
      if(body !== null && m !== 'GET'){
        var isJson = /json/i.test(hs['Content-Type'] || '');
        if(isJson){ try { L4 += "\n    ->body(" + phpStr(JSON.stringify(JSON.parse(body))) + ")"; } catch(e){ L4 += "\n    ->body(" + phpStr(body) + ")"; } }
        else L4 += "\n    ->body(" + phpStr(body) + ")";
      }
      L4 += ";\n\n$data = $response->json();";
      return L4;
    }
    if(lang === 'Python requests'){
      var L5 = "import requests\n\nres = requests.request(\n    " + q(m, 'py') + ",\n    " + q(url, 'py');
      var hk5 = Object.keys(hs);
      if(hk5.length) L5 += ",\n    headers={\n" + hk5.map(function(k){ return "        " + q(k, 'py') + ": " + q(hs[k], 'py'); }).join(',\n') + "\n    }";
      if(req.auth) { var ap2 = req.auth.split(':'); L5 += ",\n    auth=(" + q(ap2[0] || '', 'py') + ", " + q(ap2[1] || '', 'py') + ")"; }
      if(body !== null && m !== 'GET') L5 += ",\n    data=" + q(body, 'py');
      L5 += ",\n)\nprint(res.json())";
      return L5;
    }
    if(lang === 'Go net/http'){
      var L6 = 'req, _ := http.NewRequest("' + m + '", "' + url + '", ';
      L6 += (body !== null && m !== 'GET') ? 'strings.NewReader(' + q(body, 'go') + ')' : 'nil';
      L6 += ')\n';
      var hk6 = Object.keys(hs);
      hk6.forEach(function(k){ L6 += 'req.Header.Set("' + k + '", "' + hs[k] + '")\n'; });
      if(req.auth){ L6 += 'req.SetBasicAuth("' + (req.auth.split(':')[0] || '') + '", "' + (req.auth.split(':')[1] || '') + '")\n'; }
      L6 += '\nres, err := http.DefaultClient.Do(req)\nif err != nil {\n    log.Fatal(err)\n}\ndefer res.Body.Close()';
      return L6;
    }
    return '';
  }

  var reqCache = null, curLang = LANGS[0];
  $('ccRun').addEventListener('click', function(){
    var cmd = $('ccInput').value.trim();
    if(!cmd){ alert('先贴一条 curl 命令'); return; }
    reqCache = parseCurl(cmd);
    if(!reqCache.url){ $('ccOut').style.display = 'block'; $('ccCode').value = '// 没解析到 URL，检查命令里的地址'; return; }
    var tabs = '';
    LANGS.forEach(function(l){
      tabs += '<button class="btn ccTab" data-lang="' + l + '" style="padding:0.25rem 0.8rem;font-size:0.8rem;opacity:' + (l === curLang ? '1' : '0.55') + '">' + l + '</button>';
    });
    $('ccTabs').innerHTML = tabs;
    Array.prototype.forEach.call(document.querySelectorAll('.ccTab'), function(b){
      b.addEventListener('click', function(){
        curLang = b.getAttribute('data-lang');
        Array.prototype.forEach.call(document.querySelectorAll('.ccTab'), function(x){ x.style.opacity = x === b ? '1' : '0.55'; });
        $('ccCode').value = gen(reqCache, curLang);
      });
    });
    $('ccCode').value = gen(reqCache, curLang);
    $('ccOut').style.display = 'block';
  });

  $('ccCopy').addEventListener('click', function(){
    $('ccCode').select();
    navigator.clipboard.writeText($('ccCode').value);
    $('ccCopy').textContent = '已复制 ✓';
    setTimeout(function(){ $('ccCopy').textContent = '复制'; }, 1200);
  });
})();
</script>
