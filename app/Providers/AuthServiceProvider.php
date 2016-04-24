<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
        $gate->define('create', function ($user) {
            return $user->name == 'admin' or $user->name == 'user';
        });
        $gate->define('show', function ($user) {
            return $user->name == 'user' || $user->name == 'admin' || $user->name == 'guest';
        });
        $gate->define('edit', function ($user) {
            return $user->name == 'admin';
        });
        $gate->define('delete', function ($user) {
            return $user->name == 'admin';
        });
    }
}
