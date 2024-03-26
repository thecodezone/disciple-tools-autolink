<?php

namespace DT\Autolink;

use CodeZone\Router;
use DT\Autolink\Illuminate\Http\RedirectResponse;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Illuminate\Support\Str;
use DT\Autolink\League\Plates\Engine;
use DT\Autolink\Services\Template;
use DT\Autolink\Services\Options;
use DT_Magic_URL;
use Exception;

/**
 * Returns the singleton instance of the Plugin class.
 *
 * @return Plugin The singleton instance of the Plugin class.
 */
function plugin(): Plugin {
	return Plugin::$instance;
}

/**
 * Returns the container object.
 *
 * @return Illuminate\Container\Container The container object.
 */
function container(): Illuminate\Container\Container {
	return plugin()->container;
}

/**
 * Retrieves the URL of a file or directory within the Bible Plugin directory.
 *
 * @param string $path Optional. The path of the file or directory within the Bible Plugin directory. Defaults to empty string.
 *
 * @return string The URL of the specified file or directory within the Bible Plugin directory.
 */
function plugin_url( string $path = '' ): string {
	return plugins_url( 'disciple-tools-autolink' ) . '/' . ltrim( $path, '/' );
}

function route_url( string $path = '' ): string {
	return site_url( Plugin::HOME_ROUTE . '/' . ltrim( $path, '/' ));
}

/**
 * Returns the path of a plugin file or directory, relative to the plugin directory.
 *
 * @param string $path The path of the file or directory relative to the plugin directory. Defaults to an empty string.
 *
 * @return string The full path of the file or directory, relative to the plugin directory.
 */
function plugin_path( string $path = '' ): string {
	return '/' . implode( '/', [
			trim( Str::remove( '/src', plugin_dir_path( __FILE__ ) ), '/' ),
			trim( $path, '/' ),
    ] );
}

/**
 * Get the source path using the given path.
 *
 * @param string $path The path to append to the source directory.
 *
 * @return string The complete source path.
 */
function src_path( string $path = '' ): string {
	return plugin_path( 'src/' . $path );
}

/**
 * Returns the path to the resources directory.
 *
 * @param string $path Optional. Subdirectory path to append to the resources directory.
 *
 * @return string The path to the resources directory, with optional subdirectory appended.
 */
function resources_path( string $path = '' ): string {
	return plugin_path( 'resources/' . $path );
}

/**
 * Returns the path to the routes directory.
 *
 * @param string $path Optional. Subdirectory path to append to the routes directory.
 *
 * @return string The path to the routes directory, with optional subdirectory appended.
 */
function routes_path( string $path = '' ): string {
	return plugin_path( 'routes/' . $path );
}

/**
 * Returns the path to the views directory.
 *
 * @param string $path Optional. Subdirectory path to append to the views directory.
 *
 * @return string The path to the views directory, with optional subdirectory appended.
 */
function views_path( string $path = '' ): string {
	return plugin_path( 'resources/views/' . $path );
}

/**
 * Renders a view using the provided view engine.
 *
 * @param string $view Optional. The name of the view to render. Defaults to an empty string.
 * @param array $args Optional. An array of data to pass to the view. Defaults to an empty array.
 *
 * @return string|Engine The rendered view if a view name is provided, otherwise the view engine object.
 */
function view( string $view = "", array $args = [] ): string|Engine {
	$engine = container()->make( Engine::class );
	if ( ! $view ) {
		return $engine;
	}

	return $engine->render( $view, $args );
}

/**
 * Renders a template using the Template service.
 *
 * @param string $template Optional. The template to render. If not specified, the Template service instance is returned.
 * @param array $args Optional. An array of arguments to be passed to the template.
 *
 * @return mixed If $template is not specified, an instance of the Template service is returned.
 *               If $template is specified, the rendered template is returned.
 */
function template( string $template = "", array $args = [] ): mixed {
	$service = container()->make( Template::class );
	if ( ! $template ) {
		return $service;
	}

	return $service->render( $template, $args );
}

/**
 * Returns the Request object.
 *
 * @return Request The Request object.
 */
function request(): Request {
	return container()->make( Request::class );
}

/**
 * Creates a new RedirectResponse instance for the given URL.
 *
 * @param string $url The URL to redirect to.
 * @param int $status Optional. The status code for the redirect response. Default is 302.
 *
 * @return RedirectResponse A new RedirectResponse instance.
 */
function redirect( string $url, int $status = 302 ): RedirectResponse {
	return container()->makeWith( RedirectResponse::class, [
		'url'    => $url,
		'status' => $status,
	] );
}

/**
 * Validate the given data using the provided rules and messages.
 *
 * @param array $data The data to be validated.
 * @param array $rules The validation rules to be applied.
 * @param array $messages The custom error messages to be displayed.
 *
 * @return array The array of validation error messages, if any.
 */
function validate( array $data, array $rules, array $messages = [] ): array {
	$validator = container()->make( Factory::class )->make( $data, $rules, $messages );
	if ( $validator->fails() ) {
		return $validator->errors()->toArray();
	}

	return [];
}

/**
 * Set the value of an option.
 *
 * This function first checks if the option already exists. If it doesn't, it adds a new option with the given name and value.
 * If the option already exists, it updates the existing option with the given value.
 *
 * @param string $option_name The name of the option.
 * @param mixed $value The value to set for the option.
 *
 * @return bool Returns true if the option was successfully set, false otherwise.
 */
function set_option( string $option_name, mixed $value ): bool {
	if ( get_option( $option_name ) === false ) {
		return add_option( $option_name, $value );
	} else {
		return update_option( $option_name, $value );
	}
}

/**
 * Start a database transaction and execute a callback function within the transaction.
 *
 * @param callable $callback The callback function to execute within the transaction.
 *
 * @return bool|string Returns true if the transaction is successful, otherwise returns the last database error.
 *
 * @throws Exception If there is a database error before starting the transaction.
 */
function transaction( $callback ): bool|string {
	global $wpdb;
	if ( $wpdb->last_error ) {
		return $wpdb->last_error;
	}
	$wpdb->query( 'START TRANSACTION' );
	$callback();
	if ( $wpdb->last_error ) {
		$wpdb->query( 'ROLLBACK' );

		return $wpdb->last_error;
	}
	$wpdb->query( 'COMMIT' );

	return true;
}

/**
 * Retrieves the HTTPFactory instance.
 *
 * @return HTTPFactory The HTTPFactory instance.
 */
function http(): HTTPFactory {
	return container()->make( HTTPFactory::class );
}

/**
 * Concatenates the given string to the namespace of the Router class.
 *
 * @param string $shelperstring The string to be concatenated to the namespace.
 *
 * @return string The result of concatenating the given string to the namespace of the Router class.
 */
function namespace_string( string $string ) {
	return Plugin::class . '\\' . $string;
}

/**
 * Retrieves the value of an option from the options container.
 *
 * @param string $option The name of the option to retrieve.
 * @param mixed $default Optional. The default value to return if the option does not exist. Defaults to false.
 *
 * @return mixed The value of the option if it exists, or the default value if it doesn't.
 */
function get_plugin_option( $option, $default = null, $required = false ) {
	$options = container()->make( Options::class );

	return $options->get( $option, $default, $required );
}

/**
 * Sets the value of a plugin option.
 *
 * @param string $option The name of the option to set.
 * @param mixed $value The value to set for the option.
 *
 * @return bool true if the option was successfully set; otherwise, false.
 */

function set_plugin_option( $option, $value ): bool {
	$options = container()->make( Options::class );

	return $options->set( $option, $value );
}

/**
 * Generates a magic URL using the DT_Magic_URL class.
 *
 * @param string $action Optional. The action to be performed by the magic URL. If not specified, an empty string is used.
 * @param string $key Optional. The key used for the magic URL. If not specified, the key is retrieved from the user's options.
 *
 * @return string The generated magic URL.
 *               If the key is not specified and could not be retrieved from the user's options, 'settings' is returned.
 */
function share_url() {
	$current_user_id = get_current_user_id();
	$record          = \DT_Posts::get_post( 'contacts', \Disciple_Tools_Users::get_contact_for_user( $current_user_id ), true, false );
	$meta_key        = 'autolink_coached_by_magic_key';
	if ( isset( $record[ $meta_key ] ) ) {
		$key = $record[ $meta_key ];
	} else {
		$key = dt_create_unique_key();
		update_post_meta( get_the_ID(), $meta_key, $key );
	}

	return DT_Magic_URL::get_link_url_for_post(
		'contacts',
		$record['ID'],
		'autolink',
		'coached_by'
	);
}

function logo_url() {
	$logo_url        = plugin_url( 'resources/img/logo-color.png' );
	$custom_logo_url = get_option( 'custom_logo_url' );
	if ( ! empty( $custom_logo_url ) ) {
		$logo_url = $custom_logo_url;
	}

	return $logo_url;
}

function group_labels() {
	$post_type = get_post_type_object( 'groups' );
	return get_post_type_labels( $post_type );
}

function groups_label() {
	return group_labels()->name;
}

function group_label() {
	return group_labels()->singular_name;
}
