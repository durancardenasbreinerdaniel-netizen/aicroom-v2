<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Pages\Home;
use App\Livewire\Participant\Dashboard;
use App\Livewire\Participant\Evaluations\Index as EvaluationIndex;
use App\Livewire\Participant\Evaluations\Show as EvaluationShow;
use App\Livewire\Participant\Results\Index as ResultIndex;
use App\Livewire\Participant\Results\Show as ResultShow;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', Home::class)
    ->name('home');

/*
|--------------------------------------------------------------------------
| Rutas para visitantes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)
        ->name('login');

    Route::get('/register', Register::class)
        ->name('register');
});

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', Dashboard::class)
        ->name('dashboard');

    Route::get(
        '/evaluations',
        EvaluationIndex::class
    )->name('evaluations.index');

    Route::get(
        '/evaluations/{evaluation}',
        EvaluationShow::class
    )->name('evaluations.show');

    Route::get(
        '/results',
        ResultIndex::class,
    )->name('results.index');

    Route::get(
        '/results/{evaluation}',
        ResultShow::class,
    )->name('results.show');

    Route::post('/logout', LogoutController::class)
        ->name('logout');
});
