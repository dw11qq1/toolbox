@extends('layouts.app')

@section('title', $game->name . ' — 工具匣 ToolBox')
@section('meta')
<meta name="description" content="{{ $game->description }}">
@endsection

@section('content')
<div class="wrap game-shell">
  <div class="page-head">
    <p class="k">Game · 小游戏</p>
    <h1>{{ $game->name }}</h1>
  </div>

  @if($game->how_to_play)
    <div class="howto">{{ $game->how_to_play }}</div>
  @endif

  @includeIf('games.pages.'.$game->slug)
</div>
@endsection
