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
$token_type = $response['data']['token_type'];
$expires_in = $response['data']['expires_in'];
$refresh_token = $response['data']['refresh_token'];
$user = $client->setSession(['access_token'=>$access_token, 'refresh_token' =>$refresh_token]);
print_r($user);
$client->admin->deleteUser($new_user['data']['id']);
