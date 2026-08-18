<?php

use App\Http\Controllers\AuthController;
use App\Livewire\DispatchBoard;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dispatch.index')
    : redirect()->route('role.select'));

Route::middleware('guest')->group(function (): void {
    Route::get('/assessment-role', [AuthController::class, 'selectRole'])
        ->name('role.select');

    Route::post('/assessment-role/{role}', [AuthController::class, 'assumeRole'])
        ->whereIn('role', ['dispatcher', 'supervisor', 'administrator'])
        ->name('role.assume');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dispatch', DispatchBoard::class)->name('dispatch.index');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
