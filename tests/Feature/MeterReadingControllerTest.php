<?php

namespace Tests\Feature;

use Tests\TestCase;

class MeterReadingControllerTest extends TestCase
{
    public function test_getReadings()
    {
        $response = $this->get('/readings/read/{smartMeterId}');

        $response->assertStatus(200);
    }

    public function test_storeReadings()
    {
        $response = $this->post('/readings/store', ['smartMeterId' => 'smart-meter-1', 'supplier' => 'The Green Eco',
            'electricityReadings' => [['time' => '6:00', 'reading' => 0.5656]]]);

        $response->assertStatus(201);
    }
}
