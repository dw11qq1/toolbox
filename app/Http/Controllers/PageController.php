<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Quiz;
use App\Models\Tool;

class PageController extends Controller
{
    /**
     * 隐私政策（AdSense 等第三方广告服务要求的必备页面）。
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * 站点地图：首页 + 工具 + 测试 + 博客，供搜索引擎收录。
     */
    public function sitemap()
    {
        $tools = Tool::active()->orderBy('sort_order')->get();
        $quizzes = Quiz::active()->orderBy('sort_order')->get();
        $posts = Post::published()->get();

        return response()
            ->view('sitemap.index', compact('tools', 'quizzes', 'posts'))
            ->header('Content-Type', 'application/xml');
    }
}
