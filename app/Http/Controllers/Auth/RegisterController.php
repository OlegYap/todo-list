<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\DTO\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $userDTO = UserDTO::fromArray($validated);
        $user = $this->userService->createUser($userDTO);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
