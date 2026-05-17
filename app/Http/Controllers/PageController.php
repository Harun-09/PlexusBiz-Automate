<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->active()->first();

        if (! $page) {
            $title = Str::of((string) $slug)->replace('-', ' ')->title()->toString();

            $page = new Page([
                'title' => $title,
                'slug' => (string) $slug,
                'content' => sprintf(
                    '<p>%s information is available in this portal. For operational support, please use the Help Center or contact PlexusBiz support.</p><p>This page is currently served as a general information placeholder.</p>',
                    e($title),
                ),
                'meta_title' => $title,
                'meta_description' => $title.' information page',
                'is_active' => true,
            ]);
        }

        return Inertia::render('Frontend/Page', [
            'page' => $page,
            'title' => $page->title,
            'slug' => $page->slug,
        ]);
    }
}
