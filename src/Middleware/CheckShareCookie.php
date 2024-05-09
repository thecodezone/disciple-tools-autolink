<?php

namespace DT\Autolink\Middleware;

use Disciple_Tools_Users;
use DT\Autolink\CodeZone\Router\Middleware\Middleware;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;
use DT_Posts;
use Exception;
use function DT\Autolink\namespace_string;

/**
 * Class CheckShareCookie
 *
 * This class implements the Middleware interface and is responsible for checking
 * the value of the "dt_autolink_share" cookie and perform the necessary actions.
 */
class CheckShareCookie implements Middleware {
	/**
	 * Handle the incoming request.
	 *
	 * @param Request $request The incoming request
	 * @param Response $response The response object
	 * @param callable $next The next handler in the middleware stack
	 *
	 * @return mixed The result of the next handler
	 */
	public function handle( Request $request, Response $response, callable $next ) {

		if ( ! is_user_logged_in() ) {
			return $next( $request, $response );
		}
		$leader_id = $request->cookies->get( namespace_string( 'coached_by' ) );
        $group_id = $request->cookies->get( namespace_string( 'leads_group' ) );

		if ( $leader_id ) {
			try {
				$this->add_leader( $leader_id );
			} catch ( Exception $e ) {
				$this->remove_cookie();
			}
		}

        if ( $group_id ) {

            try {

                $this->add_as_group_leader( $group_id );
            } catch ( Exception $e ) {
                $this->remove_cookie();
            }
        }

		return $next( $request, $response );
	}

	/**
	 * Add a leader to a contact's coached_by field and update assigned_to field.
	 *
	 * @param int $leader_id The ID of the leader to be added.
	 *
	 * @return void
	 */
	public function add_leader( $leader_id ) {
		if ( ! $leader_id ) {
			return;
		}

		$contact = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );

		if ( $leader_id == $contact ) {
			$this->remove_cookie();
			return;
		}

		$contact_record = DT_Posts::get_post( 'contacts', $contact, true, false );
		$leader         = DT_Posts::get_post( 'contacts', $leader_id, true, false );

		if ( ! $contact_record || ! $leader ) {
			$this->remove_cookie();
		}

		if ( ! count( $contact_record['coached_by'] ) ) {
			$fields = [
				"coached_by"  => [
					"values"       => [
						[ "value" => (string) $leader_id ],
					],
					"force_values" => false
				],
				'assigned_to' => (string) $leader['corresponds_to_user']
			];

			DT_Posts::update_post( 'contacts', $contact, $fields, true, false );
		}

		$this->remove_cookie();
	}

	/**
	 * Removes the 'dt_autolink_share' cookie if it exists.
	 *
	 * @return void
	 */
	public function remove_cookie() {
		$cookie_name = namespace_string( 'coached_by' );
		if ( isset( $_COOKIE[$cookie_name] ) ) {
			unset( $_COOKIE[$cookie_name] );
			setcookie( $cookie_name, '', time() - 3600, '/' );
		}

        $cookie_name = namespace_string( 'leads_group' );
        if ( isset( $_COOKIE[$cookie_name] ) ) {
            unset( $_COOKIE[$cookie_name] );
            setcookie( $cookie_name, '', time() - 3600, '/' );

        }
    }


    public function add_as_group_leader( $group_id ) {

        // Get the current user's contact information
        $current_user_contact = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );

        // Update the group with the new leader
        $fields = [
            "leaders" => [
                "force_values" => false,
                "values" => [
                    [ 'value' => $current_user_contact ]
                ]
            ],
        ];
        $group = DT_Posts::update_post( 'groups', (int) $group_id, $fields, false, false );

        $this->remove_cookie();
    }
}
