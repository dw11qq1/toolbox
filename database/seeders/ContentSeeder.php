<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Tool;
use Illuminate\Database\Seeder;

/**
 * ToolBox 内容 Seeder：工具（含计算器与音乐创作）与趣味测试。
 * 使用 updateOrCreate，可重复执行，不会产生重复数据。
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            // 计算
            [
                'name' => '社保计算器', 'slug' => 'social-insurance', 'category' => '计算', 'sort_order' => 1,
                'description' => '输入月工资基数与城市比例，即刻算出五险一金个人与单位月缴明细和到手工资。',
            ],
            [
                'name' => '贷款/月供计算器', 'slug' => 'loan-calculator', 'category' => '计算', 'sort_order' => 2,
                'description' => '等额本息 / 等额本金两种方式对比，月供、总利息、还款总额一目了然。',
            ],
            [
                'name' => '复利/定投计算器', 'slug' => 'compound-calculator', 'category' => '计算', 'sort_order' => 3,
                'description' => '输入本金、月定投、年化与年限，曲线图直观展示资产增长，复利效应一目了然。',
            ],
            [
                'name' => '利率换算器', 'slug' => 'rate-converter', 'category' => '计算', 'sort_order' => 4,
                'description' => '日息 / 月息 / 年息互转，单利复利换算，名义利率转实际年化 APR。',
            ],
            [
                'name' => '夏普率计算器', 'slug' => 'sharpe-calculator', 'category' => '计算', 'sort_order' => 5,
                'description' => '粘贴每月收益率序列，自动算年化收益、年化波动率与夏普比率，评估策略风险调整后收益。',
            ],
            [
                'name' => '索提诺比率计算器', 'slug' => 'sortino-calculator', 'category' => '计算', 'sort_order' => 6,
                'description' => '夏普的改良版：分母换成下行标准差，只惩罚下跌波动，更贴近真实的风险感受。',
            ],
            [
                'name' => '卡玛比率计算器', 'slug' => 'calmar-calculator', 'category' => '计算', 'sort_order' => 7,
                'description' => '年化收益 ÷ 最大回撤，衡量「为了赚钱要忍受多深的浮亏」，检验曲线拿不拿得住。',
            ],
            [
                'name' => '盈亏比计算器', 'slug' => 'profit-loss-ratio', 'category' => '计算', 'sort_order' => 8,
                'description' => '粘贴每笔交易盈亏，算出平均盈利与平均亏损之比，看你是赚大亏小还是赚小亏大。',
            ],
            [
                'name' => '胜率计算器', 'slug' => 'win-rate-calculator', 'category' => '计算', 'sort_order' => 9,
                'description' => '输入总笔数与盈利笔数，附带连错概率参考，看清高胜率背后的真实风险。',
            ],
            [
                'name' => '期望值计算器', 'slug' => 'expected-value-calculator', 'category' => '计算', 'sort_order' => 10,
                'description' => '胜率 × 平均盈利 − 败率 × 平均亏损，每笔交易的数学期望，正负决定系统能不能活。',
            ],
            [
                'name' => '仓位大小计算器', 'slug' => 'position-size-calculator', 'category' => '计算', 'sort_order' => 11,
                'description' => '账户资金、风险比例、入场价、止损价，反推应买数量，把每笔亏损锁死在预算内。',
            ],
            [
                'name' => '风险回报比计算器', 'slug' => 'risk-reward-calculator', 'category' => '计算', 'sort_order' => 12,
                'description' => '入场、止损、止盈三个价格算出 R:R 与打平胜率，下单前最后一道性价比闸门。',
            ],
            // 开发
            [
                'name' => 'JSON 格式化', 'slug' => 'json-formatter', 'category' => '开发', 'sort_order' => 1,
                'description' => '粘贴 JSON 即刻格式化、压缩与校验，报错行号直接标出。全程在浏览器本地完成，数据不上传。',
            ],
            [
                'name' => '时间戳转换', 'slug' => 'timestamp-converter', 'category' => '开发', 'sort_order' => 2,
                'description' => 'Unix 时间戳与日期互转，支持秒/毫秒，附当前时间戳实时刷新。',
            ],
            [
                'name' => '正则测试器', 'slug' => 'regex-tester', 'category' => '开发', 'sort_order' => 3,
                'description' => '正则实时高亮匹配，列出位置与捕获分组，附邮箱/手机号/URL 等常用速查。',
            ],
            [
                'name' => 'Cron 表达式解析', 'slug' => 'cron-parser', 'category' => '开发', 'sort_order' => 4,
                'description' => '把 cron 表达式翻译成人话，预览最近 5 次执行时间，配宝塔定时任务不再抓瞎。',
            ],
            [
                'name' => '时间戳自然语言', 'slug' => 'natural-time', 'category' => '开发', 'sort_order' => 5,
                'description' => '贴时间戳或选日期，翻译成「3 天后」「昨天」这样的人话，附星期、年底倒计时。',
            ],
            // 转换
            [
                'name' => 'Base64 编解码', 'slug' => 'base64-encoder', 'category' => '转换', 'sort_order' => 1,
                'description' => '文本与 Base64 双向转换，支持中文，URL 安全模式可选。',
            ],
            // 音乐
            [
                'name' => '网页音乐盒', 'slug' => 'music-box', 'category' => '音乐', 'sort_order' => 1,
                'description' => '十六步音序器，点亮格子自己作曲，五声音阶随便点都好听，还能一键随机找灵感。',
            ],
            [
                'name' => '节奏工坊', 'slug' => 'beat-studio', 'category' => '音乐', 'sort_order' => 2,
                'description' => '四组打击乐 + 四件旋律乐器的多轨音序器，叠出有层次感的节奏，内置电子和嘻哈预置。',
            ],
            // 创作
            [
                'name' => '拼豆图纸工作室', 'slug' => 'perler-patterns', 'category' => '创作', 'sort_order' => 1,
                'description' => '照片一键转拼豆图纸：MARD/Perler/Artkal 三色卡匹配互转、多图层编辑、自动用豆统计、导出 PNG/PDF。',
            ],
        ];

        foreach ($tools as $tool) {
            Tool::updateOrCreate(['slug' => $tool['slug']], $tool);
        }

        $quizzes = [
            ['name' => 'MBTI 人格测试', 'slug' => 'mbti', 'sort_order' => 1, 'description' => '48 道题测出你的 16 型人格，带各维度分析与职业倾向参考。'],
            ['name' => '恋爱类型测试', 'slug' => 'love-style', 'sort_order' => 2, 'description' => '从依恋风格到相处模式，看看你在感情里是哪种类型。'],
            ['name' => '逻辑思维测试', 'slug' => 'logic-test', 'sort_order' => 3, 'description' => '数列推理、逻辑判断、图形规律，10 题测出你的逻辑段位。'],
            ['name' => '反应速度测试', 'slug' => 'reaction-test', 'sort_order' => 4, 'description' => '背景变色的一瞬间点下去，5 轮平均反应时间见分晓。'],
        ];

        foreach ($quizzes as $quiz) {
            Quiz::updateOrCreate(['slug' => $quiz['slug']], $quiz);
        }
    }
}
