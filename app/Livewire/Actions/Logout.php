<?php

declare(strict_types=1);

namespace App\Livewire\Actions;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): Redirector|RedirectResponse
    {
        $guard = Auth::guard('web');

        if ($guard instanceof StatefulGuard) {
            $guard->logout();
        }

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}
