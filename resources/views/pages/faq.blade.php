@extends('layouts.app')

@section('title', '常见问题 — 工具匣 ToolBox')
@section('meta')
<meta name="description" content="关于工具匣 ToolBox 的常见问题：数据隐私、拼豆图纸、乐器舞台、计算器公式与免费政策。">
@endsection

@php
$faqs = [
    [
        'q' => '工具计算的数据会被上传吗？',
        'a' => '不会。本站所有工具（计算器、拼豆、乐器舞台、测试）都在你的浏览器本地运行，输入数据不会发送到任何服务器，关掉页面即清空。',
    ],
    [
        'q' => '拼豆图纸生成器怎么用？',
        'a' => '上传一张图片，选择底板尺寸与色卡型号，系统会生成像素化的拼豆图纸，支持多图层编辑、用量统计，并可导出 PNG 或 PDF 直接打印。',
    ],
    [
        'q' => '乐器舞台可以保存和导出吗？',
        'a' => '可以。每件乐器独立异步演奏，你可以用「保存」把当前组合存到浏览器本地，下次自动恢复；也能导出 8 秒音频片段到本地。',
    ],
    [
        'q' => '计算器用的公式准确吗？可以当作投资建议吗？',
        'a' => '各风控计算器（夏普、索提诺、卡玛、仓位、风险回报比等）的公式均为金融领域标准定义，页面也附了公式说明。但结果仅供参考，不构成任何投资建议。',
    ],
    [
        'q' => '网站是免费的吗？靠什么维持？',
        'a' => '全部工具与博客永久免费。站点通过第三方广告（Google AdSense）获得少量收入来维持服务器与持续开发，广告不影响工具本身的使用。',
    ],
    [
        'q' => '怎么联系站长或提交需求？',
        'a' => '欢迎通过 联系页 的邮箱或 GitHub 反馈问题、提交新工具需求，我都会认真看。',
    ],
];
@endphp

@section('content')
<div class="wrap">
  @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '常见问题', 'url' => url('/faq')]]; @endphp
  @include('partials.breadcrumb')

  <div class="page-head">
    <p class="k">FAQ · 常见问题</p>
    <h1>你可能想问。</h1>
  </div>

  <div class="faq-list">
    @foreach($faqs as $item)
      <div class="faq-item">
        <h2 class="faq-q">{{ $item['q'] }}</h2>
        <p class="faq-a">{{ $item['a'] }}</p>
      </div>
    @endforeach
  </div>
</div>

<style>
.faq-list{display:flex;flex-direction:column;gap:1.4rem;margin-top:.5rem}
.faq-item{border:1px solid var(--line);border-radius:10px;padding:1.1rem 1.3rem;background:var(--panel, #fff)}
.faq-q{font-size:1rem;margin:0 0 .5rem;color:var(--ink)}
.faq-a{font-size:.92rem;line-height:1.9;color:var(--ink-2);margin:0}
</style>

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@@type' => 'FAQPage',
  'mainEntity' => collect($faqs)->map(function ($f) {
    return [
      '@@type' => 'Question',
      'name' => $f['q'],
      'acceptedAnswer' => [
        '@@type' => 'Answer',
        'text' => $f['a'],
      ],
    ];
  })->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
