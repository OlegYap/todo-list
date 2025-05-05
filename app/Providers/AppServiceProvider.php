<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\TagRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Services\Interfaces\TagServiceInterface;
use App\Services\Interfaces\TaskServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\TagService;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);

        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(TaskServiceInterface::class, TaskService::class);
        $this->app->bind(TagServiceInterface::class, TagService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
