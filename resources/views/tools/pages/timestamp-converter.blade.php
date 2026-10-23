<div class="field">
  <span class="fl">Unix 时间戳 → 日期（秒或毫秒自动识别）</span>
  <input type="text" id="tsInput" placeholder="例如 1761200000">
  <output id="tsToDate"></output>
</div>
<div class="field">
  <span class="fl">日期 → 时间戳（如 2026-10-23 20:00:00）</span>
  <input type="text" id="dateInput" placeholder="2026-10-23 20:00:00">
  <output id="dateToTs"></output>
</div>
<div class="btn-row">
  <button class="btn" id="btnNow">填入当前时间</button>
</div>
<div class="field">
  <span class="fl">当前时间戳（每秒刷新）</span>
  <output id="nowTs">&nbsp;</output>
</div>

<script>
(function(){
  var tsInput = document.getElementById('tsInput');
  var tsToDate = document.getElementById('tsToDate');
  var dateInput = document.getElementById('dateInput');
  var dateToTs = document.getElementById('dateToTs');

  function pad(n){ return String(n).padStart(2,'0'); }
  function fmt(d){
    return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate())+' '
         + pad(d.getHours())+':'+pad(d.getMinutes())+':'+pad(d.getSeconds());
  }

  tsInput.addEventListener('input', function(){
    var v = tsInput.value.trim();
    tsToDate.textContent = '';
    if(!/^\d+$/.test(v)){ if(v) tsToDate.textContent = '请输入纯数字时间戳'; return; }
    var n = +v;
    if(v.length >= 13) n = Math.floor(n/1000); // 毫秒转秒
    var d = new Date(n*1000);
    if(isNaN(d.getTime())){ tsToDate.textContent = '超出可表示范围'; return; }
    tsToDate.textContent = fmt(d);
  });

  dateInput.addEventListener('input', function(){
    dateToTs.textContent = '';
    var v = dateInput.value.trim();
    if(!v) return;
    var d = new Date(v.replace(' ', 'T'));
    if(isNaN(d.getTime())){ dateToTs.textContent = '日期格式无法解析'; return; }
    dateToTs.textContent = Math.floor(d.getTime()/1000) + '（秒）/ ' + d.getTime() + '（毫秒）';
  });

  document.getElementById('btnNow').addEventListener('click', function(){
    var d = new Date();
    tsInput.value = Math.floor(d.getTime()/1000);
    dateInput.value = fmt(d);
    tsInput.dispatchEvent(new Event('input'));
    dateInput.dispatchEvent(new Event('input'));
  });

  var nowTs = document.getElementById('nowTs');
  function tick(){ nowTs.textContent = Math.floor(Date.now()/1000); }
  tick();
  setInterval(tick, 1000);
})();
</script>
