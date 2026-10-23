<!DOCTYPE html>
<html lang="zh-CN" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', '工具匣 ToolBox')</title>
@yield('meta')
<script>(function(){var t=localStorage.getItem('theme');if(t){document.documentElement.setAttribute('data-theme',t)}else if(window.matchMedia&&matchMedia('(prefers-color-scheme:dark)').matches){document.documentElement.setAttribute('data-theme','dark')}})();</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Noto+Sans+SC:wght@400;500;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

<header class="top">
  <a href="{{ route('home') }}" class="wordmark">工具<span class="box">匣</span></a>
  <nav>
    <a href="{{ route('tools.index') }}" @if(request()->routeIs('tools.*')) aria-current="page" @endif>工具</a>
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
  </div>
</footer>

<script>
(function(){
  var root = document.documentElement;
  document.getElementById('themeToggle').addEventListener('click', function(){
    var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
  });
})();
</script>
@stack('scripts')
</body>
</html>
