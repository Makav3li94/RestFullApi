<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionSellerController extends ApiController {
	public function index( Transaction $transaction ) {
		$seller = $transaction->product->seller;

		return $this->showAll( $seller );
	}
}
