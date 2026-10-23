@extends('layouts.app')

@section('title', '趣味测试 — 工具匣 ToolBox')

@section('content')
<div class="wrap">
  <div class="page-head">
    <p class="k">Tests · 趣味测试</p>
    <h1>测一测，更了解自己。</h1>
    <p class="sub">全部本地计分，结果不联网、不保存。</p>
  </div>

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
</div>
@endsection
