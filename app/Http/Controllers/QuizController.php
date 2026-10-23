<?php

namespace App\Http\Controllers;

use App\Models\Quiz;

class QuizController extends Controller
{
    public function index()
    {
        return view('quizzes.index', [
            'quizzes' => Quiz::active()->get(),
        ]);
    }

    public function show(Quiz $quiz)
    {
        abort_unless($quiz->is_active, 404);

        return view('quizzes.show', ['quiz' => $quiz]);
    }
}
