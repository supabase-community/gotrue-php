<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Supabase\GoTrue\GoTrueClient;

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
		$client = new  GoTrueClient(
			'some_ref_id',
			'some_api_key',
			['option' => 'some_option'],
			'some_domain',
			'some_scheme',
			'/some_path'
		);
		$this->assertEquals($client->__getUrl(), 'some_scheme://some_ref_id.some_domain/some_path');
		$this->assertEquals($client->__getHeaders(), [
			'X-Client-Info' => 'gotrue-php/0.0.1',
			'Authorization' => 'Bearer some_api_key',
			'apikey' => 'some_api_key',
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
			'Supabase\GoTrue\GoTrueClient[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('{"email":"example@email.com","password":"example-password","gotrue_meta_security":{"captcha_token":null}}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/signup', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
			], $headers);

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
			'Supabase\GoTrue\GoTrueClient[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('{"email":"example@email.com","password":"example-password","gotrue_meta_security":{"captcha_token":null}}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/token?grant_type=password', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
			], $headers);

			return true;
		})->andReturn(['data' => ['user' => [], 'session' => []], 'error' => null]);

		$mock->signInWithPassword([
			'email'                => 'example@email.com',
			'password'             => 'example-password',
			'gotrue_meta_security' => ['captcha_token' => $options['captchaToken'] ?? null],
		]);
	}

	/**
	 * Test the request parameters needed for
	 * Log in a user using magiclink or a one-time password (OTP).
	 *
	 * @return void
	 */
	public function testSignInWithOtp()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('{"email":"example@email.com","password":"example-password","gotrue_meta_security":{"captcha_token":null}}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/otp', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
			], $headers);

			return true;
		})->andReturn(['data' => ['user' => [], 'session' => []], 'error' => null]);

		$mock->signInWithOtp([
			'email'                => 'example@email.com',
			'password'             => 'example-password',
			'gotrue_meta_security' => ['captcha_token' => $options['captchaToken'] ?? null],
		]);
	}

	/**
	 * Test the request parameters needed for
	 * remove the logged in user. Pending.
	 *
	 * @return void
	 */
	public function testSignOut()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/logout', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer auth-token',
				'apikey' => 'mokerymock',
				'Content-Type' => 'application/json',
			], $headers);

			return true;
		})->andReturn(['data' => ['user' => [], 'session' => []], 'error' => null]);

		$mock->signOut('auth-token');
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
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
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
			'Supabase\GoTrue\GoTrueClient[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('{"refresh_token":"auth-token"}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/token?grant_type=refresh_token', $url);
			$this->assertEquals([
				'X-Client-Info'  => 'gotrue-php/0.0.1',
				'Authorization'  => 'Bearer auth-token',
				'apikey'         => 'mokerymock',
				'Content-Type'   => 'application/json',
				'noResolveJson'  => '1',
			], $headers);

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
			'Supabase\GoTrue\GoTrueClient[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('GET', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/user', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer auth-token',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
				'noResolveJson' => true,
			], $headers);

			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->getUser('auth-token');
	}

	/**
	 * Test the request parameters needed for
	 * Updates user data for a logged in user.
	 *
	 * @return void
	 */
	public function testUpdateUser()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);
		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('PUT', $scheme);
			$this->assertEquals('{"email":"new-email"}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/user', $url);
			$this->assertEquals([
				'X-Client-Info'  => 'gotrue-php/0.0.1',
				'Authorization'  => 'Bearer auth-token',
				'apikey'         => 'mokerymock',
				'Content-Type'   => 'application/json',
				'noResolveJson'  => '1',
			], $headers);

			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->updateUser(['email' => 'new-email'], 'auth-token');
	}

	/**
	 * Test the request parameters needed for
	 * Sends a password reset request to an email address.
	 *
	 * @return void
	 */
	public function testResetPasswordForEmail()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);
		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('PUT', $scheme);
			$this->assertEquals('{"email":"email","code_challenge":null,"code_challenge_method":null,"gotrue_meta_security":{"captcha_token":null}}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/recover', $url);
			$this->assertEquals([
				'X-Client-Info'  => 'gotrue-php/0.0.1',
				'Authorization'  => 'Bearer mokerymock',
				'apikey'         => 'mokerymock',
				'Content-Type'   => 'application/json',
				'noResolveJson'  => '1',
			], $headers);

			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->resetPasswordForEmail('email');
	}

	/**
	 * Test the request parameters needed to
	 * Starts the enrollment process for a new Multi-Factor
	 * Authentication (MFA) factor.
	 *
	 * @return void
	 */
	public function testMFAEnroll()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueMFAApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('{"factor_type":"totp"}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/factors', $url);
			$this->assertEquals([
				'Authorization'  => 'Bearer auth-token',
				'apikey'         => 'mokerymock',
				'Content-Type'   => 'application/json',
				'noResolveJson'  => '1',
			], $headers);

			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->enroll(['factor_type' => 'totp'], 'auth-token');
	}

	/**
	 * Test the request parameters needed to
	 * Prepares a challenge used to verify that a
	 * user has access to a MFA factor.
	 *
	 * @return void
	 */
	public function testMFAChallenge()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueMFAApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/factors/factor-id/challenge', $url);
			$this->assertEquals([
				'Authorization'  => 'Bearer auth-token',
				'apikey'         => 'mokerymock',
				'Content-Type'   => 'application/json',
				'noResolveJson'  => '1',
			], $headers);

			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->challenge('factor-id', 'auth-token');
	}

	/**
	 * Test the request parameters needed to
	 * Prepares a challenge used to verify that a
	 * user has access to a MFA factor.
	 *
	 * @return void
	 */
	public function testMFAVerify()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueMFAApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/factors/factor-id/verify', $url);
			$this->assertEquals([
				'Authorization'  => 'Bearer auth-token',
				'apikey'         => 'mokerymock',
				'Content-Type'   => 'application/json',
				'noResolveJson'  => '1',
			], $headers);

			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->verify('factor-id', 'auth-token');
	}

	/**
	 * Test the request parameters needed a
	 * Helper method which creates a challenge
	 * and immediately uses the given code to verify against it thereafter.
	 *
	 * @return void
	 */
	public function testChallengeAndVerify()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueMFAApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('POST', $scheme);

			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->challengeAndVerify('factor-id', 'code', 'auth-token');
	}

	/**
	 * Test the request parameters needed to
	 * Unenroll removes a MFA factor.
	 *
	 * @return void
	 */
	public function testUnenroll()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueMFAApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('DELETE', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/factors/factor-id', $url);
			$this->assertEquals([
				'Authorization'  => 'Bearer auth-token',
				'apikey'         => 'mokerymock',
				'Content-Type'   => 'application/json',
				'noResolveJson'  => '1',
			], $headers);

			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->unenroll('factor-id', 'auth-token');
	}

	/**
	 * Test the request parameters needed to
	 * Returns the Authenticator Assurance Level (AAL) for the active session.
	 *
	 * @return void
	 */
	public function testGetAuthenticatorAssuranceLevel()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('DELETE', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/factors/factor-id', $url);
			$this->assertEquals([
				'Authorization'  => 'Bearer auth-token',
				'apikey'         => 'mokerymock',
				'Content-Type'   => 'application/json',
				'noResolveJson'  => '1',
			], $headers);

			return true;
		})->andReturn(['data' => [], 'error' => null]);

		$mock->_getAuthenticatorAssuranceLevel('factor-id', 'auth-token');
	}
}
