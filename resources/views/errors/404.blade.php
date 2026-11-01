@extends('layouts.app')

@section('title', '页面走丢了 — 工具匣 ToolBox')
@section('description', '这个页面没有找到。可以回到首页，或直接搜索你要用的工具。')

@section('content')
<div class="wrap error-page">
  <p class="error-code">404</p>
  <h1>这个页面没找到</h1>
  <p class="error-sub">链接可能过期了，或者地址敲错了。试试搜一下你要用的工具：</p>

  <div class="nf-search">
    <input type="search" id="nfInput" placeholder="搜索工具，比如「贷款」「正则」「拼豆」…" autocomplete="off" aria-label="搜索工具">
  </div>
  <div id="nfResults" class="nf-results"></div>

  <p class="error-sub" style="margin-top:var(--space-lg)">或者直接从这些入口回去：</p>
  <div class="btn-row error-actions">
    <a class="btn" href="{{ route('home') }}">首页</a>
    <a class="btn" href="{{ route('tools.index') }}">全部工具</a>
    <a class="btn" href="{{ route('quizzes.index') }}">趣味测试</a>
    <a class="btn" href="{{ route('blog.index') }}">博客</a>
  </div>

  <script>
  (function(){
    var index = window.TB_INDEX || [];
    var input = document.getElementById('nfInput');
    var box = document.getElementById('nfResults');
    input.addEventListener('input', function(){
      var q = input.value.trim().toLowerCase();
      box.innerHTML = '';
      if(!q) return;
      var hits = index.filter(function(i){
        return (i.n + ' ' + i.d + ' ' + i.c).toLowerCase().indexOf(q) >= 0;
      }).slice(0, 8);
      if(!hits.length){
        var empty = document.createElement('p');
        empty.className = 'empty';
        empty.textContent = '没找到相关工具，试试别的关键词。';
        box.appendChild(empty);
        return;
      }
      hits.forEach(function(h){
        var a = document.createElement('a');
        a.className = 'nav-card';
        a.href = h.u;
        var name = document.createElement('span');
        name.className = 'nav-card__name';
        name.textContent = h.n + ' →';
        var desc = document.createElement('span');
        desc.className = 'nav-card__desc';
        desc.textContent = h.d;
        a.appendChild(name);
        a.appendChild(desc);
        box.appendChild(a);
      });
    });
  })();
  </script>
</div>
@endsection
