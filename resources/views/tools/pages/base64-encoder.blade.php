<div class="field">
  <span class="fl">文本 → Base64</span>
  <textarea id="b64Plain" rows="5" placeholder="输入任意文本（支持中文）"></textarea>
  <output id="b64Out"></output>
</div>
<div class="field">
  <span class="fl">Base64 → 文本</span>
  <textarea id="b64Encoded" rows="5" placeholder="粘贴 Base64 字符串"></textarea>
  <output id="b64Decoded"></output>
</div>
<div class="btn-row">
  <label><input type="checkbox" id="b64UrlSafe"> URL 安全模式（+/ 换成 -_ ）</label>
  <button class="btn" id="btnCopy">复制编码结果</button>
</div>
<div class="msg" id="b64Msg"></div>

<script>
(function(){
  var plain = document.getElementById('b64Plain');
  var encoded = document.getElementById('b64Encoded');
  var out = document.getElementById('b64Out');
  var decoded = document.getElementById('b64Decoded');
  var urlSafe = document.getElementById('b64UrlSafe');
  var msg = document.getElementById('b64Msg');

  function applySafe(s){
    return urlSafe.checked ? s.replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,'') : s;
  }
  function unapplySafe(s){
    s = s.replace(/-/g,'+').replace(/_/g,'/');
    while(s.length % 4) s += '=';
    return s;
  }
  function encodeUtf8(str){
    return btoa(String.fromCharCode(...new TextEncoder().encode(str)));
  }
  function decodeUtf8(b64){
    return new TextDecoder().decode(Uint8Array.from(atob(b64), c => c.charCodeAt(0)));
  }

  plain.addEventListener('input', function(){
    out.textContent = '';
    if(!plain.value){ return; }
    try{
      out.textContent = applySafe(encodeUtf8(plain.value));
    }catch(e){
      out.textContent = '编码失败：' + e.message;
    }
  });

  encoded.addEventListener('input', function(){
    decoded.textContent = '';
    msg.className = 'msg';
    msg.textContent = '';
    var v = encoded.value.replace(/\s+/g,'');
    if(!v) return;
    try{
      decoded.textContent = decodeUtf8(unapplySafe(v));
    }catch(e){
      msg.className = 'msg err';
      msg.textContent = '✗ 不是合法的 Base64 字符串';
    }
  });

  urlSafe.addEventListener('change', function(){
    if(plain.value) plain.dispatchEvent(new Event('input'));
    if(encoded.value) encoded.dispatchEvent(new Event('input'));
  });

  document.getElementById('btnCopy').addEventListener('click', function(){
    if(!out.textContent) return;
    navigator.clipboard.writeText(out.textContent).then(function(){
      msg.className = 'msg ok';
      msg.textContent = '✓ 已复制到剪贴板';
    });
  });
})();
</script>
