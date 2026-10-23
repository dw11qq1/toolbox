<?php

namespace App\Http\Controllers;

use App\Models\Link;

class NavController extends Controller
{
    public function index()
    {
        return view('nav.index', [
            'links' => Link::active()->get()->groupBy('category'),
        ]);
    }
}
