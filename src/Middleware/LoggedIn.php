<?php

namespace DT\Autolink\Middleware;

use DT\Autolink\Psr\Http\Message\ResponseInterface;
use DT\Autolink\Psr\Http\Message\ServerRequestInterface;
use DT\Autolink\Psr\Http\Server\MiddlewareInterface;
use DT\Autolink\Psr\Http\Server\RequestHandlerInterface;
use function DT\Autolink\redirect;

class LoggedIn implements MiddlewareInterface {
	public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface
	{
		if ( ! is_user_logged_in() ) {
			return redirect( wp_login_url( $request->getUri() ) );
		}

		return $handler->handle( $request );
	}
}
