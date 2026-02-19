<?php

namespace Netivo\Module\WooCommerce\Importer\Test;

use PHPUnit\Framework\TestCase;
use Netivo\Module\WooCommerce\Importer\Module;
use ReflectionClass;

class ModuleTest extends TestCase {

	protected function setUp(): void {
		// Reset singleton instance
		$reflection = new ReflectionClass( Module::class );
		$instance   = $reflection->getProperty( 'instance' );
		$instance->setAccessible( true );
		$instance->setValue( null, null );

		is_admin( true );
	}

	public function test_get_instance(): void {
		$instance1 = Module::get_instance();
		$instance2 = Module::get_instance();

		$this->assertInstanceOf( Module::class, $instance1 );
		$this->assertSame( $instance1, $instance2 );
	}

	public function test_get_module_path(): void {
		$path = Module::get_module_path();
		$this->assertStringEndsWith( 'wp-core-wc-importer', $path );
		$this->assertTrue( file_exists( $path ) );
	}

	public function test_init_config_loads_from_mock_theme(): void {
		$module = Module::get_instance();
		$config = $module->get_config();

		$this->assertArrayHasKey( 'test_key', $config );
		$this->assertEquals( 'Test Label', $config['test_key']['label'] );
	}

	public function test_get_config_array_static(): void {
		$config = Module::get_config_array();
		$this->assertArrayHasKey( 'test_key', $config );
	}

	public function test_constructor_without_admin(): void {
		is_admin( false );
		$module = new Module();
		$this->assertEmpty( $module->get_config() );
	}
}
