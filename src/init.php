<?php
namespace AdminTheme;
defined( 'ABSPATH' ) || exit;

$path = wp_normalize_path( plugin_dir_path( __DIR__ ) );



if ( ! class_exists( AdminTheme::class ) ) {
	require_once path_join( $path, 'vendor/autoload.php' );
}

function admin_theme() {
	return AdminTheme::instance();
}

admin_theme();