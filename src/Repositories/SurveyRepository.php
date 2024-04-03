<?php

namespace DT\Autolink\Repositories;

use DT\Autolink\Illuminate\Support\Arr;
use function DT\Autolink\groups_label;

/**
 * Class SurveyRepository
 */
class SurveyRepository {
	public function questions() {
		$survey = apply_filters( 'dt_autolink_survey', [
			[
				'name'  => 'dt_autolink_number_of_leaders_coached',
				'label' => __( 'How many leaders are you coaching?', 'disciple-tools-autolink' )
			],
			[
				'name'  => 'dt_autolink_number_of_churches_led',
				'label' => __( 'How many', 'disciple-tools-autolink' ) . ' ' . strtolower( groups_label() ) . ' ' . __( 'are you leading?', 'disciple-tools-autolink' ),

			]
		] );
		if ( ! is_array( $survey ) ) {
			return [];
		}

		return $survey;
	}

	/**
	 * Retrieves the value of a specific page from the questions array.
	 *
	 * @param int|string $page The page number or key of the questions array to retrieve.
	 *
	 * @return mixed The value of the requested page from the questions array, or an empty array if the page is not found.
	 */
	public function get( $page ) {
		return Arr::get( $this->questions(), $page, [] );
	}

	/**
	 * Calculates the progress percentage based on the provided page number.
	 *
	 * @param int $page The current page number.
	 *
	 * @return string The progress percentage as a formatted string, e.g., '50%'.
	 */
	public function progress( $page ) {
		$progress     = $page / count( $this->questions() );
		return number_format( $progress * 100, 0 ) . '%';
	}
}
