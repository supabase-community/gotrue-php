<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

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
		$options =- [];
        $client = new  \Supabase\GoTrue\GoTrueClient($reference_id, $api_key,
		 $options, $domain, $scheme, $path);

		$this->assertEquals($client->__getUrl(), 'https://some_ref_id.supabase.co/auth/v1');
		$this->assertEquals($client->__getHeaders(), [
			'X-Client-Info' => 'gotrue-php/0.0.1',
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer somekey',
		]);
	}

	public function testGetUserById()
	{
		$mock = \Mockery::mock(
			'Supabase\GoTrue\GoTrueClient[__request]',
			['123123123', 'mokerymock',[], 'mokerymock.supabase.co',
			'http', '/auth/v1']
		);



		$mock->shouldReceive('__request')->withArgs(function ($scheme, $url, $headers) {
			$this->assertEquals('GET', $scheme);
			$this->assertEquals('http://123123123.mokerymock.supabase.co/auth/v1/admin/users/id', $url);
			$this->assertEquals([
				'X-Client-Info' => 'auth-php/0.0.1',
				'Authorization' => 'Bearer 123123123',
				'Content-Type' => 'application/json',
			], $headers);

			return true;
		});
		$mock->admin->getUserById('id');
	}
}
