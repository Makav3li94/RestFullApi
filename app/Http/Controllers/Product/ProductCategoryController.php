<?php

namespace App\Http\Controllers\Product;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index( Product $product ) {
		return $this->showAll( $product->categories);
	}

	public function update(Request $request, Product $product , Category $category){
		$product->categories()->syncWithoutDetaching([$category->id]);
		return $this->showAll($product->categories);
	}

	public function destroy(Product $product, Category $category){
		if (!$product->categories()->find($category->id)){
			return $this->errorResponse('This cat doesnt exists',404);
		}
		$product->categories()->detach($category->id);
		return $this->showAll($product->categories);
	}

}
