<?php

namespace DT\Plugin\Providers;

use DT\Plugin\Illuminate\Container\Container;

abstract class ServiceProvider {
	protected $container;

	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Register services
	 */
	abstract public function register(): void;

	/**
	 * Do any setup after services have been registered and the theme is ready
	 */
	abstract public function boot(): void;
}
