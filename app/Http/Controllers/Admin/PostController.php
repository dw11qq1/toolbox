<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index()
    {
        return view('admin.posts.index', [
            'posts' => Post::orderByDesc('updated_at')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.posts.form', ['post' => new Post]);
    }

    public function store(Request $request)
    {
        $post = new Post;
        $post->fill($this->payload($request));
        $post->save();

        return redirect()
            ->route('admin.posts.index')
            ->with('status', $post->is_published ? '文章已发布。' : '草稿已保存。');
    }

    public function edit(string $post)
    {
        return view('admin.posts.form', [
            'post' => Post::findOrFail($post),
        ]);
    }

    public function update(Request $request, string $post)
    {
        $model = Post::findOrFail($post);
        $model->fill($this->payload($request, $model));
        $model->save();

        return redirect()
            ->route('admin.posts.edit', $model->id)
            ->with('status', '文章已保存。');
    }

    public function destroy(string $post)
    {
        $model = Post::findOrFail($post);
        $this->deleteCover($model->cover_image);
        $model->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('status', '文章已删除。');
    }

    private function payload(Request $request, ?Post $post = null): array
    {
        if (trim((string) $request->input('slug')) === '') {
            $request->merge(['slug' => Str::slug((string) $request->input('title'))]);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => [
                'required', 'string', 'max:120',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('posts', 'slug')->ignore($post?->id),
            ],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'slug.required' => '中文标题请手动填写英文 slug（小写字母、数字、连字符）。',
            'slug.regex' => 'slug 只能使用小写字母、数字和连字符。',
            'slug.unique' => '这个 slug 已被其它文章占用。',
            'content.required' => '正文不能为空。',
        ]);

        $published = $request->boolean('is_published');
        $publishedAt = filled($data['published_at'] ?? null)
            ? Carbon::parse($data['published_at'])
            : $post?->published_at;

        if ($published && ! $publishedAt) {
            $publishedAt = now();
        }

        $cover = $post?->cover_image;

        if ($request->boolean('remove_cover')) {
            $this->deleteCover($cover);
            $cover = null;
        }

        if ($request->hasFile('cover')) {
            $this->deleteCover($cover);
            $cover = $this->moveCover($request);
        }

        return [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'cover_image' => $cover,
            'is_published' => $published,
            'published_at' => $publishedAt,
        ];
    }

    private function moveCover(Request $request): string
    {
        $file = $request->file('cover');
        $dir = public_path('uploads/covers');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $base = Str::slug((string) $request->input('slug')) ?: 'cover';
        $name = $base.'-'.Str::lower(Str::random(8)).'.'.strtolower($file->getClientOriginalExtension());
        $file->move($dir, $name);

        return 'uploads/covers/'.$name;
    }

    private function deleteCover(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'uploads/covers/')) {
            return;
        }

        $full = public_path($path);

        if (is_file($full)) {
            @unlink($full);
        }
    }
}
