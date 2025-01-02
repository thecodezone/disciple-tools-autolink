<?php

namespace DT\Autolink\Middleware;

use DT\Autolink\Psr\Http\Message\ResponseInterface;
use DT\Autolink\Psr\Http\Message\ServerRequestInterface;
use DT\Autolink\Psr\Http\Server\MiddlewareInterface;
use DT\Autolink\Psr\Http\Server\RequestHandlerInterface;
use function DT\Autolink\config;

class CheckShareCookie implements MiddlewareInterface {

 /**
     * If the user is not logged in, the request handler is directly called and the response is returned.
     * If the 'dt_home_share' cookie exists, sanitize and assign its value to $leader_id, otherwise set $leader_id to null.
     * If $leader_id is not null, attempt to add the leader with the given ID.
     * If an exception occurs during adding the leader, remove the 'dt_home_share' cookie.
     * Finally, call the request handler and return the response.
     *
     * @param ServerRequestInterface $request The request object.
     * @param RequestHandlerInterface $handler The request handler object.
     *
     * @return ResponseInterface The response object.
     */
    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        if ( ! is_user_logged_in() ) {
            return $handler->handle( $request );
        }

	    $leader_id = sanitize_text_field( wp_unslash( $_COOKIE[ config( 'plugin.cookies.coached_by' ) ] ?? '' ) );
	    $group_id = sanitize_text_field( wp_unslash( $_COOKIE[ config( 'plugin.cookies.leads_group' ) ] ?? '' ) );

        if ( $leader_id ) {
            try {
                $this->add_leader( $leader_id );
            } catch ( \Exception $e ) {
                $this->remove_cookie();
            }
        }

	    if ( $group_id ) {
		    try {
			    $this->add_as_group_leader( $group_id );
		    } catch ( \Exception $e ) {
			    $this->remove_cookie();
		    }
	    }

        return $handler->handle( $request );
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
		$cookie_name = config( 'plugin.cookies.coached_by' );
		if ( isset( $_COOKIE[$cookie_name] ) ) {
			unset( $_COOKIE[$cookie_name] );
			setcookie( $cookie_name, '', time() - 3600, '/' );
		}

        $cookie_name = config( 'plugin.cookies.leads_group' );
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
        DT_Posts::update_post( 'groups', (int) $group_id, $fields, false, false );

        $this->remove_cookie();
    }
}
