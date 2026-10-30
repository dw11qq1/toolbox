@extends('layouts.app')

@section('title', $quiz->name . ' — 工具匣 ToolBox')
@section('meta')
<meta name="description" content="{{ $quiz->description }}">
@endsection

@section('content')
<div class="wrap game-shell">
  <div class="page-head">
    <p class="k">Test · 趣味测试</p>
    <h1>{{ $quiz->name }}</h1>
    <p class="sub">{{ $quiz->description }}</p>
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
