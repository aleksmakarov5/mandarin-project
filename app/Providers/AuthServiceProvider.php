<?php

namespace App\Providers;


use App\Models\User;
use App\Models\user_role;
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
    public function getRole($id_user, $id_role)
    {
        $roles = user_role::where('id_user', '=', $id_user)->get();
        $user_role = false;
        foreach ($roles as $role) {
            if ($role->id_group == $id_role) {
                $user_role = true;
                break;
            }
        }
        return $user_role;
    }
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('administrator', function (User $user) {
            return $this->getRole($user->id, 1);
        });
        Gate::define('admin', function (User $user) {
            return $this->getRole($user->id, 2);
        });
        Gate::define('buh', function (User $user) {
            return $this->getRole($user->id, 3);
        });
        Gate::define('user', function (User $user) {
            return $this->getRole($user->id, 4);
        });
        Gate::define('mrk', function (User $user) {
            return $this->getRole($user->id, 5);
        });
        Gate::define('spm', function (User $user) {
            return $this->getRole($user->id, 6);
        });
        Gate::define('owner', function (User $user) {
            return $this->getRole($user->id, 7);
        });
        Gate::define('km', function (User $user) {
            return $this->getRole($user->id, 8);
        });
        Gate::define('company', function (User $user) {
            return $this->getRole($user->id, 1)
                || $this->getRole($user->id, 2)
                || $this->getRole($user->id, 3)
                || $this->getRole($user->id, 4)
                || $this->getRole($user->id, 5)
                || $this->getRole($user->id, 6)
                || $this->getRole($user->id, 7)
                || $this->getRole($user->id, 8);
        });
        Gate::define('service', function (User $user) {

            return $this->getRole($user->id, 9);
        });
    }
}
