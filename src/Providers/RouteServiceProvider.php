<?php

namespace CZ\Plugin\Providers;

use CZ\FastRoute;

class RouteServiceProvider extends ServiceProvider {

	public function register(): void {
		// TODO: Implement register() method.
	}

	/**
	 * @return void
	 */
	public function boot(): void
	{
		$this->registerWebRoutes();
		$this->registerRestRoutes();
	}

	/**
	 * Register web-based routes
	 *
	 * @return void
	 */
	public function  registerWebRoutes(): void {
		$httpMethod = $_SERVER['REQUEST_METHOD'];
		$uri = $_SERVER['REQUEST_URI'];

		// Strip query string (?foo=bar) and decode URI
		if (false !== $pos = strpos($uri, '?')) {
			$uri = substr($uri, 0, $pos);
		}
		$uri = trim(rawurldecode($uri), '/');

		$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
			require_once __DIR__ . '/../../routes/web.php';
		});

		$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

		switch ($routeInfo[0]) {
			case FastRoute\Dispatcher::FOUND:
				$handler = $routeInfo[1];
				$vars = $routeInfo[2];
				[$class, $method] = explode("@", $handler, 2);
				call_user_func_array([$this->container->make($class), $method], $vars);
				break;
		}
	}

	/**
	 * @return void
	 */
	public function registerRestRoutes() {
		add_action( 'rest_api_init', function() {
			require_once __DIR__ . '/../../routes/rest.php';
		} );
	}
}