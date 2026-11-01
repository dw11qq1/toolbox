<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    private function admin(): User
    {
        return User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-pass'),
        ]);
    }

    public function test_未登录访问后台会跳转登录页(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_登录页可以打开(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('进入管理台');
    }

    public function test_正确凭据可以登录(): void
    {
        $user = $this->admin();

        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'secret-pass',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_错误凭据会被拒绝(): void
    {
        $this->admin();

        $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong',
        ])->assertRedirect('/admin/login');

        $this->assertGuest();
    }

    public function test_登录后后台各页正常渲染(): void
    {
        Post::create([
            'title' => '测试文章', 'slug' => 'test-post', 'content' => '<p>正文</p>',
            'is_published' => true, 'published_at' => now(),
        ]);
        Tool::create(['name' => '测试工具', 'slug' => 'demo-tool', 'category' => '开发', 'sort_order' => 1, 'is_active' => true]);

        $user = $this->admin();

        foreach (['/admin', '/admin/posts', '/admin/posts/create', '/admin/tools', '/admin/quizzes'] as $url) {
            $this->actingAs($user)->get($url)->assertOk();
        }
    }

    public function test_可以新建和更新文章(): void
    {
        $user = $this->admin();

        $this->actingAs($user)->post('/admin/posts', [
            'title' => 'Hello World',
            'slug' => 'hello-world',
            'excerpt' => '摘要',
            'content' => '<p>内容</p>',
            'is_published' => '1',
        ])->assertRedirect('/admin/posts');

        $post = Post::where('slug', 'hello-world')->firstOrFail();
        $this->assertTrue($post->is_published);
        $this->assertNotNull($post->published_at);

        $this->actingAs($user)->put('/admin/posts/'.$post->id, [
            'title' => 'Hello Again',
            'slug' => 'hello-again',
            'content' => '<p>更新</p>',
            'is_published' => '0',
        ])->assertRedirect('/admin/posts/'.$post->id.'/edit');

        $post->refresh();
        $this->assertSame('hello-again', $post->slug);
        $this->assertFalse($post->is_published);
    }

    public function test_删除文章(): void
    {
        $user = $this->admin();
        $post = Post::create([
            'title' => '待删除', 'slug' => 'to-delete', 'content' => '<p>x</p>',
            'is_published' => false,
        ]);

        $this->actingAs($user)->delete('/admin/posts/'.$post->id)->assertRedirect('/admin/posts');
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_工具排序与上下线可以保存(): void
    {
        $user = $this->admin();
        $tool = Tool::create([
            'name' => '排序工具', 'slug' => 'sort-tool', 'category' => '开发',
            'sort_order' => 1, 'is_active' => true,
        ]);

        $this->actingAs($user)->put('/admin/tools', [
            'items' => [$tool->id => ['sort_order' => 9, 'is_active' => '0']],
        ])->assertRedirect('/admin/tools');

        $tool->refresh();
        $this->assertSame(9, $tool->sort_order);
        $this->assertFalse($tool->is_active);
    }

    public function test_访问统计按访客与路径累计(): void
    {
        $headers = ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/131.0'];

        $this->withHeaders($headers)->postJson('/api/visit', ['p' => '/tools/json-formatter'])->assertNoContent();
        $this->withHeaders($headers)->postJson('/api/visit', ['p' => '/tools/json-formatter'])->assertNoContent();

        $this->assertDatabaseHas('page_views', [
            'path' => '/tools/json-formatter',
            'hits' => 2,
        ]);
        $this->assertDatabaseCount('visitors', 1);
    }

    public function test_爬虫和后台路径不计入统计(): void
    {
        $this->withHeaders(['User-Agent' => 'Googlebot/2.1'])->postJson('/api/visit', ['p' => '/blog'])->assertNoContent();
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 Chrome/131.0'])->postJson('/api/visit', ['p' => '/admin'])->assertNoContent();

        $this->assertDatabaseCount('page_views', 0);
        $this->assertDatabaseCount('visitors', 0);
    }
}
