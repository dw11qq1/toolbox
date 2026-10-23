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
</div>
@endsection
