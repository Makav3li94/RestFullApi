<?php

namespace App\Http\Controllers\buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BuyerCategoryController extends ApiController {
	public function index( Buyer $buyer ) {
    	$categories = $buyer
		    ->transactions()
		    ->with('product.categories')
		    ->get()
		    ->pluck('product.categories')
		    ->collapse()
		    ->unique('id')
		    ->values();
    	return $this->showAll($categories);
	}
}
