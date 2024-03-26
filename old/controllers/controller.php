<?php

abstract class Disciple_Tools_Autolink_Controller {
	public static $_instance;
	public $functions;
	public $settings;

	public function __construct() {
		$this->settings  = new Disciple_Tools_Autolink_Settings();
		$this->functions = Disciple_Tools_Autolink_Magic_Functions::instance();
	}

	/**
	 * Global data is shared between many views in the app.
	 */
	public function global_data() {
		$data         = [];

		$data['group_fields']         = DT_Posts::get_post_field_settings( 'groups' );
		$data['create_group_link']    = $this->functions->get_create_group_url();
		$data['edit_group_link']      = $this->functions->get_edit_group_url();
		$data['contact']              = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );
		$data['coach']                = null;
		$data['coach_name']           = '';
		$data['view_church_label']    = __( 'View', 'disciple-tools-autolink' ) . ' controller.php' . $group_labels->singular_name;
		$data['churches']             = [];
		$data['church_health_label']  = $group_labels->singular_name . ' ' . __( 'Health', 'disciple-tools-autolink' );
		$data['tree_label']           = __( 'Tree', 'disciple-tools-autolink' );
		$data['genmap_label']         = __( 'GenMap', 'disciple-tools-autolink' );
		$data['training-label']       = __( 'Training', 'disciple-tools-autolink' );
		$data['training-link']        = $this->functions->get_training_url();
		$data['church_label']         = $group_labels->singular_name;
		$data['churches_label']       = $group_labels->name;

		return $data;
	}
}
