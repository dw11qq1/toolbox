@extends('layouts.app')

@section('title', '工具匣 ToolBox — 在线工具与小游戏，数据不出浏览器')

@section('content')
<div class="wrap">
  <div class="page-head">
    <p class="k">ToolBox · 工具匣</p>
    <h1>小工具，小游戏，开匣即用。</h1>
    <p class="sub">全部在浏览器本地运行，不注册、不登录，你输入的数据不会离开你的设备。</p>
  </div>

  <section class="group">
    <div class="group-title">在线工具 · Tools</div>
    @forelse($tools as $tool)
      <a class="card" href="{{ route('tools.show', $tool) }}">
        <div class="card-row">
          <h2>{{ $tool->name }}</h2>
          <span class="arrow">→</span>
        </div>
        <p class="desc">{{ $tool->description }}</p>
        <p class="meta">{{ $tool->category }}</p>
      </a>
    @empty
      <p class="empty">工具上架中，敬请期待。</p>
    @endforelse
    <a class="card" href="{{ route('tools.index') }}">
      <div class="card-row">
        <h2>全部工具</h2>
        <span class="arrow">→</span>
      </div>
    </a>
  </section>

  <section class="group">
    <div class="group-title">小游戏 · Games</div>
    @forelse($games as $game)
      <a class="card" href="{{ route('games.show', $game) }}">
        <div class="card-row">
          <h2>{{ $game->name }}</h2>
          <span class="arrow">→</span>
        </div>
        <p class="desc">{{ $game->description }}</p>
      </a>
    @empty
      <p class="empty">游戏开发中，敬请期待。</p>
    @endforelse
    <a class="card" href="{{ route('games.index') }}">
      <div class="card-row">
        <h2>全部游戏</h2>
        <span class="arrow">→</span>
      </div>
    </a>
  </section>

  <section class="group">
    <div class="group-title">趣味测试 · Tests</div>
    @forelse($quizzes as $quiz)
      <a class="card" href="{{ route('quizzes.show', $quiz) }}">
        <div class="card-row">
          <h2>{{ $quiz->name }}</h2>
          <span class="arrow">→</span>
        </div>
        <p class="desc">{{ $quiz->description }}</p>
      </a>
    @empty
      <p class="empty">测试上线中，敬请期待。</p>
    @endforelse
    <a class="card" href="{{ route('quizzes.index') }}">
      <div class="card-row">
        <h2>全部测试</h2>
        <span class="arrow">→</span>
      </div>
    </a>
  </section>

  <section class="group">
    <div class="group-title">导航站 · Nav</div>
    <a class="card" href="{{ route('nav.index') }}">
      <div class="card-row">
        <h2>常去的站，都在这</h2>
        <span class="arrow">→</span>
      </div>
      <p class="desc">按用途分组的精选链接导航。</p>
    </a>
  </section>
</div>
@endsection
