<?php

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', sys_get_temp_dir() );
}

if ( ! function_exists( 'is_admin' ) ) {
	function is_admin( $set = null ) {
		static $is_admin = true;
		if ( $set !== null ) {
			$is_admin = $set;
		}

		return $is_admin;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $tag, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

if ( ! function_exists( 'current_filter' ) ) {
	function current_filter() {
		return 'woocommerce_product_export_product_column_test_key';
	}
}

if ( ! function_exists( 'get_stylesheet_directory' ) ) {
	function get_stylesheet_directory() {
		return dirname( __DIR__ ) . '/tests/mock_theme';
	}
}
