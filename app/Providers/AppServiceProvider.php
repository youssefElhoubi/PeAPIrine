<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\DAO\DAOintretface\SalesInterface;
use App\DAO\DAOs\SaleDAO;
use App\Repository\RepositoryIntarface\SaleRepoInterface;
use App\Repository\SaleRepo;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SalesInterface::class,SaleDAO::class);
        $this->app->bind(SaleRepoInterface::class,SaleRepo::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
