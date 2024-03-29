<?php
namespace Faid\tests;

use \Faid\Configure\Configure;
use \Faid\Configure\ConfigureException;

class ConfigureTest extends \Faid\tests\baseTest {
	public function setUp(): void {

	}

	/**
	 * 
	 */
	public function testGetUnknown() {
        $this->expectException(ConfigureException::class);
		Configure::read( 'xxxx' );
	}

	public function testSetAndEdit() {
		Configure::write( 'a', '1' );
		Configure::write( 'b', '2' );
		Configure::write( 'c', '3' );
		$this->assertEquals( '2', Configure::read( 'b' ) );
	}

	public function testSetAndEditArrays() {
		$data = array(
			'item1'  => 1,
			'item2'  => 2,
			'item3'  => 3,
			'level2' => array(
				'subitem'  => 3,
				'sublevel' => array( 1, 2, 3 )
			),
		);
		Configure::write( 'test', $data );
		$this->assertEquals( $data[ 'level2' ], Configure::read( 'test.level2' ) );
	}
}

?>
