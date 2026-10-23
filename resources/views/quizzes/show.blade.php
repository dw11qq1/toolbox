@extends('layouts.app')

@section('title', $quiz->name . ' — 工具匣 ToolBox')
@section('meta')
<meta name="description" content="{{ $quiz->description }}">
@endsection

@section('content')
<div class="wrap game-shell">
  <div class="page-head">
    <p class="k">Test · 趣味测试</p>
    <h1>{{ $quiz->name }}</h1>
    <p class="sub">{{ $quiz->description }}</p>
  </div>

  @includeIf('quizzes.pages.'.$quiz->slug)
</div>
@endsection
