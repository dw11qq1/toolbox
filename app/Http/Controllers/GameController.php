<?php

namespace App\Http\Controllers;

use App\Models\Game;

class GameController extends Controller
{
    public function index()
    {
        return view('games.index', [
            'games' => Game::active()->get(),
        ]);
    }

    public function show(Game $game)
    {
        abort_unless($game->is_active, 404);

        return view('games.show', ['game' => $game]);
    }
}
