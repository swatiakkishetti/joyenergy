<?php

namespace App\Repository;

use App\Models\ElectricityReadings;
use Illuminate\Support\Facades\DB;

class ElectricityReadingRepository
{
    public function getElectricityReadings($smartMeterId): \Illuminate\Support\Collection
    {
        return DB::table(ElectricityReadings::$tableName)
            ->join('smart_meters', 'electricity_readings.smart_meter_id', '=', 'smart_meters.id')
            ->where('smart_meters.smartMeterId', '=', $smartMeterId)
            ->get(['time', 'reading']);
    }

    public function getSmartMeterId($smartMeterId)
    {
        return DB::table('smart_meters')
            ->where('smart_meters.smartMeterId', '=', $smartMeterId)
            ->first('smart_meters.id');
    }

    public function insertElectricityReadings($electricityReadingArray): bool
    {
        return DB::table(ElectricityReadings::$tableName)
            ->insert($electricityReadingArray);
    }

    public function insertSmartMeter($smartMeter): int
    {
        return DB::table('smart_meters')
            ->insertGetId($smartMeter);
    }
}
