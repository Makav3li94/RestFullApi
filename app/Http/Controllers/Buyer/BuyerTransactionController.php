<?php

namespace App\Http\Controllers\buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BuyerTransactionController extends ApiController
{
    public function index(Buyer $buyer){
    	return $this->showAll($buyer->transactions);
    }
}
