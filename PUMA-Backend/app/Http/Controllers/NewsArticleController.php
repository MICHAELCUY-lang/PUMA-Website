<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Http\Requests\StoreNewsArticleRequest;
use App\Http\Requests\UpdateNewsArticleRequest;
use Illuminate\Http\Request;

class NewsArticleController extends Controller
{
    /**
     * Display a listing of published news articles.
     */
    public function index(Request $request)
    {
        $query = NewsArticle::query();

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by featured status if provided
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Only show published articles unless 'all' is requested
        if (!$request->boolean('all')) {
            $query->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        }

        // Order by published date descending (most recent first)
        $articles = $query->orderBy('published_at', 'desc')->get();

        // Transform the data
        $transformedArticles = $articles->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'description' => $article->description,
                'content' => $article->content,
                'category' => $article->category,
                'author' => $article->author,
                'featured_image' => $article->featured_image,
                'is_featured' => $article->is_featured,
                'views' => $article->views,
                'date' => \Carbon\Carbon::parse($article->published_at)->format('F j, Y'),
                'published_at' => $article->published_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedArticles
        ]);
    }

    /**
     * Get featured news articles only.
     */
    public function featured()
    {
        $articles = NewsArticle::where('is_featured', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->get();

        $transformedArticles = $articles->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'description' => $article->description,
                'content' => $article->content,
                'category' => $article->category,
                'author' => $article->author,
                'featured_image' => $article->featured_image,
                'is_featured' => $article->is_featured,
                'views' => $article->views,
                'date' => \Carbon\Carbon::parse($article->published_at)->format('F j, Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedArticles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsArticleRequest $request)
    {
        $article = NewsArticle::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'News article created successfully',
            'data' => $article
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(NewsArticle $newsArticle)
    {
        // Increment views
        $newsArticle->increment('views');

        $transformedArticle = [
            'id' => $newsArticle->id,
            'title' => $newsArticle->title,
            'description' => $newsArticle->description,
            'content' => $newsArticle->content,
            'category' => $newsArticle->category,
            'author' => $newsArticle->author,
            'featured_image' => $newsArticle->featured_image,
            'is_featured' => $newsArticle->is_featured,
            'views' => $newsArticle->views,
            'date' => \Carbon\Carbon::parse($newsArticle->published_at)->format('F j, Y'),
            'published_at' => $newsArticle->published_at,
        ];

        return response()->json([
            'success' => true,
            'data' => $transformedArticle
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NewsArticle $newsArticle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsArticleRequest $request, NewsArticle $newsArticle)
    {
        $newsArticle->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'News article updated successfully',
            'data' => $newsArticle
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsArticle $newsArticle)
    {
        $newsArticle->delete();

        return response()->json([
            'success' => true,
            'message' => 'News article deleted successfully'
        ]);
    }
}
