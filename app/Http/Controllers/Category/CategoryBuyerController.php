<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryBuyerController extends ApiController
{
    public function index(Category $category){
    	$buyers = $category
		    ->products()
		    ->whereHas('transactions.buyer')
		    ->with('transactions.buyer')
		    ->get()
		    ->pluck('transactions')
		    ->collapse()
		    ->pluck('buyer')
		    ->unique('id')
		    ->values();

    	return $this->showAll($buyers);

    }
}
