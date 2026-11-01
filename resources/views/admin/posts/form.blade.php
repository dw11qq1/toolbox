@extends('admin.layout')

@section('title', ($post->exists ? '编辑文章' : '新建文章').' — 工具匣 管理台')
@section('kicker', 'Posts')
@section('heading', $post->exists ? '编辑文章' : '新建文章')

@section('actions')
  <a class="adm-btn" href="{{ route('admin.posts.index') }}">← 返回列表</a>
@endsection

@section('content')
<form method="POST"
      action="{{ $post->exists ? route('admin.posts.update', $post->id) : route('admin.posts.store') }}"
      enctype="multipart/form-data">
  @csrf
  @if($post->exists)
    @method('PUT')
  @endif

  <div class="adm-form-grid">
    <div class="adm-form-main">
      <section class="adm-card">
        <header class="adm-card__head">
          <h2>正文</h2>
          <span class="adm-card__meta">HTML</span>
        </header>

        <label class="adm-field">
          <span class="adm-field__label">标题</span>
          <input class="adm-input" id="postTitle" type="text" name="title" value="{{ old('title', $post->title) }}" maxlength="160" required>
          @error('title')<span class="adm-field__err">{{ $message }}</span>@enderror
        </label>

        <label class="adm-field">
          <span class="adm-field__label">slug <span class="adm-hint">小写字母、数字、连字符，用于 /blog/…</span></span>
          <input class="adm-input adm-mono" id="postSlug" type="text" name="slug" value="{{ old('slug', $post->slug) }}" maxlength="120" required>
          @error('slug')<span class="adm-field__err">{{ $message }}</span>@enderror
        </label>

        <label class="adm-field">
          <span class="adm-field__label">摘要 <span class="adm-hint">选填，最多 300 字</span></span>
          <textarea class="adm-input" name="excerpt" rows="2" maxlength="300">{{ old('excerpt', $post->excerpt) }}</textarea>
          @error('excerpt')<span class="adm-field__err">{{ $message }}</span>@enderror
        </label>

        <div class="adm-field">
          <div class="adm-field__bar">
            <span class="adm-field__label">正文 <span class="adm-hint">按 HTML 渲染</span></span>
            <button class="adm-btn adm-btn--small" type="button" id="previewToggle" aria-expanded="false" aria-controls="postPreview">预览正文</button>
          </div>
          <textarea class="adm-input adm-textarea adm-mono" id="postContent" name="content" rows="20" required>{{ old('content', $post->content) }}</textarea>
          @error('content')<span class="adm-field__err">{{ $message }}</span>@enderror
          <div class="adm-preview" id="postPreview" hidden>
            <iframe title="正文预览" sandbox></iframe>
          </div>
        </div>
      </section>
    </div>

    <aside class="adm-form-side">
      <section class="adm-card">
        <header class="adm-card__head">
          <h2>发布</h2>
        </header>

        <label class="adm-check adm-check--box">
          <input type="hidden" name="is_published" value="0">
          <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published))>
          <span>已发布（取消则保存为草稿）</span>
        </label>

        <label class="adm-field">
          <span class="adm-field__label">发布时间 <span class="adm-hint">留空则发布时自动填写</span></span>
          <input class="adm-input adm-mono" type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
          @error('published_at')<span class="adm-field__err">{{ $message }}</span>@enderror
        </label>

        <div class="adm-field">
          <span class="adm-field__label">封面图 <span class="adm-hint">JPG / PNG / WebP，≤ 3MB</span></span>
          @if($post->cover_image)
            <div class="adm-cover">
              <img src="{{ asset($post->cover_image) }}" alt="当前封面">
              <label class="adm-check">
                <input type="checkbox" name="remove_cover" value="1">
                <span>移除当前封面</span>
              </label>
            </div>
          @endif
          <input class="adm-input" type="file" name="cover" accept="image/jpeg,image/png,image/webp">
          @error('cover')<span class="adm-field__err">{{ $message }}</span>@enderror
        </div>

        <button class="adm-btn adm-btn--primary adm-btn--block" type="submit">{{ $post->exists ? '保存修改' : '保存文章' }}</button>
      </section>
    </aside>
  </div>
</form>

@if($post->exists)
  <section class="adm-card adm-danger">
    <header class="adm-card__head">
      <h2>危险操作</h2>
    </header>
    <p class="adm-muted">删除后文章与封面图都会移除，且不可恢复。</p>
    <form method="POST" action="{{ route('admin.posts.destroy', $post->id) }}" data-confirm="删除《{{ $post->title }}》？此操作不可撤销。">
      @csrf
      @method('DELETE')
      <button class="adm-btn adm-btn--danger" type="submit">删除这篇文章</button>
    </form>
  </section>
@endif
@endsection

@push('scripts')
<script>
(function(){
  var title = document.getElementById('postTitle');
  var slug = document.getElementById('postSlug');
  var touched = slug.value.trim() !== '';

  slug.addEventListener('input', function(){ touched = true; });

  title.addEventListener('input', function(){
    if (touched) return;
    var v = title.value.toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
    if (v) slug.value = v;
  });

  var toggle = document.getElementById('previewToggle');
  var box = document.getElementById('postPreview');
  var content = document.getElementById('postContent');
  var frame = box.querySelector('iframe');

  toggle.addEventListener('click', function(){
    var open = box.hidden;
    box.hidden = !open;
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.textContent = open ? '收起预览' : '预览正文';
    if (!open) return;

    var css = 'body{font-family:"Noto Sans SC",sans-serif;line-height:1.9;color:#2b2621;background:#f6f3ec;padding:24px;margin:0}'
      + 'h1,h2,h3{font-family:"Noto Serif SC",serif;line-height:1.4;margin:1.4em 0 .6em}'
      + 'h1{font-size:1.7rem}h2{font-size:1.3rem}h3{font-size:1.1rem}'
      + 'p{margin:0 0 1em}pre{background:#eae4d9;padding:12px;overflow:auto;border-radius:4px}'
      + 'code{font-family:"JetBrains Mono",monospace;font-size:.86em}'
      + 'a{color:#b33a24}blockquote{border-left:3px solid #b33a24;margin:1em 0;padding-left:1em;color:#5c554c}'
      + 'img{max-width:100%;height:auto}table{border-collapse:collapse;width:100%}td,th{border:1px solid #d8d0c2;padding:6px 8px}';
    frame.srcdoc = '<!doctype html><html><head><meta charset="utf-8"><style>' + css + '</style></head><body>' + content.value + '</body></html>';
  });
})();
</script>
@endpush
