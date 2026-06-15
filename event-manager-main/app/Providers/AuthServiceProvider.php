<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Comment;
use App\Policies\EventPolicy;
use App\Policies\CommentPolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Event::class => EventPolicy::class,
        Comment::class => CommentPolicy::class,
    ];


    public function register(): void
    {
        //
    }


    public function boot(): void
    {
        $this->registerPolicies();


        Gate::define('be-organizer', function (User $user) {
            return in_array($user->role, ['admin', 'organizer']);
        });

        Gate::define('access-admin-panel', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
