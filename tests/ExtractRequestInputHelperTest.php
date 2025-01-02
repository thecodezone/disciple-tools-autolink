<?php


use DT\Autolink\CodeZone\WPSupport\Router\ServerRequestFactory;
use function DT\Autolink\extract_request_input;

class ExtractRequestInputHelperTest extends \Tests\TestCase {

	/**
	 * @test
	 */
	public function it_handles_get_requests() {
		$request = ServerRequestFactory::request( 'GET', '/?foo=bar', [
			'foo' => 'bar'
		] );
		$params  = extract_request_input( $request );
		$this->assertEquals( [ 'foo' => 'bar' ], $params );
	}

	/**
	 * @test
	 */
	public function it_handles_post_requests() {
		$request = ServerRequestFactory::request( 'POST', '/?foo=bar', [
			'foo' => 'bar'
		] );
		$params  = extract_request_input( $request );
		$this->assertEquals( [ 'foo' => 'bar' ], $params );
	}

    /**
     * @test
     */
    public function it_handles_json_requests() {
        $request = ServerRequestFactory::request( 'POST', '/?foo=bar', [
            'foo' => 'bar'
            ], [
            'Content-Type' => 'application/json'
        ]);
        $params  = extract_request_input( $request );
        $this->assertEquals( [ 'foo' => 'bar' ], $params );
    }

    /**
     * @test
     */
    public function it_handles_form_encoded_requests() {
        $request = ServerRequestFactory::request( 'POST', '/?foo=bar', [
            'foo' => 'bar'
            ], [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]  );
        $params  = extract_request_input( $request );
        $this->assertEquals( [ 'foo' => 'bar' ], $params );
    }

    /**
     * @test
     */
    public function it_handles_form_encoded_get_requests() {
        $request = ServerRequestFactory::request( 'GET', '/?foo=bar', [
            'foo' => 'bar'
            ], [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ] );
        $params  = extract_request_input( $request );
        $this->assertEquals( [ 'foo' => 'bar' ], $params );
        $this->assertEquals( 'bar', $request->getQueryParams()['foo'] );
    }
}
