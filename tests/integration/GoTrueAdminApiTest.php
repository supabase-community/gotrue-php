<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Supabase\Util\EnvSetup;

final class GoTrueAdminApiTest extends TestCase
{
	private $client;

	public function setup(): void
	{
		parent::setUp();

		$keys = EnvSetup::env(__DIR__.'/../');
		$api_key = $keys['API_KEY'];
		$reference_id = $keys['REFERENCE_ID'];

		$this->client = new  \Supabase\GoTrue\GoTrueClient($reference_id, $api_key, [
			'autoRefreshToken'   => false,
			'persistSession'     => true,
			'storageKey'         => $api_key, // @TODO - is this the correct interface?
		]);
	}

	public function testGetUserById(): void
	{
		$email = $this->createRandomEmail();
		$result = $this->client->admin->createUser([
			'email'                => $email,
			'password'             => 'example-password',
			'email_confirm'        => true,
		]);

		$result = $this->client->signInWithPassword([
			'email'                => $email,
			'password'             => 'example-password',
		]);
		$uid = $result['data']['user']['id'];
		$result = $this->client->admin->getUserById($uid);
		$this->assertEquals($uid, $result['id']);
		$this->assertIsArray($result);

		$result = $this->client->admin->deleteUser($uid);
	}

	public function testListUsers(): void
	{
		$result = $this->client->admin->listUsers([
			'page'   => 1,
			'perPage'=> 2,
		]);
		$this->assertNull($result['error']);
		$this->assertIsArray($result['data']);
	}

	public function testCreateUser(): void
	{
		$email = $this->createRandomEmail();
		$result = $this->client->admin->createUser([
			'email'                => $email,
			'password'             => 'example-password',
			'email_confirm'        => true,
		]);
		$uid = $result['data']['id'];
		$this->assertNull($result['error']);
		$this->assertIsArray($result['data']);
		$result = $this->client->admin->deleteUser($uid);
	}

	public function testDeleteUser(): void
	{
		$email = $this->createRandomEmail();
		$result = $this->client->admin->createUser([
			'email'                => $email,
			'password'             => 'example-password',
			'email_confirm'        => true,
		]);
		$uid = $result['data']['id'];
		$result = $this->client->admin->deleteUser($uid);
		$this->assertNull($result['error']);
		$this->assertIsArray($result['data']);
	}

	/*
	public function testInviteUserByEmail(): void
	{
		$email = $this->createRandomEmail();
		$result = $this->client->admin->inviteUserByEmail($email);
		$this->assertNull($result['error']);
		$this->assertIsArray($result['data']);
	}

	public function testResetPasswordForEmail(): void
	{
		$email = $this->createRandomEmail();
		$result = $this->client->admin->createUser([
			'email'                => $email,
			'password'             => 'example-password',
			'email_confirm'        => true,
		]);
		$uid = $result['data']['id'];
		$result = $this->client->admin->resetPasswordForEmail(
			$email,
			['redirectTo' => 'https://example.com/update-password']
		);
		$this->assertIsArray($result['data']);
		$this->assertNull($result['error']);
		$result = $this->client->admin->deleteUser($uid);
	}*/

	public function testGenerateLink(): void
	{
		$email = $this->createRandomEmail();
		$params = [
			'type'     => 'signup',
			'email'    => $email,
			'password' => 'secret',
		];
		$result = $this->client->admin->generateLink($params);
		$this->assertIsArray($result['data']);
		$this->assertNull($result['error']);
	}

	public function testUpdateUserById(): void
	{
		$email = $this->createRandomEmail();
		$result = $this->client->admin->createUser([
			'email'                => $email,
			'password'             => 'example-password',
			'email_confirm'        => true,
		]);
		$uid = $result['data']['id'];
		$result = $this->client->admin->updateUserById(
			$uid,
			['email'=> 'updated-'.$email]
		);
		$this->assertIsArray($result['data']);
		$this->assertNull($result['error']);
		$this->assertEquals('updated-'.$email, $result['data']['email']);
		$result = $this->client->admin->deleteUser($uid);
	}

	public function testListAndDeleteFactors(): void
	{
		$email = $this->createRandomEmail();
		$result = $this->client->admin->createUser([
			'email'                => $email,
			'password'             => 'example-password',
			'email_confirm'        => true,
		]);
		$uid = $result['data']['id'];
		$result = $this->client->admin->_listFactors($uid);
		foreach ($result['data'] as $key => $factor) {
			$responseFactorDelete = $this->client->admin->_deleteFactor($uid, $factor['id']);
		}
		$this->assertIsArray($result['data']);
		$this->assertNull($result['error']);
		$result = $this->client->admin->deleteUser($uid);
	}

	private function createRandomEmail(): string
	{
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$random_string = '';
		$domain = 'example.com';
		$length = 10;
		for ($i = 0; $i < $length; $i++) {
			$random_string .= $characters[rand(0, strlen($characters) - 1)];
		}

		return $random_string.'@'.$domain;
	}
}
