<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Cierra la sesión actual de forma segura.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        Auth::logout();

        /*
         * Invalida todos los datos de la sesión anterior.
         */
        $request->session()->invalidate();

        /*
         * Genera un nuevo token CSRF para la siguiente sesión.
         */
        $request->session()->regenerateToken();

        return redirect()
            ->route('home');
    }
}
