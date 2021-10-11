<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class PricePlanRepository
{
    public function getPricePlanId($supplier){
        return DB::table('price_plans')
            ->where('price_plans.supplier', '=', $supplier)
            ->get('price_plans.id');
    }
}
