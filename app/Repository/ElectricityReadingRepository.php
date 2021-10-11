<?php

namespace App\Repository;

use App\Models\ElectricityReadings;
use Illuminate\Support\Facades\DB;

class ElectricityReadingRepository
{
    public function getElectricityReadings($smartMeterId){
        return DB::table(ElectricityReadings::$tableName)
            ->join('smart_meters', 'electricity_readings.smart_meter_id', '=', 'smart_meters.id')
            ->where('smart_meters.smartMeterId', '=', $smartMeterId)
            ->get(['time', 'reading']);
    }
}
