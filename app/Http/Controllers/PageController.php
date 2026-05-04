<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->active()->firstOrFail();

        return Inertia::render('Frontend/Page', [
            'page' => $page,
            'title' => $page->title,
            'slug' => $page->slug,
        ]);
    }
}
