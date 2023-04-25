<?php

include __DIR__.'../../header.php';
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
	'email'                => $ramdom_email,
	'password'             => '12345678',
]);
$access_token = $response['data']['access_token'];

//Enroll
$data = $client->mfa->enroll(['factor_type'=> 'totp'], $access_token);
$factor_id = $data['data']['id'];
$secret = $data['data']['totp']['secret'];
unset($data['data']['totp']['qr_code']);

//Challange
$data_challenge = $client->mfa->challenge($factor_id, $access_token);
$challenge_id = $data_challenge['data']['id'];
$expires_at = $data_challenge['data']['expires_at'];

//Verify
$data_verify = $client->mfa->verify(
	$factor_id,
	$access_token,
	['challenge_id'=> $challenge_id, 'code'=>$secret]
);

//Challange and Verify
$data_challenge_verify = $client->mfa->challengeAndVerify($factor_id, '123456', $access_token);
$data_unenroll = $client->mfa->unenroll($factor_id, $access_token);
