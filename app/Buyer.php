<?php

namespace App;


use App\Scopes\BuyerScope;
use App\Transformers\BuyerTrasnformer;

class Buyer extends User {

	public $transformer = BuyerTrasnformer::class;
	protected static function boot() {
		parent::boot();
		static::addGlobalScope(new BuyerScope);
	}

	public function transactions() {
		return $this->hasMany( Transaction::class );
	}

}
