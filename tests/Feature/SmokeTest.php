<?php

namespace Tests\Feature;

use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function seedContent(): void
    {
        $this->seed(ContentSeeder::class);
    }

    /* ---------- 首页与板块索引 ---------- */

    public function test_首页正常打开(): void
    {
        $this->seedContent();
        $this->get('/')->assertStatus(200)->assertSee('工具匣');
    }

    public function test_三个板块索引页正常(): void
    {
        $this->seedContent();
        foreach (['/tools', '/tests', '/blog'] as $url) {
            $this->get($url)->assertStatus(200);
        }
    }

    /* ---------- 工具（18 个） ---------- */

    public function test_全部工具页正常(): void
    {
        $this->seedContent();
        foreach ([
            'json-formatter', 'timestamp-converter', 'base64-encoder',
            'social-insurance', 'loan-calculator', 'compound-calculator',
            'rate-converter', 'sharpe-calculator',
            'sortino-calculator', 'calmar-calculator', 'profit-loss-ratio',
            'win-rate-calculator', 'expected-value-calculator',
            'position-size-calculator', 'risk-reward-calculator',
            'music-box', 'beat-studio', 'perler-patterns',
        ] as $slug) {
            $this->get("/tools/{$slug}")->assertStatus(200);
        }
    }

    public function test_不存在的工具返回404(): void
    {
        $this->get('/tools/not-exist')->assertStatus(404);
    }

    public function test_旧路由已下线(): void
    {
        foreach (['/games', '/games/2048', '/nav', '/games/survival-tales'] as $url) {
            $this->get($url)->assertStatus(404);
        }
    }

    /* ---------- 测试板块 ---------- */

    public function test_四个测试页正常(): void
    {
        $this->seedContent();
        foreach (['mbti', 'love-style', 'logic-test', 'reaction-test'] as $slug) {
            $this->get("/tests/{$slug}")->assertStatus(200);
        }
    }

    public function test_不存在的测试返回404(): void
    {
        $this->get('/tests/not-exist')->assertStatus(404);
    }

    /* ---------- 博客 ---------- */

    public function test_博客空列表正常(): void
    {
        $this->seedContent();
        $this->get('/blog')->assertStatus(200);
    }

    public function test_已发布文章可访问且草稿404(): void
    {
        $this->seedContent();

        $post = \App\Models\Post::create([
            'title' => '测试文章',
            'slug' => 'hello-world',
            'content' => '<p>你好</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);
        $this->get('/blog/hello-world')->assertStatus(200)->assertSee('测试文章');

        $draft = \App\Models\Post::create([
            'title' => '草稿文章',
            'slug' => 'draft-one',
            'content' => '<p>草稿</p>',
            'is_published' => false,
        ]);
        $this->get('/blog/draft-one')->assertStatus(404);
    }

    /* ---------- 上下架隔离 ---------- */

    public function test_下架的条目前台不可访问(): void
    {
        $this->seedContent();

        $tool = \App\Models\Tool::where('slug', 'json-formatter')->first();
        $tool->update(['is_active' => false]);
        $this->get('/tools/json-formatter')->assertStatus(404);

        $quiz = \App\Models\Quiz::where('slug', 'mbti')->first();
        $quiz->update(['is_active' => false]);
        $this->get('/tests/mbti')->assertStatus(404);
    }

    /* ---------- 后台 ---------- */

    public function test_后台登录页正常(): void
    {
        $this->get('/admin/login')->assertStatus(200);
    }

    public function test_未登录访问后台跳转登录(): void
    {
        $this->get('/admin')->assertRedirect();
    }

    public function test_静态资源文件存在(): void
    {
        $this->assertFileExists(public_path('css/app.css'));
        $this->assertFileExists(public_path('js/filament/filament/app.js'));
    }
}
