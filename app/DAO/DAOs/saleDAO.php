<?php
namespace App\DAO\DAOs;
use App\DAO\DAOintretface\SalesInterface;
use App\Models\orders;
use Illuminate\Support\Facades\DB;

class SaleDAO implements SalesInterface{
    protected orders $orders ;
    public function __construct(orders $orders){
        $this->orders = $orders ;
    }
    public function totaleTales(){
        $popularPlants = DB::table('orders')
    ->join('plants', 'orders.plant_id', '=', 'plants.id')
    ->select('plants.id', 'plants.name', DB::raw('COUNT(*) as total_orders'))
    ->groupBy('plants.id', 'plants.name')
    ->orderByDesc('total_orders')
    ->limit(3)
    ->get();
    return $popularPlants;
    }
    public function popularPlants(){}
    public function salesByCatigory(){
        $topCategory = DB::table('orders')
    ->join('palnts', 'orders.plant_id', '=', 'palnts.id') // Fix table name if needed
    ->join('categories', 'palnts.category_id', '=', 'categories.id')
    ->select('categories.id', 'categories.name', DB::raw('COUNT(orders.id) as total_sales'))
    ->groupBy('categories.id', 'categories.name')
    ->orderByDesc('total_sales')
    ->get();
    return $topCategory;
    }

}