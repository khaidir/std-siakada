<?php

namespace App\Http\Responses;

use App\Support\RoleDashboard;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Redirect pengguna ke dashboard sesuai perannya setelah login.
     */
    public function toResponse($request): RedirectResponse
    {
        $role = $request->user()?->getRoleNames()->first() ?? '';

        return redirect()->intended(RoleDashboard::pathFor($role));
    }
}
