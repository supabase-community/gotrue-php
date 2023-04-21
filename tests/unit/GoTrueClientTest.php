<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class GoTrueClientTest extends TestCase
{
	public function tearDown(): void
	{
		parent::tearDown();
		\Mockery::close();
	}

	/**
	 * Test the request parameters for new storage file.
	 */
	public function testNewFunction()
	{
		$client = new  \Supabase\GoTrue\GoTrueClient('some_ref_id', 'some_api_key',
		 ['option'=>'some_option'], 'some_domain', 'some_scheme', '/some_path');
		$this->assertEquals($client->__getUrl(), 'some_scheme://some_ref_id.some_domain/some_path');
		$this->assertEquals($client->__getHeaders(), [
			'X-Client-Info' => 'auth-php/0.0.1',
			'Authorization' => 'Bearer somekey',
			'Content-Type' => 'application/json',
		]);
	}

	public function testSignUp()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[__request]',
			['123123123', 'mokerymock',[] ,'mokerymock.supabase.co',
			'http', '/auth/v1']
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/signup', $url);
			$this->assertEquals([
				'Authorization' => 'Bearer mokerymock',
				'apikey' => 'mokerymock',
				'Content-Type' => 'application/json'
			], $headers);
			$this->assertEquals('{"email":"example@email.com","password":"example-password","gotrue_meta_security":{"captcha_token":null}}', $body);

			return true;
		});

		
		$mock->signUp([
			'email'                => 'example@email.com',
			'password'             => 'example-password',
			'gotrue_meta_security' => ['captcha_token' => $options['captchaToken'] ?? null],
		]);
	}	
}
