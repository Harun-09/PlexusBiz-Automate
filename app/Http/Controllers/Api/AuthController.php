<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Auth\ApiLoginData;
use App\DTOs\Auth\ApiRegisterData;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    use FormatsApiResponses;

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $dto = ApiRegisterData::fromValidated($validated);

        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'status' => UserStatus::Active->value,
        ]);

        $user->assignRole(Role::findOrCreate(RoleName::Buyer->value));

        $token = $user->createToken($dto->deviceName)->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->profileData($user->fresh()),
        ], 'Registration completed successfully.', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $dto = ApiLoginData::fromValidated($validated);
        $user = User::query()->where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        if ($user->status !== UserStatus::Active) {
            return $this->errorResponse('Your account is not active. Please contact support.', 403);
        }

        $token = $user->createToken($dto->deviceName)->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->profileData($user),
        ], 'Login successful.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Logout successful.');
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $currentToken = $user?->currentAccessToken();

        if (! $user || ! $currentToken) {
            return $this->errorResponse('No active API token found.', 401);
        }

        $currentToken->delete();
        $token = $user->createToken('api-refresh')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->profileData($user),
        ], 'Token refreshed successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            $this->profileData($request->user()),
            'Authenticated user profile fetched successfully.',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function profileData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status->value,
            'roles' => $user->getRoleNames()->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
        ];
    }
}
