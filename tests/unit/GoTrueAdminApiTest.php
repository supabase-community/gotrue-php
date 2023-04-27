<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Supabase\GoTrue\GoTrueClient;

class GoTrueAdminApiTest extends TestCase
{
	public function tearDown(): void
	{
		parent::tearDown();
		\Mockery::close();
	}

	/**
	 * Test new StorageClient().
	 *
	 * @return void
	 */
	public function testNewFunctionsClient()
	{
		$scheme = 'https';
		$domain = 'supabase.co';
		$path = '/auth/v1';
		$api_key = 'somekey';
		$reference_id = 'some_ref_id';
		$options = [];
		$client = new  GoTrueClient(
			$reference_id,
			$api_key,
			$options,
			$domain,
			$scheme,
			$path
		);

		$this->assertEquals($client->__getUrl(), 'https://some_ref_id.supabase.co/auth/v1');
		$this->assertEquals($client->__getHeaders(), [
			'X-Client-Info' => 'gotrue-php/0.0.1',
			'Authorization' => 'Bearer somekey',
			'apikey' => 'somekey',
		]);
	}

	/**
	 * Test the request parameters needed for Sing out a user.
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
				'Content-Type'  => 'application/json',
				'apikey'        => 'mokerymock',
			], $headers);

			return true;
		})->once()
			->andReturn([]);

		$mock->signOut('auth-token');
	}

	/**
	 * Test the request parameters needed for
	 * Fetch the user object from the database based on the user's id.
	 *
	 * @return void
	 */
	public function testGetUserById()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('GET', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/admin/users/uid', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'Content-Type'  => 'application/json',
				'apikey'        => 'mokerymock',
			], $headers);

			return true;
		})->once()
			->andReturn([]);

		$mock->getUserById('uid');
	}

	/**
	 * Test the request parameters needed for
	 * Get a list of users.
	 *
	 * @return void
	 */
	public function testListUsers()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('GET', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/admin/users', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
				'noResolveJson' => '1',
			], $headers);

			return true;
		})->once()
			->andReturn([]);

		$mock->listUsers();
	}

	/**
	 * Test the request parameters needed for
	 * Creates a new user.
	 *
	 * @return void
	 */
	public function testCreateUser()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('{"email":"user@email.com","email_confirm":true}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/admin/users', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
				'noResolveJson' => '1',
			], $headers);

			return true;
		})->once()
			->andReturn([]);

		$userData = [
			'email'         => 'user@email.com',
			'email_confirm' => true,
		];
		$mock->createUser($userData);
	}

	/**
	 * Test the request parameters needed for
	 * Delete a user.
	 *
	 * @return void
	 */
	public function testDeleteUser()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('DELETE', $scheme);
			$this->assertEquals('{"should_soft_delete":false}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/admin/users/uid', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
				'noResolveJson' => '1',
			], $headers);

			return true;
		})->once()
			->andReturn([]);
		$mock->deleteUser('uid');
	}

	/**
	 * Test the request parameters needed for
	 * Sends an invite link to an email address.
	 *
	 * @return void
	 */
	public function testInviteUserByEmail()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('{"email":"email-address","data":null}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/invite', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
				'noResolveJson' => '1',
			], $headers);

			return true;
		})->once()
			->andReturn([]);
		$mock->inviteUserByEmail('email-address');
	}

	/**
	 * Test the request parameters needed for
	 * Sends an invite link to an email address.
	 *
	 * @return void
	 */
	public function testResetPasswordForEmail()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('{"email":"email@example.com","gotrue_meta_security":{"captcha_token":""}}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/recover?redirect_to=https://example.com/update-password', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
				'noResolveJson' => '1',
			], $headers);

			return true;
		})->once()
			->andReturn([]);
		$mock->resetPasswordForEmail(
			'email@example.com',
			['redirectTo' => 'https://example.com/update-password']
		);
	}

	/**
	 * Test the request parameters needed for
	 * Generates email links and OTPs to be sent via a custom email provider.
	 *
	 * @return void
	 */
	public function testGenerateLink()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('POST', $scheme);
			$this->assertEquals('{"type":"invite","email":"email@example.com"}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/admin/generate_link', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
				'noResolveJson' => '1',
			], $headers);

			return true;
		})->once()
			->andReturn([]);

		$params = [
			'type'  => 'invite',
			'email' => 'email@example.com',
		];
		$mock->generateLink($params);
	}

	/**
	 * Test the request parameters needed for
	 * Updates the user data.
	 *
	 * @return void
	 */
	public function testUpdateUserById()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers, $body) {
			$this->assertEquals('PUT', $scheme);
			$this->assertEquals('{"email":"new-email"}', $body);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/admin/users/uid', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
				'noResolveJson' => '1',
			], $headers);

			return true;
		})->once()
			->andReturn([]);
		$mock->updateUserById('uid', ['email'=> 'new-email']);
	}

	/**
	 * Test the request parameters needed for
	 * Lists all factors associated to a user.
	 *
	 * @return void
	 */
	public function testListFactors()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('GET', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/admin/users/uid/factors', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
				'noResolveJson' => '1',
			], $headers);

			return true;
		})->once()
			->andReturn([]);

		$mock->_listFactors('uid');
	}

	/**
	 * Test the request parameters needed for
	 * Deletes a factor on a user.
	 *
	 * @return void
	 */
	public function testDeleteFactor()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueAdminApi[__request]',
			[
				'123123123', 'mokerymock', [], 'mokerymock.supabase.co',
				'http', '/auth/v1',
			]
		);

		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('DELETE', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/admin/users/uid/factors/factor-id', $url);
			$this->assertEquals([
				'X-Client-Info' => 'gotrue-php/0.0.1',
				'Authorization' => 'Bearer mokerymock',
				'apikey'        => 'mokerymock',
				'Content-Type'  => 'application/json',
				'noResolveJson' => '1',
			], $headers);

			return true;
		})->once()
			->andReturn([]);

		$mock->_deleteFactor('uid', 'factor-id');
	}
}
