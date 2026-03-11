<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Profile fetched successfully',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'created_at' => $user->created_at,
            ],
        ], 200);
    }

    
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update($request->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

       
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password updated successfully',
        ], 200);
    }

    
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        
        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ], 200);
    }
}