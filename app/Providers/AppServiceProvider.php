<?php

namespace App\Providers;

use App\Models\Quiz;
use App\Models\Tool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 全站搜索索引：工具 + 测试，缓存 1 小时，供顶部搜索框与 404 页使用
        // 用 composer 延迟到视图渲染时才查询，避免应用启动阶段（测试建表前）访问数据库
        View::composer('layouts.app', function ($view) {
            $view->with('searchIndex', Cache::remember('toolbox.search.index', 3600, function () {
            $items = collect();

            Tool::active()->get()->each(function ($tool) use ($items) {
                $items->push([
                    'n' => $tool->name,
                    'd' => $tool->description,
                    'c' => $tool->category,
                    'u' => url('/tools/' . $tool->slug),
                ]);
            });

            Quiz::active()->get()->each(function ($quiz) use ($items) {
                $items->push([
                    'n' => $quiz->name,
                    'd' => $quiz->description,
                    'c' => '测试',
                    'u' => url('/tests/' . $quiz->slug),
                ]);
            });

            return $items->all();
            }));
        });
    }
}
