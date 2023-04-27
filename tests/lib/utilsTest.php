<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require 'vendor/autoload.php';

class TestUtilTest extends TestCase
{
	public function testMockUserCredentials(): void
	{
		$utils = new TestUtils();
		$result = $utils->mockUserCredentials();
		$this->assertArrayHasKey('email', $result);
		$this->assertArrayHasKey('password', $result);
		$this->assertArrayHasKey('phone', $result);

		$this->assertIsString($result['email']);
		$this->assertIsString($result['password']);
		$this->assertIsString($result['phone']);

		$this->expectMatchesRegularExpression('/.*@.*/', $result['email']);
		$this->expectMatchesRegularExpression('/[0-9]{3}-[0-9]{3}-[0-9]{4}/', $result['phone']);

		$this->assertGreaterThan(0, strlen($result['password']));
	}

	public function testCreateNewUserWithEmail(): void
	{
		$utils = new TestUtils();
		$email = $utils->mockEmail();
		$result = $utils->createNewUserWithEmail(['email' => $email]);

		$this->expectNotToBeNull($result->data);
		$this->expectNotToBeNull($result->data->user);
		$this->expectNotToBeUndefined($result->data->user->email);

		$this->assertEquals($email, $result->data->user->email);
	}

	public function testMockUserMetadata(): void
	{
		$utils = new TestUtils();
		$result = $utils->mockUserMetadata();

		$this->assertArrayHasKey('profile_image', $result);
		$this->assertIsString($result['profile_image']);
		$this->expectMatchesRegularExpression('/.*gravatar.com\/avatar\/.*/', $result['profile_image']);
	}

	public function testMockAppMetaData(): void
	{
		$utils = new TestUtils();
		$result = $utils->mockAppMetaData();

		$this->assertArrayHasKey('roles', $result);
		$this->assertIsArray($result['roles']);
		$this->assertGreaterThan(0, count($result['roles']));
	}
}
