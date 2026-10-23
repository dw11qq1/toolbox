@extends('layouts.app')

@section('title', '全部工具 — 工具匣 ToolBox')

@section('content')
<div class="wrap">
  <div class="page-head">
    <p class="k">Tools · 工具索引</p>
    <h1>全部工具。</h1>
    <p class="sub">按用途分类，全部本地运行。</p>
  </div>

  @forelse($tools as $category => $group)
    <section class="group">
      <div class="group-title">{{ $category }}</div>
      @foreach($group as $tool)
        <a class="card" href="{{ route('tools.show', $tool) }}">
          <div class="card-row">
            <h2>{{ $tool->name }}</h2>
            <span class="arrow">→</span>
          </div>
          <p class="desc">{{ $tool->description }}</p>
        </a>
      @endforeach
    </section>
  @empty
    <p class="empty">工具上架中，敬请期待。</p>
  @endforelse
</div>
@endsection
