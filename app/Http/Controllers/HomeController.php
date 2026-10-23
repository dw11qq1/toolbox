<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tool;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'tools' => Tool::active()->get(),
            'games' => Game::active()->get(),
        ]);
    }
}
