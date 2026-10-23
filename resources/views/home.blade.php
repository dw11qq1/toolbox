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
    $order = ['计算', '开发', '转换', '音乐'];
  @endphp

  @foreach($order as $cat)
    @if(isset($grouped[$cat]))
      <section class="group">
        <div class="group-title">{{ $cat }}</div>
        @foreach($grouped[$cat] as $tool)
          <a class="card" href="{{ route('tools.show', $tool) }}">
            <div class="card-row">
              <h2>{{ $tool->name }}</h2>
              <span class="arrow">→</span>
            </div>
            <p class="desc">{{ $tool->description }}</p>
          </a>
        @endforeach
      </section>
    @endif
  @endforeach

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
    <div class="group-title">博客 · Blog</div>
    <a class="card" href="{{ route('blog.index') }}">
      <div class="card-row">
        <h2>写下来的思考</h2>
        <span class="arrow">→</span>
      </div>
      <p class="desc">开发路上踩过的坑、想明白的事。</p>
    </a>
  </section>
</div>
@endsection
