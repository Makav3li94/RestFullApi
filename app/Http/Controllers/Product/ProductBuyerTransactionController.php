<?php

namespace App\Http\Controllers\Product;

use App\Buyer;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Product;
use App\Transaction;
use App\Transformers\TransactionTrasnformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController {

	public function __construct() {
		Parent_::__construct();
		$this->middleware('transform.input'.TransactionTrasnformer::class)->only(['store']);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request, Product $product, User $buyer, Transaction $transaction ) {
		$rules = [
			'quantity' => 'required|integer|min:1'
		];

		if ( $buyer->id == $product->seller_id ) {
			return $this->errorResponse( 'The buyer should be different from seller', 409 );
		}

		if ( ! $buyer->isVerified() ) {
			return $this->errorResponse( 'The buyer should be verified', 409 );
		}

		if ( ! $product->seller->isVerified() ) {
			return $this->errorResponse( 'The buyer should be verified', 409 );
		}

		if ( ! $product->isAvailable() ) {
			return $this->errorResponse( 'The product is no available', 409 );
		}

		if ( $product->quantity < $request->quantity ) {
			return $this->errorResponse( 'The product has not enough quantity', 409 );
		}

		return DB::transaction( function () use ( $request, $product, $buyer ) {
			$product->quantity -= $request->quantity;
			$product->save();
			$tansaction = Transaction::create( [
				'quantity'   => $request->quantity,
				'buyer_id'   => $buyer->id,
				'product_id' => $product->id
			] );
			return $this->showOne($tansaction,201);
		} );

	}

}
