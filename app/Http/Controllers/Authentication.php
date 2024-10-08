<?php

namespace App\Http\Controllers;

use App\CustomValidation\CustomValue;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Authentication extends Controller
{
    private Request $req;
    public function __construct(Request $req)
    {
        $this->req = $req;
    }
    public function register()
    {
        try {
            $validated = Validator::make($this->req->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed'
            ])->validate();

            $defaultId = Role::where('name', 'owner')->first()->id;

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $defaultId
            ]);

            $expireDate = now()->addDays(7);
            $token = $user->createToken('my_token' , expiresAt:$expireDate)->plainTextToken;

            return response()->json(['success' => true, 'message' => 'welcome new member ^w^', 'user' => $user, 'token' => $token], 201);
        } catch (ValidationException $e) {
            $customErrorMessage = 'Oops, looks like something went wrong with your submission.';
            return response(['success' => false, 'message' => $customErrorMessage , 'issues' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error("error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function login()
    {
        try {
            $validated = Validator::make($this->req->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ],CustomValue::LoginMsg())->validate();

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response(['success' => false, 'message' => "incorrect credential! >w<"]);
            }

            $expireDate = now()->addDays(7);
            $token = $user->createToken('my_token' , expiresAt:$expireDate)->plainTextToken;

            return response()->json(['success' => true, 'message' => 'welcome back master :3', 'user' => $user, 'token' => $token], 200);
        } catch (ValidationException $e) {
            $customErrorMessage = 'oops look likes something wrong with your submission';
            return response(['success' => false, 'message' => $customErrorMessage, 'issues' => $e->errors()], 422);
        } catch (Exception $e) {
            Log("error: ", $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function logout(){

        $this->req->user()->currentAccessToken()->delete();
        
        return response()->json(['message'=>'Logged out successfully!']);
    }
}
