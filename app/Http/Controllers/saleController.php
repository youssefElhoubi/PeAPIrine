<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use APP\Repository\RepositoryIntarface\SaleRepoInterface;
use Symfony\Component\HttpFoundation\Response;

class saleController extends Controller
{
    protected $saleRepo;
    public function __construct(SaleRepoInterface $saleRepo)
    {
        $this->saleRepo = $saleRepo;
    }
    public function totaleTales()
    {
        $totaleSale =  $this->saleRepo->totaleTales();
        return response()->json(['totaleSale' => $totaleSale], Response::HTTP_OK);
    }
    public function popularPlants()
    {
        $mostPopularePlants =  $this->saleRepo->popularPlants();
        return response()->json(['mostPopularePlants' => $mostPopularePlants], Response::HTTP_OK);
    }
    public function salesByCatigory()
    {
        $bestcategorySale =  $this->saleRepo->salesByCatigory();
        return response()->json(['bestcategorySale' => $bestcategorySale], Response::HTTP_OK);
    }
}
