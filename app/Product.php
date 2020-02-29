<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

	const UNAVAILABLE_PRODUCT = 'unavailable';
	const AVAILABLE_PRODUCT = 'available';

	protected $fillable = [
		'name',
		'description',
		'price',
		'quantity',
		'status',
		'image',
		'seller_id'
	];

	public function isAvailable() {
		return $this->status == Product::AVAILABLE_PRODUCT;
	}

	public function categories() {
		return $this->belongsToMany( Category::class );
	}

	public function seller() {
		return $this->belongsTo( Seller::class );
	}

	public function transactions() {
		return $this->hasMany(Transaction::class);
	}

}
