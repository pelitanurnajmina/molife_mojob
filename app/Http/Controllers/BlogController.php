<?php

namespace App\Http\Controllers;

use App\Support\BlogPosts;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPosts::all();
        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = BlogPosts::find($slug);
        abort_if(!$post, 404);
        $related = BlogPosts::related($slug);
        return view('blog.show', compact('post', 'related'));
    }

    /** XML sitemap of public pages + blog posts (for search engines). */
    public function sitemap()
    {
        $urls = [
            ['loc' => route('landing'),    'lastmod' => date('Y-m-d'), 'priority' => '1.0'],
            ['loc' => route('blog.index'), 'lastmod' => date('Y-m-d'), 'priority' => '0.8'],
        ];
        foreach (BlogPosts::all() as $slug => $p) {
            $urls[] = [
                'loc'      => route('blog.show', $slug),
                'lastmod'  => $p['updated'] ?? $p['date'],
                'priority' => '0.7',
            ];
        }

        return response()
            ->view('blog.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}
