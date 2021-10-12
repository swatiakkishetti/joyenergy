<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class PricePlanRepository
{
    public function getRandomPricePlanId(){
        return DB::table('price_plans')->get('price_plans.id')->random();
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
