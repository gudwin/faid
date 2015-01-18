<?php
namespace Faid\tests\Validators;

use \Faid\Validators\FileInSecuredFolder;
class FileInSecuredFolderTest extends \Faid\tests\baseTest {
	const TmpFolder = '/tmp/';
	public function setUp() {
		parent::setUp();
	}
	public function teardDown() {

	}

	/**
	 * @expectedException \Faid\Validators\Exception
	 */
	public function testUnknownBaseFolder() {
		new FileInSecuredFolder( 'unknown');
	}
	public function testNotValid() {
		$validator = new FileInSecuredFolder( __DIR__ . self::TmpFolder );

		$assertations = array(
			'/unknown',
			'/../'. __FILE__
		);
		foreach ( $assertations as $row ) {
			$path = __DIR__ . $row;
			$this->AssertFalse( $validator->isValid( $path ) );
		}
	}
	public function testValid() {
		$validator = new FileInSecuredFolder( __DIR__ . self::TmpFolder );
		$this->assertTrue( $validator->isValid( __DIR__ . self::TmpFolder . 'testfile'));
	}
	public function testOffset( ) {
		$validator = new FileInSecuredFolder( __DIR__ . self::TmpFolder );
		$offset = 'testfile';
		$path = __DIR__ . self::TmpFolder . $offset;

		$this->assertEquals( $offset, $validator->getOffset( $path ));
	}
}