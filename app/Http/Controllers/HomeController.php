<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Quiz;
use App\Models\Tool;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'tools' => Tool::active()->get(),
            'quizzes' => Quiz::active()->get(),
            'posts' => Post::published()->limit(3)->get(),
            'postCount' => Post::published()->count(),
        ]);
    }
}
