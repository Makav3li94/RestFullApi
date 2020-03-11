<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use DemeterChain\C;
use Illuminate\Http\Request;

class CategoryTransactionController extends ApiController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index( Category $category ) {
		$transactions = $category
			->products()
			->whereHas('transactions')
			->with( 'transactions' )
			->get()
			->pluck( 'transactions' )
			->collapse()
			->values();

		return $this->showAll( $transactions );
	}

}
