<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Trip;
use App\Policies\TripPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Trip::class, TripPolicy::class);
    }
}
