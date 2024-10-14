<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Repositories\Posts\PostRepository;
use Illuminate\Support\Facades\Auth;
class UserPostController extends Controller
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    // List all posts created by the authenticated user
    public function index(Request $request)
    {
        $user = Auth::user();
        $posts = $this->postRepository->getPostsByAuthor($user->id);
        return response()->json($posts);
    }

    // Show a specific post created by the authenticated user
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $post = $this->postRepository->find($id);

        if (!$post || $post->author_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized or Post not found'], 403);
        }

        return response()->json($post);
    }

    // Create a new post by the authenticated user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|url',
            'read_time' => 'nullable|integer',
            'published_at' => 'nullable|date'
        ]);

        $validated['author_id'] = $request->user()->id;
        $post = $this->postRepository->create($validated);

        return response()->json($post, 201);
    }

    // Update a post created by the authenticated user
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $post = $this->postRepository->find($id);

        if (!$post || $post->author_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized or Post not found'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|url',
            'read_time' => 'nullable|integer',
            'published_at' => 'nullable|date'
        ]);

        $updatedPost = $this->postRepository->update($id, $validated);
        return response()->json($updatedPost);
    }

    // Delete a post created by the authenticated user
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $post = $this->postRepository->find($id);

        if (!$post || $post->author_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized or Post not found'], 403);
        }

        $this->postRepository->delete($id);
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
