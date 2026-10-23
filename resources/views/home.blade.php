@extends('layouts.app')

@section('title', '工具匣 ToolBox — 在线工具与音乐创作，数据不出浏览器')

@section('content')
<div class="wrap">
  <div class="page-head">
    <p class="k">ToolBox · 工具匣</p>
    <h1>小工具，小旋律，开匣即用。</h1>
    <p class="sub">全部在浏览器本地运行，不注册、不登录，你输入的数据不会离开你的设备。</p>
  </div>

  @php
    $grouped = $tools->groupBy('category');
    $order = ['计算', '开发', '转换', '音乐', '创作'];
  @endphp

  @foreach($order as $cat)
    @if(isset($grouped[$cat]))
      <section class="group">
        <div class="group-title">{{ $cat }}</div>
        <div class="nav-grid">
          @foreach($grouped[$cat] as $tool)
            <a class="nav-card" href="{{ route('tools.show', $tool) }}">
              <span class="nav-card__name">{{ $tool->name }} <span class="arrow">→</span></span>
              <span class="nav-card__desc">{{ $tool->description }}</span>
            </a>
          @endforeach
        </div>
      </section>
    @endif
  @endforeach

  <section class="group">
    <div class="group-title">趣味测试 · Tests</div>
    <div class="nav-grid">
      @forelse($quizzes as $quiz)
        <a class="nav-card" href="{{ route('quizzes.show', $quiz) }}">
          <span class="nav-card__name">{{ $quiz->name }} <span class="arrow">→</span></span>
          <span class="nav-card__desc">{{ $quiz->description }}</span>
        </a>
      @empty
        <p class="empty">测试上线中，敬请期待。</p>
      @endforelse
      <a class="nav-card" href="{{ route('quizzes.index') }}">
        <span class="nav-card__name">全部测试 <span class="arrow">→</span></span>
        <span class="nav-card__desc">四个趣味测试一览。</span>
      </a>
    </div>
  </section>

  <section class="group">
    <div class="group-title">博客 · Blog</div>
    <div class="nav-grid">
      <a class="nav-card" href="{{ route('blog.index') }}">
        <span class="nav-card__name">写下来的思考 <span class="arrow">→</span></span>
        <span class="nav-card__desc">开发路上踩过的坑、想明白的事。</span>
      </a>
    </div>
  </section>
</div>
@endsection
