<?php

namespace DT\Autolink\Middleware;

use DT\Autolink\CodeZone\Router\Middleware\Middleware;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Psr\Http\Message\ResponseInterface;
use DT\Autolink\Psr\Http\Message\ServerRequestInterface;
use DT\Autolink\Psr\Http\Server\MiddlewareInterface;
use DT\Autolink\Psr\Http\Server\RequestHandlerInterface;
use DT\Autolink\Repositories\SurveyRepository;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;
use function DT\Autolink\container;
use function DT\Autolink\redirect;
use function DT\Autolink\route_url;

class SurveyCompleted implements MiddlewareInterface
{

	public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface
	{
		$survey = container()->get( SurveyRepository::class )->questions();
		$user          = wp_get_current_user();
		$contact_id    = \Disciple_Tools_Users::get_contact_for_user( $user->ID, true );

		foreach ( $survey as $page => $question ) {
			$question_name = $question['name'];
			$answer = get_post_meta( $contact_id, $question_name, true );
		    if ( ! $answer ) {
				return redirect( route_url( 'survey/' . $page ) );
		    }
		}

		return $handler->handle( $request );
	}
}
