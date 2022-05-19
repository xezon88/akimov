<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Underpin, and its dependencies.
$autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

require_once( $autoload );

require_once(__DIR__ . '/vendor/underpin/debug-bar-extension/debug-bar.php');