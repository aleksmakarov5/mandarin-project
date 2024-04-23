<?php

namespace App\Providers;


use App\Models\User;
use Illuminate\Support\Facades\Gate;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('administrator', function (User $user) {
            return $user->GroupId === 1;
        });
        Gate::define('admin', function (User $user) {
            return $user->GroupId === 2 || $user->GroupId === 1 || $user->GroupId === 7 || $user->GroupId === 8 || $user->GroupId === 3;
        });
        Gate::define('buh', function (User $user) {
            return $user->GroupId === 3 || $user->GroupId === 1 || $user->GroupId === 7;
        });
        Gate::define('user', function (User $user) {
            return $user->GroupId === 4 || $user->GroupId === 1 || $user->GroupId === 7;
        });
        Gate::define('mrk', function (User $user) {
            return $user->GroupId === 5 || $user->GroupId === 1 || $user->GroupId === 7;
        });
        Gate::define('spm', function (User $user) {
            return $user->GroupId === 6 || $user->GroupId === 1 || $user->GroupId === 7;
        });
        Gate::define('owner', function (User $user) {
            return $user->GroupId === 7 || $user->GroupId === 1 || $user->GroupId === 7;
        });
        Gate::define('km', function (User $user) {
            return $user->GroupId === 8 || $user->GroupId === 1 || $user->GroupId === 7;
        });
        Gate::define('company', function (User $user) {
            return $user->GroupId < 9;
        });
        Gate::define('service', function (User $user) {
            return $user->GroupId === 9;
        });
    }
}
