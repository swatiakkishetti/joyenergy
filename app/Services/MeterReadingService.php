<?php

namespace App\Services;

use App\Models\ElectricityReadings;
use App\Models\MeterReadingsInitialize;
use App\Repository\ElectricityReadingRepository;
use App\Repository\PricePlanRepository;
use Illuminate\Support\Facades\DB;

class MeterReadingService
{
    private $electricityReadingRepository;
    private $pricePlanRepository;

    public function __construct(ElectricityReadingRepository $electricityReadingRepository, PricePlanRepository $pricePlanRepository)
    {
        $this->electricityReadingRepository = $electricityReadingRepository;
        $this->pricePlanRepository = $pricePlanRepository;
    }

    public function getReadings($smartMeterId)
    {
        return $this->electricityReadingRepository->getElectricityReadings($smartMeterId);
    }

    public function storeReadings($smartMeterId, $supplier, $readings): bool
    {
        $result = false;
        foreach ($readings as $reading) {
            $smartIDFromDb = $this->electricityReadingRepository->getSmartMeterId($smartMeterId);
            var_dump($smartIDFromDb);
            if (count($smartIDFromDb) > 0 && (int)$smartIDFromDb[0]->id > 0) {
                var_dump("im here");
                $result = $this->insertDataIntoElectricityReadings($reading, (int)$smartIDFromDb[0]->id);
            } else {
                $pricePlanIdFromDB = $this->pricePlanRepository->getPricePlanId($supplier);

                if (count($pricePlanIdFromDB) > 0) {
                    $pricePlanIdFromDB = (int)$pricePlanIdFromDB[0]->id;
                    $smartMeter = array('smartMeterId' => $smartMeterId, 'price_plan_id' => $pricePlanIdFromDB);

                    $insertedSmartMeterId = $this->electricityReadingRepository->insertSmartMeter($smartMeter);

                    if ($insertedSmartMeterId > 0) {
                        $result = $this->insertDataIntoElectricityReadings($reading, $insertedSmartMeterId);
                    }
                }
            }
        }
        return $result;
    }


    /**
     * @param $reading
     * @param int $smartIDFromDb
     * @return bool
     */
    private function insertDataIntoElectricityReadings($reading, int $smartIDFromDb):bool
    {
        $electricityReadingArray = array('reading' => $reading['reading'], 'time' => $reading['time'], 'smart_meter_id' => intval($smartIDFromDb),
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'));
        return $this->electricityReadingRepository->insertElectricityReadings($electricityReadingArray);
    }

}
