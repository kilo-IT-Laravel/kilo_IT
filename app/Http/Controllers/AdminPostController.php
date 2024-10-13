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
    }

    // Admin can create a post
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|integer',
            'author_id' => 'required|integer',
            'thumbnail' => 'nullable|image',
        ]);

        $this->postRepository->create($validatedData);

        return response()->json(['message' => 'Post created successfully'], 201);
    }

    // Admin can update any post
    public function update(Request $request, $id)
    {
        $validatedData = $request -> validate ([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        $this->postRepository->update($id, $request->all());

        return response()->json(['message' => 'Post updated successfully']);
    }

    // Admin can delete any post
    public function destroy($id)
    {
        $this->postRepository->delete($id);

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
