<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Responses\ApiResponse
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        // we just use the email for the name
        $validated['name'] = $validated['email'];

        $user = User::create($validated);

        return new ApiResponse($user, 'User created.', 201);
    }

    /**
     * Login a user and return a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Responses\ApiResponse
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::attempt($validated)) {
            $user = User::where('email', $validated['email'])->first();
            $token = $user->createToken('auth_token')->plainTextToken;

            return new ApiResponse([
                'user' => $user,
                'token' => $token,
            ], 'User logged in.');
        } else {
            return new ApiResponse(null, 'Invalid credentials.', 401);
        }
    }

    /**
     * Logout a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Responses\ApiResponse
     */
    public function logout(Request $request)
    {
        // Revoke all tokens...
        $request->user()->tokens()->delete();

        return new ApiResponse(null, 'User logged out.');
    }

    /**
     * Get the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Responses\ApiResponse
     */
    public function user(Request $request)
    {
        return new ApiResponse($request->user(), 'User retrieved.');
    }

    /**
     * Update the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Responses\ApiResponse
     */
    public function update(Request $request)
    {
        $request->user()->update($request->all());
        return new ApiResponse($request->user(), 'User updated.');
    }
}
