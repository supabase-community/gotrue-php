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
	 * Test the request parameters for new functions Object.
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

	/**
	 * Test the request parameters needed for Create a new user.
	 *
	 * @return void
	 */
	public function testSignUp()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[signUp]',
			['123123123', 'mokerymock',[] ,'mokerymock.supabase.co',
			'http', '/auth/v1']
		);

		$mock->shouldReceive('signUp')->withArgs(function ($data) {			
			$this->assertEquals('example@email.com', $data['email']);
			$this->assertEquals('example-password', $data['password']);
			return true;
		})->andReturn(['data' => ['user' => [], 'session' => []], 'error' => null]);

		$mock->signUp([
			'email'                => 'example@email.com',
			'password'             => 'example-password',
			'gotrue_meta_security' => ['captcha_token' => $options['captchaToken'] ?? null],
		]);
	}

	/**
	 * Test the request parameters needed for Sign in a user.
	 *
	 * @return void
	 */
	public function testSignInWithPassword()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[signInWithPassword]',
			['123123123', 'mokerymock',[] ,'mokerymock.supabase.co',
			'http', '/auth/v1']
		);

		$mock->shouldReceive('signInWithPassword')->withArgs(function ($data) {			
			$this->assertEquals('example@email.com', $data['email']);
			$this->assertEquals('example-password', $data['password']);
			return true;
		})->andReturn(['data' => ['user' => [], 'session' => []], 'error' => null]);

		$mock->signInWithPassword([
			'email'                => 'example@email.com',
			'password'             => 'example-password',
			'gotrue_meta_security' => ['captcha_token' => $options['captchaToken'] ?? null],
		]);
	}	

	/**
	 * Test the request parameters needed for Return a session,.
	 *
	 * @return void
	 */
	public function testGetSession()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[getSession]',
			['123123123', 'mokerymock',[] ,'mokerymock.supabase.co',
			'http', '/auth/v1']
		);

		$mock->shouldReceive('getSession')->withArgs(function ($authToken) {			
			$this->assertEquals('auth-token', $authToken);
			return true;
		})->andReturn(['access_token' => 'auth-token']);

		$mock->getSession('auth-token');
	}
	
	/**
	 * Test the request parameters needed for Returns a new session.
	 *
	 * @return void
	 */
	public function testRefreshSession()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[refreshSession]',
			['123123123', 'mokerymock',[] ,'mokerymock.supabase.co',
			'http', '/auth/v1']
		);

		$mock->shouldReceive('refreshSession')->withArgs(function ($authToken) use ($mock) {			
			$this->assertEquals('auth-token', $authToken);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1', $mock->__getUrl());
			$this->assertEquals([
				//'X-Client-Info' => 'auth-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey' => 'mokerymock',
				'X-Client-Info' => 'gotrue-php/0.0.1',
			], $mock->__getHeaders());
			
			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->refreshSession('auth-token');
	}

	/**
	 * Test the request parameters needed for Gets the current
	 * user details if there is an existing session.
	 *
	 * @return void
	 */
	public function testGetUser()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[getUser]',
			['123123123', 'mokerymock',[] ,'mokerymock.supabase.co',
			'http', '/auth/v1']
		);
		$mock->shouldReceive('getUser')->withArgs(function ($authToken) use ($mock) {			
			$this->assertEquals('auth-token', $authToken);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1', $mock->__getUrl());
			$this->assertEquals([
				//'X-Client-Info' => 'auth-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey' => 'mokerymock',
				'X-Client-Info' => 'gotrue-php/0.0.1',
			], $mock->__getHeaders());
			
			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->getUser('auth-token');
	}

	/**
	 * In order to use the updateUser() method, the user
	 * needs to be signed in first.
	 *
	 * @return void
	 */
	public function testUpdateUser()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[updateUser]',
			['123123123', 'mokerymock',[] ,'mokerymock.supabase.co',
			'http', '/auth/v1']
		);
		$mock->shouldReceive('updateUser')->withArgs(function ($attrs, $jwt = null) use ($mock) {			
			$this->assertEquals('auth-token', $jwt);
			$this->assertEquals('new-email', $attrs['email']);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1', $mock->__getUrl());
			$this->assertEquals([
				//'X-Client-Info' => 'auth-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey' => 'mokerymock',
				'X-Client-Info' => 'gotrue-php/0.0.1',
			], $mock->__getHeaders());
			
			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->updateUser(['email'=>"new-email"], 'auth-token');
	}

	/**
	 * Test the request parameters needed for 
	 * Sets the session data from the current session.
	 *
	 * @return void
	 */
	public function testSetSession()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[setSession]',
			['123123123', 'mokerymock',[] ,'mokerymock.supabase.co',
			'http', '/auth/v1']
		);
		$mock->shouldReceive('setSession')->withArgs(function ($jwt, $refreshToken) use ($mock) {			
			$this->assertEquals('auth-token', $jwt);
			$this->assertEquals('refresh-token', $refreshToken);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1', $mock->__getUrl());
			$this->assertEquals([
				//'X-Client-Info' => 'auth-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey' => 'mokerymock',
				'X-Client-Info' => 'gotrue-php/0.0.1',
			], $mock->__getHeaders());
			
			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->setSession('auth-token', 'refresh-token');
	}

	/**
	 * Sends a password reset request to an email address.
	 *
	 * @return void
	 */
	public function testResetPasswordForEmail()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[resetPasswordForEmail]',
			['123123123', 'mokerymock',[] ,'mokerymock.supabase.co',
			'http', '/auth/v1']
		);
		$mock->shouldReceive('resetPasswordForEmail')->withArgs(function ($email) use ($mock) {			
			$this->assertEquals('email', $email);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1', $mock->__getUrl());
			$this->assertEquals([
				//'X-Client-Info' => 'auth-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey' => 'mokerymock',
				'X-Client-Info' => 'gotrue-php/0.0.1',
			], $mock->__getHeaders());
			
			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->resetPasswordForEmail('email');
	}

	/**
	 * Starts the enrollment process for a new Multi-Factor 
	 * Authentication (MFA) factor.
	 *
	 * @return void
	 */
	public function testMFAEnroll()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueMFAApi[enrroll]',
			['123123123', 'mokerymock',[] ,'mokerymock.supabase.co',
			'http', '/auth/v1']
		);
		$mock->shouldReceive('enroll')->withArgs(function ($email) use ($mock) {			
			$this->assertEquals('email', $email);			
			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->enroll(['factor_type'=> 'totp'], 'auth-token');
	}
}
