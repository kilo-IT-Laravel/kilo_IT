<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;
use App\Policies\PostPolicy;

class AuthServiceProvider extends ServiceProvider
{
  
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

   
    public function boot()
    {
        $this->registerPolicies();

       
    }
}
