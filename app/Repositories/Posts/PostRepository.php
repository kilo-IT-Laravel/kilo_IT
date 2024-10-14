<?php

namespace App\Repositories\Posts;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Exception;

class PostRepository implements PostInterface

{

    // Retrieve all posts with pagination, filtering, and sorting
    public function all($filters = [], $pagination = 10, $sortBy = 'created_at', $sortOrder = 'desc')
    {
        $query = Post::query();

        // Eager load relationships (e.g., category, author, media)
        $query->with(['category', 'author', 'uploadMedia']);

        // Apply filters (e.g., category, author)
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        // Search by title or description
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Handle soft-deleted posts 
        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        // Paginate the results
        return $query->paginate($pagination);
    }

    // Find a specific post by ID with relationships
    public function find(int $id)
    {
        try {
            return Post::with(['category', 'author', 'uploadMedia'])->findOrFail($id);
        } catch (Exception $e) {
            Log::error("Post not found: {$e->getMessage()}");
            throw new Exception("Post with ID {$id} not found");
        }
    }

    // Create a new post with media upload handling
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            // Create the post
            $post = Post::create($data);

            // Handle media upload if provided
            if (isset($data['media'])) {
                $this->handleMediaUpload($post, $data['media']);
            }

            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Post creation failed: {$e->getMessage()}");
            throw new Exception("Error occurred while creating the post.");
        }
    }

    // Update an existing post and handle media updates
    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $post = $this->find($id);
            $post->update($data);

            // Handle media upload if provided
            if (isset($data['media'])) {
                $this->handleMediaUpload($post, $data['media']);
            }

            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Post update failed: {$e->getMessage()}");
            throw new Exception("Error occurred while updating the post.");
        }
    }

    // Delete a post (soft delete by default)
    public function delete($id)
    {
        try {
            $post = $this->find($id);
            $post->delete();
            return true;
        } catch (Exception $e) {
            Log::error("Post deletion failed: {$id}, Error: {$e->getMessage()}");
            throw new Exception("Error occurred while deleting the post.");
        }
    }

    // Permanently delete a post
    public function forceDelete($id)
    {
        try {
            $post = $this->find($id);
            $post->forceDelete();  // Permanently deletes the post from the database
            return true;
        } catch (Exception $e) {
            Log::error("Post force deletion failed: {$id}, Error: {$e->getMessage()}");
            throw new Exception("Error occurred while force deleting the post.");
        }
    }

    // Restore a soft deleted post
    public function restore($id)
    {
        try {
            $post = Post::withTrashed()->findOrFail($id);
            $post->restore();
            return true;
        } catch (Exception $e) {
            Log::error("Post restore failed: {$id}, Error: {$e->getMessage()}");
            throw new Exception("Error occurred while restoring the post.");
        }
    }

    // Handle media upload
    private function handleMediaUpload($post, $media)
    {
        if ($media->isValid()) {
            // Store media file in a specific directory
            $path = $media->store('posts/' . $post->id, 'public');

            // Save the media URL in the post's media relation
            $post->uploadMedia()->create([
                'url' => Storage::url($path),
            ]);
        } else {
            throw new Exception("Invalid media file.");
        }
    }

    public function getPostsByAuthor(int $authorId)
    {
        return Post::where('author_id', $authorId)->get();
    }

    public function getPublishedPosts()
    {
        return Post::published()->get();
    }

    // Increment the views of a post
    public function incrementViews($id)
    {
        $post = $this->find($id);
        $post->incrementViews();
        return $post;
    }

    // Publish a post
    public function publish($id)
    {
        $post = $this->find($id);
        $post->publish();
        return $post;
    }

    // Unpublish a post
    public function unpublish($id)
    {
        $post = $this->find($id);
        $post->unpublish();
        return $post;
    }

    // Like a post
    public function like($postId, $userId)
    {
        $post = $this->find($postId);
        $post->likes()->attach($userId);
    }

    // Unlike a post
    public function unlike($postId, $userId)
    {
        $post = $this->find($postId);
        $post->likes()->detach($userId);
    }
    
     // Retrieve all posts, including soft deleted ones
     public function onlyTrashed($pagination = 10)
     {
         $query = Post::withTrashed();
 
         return Post::onlyTrashed()->get();
 
         return $query->paginate($pagination);
     }


}
