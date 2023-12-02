<?php
function remove( $src ) {
    if ( is_dir( $src ) ) {
        $dir = opendir( $src );

        while ( false !== ( $file = readdir( $dir ) ) ) {
            if ( ( $file != '.' ) && ( $file != '..' ) ) {
                $full = $src . '/' . $file;
                if ( is_dir( $full ) ) {
                    remove( $full );
                } else {
                    unlink( $full );
                }
            }
        }

        closedir( $dir );
        rmdir( $src );
    } elseif ( is_file( $src ) ) {
        unlink( $src );
    }
}

function path( ...$parts ) {
    return join( DIRECTORY_SEPARATOR, $parts );
}

// define variables

$source        = '/home/codezone/Projects/discipletools/wp-content/plugins/dt-plugin/tmp-990b6857a9/source';
$destination   = '/home/codezone/Projects/discipletools/wp-content/plugins/dt-plugin/tmp-990b6857a9/destination';
$cwd           = '/home/codezone/Projects/discipletools/wp-content/plugins/dt-plugin';
$composer_lock = 'composer.scoped.lock';
$deps          = '/home/codezone/Projects/discipletools/wp-content/plugins/dt-plugin/vendor-scoped';
$temp          = '/home/codezone/Projects/discipletools/wp-content/plugins/dt-plugin/tmp-990b6857a9';
$prefix        = strtolower( preg_replace( "/[[a-zA-Z0-9]+]/", '', 'CZ' ) );

// fix static files autoloader
$autoload_static_path = path( $destination, 'vendor', 'composer', 'autoload_static.php' );
$autoload_static      = file_get_contents( $autoload_static_path );
$autoload_static      = preg_replace(
    "/'([[:alnum:]]+)'\s*=>\s*([a-zA-Z0-9 .'\"\/\-_]+),/",
    "'" . $prefix . "\\1' => \\2,",
    $autoload_static
);
file_put_contents( $autoload_static_path, $autoload_static );

// copy composer.lock

remove( path( $cwd, $composer_lock ) );
copy( path( $destination, 'composer.lock' ), path( $cwd, $composer_lock ) );

// copy deps folder

remove( $deps );
rename( path( $destination, 'vendor' ), $deps );

// remove temp folder

remove( $temp );
