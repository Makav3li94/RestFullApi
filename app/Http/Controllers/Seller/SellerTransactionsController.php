<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Seller;
use Illuminate\Http\Request;

class SellerTransactionsController extends ApiController {
	public function index( Seller $seller ) {
		$transactions = $seller
			->products()
			->whereHas( 'transactions' )
			->with( 'transactions' )
			->get()
			->pluck('transactions')
			->collapse()
			->values();

		return $this->showAll( $transactions );
	}
}
