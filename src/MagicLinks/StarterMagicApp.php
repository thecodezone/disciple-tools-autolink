<?php

namespace DT\Plugin\MagicLinks;

use DT_Magic_Url_Base;

/**
 * Class DT_Plugin_Magic_User_App
 */
class StarterMagicApp extends DT_Magic_Url_Base {

	public $page_title = 'DT Plugin - Magic Links - Starter Magic App';
	public $page_description = 'Starter Magic App - Magic Links.';
	public $root = 'starter'; // @todo define the root of the url {yoursite}/root/type/key/action
	public $type = 'app'; // @todo define the type
	public $post_type = 'user';
	public $show_bulk_send = false;
	public $show_app_tile = false;
	public $meta = [];
	public $type_actions = [
		"subpage" => "subpage",
	];

	private $meta_key = '';

	public function __construct() {
		/**
		 * Specify metadata structure, specific to the processing of current
		 * magic link type.
		 *
		 * - meta:              Magic link plugin related data.
		 *      - app_type:     Flag indicating type to be processed by magic link plugin.
		 *      - post_type     Magic link type post type.
		 *      - contacts_only:    Boolean flag indicating how magic link type user assignments are to be handled within magic link plugin.
		 *                          If True, lookup field to be provided within plugin for contacts only searching.
		 *                          If false, Dropdown option to be provided for user, team or group selection.
		 *      - fields:       List of fields to be displayed within magic link frontend form.
		 */
		$this->meta = [
			'app_type'      => 'magic_link',
			'post_type'     => $this->post_type,
			'contacts_only' => false,
			'fields'        => [
				[
					'id'    => 'name',
					'label' => 'Name',
				],
			],
		];

		$this->meta_key = $this->root . '_' . $this->type . '_magic_key';
		parent::__construct();

		/**
		 * user_app and module section
		 */
		add_filter( 'dt_settings_apps_list', [ $this, 'dt_settings_apps_list' ], 10, 1 );

		/**
		 * tests if other URL
		 */
		$url = dt_get_url_path();
		if ( strpos( $url, $this->root . '/' . $this->type ) === false ) {
			return;
		}
		/**
		 * tests magic link parts are registered and have valid elements
		 */
		if ( ! $this->check_parts_match() ) {
			return;
		}

		// load if valid url
		add_filter( 'dt_magic_url_base_allowed_css', [ $this, 'dt_magic_url_base_allowed_css' ], 10, 1 );
		add_filter( 'dt_magic_url_base_allowed_js', [ $this, 'dt_magic_url_base_allowed_js' ], 10, 1 );
	}

	public function dt_magic_url_base_allowed_js( $allowed_js ) {
		// @todo add or remove js files with this filter
		return $allowed_js;
	}

	public function dt_magic_url_base_allowed_css( $allowed_css ) {
		// @todo add or remove js files with this filter
		return $allowed_css;
	}

	/**
	 * Builds magic link type settings payload:
	 * - key:               Unique magic link type key; which is usually composed of root, type and _magic_key suffix.
	 * - url_base:          URL path information to map with parent magic link type.
	 * - label:             Magic link type name.
	 * - description:       Magic link type description.
	 * - settings_display:  Boolean flag which determines if magic link type is to be listed within frontend user profile settings.
	 *
	 * @param $apps_list
	 *
	 * @return mixed
	 */
	public function dt_settings_apps_list( $apps_list ) {
		$apps_list[ $this->meta_key ] = [
			'key'              => $this->meta_key,
			'url_base'         => $this->root . '/' . $this->type,
			'label'            => $this->page_title,
			'description'      => $this->page_description,
			'settings_display' => true,
		];

		return $apps_list;
	}
}
