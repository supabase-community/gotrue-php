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

$response = $client->signInWithPassword([
	'email'                => 'adolfomariscalh@hotmail.com',
	'password'             => '12345678',
	'gotrue_meta_security' => ['captcha_token' => $options['captchaToken'] ?? null],
]);
$access_token = $response['data']['access_token'];
$token_type = $response['data']['token_type'];
$expires_in = $response['data']['expires_in'];
$refresh_token = $response['data']['refresh_token'];
$user = $client->getUser($access_token);
print_r($user);
