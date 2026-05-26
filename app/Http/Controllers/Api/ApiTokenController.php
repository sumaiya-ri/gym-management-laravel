<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiTokenController extends Controller
{
    /**
     * Authenticate and create a new Sanctum API token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::warning('Failed API login attempt: Invalid credentials.', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $user = Auth::user();
        $deviceName = $request->input('device_name', 'API Token');
        $abilities = $user->getAbilitiesByRole();
        $expiresAt = now()->addDays(30);

        // Create the token with the correct abilities and expiration
        $tokenResult = $user->createToken($deviceName, $abilities, $expiresAt);

        Log::info('Token created successfully.', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'device_name' => $deviceName,
            'abilities' => $abilities,
            'expires_at' => $expiresAt->toDateTimeString(),
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'token' => $tokenResult->plainTextToken,
            'token_type' => 'Bearer',
            'abilities' => $abilities,
            'expires_at' => $expiresAt->toIso8601String(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'gym_id' => $user->gym_id,
            ]
        ], 200);
    }

    /**
     * Revoke the current token.
     */
    public function revokeCurrent(Request $request)
    {
        $currentToken = $request->user()->currentAccessToken();
        
        if ($currentToken) {
            $tokenId = $currentToken->id;
            $currentToken->delete();

            Log::info('Current token revoked.', [
                'user_id' => $request->user()->id,
                'token_id' => $tokenId,
                'ip' => $request->ip(),
            ]);
        }

        return response()->json([
            'message' => 'Current token revoked successfully.'
        ], 200);
    }

    /**
     * Revoke a specific token by ID.
     */
    public function revokeSpecific(Request $request, $tokenId)
    {
        $token = $request->user()->tokens()->find($tokenId);

        if (!$token) {
            Log::warning('Failed token revocation: Token not found or unauthorized.', [
                'user_id' => $request->user()->id,
                'requested_token_id' => $tokenId,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Token not found.'
            ], 404);
        }

        $token->delete();

        Log::info('Specific token revoked.', [
            'user_id' => $request->user()->id,
            'token_id' => $tokenId,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => "Token ID {$tokenId} revoked successfully."
        ], 200);
    }

    /**
     * Revoke all tokens.
     */
    public function revokeAll(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        Log::info('All tokens revoked.', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'All tokens revoked successfully.'
        ], 200);
    }
}
