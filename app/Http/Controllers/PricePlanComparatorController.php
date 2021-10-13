<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidMeterIdException;
use App\Services\PricePlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class PricePlanComparatorController extends Controller
{
    private $pricePlanService;

    public function __construct(PricePlanService $pricePlanService)
    {
        $this->pricePlanService = $pricePlanService;
    }

    public function recommendCheapestPricePlans($smartMeterId, Request $request): JsonResponse
    {
        $limit = $request->query('limit');

        try {
            $recommendedPlans = $this->pricePlanService->getConsumptionCostOfElectricityReadingsForEachPricePlan($smartMeterId);
            $recommendedPlansAfterSorting = $this->sortPlans($recommendedPlans);

            if ($limit != null && $limit < count($recommendedPlans)) {
                $recommendedPlansAfterSorting = array_slice($recommendedPlansAfterSorting, 0, $limit);
            }

            return response()->json($recommendedPlansAfterSorting);

        } catch (InvalidMeterIdException $exception) {
            return response()->json($exception->getMessage());
        }
    }

    /**
     * @throws InvalidMeterIdException
     */
    public function calculatedCostForEachPricePlan($smartMeterId): JsonResponse
    {
        $costPricePerPlans = $this->pricePlanService->getCostPlanForAllSuppliersWithCurrentSupplierDetails($smartMeterId);
        return response()->json($costPricePerPlans);
    }


    private function sortPlans($recommendedPlans)
    {
        $sortedPlans = array_column($recommendedPlans, 'value');
        array_multisort($sortedPlans, SORT_ASC, $recommendedPlans);
        return $recommendedPlans;
    }
}
