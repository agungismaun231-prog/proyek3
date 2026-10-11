<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate mobile user via email and password.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
                'errors' => [
                    'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.']
                ]
            ], 422);
        }

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 200);
    }

    /**
     * Register a new customer via mobile app.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'customer',
            ]);

            Customer::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

            return $user;
        });

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Register a new provider (technician) via mobile app.
     */
    public function registerProvider(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'specialization' => 'nullable|string|max:255',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'technician',
            ]);

            Technician::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'specialization' => $validated['specialization'] ?? 'AC Maintenance',
                'status' => 'available',
            ]);

            return $user;
        });

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Handle Google OAuth login from mobile client.
     */
    public function googleLoginMobile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'id_token' => 'required|string',
        ]);

        $email = strtolower(trim($validated['email']));

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = DB::transaction(function () use ($validated, $email) {
                $createdUser = User::create([
                    'name' => $validated['name'],
                    'email' => $email,
                    'password' => Hash::make('google_' . uniqid('', true)),
                    'role' => 'customer',
                    'provider' => 'google',
                    'provider_id' => substr(hash('sha256', $validated['id_token']), 0, 32),
                ]);

                Customer::create([
                    'name' => $validated['name'],
                    'email' => $email,
                    'phone' => '',
                    'address' => '',
                ]);

                return $createdUser;
            });
        }

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 200);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ], 200);
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($user, $validated) {
            $oldEmail = $user->email;
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            if ($user->role === 'customer') {
                Customer::updateOrCreate(
                    ['email' => $oldEmail],
                    [
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                        'address' => $validated['address'],
                        'city' => $validated['city'] ?? null,
                        'postal_code' => $validated['postal_code'] ?? null,
                    ]
                );
            } elseif ($user->role === 'technician') {
                Technician::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                        'address' => $validated['address'],
                        'specialization' => $validated['specialization'] ?? null,
                    ]
                );
            }
        });

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => new UserResource($user->fresh()),
        ], 200);
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak sesuai.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json([
            'message' => 'Password berhasil diperbarui.',
        ], 200);
    }

    /**
     * Revoke mobile Sanctum token.
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logout berhasil.',
        ], 200);
    }
}
