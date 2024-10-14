<?php

namespace App\Providers;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryInterface;
use App\Repositories\Posts\PostController;
use App\Repositories\Posts\PostInterface;
use App\Repositories\PostViews\PostViewController;
use App\Repositories\PostViews\PostViewInterface;
use App\Repositories\Topics\TopicController;
use App\Repositories\Topics\TopicInterface;
use App\Repositories\UploadMedias\UploadMediaController;
use App\Repositories\UploadMedias\UploadMediaInterface;
use App\Repositories\User\UserController;
use App\Repositories\User\UserInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserInterface::class , UserController::class);
        $this->app->bind(CategoryInterface::class , CategoryRepository::class);
        $this->app->bind(PostInterface::class , PostController::class);
        $this->app->bind(PostViewInterface::class , PostViewController::class);
        $this->app->bind(TopicInterface::class , TopicController::class);
        $this->app->bind(UploadMediaInterface::class , UploadMediaController::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
