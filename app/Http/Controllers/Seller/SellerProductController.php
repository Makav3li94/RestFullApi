<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Product;
use App\Seller;
use App\Transformers\ProductTrasnformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Parent_;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController {

	public function __construct() {
		Parent_::__construct();
		$this->middleware('transform.input'.ProductTrasnformer::class)->only(['store','update']);
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index( Seller $seller ) {
		return $this->showAll( $seller->products );
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request, User $seller ) {
		$rules = [
			'name'        => 'required',
			'description' => 'required',
			'quantity'    => 'required|integer|min:1',
			'price'       => 'required|integer|min:1',
			'image'       => 'required|image',
		];
		$this->validate( $request, $rules );

		$data              = $request->all();
		$data['status']    = Product::UNAVAILABLE_PRODUCT;
		$data['image']     = $request->image->store( '' );
		$data['seller_id'] = $seller->id;
		$product           = Product::create( $data );

		return $this->showOne( $product );

	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Seller $seller
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, Seller $seller, Product $product ) {
		$rules = [
			'quantity' => 'integer|min:1',
			'status'   => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,
			'image'    => 'image',
		];
		$this->validate( $request, $rules );
		$this->checkSeller( $seller, $product );

		$product->fill( $request->only(
			[
				'name',
				'description',
				'quantity'
			]
		) );
		if ( $request->has( 'status' ) ) {
			$product->status = $request->status;
			if ( $product->isAvailable() && $product->categories()->count() == 0 ) {
				return $this->errorResponse( 'Product must have atleast on cat', 409 );
			}
		}

		if ( $request->hasFile( 'image' ) ) {
			Storage::delete( $product->image );
			$product->image = $request->image->store( '' );
		}

		if ( $product->isClean() ) {
			return $this->errorResponse( 'No value is changed', 422 );
		}
		$product->save();

		return $this->showOne( $product );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Seller $seller
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( Seller $seller, Product $product ) {
		$this->checkSeller( $seller, $product );

		Storage::delete( $product->image );

		$product->delete();

		return $this->showOne( $product );
	}


	public function checkSeller( $seller, $product ) {
		if ( $seller->id != $product->seller_id ) {
			throw new HttpException( "Seller isnt the actual seller of product", 422 );
		}
	}
}