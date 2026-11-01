<!DOCTYPE html>
<html lang="zh-CN" data-theme="light">
<head>
@include('admin.partials.head')
</head>
<body class="adm">
<div class="adm-shell">
  <aside class="adm-side">
    <a class="adm-brand" href="{{ route('admin.dashboard') }}">
      <span class="adm-brand__cn">工具<span class="adm-brand__box">匣</span></span>
      <span class="adm-brand__en">管理台</span>
    </a>

    <nav class="adm-nav" aria-label="后台导航">
      <a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>概览</a>
      <a href="{{ route('admin.posts.index') }}" @if(request()->routeIs('admin.posts.*')) aria-current="page" @endif>文章</a>
      <a href="{{ route('admin.tools.index') }}" @if(request()->routeIs('admin.tools.*')) aria-current="page" @endif>工具</a>
      <a href="{{ route('admin.quizzes.index') }}" @if(request()->routeIs('admin.quizzes.*')) aria-current="page" @endif>测试</a>
    </nav>

    <div class="adm-side-foot">
      <a href="{{ route('home') }}" target="_blank" rel="noopener">查看站点 <span aria-hidden="true">↗</span></a>
      <p class="adm-side-mail">{{ auth()->user()->email }}</p>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="adm-linkbtn">退出登录</button>
      </form>
    </div>
  </aside>

  <div class="adm-main">
    <header class="adm-top">
      <div class="adm-top__titles">
        <p class="adm-kicker">@yield('kicker', '管理台')</p>
        <h1 class="adm-title">@yield('heading', '概览')</h1>
      </div>
      <div class="adm-top__actions">
        @yield('actions')
        <button class="adm-theme" id="admTheme" type="button" aria-label="切换到深色模式">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        </button>
      </div>
    </header>

    <main class="adm-content">
      @if(session('status'))
        <p class="adm-flash" role="status">{{ session('status') }}</p>
      @endif

      @if($errors->any())
        <div class="adm-flash adm-flash--error" role="alert">
          @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
          @endforeach
        </div>
      @endif

      @yield('content')
    </main>
  </div>
</div>

<script>
(function(){
  var root = document.documentElement;
  var btn = document.getElementById('admTheme');
  if(!btn) return;
  function label(){
    var dark = root.getAttribute('data-theme') === 'dark';
    btn.setAttribute('aria-label', dark ? '切换到浅色模式' : '切换到深色模式');
    btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
  }
  btn.addEventListener('click', function(){
    var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    label();
  });
  label();
})();

document.querySelectorAll('form[data-confirm]').forEach(function(form){
  form.addEventListener('submit', function(event){
    if (!window.confirm(form.dataset.confirm)) event.preventDefault();
  });
});
</script>
@stack('scripts')
</body>
</html>
