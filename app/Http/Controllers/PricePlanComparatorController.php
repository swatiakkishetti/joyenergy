<?php

namespace App\Http\Controllers;

use App\Services\PricePlanService;
use Symfony\Component\HttpFoundation\Request;

class PricePlanComparatorController extends Controller
{
    private $pricePlanService;

    public function __construct(PricePlanService $pricePlanService)
    {
        $this->pricePlanService = $pricePlanService;
    }

    public function recommendCheapestPricePlans($smartMeterId,Request $request): \Illuminate\Http\JsonResponse
    {
        $limit= $request->query('limit');

        $recommendedPlans = $this->pricePlanService->getConsumptionCostOfElectricityReadingsForEachPricePlan($smartMeterId);
        $recommendedPlansAfterSorting = $this->sortPlans($recommendedPlans);

        if ($limit != null && $limit < count($recommendedPlans)) {
            $recommendedPlansAfterSorting = array_slice($recommendedPlansAfterSorting, 0, $limit);
        }
        return response()->json($recommendedPlansAfterSorting, 200);
    }

    public function calculatedCostForEachPricePlan($smartMeterId)
    {
        $costPricePerPlans = $this->pricePlanService->getCostPlanForAllSuppliersWithCurrentSupplierDetails($smartMeterId);
        return response()->json($costPricePerPlans, 200);
    }


    private function sortPlans($recommendedPlans)
    {
        $sortedPlans = array_column($recommendedPlans, 'value');
        array_multisort($sortedPlans, SORT_ASC, $recommendedPlans);
        return $recommendedPlans;
    }
}
