<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces
        $this->app->bind(
            \App\Repositories\Interfaces\OrganizationRepositoryInterface::class,
            \App\Repositories\OrganizationRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\ElectionRepositoryInterface::class,
            \App\Repositories\ElectionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\CandidateRepositoryInterface::class,
            \App\Repositories\CandidateRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\VoteRepositoryInterface::class,
            \App\Repositories\VoteRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\VoterAccreditationRepositoryInterface::class,
            \App\Repositories\VoterAccreditationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
