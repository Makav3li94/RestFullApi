<?php

namespace App\Http\Controllers\buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BuyerProductController extends ApiController
{
    public function index(Buyer $buyer){
		$product = $buyer->transactions()->with('product')->get()->pluck('product ');
		return $this->showOne($product);
    }
}
