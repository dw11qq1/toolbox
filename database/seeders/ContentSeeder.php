<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Link;
use App\Models\Quiz;
use App\Models\Tool;
use Illuminate\Database\Seeder;

/**
 * ToolBox 内容 Seeder：工具 + 游戏。
 * 使用 updateOrCreate，可重复执行，不会产生重复数据。
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            [
                'name' => 'JSON 格式化',
                'slug' => 'json-formatter',
                'category' => '开发',
                'sort_order' => 1,
                'description' => '粘贴 JSON 即刻格式化、压缩与校验，报错行号直接标出。全程在浏览器本地完成，数据不上传。',
            ],
            [
                'name' => '时间戳转换',
                'slug' => 'timestamp-converter',
                'category' => '开发',
                'sort_order' => 2,
                'description' => 'Unix 时间戳与日期互转，支持秒/毫秒，附当前时间戳实时刷新。',
            ],
            [
                'name' => 'Base64 编解码',
                'slug' => 'base64-encoder',
                'category' => '转换',
                'sort_order' => 3,
                'description' => '文本与 Base64 双向转换，支持中文，URL 安全模式可选。',
            ],
        ];

        $games = [
            [
                'name' => '2048',
                'slug' => '2048',
                'sort_order' => 1,
                'description' => '经典数字合并游戏，方向键或滑动操作，本地记录最高分。',
                'how_to_play' => '方向键或滑动屏幕移动方块，相同数字碰撞合并，凑出 2048 即胜利。',
            ],
            [
                'name' => '记忆翻牌',
                'slug' => 'memory-flip',
                'sort_order' => 2,
                'description' => '8 对图案翻牌配对，计时计步，挑战最短时间。',
                'how_to_play' => '点击卡片翻开，每次翻两张，配对成功保持翻开，全部配对即通关。',
            ],
            [
                'name' => '网页音乐盒',
                'slug' => 'music-box',
                'sort_order' => 3,
                'description' => '十六步音序器，点亮格子自己作曲，五声音阶随便点都好听，还能一键随机找灵感。',
                'how_to_play' => '点击格子点亮音符，按播放循环你的旋律；「随机灵感」一键生成一段，速度滑块可调 BPM。',
            ],
        ];

        $quizzes = [
            [
                'name' => 'MBTI 16 型人格测试',
                'slug' => 'mbti',
                'sort_order' => 1,
                'description' => '48 题精简版，从精力来源、信息获取、决策方式、生活态度四个维度测出你的 16 型人格。',
            ],
            [
                'name' => '恋爱类型测试',
                'slug' => 'love-style',
                'sort_order' => 2,
                'description' => '12 题测出你在亲密关系中的依恋风格：安全型、专注型、回避型还是恐惧型。',
            ],
            [
                'name' => '逻辑思维测试',
                'slug' => 'logic-test',
                'sort_order' => 3,
                'description' => '10 道数列与推理题，测测你的逻辑脑，附完整解析。',
            ],
            [
                'name' => '反应速度测试',
                'slug' => 'reaction-test',
                'sort_order' => 4,
                'description' => '红变绿的瞬间点下去，5 轮平均测出你的反应毫秒数。',
            ],
        ];

        foreach ($tools as $tool) {
            Tool::updateOrCreate(['slug' => $tool['slug']], $tool);
        }

        foreach ($games as $game) {
            Game::updateOrCreate(['slug' => $game['slug']], $game);
        }

        foreach ($quizzes as $quiz) {
            Quiz::updateOrCreate(['slug' => $quiz['slug']], $quiz);
        }

        $links = [
            // 常用
            ['name' => 'GitHub', 'url' => 'https://github.com', 'category' => '常用', 'sort_order' => 1, 'description' => '全球最大代码托管平台'],
            ['name' => '哔哩哔哩', 'url' => 'https://www.bilibili.com', 'category' => '常用', 'sort_order' => 2, 'description' => '技术教程视频最多的中文社区'],
            ['name' => '知乎', 'url' => 'https://www.zhihu.com', 'category' => '常用', 'sort_order' => 3, 'description' => '中文问答社区'],
            // 开发
            ['name' => 'Laravel 文档', 'url' => 'https://laravel.com/docs', 'category' => '开发', 'sort_order' => 1, 'description' => 'Laravel 官方文档，写得像教程一样好读'],
            ['name' => 'MDN Web Docs', 'url' => 'https://developer.mozilla.org', 'category' => '开发', 'sort_order' => 2, 'description' => 'Web 技术权威参考手册'],
            ['name' => 'PHP 官方文档', 'url' => 'https://www.php.net/manual/zh/', 'category' => '开发', 'sort_order' => 3, 'description' => 'PHP 中文手册'],
            ['name' => 'Can I Use', 'url' => 'https://caniuse.com', 'category' => '开发', 'sort_order' => 4, 'description' => '浏览器兼容性查询'],
            ['name' => 'Composer Packagist', 'url' => 'https://packagist.org', 'category' => '开发', 'sort_order' => 5, 'description' => 'PHP 包仓库'],
            // 学习
            ['name' => '菜鸟教程', 'url' => 'https://www.runoob.com', 'category' => '学习', 'sort_order' => 1, 'description' => '入门友好的中文教程站'],
            ['name' => 'LeetCode 力扣', 'url' => 'https://leetcode.cn', 'category' => '学习', 'sort_order' => 2, 'description' => '算法刷题平台'],
            ['name' => 'Stack Overflow', 'url' => 'https://stackoverflow.com', 'category' => '学习', 'sort_order' => 3, 'description' => '程序员问答社区'],
        ];

        foreach ($links as $link) {
            Link::updateOrCreate(['url' => $link['url']], $link);
        }
    }
}
