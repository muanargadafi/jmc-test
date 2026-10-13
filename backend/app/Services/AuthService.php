<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthService
 *
 * Handles authentication business logic, session management, and authorization abilities.
 *
 * @package App\Services
 */
class AuthService
{
    private const ABILITIES = [
        'barang-masuk.manage',
        'master-data.manage',
        'users.manage',
    ];

    /**
     * Authenticate user credentials, check account lock status, and start a session.
     *
     * @param array<string, string> $credentials
     * @param Request $request
     * @return User
     * @throws ValidationException
     */
    public function login(array $credentials, Request $request): User
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'username' => ['Invalid username or password.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->isLocked()) {
            $this->terminateSession($request);

            throw ValidationException::withMessages([
                'username' => ['Your account is locked, please contact the administrator.'],
            ]);
        }

        $request->session()->regenerate();

        return $user;
    }

    /**
     * Log the user out and invalidate the session.
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void
    {
        $this->terminateSession($request);
    }

    /**
     * Get allowed abilities/permissions for a given user.
     *
     * @param User $user
     * @return array<int, string>
     */
    public function allowedAbilities(User $user): array
    {
        return collect(self::ABILITIES)
            ->filter(fn(string $ability) => Gate::forUser($user)->allows($ability))
            ->values()
            ->all();
    }

    /**
     * Terminate the current user session and invalidate tokens.
     *
     * @param Request $request
     * @return void
     */
    private function terminateSession(Request $request): void
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
