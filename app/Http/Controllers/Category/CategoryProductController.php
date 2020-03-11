<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryProductController extends ApiController
{
    public function index(Category $category){
    	return $this->showAll($category->products);
    }
}
