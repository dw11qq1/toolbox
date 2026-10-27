<!DOCTYPE html>
<html lang="zh-CN" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<meta name="twitter:card" content="summary">
<meta name="google-adsense-account" content="ca-pub-1389652054868935">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
@yield('meta')
<script>(function(){var t=localStorage.getItem('theme');if(t){document.documentElement.setAttribute('data-theme',t)}else if(window.matchMedia&&matchMedia('(prefers-color-scheme:dark)').matches){document.documentElement.setAttribute('data-theme','dark')}})();</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Noto+Sans+SC:wght@400;500;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1389652054868935" crossorigin="anonymous"></script>
</head>
<body>

<header class="top">
  <a href="{{ route('home') }}" class="wordmark">工具<span class="box">匣</span></a>
  <div class="top-search">
    <input type="search" id="tbSearch" placeholder="搜索工具…" autocomplete="off" aria-label="搜索工具">
    <div class="tb-suggest" id="tbSuggest" hidden></div>
  </div>
  <nav>
    <a href="{{ route('tools.index') }}" @if(request()->routeIs('tools.index')) aria-current="page" @endif>工具</a>
    <a href="{{ route('tools.show', 'perler-patterns') }}" @if(request()->is('tools/perler-patterns')) aria-current="page" @endif>拼豆</a>
    <a href="{{ route('quizzes.index') }}" @if(request()->routeIs('quizzes.*')) aria-current="page" @endif>测试</a>
    <a href="{{ route('blog.index') }}" @if(request()->routeIs('blog.*')) aria-current="page" @endif>博客</a>
    <button class="theme-toggle" id="themeToggle" aria-label="切换主题">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
    </button>
  </nav>
</header>

<main>
@yield('content')
</main>

<footer>
  <div class="inner">
    <a class="wordmark" href="{{ route('home') }}">工具匣</a>
    <span class="muted">© {{ date('Y') }} 工具匣 ToolBox · 数据不出浏览器</span>
    <span class="foot-links">
      <a href="{{ route('privacy') }}">隐私政策</a>
    </span>
  </div>
</footer>

<script>
window.TB_INDEX = @json($searchIndex ?? []);
</script>
<script>
(function(){
  var root = document.documentElement;
  document.getElementById('themeToggle').addEventListener('click', function(){
    var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
  });

  /* ---- 全站搜索 ---- */
  var index = window.TB_INDEX || [];
  var input = document.getElementById('tbSearch');
  var box = document.getElementById('tbSuggest');
  if(!input || !box) return;

  function close(){ box.hidden = true; box.innerHTML = ''; }

  function render(){
    var q = input.value.trim().toLowerCase();
    box.innerHTML = '';
    if(!q){ close(); return; }
    var hits = index.filter(function(i){
      return (i.n + ' ' + i.d + ' ' + (i.c || '')).toLowerCase().indexOf(q) >= 0;
    }).slice(0, 8);
    if(!hits.length){
      box.innerHTML = '<p class="tb-empty">没有匹配的工具</p>';
      box.hidden = false;
      return;
    }
    hits.forEach(function(h){
      var a = document.createElement('a');
      a.className = 'tb-item';
      a.href = h.u;
      a.innerHTML = '<span class="tb-item__n">' + h.n + '</span>' +
                    '<span class="tb-item__c">' + (h.c || '') + '</span>';
      box.appendChild(a);
    });
    box.hidden = false;
  }

  input.addEventListener('input', render);
  input.addEventListener('focus', render);
  input.addEventListener('keydown', function(e){
    if(e.key === 'Enter'){
      var first = box.querySelector('a.tb-item');
      if(first){ window.location.href = first.href; }
    }
    if(e.key === 'Escape'){ close(); input.blur(); }
  });
  document.addEventListener('click', function(e){
    if(!box.contains(e.target) && e.target !== input) close();
  });
})();
</script>
@stack('scripts')
</body>
</html>
