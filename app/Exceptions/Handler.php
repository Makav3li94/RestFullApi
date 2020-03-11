<?php

namespace App\Exceptions;

use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler {
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	/**
	 * Report or log an exception.
	 *
	 * @param \Exception $exception
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public function report( Exception $exception ) {
		parent::report( $exception );
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Exception $exception
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Exception
	 */
	public function render( $request, Exception $exception ) {
		if ( $exception instanceof ValidationException ) {
			return $this->convertValidationExceptionToResponse( $exception, $request );
		}

		if ( $exception instanceof AuthenticationException ) {
			return $this->unauthenticated( $exception, $request );
		}
		if ( $exception instanceof AuthorizationException ) {
			return $this->errorResponse( $exception->getMessage(), 403 );
		}
		if ( $exception instanceof NotFoundHttpException ) {
			return $this->errorResponse( 'The url doesnt not found', 404 );
		}
		if ( $exception instanceof MethodNotAllowedHttpException ) {
			return $this->errorResponse( 'The method is invalid', 405 );
		}
		if ( $exception instanceof HttpException ) {
			return $this->errorResponse( $exception->getMessage(), $exception->getStatusCode() );
		}
		if ( $exception instanceof QueryException ) {
			$errorCode = $exception->errorInfo[1];
			if ( $errorCode == 1451 ) {
				return $this->errorResponse( "error code {$errorCode}", 409 );
			}
		}
		if ( $exception instanceof ModelNotFoundException ) {
			$modelName = strtolower( class_basename( $exception->getModel() ) );

			return $this->errorResponse( "does not exist any {$modelName} with the identifier", 404 );
		}

		if ( config( 'app.debug' ) ) {
			return parent::render( $request, $exception );
		}

		return $this->errorResponse( 'Unexpected Exception', 500 );


	}
}
