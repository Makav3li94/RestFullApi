<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class BuyerController extends ApiController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$buyers = Buyer::has( 'transactions' )->get();

		return $this->showAll( $buyers );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function show( Buyer $buyer ) {
		return $this->showOne( $buyer );
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Buyer $buyer
	 *
	 * @return \Illuminate\Http\Response
	 */

}
