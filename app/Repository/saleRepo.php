<?php

namespace App\Repository;

use App\DAO\DAOs\SaleDAO;
use App\Repository\RepositoryIntarface\SaleRepoInterface;

class SaleRepo implements SaleRepoInterface
{
    protected $saleDAO;
    public function __construct(SaleDAO $saleDAO)
    {
        $this->saleDAO = $saleDAO;
    }
    public function totaleTales()
    {
        return $this->saleDAO->totaleTales();
    }
    public function popularPlants()
    {
        return $this->saleDAO->popularPlants();
    }
    public function salesByCatigory()
    {
        return $this->saleDAO->salesByCatigory();
    }
}
