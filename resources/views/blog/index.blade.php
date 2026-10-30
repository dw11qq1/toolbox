@extends('layouts.app')

@section('title', '博客 — 工具匣 ToolBox')

@section('content')
<div class="wrap">
  @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '博客', 'url' => route('blog.index')]]; @endphp
  @include('partials.breadcrumb')
  <div class="page-head">
    <p class="k">Blog · 博客</p>
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
@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@@type' => 'BreadcrumbList',
  'itemListElement' => [
    ['@@type' => 'ListItem', 'position' => 1, 'name' => '首页', 'item' => url('/')],
    ['@@type' => 'ListItem', 'position' => 2, 'name' => '博客', 'item' => route('blog.index')],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
