@extends('layouts.app')

@section('title', '页面走丢了 — 工具匣 ToolBox')

@section('content')
<div class="wrap" style="max-width:720px;text-align:center;padding-top:2.5rem">
  <p class="k" style="font-family:var(--font-mono);font-size:2.6rem;color:var(--accent);margin:0">404</p>
  <h1 style="margin:0.4rem 0 0.5rem">这个页面没找到</h1>
  <p class="sub">链接可能过期了，或者地址敲错了。试试搜一下你要用的工具：</p>

  <div class="nf-search">
    <input type="search" id="nfInput" placeholder="搜索工具，比如「贷款」「正则」「拼豆」…" autocomplete="off">
  </div>
  <div id="nfResults" class="nav-grid" style="padding-top:1rem"></div>

  <p class="sub" style="margin-top:1.6rem">或者直接从这些入口回去：</p>
  <div class="btn-row" style="justify-content:center;flex-wrap:wrap;margin-top:0.6rem">
    <a class="btn" href="{{ route('home') }}">首页</a>
    <a class="btn" href="{{ route('tools.index') }}">全部工具</a>
    <a class="btn" href="{{ route('quizzes.index') }}">趣味测试</a>
    <a class="btn" href="{{ route('blog.index') }}">博客</a>
  </div>

  <style>
    .nf-search{margin:1.2rem auto 0;max-width:420px}
    .nf-search input{width:100%;font-size:1rem;padding:0.6rem 0.9rem;border:1px solid var(--line);background:var(--panel);color:var(--ink);border-radius:999px}
    .nf-search input:focus{outline:2px solid var(--accent);outline-offset:2px}
    #nfResults:empty{display:none}
  </style>

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
        box.innerHTML = '<p class="sub">没找到相关工具，试试别的关键词。</p>';
        return;
      }
      hits.forEach(function(h){
        var a = document.createElement('a');
        a.className = 'nav-card';
        a.href = h.u;
        a.innerHTML = '<span class="nav-card__name">' + h.n + ' <span class="arrow">→</span></span>' +
                      '<span class="nav-card__desc">' + h.d + '</span>';
        box.appendChild(a);
      });
    });
  })();
  </script>
</div>
@endsection
