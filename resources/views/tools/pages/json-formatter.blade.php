<div class="field">
  <span class="fl">输入 JSON</span>
  <textarea id="jsonInput" rows="10" placeholder='{"name":"工具匣","version":1}'></textarea>
</div>
<div class="btn-row">
  <button class="btn" id="btnFormat">格式化（2 空格缩进）</button>
  <button class="btn" id="btnMinify">压缩成一行</button>
  <button class="btn" id="btnClear">清空</button>
</div>
<div class="field">
  <span class="fl">结果（可编辑，可再次格式化）</span>
  <textarea id="jsonOutput" rows="10" readonly></textarea>
</div>
<div class="msg" id="jsonMsg"></div>

<script>
(function(){
  var input = document.getElementById('jsonInput');
  var output = document.getElementById('jsonOutput');
  var msg = document.getElementById('jsonMsg');

  function fail(e){
    // 从错误信息里提取位置，换算行号列号
    var m = /position (\d+)/.exec(e.message);
    var pos = '位置 ' + (m ? m[1] : '?');
    if(m){
      var before = input.value.slice(0, +m[1]);
      var line = before.split('\n').length;
      var col = m[1] - before.lastIndexOf('\n');
      pos = '第 ' + line + ' 行，第 ' + col + ' 列';
    }
    msg.className = 'msg err';
    msg.textContent = '✗ JSON 不合法：' + pos;
  }

  function run(space){
    msg.className = 'msg';
    msg.textContent = '';
    if(!input.value.trim()){ msg.className='msg err'; msg.textContent='✗ 请先输入 JSON'; return; }
    try{
      output.value = JSON.stringify(JSON.parse(input.value), null, space);
      msg.className = 'msg ok';
      msg.textContent = '✓ JSON 合法';
    }catch(e){ fail(e); }
  }

  document.getElementById('btnFormat').addEventListener('click', function(){ run(2); });
  document.getElementById('btnMinify').addEventListener('click', function(){ run(0); });
  document.getElementById('btnClear').addEventListener('click', function(){
    input.value=''; output.value=''; msg.className='msg'; msg.textContent='';
  });
})();
</script>
