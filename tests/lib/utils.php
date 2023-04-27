<?php

require_once __DIR__.'/client.php';

class TestUtils
{
	public $serviceRoleApiClient;
	public $GOTRUE_JWT_SECRET;
	public $faker;

	public function __construct()
	{
		$client = new TestClient();
		$this->serviceRoleApiClient = $client->serviceRoleApiClient;
		$this->GOTRUE_JWT_SECRET = $client->GOTRUE_JWT_SECRET;
		$this->faker = Faker\Factory::create();
		$this->faker->addProvider(new Ottaviano\Faker\Gravatar($faker));
		$this->faker->addProvider(new Faker\Provider\en_US\PhoneNumber($faker));
	}

	public function mockAccessToken()
	{
		$tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));

		$this->GOTRUE_JWT_SECRET = $tokenBuilder
			->withClaim('sub', '1234567890')
			->withClaim('role', 'anon_key')
			->getToken(new Sha256(), $this->GOTRUE_JWT_SECRET)
			->toString();
	}

	public function mockEmail()
	{
		return $this->faker->email;
	}

	public function mockUserCredentials($opts = [])
	{
		if (! isset($opts['email'])) {
			$opts['email'] = $this->faker->email;
		}

		if (! isset($opts['password'])) {
			$opts['password'] = $this->faker->password;
		}

		if (! isset($opts['phone'])) {
			$opts['phone'] = $this->faker->phoneNumber;
		}

		return [
			'email'    => $opts['email'],
			'password' => $opts['password'],
			'phone'    => $opts['phone'],
		];
	}

	public function mockVerificationOTP()
	{
		return $this->faker->randomNumber(6);
	}

	public function mockUserMetadata()
	{
		return [
			'profile_image' => $this->faker->gravatarUrl(),
		];
	}

	public function mockAppMetaData()
	{
		return [
			'roles' => ['editor', 'publisher'],
		];
	}

	public function createNewUserWithEmail($opts)
	{
		$mockedCredentials = $this->mockUserCredentials($opts);

		return $this->serviceRoleApiClient->createUser([
			'email'    => $mockedCredentials['email'],
			'password' => $mockedCredentials['password'],
			'data'     => [],
		]);
	}
}
