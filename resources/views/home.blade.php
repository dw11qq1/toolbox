@extends('layouts.app')

@section('title', '工具匣 ToolBox — 在线计算器、开发工具与拼豆图纸生成器')

@section('description', '工具匣 ToolBox 提供 25 个免费在线工具：贷款与复利计算、风控指标矩阵、社保计算、JSON/正则/Cron 开发工具、格式互转、拼豆图纸生成器与趣味测试，全部浏览器本地运行，数据不出设备。')

@section('content')
<div class="wrap">
  <div class="page-head">
    <p class="k">ToolBox · 工具匣</p>
    <h1>25 个在线工具，开匣即用。</h1>
    <p class="sub">计算器、开发工具、格式转换、拼豆图纸——全部在浏览器本地运行，不注册、不登录，你输入的数据不会离开你的设备。</p>
  </div>

  @php
    $grouped = $tools->groupBy('category');
    $order = ['计算', '开发', '转换', '音乐'];
  @endphp

  @foreach($order as $cat)
    @if(isset($grouped[$cat]))
      <section class="group">
        <div class="group-title">{{ $cat }}</div>
        <div class="nav-grid">
          @foreach($grouped[$cat] as $tool)
            <a class="nav-card" href="{{ route('tools.show', $tool) }}">
              <span class="nav-card__name">{{ $tool->name }} <span class="arrow">→</span></span>
              <span class="nav-card__desc">{{ $tool->description }}</span>
            </a>
          @endforeach
        </div>
      </section>
    @endif
  @endforeach

  <section class="group">
    <div class="group-title">趣味测试 · Tests</div>
    <div class="nav-grid">
      @forelse($quizzes as $quiz)
        <a class="nav-card" href="{{ route('quizzes.show', $quiz) }}">
          <span class="nav-card__name">{{ $quiz->name }} <span class="arrow">→</span></span>
          <span class="nav-card__desc">{{ $quiz->description }}</span>
        </a>
      @empty
        <p class="empty">测试上线中，敬请期待。</p>
      @endforelse
      <a class="nav-card" href="{{ route('quizzes.index') }}">
        <span class="nav-card__name">全部测试 <span class="arrow">→</span></span>
        <span class="nav-card__desc">四个趣味测试一览。</span>
      </a>
    </div>
  </section>

  <section class="group">
    <div class="group-title">拼豆 · Creation</div>
    <div class="nav-grid">
      <a class="nav-card" href="{{ route('tools.show', 'perler-patterns') }}">
        <span class="nav-card__name">拼豆图纸工作室 <span class="arrow">→</span></span>
        <span class="nav-card__desc">照片一键转拼豆图纸：MARD / Perler / Artkal 三色卡匹配互转、多图层编辑、自动用豆统计、导出 PNG/PDF。</span>
      </a>
    </div>
  </section>

  <section class="group">
    <div class="group-title">博客 · Blog</div>
    <div class="nav-grid">
      <a class="nav-card" href="{{ route('blog.index') }}">
        <span class="nav-card__name">写下来的思考 <span class="arrow">→</span></span>
        <span class="nav-card__desc">开发路上踩过的坑、想明白的事。</span>
      </a>
    </div>
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
