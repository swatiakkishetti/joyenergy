<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricePlan extends Model
{
    public $supplier;
    public $unitrate;
    public $peaktimemultipler = [];

    public function __construct($supplier, $unitrate)
    {
        $this->supplier = $supplier;
        $this->unitrate = $unitrate;
    }

    public function GetPrice($datetime)
    {

    }
}

class PeakTimeMultiplier extends Model
{
    public $dayofweek;
    public $multiplier;
}
