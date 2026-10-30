@if(!empty($breadcrumbs))
<nav class="tb-breadcrumb" aria-label="面包屑导航">
  @foreach($breadcrumbs as $i => $bc)
    @if($i > 0)<span class="sep" aria-hidden="true">/</span>@endif
    @if(!$loop->last)
      <a href="{{ $bc['url'] }}">{{ $bc['name'] }}</a>
    @else
      <span class="current" aria-current="page">{{ $bc['name'] }}</span>
    @endif
  @endforeach
</nav>
<style>
.tb-breadcrumb{display:flex;flex-wrap:wrap;align-items:center;gap:.55rem;font-size:.82rem;color:var(--ink-3);margin:0 0 1.4rem}
.tb-breadcrumb a{color:var(--ink-3);text-decoration:none}
.tb-breadcrumb a:hover{color:var(--ink);text-decoration:underline}
.tb-breadcrumb .sep{color:var(--line)}
.tb-breadcrumb .current{color:var(--ink-2);font-weight:500}
</style>
@endif
