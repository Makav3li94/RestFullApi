<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {

	const ISADMIN = true;
	const NOTADMIN = false;

	const ISVERIFIED = '1';
	const NOTVERIFIED = '0';

	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
		'verified',
		'verification_token',
		'admin'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
		'verification_token'
	];

	protected $table = 'users';

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	public function setNameAttribute( $name ) {
		$this->attributes['name'] = strtolower( $name );
	}

	public function getNameAttribute( $name ) {
		return ucwords( $name );
	}

	public function setEmailAttribute( $email ) {
		$this->attributes['email'] = strtolower( $email );
	}

	public function isAdmin() {
		return $this->admin == self::ISADMIN;
	}

	public function isVerified() {
		return $this->verified == self::ISVERIFIED;
	}

	public static function generateVerificationToken() {
		return str_random( 40 );
	}
}
