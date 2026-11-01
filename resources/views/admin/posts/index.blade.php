@extends('admin.layout')

@section('title', '文章 — 工具匣 管理台')
@section('kicker', 'Posts')
@section('heading', '文章')

@section('actions')
  <a class="adm-btn adm-btn--primary" href="{{ route('admin.posts.create') }}">新建文章</a>
@endsection

@section('content')
<section class="adm-card">
  <header class="adm-card__head">
    <h2>全部文章</h2>
    <span class="adm-card__meta">{{ $posts->count() }} 篇</span>
  </header>

  @if($posts->isEmpty())
    <p class="adm-empty">还没有文章，<a href="{{ route('admin.posts.create') }}">写第一篇</a>。</p>
  @else
    <div class="adm-table-wrap">
      <table class="adm-table adm-table--posts">
        <thead>
          <tr>
            <th scope="col">标题</th>
            <th scope="col">状态</th>
            <th scope="col">发布时间</th>
            <th scope="col">更新时间</th>
            <th scope="col"><span class="adm-sr">操作</span></th>
          </tr>
        </thead>
        <tbody>
          @foreach($posts as $post)
            <tr>
              <td>
                <a class="adm-title-link" href="{{ route('admin.posts.edit', $post->id) }}">{{ $post->title }}</a>
                <span class="adm-mono adm-muted adm-block">/blog/{{ $post->slug }}</span>
              </td>
              <td>
                @if($post->is_published)
                  <span class="adm-badge adm-badge--ok">已发布</span>
                @else
                  <span class="adm-badge">草稿</span>
                @endif
              </td>
              <td class="adm-mono adm-muted">{{ $post->published_at?->format('Y-m-d') ?? '—' }}</td>
              <td class="adm-mono adm-muted">{{ $post->updated_at?->format('Y-m-d H:i') }}</td>
              <td class="is-actions">
                @if($post->is_published)
                  <a class="adm-link" href="{{ route('blog.show', $post) }}" target="_blank" rel="noopener">查看</a>
                @endif
                <a class="adm-link" href="{{ route('admin.posts.edit', $post->id) }}">编辑</a>
                <form class="adm-inline-form" method="POST" action="{{ route('admin.posts.destroy', $post->id) }}" data-confirm="删除《{{ $post->title }}》？此操作不可撤销。">
                  @csrf
                  @method('DELETE')
                  <button class="adm-link adm-link--danger" type="submit">删除</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</section>
@endsection
