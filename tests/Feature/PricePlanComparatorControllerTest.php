<?php

namespace Tests\Feature;

use App\Repository\ElectricityReadingRepository;
use App\Repository\PricePlanRepository;
use App\Services\MeterReadingService;
use App\Services\PricePlanService;
use stdClass;
use Tests\TestCase;

class PricePlanComparatorControllerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionWhenNoReadingsAvailable()
    {
        $response = $this->get('price-plans/recommend/smart-meter-70?limit=4')->exception->getMessage();
        self::assertEquals("No readings available", $response);
    }

    /**
     * @test
     */

    public function shouldReturnRecommendedPlans()
    {
        $response = $this->get('price-plans/recommend/smart-meter-1?limit=1');
        $response->assertStatus(200);
    }

}
