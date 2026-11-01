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
@endif
