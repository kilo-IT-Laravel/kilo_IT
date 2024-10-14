<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Repositories\Posts\PostRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $posts = $this->postRepository->getPostsByAuthor($user->id);
        return response()->json($posts);
    }


    // Get all published posts
    public function getPublished()
    {
        try {
            $posts = $this->postRepository->getPublishedPosts();
            return response()->json($posts, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching published posts: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch published posts'], 500);
        }
    }

    // Get a single post by ID
    public function show($id)
    {
        try {
            $post = $this->postRepository->find($id);

            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }

            return response()->json($post, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching post: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving the post'], 500);
        }
    }

    // Store a new post
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required',
                'category_id' => 'required|integer',
                'author_id' => 'required|integer',
                'thumbnail' => 'nullable|string|max:500',
                'read_time' => 'nullable|integer',
            ]);

            $post = Post::create($validatedData);

            return response()->json($post, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create post'], 500);
        }
    }

    // Update an existing post
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required',
                'category_id' => 'required|integer',
                'thumbnail' => 'nullable|string|max:500',
                'read_time' => 'nullable|integer',
            ]);

            $post = $this->postRepository->update($id, $validated);

            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }

            return response()->json($post, 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update post'], 500);
        }
    }

    // Increment views
    public function incrementViews($id)
    {
        try {
            $post = $this->postRepository->incrementViews($id);

            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }

            return response()->json($post, 200);
        } catch (\Exception $e) {
            Log::error('Error incrementing views: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to increment views'], 500);
        }
    }

    // Publish a post
    public function publish($id)
    {
        try {
           
            $this->postRepository->publish($id);
         
            return response()->json(['message' => 'Post published'], 200);
        } catch (\Exception $e) {
            Log::error('Error publishing post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to publish post'], 500);
        }
    }

    // Unpublish a post
    public function unpublish($id)
    {
        try {
            $this->postRepository->unpublish($id);
            return response()->json(['message' => 'Post unpublished'], 200);
        } catch (\Exception $e) {
            Log::error('Error unpublishing post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to unpublish post'], 500);
        }
    }

    // Delete a post
    public function softDelete($id)
    {
        try {
            $this->postRepository->delete($id);
            return response()->json(['message' => 'Post deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete post'], 500);
        }
    }

    // Restore a deleted post
    public function restore($id)
    {
        try {
            $this->postRepository->restore($id);
            return response()->json(['message' => 'Post restored successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error restoring post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to restore post'], 500);
        }
    }

    // Force delete a post
    public function forceDelete($id)
    {
        try {
            $this->postRepository->forceDelete($id);
            return response()->json(['message' => 'Post permanently deleted'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error permanently deleting post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to permanently delete post'], 500);
        }
    }

    // Like a post
    public function like($id)
    {
        try {
            $this->postRepository->like($id, Auth::id());
            return response()->json(['message' => 'Post liked successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error liking post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to like post'], 500);
        }
    }

    // Unlike a post
    public function unlike($id)
    {
        try {
            $this->postRepository->unlike($id, Auth::id());
            return response()->json(['message' => 'Post unliked successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error unliking post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to unlike post'], 500);
        }
    }

    // Get all trashed posts
    public function trashed()
    {
        try {
            $trashedPosts = $this->postRepository->onlyTrashed();
            if($trashedPosts->isEmpty()) {
                return response()->json(['message' => 'No trashed posts found'], 200);
            }
            return response()->json($trashedPosts, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching trashed posts: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch trashed posts'], 500);
        }
    }
}
