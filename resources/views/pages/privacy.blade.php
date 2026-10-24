@extends('layouts.app')

@section('title', '隐私政策 — 工具匣 ToolBox')

@section('description', '工具匣 ToolBox 的隐私政策：所有计算在浏览器本地完成，数据不上传服务器；说明 Cookie 与第三方广告（Google AdSense）的使用方式。')

@section('content')
<div class="wrap" style="max-width:760px">
  <div class="page-head">
    <p class="k">Privacy · 隐私政策</p>
    <h1>隐私政策</h1>
    <p class="sub">最后更新：{{ date('Y 年 m 月 d 日') }}</p>
  </div>

  <article class="prose">
    <h2>一、我们收集什么</h2>
    <p><b>几乎什么都不收集。</b>本站所有工具（计算器、转换器、开发工具、拼豆图纸生成器）都在你的浏览器里用 JavaScript 本地运算，你输入的数据<b>不会发送到服务器</b>，也不会被保存。</p>
    <p>服务端日志会像任何普通网站一样记录访问 IP、时间、请求的 URL 与浏览器标识，仅用于排查故障和统计访问量，不做身份识别。</p>

    <h2>二、本地存储</h2>
    <p>部分功能会把你的输入暂存在浏览器的 <code>localStorage</code> 里（比如拼豆图纸、主题偏好），目的是刷新页面不丢失内容。这些数据只存在你自己的设备上，你可以随时在浏览器设置中清除。</p>

    <h2>三、Cookie 与第三方广告</h2>
    <p>本站接入了 <b>Google AdSense</b> 广告服务。Google 及合作伙伴会使用 Cookie（含双重 Cookie / DART Cookie）基于你访问本站及其他网站的行为投放个性化广告。</p>
    <p>你可以通过 Google 的广告设置页面选择退出个性化广告：<br>
      <a href="https://www.google.com/settings/ads" target="_blank" rel="noopener">https://www.google.com/settings/ads</a>
    </p>
    <p>第三方广告商可能直接向你的浏览器发送 Cookie，本站无法控制这些 Cookie，你可在浏览器设置中禁用或删除。</p>

    <h2>四、儿童隐私</h2>
    <p>本站内容面向普通用户，不面向 13 岁以下儿童定向收集信息。若你认为孩子在未经同意的情况下提供了信息，请联系我们删除。</p>

    <h2>五、政策更新</h2>
    <p>本站可能会不定期更新本政策，更新后的版本会直接在此页面发布，继续使用即视为接受。</p>

    <h2>六、联系我们</h2>
    <p>对本政策有疑问，可通过邮件联系：<a href="mailto:3023207113@qq.com">3023207113@qq.com</a></p>
  </article>

  <style>
    .prose h2{font-size:1rem;font-weight:700;margin:1.6rem 0 0.5rem;color:var(--ink)}
    .prose p{font-size:0.9rem;line-height:1.95;color:var(--ink-2);margin:0 0 0.7rem}
    .prose b{color:var(--ink)}
    .prose a{color:var(--accent)}
    .prose code{font-family:var(--font-mono);font-size:0.82rem;background:var(--panel);border:1px solid var(--line);border-radius:4px;padding:0.1rem 0.35rem}
  </style>
</div>
@endsection
