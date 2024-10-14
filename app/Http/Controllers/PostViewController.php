<?php

namespace App\Http\Controllers;
use App\Repositories\PostViews\PostViewRepository;

use Illuminate\Http\Request;

class PostViewController extends Controller
{
    protected $postViewRepository;

    public function __construct(PostViewRepository $postViewRepository)
    {
        $this->postViewRepository = $postViewRepository;
    }

    // Record a view
    public function recordView(Request $request, $postId)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer'
        ]);

        $view = $this->postViewRepository->recordView($postId, $validated['user_id']);
        return response()->json($view, 201);
    }

    // Get views by post
    public function getViewsByPost($postId)
    {
        $views = $this->postViewRepository->getViewsByPost($postId);
        return response()->json($views);
    }

    // Get views by user
    public function getViewsByUser($userId)
    {
        $views = $this->postViewRepository->getViewsByUser($userId);
        return response()->json($views);
    }

    // Check if user viewed a post
    public function checkUserViewedPost($postId, $userId)
    {
        $hasViewed = $this->postViewRepository->hasUserViewedPost($postId, $userId);
        return response()->json(['viewed' => $hasViewed]);
    }

}
