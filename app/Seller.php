<?php

namespace App;


use App\Scopes\SellerScope;

class Seller extends User {
	public function products() {
		return $this->hasMany( Product::class );
	}

	public static function boot() {
		parent::boot();
		static::addGlobalScope( new SellerScope );
	}
}
