@extends('admin.layout')

@section('title', '概览 — 工具匣 管理台')
@section('kicker', 'Dashboard')
@section('heading', '站点概览')

@section('content')
@unless($statsAvailable)
  <div class="adm-flash adm-flash--warn" role="status">
    访问统计表尚未创建：在服务器执行 <code>php artisan migrate --force</code> 后即可开始记录。
  </div>
@endunless

<section class="adm-stats" aria-label="访问数据">
  <article class="adm-stat">
    <p class="adm-stat__k">今日浏览量 · PV</p>
    <p class="adm-stat__v">{{ number_format($todayStats['pv']) }}</p>
    <p class="adm-stat__sub">
      昨日 {{ number_format($yesterdayStats['pv']) }}
      @if($todayPvDelta !== null)
        <span class="adm-delta {{ $todayPvDelta >= 0 ? 'is-up' : 'is-down' }}">{{ $todayPvDelta >= 0 ? '+' : '' }}{{ $todayPvDelta }}%</span>
      @endif
    </p>
  </article>

  <article class="adm-stat">
    <p class="adm-stat__k">今日访客 · UV</p>
    <p class="adm-stat__v">{{ number_format($todayStats['uv']) }}</p>
    <p class="adm-stat__sub">
      昨日 {{ number_format($yesterdayStats['uv']) }}
      @if($todayUvDelta !== null)
        <span class="adm-delta {{ $todayUvDelta >= 0 ? 'is-up' : 'is-down' }}">{{ $todayUvDelta >= 0 ? '+' : '' }}{{ $todayUvDelta }}%</span>
      @endif
    </p>
  </article>

  <article class="adm-stat">
    <p class="adm-stat__k">近 7 天</p>
    <p class="adm-stat__v">{{ number_format($weekStats['pv']) }} <span class="adm-stat__unit">PV</span></p>
    <p class="adm-stat__sub">{{ number_format($weekStats['uv']) }} 位访客</p>
  </article>

  <article class="adm-stat">
    <p class="adm-stat__k">近 30 天</p>
    <p class="adm-stat__v">{{ number_format($monthStats['pv']) }} <span class="adm-stat__unit">PV</span></p>
    <p class="adm-stat__sub">{{ number_format($monthStats['uv']) }} 位访客</p>
  </article>

  <article class="adm-stat">
    <p class="adm-stat__k">累计浏览量</p>
    <p class="adm-stat__v">{{ number_format($totalPv) }}</p>
    <p class="adm-stat__sub">自统计启用起</p>
  </article>
</section>

<section class="adm-card">
  <header class="adm-card__head">
    <h2>近 14 天浏览量</h2>
    <span class="adm-card__meta">{{ number_format($series->sum('pv')) }} PV</span>
  </header>

  <div class="adm-chart" role="img" aria-label="近 14 天每日浏览量柱状图">
    @foreach($series as $day)
      <div class="adm-chart__col" title="{{ $day['date'] }} · {{ $day['pv'] }} PV">
        <div class="adm-chart__track">
          <div class="adm-chart__bar" style="height: {{ $day['pv'] > 0 ? max(3, (int) round($day['pv'] / $maxPv * 100)) : 0 }}%"></div>
        </div>
        <span class="adm-chart__label">{{ $day['label'] }}</span>
      </div>
    @endforeach
  </div>
</section>

<div class="adm-grid-2">
  <section class="adm-card">
    <header class="adm-card__head">
      <h2>热门页面</h2>
      <span class="adm-card__meta">近 7 天 · PV</span>
    </header>

    @if($topPages->isEmpty())
      <p class="adm-empty">还没有访问数据。打开站点逛一圈，这里就会出现内容。</p>
    @else
      <div class="adm-table-wrap">
        <table class="adm-table adm-table--pages">
          <thead>
            <tr><th scope="col">页面</th><th scope="col">路径</th><th scope="col" class="is-num">PV</th></tr>
          </thead>
          <tbody>
            @foreach($topPages as $page)
              <tr>
                <td>{{ $pageLabels[$page->path] ?? $page->path }}</td>
                <td class="adm-mono adm-muted">{{ $page->path }}</td>
                <td class="is-num adm-mono">{{ number_format($page->pv) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>

  <section class="adm-card">
    <header class="adm-card__head">
      <h2>内容概览</h2>
      <a class="adm-card__link" href="{{ route('admin.posts.index') }}">管理文章 →</a>
    </header>

    <dl class="adm-kv">
      <div>
        <dt>文章</dt>
        <dd>{{ $counts['posts'] }} 篇 <span class="adm-muted">· {{ $counts['posts_published'] }} 已发布 / {{ $counts['posts'] - $counts['posts_published'] }} 草稿</span></dd>
      </div>
      <div>
        <dt>工具</dt>
        <dd>{{ $counts['tools'] }} 个 <span class="adm-muted">· {{ $counts['tools_active'] }} 上线</span></dd>
      </div>
      <div>
        <dt>测试</dt>
        <dd>{{ $counts['quizzes'] }} 个 <span class="adm-muted">· {{ $counts['quizzes_active'] }} 上线</span></dd>
      </div>
    </dl>

    <div class="adm-quick">
      <a class="adm-btn" href="{{ route('admin.posts.create') }}">写新文章</a>
      <a class="adm-btn" href="{{ route('admin.tools.index') }}">调整工具</a>
      <a class="adm-btn" href="{{ route('admin.quizzes.index') }}">调整测试</a>
    </div>
  </section>
</div>

<section class="adm-card">
  <header class="adm-card__head">
    <h2>最近更新</h2>
    <a class="adm-card__link" href="{{ route('admin.posts.index') }}">全部文章 →</a>
  </header>

  @if($recentPosts->isEmpty())
    <p class="adm-empty">还没有文章，<a href="{{ route('admin.posts.create') }}">写第一篇</a>。</p>
  @else
    <div class="adm-table-wrap">
      <table class="adm-table adm-table--recent">
        <thead>
          <tr><th scope="col">标题</th><th scope="col">状态</th><th scope="col">更新时间</th><th scope="col"></th></tr>
        </thead>
        <tbody>
          @foreach($recentPosts as $post)
            <tr>
              <td>{{ $post->title }}</td>
              <td>
                @if($post->is_published)
                  <span class="adm-badge adm-badge--ok">已发布</span>
                @else
                  <span class="adm-badge">草稿</span>
                @endif
              </td>
              <td class="adm-mono adm-muted">{{ $post->updated_at?->format('Y-m-d H:i') }}</td>
              <td class="is-actions"><a class="adm-link" href="{{ route('admin.posts.edit', $post->id) }}">编辑</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</section>
@endsection
