<?php

namespace App\Services;

use App\Exceptions\InvalidMeterIdException;
use App\Models\MeterReadingsInitialize;
use App\Models\PricePlan;
use Illuminate\Support\Facades\DB;

class PricePlanService
{
    private $meterReadingService;
    private $meterReadingInitializer;

    public function __construct(MeterReadingService $meterReadingService, MeterReadingsInitialize $meterReadingInitializer)
    {
        $this->meterReadingService = $meterReadingService;
        $this->meterReadingInitializer = $meterReadingInitializer;
    }

    public function getConsumptionCostOfElectricityReadingsForEachPricePlan($smartMeterId)
    {
        $getCostForAllPlans = [];

        $readings = DB::table('electricity_readings')
            ->join('smart_meters', 'electricity_readings.smart_meter_id', '=', 'smart_meters.id')
            ->where('smart_meters.smartMeterId', '=', $smartMeterId)
            ->get(['time', 'reading'])->toArray();

        $pricePlans = DB::table('price_plans')->get(['supplier', 'unitRate'])->toArray();

        if (is_null($readings)) {
            return $readings;
        }

        foreach ($pricePlans as $pricePlan) {
            $getCostForAllPlans[] = array('key' => $pricePlan->supplier, 'value' => $this->calculateCost($readings, $pricePlan));
        }

        return $getCostForAllPlans;
    }

    public function getCostPlanForAllSuppliersWithCurrentSupplierDetails($smartMeterId)
    {
        $costPricePerPlans = $this->getConsumptionCostOfElectricityReadingsForEachPricePlan($smartMeterId);
        $currentAvailableSupplierIds = DB::table('smart_meters')
            ->join('price_plans', 'smart_meters.price_plan_id', '=', 'price_plans.id')
            ->get(['smartMeterId', 'supplier'])->toArray();


        $currentSupplierIdForSmartMeterID = [];
        foreach ($currentAvailableSupplierIds as $currentAvailableSupplierId) {
            if ($currentAvailableSupplierId->smartMeterId = $smartMeterId) {
                $currentSupplierIdForSmartMeterID = ['Current Supplier' => $currentAvailableSupplierId->supplier,
                    "SmartMeterId" => $currentAvailableSupplierId->smartMeterId];
            }
        }
        array_push($costPricePerPlans, $currentSupplierIdForSmartMeterID);

        return $costPricePerPlans;
    }

    private function calculateCost($electricityReadings, $pricePlan)
    {
        $average = $this->calculateAverageReading($electricityReadings);
        $timeElapsed = $this->calculateTimeElapsed($electricityReadings);
        $averagedCost = $average / $timeElapsed;
        return $averagedCost * $pricePlan->unitRate;
    }

    private function calculateAverageReading($electricityReadings)
    {
        if(count($electricityReadings) <= 0){
            throw new InvalidMeterIdException("No readings available");
        }
        $newSummedReadings = 0;
        foreach ($electricityReadings as $electricityReading) {
            foreach ($electricityReading as $reading) {
                $newSummedReadings += (int)$reading;
            }
        }
        return $newSummedReadings / count($electricityReadings);
    }

    private function calculateTimeElapsed($electricityReadings)
    {
        $readingHours = [];
        foreach ($electricityReadings as $electricityReading) {
            foreach ($electricityReading as $time) {
                $readingHours[] = $time;
            }
        }
        $minimumElectricityReading = strtotime(min($readingHours));
        $maximumElectricityReading = strtotime(max($readingHours));
        return abs($maximumElectricityReading - $minimumElectricityReading) / (60 * 60);
    }
}
