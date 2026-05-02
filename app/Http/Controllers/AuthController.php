<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());
        return ApiResponse::success('User registered successfully', $data, 201);
    }

    public function login(LoginRequest $request)
    {
        sleep(1);
        $response = $this->authService->login($request->validated());
        return ApiResponse::success('Login successful', $response);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return ApiResponse::success('Logout successful');
    }
}
