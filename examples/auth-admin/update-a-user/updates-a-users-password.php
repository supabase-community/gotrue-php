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
	'email'         => 'user@email.com',
	'email_confirm' => true,
];

$create_response = $client->admin->createUser($userData);
$response = $client->admin->updateUserById(
	$create_response['data']['id'],
	['password'=> 'new_password']
);
print_r($response);
$client->admin->deleteUser($create_response['data']['id']);
