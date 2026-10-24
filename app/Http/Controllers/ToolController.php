<?php

namespace App\Http\Controllers;

use App\Models\Tool;

class ToolController extends Controller
{
    public function index()
    {
        return view('tools.index', [
            'tools' => Tool::active()->get()->groupBy('category'),
        ]);
    }

    public function show(Tool $tool)
    {
        abort_unless($tool->is_active, 404);

        $related = Tool::active()
            ->where('id', '!=', $tool->id)
            ->orderByRaw("category = ? desc", [$tool->category])
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('tools.show', ['tool' => $tool, 'related' => $related]);
    }
}
