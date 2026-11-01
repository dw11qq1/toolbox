<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\VisitController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tools', [ToolController::class, 'index'])->name('tools.index');
Route::get('/tools/{tool}', [ToolController::class, 'show'])->name('tools.show');
Route::get('/tests', [QuizController::class, 'index'])->name('quizzes.index');
Route::get('/tests/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/llms.txt', [PageController::class, 'llms'])->name('llms');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');

Route::post('/api/visit', [VisitController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('visit');

// 公开站默认不启用 session / CSRF，后台单独挂回；始终禁用缓存。
Route::prefix('admin')->name('admin.')->middleware([
    StartSession::class,
    ShareErrorsFromSession::class,
    PreventRequestForgery::class,
])->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('posts', [PostController::class, 'store'])->name('posts.store');
        Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        Route::get('tools', [CatalogController::class, 'tools'])->name('tools.index');
        Route::put('tools', [CatalogController::class, 'updateTools'])->name('tools.update');
        Route::get('quizzes', [CatalogController::class, 'quizzes'])->name('quizzes.index');
        Route::put('quizzes', [CatalogController::class, 'updateQuizzes'])->name('quizzes.update');
    });
});
