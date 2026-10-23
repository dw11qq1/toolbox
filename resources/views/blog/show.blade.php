@extends('layouts.app')

@section('title', $post->title . ' — 工具匣 ToolBox')
@section('meta')
<meta name="description" content="{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 150) }}">
@endsection

@section('content')
<div class="wrap">
  <div class="page-head">
    <a class="back" href="{{ route('blog.index') }}" style="font-family:var(--font-mono);font-size:0.82rem;color:var(--ink-3);text-decoration:none">← 全部文章</a>
    <p class="k" style="margin-top:1.5rem">{{ $post->published_at->isoFormat('YYYY 年 MM 月 DD 日') }}</p>
    <h1>{{ $post->title }}</h1>
  </div>

  <div class="prose">{!! $post->content !!}</div>
</div>
@endsection
