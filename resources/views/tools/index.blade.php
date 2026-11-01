@extends('layouts.app')

@section('title', '全部工具 — 工具匣 ToolBox')

@section('description', '工具匣全部在线工具索引：财务计算器、开发工具、格式转换、乐器与拼豆图纸生成器，共 25 个，全部在浏览器本地运行。')

@section('content')
<div class="wrap">
  @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '工具', 'url' => route('tools.index')]]; @endphp
  @include('partials.breadcrumb')

  <div class="page-head">
    <h1>全部工具。</h1>
    <p class="sub">按用途分类，共 {{ $tools->flatten()->count() }} 个。全部在浏览器里运行，输入的数据不会上传。</p>
  </div>

  @forelse($tools as $category => $group)
    <section class="idx" aria-labelledby="tools-{{ $loop->index }}">
      <div class="idx-head">
        <h2 id="tools-{{ $loop->index }}">{{ $category }}</h2>
        <span class="idx-count">{{ $group->count() }}</span>
      </div>
      @foreach($group as $tool)
        <a class="idx-row" href="{{ route('tools.show', $tool) }}">
          <span class="idx-name">{{ $tool->name }}</span>
          <span class="idx-desc">{{ $tool->description }}</span>
          <span class="idx-mark" aria-hidden="true">→</span>
        </a>
      @endforeach
    </section>
  @empty
    <p class="empty">工具上架中，敬请期待。</p>
  @endforelse
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
        ['@@type' => 'ListItem', 'position' => 2, 'name' => '工具', 'item' => route('tools.index')],
      ],
    ],
    [
      '@@type' => 'ItemList',
      'name' => '工具匣在线工具索引',
      'itemListElement' => $tools->flatten()->values()->map(fn ($tool, $i) => [
        '@@type' => 'ListItem',
        'position' => $i + 1,
        'name' => $tool->name,
        'url' => route('tools.show', $tool),
      ])->all(),
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
