@extends('admin.layout')

@php
  $isTools = $kind === 'tools';
  $label = $isTools ? '工具' : '测试';
@endphp

@section('title', $label.'排序 — 工具匣 管理台')
@section('kicker', $isTools ? 'Tools' : 'Quizzes')
@section('heading', $label.'排序与上下线')

@section('content')
<form method="POST" action="{{ $isTools ? route('admin.tools.update') : route('admin.quizzes.update') }}">
  @csrf
  @method('PUT')

  <section class="adm-card">
    <header class="adm-card__head">
      <div>
        <h2>{{ '全部'.$label }}</h2>
        <p class="adm-card__note">{{ $label }}正文由页面模板维护，这里只调整排序与是否上线。数字越小越靠前。</p>
      </div>
      <button class="adm-btn adm-btn--primary" type="submit">保存全部</button>
    </header>

    @if($items->isEmpty())
      <p class="adm-empty">还没有{{ $label }}记录。</p>
    @else
      <div class="adm-table-wrap">
        <table class="adm-table adm-table--catalog">
          <thead>
            <tr>
              <th scope="col">名称</th>
              <th scope="col">slug</th>
              @if($isTools)
                <th scope="col">分类</th>
              @endif
              <th scope="col" class="is-num">排序</th>
              <th scope="col">上线</th>
              <th scope="col">更新时间</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $item)
              <tr>
                <td class="adm-cell-name">{{ $item->name }}</td>
                <td class="adm-cell-slug adm-mono adm-muted">{{ $item->slug }}</td>
                @if($isTools)
                  <td class="adm-cell-cat">{{ $item->category }}</td>
                @endif
                <td class="adm-cell-sort is-num" data-label="排序">
                  <input class="adm-input adm-input--num" type="number"
                         name="items[{{ $item->id }}][sort_order]"
                         value="{{ old('items.'.$item->id.'.sort_order', $item->sort_order) }}"
                         min="0" max="9999" step="1" required
                         aria-label="{{ $item->name }} 的排序">
                </td>
                <td class="adm-cell-active" data-label="上线">
                  <input type="hidden" name="items[{{ $item->id }}][is_active]" value="0">
                  <input type="checkbox" name="items[{{ $item->id }}][is_active]" value="1"
                         @checked(old('items.'.$item->id.'.is_active', $item->is_active))
                         aria-label="{{ $item->name }} 是否上线">
                </td>
                <td class="adm-cell-date adm-mono adm-muted">{{ $item->updated_at?->format('Y-m-d') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>
</form>
@endsection
