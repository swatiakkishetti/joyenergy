<?php

namespace App\Http\Controllers;

use App\Services\MeterReadingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeterReadingController extends Controller
{

    private $meterReadingService;

    public function __construct(MeterReadingService $meterReadingService)
    {
        $this->meterReadingService = $meterReadingService;
    }

    public function getReading($smartMeterId): JsonResponse
    {
        return response()->json($this->meterReadingService->getReadings($smartMeterId));
    }

    public function storeReadings(Request $request): JsonResponse
    {
        $this->meterReadingService->storeReadings($request->all()["smartMeterId"], $request->all()["electricityReadings"]);

        return response()->json("Readings inserted sucessfully", 201);

    }
}
