<?php

namespace DT\Autolink;

use DT\Autolink\CodeZone\WPSupport\Config\ConfigInterface;
use DT\Autolink\CodeZone\WPSupport\Container\ContainerFactory;
use DT\Autolink\CodeZone\WPSupport\Options\OptionsInterface;
use DT\Autolink\CodeZone\WPSupport\Rewrites\RewritesInterface;
use DT\Autolink\CodeZone\WPSupport\Router\ResponseFactory;
use DT\Autolink\League\Container\Container;
use DT\Autolink\League\Plates\Engine;
use DT\Autolink\Psr\Http\Message\RequestInterface;
use DT\Autolink\Psr\Http\Message\ResponseInterface;
use DT\Autolink\Psr\Http\Message\ServerRequestInterface;
use DT\Autolink\Services\Template;
use DT_Magic_URL;
use Exception;

/**
 * @var $container Container
 */

/**
 * Returns the singleton instance of the Plugin class.
 *
 * @return Plugin The singleton instance of the Plugin class.
 */
function plugin(): Plugin {
	return container()->get( Plugin::class );
}

/**
 * Return the container instance.
 *
 * @return Container The container instance.
 * @see https://container.thephpleague.com/4.x/
 */
function container(): Container {
	return ContainerFactory::singleton();
}

/**
 * Returns the ConfigInterface object or the value of a specific configuration key.
 * If a key is provided, the method will return the value of the specified key from the ConfigInterface object.
 * If no key is provided, the method will return the ConfigInterface object itself.
 *
 * @param string|null $key (optional) The configuration key to retrieve the value for.
 *
 * @return mixed The ConfigInterface object if no key is provided, or the value of the specified configuration key.
 * @see https://config.thephpleague.com/
 */
function config( $key = null, $default = null ) {
	$service = container()->get( ConfigInterface::class );

	if ( $key ) {
		return $service->get( $key, $default );
	}

	return $service;
}

function set_config( $key, $value ) {
	$service = container()->get( ConfigInterface::class );

	return $service->set( $key, $value );
}

/**
 * Checks if the route rewrite rule exists in the WordPress rewrite rules.
 *
 * @return bool Whether the route rewrite rule exists in the rewrite rules.
 * @global WP_Rewrite $wp_rewrite The main WordPress rewrite rules object.
 *
 */
function has_route_rewrite(): bool {
	$rewrites = container()->get( RewritesInterface::class );

	return $rewrites->exists(
		array_key_first( config()->get( 'routes.rewrites' ) )
	);
}

/**
 * Retrieves the URL of a file or directory within the plugin directory.
 *
 * @param string $path Optional. The path of the file or directory within the Bible Plugin directory. Defaults to empty string.
 *
 * @return string The URL of the specified file or directory within the Bible Plugin directory.
 */
function plugin_url( string $path = '' ): string {
	return plugins_url( 'dt-autolink' ) . '/' . ltrim( $path, '/' );
}

/**
 * Returns the URL for a given route.
 *
 * @param string $path The path of the route. Defaults to an empty string.
 * @param string $key The key of the route file in the configuration. Defaults to 'web'.
 *
 * @return string The URL for the given route.
 */
function route_url( string $path = '', $key = 'web' ): string {
	$file = config()->get( 'routes.files' )[ $key ];

	if ( ! has_route_rewrite() ) {
		return site_url() . '?' . http_build_query( [ $file['query'] => $path ] );
	} else {
		return site_url( $file['path'] . '/' . ltrim( $path, '/' ) );
	}
}

/**
 * Returns the URL of an API endpoint based on the given path.
 *
 * @param string $path The path of the API endpoint.
 *
 * @return string The URL of the API endpoint.
 *
 * @see route_url()
 */
function api_url( string $path ) {
	return route_url( $path, 'api' );
}

/**
 * Returns the URL for a given web path.
 *
 * @param string $path The web path to generate the URL for.
 *
 * @return string The generated URL.
 */
function web_url( string $path ) {
	return route_url( $path, 'web' );
}

/**
 * Returns the path of a plugin file or directory, relative to the plugin directory.
 *
 * @param string $path The path of the file or directory relative to the plugin directory. Defaults to an empty string.
 *
 * @return string The full path of the file or directory, relative to the plugin directory.
 * @see https://developer.wordpress.org/reference/functions/plugin_dir_path/
 */
function plugin_path( string $path = '' ): string {
	return Plugin::dir_path() . '/' . trim( $path, '/' );
}

/**
 * Get the source path using the given path.
 *
 * @param string $path The path to append to the source directory.
 *
 * @return string The complete source path.
 */
function src_path( string $path = '' ): string {
	return plugin_path( config( 'plugin.paths.src' ) . '/' . $path );
}

/**
 * Returns the path to the resources directory.
 *
 * @param string $path Optional. Subdirectory path to append to the resources directory.
 *
 * @return string The path to the resources directory, with optional subdirectory appended.
 */
function resources_path( string $path = '' ): string {
	return plugin_path( config( 'plugin.paths.resources' ) . '/' . $path );
}

/**
 * Returns the path to the routes directory.
 *
 * @param string $path Optional. Subdirectory path to append to the routes directory.
 *
 * @return string The path to the routes directory, with optional subdirectory appended.
 */
function routes_path( string $path = '' ): string {
	return plugin_path( config( 'plugin.paths.routes' ) . '/' . $path );
}

/**
 * Returns the path to the views directory.
 *
 * @param string $path Optional. Subdirectory path to append to the views directory.
 *
 * @return string The path to the views directory, with optional subdirectory appended.
 */
function views_path( string $path = '' ): string {
	return plugin_path( config( 'plugin.paths.views' ) . '/' . $path );
}

/**
 * Renders a view and returns a response.
 *
 * @param string $view Optional. The name of the view to render. Defaults to an empty string.
 * @param array $args Optional. An array of data to pass to the view. Defaults to an empty array.
 *
 * @return ResponseInterface The rendered view if a view name is provided, otherwise the view engine object.
 * @see https://platesphp.com/v3/
 */
function view( string $view = "", array $args = [] ) {
	$engine = container()->get( Engine::class );
	if ( ! $view ) {
		return $engine;
	}

	return response(
		$engine->render( $view, $args )
	);
}

/**
 * Renders a template using the Template service and returns a response
 *
 * @param string $template Optional. The template to render. If not specified, the Template service instance is returned.
 * @param array $args Optional. An array of arguments to be passed to the template.
 *
 * @return mixed If $template is not specified, an instance of the Template service is returned.
 *               If $template is specified, the rendered template is returned.
 */
function template( string $template = "", array $args = [] ) {
	$service = container()->get( Template::class );
	if ( ! $template ) {
		return $service;
	}

	$service->register();

	return view( $template, $args );
}

/**
 * Returns the Request object.
 * @see https://github.com/guzzle/psr7
 */
function request(): ServerRequestInterface {
	return container()->get( ServerRequestInterface::class );
}

/**
 * Creates a new ResponseInterface instance for the given URL.
 *
 * @param string $url The URL to redirect to.
 * @param int $status Optional. The status code for the redirect response. Default is 302.
 *
 * @return ResponseInterface A new RedirectResponse instance.
 * @see https://github.com/guzzle/psr7
 */
function redirect( string $url, int $status = 302, $headers = [] ): ResponseInterface {
	return ResponseFactory::redirect( $url, $status, $headers );
}

/**
 * Returns a response object.
 *
 * @param mixed $content The content of the response. Can be an array or a string.
 * @param int $status Optional. The HTTP status code of the response. Default is 200.
 * @param array $headers Optional. Additional headers to include in the response. Default is an empty array.
 *
 * @return ResponseInterface The response object with the specified content, status, and headers.
 * @see https://github.com/guzzle/psr7
 */
function response( $content, $status = 200, $headers = [] ) {
	return ResponseFactory::make( $content, $status, $headers );
}

/**
 * Set the value of an option.
 *
 * This is a convenience function that checks if the option exists before setting it.
 *
 * @param string $option_name The name of the option.
 * @param mixed $value The value to set for the option.
 *
 * @return bool Returns true if the option was successfully set, false otherwise.
 * @see https://developer.wordpress.org/reference/functions/add_option/
 * @see https://developer.wordpress.org/reference/functions/update_option/
 */
function set_option( string $option_name, $value ): bool {
	if ( get_option( $option_name ) === false ) {
		return add_option( $option_name, $value );
	} else {
		return update_option( $option_name, $value );
	}
}

/**
 * Retrieves the value of an option taking the default value set in the options service provider.
 *
 * @param string $option The name of the option to retrieve.
 * @param mixed $default Optional. The default value to return if the option does not exist. Defaults to false.
 *
 * @return mixed The value of the option if it exists, or the default value if it doesn't.
 */
function get_plugin_option( $option, $default = null, $required = false ) {
	$options = container()->get( OptionsInterface::class );

	return $options->get( $option, $default, $required );
}

/**
 * Sets the value of a plugin option.
 *
 * @param mixed $option The option to set.
 * @param mixed $value The value to set for the option.
 *
 * @return bool True if the option value was successfully set, false otherwise.
 */
function set_plugin_option( $option, $value ): bool {
	$options = container()->get( OptionsInterface::class );

	return $options->set( $option, $value );
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
function transaction( $callback ) {
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
 * Concatenates the given string to the namespace of the Plugin class.
 *
 * @param string $string The string to be concatenated to the namespace.
 *
 * @return string The result of concatenating the given string to the namespace of the Router class.
 */
function namespace_string( string $string ): string {
	return config( 'plugin.text_domain' ) . '.' . $string;
}

/**
 * Returns the registered magic apps for a specific root and type.
 *
 * @param string $root The root of the magic apps.
 * @param string $type The type of the magic app.
 *
 * @return array|bool The registered magic apps for the given root and type.
 *                  Returns an array if found, otherwise returns false.
 */
function magic_app( $root, $type ) {
	$magic_apps = apply_filters( 'dt_magic_url_register_types', [] );
	$root_apps  = $magic_apps[ $root ] ?? [];

	return $root_apps[ $type ] ?? false;
}

/**
 * Generates a magic URL for a given root, type, and ID.
 *
 * @param string $root The root of the magic URL.
 * @param string $type The type of the magic URL.
 * @param int $id The ID of the post to generate the magic URL for.
 *
 * @return string The generated magic URL.
 */
function magic_url( $root, $type, $id ): string {
	$app = magic_app( $root, $type );
	if ( ! $app ) {
		return "";
	}
	if ( $app['post_type'] === 'user' ) {
		$app_user_key = get_user_option( $app['meta_key'] );
		$app_url_base = trailingslashit( trailingslashit( site_url() ) . $app['url_base'] );

		return $app_url_base . $app_user_key;
	} else {
		return DT_Magic_URL::get_link_url_for_post(
			$app["post_type"],
			$id,
			$app["root"],
			$app["type"]
		);
	}
}

/**
 * Extracts data from a request and returns it as an array.
 *
 * Works with JSON requests, GET requests
 *
 * @param RequestInterface $request The request object from which to
 */
function extract_request_input( RequestInterface $request ): array {
    $content_type = $request->getHeaderLine( 'Content-Type' );

    if ( strpos( $content_type, 'application/json' ) !== false ) {
        // Handle JSON content type.
        $body = $request->getBody()->getContents();

        return json_decode( $body, true );
    }

    switch ( strtoupper( $request->getMethod() ) ) {
        case 'GET':
            return $request->getQueryParams();
        default:
            return $request->getParsedBody();
    }
}

/**
 * Extracts data from a request and returns it as an array.
 *
 * Works with JSON requests, GET requests
 *
 * @param RequestInterface $request The request object from which to
 */
function group_labels()
{
    $post_type = get_post_type_object( 'groups' );
    return get_post_type_labels( $post_type );
}

/**
 * Returns the label of the groups.
 *
 * @return string The label of the groups.
 */
function groups_label()
{
    return group_labels()->name;
}

/**
 * Returns the singular name of the group label.
 *
 * @return string The singular name of the group label.
 */
function group_label()
{
    return group_labels()->singular_name;
}