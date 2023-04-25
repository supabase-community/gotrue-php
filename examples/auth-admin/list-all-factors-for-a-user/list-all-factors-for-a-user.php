<?php

include __DIR__.'../../../header.php';
use Supabase\GoTrue\GoTrueClient;

$scheme = 'https';
$domain = 'supabase.co';
$path = '/auth/v1';

$client = new GoTrueClient($reference_id, $api_key, [
	'autoRefreshToken'   => false,
	'persistSession'     => true,
	'storageKey'         => $api_key,
], $domain, $scheme, $path);

$userData = [
	'email'                => $ramdom_email,
	'password'             => '12345678',
	'email_confirm'        => true,
];

$new_user = $client->admin->createUser($userData);

$response = $client->signInWithPassword([
	'email'                => 'adolfomariscalh@hotmail.com',
	'password'             => '12345678',
]);
$access_token = $response['data']['access_token'];
$uid = $response['data']['user']['id'];
$data = $client->mfa->enroll(['factor_type'=> 'totp'], $access_token);
$factor_id = $data['data']['id'];
$secret = $data['data']['totp']['secret'];
unset($data['data']['totp']['qr_code']);
$data_challenge = $client->mfa->challenge($factor_id, $access_token);
$challenge_id = $data_challenge['data']['id'];
$expires_at = $data_challenge['data']['expires_at'];
$data_unenroll = $client->mfa->unenroll($factor_id, $access_token);

//List Factor from User
$responseFactors = $client->listFactors($access_token);

//List Factors from Admin
$responseFactorsFromAdmin = $client->admin->_listFactors($uid);

foreach ($responseFactorsFromAdmin['data'] as $key => $factor) {
	$responseFactorDelete = $client->admin->_deleteFactor($uid, $factor['id']);
	print_r($responseFactorDelete);
}

$response_delete = $client->admin->deleteUser($new_user['data']['id']);
