<?php

namespace App\Repositories\PostViews;

use App\Models\post_view;

class PostViewRepository  implements PostViewInterface
{
    public function all(): post_view
    {
        $category = post_view::all()->latest();

        return $category;
    }

    public function find(int $id): post_view
    {
        return post_view::all()->latest();
    }

    public function create(array $data): void {}

    public function update($id, array $data): void {}

    public function delete($id): void {}

    public function recordView($postId, $userId)
    {
        return post_view::create([
            'post_id' => $postId,
            'user_id' => $userId,
            'viewed_at' => now(),
        ]);
    }

    // Get all views for a specific post
    public function getViewsByPost($postId)
    {
        return post_view::where('post_id', $postId)->get();
    }

    // Get all views by a specific user
    public function getViewsByUser($userId)
    {
        return post_view::where('user_id', $userId)->get();
    }

    // Check if a user has viewed a specific post
    public function hasUserViewedPost($postId, $userId)
    {
        return post_view::where('post_id', $postId)
            ->where('user_id', $userId)
            ->exists();
    }
}
