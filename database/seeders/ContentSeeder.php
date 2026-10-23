<?php

namespace Database\Seeders;

use App\Models\Game;
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
        ];

        foreach ($tools as $tool) {
            Tool::updateOrCreate(['slug' => $tool['slug']], $tool);
        }

        foreach ($games as $game) {
            Game::updateOrCreate(['slug' => $game['slug']], $game);
        }
    }
}
