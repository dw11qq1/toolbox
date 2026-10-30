<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

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
