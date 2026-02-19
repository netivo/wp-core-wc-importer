<?php

namespace Netivo\Module\WooCommerce\Importer\Test\Admin;

use PHPUnit\Framework\TestCase;
use Netivo\Module\WooCommerce\Importer\Admin\Importer;
use Netivo\Module\WooCommerce\Importer\Module;
use ReflectionClass;
use ReflectionMethod;

class ImporterTest extends TestCase {

	protected function setUp(): void {
		$reflection = new ReflectionClass( Module::class );
		$instance   = $reflection->getProperty( 'instance' );
		$instance->setAccessible( true );
		$instance->setValue( null, null );
		is_admin( true );
	}

	public function test_constructor_with_config(): void {
		// Module is initialized and config is loaded from mock_theme/config/importer.config.php
		$importer = new Importer();
		$this->assertInstanceOf( Importer::class, $importer );
		// Since we cannot easily verify add_filter calls in global scope without a framework,
		// we assume it passes if it doesn't throw.
	}

	public function test_add_columns(): void {
		$importer = new Importer();
		$method   = new ReflectionMethod( Importer::class, 'add_columns' );
		$method->setAccessible( true );

		$options = [ 'existing' => 'Existing Label' ];
		$result  = $method->invoke( $importer, $options );

		$this->assertArrayHasKey( 'existing', $result );
		$this->assertArrayHasKey( 'test_key', $result );
		$this->assertEquals( 'Test Label', $result['test_key'] );
	}

	public function test_add_export_custom_meta(): void {
		$importer = new Importer();
		$method   = new ReflectionMethod( Importer::class, 'add_export_custom_meta' );
		$method->setAccessible( true );

		$product = $this->createMock( \stdClass::class );
		// Need to mock get_meta method
		$product = new class {
			public function get_meta( $key, $single, $context ) {
				if ( $key === '_test_meta_key' ) {
					return 'meta_value';
				}

				return null;
			}
		};

		$value  = 'original_value';
		$result = $method->invoke( $importer, $value, $product );

		// bootstrap.php current_filter() returns 'woocommerce_product_export_product_column_test_key'
		$this->assertEquals( 'meta_value', $result );
	}

	public function test_add_column_to_mapping_screen(): void {
		$importer = new Importer();
		$columns  = [ 'Old' => 'old_key' ];
		$result   = $importer->add_column_to_mapping_screen( $columns );

		$this->assertArrayHasKey( 'Old', $result );
		$this->assertArrayHasKey( 'Test Label', $result );
		$this->assertEquals( 'test_key', $result['Test Label'] );
	}

	public function test_process_import(): void {
		$importer = new Importer();
		$object   = new class {
			public $meta = [];

			public function update_meta_data( $key, $value ) {
				$this->meta[ $key ] = $value;
			}
		};

		$data   = [ 'test_key' => 'imported_value' ];
		$result = $importer->process_import( $object, $data );

		$this->assertEquals( 'imported_value', $result->meta['_test_meta_key'] );
	}
}
