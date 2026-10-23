<div class="pb-world">
<div class="field">
  <span class="fl">📌 底板尺寸</span>
  <div class="btn-row" style="gap:0.6rem;flex-wrap:wrap">
    <select id="pbSize" style="font-size:0.9rem;padding:0.35rem 0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px">
      <option value="16">16 × 16（小底板）</option>
      <option value="20">20 × 20</option>
      <option value="29" selected>29 × 29（标准大方板）</option>
      <option value="40">40 × 40（拼两块）</option>
      <option value="58">58 × 58（拼四块）</option>
    </select>
    <button class="btn pb-pat" data-pat="love" style="padding:0.35rem 0.8rem">🩷 爱心</button>
    <button class="btn pb-pat" data-pat="star" style="padding:0.35rem 0.8rem">⭐ 星星</button>
    <button class="btn pb-pat" data-pat="mushroom" style="padding:0.35rem 0.8rem">🍄 小蘑菇</button>
    <button class="btn pb-pat" data-pat="bunny" style="padding:0.35rem 0.8rem">🐰 小兔子</button>
    <button class="btn" id="pbClear" style="padding:0.35rem 0.9rem">清空当前图层</button>
  </div>
</div>

<div class="field">
  <span class="fl">🎨 色卡型号</span>
  <div class="btn-row" style="gap:0.6rem;flex-wrap:wrap">
    <select id="pbCard" style="font-size:0.9rem;padding:0.35rem 0.6rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:6px">
      <option value="mard" selected>MARD 221 色</option>
      <option value="perler">Perler 48 色（美国）</option>
      <option value="artkal">Artkal S 系 159 色</option>
    </select>
    <button class="btn" id="pbConvert" style="padding:0.35rem 0.9rem">图纸色号转换到当前色卡</button>
  </div>
  <p style="font-size:0.75rem;color:var(--ink-3);margin-top:0.4rem">换色卡后点「转换」，图纸上每个豆会自动映射到当前色卡里最接近的色号（买不同牌子的豆时用）。</p>
</div>

<div class="field">
  <span class="fl">📷 照片转图纸（自动匹配当前色卡）</span>
  <div class="btn-row" style="gap:0.8rem;flex-wrap:wrap;align-items:center">
    <input type="file" id="pbFile" accept="image/*" style="font-size:0.8rem">
    <label style="font-size:0.8rem;color:var(--ink-2);display:flex;align-items:center;gap:0.3rem">
      <input type="checkbox" id="pbDither"> 抖动算法（照片更细腻）
    </label>
    <label style="font-size:0.8rem;color:var(--ink-2);display:flex;align-items:center;gap:0.3rem">
      <input type="checkbox" id="pbWhiteOut"> 接近白色不加豆（做镂空）
    </label>
  </div>
  <p style="font-size:0.75rem;color:var(--ink-3);margin-top:0.4rem">照片会缩到当前底板尺寸并逐格匹配最接近的色号，结果写入<b>当前图层</b>。像素画/Logo 建议关抖动，照片开抖动更自然。</p>
</div>

<div class="field">
  <span class="fl">🥞 图层（点豆只影响选中图层；底层半透明显示方便对照）</span>
  <div class="btn-row" id="pbLayers" style="gap:0.4rem;flex-wrap:wrap"></div>
</div>

<div class="field">
  <span class="fl">🧸 色板（点击选色 · 当前：<b id="pbCurName">MARD F2</b>）</span>
  <div id="pbPalette"></div>
</div>

<div class="field">
  <span class="fl">🧩 图纸（按住拖动点豆 · 右键擦除）
    <span style="float:right;display:inline-flex;gap:0.3rem;align-items:center">
      <button class="btn" id="pbZoomOut" style="padding:0.1rem 0.6rem;font-size:0.85rem">−</button>
      <span id="pbZoomLabel" style="font-size:0.75rem;color:var(--ink-3);font-family:var(--font-mono)">18px</span>
      <button class="btn" id="pbZoomIn" style="padding:0.1rem 0.6rem;font-size:0.85rem">＋</button>
    </span>
  </span>
  <div id="pbWrap" style="overflow:auto;max-width:100%;border-radius:14px;scrollbar-width:thin">
    <canvas id="pbCanvas" style="border:1px solid #ffd9e5;border-radius:14px;touch-action:none;cursor:crosshair;background:#fffdf9"></canvas>
  </div>
  <p style="font-size:0.75rem;color:var(--ink-3);margin-top:0.4rem">圆角方块的糖果小豆，带一点点高光——放大方便逐格微调，画布超出时上下左右滚动，比例永远是 1:1 不变形。</p>
</div>

<div class="field" id="pbCountWrap" style="display:none">
  <span class="fl">🛒 用豆清单（照着买豆就行，建议多备 5%~10% 损耗）</span>
  <div id="pbCountsActive" style="margin-top:0.3rem"></div>
  <span class="fl" id="pbTotalTitle" style="margin-top:0.8rem;display:none">全部图层合计</span>
  <div id="pbCountsTotal"></div>
</div>

<div class="btn-row" style="gap:0.6rem;flex-wrap:wrap">
  <button class="btn" id="pbSave" style="padding:0.35rem 0.9rem">保存到本地</button>
  <button class="btn" id="pbLoad" style="padding:0.35rem 0.9rem">载入</button>
  <button class="btn" id="pbExport" style="padding:0.35rem 0.9rem">导出 PNG</button>
  <button class="btn" id="pbPdf" style="padding:0.35rem 0.9rem">导出 PDF（图纸 + 用量清单）</button>
</div>
</div>

<style>
.pb-world .btn{background:linear-gradient(180deg,#ffa7c4,#ff7fa8);border:1px solid #f76c9d;color:#fff;border-radius:999px;box-shadow:0 1px 0 rgba(247,108,157,0.25)}
.pb-world .btn:hover{filter:brightness(1.06)}
.pb-world .btn:active{transform:translateY(1px)}
.pb-world select{border-color:#ffc9db!important;border-radius:999px!important}
.pb-world .pb-layer.on{border-color:#f76c9d;color:#e0447f}
.pb-world .pb-count{border-color:#ffd3e0;border-radius:999px}
.pb-world .pb-swatch.on{border-color:#f76c9d;box-shadow:0 0 0 2px #fff5f8,0 0 0 4px #f76c9d}
#pbPalette{max-width:560px}
.pb-group{display:flex;align-items:flex-start;gap:6px;margin-bottom:6px;flex-wrap:wrap}
.pb-group em{font-style:normal;font-family:var(--font-mono);font-size:0.7rem;color:var(--ink-3);width:52px;padding-top:4px;flex-shrink:0}
.pb-swatch{width:22px;height:22px;border-radius:22%;cursor:pointer;border:1px solid rgba(0,0,0,0.15);box-sizing:border-box;transition:transform .1s}
.pb-swatch:hover{transform:scale(1.2)}
.pb-swatch.on{border:2px solid var(--ink);transform:scale(1.2);box-shadow:0 0 0 2px var(--panel),0 0 0 4px var(--ink)}
.pb-layer{display:inline-flex;align-items:center;gap:0.35rem;font-size:0.8rem;padding:0.3rem 0.7rem;border:1px solid var(--line);border-radius:999px;cursor:pointer;background:var(--panel);color:var(--ink-2)}
.pb-layer.on{border-color:var(--ink);color:var(--ink);font-weight:600}
.pb-layer i{font-style:normal;cursor:pointer;opacity:0.7}
.pb-layer i:hover{opacity:1}
#pbCountsActive,#pbCountsTotal{display:flex;flex-wrap:wrap;gap:0.4rem}
.pb-count{display:inline-flex;align-items:center;gap:0.4rem;font-size:0.78rem;color:var(--ink-2);border:1px solid var(--line);border-radius:6px;padding:0.2rem 0.65rem 0.2rem 0.3rem}
.pb-count i{width:16px;height:16px;border-radius:4px;display:inline-block;border:1px solid rgba(0,0,0,0.15)}
.pb-count b{font-family:var(--font-mono)}
</style>

<script>
(function(){
  var $ = function(id){ return document.getElementById(id); };

  /* ================= 色卡库 ================= */
  var CARDS = {
    mard: { label: 'MARD 221', short: 'MARD', prefix: 'MARD', groups: [['A','黄·橙'],['B','绿'],['C','蓝·青'],['D','紫'],['E','粉'],['F','红'],['G','肤·棕'],['H','黑·白·灰'],['M','莫兰迪']],
      data:
        'A1 F9F0CD|A2 FBFBD4|A3 FAFC9F|A4 FFE953|A5 F4D738|A6 FDAD49|A7 FF7C2F|A8 EACA49|A9 FF995A|A10 FF9D55|A11 FFDD99|A12 FCB58F|A13 FFBB59|A14 FF6D40|A15 FDFF44|A16 FEF9AE|A17 FFE36E|A18 FECF98|A19 FD7B72|A20 EFCD67|A21 FFE395|A22 FFF3A4|A23 F3D5BF|A24 FBF8C9|A25 FFD67D|A26 FFBB27|' +
        'B1 E6EE32|B2 5BE419|B3 7CEE9D|B4 1EF942|B5 00BD35|B6 5AE8BA|B7 03AC88|B8 029D26|B9 26523A|B10 95D3C2|B11 5D722A|B12 156F40|B13 D9F794|B14 ADE945|B15 2E5132|B16 C6ED9C|B17 9BB13A|B18 E6EE49|B19 25B88C|B20 C2F0CC|B21 146A6B|B22 0B3C43|B23 303921|B24 EEFCA5|B25 4E846D|B26 8C7A36|B27 D1DCC1|B28 9EE5B9|B29 C5E254|B30 ECFBD0|B31 C4E6B5|B32 9BAB5A|' +
        'C1 E8FFE7|C2 BCF9F6|C3 A0E2FB|C4 42CCFF|C5 01ACEB|C6 50A9F0|C7 0188D3|C8 1054C0|C9 314BCA|C10 3EBCE2|C11 03B9B9|C12 1C334D|C13 CDE8FF|C14 D5FDFF|C15 23C4C6|C16 1757A8|C17 50D3EC|C18 1C3344|C19 1787A2|C20 0082BE|C21 BEDDFF|C22 67B4BE|C23 C2DCEB|C24 7DC4FF|C25 A9E5E5|C26 2F99B3|C27 EBF5FC|C28 BBCFED|C29 4B5BA3|' +
        'D1 AEB4F2|D2 858EDD|D3 3054AF|D4 182A84|D5 B843C5|D6 AC7BDE|D7 6E399A|D8 E2D3FF|D9 D5B9F8|D10 361B50|D11 B9BAE1|D12 DE9AD4|D13 B90295|D14 8B279B|D15 2F1F90|D16 E2E1EE|D17 C4D4F6|D18 A45EC7|D19 D8C3D7|D20 9C32B2|D21 9A009B|D22 333995|D23 EADAFC|D24 7786E5|D25 484FC7|D26 E9C3F6|' +
        'E1 FDD3CC|E2 FECDDF|E3 FF97C3|E4 E8649E|E5 F551A2|E6 FF346B|E7 C63578|E8 FFDBE9|E9 E970CC|E10 D33893|E11 FCDDD2|E12 FFA1C5|E13 B6006D|E14 FFD1BA|E15 F2CFD0|E16 FFECDE|E17 FFE2EA|E18 FFC9D6|E19 FFD2E7|E20 D8C7D1|E21 BD9DA1|E22 CC78A7|E23 937A8D|E24 F6E4F9|' +
        'F1 FD957B|F2 FC3D45|F3 F74941|F4 FC283C|F5 D80127|F6 B0443D|F7 971937|F8 BC0127|F9 E2677A|F10 A74D22|F11 6F201F|F12 FD4D6A|F13 DD422F|F14 FFA9AD|F15 C80020|F16 FFD9C8|F17 F79B71|F18 D37C46|F19 C1444A|F20 CD9391|F21 F4B1B4|F22 FFD0CB|F23 F57E66|F24 FCC1C4|F25 E54B4F|' +
        'G1 FFE2CE|G2 FFCAAA|G3 F4C3A5|G4 E1B383|G5 ED9435|G6 F59734|G7 9D5B3E|G8 592A21|G9 E6B483|G10 C88135|G11 E0C593|G12 EBBB83|G13 B7714A|G14 8D614C|G15 FCF9E0|G16 F2D9BA|G17 56403C|G18 FFE4CC|G19 E1943A|G20 A94023|G21 CB8E77|' +
        'H1 E2E2E2|H2 FFFFFF|H3 B3B3B3|H4 868686|H5 474747|H6 2C2C2C|H7 000000|H8 E7D6DB|H9 E4E7E3|H10 EEE9EA|H11 CECDD5|H12 FFF5ED|H13 F3E1C9|H14 CFD7D3|H15 98A6A8|H16 3B2F23|H17 F1EDED|H18 FFFDF0|H19 F6EFE2|H20 949FA3|H21 F7F3E4|H22 CACAD5|H23 9A9D94|' +
        'M1 BCC6B8|M2 8AA385|M3 697D80|M4 DACEBE|M5 D0CCAA|M6 B0A782|M7 B4A497|M8 B38281|M9 A58767|M10 C5B1BC|M11 9F7494|M12 644749|M13 D19066|M14 C77361|M15 757D7B' },
    perler: { label: 'Perler 48', short: 'P', prefix: '', groups: [['P','经典 48 色']],
      data:
        'P01 白 F5F5F2|P02 黑 202124|P03 黄 F4D83D|P04 奶油 F3E7B5|P05 红 C93535|P06 橙 E97832|P07 浅蓝 75B9DE|P08 深蓝 24528A|P09 浅绿 8CCB82|P10 绿 4D9852|P11 棕 78533D|P12 灰 97999A|P13 粉 E990AA|P14 浅棕 B98C66|P15 深绿 336B4A|P16 粉蓝 B5D9E9|P17 粉绿 B9DBB0|P18 紫 73518E|P19 淡紫 C8B5D7|P20 淡黄 F2E59A|P21 芝士黄 EBAE36|P22 铁锈 A84B35|P23 浅粉 F0BBCB|P24 蜜桃 F1B08D|P25 薄荷蓝 83CFCA|P26 青蓝 34A7A5|P27 钴蓝 31569B|P28 海军蓝 253650|P29 李紫 6C3F69|P30 洋红 B23D72|P31 珊瑚 E56059|P32 鲑粉 E99282|P33 黄褐 C6A477|P34 沙色 D8C29F|P35 浅灰 C8C9C7|P36 深灰 5C6062|P37 炭黑 3D4143|P38 象牙 F2EBD9|P39 奶糖 C88B3A|P40 姜饼 9A653F|P41 森林绿 294F3A|P42 奇异果 8DBC43|P43 青柠 B6D648|P44 水蓝 62C5DB|P45 天空蓝 8ED0ED|P46 长春花 7188C7|P47 薰衣草 A78AC4|P48 腮红 E4A6A4' },
    artkal: { label: 'Artkal S 159', short: 'S', prefix: '', groups: [['S','S 系 159 色']],
      data:
        'S01 FFFFFF|S02 FFA38B|S03 FF8200|S04 FA4616|S05 EE2737|S06 EF64A2|S07 97999B|S08 26D07C|S09 007371|S10 56B7E6|S11 0050B5|S12 9063CD|S13 000000|S14 FDDA24|S15 A72B2A|S16 674736|S17 7B4D35|S18 EAA794|S19 F8C1B8|S20 249E6B|S21 93C90E|S22 483698|S23 7D55C7|S24 1164C9|S25 EF60A3|S26 F04E98|S27 FFC72C|S28 EF95CF|S29 FBDB65|S30 A4E6E8|S31 B5E3D8|S32 FECB8B|S33 C5B4E3|S34 D50032|S35 F7CED7|S36 E06287|S37 8BD3E6|S38 BC204B|S39 FF7F41|S40 F99FC9|S41 9A5516|S42 A09F9D|S43 75787B|S44 9BCBEB|S45 00B2A9|S46 8EDD65|S47 A6631B|S48 FFC845|S49 973961|S50 FFB3AB|S51 FFF8DB|S52 F8E08E|S53 62A0E6|S54 0090DA|S55 ADDC91|S56 FF6A39|S57 B33D26|S58 BA0C2F|S59 5949A7|S60 9678D3|S61 E6DE77|S62 007C58|S63 4C5914|S64 2C2D65|S65 F0EC74|S66 EE5340|S67 ECC3B2|S68 E7B78A|S69 212721|S70 BABC16|S71 008522|S72 67D9DF|S73 48A9C5|S74 04A9C7|S75 0085AD|S76 00A7B5|S77 D9D9D6|S78 C8C9C7|S79 B1B3B3|S80 A2A569|S81 D5A286|S82 C58B68|S83 B15533|S84 AB5C57|S85 8A2A2B|S86 F29D04|S87 FF808B|S88 DA1884|S89 53565A|S90 FFC56E|S91 183028|S92 C69214|S93 EDC8A3|S94 EAA794|S95 E8927C|S96 FF8D6D|S97 E35205|S98 80B6E8|S99 5CA9CC|S100 5DB3CB|S101 00A3E1|S102 0077CF|S103 0067B9|S104 007DBA|S105 00859B|S106 0076A8|S107 007096|S108 E6C78A|S109 CEB888|S110 C6AA76|S111 A08629|S112 897630|S113 C3C6A8|S114 C0BB87|S115 B0AA7E|S116 A3AA83|S117 77744D|S118 5E6738|S119 98DBCE|S120 9BE3BF|S121 6BBBAE|S122 00BB7E|S123 31B700|S124 035F1D|S125 007864|S126 00685E|S127 DECDE7|S128 C98BDB|S129 DCB6C9|S130 DD74A1|S131 AD96DC|S132 EF426F|S133 C724B1|S134 8031A7|S135 9FAEE5|S136 B4B2E4|S137 5F8DDA|S138 5780D2|S139 5576D1|S140 3C62C3|S141 466CCA|S142 365ABD|S143 1E22AA|S144 002D72|S145 E9E186|S146 8C243D|S147 86C8BC|S148 724736|S149 FCD299|S150 8B84D7|S151 335525|S152 A7A4E0|S153 C6C4EA|S154 EBC29D|S155 C58B68|S156 63666A|S157 46494C|S158 191D19|S159 88888D' }
  };

  function parseCard(card){
    return card.data.split('|').map(function(s){
      var p = s.split(' ');
      var code = p[0];
      var hex = p[p.length - 1];
      var rest = p.length > 2 ? p.slice(1, -1).join(' ') : '';
      var name = (card.prefix ? card.prefix + ' ' : '') + code + (rest ? ' ' + rest : '');
      return [name, '#' + hex.toUpperCase(), code.slice(0, 1)];
    });
  }

  var cardKey = 'mard';
  var PAL = parseCard(CARDS.mard);
  var RGBX = PAL.map(function(c){ return hexRgb(c[1]); });
  var curName = 'MARD F2';

  function hexRgb(h){
    return [parseInt(h.slice(1, 3), 16), parseInt(h.slice(3, 5), 16), parseInt(h.slice(5, 7), 16)];
  }
  function beadName(hex){
    for(var i = 0; i < PAL.length; i++){ if(PAL[i][1] === hex) return PAL[i][0]; }
    return '色 ' + hex.toUpperCase();
  }

  /* ================= 状态 ================= */
  var rows = 29, cols = 29, cell = 18;
  var ZOOMS = [14, 18, 22, 26, 30, 36, 44];
  var zi = 1;
  var layers = [];
  var active = 0;
  var current = '#FC3D45';
  var canvas = $('pbCanvas'), ctx = canvas.getContext('2d');
  var dpr = window.devicePixelRatio || 1;
  var drawing = false, eraseMode = false;

  function newGrid(r, c){
    var g = [];
    for(var y = 0; y < r; y++){ g.push(new Array(c).fill('')); }
    return g;
  }
  function addLayer(name){
    layers.push({ name: name || ('图层 ' + (layers.length + 1)), visible: true, grid: newGrid(rows, cols) });
    active = layers.length - 1;
  }

  /* ================= 画布 ================= */
  function setupCanvas(){
    cell = ZOOMS[zi];
    $('pbZoomLabel').textContent = cell + 'px';
    canvas.width = cols * cell * dpr;
    canvas.height = rows * cell * dpr;
    canvas.style.width = (cols * cell) + 'px';
    canvas.style.height = (rows * cell) + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  }

  // 圆角矩形路径（豆子基础形态）
  function rr(c2, x, y, w, h, r){
    c2.beginPath();
    c2.moveTo(x + r, y);
    c2.arcTo(x + w, y, x + w, y + h, r);
    c2.arcTo(x + w, y + h, x, y + h, r);
    c2.arcTo(x, y + h, x, y, r);
    c2.arcTo(x, y, x + w, y, r);
    c2.closePath();
  }

  // 亮色缓存：豆子顶部高光用
  var LIGHT = {};
  function lightenHex(hex, amt){
    var key = hex + '|' + amt;
    if(!LIGHT[key]){
      var c = hexRgb(hex);
      LIGHT[key] = 'rgb(' + Math.round(c[0] + (255 - c[0]) * amt) + ',' + Math.round(c[1] + (255 - c[1]) * amt) + ',' + Math.round(c[2] + (255 - c[2]) * amt) + ')';
    }
    return LIGHT[key];
  }

  // 豆子：圆角方块 + 顶部高光，糖果质感
  function drawBeads(c2, grid, px){
    var inset = px * 0.06;
    var side = px - inset * 2;
    var rad = side * 0.3;
    for(var y = 0; y < rows; y++){
      for(var x = 0; x < cols; x++){
        var color = grid[y][x];
        if(!color) continue;
        rr(c2, x * px + inset, y * px + inset, side, side, rad);
        c2.fillStyle = color;
        c2.fill();
        rr(c2, x * px + inset + px * 0.09, y * px + inset + px * 0.07, side - px * 0.18, side * 0.4, rad * 0.65);
        c2.fillStyle = lightenHex(color, 0.4);
        c2.fill();
        rr(c2, x * px + inset, y * px + inset, side, side, rad);
        c2.lineWidth = Math.max(0.8, px * 0.04);
        c2.strokeStyle = 'rgba(0,0,0,0.13)';
        c2.stroke();
      }
    }
  }

  function draw(){
    ctx.fillStyle = '#fffdf9';
    ctx.fillRect(0, 0, cols * cell, rows * cell);
    layers.forEach(function(layer, i){
      ctx.globalAlpha = (i === active || !layers[active]) ? 1 : 0.35;
      if(layer.visible) drawBeads(ctx, layer.grid, cell);
    });
    ctx.globalAlpha = 1;
    ctx.strokeStyle = 'rgba(190,120,150,0.13)';
    ctx.lineWidth = 1;
    for(var x = 0; x <= cols; x++){
      ctx.beginPath(); ctx.moveTo(x * cell + 0.5, 0); ctx.lineTo(x * cell + 0.5, rows * cell); ctx.stroke();
    }
    for(var y = 0; y <= rows; y++){
      ctx.beginPath(); ctx.moveTo(0, y * cell + 0.5); ctx.lineTo(cols * cell, y * cell + 0.5); ctx.stroke();
    }
    renderCounts(); renderLayers();
  }

  /* ================= 图层栏 ================= */
  function renderLayers(){
    var html = '';
    layers.forEach(function(l, i){
      html += '<span class="pb-layer' + (i === active ? ' on' : '') + '" data-i="' + i + '">' +
        '<i class="pb-eye" data-i="' + i + '">' + (l.visible ? '👁' : '🚫') + '</i> ' + l.name +
        (layers.length > 1 ? '<i class="pb-del" data-i="' + i + '" title="删除该图层">✕</i>' : '') +
        '</span>';
    });
    html += '<span class="pb-layer" id="pbAddLayer">＋ 新建图层</span>';
    $('pbLayers').innerHTML = html;
  }
  $('pbLayers').addEventListener('click', function(e){
    if(e.target.id === 'pbAddLayer'){ addLayer(); draw(); return; }
    var eye = e.target.closest('.pb-eye');
    if(eye){ var i = +eye.getAttribute('data-i'); layers[i].visible = !layers[i].visible; draw(); return; }
    var del = e.target.closest('.pb-del');
    if(del){ var j = +del.getAttribute('data-i'); layers.splice(j, 1); active = Math.max(0, Math.min(active, layers.length - 1)); draw(); return; }
    var chip = e.target.closest('.pb-layer');
    if(chip && chip.hasAttribute('data-i')){ active = +chip.getAttribute('data-i'); draw(); }
  });

  /* ================= 色板 ================= */
  function buildPalette(){
    var html = '';
    var curGroup = '';
    PAL.forEach(function(c){
      if(c[2] !== curGroup){
        if(curGroup !== '') html += '</div>';
        var g = CARDS[cardKey].groups.filter(function(x){ return x[0] === c[2]; })[0] || [c[2], ''];
        html += '<div class="pb-group"><em>' + g[0] + ' ' + g[1] + '</em>';
        curGroup = c[2];
      }
      html += '<span class="pb-swatch" data-hex="' + c[1] + '" data-name="' + c[0] + '" style="background:' + c[1] + '"></span>';
    });
    html += '</div>';
    $('pbPalette').innerHTML = html;
  }
  $('pbPalette').addEventListener('click', function(e){
    var s = e.target.closest('.pb-swatch');
    if(!s) return;
    current = s.getAttribute('data-hex');
    curName = s.getAttribute('data-name');
    $('pbCurName').textContent = curName;
    document.querySelectorAll('.pb-swatch').forEach(function(el){ el.classList.toggle('on', el === s); });
  });

  /* 色卡切换与转换 */
  $('pbCard').addEventListener('change', function(){
    cardKey = this.value;
    PAL = parseCard(CARDS[cardKey]);
    RGBX = PAL.map(function(c){ return hexRgb(c[1]); });
    buildPalette();
    draw();
  });

  $('pbConvert').addEventListener('click', function(){
    var cache = {};
    var changed = 0;
    layers.forEach(function(l){
      for(var y = 0; y < rows; y++){
        for(var x = 0; x < cols; x++){
          var hex = l.grid[y][x];
          if(!hex) continue;
          if(!cache[hex]){
            var rgb = hexRgb(hex);
            var ni = nearestRgb(rgb[0], rgb[1], rgb[2]);
            cache[hex] = PAL[ni][1];
          }
          if(cache[hex] !== hex){ l.grid[y][x] = cache[hex]; changed++; }
        }
      }
    });
    // 当前色在新卡里也同步贴过去
    var cr = hexRgb(current);
    current = PAL[nearestRgb(cr[0], cr[1], cr[2])][1];
    draw();
    $('pbCurName').textContent = changed ? ('已转换 ' + changed + ' 颗豆') : '无需转换';
    setTimeout(function(){ $('pbCurName').textContent = curName; }, 2000);
  });

  /* ================= 点豆 ================= */
  function cellAt(e){
    var rect = canvas.getBoundingClientRect();
    var x = Math.floor((e.clientX - rect.left) / (rect.width / cols));
    var y = Math.floor((e.clientY - rect.top) / (rect.height / rows));
    if(x < 0 || y < 0 || x >= cols || y >= rows) return null;
    return [x, y];
  }
  function apply(e){
    var pos = cellAt(e);
    if(!pos) return;
    layers[active].grid[pos[1]][pos[0]] = (e.buttons === 2 || eraseMode) ? '' : current;
    draw();
  }
  canvas.addEventListener('contextmenu', function(e){ e.preventDefault(); });
  canvas.addEventListener('pointerdown', function(e){
    drawing = true; eraseMode = (e.button === 2);
    apply(e); canvas.setPointerCapture(e.pointerId);
  });
  canvas.addEventListener('pointermove', function(e){ if(drawing) apply(e); });
  canvas.addEventListener('pointerup', function(){ drawing = false; eraseMode = false; });

  $('pbZoomIn').addEventListener('click', function(){ if(zi < ZOOMS.length - 1){ zi++; setupCanvas(); draw(); } });
  $('pbZoomOut').addEventListener('click', function(){ if(zi > 0){ zi--; setupCanvas(); draw(); } });

  /* ================= 用量统计 ================= */
  function tally(grid){
    var t = {};
    for(var y = 0; y < rows; y++){
      for(var x = 0; x < cols; x++){
        var c = grid[y][x];
        if(c) t[c] = (t[c] || 0) + 1;
      }
    }
    return t;
  }
  function chipsHtml(t){
    var keys = Object.keys(t);
    if(!keys.length) return '<span style="font-size:0.8rem;color:var(--ink-3)">（空）</span>';
    keys.sort(function(a, b){ return t[b] - t[a]; });
    var html = '';
    keys.forEach(function(hex){
      html += '<span class="pb-count"><i style="background:' + hex + '"></i>' + beadName(hex) + ' <b>× ' + t[hex] + '</b></span>';
    });
    return html;
  }
  function renderCounts(){
    var tActive = tally(layers[active].grid);
    var total = {};
    layers.forEach(function(l){
      if(!l.visible) return;
      var t = tally(l.grid);
      Object.keys(t).forEach(function(k){ total[k] = (total[k] || 0) + t[k]; });
    });
    var hasMulti = layers.length > 1;
    $('pbCountWrap').style.display = 'block';
    $('pbCountsActive').innerHTML = chipsHtml(tActive);
    $('pbTotalTitle').style.display = hasMulti ? 'block' : 'none';
    $('pbCountsTotal').innerHTML = hasMulti ? chipsHtml(total) : '';
  }

  /* ================= 尺寸 / 示例 / 清空 ================= */
  $('pbSize').addEventListener('change', function(){
    var n = parseInt(this.value);
    var old = layers.map(function(l){ return l.grid; });
    var or = rows, oc = cols;
    rows = cols = n;
    layers.forEach(function(l, i){
      var g = newGrid(rows, cols);
      for(var y = 0; y < Math.min(rows, or); y++){
        for(var x = 0; x < Math.min(cols, oc); x++){
          if(old[i] && old[i][y]) g[y][x] = old[i][y][x] || '';
        }
      }
      l.grid = g;
    });
    setupCanvas(); draw();
  });

  $('pbClear').addEventListener('click', function(){
    layers[active].grid = newGrid(rows, cols); draw();
  });

  /* ---------- 可爱像素图案预设 ---------- */
  var PATTERNS = {
    love: { colors: { R: '#FC3D45', P: '#FFC2D4' }, rows: [
      '................',
      '..RRR......RRR..',
      '.RRRRR....RRRRR.',
      'RRRRRRR..RRRRRRR',
      'RRRRRRRRRRRRRRRR',
      'RPPRRRRRRRRRRRRR',
      'RPPRRRRRRRRRRRRR',
      'RRRRRRRRRRRRRRRR',
      '.RRRRRRRRRRRRRR.',
      '..RRRRRRRRRRRR..',
      '...RRRRRRRRRR...',
      '....RRRRRRRR....',
      '.....RRRRRR.....',
      '......RRRR......',
      '.......RR.......',
      '................',
    ]},
    star: { colors: { Y: '#FFD21F' }, rows: [
      '.......YY.......',
      '.......YY.......',
      '......YYYY......',
      '......YYYY......',
      'YYYYYYYYYYYYYYYY',
      '.YYYYYYYYYYYYYY.',
      '..YYYYYYYYYYYY..',
      '...YYYYYYYYYY...',
      '....YYYYYYYY....',
      '...YYYYYYYYYY...',
      '...YYYY..YYYY...',
      '..YYY......YYY..',
      '.YYY........YYY.',
      '.YY..........YY.',
      'YY............YY',
      '................',
    ]},
    mushroom: { colors: { R: '#FC3D45', W: '#FFF6E9', K: '#2C2C2C' }, rows: [
      '....RRRRRRRR....',
      '..RRRRRRRRRRRR..',
      '.RRWWRRRRRRWWRR.',
      '.RWWWWRRRRWWWWR.',
      'RRWWWWRRRRWWWWRR',
      'RRRWWRRRRRRWWRRR',
      'RRRRRRRRRRRRRRRR',
      '.RRRRRRRRRRRRRR.',
      '..WWWWWWWWWWWW..',
      '..WWKWWWWWWKWW..',
      '..WWKWWWWWWKWW..',
      '..WWWWWWWWWWWW..',
      '...WWWWWWWWWW...',
      '...WWWWWWWWWW...',
      '....WWWWWWWW....',
      '................',
    ]},
    bunny: { colors: { W: '#FFFFFF', P: '#FFB3C7', K: '#2C2C2C' }, rows: [
      '...WW......WW...',
      '...WW......WW...',
      '...WPW....WPW...',
      '...WWW....WWW...',
      '...WWWWWWWWWW...',
      '..WWWWWWWWWWWW..',
      '.WWKWWWWWWWWKWW.',
      '.WWPWWWWWWWWPWW.',
      '.WWWWWWPPWWWWWW.',
      '.WWWWWWWWWWWWWW.',
      '..WWWWWWWWWWWW..',
      '..WWWWWWWWWWWW..',
      '...WWWWWWWWWW...',
      '...WWWWWWWWWW...',
      '..WWWWWWWWWWWW..',
      '................',
    ]},
  };

  document.querySelectorAll('.pb-pat').forEach(function(b){
    b.addEventListener('click', function(){
      var pat = PATTERNS[this.getAttribute('data-pat')];
      if(!pat) return;
      var g = newGrid(rows, cols);
      var ox = Math.floor((cols - 16) / 2), oy = Math.floor((rows - 16) / 2);
      pat.rows.forEach(function(row, y){
        for(var x = 0; x < 16; x++){
          var ch = row[x];
          if(ch === '.' || !pat.colors[ch]) continue;
          var gy = y + oy, gx = x + ox;
          if(gy >= 0 && gy < rows && gx >= 0 && gx < cols) g[gy][gx] = pat.colors[ch];
        }
      });
      layers[active].grid = g; draw();
    });
  });

  /* ================= 照片转图纸 ================= */
  function nearestRgb(r, g, b){
    var best = 0, bestD = Infinity;
    for(var i = 0; i < RGBX.length; i++){
      var dr = r - RGBX[i][0], dg = g - RGBX[i][1], db = b - RGBX[i][2];
      var d = dr * dr * 2 + dg * dg * 4 + db * db * 3;
      if(d < bestD){ bestD = d; best = i; }
    }
    return best;
  }

  $('pbFile').addEventListener('change', function(){
    var file = this.files[0];
    if(!file) return;
    var reader = new FileReader();
    reader.onload = function(ev){
      var img = new Image();
      img.onload = function(){ convert(img); };
      img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
    this.value = '';
  });

  function convert(img){
    var off = document.createElement('canvas');
    off.width = cols; off.height = rows;
    var c2 = off.getContext('2d');
    c2.drawImage(img, 0, 0, cols, rows);
    var data = c2.getImageData(0, 0, cols, rows).data;
    var dither = $('pbDither').checked;
    var whiteOut = $('pbWhiteOut').checked;

    var g = newGrid(rows, cols);
    var buf = new Float32Array(cols * rows * 3);
    for(var i = 0; i < cols * rows; i++){
      buf[i * 3] = data[i * 4];
      buf[i * 3 + 1] = data[i * 4 + 1];
      buf[i * 3 + 2] = data[i * 4 + 2];
    }

    for(var y = 0; y < rows; y++){
      for(var x = 0; x < cols; x++){
        var idx = y * cols + x;
        var a = data[idx * 4 + 3];
        var r = buf[idx * 3], gg = buf[idx * 3 + 1], b = buf[idx * 3 + 2];
        if(a < 128 || (whiteOut && r > 245 && gg > 245 && b > 245)) continue;

        var ni = nearestRgb(Math.round(r), Math.round(gg), Math.round(b));
        g[y][x] = PAL[ni][1];

        if(dither){
          var er = r - RGBX[ni][0], eg = gg - RGBX[ni][1], eb = b - RGBX[ni][2];
          var spread = function(nx, ny, f){
            if(nx < 0 || nx >= cols || ny < 0 || ny >= rows) return;
            var j = ny * cols + nx;
            buf[j * 3] += er * f; buf[j * 3 + 1] += eg * f; buf[j * 3 + 2] += eb * f;
          };
          spread(x + 1, y, 7 / 16);
          spread(x - 1, y + 1, 3 / 16);
          spread(x, y + 1, 5 / 16);
          spread(x + 1, y + 1, 1 / 16);
        }
      }
    }
    layers[active].grid = g;
    draw();
  }

  /* ================= 保存 / 载入 ================= */
  $('pbSave').addEventListener('click', function(){
    try{
      localStorage.setItem('toolbox_perler', JSON.stringify({ r: rows, c: cols, layers: layers, active: active, card: cardKey }));
      $('pbCurName').textContent = '已保存 ✓';
      setTimeout(function(){ $('pbCurName').textContent = curName; }, 1500);
    }catch(err){}
  });
  $('pbLoad').addEventListener('click', function(){
    try{
      var d = JSON.parse(localStorage.getItem('toolbox_perler'));
      if(!d) return;
      rows = cols = d.r;
      layers = d.layers || [{ name: '图层 1', visible: true, grid: d.g || newGrid(d.r, d.c) }];
      active = Math.min(d.active || 0, layers.length - 1);
      if(d.card && CARDS[d.card]){
        cardKey = d.card;
        PAL = parseCard(CARDS[cardKey]);
        RGBX = PAL.map(function(c){ return hexRgb(c[1]); });
        $('pbCard').value = cardKey;
        buildPalette();
      }
      $('pbSize').value = String(rows);
      setupCanvas(); draw();
    }catch(err){}
  });

  /* ================= 导出 PNG / PDF ================= */
  function patternCanvas(px){
    var out = document.createElement('canvas');
    out.width = cols * px; out.height = rows * px;
    var c2 = out.getContext('2d');
    c2.fillStyle = '#ffffff'; c2.fillRect(0, 0, out.width, out.height);
    layers.forEach(function(l){ if(l.visible) drawBeads(c2, l.grid, px); });
    return out;
  }

  $('pbExport').addEventListener('click', function(){
    var a = document.createElement('a');
    a.download = 'perler-' + cols + 'x' + rows + '.png';
    a.href = patternCanvas(24).toDataURL('image/png');
    a.click();
  });

  function loadJsPdf(cb){
    if(window.jspdf && window.jspdf.jsPDF) return cb();
    var s = document.createElement('script');
    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
    s.onload = cb;
    s.onerror = function(){ alert('PDF 组件加载失败（需要联网一次），可以先导出 PNG。'); };
    document.head.appendChild(s);
  }

  function reportCanvas(){
    var W = 1240, H = 1754;
    var rep = document.createElement('canvas');
    rep.width = W; rep.height = H;
    var c2 = rep.getContext('2d');
    c2.fillStyle = '#ffffff'; c2.fillRect(0, 0, W, H);

    c2.fillStyle = '#1c1e22';
    c2.font = '700 40px "Noto Sans SC", sans-serif';
    c2.fillText('拼豆图纸 · ' + CARDS[cardKey].label, 80, 100);
    c2.font = '400 22px "Noto Sans SC", sans-serif';
    c2.fillStyle = '#6b7280';
    var totalBeads = 0;
    layers.forEach(function(l){ if(l.visible){ var t = tally(l.grid); Object.keys(t).forEach(function(k){ totalBeads += t[k]; }); } });
    c2.fillText('底板 ' + cols + ' × ' + rows + '　·　' + layers.length + ' 个图层　·　共 ' + totalBeads + ' 颗豆　·　' + new Date().toLocaleDateString('zh-CN'), 80, 140);

    var pat = patternCanvas(24);
    var patSize = Math.min(880, W - 160);
    c2.strokeStyle = '#e5e7eb';
    c2.strokeRect(78.5, 178.5, patSize + 3, patSize + 3);
    c2.drawImage(pat, 80, 180, patSize, patSize);

    var total = {};
    layers.forEach(function(l){
      if(!l.visible) return;
      var t = tally(l.grid);
      Object.keys(t).forEach(function(k){ total[k] = (total[k] || 0) + t[k]; });
    });
    var keys = Object.keys(total).sort(function(a, b){ return total[b] - total[a]; });
    c2.fillStyle = '#1c1e22';
    c2.font = '700 26px "Noto Sans SC", sans-serif';
    c2.fillText('用豆清单（全部可见图层合计，建议多备 5%~10% 损耗）', 80, patSize + 260);
    var colW = (W - 160) / 4;
    keys.forEach(function(hex, i){
      var col = i % 4, rowI = Math.floor(i / 4);
      var bx = 80 + col * colW, by = patSize + 300 + rowI * 34;
      if(by > H - 40) return;
      c2.beginPath();
      c2.arc(bx + 10, by - 6, 9, 0, Math.PI * 2);
      c2.fillStyle = hex; c2.fill();
      c2.strokeStyle = 'rgba(0,0,0,0.2)'; c2.lineWidth = 1; c2.stroke();
      c2.fillStyle = '#374151';
      c2.font = '400 19px "Noto Sans SC", sans-serif';
      c2.fillText(beadName(hex) + '  × ' + total[hex], bx + 26, by);
    });
    return rep;
  }

  $('pbPdf').addEventListener('click', function(){
    loadJsPdf(function(){
      var doc = new window.jspdf.jsPDF({ unit: 'pt', format: 'a4', orientation: 'portrait' });
      doc.addImage(reportCanvas().toDataURL('image/jpeg', 0.92), 'JPEG', 0, 0, 595.28, 841.89);
      doc.save('perler-' + cols + 'x' + rows + '.pdf');
    });
  });

  /* ================= 启动 ================= */
  addLayer();
  buildPalette();
  setupCanvas();
  draw();
})();
</script>

<div class="explain">
  <h3>玩法 & 怎么用</h3>
  <p class="ex-formula">用豆数 = 图纸上该色号占据的格子数（多图层自动合计）<br>总豆数 = 各色号用量之和，建议多备 5%~10% 损耗</p>
  <p class="ex-text">豆子渲染为<b>带圆角的方块</b>加一点顶部高光，糖果质感——接近真实拼豆熨烫后的圆润轮廓，点起来也解压。四个<b>可爱像素预设</b>（🩷爱心 / ⭐星星 / 🍄小蘑菇 / 🐰小兔子）一键起手，再慢慢改造。三张色卡随时切换：<b>MARD 221</b>（国产主流）、<b>Perler 48</b>（美国经典）、<b>Artkal S 系 159</b>（色号最全），换卡后点「转换」图纸上的豆会自动映射到新卡里最接近的色号——用 MARD 画的图纸可以直接转成 Artkal 采购单。照片转图纸、多图层立体拼豆、PNG/PDF 导出：照片开抖动更细腻，像素画关抖动保持干净。</p>
</div>
