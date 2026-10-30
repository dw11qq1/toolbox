@extends('layouts.app')

@section('title', '全部工具 — 工具匣 ToolBox')

@section('content')
<div class="wrap">
  <div class="page-head">
    <p class="k">Tools · 工具索引</p>
    <h1>全部工具。</h1>
    <p class="sub">按用途分类，全部本地运行。</p>
  </div>

  @forelse($tools as $category => $group)
    <section class="group">
      <div class="group-title">{{ $category }}</div>
      <div class="nav-grid">
        @foreach($group as $tool)
          <a class="nav-card" href="{{ route('tools.show', $tool) }}">
            <span class="nav-card__name">{{ $tool->name }} <span class="arrow">→</span></span>
            <span class="nav-card__desc">{{ $tool->description }}</span>
          </a>
        @endforeach
      </div>
    </section>
  @empty
    <p class="empty">工具上架中，敬请期待。</p>
  @endforelse
</div>
@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@@type' => 'BreadcrumbList',
  'itemListElement' => [
    ['@@type' => 'ListItem', 'position' => 1, 'name' => '首页', 'item' => url('/')],
    ['@@type' => 'ListItem', 'position' => 2, 'name' => '工具', 'item' => route('tools.index')],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
