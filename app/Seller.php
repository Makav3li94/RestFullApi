<?php

namespace App;


use App\Scopes\SellerScope;
use App\Transformers\SellerTrasnformer;

class Seller extends User {
	public $transformer = SellerTrasnformer::class;
	public function products() {
		return $this->hasMany( Product::class );
	}

	public static function boot() {
		parent::boot();
		static::addGlobalScope( new SellerScope );
	}
}
