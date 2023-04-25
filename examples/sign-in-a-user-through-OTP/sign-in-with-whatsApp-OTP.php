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

$response = $client->signInWithOtp([
	'phone'                => '+5213221924866',
	'gotrue_meta_security' => ['captcha_token' => $options['captchaToken'] ?? null],
	'options'              => [
		'channel'=> 'whaapp',
	],
]);
print_r($response);
