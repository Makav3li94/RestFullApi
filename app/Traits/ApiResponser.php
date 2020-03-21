<?php

namespace App\Traits;


use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait ApiResponser {

	protected function successResponse( $data, $code ) {
		return response()->json( $data, $code );
	}

	protected function errorResponse( $message, $code ) {
		return response()->json( [ 'error' => $message, 'code' => $code ], $code );
	}

	protected function showAll( Collection $collection, $code = 200 ) {
		if ( $collection->isEmpty() ) {
			return $this->successResponse( [ 'data' => $collection ], $code );
		}
		$transformer = $collection->first()->transformer;

		$collection = $this->filterData( $collection, $transformer );
		$collection = $this->sortData( $collection, $transformer );
		$collection = $this->paginate( $collection );
		$collection = $this->transformData( $collection, $transformer );
		$collection = $this->cacheResponse( $collection );

		return response()->json( $collection, $code );
	}

	protected function showOne( Model $instance, $code = 200 ) {
		$transformer = $instance->transformer;
		$instance    = $this->transformData( $instance, $transformer );

		return response()->json( $instance, $code );
	}

	protected function showMessage( $message, $code = 200 ) {
		return response()->json( [ 'data' => $message ], $code );
	}

	protected function filterData( Collection $collection, $transformer ) {
		foreach ( request()->query() as $query => $value ) {
			$attribute = $transformer::originalAttribute( $query );

			if ( isset( $attribute, $value ) ) {
				$collection = $collection->where( $attribute, $value );
			}
		}

		return $collection;
	}

	protected function sortData( Collection $collection, $transformer ) {

		if ( request()->has( 'sort_by' ) ) {
			$attribute  = $transformer::originalAttribute( request()->sort_by );
			$collection = $collection->sortBy( $attribute );
		}

		return $collection;
	}

	protected function paginate( Collection $collection ) {
		$rules = [
			'per_page' => 'integer|min2|max50',
		];
		Validator::make( request()->all(), $rules );
		$page    = lengthAwarePaginator::resolveCurrentPage();
		$perPage = 15;
		if ( request()->has( 'per_page' ) ) {
			$perPage = request()->per_page;
		}

		$result    = $collection->slice( ( $page - 1 ) * $perPage, $perPage )->values();
		$paginated = new lengthAwarePaginator( $result, $collection->count(), $perPage, $page, [
			'path' => lengthAwarePaginator::resolveCurrentPath(),
		] );

		$paginated->appends( request()->all() );

		return $paginated;
	}

	protected function transformData( $data, $transformer ) {
		$transformation = fractal( $data, new $transformer );

		return $transformation->toArray();
	}

	protected function cacheResponse( $data ) {
		$url = request()->url();
		$query = request()->query();

		ksort($query);

		$queryString = http_build_query($query);

		$fullUrl = "{$url}?{$queryString}";

		return Cache::remember($fullUrl,30/60,function () use ($data){
			return $data;
		});
	}


}