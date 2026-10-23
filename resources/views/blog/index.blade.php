@extends('layouts.app')

@section('title', '技术笔记 — 工具匣 ToolBox')

@section('content')
<div class="wrap">
  <div class="page-head">
    <p class="k">Blog · 技术笔记</p>
    <h1>写下来的思考。</h1>
    <p class="sub">记录开发路上踩过的坑、想明白的事。</p>
  </div>

  @forelse($posts as $post)
    <a class="card" href="{{ route('blog.show', $post) }}">
      <div class="card-row">
        <h2>{{ $post->title }}</h2>
        <span class="arrow">→</span>
      </div>
      <p class="desc">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}</p>
      <p class="meta">{{ $post->published_at->isoFormat('YYYY-MM-DD') }}</p>
    </a>
  @empty
    <p class="empty">还没有文章，敬请期待。</p>
  @endforelse
</div>
@endsection
