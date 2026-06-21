<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class FanAuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $method = $request->validate([
            'method' => ['required', Rule::in(['email', 'phone'])],
        ])['method'];

        $data = $method === 'email'
            ? $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'max:255'],
            ])
            : $request->validate([
                'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/', 'unique:users,phone'],
            ]);

        $user = User::create([
            'name' => $method === 'email'
                ? $data['name']
                : 'Fan '.substr($data['phone'], -4),
            'email' => $method === 'email' ? Str::lower($data['email']) : null,
            'phone' => $method === 'phone' ? $data['phone'] : null,
            'password' => Hash::make(
                $method === 'email' ? $data['password'] : Str::random(64)
            ),
            'has_password' => $method === 'email',
            'is_artist' => false,
            'is_super_admin' => false,
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json(['user' => $this->userData($user)], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = User::where('email', Str::lower($data['email']))->first();

        if (! $user || $user->is_artist || $user->is_super_admin) {
            return response()->json(['message' => 'No fan account was found with that email address.'], 404);
        }

        if (! $user->has_password || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'The password you entered is incorrect.'], 422);
        }

        Auth::login($user, (bool) ($data['remember'] ?? false));
        $request->session()->regenerate();

        return response()->json(['user' => $this->userData($user)]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Signed out.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->fan($request);

        return response()->json(['user' => $this->userData($user)]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->fan($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $user->name = $data['name'];

        if ($request->boolean('remove_avatar') && $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->avatar_path = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated.',
            'user' => $this->userData($user),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = $this->fan($request);

        $rules = [
            'password' => ['required', 'confirmed', Password::min(8)],
        ];

        if ($user->has_password) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $data = $request->validate($rules);
        $user->password = $data['password'];
        $user->has_password = true;
        $user->save();

        $request->session()->regenerate();

        return response()->json(['message' => 'Password updated.']);
    }

    private function fan(Request $request): User
    {
        $user = $request->user();
        abort_unless($user && ! $user->is_artist && ! $user->is_super_admin, 403);

        return $user;
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar_url' => $user->avatar_url,
            'has_password' => $user->has_password,
        ];
    }
}
