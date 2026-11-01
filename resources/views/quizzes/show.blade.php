@extends('layouts.app')

@section('title', $quiz->name . ' — 工具匣 ToolBox')
@section('description', $quiz->description)

@section('content')
<div class="wrap game-shell">
  @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '测试', 'url' => route('quizzes.index')], ['name' => $quiz->name, 'url' => route('quizzes.show', $quiz)]]; @endphp
  @include('partials.breadcrumb')
  <div class="page-head">
    <h1>{{ $quiz->name }}</h1>
    <p class="sub">{{ $quiz->description }}</p>
    <p class="page-meta">趣味测试 · 本地计分 · 结果不保存</p>
  </div>

  @includeIf('quizzes.pages.'.$quiz->slug)
</div>

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@@type' => 'BreadcrumbList',
  'itemListElement' => [
    ['@@type' => 'ListItem', 'position' => 1, 'name' => '首页', 'item' => url('/')],
    ['@@type' => 'ListItem', 'position' => 2, 'name' => '测试', 'item' => route('quizzes.index')],
    ['@@type' => 'ListItem', 'position' => 3, 'name' => $quiz->name, 'item' => route('quizzes.show', $quiz)],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
