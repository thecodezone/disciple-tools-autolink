<?php

namespace DT\Autolink\Repositories;

/**
 * The UserRepository class provides methods for retrieving user information from the database.
 */
class UserRepository {
	/**
	 * Retrieves the display name of a user based on the given user ID or the current user's ID.
	 *
	 * @param int|null $id The ID of the user to retrieve the display name for. Defaults to null,
	 *                     which will retrieve the display name for the current user.
	 *
	 * @return string Returns the display name of the user.
	 */
	public function display_name( $id = null ) {
		if ( ! $id ) {
			$id = get_current_user_id();
		}

		return dt_get_user_display_name( $id );
	}
	/**
	 * Retrieves the contact for a given user ID.
	 *
	 * If no user ID is provided, it will retrieve the contact for the current user.
	 *
	 * @param int|null $id The user ID. Default is null.
	 *
	 * @return mixed The contact object for the specified user.
	 */
	public function contact( $id = null ) {
		if ( !$id ) {
			$id = get_current_user_id();
		}
		$contact_id = \Disciple_Tools_Users::get_contact_for_user( $id );

        $contact = \DT_Posts::get_post( 'contacts', $contact_id );
        if ( empty( $contact ) || is_wp_error( $contact ) ) {
            return [];
        }

        return $contact;
	}


	/**
	 * Get the ID of the coach for a given contact.
	 *
	 * @param int|null $id (optional) The ID of the contact. Default is NULL.
	 *
	 * @return int|null The ID of the coach for the contact if found, NULL otherwise.
	 */
	public function coached_by( $id = null ) {
		$contact = $this->contact( $id );
		return $contact["coached_by"] ?? null;
	}

	/**
	 * Retrieves the coach's contact information based on the given ID or the current coach's ID.
	 *
	 * @param int|null $id The ID of the coach to retrieve contact information for. Defaults to null,
	 *                     which will retrieve the contact information for the current coach.
	 *
	 * @return array|null Returns an array containing the coach's contact information, or null if no coach is found.
	 */
	public function coach( $id = null ) {
		return $this->coached_by( $id )[0] ?? null;
	}

	/**
	 * Retrieves the name of the coach based on the given ID or the current coach's ID.
	 *
	 * @param int|null $id The ID of the coach to retrieve the name for. Defaults to null,
	 *                     which will retrieve the name of the current coach.
	 *
	 * @return string Returns a string containing the name of the coach. If no coach is found,
	 *                an empty string will be returned.
	 */
	public function coach_name( $id = null ) {
		$coach = $this->coach( $id );
		return $coach["name"] ?? "";
	}

	/**
	 * Retrieves the list of groups assigned to the given user ID or the current user ID.
	 *
	 * @param int|null $id The ID of the user to retrieve the assigned groups for. Defaults to null,
	 *                     which will retrieve the assigned groups for the current user.
	 *
	 * @return array Returns an array containing the list of assigned groups for the given user ID,
	 *               or an empty array if no groups are assigned.
	 */
	public function groups( $id = null ) {
		if ( !$id ) {
			$id = get_current_user_id();
		}
		return \DT_Posts::list_posts( 'groups', [
			'assigned_to' => [ $id ],
			'orderby'     => 'modified',
			'order'       => 'DESC',
		], false );
	}
}
