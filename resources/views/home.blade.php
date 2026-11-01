@extends('layouts.app')

@section('title', '工具匣 ToolBox — 在线计算器、开发工具与拼豆图纸生成器')

@section('description', '工具匣 ToolBox 提供 25 个免费在线工具：贷款与复利计算、风控指标矩阵、社保计算、JSON/正则/Cron 开发工具、格式互转、拼豆图纸生成器与趣味测试，全部浏览器本地运行，数据不出设备。')

@section('content')
@php
  $grouped = $tools->groupBy('category');
  $order = ['计算', '开发', '转换', '音乐', '创作'];
  $known = collect($order);
  $extra = $grouped->keys()->reject(fn ($c) => $known->contains($c));
  $order = array_merge($order, $extra->all());
  $categoryCount = $grouped->count();
@endphp

<div class="wrap">
  <header class="index-intro">
    <h1>25 个在线工具，开匣即用。</h1>
    <p>计算器、开发工具、格式转换、拼豆图纸与测试——全部在浏览器本地运行。不注册、不登录，你输入的数据不会离开你的设备。</p>
    <p class="index-meta">
      <span><b>{{ $tools->count() }}</b> 个工具</span>
      <span><b>{{ $categoryCount }}</b> 个分类</span>
      <span><b>{{ $quizzes->count() }}</b> 个测试</span>
      @if(!empty($postCount))
        <span><b>{{ $postCount }}</b> 篇文章</span>
      @endif
      <span>无需登录</span>
    </p>
  </header>

  @foreach($order as $cat)
    @if(isset($grouped[$cat]))
      <section class="idx" aria-labelledby="cat-{{ $loop->index }}">
        <div class="idx-head">
          <h2 id="cat-{{ $loop->index }}">{{ $cat }}</h2>
          <span class="idx-count">{{ $grouped[$cat]->count() }}</span>
        </div>
        @foreach($grouped[$cat] as $tool)
          <a class="idx-row" href="{{ route('tools.show', $tool) }}">
            <span class="idx-name">{{ $tool->name }}</span>
            <span class="idx-desc">{{ $tool->description }}</span>
            <span class="idx-mark" aria-hidden="true">→</span>
          </a>
        @endforeach
      </section>
    @endif
  @endforeach

  <section class="idx" aria-labelledby="cat-tests">
    <div class="idx-head">
      <h2 id="cat-tests">趣味测试</h2>
      <span class="idx-count">{{ $quizzes->count() }}</span>
      <a class="idx-more" href="{{ route('quizzes.index') }}">全部测试 →</a>
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

  <section class="idx" aria-labelledby="cat-blog">
    <div class="idx-head">
      <h2 id="cat-blog">博客</h2>
      @if(!empty($postCount))
        <span class="idx-count">{{ $postCount }}</span>
      @endif
      <a class="idx-more" href="{{ route('blog.index') }}">全部文章 →</a>
    </div>
    @forelse($posts ?? [] as $post)
      <a class="idx-row idx-row--post" href="{{ route('blog.show', $post) }}">
        <span class="idx-name">{{ $post->title }}</span>
        <span class="idx-desc">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 110) }}</span>
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
      '@@type' => 'Organization',
      '@@id' => url('/').'#organization',
      'name' => '工具匣 ToolBox',
      'url' => url('/'),
      'logo' => asset('favicon.svg'),
      'description' => '25 个免费在线工具：财务计算器、开发工具、格式互转、拼豆图纸生成器与趣味测试，全部浏览器本地运行。',
    ],
    [
      '@@type' => 'WebSite',
      '@@id' => url('/').'#website',
      'url' => url('/'),
      'name' => '工具匣 ToolBox',
      'inLanguage' => 'zh-CN',
      'publisher' => ['@@id' => url('/').'#organization'],
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
