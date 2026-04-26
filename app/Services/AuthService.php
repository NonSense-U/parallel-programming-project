<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Throwable;

class AuthService
{
    public function register(array $payload)
    {
        try {
            $data = collect();
            $user = User::create($payload['base']);;
            $data['user'] = $user;
            if (!empty($payload['login']) && $payload['login']) {
                $data['token'] = $user->createToken('auth_token')->plainTextToken;
            }


            return $data;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function login(array $payload)
    {
        try {

            $user = User::query()->where('email', $payload['email'])->firstOrFail();
            $response['id'] = $user->id;
            $response['name'] = $user->name;
            $response['access_token'] = $user->createToken('auth_token')->plainTextToken;
            $response['token_type'] = 'Bearer';

            return $response;
        } catch (Throwable $e) {
            throw $e;
        }
    }


    public function logout(User $user)
    {
        try {
            /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
            $token = $user->currentAccessToken();
            $token?->delete();
            $user->update(['fcm_token' => null]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
