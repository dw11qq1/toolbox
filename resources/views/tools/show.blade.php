@extends('layouts.app')

@section('title', $tool->name . ' — 工具匣 ToolBox')
@section('description', $tool->description)

@section('content')
<div class="wrap tool-shell">
  @if($tool->slug !== 'perler-patterns')
    @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '工具', 'url' => route('tools.index')], ['name' => $tool->name, 'url' => route('tools.show', $tool)]]; @endphp
    @include('partials.breadcrumb')
    <div class="page-head">
      <h1>{{ $tool->name }}</h1>
      <p class="sub">{{ $tool->description }}</p>
      <p class="page-meta">分类 · {{ $tool->category }}<span aria-hidden="true"> · </span>浏览器本地运行</p>
    </div>
  @else
    <h1 class="sr-only">{{ $tool->name }}</h1>
  @endif

  @includeIf('tools.pages.'.$tool->slug)

  @if($tool->slug !== 'perler-patterns' && $related->isNotEmpty())
    <section class="idx" aria-labelledby="related-tools" style="margin-top:var(--space-xl)">
      <div class="idx-head">
        <h2 id="related-tools">相关工具</h2>
        <span class="idx-count">{{ $related->count() }}</span>
        <a class="idx-more" href="{{ route('tools.index') }}">全部工具 →</a>
      </div>
      @foreach($related as $item)
        <a class="idx-row" href="{{ route('tools.show', $item) }}">
          <span class="idx-name">{{ $item->name }}</span>
          <span class="idx-desc">{{ $item->description }}</span>
          <span class="idx-mark" aria-hidden="true">→</span>
        </a>
      @endforeach
    </section>
  @endif

  <style>
    .explain{margin-top:var(--space-xl);padding-top:var(--space-md);border-top:var(--rule-hair) solid var(--color-rule)}
    .explain h3{font-family:var(--font-outlier);font-size:var(--text-xs);color:var(--color-ink-3);font-weight:400;letter-spacing:0.08em;margin:0 0 var(--space-xs);text-transform:uppercase}
    .ex-formula{font-family:var(--font-outlier);font-size:0.84rem;line-height:1.8;background:var(--color-paper-2);border:var(--rule-hair) solid var(--color-rule);border-radius:var(--radius-sm);padding:0.7rem 0.9rem;margin:0 0 0.7rem;color:var(--color-ink);overflow-x:auto}
    .ex-text{font-size:var(--text-sm);color:var(--color-ink-2);line-height:1.95;margin:0}
    .ex-text b{color:var(--color-ink)}
  </style>
</div>

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@@graph' => [
    [
      '@@type' => 'Product',
      'name' => $tool->name,
      'description' => $tool->description,
      'url' => route('tools.show', $tool),
      'applicationCategory' => 'UtilityApplication',
      'operatingSystem' => 'Any',
      'brand' => ['@@type' => 'Brand', 'name' => '工具匣 ToolBox'],
      'offers' => ['@@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'CNY'],
    ],
    [
      '@@type' => 'BreadcrumbList',
      'itemListElement' => [
        ['@@type' => 'ListItem', 'position' => 1, 'name' => '首页', 'item' => url('/')],
        ['@@type' => 'ListItem', 'position' => 2, 'name' => '工具', 'item' => route('tools.index')],
        ['@@type' => 'ListItem', 'position' => 3, 'name' => $tool->name, 'item' => route('tools.show', $tool)],
      ],
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
