@extends('layouts.app')

@section('title', '全部游戏 — 工具匣 ToolBox')

@section('content')
<div class="wrap">
  <div class="page-head">
    <p class="k">Games · 游戏索引</p>
    <h1>全部游戏。</h1>
    <p class="sub">打开即玩，进度和最高分存在你自己的浏览器里。</p>
  </div>

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
</div>
@endsection
