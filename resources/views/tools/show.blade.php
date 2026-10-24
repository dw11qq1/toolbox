@extends('layouts.app')

@section('title', $tool->name . ' — 工具匣 ToolBox')
@section('meta')
<meta name="description" content="{{ $tool->description }}">
@endsection

@section('content')
<div class="wrap tool-shell">
  <div class="page-head">
    <p class="k">Tool · {{ $tool->category }}</p>
    <h1>{{ $tool->name }}</h1>
    <p class="sub">{{ $tool->description }}</p>
  </div>

  @includeIf('tools.pages.'.$tool->slug)

  @if($related->isNotEmpty())
    <section class="group" style="margin-top:2rem">
      <div class="group-title">相关工具</div>
      <div class="nav-grid">
        @foreach($related as $item)
          <a class="nav-card" href="{{ route('tools.show', $item) }}">
            <span class="nav-card__name">{{ $item->name }} <span class="arrow">→</span></span>
            <span class="nav-card__desc">{{ $item->description }}</span>
          </a>
        @endforeach
      </div>
    </section>
  @endif

  <style>
    .explain{margin-top:1.8rem;padding-top:1.2rem;border-top:1px solid var(--line)}
    .explain h3{font-size:0.72rem;color:var(--ink-3);font-weight:600;letter-spacing:0.12em;margin:0 0 0.6rem;text-transform:uppercase}
    .ex-formula{font-family:var(--font-mono);font-size:0.85rem;line-height:1.8;background:var(--panel);border:1px solid var(--line);border-radius:6px;padding:0.7rem 0.9rem;margin:0 0 0.7rem;color:var(--ink);overflow-x:auto}
    .ex-text{font-size:0.88rem;color:var(--ink-2);line-height:1.95;margin:0}
    .ex-text b{color:var(--ink)}
  </style>
</div>
@endsection
