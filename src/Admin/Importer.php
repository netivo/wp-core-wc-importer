<?php

namespace Netivo\Module\WooCommerce\Importer\Admin;

use Netivo\Module\WooCommerce\Importer\Module;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

class Importer {

	public function __construct() {
		if ( ! empty( Module::get_config_array() ) ) {
			add_filter( 'woocommerce_product_export_column_names', [ $this, 'add_columns' ] );
			add_filter( 'woocommerce_product_export_product_default_columns', [ $this, 'add_columns' ] );

			foreach ( Module::get_config_array() as $column_key => $column_config ) {
				add_filter( 'woocommerce_product_export_product_column_' . $column_key, [
					$this,
					'add_export_custom_meta'
				], 10, 2 );
			}

			add_filter( 'woocommerce_csv_product_import_mapping_options', [ $this, 'add_columns' ] );
			add_filter( 'woocommerce_csv_product_import_mapping_default_columns', [
				$this,
				'add_column_to_mapping_screen'
			] );
			add_filter( 'woocommerce_product_import_pre_insert_product_object', [ $this, 'process_import' ], 10, 2 );
		}
	}

	protected function add_columns( $options ) {
		foreach ( Module::get_config_array() as $key => $config ) {
			$options[ $key ] = $config['label'];
		}

		return $options;
	}

	protected function add_export_custom_meta( $value, $product ) {
		$current_filter = current_filter();
		$column_key     = str_replace( 'woocommerce_product_export_product_column_', '', $current_filter );

		$custom_columns = Module::get_config_array();

		if ( isset( $custom_columns[ $column_key ] ) ) {
			return $product->get_meta( $custom_columns[ $column_key ]['meta_key'], true, 'edit' );
		}

		return $value;
	}

	function add_column_to_mapping_screen( $columns ) {
		foreach ( Module::get_config_array() as $key => $config ) {
			$columns[ $config['label'] ] = $key;
		}

		return $columns;
	}

	function process_import( $object, $data ) {
		foreach ( Module::get_config_array() as $key => $config ) {
			if ( ! empty( $data[ $key ] ) ) {
				$object->update_meta_data( $config['meta_key'], $data[ $key ] );
			}
		}

		return $object;
	}
}