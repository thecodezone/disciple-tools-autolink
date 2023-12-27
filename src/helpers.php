<?php

namespace DT\Plugin;

use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Illuminate\Support\Str;
use DT\Plugin\League\Plates\Engine;
use DT\Plugin\Services\Template;

function plugin() {
	return Plugin::$instance;
}

function container() {
	return plugin()->container;
}

function plugin_path( $path = '' ) {
	return '/' . implode( '/', [
			trim( Str::remove( '/src', plugin_dir_path( __FILE__ ) ), '/' ),
			trim( $path, '/' ),
		] );
}

function src_path( $path = '' ) {
	return plugin_path( 'src/' . $path );
}

function resources_path( $path = '' ) {
	return plugin_path( 'resources/' . $path );
}

function routes_path( $path = '' ) {
	return plugin_path( 'routes/' . $path );
}

function views_path( $path = '' ) {
	return plugin_path( 'resources/views/' . $path );
}

function view( $view = "", $args = [] ) {
	$engine = container()->make( Engine::class );
	if ( ! $view ) {
		return $engine;
	}

	return $engine->render( $view, $args );
}

function template( $template = "", $args = [] ) {
	$service = container()->make( Template::class );
	if ( ! $template ) {
		return $service;
	}

	return $service->render( $template, $args );
}

function request() {
	return container()->make( Request::class );
}

function is_json( $string ) {
	if ( ! is_string( $string ) ) {
		return false;
	}
	json_decode( $string );

	return json_last_error() === JSON_ERROR_NONE;
}
