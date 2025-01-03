<?php

namespace Tests;

use WP_UnitTestCase;
use Faker;
abstract class TestCase extends WP_UnitTestCase {
	/**
	 * The Faker instance.
	 *
	 * @var Faker\Generator
	 */
	protected Faker\Generator $faker;

	/**
	 * Constructs a new instance of the class.
	 *
	 * @param string|null $name The name of the test case.
	 * @param array $data An array of test data.
	 * @param mixed|string $data_nme Additional data parameter (name).
	 */
	public function __construct( ?string $name = null, array $data = [], $data_nme = '' ) {
		$this->faker = \Faker\Factory::create();
		parent::__construct( $name, $data, $data_nme );
	}

	/**
	 * Sets up the test environment before executing each test method.
	 *
	 * @return void
	 */
	public function setUp(): void {
		global $wpdb;
		wp_logout();
		wp_clear_auth_cookie();
		$wpdb->query( 'START TRANSACTION' );
		parent::setUp();
	}

	/**
	 * The tearDown method is used to clean up any resources or connections after each test case is executed.
	 * In this specific case, it performs a rollback in the database using the global $wpdb variable of WordPress.
	 * It then calls the tearDown method of the parent class to ensure any additional cleanup tasks are performed.
	 * @return void
	 */
	public function tearDown(): void {
		global $wpdb;
		$wpdb->query( 'ROLLBACK' );
		wp_logout();
		wp_clear_auth_cookie();
		parent::tearDown();
	}

	public function as_user() {
		$user = wp_create_user( $this->faker->userName, $this->faker->password, $this->faker->email );
		$this->acting_as( $user );
		return $user;
	}

	public function acting_as( $user_id ) {
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );
	}
}
