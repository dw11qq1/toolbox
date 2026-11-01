<!DOCTYPE html>
<html lang="zh-CN" data-theme="light">
<head>
@include('admin.partials.head', ['title' => '登录 — 工具匣 管理台'])
</head>
<body class="adm adm-login">
<main class="adm-login__wrap">
  <a class="adm-brand adm-brand--login" href="{{ route('home') }}">
    <span class="adm-brand__cn">工具<span class="adm-brand__box">匣</span></span>
    <span class="adm-brand__en">管理台</span>
  </a>

  <form class="adm-login__card" method="POST" action="{{ route('admin.login.store') }}">
    @csrf
    <h1>进入管理台</h1>
    <p class="adm-login__sub">文章管理、排序与站点数据。仅限站长使用。</p>

    @if($errors->any())
      <div class="adm-flash adm-flash--error" role="alert">
        @foreach($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <label class="adm-field">
      <span class="adm-field__label">邮箱</span>
      <input class="adm-input" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
    </label>

    <label class="adm-field">
      <span class="adm-field__label">密码</span>
      <input class="adm-input" type="password" name="password" autocomplete="current-password" required>
    </label>

    <label class="adm-check">
      <input type="checkbox" name="remember" value="1"> <span>保持登录</span>
    </label>

    <button class="adm-btn adm-btn--primary adm-btn--block" type="submit">登录</button>
  </form>

  <a class="adm-login__back" href="{{ route('home') }}">← 返回网站</a>
</main>
</body>
</html>
