<?php

namespace test;
use WP_UnitTestCase;

class Cleanup extends WP_UnitTestCase {

	public function test_dummy() {
		$this->assertTrue( true );
	}

		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dt_activity_log" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dt_location_grid" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dt_notifications" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dt_reports" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dt_reportmeta" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dt_share" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dt_post_user_meta" );
	}
}
