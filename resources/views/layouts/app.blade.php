<!DOCTYPE html>
<html lang="zh-CN" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', '工具匣 ToolBox')</title>
@hasSection('description')
<meta name="description" content="@yield('description')">
@else
<meta name="description" content="工具匣 ToolBox — 25 个免费在线工具：财务计算器、开发工具、格式互转、拼豆图纸生成器与趣味测试，全部浏览器本地运行。">
@endif
<meta property="og:type" content="website">
<meta property="og:site_name" content="工具匣 ToolBox">
<meta property="og:title" content="@yield('title', '工具匣 ToolBox')">
@hasSection('description')
<meta property="og:description" content="@yield('description')">
@else
<meta property="og:description" content="25 个免费在线工具：财务计算器、开发工具、格式互转、拼豆图纸生成器，全部浏览器本地运行。">
@endif
<meta property="og:url" content="{{ url()->current() }}">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:image" content="{{ asset('og-default.png') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="" id="themeColor">
<meta name="google-adsense-account" content="ca-pub-1389652054868935">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
@yield('meta')
<script>(function(){var t=localStorage.getItem('theme');if(t){document.documentElement.setAttribute('data-theme',t)}else if(window.matchMedia&&matchMedia('(prefers-color-scheme:dark)').matches){document.documentElement.setAttribute('data-theme','dark')}})();</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@600;700&family=Noto+Sans+SC:wght@400;500&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/tokens.css') }}?v={{ filemtime(public_path('css/tokens.css')) }}" rel="stylesheet">
<link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1389652054868935" crossorigin="anonymous"></script>
</head>
<body>

<a class="skip-link" href="#main">跳到正文</a>

<header class="site-head">
  <div class="head-inner">
    <a href="{{ route('home') }}" class="wordmark" aria-label="工具匣 ToolBox 首页">
      <span class="wordmark__cn">工具<span class="box">匣</span></span>
      <span class="wordmark__en" aria-hidden="true">TOOLBOX</span>
    </a>

    <nav class="site-nav" aria-label="主导航">
      <a href="{{ route('tools.index') }}" @if(request()->routeIs('tools.index') || (request()->routeIs('tools.show') && !request()->is('tools/perler-patterns'))) aria-current="page" @endif>工具</a>
      <a href="{{ route('tools.show', 'perler-patterns') }}" @if(request()->is('tools/perler-patterns')) aria-current="page" @endif>拼豆</a>
      <a href="{{ route('quizzes.index') }}" @if(request()->routeIs('quizzes.*')) aria-current="page" @endif>测试</a>
      <a href="{{ route('blog.index') }}" @if(request()->routeIs('blog.*')) aria-current="page" @endif>博客</a>
    </nav>

    <div class="search" role="search">
      <label class="tb-live" for="tbSearch">搜索工具</label>
      <input type="search" id="tbSearch" placeholder="搜索工具…" autocomplete="off"
             role="combobox" aria-expanded="false" aria-controls="tbSuggest" aria-autocomplete="list"
             aria-describedby="tbLive">
      <span class="search__hint" id="searchHint" aria-hidden="true">Ctrl K</span>
      <div class="tb-suggest" id="tbSuggest" role="listbox" aria-label="搜索结果" hidden></div>
      <p class="tb-live" id="tbLive" role="status" aria-live="polite"></p>
    </div>

    <button class="theme-toggle" id="themeToggle" type="button" aria-label="切换到深色模式">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
    </button>

    <button class="menu-toggle" id="menuToggle" type="button" aria-expanded="false" aria-controls="mobileNav" aria-label="打开菜单">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
  </div>

  <nav class="mobile-nav" id="mobileNav" aria-label="移动导航">
    <a href="{{ route('tools.index') }}" @if(request()->routeIs('tools.index') || (request()->routeIs('tools.show') && !request()->is('tools/perler-patterns'))) aria-current="page" @endif>工具</a>
    <a href="{{ route('tools.show', 'perler-patterns') }}" @if(request()->is('tools/perler-patterns')) aria-current="page" @endif>拼豆</a>
    <a href="{{ route('quizzes.index') }}" @if(request()->routeIs('quizzes.*')) aria-current="page" @endif>测试</a>
    <a href="{{ route('blog.index') }}" @if(request()->routeIs('blog.*')) aria-current="page" @endif>博客</a>
  </nav>
</header>

<main id="main">
@yield('content')
</main>

<footer class="site-foot">
  <div class="colophon">
    <p class="colophon__line">工具匣 ToolBox — 计算器、开发工具、格式转换、拼豆图纸、乐器与测试。全部在浏览器本地运行，输入的数据不离开你的设备。</p>
    <p class="colophon__meta">
      <span>© {{ date('Y') }}</span>
      <span class="dot" aria-hidden="true">·</span>
      <a href="{{ route('about') }}">关于</a>
      <a href="{{ route('contact') }}">联系</a>
      <a href="{{ route('faq') }}">常见问题</a>
      <a href="{{ route('privacy') }}">隐私政策</a>
      <a href="{{ config('site.github') }}" target="_blank" rel="noopener">GitHub</a>
      <span class="dot" aria-hidden="true">·</span>
      <span>数据不出浏览器</span>
    </p>
  </div>
</footer>

<script>
window.TB_INDEX = @json($searchIndex ?? []);
</script>
<script>
(function(){
  var root = document.documentElement;
  var themeBtn = document.getElementById('themeToggle');
  var themeColor = document.getElementById('themeColor');

  function paintChrome(){
    var dark = root.getAttribute('data-theme') === 'dark';
    themeBtn.setAttribute('aria-label', dark ? '切换到浅色模式' : '切换到深色模式');
    themeBtn.setAttribute('aria-pressed', dark ? 'true' : 'false');
    if(themeColor){
      var bg = getComputedStyle(document.body).backgroundColor;
      if(bg) themeColor.setAttribute('content', bg);
    }
  }
  paintChrome();

  themeBtn.addEventListener('click', function(){
    var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    paintChrome();
  });

  /* ---- 移动端菜单 ---- */
  var menuBtn = document.getElementById('menuToggle');
  var mobileNav = document.getElementById('mobileNav');
  menuBtn.addEventListener('click', function(){
    var open = mobileNav.getAttribute('data-open') === 'true';
    mobileNav.setAttribute('data-open', open ? 'false' : 'true');
    menuBtn.setAttribute('aria-expanded', open ? 'false' : 'true');
    menuBtn.setAttribute('aria-label', open ? '打开菜单' : '关闭菜单');
  });

  /* ---- 全站搜索 ---- */
  var index = window.TB_INDEX || [];
  var input = document.getElementById('tbSearch');
  var box = document.getElementById('tbSuggest');
  var live = document.getElementById('tbLive');
  if(!input || !box) return;

  var hint = document.getElementById('searchHint');
  if(hint && /Mac|iPhone|iPad/.test(navigator.platform || '')) hint.textContent = '⌘K';

  var active = -1;

  function options(){ return box.querySelectorAll('a.tb-item'); }

  function close(){
    box.hidden = true;
    box.innerHTML = '';
    input.setAttribute('aria-expanded', 'false');
    input.removeAttribute('aria-activedescendant');
    active = -1;
  }

  function setActive(next){
    var opts = options();
    if(!opts.length) return;
    active = (next + opts.length) % opts.length;
    opts.forEach(function(el, i){
      el.setAttribute('aria-selected', i === active ? 'true' : 'false');
    });
    var cur = opts[active];
    input.setAttribute('aria-activedescendant', cur.id);
    cur.scrollIntoView({ block: 'nearest' });
  }

  function render(){
    var q = input.value.trim().toLowerCase();
    box.innerHTML = '';
    active = -1;
    input.removeAttribute('aria-activedescendant');
    if(!q){ close(); return; }

    var hits = index.filter(function(i){
      return (i.n + ' ' + i.d + ' ' + (i.c || '')).toLowerCase().indexOf(q) >= 0;
    }).slice(0, 8);

    if(!hits.length){
      var empty = document.createElement('p');
      empty.className = 'tb-empty';
      empty.textContent = '没有匹配的工具';
      box.appendChild(empty);
      box.hidden = false;
      input.setAttribute('aria-expanded', 'true');
      live.textContent = '没有匹配结果';
      return;
    }

    hits.forEach(function(h, i){
      var a = document.createElement('a');
      a.className = 'tb-item';
      a.href = h.u;
      a.id = 'tb-opt-' + i;
      a.setAttribute('role', 'option');
      a.setAttribute('aria-selected', 'false');
      var n = document.createElement('span');
      n.className = 'tb-item__n';
      n.textContent = h.n;
      var c = document.createElement('span');
      c.className = 'tb-item__c';
      c.textContent = h.c || '';
      a.appendChild(n);
      a.appendChild(c);
      box.appendChild(a);
    });

    box.hidden = false;
    input.setAttribute('aria-expanded', 'true');
    live.textContent = '找到 ' + hits.length + ' 个结果';
  }

  input.addEventListener('input', render);
  input.addEventListener('focus', function(){ if(input.value.trim()) render(); });
  input.addEventListener('keydown', function(e){
    if(e.key === 'ArrowDown'){ e.preventDefault(); setActive(active + 1); }
    else if(e.key === 'ArrowUp'){ e.preventDefault(); setActive(active - 1); }
    else if(e.key === 'Enter'){
      var picked = active >= 0 ? options()[active] : options()[0];
      if(picked){ window.location.href = picked.href; }
    }
    else if(e.key === 'Escape'){ close(); input.blur(); }
  });

  document.addEventListener('click', function(e){
    if(!box.contains(e.target) && e.target !== input) close();
  });

  document.addEventListener('keydown', function(e){
    var typing = /^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement.tagName) || document.activeElement.isContentEditable;
    if((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k'){
      e.preventDefault(); input.focus(); input.select();
    } else if(e.key === '/' && !typing){
      e.preventDefault(); input.focus();
    }
  });
})();
</script>
<script>
(function(){
  var nav = navigator;
  if (nav.doNotTrack === '1' || window.doNotTrack === '1' || nav.globalPrivacyControl) return;
  var body = JSON.stringify({ p: location.pathname });
  var endpoint = '{{ url('/api/visit') }}';
  if (nav.sendBeacon) {
    nav.sendBeacon(endpoint, new Blob([body], { type: 'application/json' }));
  } else if (window.fetch) {
    fetch(endpoint, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: body, keepalive: true }).catch(function(){});
  }
})();
</script>
@stack('jsonld')
@stack('scripts')
</body>
</html>
