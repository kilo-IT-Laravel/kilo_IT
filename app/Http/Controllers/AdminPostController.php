<?php

namespace App\Http\Controllers;

use App\Repositories\Posts\PostInterface;
use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    protected $postRepository;

    public function __construct(PostInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    // Admin can view all posts
    public function index()
    {
        return response()->json($this->postRepository->all());
    }

    // Admin can view a specific post
    public function show($id)
    {
        return response()->json($this->postRepository->find($id));
        if (!$post){
            return request()->json(['message' => 'Post not found'], 404);
        }
        return request()->json([$post]);
    }

    // Admin can create a post
    public function store(Request $request)
    {
        $validated  = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|integer',
            'author_id' => 'required|integer',
            'thumbnail' => 'nullable|image',
            'read_time' => 'nullable|integer',
            'published_at' => 'nullable|date'
        ]);

        $post = $this->postRepository->create($validated);

        return response()->json(['message' => 'Post created successfully' ,$post], 201);
    }

    // Admin can update any post
    public function update(Request $request, $id)

    {
        $post = $this->postRepository->find($id);

         if (!$post) {
             return response()->json(['message' => 'Post not found'], 404);
         }

        $validated  = $request -> validate ([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|url',
            'read_time' => 'nullable|integer',
            'published_at' => 'nullable|date'
        ]);

        $updatedPost = $this->postRepository->update($id, $validated);

        return response()->json(['message' => 'Post updated successfully']);
    }

    // Admin can delete any post
    public function destroy($id)
    {
        $post = $this->postRepository->find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $this->postRepository->delete($id);
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
