<?php declare( strict_types=1 );

use Isolated\Symfony\Component\Finder\Finder;

$config = require_once __DIR__ . '/scoper.config.php';

$project_customizations = __DIR__ . '/scoper.custom.php';

if ( file_exists( $project_customizations ) ) {
	require_once $project_customizations;
}

if ( ! function_exists( 'customize_php_scoper_config' ) ) {
	function customize_php_scoper_config( array $config = [] ) {
		return $config;
	}
}

$prefix      = $config['prefix'];
$source      = $config['source'];
$destination = $config['destination'];

return customize_php_scoper_config( [
	'prefix'                  => $prefix,
	'finders'                 => [
		Finder::create()
		      ->files()
		      ->ignoreVCS( true )
		      ->in( $source . DIRECTORY_SEPARATOR . 'vendor' ),
		Finder::create()
            ->append( [
			      $source . '/composer.json',
			      $source . '/composer.lock'
            ] ),
	],
	'patchers'                => [
		function ( string $file_path, string $prefix, string $content ) use ( $config ): string {
			if ( strpos( $file_path, 'guzzlehttp/guzzle/src/Handler/CurlFactory.php' ) !== false ) {
				$content = str_replace( 'stream_for($sink)', 'Utils::streamFor()', $content );
			}

			if ( strpos( $file_path, 'php-di/php-di/src/Compiler/Template.php' ) !== false ) {
				$content = str_replace( "namespace $prefix;", '', $content );
			}

			if ( strpos( $file_path, 'yahnis-elsts/plugin-update-checker/Puc/v4p11/UpdateChecker.php' ) !== false ) {
				$content = str_replace( "namespace $prefix;", "namespace $prefix;\n\nuse WP_Error;", $content );
			}

			if ( strpos( $file_path, 'twig/src/Node/ModuleNode.php' ) !== false ) {
				$content = str_replace( 'write("use Twig', 'write("use ' . $prefix . '\\\\Twig', $content );
				$content = str_replace( 'Template;\\n\\n', 'Template;\\n\\n use function ' . $prefix . '\\\\twig_escape_filter; \\n\\n', $content );
			}

			usort( $config['expose-classes'], function ( $a, $b ) {
				return strlen( $b ) - strlen( $a );
			} );

			$count        = 0;
			$searches     = [];
			$replacements = [];

			foreach ( $config['expose-classes'] as $symbol ) {
				$searches[]     = "\\$prefix\\$symbol";
				$replacements[] = "\\$symbol";

				$searches[]     = "use $prefix\\$symbol";
				$replacements[] = "use $symbol";
			}

			$content = str_replace( $searches, $replacements, $content, $count );

			return $content;
		},
	],
	'expose-global-constants' => false,
	'expose-global-classes'   => false,
	'expose-global-functions' => false,
] );
