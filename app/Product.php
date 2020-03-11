<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
	use SoftDeletes;

	protected $dates = [ 'deleted_at' ];
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

	protected $hidden = ['pivot'];

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
