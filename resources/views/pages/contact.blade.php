@extends('layouts.app')

@section('title', '联系 — 工具匣 ToolBox')
@section('description', '联系工具匣 ToolBox 站长汪洋：反馈问题、提交需求或商务合作。')

@section('content')
<div class="wrap">
  @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '联系', 'url' => url('/contact')]]; @endphp
  @include('partials.breadcrumb')

  <div class="page-head">
    <h1>说说你的想法。</h1>
    <p class="sub">反馈问题、提交需求，或只是打个招呼都欢迎。</p>
  </div>

  <div class="prose">
    <ul class="contact-list">
      <li><span class="lbl">邮箱</span><a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></li>
      <li><span class="lbl">GitHub</span><a href="{{ config('site.github') }}" target="_blank" rel="noopener">{{ config('site.github') }}</a></li>
      <li><span class="lbl">站点</span><a href="{{ url('/') }}">xqlwy.online</a></li>
    </ul>
    <p class="muted-note">本站坚持「数据不出浏览器」原则、未接入表单后端，请直接通过上述渠道联系，我会尽快回复。</p>
  </div>
</div>
@endsection
