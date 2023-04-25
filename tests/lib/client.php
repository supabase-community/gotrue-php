<?php

declare(strict_types=1);

use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Supabase\GoTrue\GoTrueClient;

require 'vendor/autoload.php';

class TestClient
{
	public $AUTH_ADMIN_JWT;

	public $SIGNUP_ENABLED_AUTO_CONFIRM_OFF_PORT = 9999;
	public $SIGNUP_ENABLED_AUTO_CONFIRM_ON_PORT = 9998;
	public $SIGNUP_DISABLED_AUTO_CONFIRM_OFF_PORT = 9997;

	public $GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_OFF;
	public $GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_ON;
	public $GOTRUE_URL_SIGNUP_DISABLED_AUTO_CONFIRM_OFF;

	public $authClient;
	public $authClientWithSession;
	public $authSubscriptionClient;
	public $clientApiAutoConfirmEnabledClient;
	public $clientApiAutoConfirmOffSignupsEnabledClient;
	public $clientApiAutoConfirmDisabledClient;
	public $authAdminApiAutoConfirmEnabledClient;
	public $authAdminApiAutoConfirmDisabledClient;

	public $SERVICE_ROLE_JWT;

	public $serviceRoleApiClient;
	public $serviceRoleApiClientWithSms;
	public $serviceRoleApiClientNoSms;

	public function __construct()
	{
		$GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_OFF = 'http://localhost:'.$this->SIGNUP_ENABLED_AUTO_CONFIRM_OFF_PORT;
		$GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_ON = 'http://localhost:'.$this->SIGNUP_ENABLED_AUTO_CONFIRM_ON_PORT;
		$GOTRUE_URL_SIGNUP_DISABLED_AUTO_CONFIRM_OFF = 'http://localhost:'.$this->SIGNUP_DISABLED_AUTO_CONFIRM_OFF_PORT;

		$GOTRUE_JWT_SECRET = InMemory::plainText(random_bytes(32));

		$tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));

		$this->AUTH_ADMIN_JWT = $tokenBuilder
			->relatedTo('1234567890')
			->withClaim('role', 'supabase_admin')
			->getToken(new Sha256(), $GOTRUE_JWT_SECRET)
			->toString();

		$this->SERVICE_ROLE_JWT = $tokenBuilder
			->withClaim('role', 'service_role')
			->getToken(new Sha256(), $GOTRUE_JWT_SECRET)
			->toString();

		$this->authClient = new GoTrueClient([
			'url'              => $GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_OFF,
			'autoRefreshToken' => false,
			'persistSession'   => true,
		]);

		$this->authClientWithSession = new GoTrueClient([
			'url'              => $GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_ON,
			'autoRefreshToken' => false,
			'persistSession'   => false,
		]);

		$this->authSubscriptionClient = new GoTrueClient([
			'url'              => $this->GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_ON,
			'autoRefreshToken' => false,
			'persistSession'   => true,
		]);

		$this->clientApiAutoConfirmEnabledClient = new GoTrueClient([
			'url'              => $this->GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_ON,
			'autoRefreshToken' => false,
			'persistSession'   => true,
		]);

		$this->clientApiAutoConfirmOffSignupsEnabledClient = new GoTrueClient([
			'url'              => $this->GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_OFF,
			'autoRefreshToken' => false,
			'persistSession'   => true,
		]);

		$this->clientApiAutoConfirmDisabledClient = new GoTrueClient([
			'url'              => $this->GOTRUE_URL_SIGNUP_DISABLED_AUTO_CONFIRM_OFF,
			'autoRefreshToken' => false,
			'persistSession'   => true,
		]);

		$this->authAdminApiAutoConfirmEnabledClient = new GoTrueClient([
			'url'     => $this->GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_ON,
			'headers' => ['Authorization' => 'Bearer '.$this->AUTH_ADMIN_JWT],
		]);

		$this->authAdminApiAutoConfirmDisabledClient = new GoTrueClient([
			'url'     => $this->GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_OFF,
			'headers' => ['Authorization' => 'Bearer '.$this->AUTH_ADMIN_JWT],
		]);

		$this->serviceRoleApiClient = new GoTrueClient([
			'url'     => $this->GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_ON,
			'headers' => ['Authorization' => 'Bearer '.$this->SERVICE_ROLE_JWT],
		]);

		$this->serviceRoleApiClientWithSms = new GoTrueClient([
			'url'     => $this->GOTRUE_URL_SIGNUP_ENABLED_AUTO_CONFIRM_OFF,
			'headers' => ['Authorization' => 'Bearer '.$this->SERVICE_ROLE_JWT],
		]);

		$this->serviceRoleApiClientNoSms = new GoTrueClient([
			'url'     => $this->GOTRUE_URL_SIGNUP_DISABLED_AUTO_CONFIRM_OFF,
			'headers' => ['Authorization' => 'Bearer '.$this->SERVICE_ROLE_JWT],
		]);
	}
}
