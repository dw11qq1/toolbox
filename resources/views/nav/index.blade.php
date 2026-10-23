@extends('layouts.app')

@section('title', '导航站 — 工具匣 ToolBox')

@section('content')
<div class="wrap">
  <div class="page-head">
    <p class="k">Nav · 导航站</p>
    <h1>常去的站，都在这。</h1>
    <p class="sub">按用途分组的精选链接，后台可持续添加。</p>
  </div>

  @forelse($links as $category => $group)
    <section class="group">
      <div class="group-title">{{ $category }}</div>
      <div class="nav-grid">
        @foreach($group as $link)
          <a class="nav-card" href="{{ $link->url }}" target="_blank" rel="noopener">
            <div class="nav-card__name">{{ $link->name }} <span class="arrow">↗</span></div>
            @if($link->description)
              <p class="nav-card__desc">{{ $link->description }}</p>
            @endif
          </a>
        @endforeach
      </div>
    </section>
  @empty
    <p class="empty">链接整理中，敬请期待。</p>
  @endforelse
</div>
@endsection
