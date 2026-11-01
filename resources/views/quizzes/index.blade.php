@extends('layouts.app')

@section('title', '趣味测试 — 工具匣 ToolBox')
@section('description', '工具匣 ToolBox 的趣味测试：MBTI 人格、恋爱风格、逻辑推理与反应速度，全部本地计分，结果不联网、不保存。')

@section('content')
<div class="wrap">
  @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '测试', 'url' => route('quizzes.index')]]; @endphp
  @include('partials.breadcrumb')
  <div class="page-head">
    <h1>测一测，更了解自己。</h1>
    <p class="sub">全部本地计分，结果不联网、不保存。</p>
  </div>

  <section class="idx">
    <div class="idx-head">
      <h2>全部测试</h2>
      <span class="idx-count">{{ $quizzes->count() }}</span>
    </div>
    @forelse($quizzes as $quiz)
      <a class="idx-row" href="{{ route('quizzes.show', $quiz) }}">
        <span class="idx-name">{{ $quiz->name }}</span>
        <span class="idx-desc">{{ $quiz->description }}</span>
        <span class="idx-mark" aria-hidden="true">→</span>
      </a>
    @empty
      <p class="empty">测试上线中，敬请期待。</p>
    @endforelse
  </section>
</div>
@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@@type' => 'BreadcrumbList',
  'itemListElement' => [
    ['@@type' => 'ListItem', 'position' => 1, 'name' => '首页', 'item' => url('/')],
    ['@@type' => 'ListItem', 'position' => 2, 'name' => '测试', 'item' => route('quizzes.index')],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
