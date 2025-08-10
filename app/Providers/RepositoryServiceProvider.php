<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Knowledge\KnowledgeRepositoryInterface;
use App\Repositories\Knowledge\KnowledgeRepository;
use App\Services\Knowledge\KnowledgeService;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Services\User\UserService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Knowledge Module
        $this->app->bind(KnowledgeRepositoryInterface::class, KnowledgeRepository::class);
        $this->app->bind(KnowledgeService::class, function ($app) {
            return new KnowledgeService($app->make(KnowledgeRepositoryInterface::class));
        });

        // User Module
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(UserRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

