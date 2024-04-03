<?php

namespace DT\Autolink\Middleware;

use DT\Autolink\CodeZone\Router\Middleware\Middleware;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Repositories\SurveyRepository;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;
use function DT\Autolink\redirect;
use function DT\Autolink\route_url;

class SurveyCompleted implements Middleware {

	public function __construct( private SurveyRepository $survey_repository ){}

	public function handle( Request $request, Response $response, callable $next ){
		$survey = $this->survey_repository->questions();
		$user          = wp_get_current_user();
		$contact_id    = \Disciple_Tools_Users::get_contact_for_user( $user->ID, true );

		foreach ( $survey as $page => $question ) {
			$question_name = $question['name'];
			$answer = get_post_meta( $contact_id, $question_name, true );
		    if ( ! $answer ) {
				return $next( $request, redirect( route_url( 'survey/' . $page ) ) );
		    }
		}

		return $next( $request, $response );
	}
}
