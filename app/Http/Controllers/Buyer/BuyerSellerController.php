<?php

namespace App\Http\Controllers\buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BuyerSellerController extends ApiController {
	public function index( Buyer $buyer ) {
		$sellers = $buyer
			->transactions()
			->with( 'product.seller' )
			->get()
			->pluck( 'product.seller' )
			->unique('id')
			->values();

    	return $this->showAll( $sellers );

    }
}
