<?php

namespace Tests\Feature;

use Tests\TestCase;

class PricePlanComparatorControllerTest extends TestCase
{

    public function test_RecommendCheapestPricePlans()
    {
        $response = $this->get('price-plans/recommend/{smartMeterId}/{limit?}',['smartMeterId'=>"smart-meter-1", 'limit' => 5]);

        $response->assertStatus(200);
    }
}
