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

    public function test_四个板块索引页正常(): void
    {
        $this->seedContent();
        foreach (['/tools', '/games', '/tests', '/nav'] as $url) {
            $this->get($url)->assertStatus(200);
        }
    }

    /* ---------- 工具 ---------- */

    public function test_三个工具页正常(): void
    {
        $this->seedContent();
        foreach (['json-formatter', 'timestamp-converter', 'base64-encoder'] as $slug) {
            $this->get("/tools/{$slug}")->assertStatus(200);
        }
    }

    public function test_不存在的工具返回404(): void
    {
        $this->get('/tools/not-exist')->assertStatus(404);
    }

    /* ---------- 游戏 ---------- */

    public function test_五个游戏页正常(): void
    {
        $this->seedContent();
        foreach (['2048', 'memory-flip', 'music-box', 'beat-studio', 'life-restart', 'soup-riddle'] as $slug) {
            $this->get("/games/{$slug}")->assertStatus(200);
        }
    }

    public function test_不存在的游戏返回404(): void
    {
        $this->get('/games/not-exist')->assertStatus(404);
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

    /* ---------- 导航站 ---------- */

    public function test_导航站渲染出链接(): void
    {
        $this->seedContent();
        $this->get('/nav')->assertStatus(200)->assertSee('GitHub');
    }

    /* ---------- 上下架隔离 ---------- */

    public function test_下架的条目前台不可访问(): void
    {
        $this->seedContent();

        $tool = \App\Models\Tool::where('slug', 'json-formatter')->first();
        $tool->update(['is_active' => false]);
        $this->get('/tools/json-formatter')->assertStatus(404);

        $game = \App\Models\Game::where('slug', '2048')->first();
        $game->update(['is_active' => false]);
        $this->get('/games/2048')->assertStatus(404);

        $quiz = \App\Models\Quiz::where('slug', 'mbti')->first();
        $quiz->update(['is_active' => false]);
        $this->get('/tests/mbti')->assertStatus(404);

        $link = \App\Models\Link::where('url', 'https://github.com')->first();
        $link->update(['is_active' => false]);
        $this->get('/nav')->assertOk()->assertDontSee('href="https://github.com"', false);
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
        $this->assertFileExists(public_path('js/games/2048.js'));
    }
}
