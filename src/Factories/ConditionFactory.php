<?php

namespace DT\Plugin\Factories;

use ArrayAccess;
use DT\Plugin\Conditions\CallbackCondition;
use DT\Plugin\Conditions\Condition;
use DT\Plugin\Illuminate\Container\Container;
use DT\Plugin\Illuminate\Support\Collection;

class ConditionFactory {
	protected Container $container;

	public function __construct( Container $container ) {
		$this->container = $container;
	}

	public function make( $condition ): Condition|Collection {
		if ( ! $condition ) {
			return new CallbackCondition( function () {
				return true;
			} );
		}

		if ( $condition instanceof Condition ) {
			return $condition;
		}

		if ( is_callable( $condition ) ) {
			return new CallbackCondition( $condition );
		}

		if ( is_string( $condition ) ) {
			return $this->container->make( $condition );
		}

		if ( is_array( $condition ) || $condition instanceof ArrayAccess ) {
			return Collection::make( $condition )->each( function ( $condition ) {
				$this->make( $condition );
			} );
		}

		return new CallbackCondition( function () {
			return false;
		} );
	}
}