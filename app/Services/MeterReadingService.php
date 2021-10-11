<?php

namespace App\Services;

use App\Models\ElectricityReadings;
use App\Models\MeterReadingsInitialize;
use App\Repository\ElectricityReadingRepository;
use Illuminate\Support\Facades\DB;

class MeterReadingService
{
    Private $electricityReadingRepository;

    public function __construct(ElectricityReadingRepository $electricityReadingRepository)
    {
       $this-> electricityReadingRepository = $electricityReadingRepository;
    }

    public function getReadings($smartMeterId)
    {
        return $this->electricityReadingRepository->getElectricityReadings($smartMeterId);
    }

    public function storeReadings($smartMeterId, $supplier, $readings)
    {

        foreach ($readings as $reading) {
            $result = false;
            $smartIDFromDb = DB::table('smart_meters')
                ->where('smart_meters.smartMeterId', '=', $smartMeterId)
                ->get('smart_meters.id');

            if(count($smartIDFromDb) > 0 && (int)$smartIDFromDb[0]->id > 0 ){
                $result = $this->insertDataIntoElectricityReadings($reading, (int)$smartIDFromDb[0]->id);
            }
            else{
                $pricePlanIdFromDB = DB::table('price_plans')
                            ->where('price_plans.supplier', '=', $supplier)
                            ->get('price_plans.id');

                if(count($pricePlanIdFromDB) > 0){
                    $pricePlanIdFromDB = (int)$pricePlanIdFromDB[0]->id;
                    $data = array('smartMeterId' => $smartMeterId, 'price_plan_id' => $pricePlanIdFromDB);

                    $insertedSmartMeterId = DB::table('smart_meters')
                        ->insertGetId($data);

                    if($insertedSmartMeterId > 0){
                        $result = $this->insertDataIntoElectricityReadings($reading, $insertedSmartMeterId);
                    }
                }
            }
        }
        return $result;
    }


    /**
     * @param $reading
     * @param array $smartIDFromDb
     */
    private function insertDataIntoElectricityReadings($reading, int $smartIDFromDb)
    {
        $data = array('reading' => $reading['reading'], 'time' => $reading['time'], 'smart_meter_id' => intval($smartIDFromDb),
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'));
        $result = DB::table(ElectricityReadings::$tableName)
            ->insert($data);

        return $result;
    }

    /**
     * @param $smartMeterId
     * @return \Illuminate\Support\Collection
     */
    private function getSmartIDFromDB($smartMeterId): \Illuminate\Support\Collection
    {
        return DB::table('smart_meters')
            ->where('smart_meters.smartMeterId', '=', $smartMeterId)
            ->get('smart_meters.id')->values();
    }

}
