<?php

namespace App\Http\Controllers;

use App\Repositories\Posts\PostInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserPostController extends Controller
{
    protected $postRepository;

    public function __construct(PostInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    // view own post 
    public function index()
    {
        $userId = Auth::id();
        $posts = $this->postRepository->all()->where('author_id', $userId);

        return response()->json($posts);
    }

    // Users can view a specific post 
    public function show($id)
    {
        $post = $this->postRepository->find($id);

        if ($post->author_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($post);
    }

    // Users can create their own post
    public function store(Request $request)

    {   
        $validatedData = $request->validate ([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|integer',
        ]);

        $data = $request->all();
        $data['author_id'] = Auth::id(); // Automatically set the authenticated user as the author

        $this->postRepository->create($data);

        return response()->json(['message' => 'Post created successfully'], 201);
    }

    // Users can update only their own posts
    public function update(Request $request, $id)
    {
        $post = $this->postRepository->find($id);

        if ($post->author_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        $this->postRepository->update($id, $request->all());

        return response()->json(['message' => 'Post updated successfully']);
    }

    // Users can delete only their own posts
    public function destroy($id)
    {
        $post = $this->postRepository->find($id);

        if ($post->author_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->postRepository->delete($id);

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
