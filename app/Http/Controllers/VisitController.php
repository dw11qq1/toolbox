<?php

namespace App\Http\Controllers;

use App\Support\VisitRecorder;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function store(Request $request, VisitRecorder $recorder)
    {
        $data = $request->validate([
            'p' => ['required', 'string', 'max:190'],
        ]);

        $recorder->record($request, $data['p']);

        return response()->noContent();
    }
}
