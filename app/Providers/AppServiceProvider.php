<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GameService;
use App\Services\Interfaces\GameServiceInterface;
use App\Repositories\GuessAttemptRepository;
use App\Repositories\Interfaces\GuessAttemptRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(GameServiceInterface::class, GameService::class);
        $this->app->bind(GuessAttemptRepositoryInterface::class, GuessAttemptRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
