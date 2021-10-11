<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class PricePlanRepository
{
    public function getPricePlanId($supplier){
        return DB::table('price_plans')
            ->where('price_plans.supplier', '=', $supplier)
            ->first('price_plans.id');
    }

    public function getPricePlans(): array
    {
        return DB::table('price_plans')->get(['supplier', 'unitRate'])->toArray();
    }

    public function getCurrentAvailableSupplierIds(): array
    {
        return  DB::table('smart_meters')
            ->join('price_plans', 'smart_meters.price_plan_id', '=', 'price_plans.id')
            ->get(['smartMeterId', 'supplier'])->toArray();
    }
}
