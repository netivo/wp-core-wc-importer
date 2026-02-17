<?php

namespace Netivo\Module\WooCommerce\Importer;

use Netivo\Module\WooCommerce\Importer\Admin\Importer;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

class Module {

	protected static ?self $instance = null;
	protected array $config = array();

	/**
	 * Retrieves the absolute path to the module directory.
	 *
	 * @return false|string|null Returns the absolute path to the module directory if it exists,
	 *                           null if the file does not exist, or false on failure.
	 */
	public static function get_module_path(): false|string|null {
		$file = realpath( __DIR__ . '/../' );
		if ( file_exists( $file ) ) {
			return $file;
		}

		return null;
	}

	public static function get_instance(): self {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function get_config_array(): array {
		return self::get_instance()->get_config();
	}

	public function __construct() {
		if ( is_admin() ) {
			$this->init_config();
			new Importer();
		}
	}

	public function get_config(): array {
		return $this->config;
	}

	protected function init_config(): void {
		if ( file_exists( get_stylesheet_directory() . '/config/importer.config.php' ) ) {
			$this->config = include get_stylesheet_directory() . '/config/importer.config.php';
		}
	}
}