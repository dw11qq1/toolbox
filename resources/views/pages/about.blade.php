@extends('layouts.app')

@section('title', '关于 — 工具匣 ToolBox')
@section('meta')
<meta name="description" content="工具匣 ToolBox 是一个纯前端、数据不出浏览器的在线工具站，由后端开发工程师汪洋独立开发与维护。">
@endsection

@section('content')
<div class="wrap">
  @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '关于', 'url' => url('/about')]]; @endphp
  @include('partials.breadcrumb')

  <div class="page-head">
    <p class="k">About · 关于</p>
    <h1>关于工具匣。</h1>
    <p class="sub">一个只想把小事做好的工具站。</p>
  </div>

  <div class="prose">
    <p>工具匣 ToolBox 是一个<strong>纯前端、数据不出浏览器</strong>的在线工具集合。这里的计算器、拼豆图纸生成器、乐器舞台与测试，全部在你的浏览器本地运行，不会把任何输入上传到服务器。</p>
    <p>站点由后端开发工程师 <strong>汪洋</strong> 独立开发并维护，技术栈为 Laravel + 原生 JavaScript。初衷很简单：把经常要用、却又散落在各处的小工具，集中到一个干净、快速、不打扰的地方。</p>
    <p>目前上线 25 个免费工具，持续更新中。如果某个工具帮到了你，或你有想提的需求，欢迎通过 <a href="{{ route('contact') }}">联系页</a> 告诉我。</p>
  </div>
</div>
@endsection
