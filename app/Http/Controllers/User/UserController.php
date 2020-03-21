<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Parent_;

class UserController extends ApiController {


	public function __construct() {
		Parent_::__construct();
		$this->middleware('transform.input'.UserTransformer::class)->only(['store','update']);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$users = User::all();

		return $this->showAll( $users );
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request ) {
		$rules = [
			'name'     => 'required',
			'email'    => 'required|email|unique:users',
			'password' => 'required|min:6|confirmed'
		];
		$this->validate( $request, $rules );
		$data                       = $request->all();
		$data['password']           = bcrypt( $request->password );
		$data['verified']           = User::NOTVERIFIED;
		$data['verification_token'] = User::generateVerificationToken();
		$data['admin']              = User::NOTADMIN;

		$user = User::create( $data );

		return $this->showOne( $user );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\User $user
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( User $user ) {
		return $this->showOne( $user );
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\User $user
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, User $user ) {
		$rules = [
			'email'    => 'email|unique:users,email,' . $user->id,
			'password' => 'min:6|confirmed',
			'admin'    => 'in:' . User::ISADMIN . ',' . User::NOTADMIN
		];
		$this->validate( $request, $rules );
		if ( $request->has( 'name' ) ) {
			$user->name = $request->name;
		}

		if ( $request->has( 'email' ) && $user->email != $request->email ) {
			$user->verfied             = User::NOTVERIFIED;
			$user->verificateion_token = User::generateVerificationToken();
			$user->email               = $request->email;
		}

		if ( $request->has( 'password' ) ) {
			$user->password = bcrypt( $request->password );
		}

		if ( $request->has( 'admin' ) ) {
			if ( ! $user->isVerified() ) {
				return $this->errorResponse( 'not verified user', 409 );
			}
			$user->admin = $request->admin;
		}

		if ( ! $user->isDirty() ) {
			return $this->errorResponse( 'nothing changed', 422 );
		}

		$user->save();

		return $this->showOne( $user );


	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\User $user
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( User $user ) {
		$user->delete();

		return response()->json( [ 'data', $user ], 200 );
	}


	public function verify($token){
		$user = User::where('verification_token',$token)->firstOrFail();
		$user->verified = User::ISVERIFIED;
		$user->verification_token = null;
		$user->save();
		return $this->showMessage('Account verified');
	}

	public function resend(User $user){
		if ($user->isVerified()){
			return $this->errorResponse( 'This user is verified', 409 );
		}
		Mail::to($user)->send(new UserCreated($user));
		return $this->showMessage('Email resend');
	}
}
