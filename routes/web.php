<?php

use App\Livewire\Pages\Home;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
|
| Los componentes Livewire pueden registrarse directamente como páginas.
| La página Home utiliza su propio layout mediante el atributo #[Layout].
|
*/

Route::get('/', Home::class)
    ->name('home');
