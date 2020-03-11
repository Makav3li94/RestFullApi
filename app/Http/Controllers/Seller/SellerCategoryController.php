<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Seller;
use Illuminate\Http\Request;

class SellerCategoryController extends ApiController {
	public function index( Seller $seller ) {
		$categories = $seller->products()
		                     ->whereHas( 'categories' )
		                     ->with( 'categories' )
		                     ->get()
		                     ->pluck( 'categories' )
		                     ->collapse()
		                     ->values();

		return $this->showAll( $categories );
	}
}
