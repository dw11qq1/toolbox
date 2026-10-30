@extends('layouts.app')

@section('title', '联系 — 工具匣 ToolBox')
@section('meta')
<meta name="description" content="联系工具匣 ToolBox 站长汪洋：反馈问题、提交需求或商务合作。">
@endsection

@section('content')
<div class="wrap">
  @php $breadcrumbs = [['name' => '首页', 'url' => url('/')], ['name' => '联系', 'url' => url('/contact')]]; @endphp
  @include('partials.breadcrumb')

  <div class="page-head">
    <p class="k">Contact · 联系</p>
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

<style>
.contact-list{list-style:none;padding:0;margin:0 0 1.4rem;display:flex;flex-direction:column;gap:.9rem}
.contact-list li{display:flex;align-items:center;gap:1rem;font-size:.95rem}
.contact-list .lbl{display:inline-block;min-width:3.5rem;color:var(--ink-3);font-size:.8rem;letter-spacing:.08em}
.contact-list a{color:var(--ink);text-decoration:none;border-bottom:1px solid var(--line)}
.contact-list a:hover{color:var(--accent, #2563eb);border-color:currentColor}
.muted-note{color:var(--ink-3);font-size:.88rem;line-height:1.9}
</style>
@endsection
