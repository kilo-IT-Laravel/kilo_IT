<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 

class UserProfileController extends Controller
{
    public function show(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => $user
        ], 200);
    }



    public function update(Request $request)
    {
       
        $request->validate([
            'name' => 'required|string|max:255',
            'profile_image_url' => 'nullable|url', 
        ]);

        // Update other user name   
        $id = Auth::id();   
        $user = User::findOrFail($id)->update([
            'name' => $request->input('name'),
            'profile_image_url' => $request->input('profile_image_url') ? $request->input('profile_image_url') : null,
        ]);
     
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }
}
