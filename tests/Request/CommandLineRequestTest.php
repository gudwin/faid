<?php


namespace Faid\tests\Request;

use \Faid\Request\CommandLineRequest;

class CommandLineRequestTest extends \Faid\tests\baseTest {
	public function setUp() {
		parent::setUp();
	}

	public function testSetURI() {
		$fixture = 'my_uri';
		//
		$request = new CommandLineRequest();
		$request->uri( $fixture );
		$this->assertEquals( '/'. $fixture, $request->uri());

	}
	public function testDomain() {
		$fixture = 'my_domain';
		$request = new CommandLineRequest();
		$request->domain( $fixture );
		$this->assertEquals( $fixture, $request->domain());
	}
	public function testURL() {
		$domain = 'my_domain';
		$uri = '/my_uri';
		$request = new CommandLineRequest();
		$request->domain( $domain );
		$request->uri( $uri );
		$this->assertEquals( 'http://my_domain/my_uri', $request->url() );
	}

	public function testAutoload() {
		$url = 'http://$Test/my_test_url';
		$fixture = 'Hello world!';
		$argv = [ $url, $fixture ];

		$request = new CommandLineRequest( $argv );
		$this->assertEquals( '$Test', $request->domain( ));
		$this->assertEquals( '/my_test_url', $request->uri( ));
		$this->assertEquals( $url , $request->url( ));
		$this->assertEquals( $url, $request->get(0 ) );
		$this->assertEquals( $fixture , $request->get( 1) );
	}
	public function testWithoutURI() {
		$url = 'http://$Test';
		$argv = [ $url];
		$request = new CommandLineRequest( $argv );
		$this->assertEquals( '$Test', $request->domain( ));
		$this->assertEquals( '/', $request->uri( ));
	}

} 