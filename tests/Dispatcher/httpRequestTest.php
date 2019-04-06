<?php
namespace Faid\tests\Dispatcher {
	use \Faid\Request\HttpRequest;
	class HttpRequestTest extends BasicTest {
		const FirstDomainFixture = 'test.domain';
		const SecondDomainFixture = 'test2.domain';
		protected $defaultDomain = '';
		public function setUp(): void {
			$this->defaultDomain = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
		}
		public function tearDown(): void {
			$_SERVER['HTTP_HOST'] = $this->defaultDomain;
		}

		public function testGetDomain( ) {
			$_SERVER['HTTP_HOST'] = self::FirstDomainFixture;
			$request = new HttpRequest();
			$this->assertEquals( self::FirstDomainFixture, $request->domain());
		}

		public function testSetDomain( ) {
			$_SERVER['HTTP_HOST'] = self::FirstDomainFixture;
			$request = new HttpRequest();
			$request->domain( self::SecondDomainFixture);
			$this->assertEquals( self::SecondDomainFixture, $request->domain());
			$this->assertEquals( self::FirstDomainFixture, $_SERVER['HTTP_HOST']);

			$request->domain( self::FirstDomainFixture);
			$this->assertEquals( self::FirstDomainFixture, $request->domain());

		}
        public function testGetURL( ) {
            $_SERVER['HTTP_HOST'] = self::FirstDomainFixture;
            $request = new HttpRequest();

            $this->assertEquals( sprintf('http://%s/',self::FirstDomainFixture), $request->url());
        }
        public function testSetUrl( ) {
            $request = new HttpRequest();
            $request->url( self::FirstDomainFixture );
            $this->assertEquals( self::FirstDomainFixture, $request->url());
        }

	}
}
