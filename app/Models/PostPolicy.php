<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    // Check if the user is the author (for update, delete, publish, etc.)
    public function update(User $user, Post $post)
    {
        return $user->id === $post->author_id;
    }

    public function delete(User $user, Post $post)
    {
        return $user->id === $post->author_id;
    }

    public function publish(User $user, Post $post)
    {
        return $user->id === $post->author_id;
    }

    public function restore(User $user, Post $post)
    {
        return $user->id === $post->author_id;
    }

    public function unpublish(User $user, Post $post)
    {
        return $user->id === $post->author_id;
    }
    
    // Optionally, you can add a view policy if you want to restrict view access
    public function view(User $user, Post $post)
    {
        return $user->id === $post->author_id || $post->is_published;
    }
}
