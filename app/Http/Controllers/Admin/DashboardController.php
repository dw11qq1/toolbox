<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Quiz;
use App\Models\Tool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $available = Schema::hasTable('page_views') && Schema::hasTable('visitors');
        $today = now()->startOfDay();

        $window = function ($from, $to) use ($available) {
            if (! $available) {
                return ['pv' => 0, 'uv' => 0];
            }

            $range = [$from->toDateString(), $to->toDateString()];

            return [
                'pv' => (int) DB::table('page_views')->whereBetween('viewed_on', $range)->sum('hits'),
                'uv' => (int) DB::table('visitors')->whereBetween('viewed_on', $range)->distinct()->count('visitor_hash'),
            ];
        };

        $todayStats = $window($today, $today);
        $yesterdayStats = $window($today->copy()->subDay(), $today->copy()->subDay());
        $weekStats = $window($today->copy()->subDays(6), $today);
        $monthStats = $window($today->copy()->subDays(29), $today);
        $totalPv = $available ? (int) DB::table('page_views')->sum('hits') : 0;

        $series = collect(range(13, 0))->map(function (int $ago) {
            $date = now()->subDays($ago);

            return ['date' => $date->toDateString(), 'label' => $date->format('m-d'), 'pv' => 0];
        });

        if ($available) {
            $rows = DB::table('page_views')
                ->selectRaw('viewed_on, sum(hits) as pv')
                ->where('viewed_on', '>=', now()->subDays(13)->toDateString())
                ->groupBy('viewed_on')
                ->pluck('pv', 'viewed_on');

            $series = $series->map(fn (array $day) => [
                ...$day,
                'pv' => (int) ($rows[$day['date']] ?? 0),
            ]);
        }

        $topPages = collect();

        if ($available) {
            $topPages = DB::table('page_views')
                ->selectRaw('path, sum(hits) as pv')
                ->where('viewed_on', '>=', $today->copy()->subDays(6)->toDateString())
                ->groupBy('path')
                ->orderByDesc('pv')
                ->limit(10)
                ->get();
        }

        $delta = fn (int $current, int $previous) => $previous > 0
            ? (int) round(($current - $previous) / $previous * 100)
            : null;

        return view('admin.dashboard', [
            'statsAvailable' => $available,
            'todayStats' => $todayStats,
            'yesterdayStats' => $yesterdayStats,
            'weekStats' => $weekStats,
            'monthStats' => $monthStats,
            'totalPv' => $totalPv,
            'todayPvDelta' => $delta($todayStats['pv'], $yesterdayStats['pv']),
            'todayUvDelta' => $delta($todayStats['uv'], $yesterdayStats['uv']),
            'series' => $series,
            'maxPv' => max(1, (int) $series->max('pv')),
            'topPages' => $topPages,
            'pageLabels' => $this->labels($topPages->pluck('path')),
            'counts' => [
                'posts' => Post::count(),
                'posts_published' => Post::where('is_published', true)->count(),
                'tools' => Tool::count(),
                'tools_active' => Tool::where('is_active', true)->count(),
                'quizzes' => Quiz::count(),
                'quizzes_active' => Quiz::where('is_active', true)->count(),
            ],
            'recentPosts' => Post::orderByDesc('updated_at')->limit(6)->get(),
        ]);
    }

    private function labels(Collection $paths): array
    {
        $labels = [
            '/' => '首页',
            '/tools' => '工具索引',
            '/tests' => '测试索引',
            '/blog' => '博客索引',
            '/privacy' => '隐私政策',
            '/about' => '关于',
            '/contact' => '联系',
            '/faq' => '常见问题',
        ];

        $tools = Tool::pluck('name', 'slug');
        $quizzes = Quiz::pluck('name', 'slug');
        $posts = Post::pluck('title', 'slug');

        foreach ($paths as $path) {
            if (isset($labels[$path])) {
                continue;
            }

            if (preg_match('#^/tools/([^/]+)$#', $path, $m) && isset($tools[$m[1]])) {
                $labels[$path] = $tools[$m[1]];
            } elseif (preg_match('#^/tests/([^/]+)$#', $path, $m) && isset($quizzes[$m[1]])) {
                $labels[$path] = $quizzes[$m[1]];
            } elseif (preg_match('#^/blog/([^/]+)$#', $path, $m) && isset($posts[$m[1]])) {
                $labels[$path] = $posts[$m[1]];
            } else {
                $labels[$path] = $path;
            }
        }

        return $labels;
    }
}
