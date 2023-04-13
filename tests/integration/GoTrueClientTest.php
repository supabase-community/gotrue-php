<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class GoTrueClientTest extends TestCase
{
	private $client;

	public function setup(): void
	{
		parent::setUp();
		$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__, '/../../.env.test');
		$dotenv->load();
        $scheme = 'https';
        $domain = 'supabase.co';
        $path = '/auth/v1';
		$api_key = getenv('API_KEY');
		$reference_id = getenv('REFERENCE_ID');
		$this->client = new  \Supabase\GoTrue\GoTrueClient($reference_id, $api_key, [
            'autoRefreshToken'   => false,
            'persistSession'     => true,
            'storageKey'         => $api_key,
        ], $domain, $scheme, $path);
	}

	/**
	 * Test Creates a new user.
	 *
	 * @return void
	 */
	public function testCreateUser(): void
	{
		$email = $this->createRandomEmail();
		$result = $this->client->signUp([
            'email'                => $email,
            'password'             => 'example-password',
            'gotrue_meta_security' => ['captcha_token' => $options['captchaToken'] ?? null],
        ]);
        $this->assertNull($result['error']);
		$this->assertArrayHasKey('data', $result);
		//$getValue = json_decode((string) $result->getBody());
        //fwrite(STDERR, print_r($result['data'], TRUE));
		$email = $result['data']['user']['email'];
        $uid = $result['data']['user']['id'];
		$this->assertEquals($email, $email);
        $result = $this->client->admin->deleteUser($uid);
	}

    public function testSignInUser(): void
	{
		$email = $this->createRandomEmail();
		$result = $this->client->admin->createUser([
            'email'                => $email,
            'password'             => 'example-password',
            'email_confirm' => true,
        ]);

        $result = $this->client->signInWithPassword([
            'email'                => $email,
            'password'             => 'example-password',
        ]);

        $this->assertNull($result['error']);
		$this->assertArrayHasKey('data', $result);
        $uid = $result['data']['user']['id'];
        $acces_token = $result['data']['access_token'];
		$this->assertIsString($acces_token);
        $result = $this->client->admin->deleteUser($uid);
	}

    public function testSignInWithOtp(): void
	{
		$email = $this->createRandomEmail();
		$user = $this->client->admin->createUser([
            'email'                => $email,
            'password'             => 'example-password',
            'email_confirm' => true,
        ]);

        $result = $this->client->signInWithOtp([
            'email'                => $email,
            'gotrue_meta_security' => ['captcha_token' => $options['captchaToken'] ?? null],
            'options'              => [
                'emailRedirectTo'=> 'https://example.com/welcome',
            ],
        ]);

        $this->assertNull($result['error']);
		$this->assertArrayHasKey('data', $result);
        $uid = $user['data']['id'];
        $result = $this->client->admin->deleteUser($uid);
	}

    public function testSignOut(): void
	{
		$email = $this->createRandomEmail();
		$result = $this->client->admin->createUser([
            'email'                => $email,
            'password'             => 'example-password',
            'email_confirm' => true,
        ]);

        $result = $this->client->signInWithPassword([
            'email'                => $email,
            'password'             => 'example-password',
        ]);
        fwrite(STDERR, print_r($result, TRUE));
        $acces_token = $result['data']['access_token'];
        $result = $this->client->signOut($acces_token);

        fwrite(STDERR, print_r($result, TRUE));

        $this->assertNull($result['error']);
		//$this->assertArrayHasKey('data', $result);
        $uid = $result['data']['user']['id'];        
		$this->assertIsString($acces_token);

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

	/**
	 * Test Retrieves the details of an existing Storage bucket function.
	 *
	 * @return void
	 */
	public function testGetBucketWithId(): void
	{
		$bucketName = 'bucket'.microtime(false);
		$this->client->createBucket($bucketName, ['public' => true]);
		$bucket = $this->client->getBucket($bucketName);
		$this->assertEquals('200', $bucket->getStatusCode());
		$this->assertEquals('OK', $bucket->getReasonPhrase());
		$getValue = json_decode((string) $bucket->getBody());
		$obj = $getValue->{'id'};
		$this->assertEquals($bucketName, $obj);
		$this->client->deleteBucket($bucketName);
	}

	/**
	 * Test Updates a Storage bucket function.
	 *
	 * @return void
	 */
	public function testUpdateBucket(): void
	{
		$bucketName = 'bucket'.microtime(false);
		$result = $this->client->createBucket($bucketName, ['public' => true]);
		$result = $this->client->updateBucket($bucketName, ['public' => true]);
		$this->assertEquals('200', $result->getStatusCode());
		$this->assertEquals('OK', $result->getReasonPhrase());
		$this->assertJsonStringEqualsJsonString('{"message":"Successfully updated"}', (string) $result->getBody());
		$result = $this->client->deleteBucket($bucketName);
	}

	/**
	 * Test Removes all objects inside a single bucket function.
	 *
	 * @return void
	 */
	public function testEmptyBucket()
	{
		$bucketName = 'bucket'.microtime(false);
		$result = $this->client->createBucket($bucketName, ['public' => true]);
		$result = $this->client->emptyBucket($bucketName);
		$this->assertEquals('200', $result->getStatusCode());
		$this->assertEquals('OK', $result->getReasonPhrase());
		$this->assertJsonStringEqualsJsonString('{"message":"Successfully emptied"}', (string) $result->getBody());
		$result = $this->client->deleteBucket($bucketName);
	}

	/**
	 * Test Deletes an existing bucket function.
	 *
	 * @return void
	 */
	public function testDeleteBucket()
	{
		$bucketName = 'bucket'.microtime(false);
		$result = $this->client->createBucket($bucketName, ['public' => true]);
		$result = $this->client->deleteBucket($bucketName);
		$this->assertEquals('200', $result->getStatusCode());
		$this->assertEquals('OK', $result->getReasonPhrase());
		$this->assertJsonStringEqualsJsonString('{"message":"Successfully deleted"}', (string) $result->getBody());
	}

	/**
	 * Test Invailid bucket id function.
	 *
	 * @return void
	 */
	public function testGetBucketWithInvalidId(): void
	{
		try {
			$this->client->getBucket('not-a-real-bucket-id');
		} catch (\Exception $e) {
			$this->assertEquals('The resource was not found', $e->getMessage());
		}
	}

	/**
	 * Test Creates a new Storage public bucket function.
	 *
	 * @return void
	 */
	public function testCreatePrivateBucket(): void
	{
		$bucketName = 'bucket'.microtime(false);
		$result = $this->client->createBucket($bucketName, ['public' => false]);
		$this->assertEquals('200', $result->getStatusCode());
		$this->assertEquals('OK', $result->getReasonPhrase());
		$this->assertJsonStringEqualsJsonString('{"name":"'.$bucketName.'"}', (string) $result->getBody());
		$resultInfo = $this->client->getBucket($bucketName);
		$getValue = json_decode((string) $resultInfo->getBody());
		$isPrivate = $getValue->{'public'};
		$this->assertFalse($isPrivate);
		$result = $this->client->deleteBucket($bucketName);
	}
}