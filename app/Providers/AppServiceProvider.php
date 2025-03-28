<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\DAO\DAOintretface\SalesInterface;
use App\DAO\DAOs\SaleDAO;
use App\Repository\RepositoryIntarface\SaleRepoInterface;
use App\Repository\SaleRepo;
use App\DAO\DAOintretface\UserAuthInterface;
use App\DAO\DAOs\UserAuthDAO;
use App\DAO\DAOintretface\Catigoryinterface;
use App\DAO\DAOs\CatugoryDAO;
use App\DAO\DAOintretface\OrderDAOInterface;
use App\DAO\DAOs\OrderDAO;
use App\DAO\DAOintretface\PlantDAOInterface;
use App\DAO\DAOs\PlantDAO;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PlantDAOInterface::class, PlantDAO::class);
        $this->app->bind(OrderDAOInterface::class, OrderDAO::class);
        $this->app->bind(Catigoryinterface::class, CatugoryDAO::class);
        $this->app->bind(UserAuthInterface::class, UserAuthDAO::class);
        $this->app->bind(SalesInterface::class, SaleDAO::class);
        $this->app->bind(SaleRepoInterface::class, SaleRepo::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
