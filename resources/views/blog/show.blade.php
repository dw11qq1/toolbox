@extends('layouts.app')

@section('title', $post->title . ' — 工具匣 ToolBox')
@section('description', $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 150))

@section('content')
<div class="wrap">
  @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '博客', 'url' => route('blog.index')], ['name' => $post->title, 'url' => route('blog.show', $post)]]; @endphp
  @include('partials.breadcrumb')
  <div class="page-head">
    <a class="back-link" href="{{ route('blog.index') }}">← 全部文章</a>
    <h1 style="margin-top:var(--space-sm)">{{ $post->title }}</h1>
    <p class="page-meta">作者 {{ config('site.author_name') }} · {{ $post->published_at->isoFormat('YYYY 年 MM 月 DD 日') }}</p>
  </div>
  <div class="prose">{!! $post->content !!}</div>
</div>

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@@graph' => [
    [
      '@@type' => 'Article',
      'headline' => $post->title,
      'description' => $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 150),
      'author' => [
        '@@type' => 'Person',
        'name' => config('site.author_name'),
        'url' => url(config('site.author_url')),
        'jobTitle' => config('site.author_title'),
      ],
      'publisher' => [
        '@@type' => 'Organization',
        'name' => '工具匣 ToolBox',
        'logo' => ['@@type' => 'ImageObject', 'url' => asset('favicon.svg')],
      ],
      'datePublished' => $post->published_at->toIso8601String(),
      'dateModified' => ($post->updated_at ?? $post->published_at)->toIso8601String(),
      'mainEntityOfPage' => ['@@type' => 'WebPage', '@@id' => route('blog.show', $post)],
      'image' => $post->cover_image ? asset($post->cover_image) : asset('favicon.svg'),
    ],
    [
      '@@type' => 'BreadcrumbList',
      'itemListElement' => [
        ['@@type' => 'ListItem', 'position' => 1, 'name' => '首页', 'item' => url('/')],
        ['@@type' => 'ListItem', 'position' => 2, 'name' => '博客', 'item' => route('blog.index')],
        ['@@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => route('blog.show', $post)],
      ],
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
