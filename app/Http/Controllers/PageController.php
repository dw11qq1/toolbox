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

    /**
     * 关于页。
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * 联系页（无表单后端，纯信息展示，遵循「数据不出浏览器」原则）。
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * 常见问题页（含 FAQPage 结构化数据）。
     */
    public function faq()
    {
        return view('pages.faq');
    }

    /**
     * llms.txt：给 AI 爬虫的站点说明书（llmstxt.org 规范）。
     */
    public function llms()
    {
        $base = rtrim(config('app.url'), '/');
        $posts = Post::published()->take(10)->get();

        $lines = [];
        $lines[] = '# 工具匣 ToolBox';
        $lines[] = '';
        $lines[] = '> 一个纯前端、数据不出浏览器的在线工具站。提供财务/风控计算器、开发工具、格式互转、拼豆图纸生成器、乐器舞台与趣味测试，全部在浏览器本地运行，不上传任何数据。';
        $lines[] = '';
        $lines[] = '## 核心工具';
        $lines[] = '- [全部工具](' . $base . '/tools): 财务与风控计算器、开发工具、格式互转等';
        $lines[] = '- [拼豆图纸生成器](' . $base . '/tools/perler-patterns): 照片转拼豆图纸、色卡匹配、多图层、PNG/PDF 导出';
        $lines[] = '- [乐器舞台](' . $base . '/tools/beat-studio): 拖拽乐器各自异步演奏、本地保存与导出';
        $lines[] = '- [趣味测试](' . $base . '/tests): MBTI 等本地计分测试';
        $lines[] = '';
        $lines[] = '## 博客';
        $lines[] = '- [博客首页](' . $base . '/blog): 计算器公式、风控指标、开发技巧与拼豆科普';
        foreach ($posts as $p) {
            $lines[] = '- [' . $p->title . '](' . $base . '/blog/' . $p->slug . ')';
        }
        $lines[] = '';
        $lines[] = '## 关于与联系';
        $lines[] = '- [关于](' . $base . '/about)';
        $lines[] = '- [联系](' . $base . '/contact)';
        $lines[] = '- [常见问题](' . $base . '/faq)';
        $lines[] = '- [隐私政策](' . $base . '/privacy)';
        $lines[] = '';

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
