@extends('layouts.app')

@section('title', '博客 — 工具匣 ToolBox')
@section('description', '工具匣 ToolBox 的博客：记录开发、设计与工具背后的思考，以及踩过的坑和想明白的事。')

@section('content')
<div class="wrap">
  @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '博客', 'url' => route('blog.index')]]; @endphp
  @include('partials.breadcrumb')
  <div class="page-head">
    <h1>写下来的思考。</h1>
    <p class="sub">记录开发路上踩过的坑、想明白的事。</p>
  </div>

  <section class="idx">
    <div class="idx-head">
      <h2>全部文章</h2>
      <span class="idx-count">{{ $posts->count() }}</span>
    </div>
    @forelse($posts as $post)
      <a class="idx-row idx-row--post" href="{{ route('blog.show', $post) }}">
        <span class="idx-name">{{ $post->title }}</span>
        <span class="idx-desc">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}</span>
        <time class="idx-date" datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->isoFormat('YYYY-MM-DD') }}</time>
        <span class="idx-mark" aria-hidden="true">→</span>
      </a>
    @empty
      <p class="empty">还没有文章，敬请期待。</p>
    @endforelse
  </section>
</div>
@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@@graph' => [
    [
      '@@type' => 'BreadcrumbList',
      'itemListElement' => [
        ['@@type' => 'ListItem', 'position' => 1, 'name' => '首页', 'item' => url('/')],
        ['@@type' => 'ListItem', 'position' => 2, 'name' => '博客', 'item' => route('blog.index')],
      ],
    ],
    [
      '@@type' => 'ItemList',
      'name' => '工具匣 ToolBox 博客文章',
      'itemListElement' => $posts->values()->map(fn ($post, $i) => [
        '@@type' => 'ListItem',
        'position' => $i + 1,
        'name' => $post->title,
        'url' => route('blog.show', $post),
      ])->all(),
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
