<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Http\Response;
use DT\Autolink\Repositories\SurveyRepository;
use function DT\Autolink\redirect;
use function DT\Autolink\route_url;
use function DT\Autolink\template;

/**
 * Class SurveyController
 */
class SurveyController {
	/**
	 * Retrieves the survey question to be displayed on the current page.
	 *
	 * @param Request $request The request object containing the page number.
	 * @param Response $response The response object.
	 *
	 * @return mixed The template for displaying the survey question.
	 * @throws Exception When the survey question is not found.
	 */
	public function show( Request $request, Response $response, SurveyRepository $survey_repository, $page = 0 ) {
		$prev_page = $page - 1;
		$question = $survey_repository->get( $page );
		if ( ! $question ) {
			return redirect( route_url( 'survey' ) );
		}
		$answer       = get_user_meta( get_current_user_id(), $question['name'], true );
		$answer       = $answer ?? 0;
		$action       = route_url( 'survey/' . $page );
		$previous_url = $prev_page > 0 ? route_url( 'survey/' . $prev_page ) : null;
		$progress     = $survey_repository->progress( $page );
		return template( 'survey', compact( 'question', 'answer', 'action', 'previous_url', 'progress' ) );
	}

	/**
	 * Process the user's answer to a survey question.
	 *
	 * @param Request $request The request object containing the user's answer.
	 * @param Response $response The response object.
	 *
	 * @return mixed The appropriate redirect after processing the answer.
	 * @throws Exception When the survey question is not found.
	 */
	public function update( Request $request, Response $response, SurveyRepository $survey_repository, $page) {
		$question      = $survey_repository->get( $page );
		$next_page     = $page + 1;
		$question_name = $question['name'];
		$user          = wp_get_current_user();
		$contact_id    = \Disciple_Tools_Users::get_contact_for_user( $user->ID, true );

		if ( ! $question ) {
			return redirect( route_url( 'survey' ) );
		}

		$answer = $request->input( $question_name );

		if ( !$answer ) {
			return redirect( route_url( 'survey/' . $page ) );
		}

		update_post_meta( $contact_id, $question_name, $answer );

		dt_activity_insert(
			[
				'action' => "comment",
				'object_type' => 'contacts',
				'object_id' => $contact_id,
				"object_subtype" => "contact",
				'object_name' => $question['label'],
				"meta_key" => $question['name'],
				"meta_value" => $answer,
				'object_note' => "Survey question '{$question['label']}' answered as " . $answer,
				"field_type" => "text"
			]
		);

		if ( $survey_repository->get( $next_page ) ) {
			return redirect( route_url( 'survey/' . $next_page ) );
		}

		return redirect( route_url() );
	}
}