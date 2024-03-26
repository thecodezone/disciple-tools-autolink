<?php

namespace DT\Autolink\Services;

use DT\Autolink\CodeZone\Router;
use DT\Autolink\Illuminate\Http\Response;
use function DT\Autolink\view;

class Template {
	public function __construct( private Assets $assets ) {}

	/**
	 * Allow access to blank template
	 * @return bool
	 */
	public function blank_access(): bool {
		return true;
	}


	/**
	 * Render the header
	 * @return void
	 */
	public function header() {
		wp_head();
	}

	/**
	 * Render the template
	 *
	 * @param $template
	 * @param $data
	 *
	 * @return mixed
	 */
	public function render( $template, $data ) {
		add_action( Router\namespace_string( 'render' ), [ $this, 'render_response' ], 10, 2 );
		add_filter( 'dt_blank_access', [ $this, 'blank_access' ], 11 );
		add_action( 'dt_blank_head', [ $this, 'header' ], 11 );
		add_action( 'dt_blank_footer', [ $this, 'footer' ], 11 );
		$this->assets->enqueue();

		return view()->render( $template, $data );
	}

	public function render_response( Response $response ) {
		if ( apply_filters( 'dt_blank_access', false ) ) {
			add_action( 'dt_blank_body', function () use ( $response ) {
				// phpcs:ignore
				echo $response->getContent();
			}, 11 );
		} else {
			$response->send();
		}
	}

	/**
	 * Render the footer
	 * @return void
	 */
	public function footer() {
		wp_footer();
	}
}
