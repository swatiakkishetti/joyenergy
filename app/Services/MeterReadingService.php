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

    public function getReadings($smartMeterId): \Illuminate\Support\Collection
    {
        return $this->electricityReadingRepository->getElectricityReadings($smartMeterId);
    }

    public function storeReadings($smartMeterId, $supplier, $readings): bool
    {
        $result = false;
        foreach ($readings as $reading) {
            $smartIDFromDb = $this->electricityReadingRepository->getSmartMeterId($smartMeterId);

            if ($smartIDFromDb != null && $smartIDFromDb->id > 0) {
                $result = $this->insertDataIntoElectricityReadings($reading, $smartIDFromDb->id);
            } else {
                $pricePlanIdFromDB = $this->pricePlanRepository->getPricePlanId($supplier);

                if ($pricePlanIdFromDB !=null && $pricePlanIdFromDB->id > 0) {
                    $smartMeter = array('smartMeterId' => $smartMeterId, 'price_plan_id' => $pricePlanIdFromDB->id);
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
        $electricityReadingArray = array('reading' => $reading['reading'], 'time' => $reading['time'], 'smart_meter_id' => $smartIDFromDb,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'));
        return $this->electricityReadingRepository->insertElectricityReadings($electricityReadingArray);
    }

}
