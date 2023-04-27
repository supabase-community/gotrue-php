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

$params = [
	'type'  => 'invite',
	'email' => 'email@example.com',
];

$response = $client->admin->generateLink($params);
if ($response['error']) {
	print_r($response);
} else {
	print_r($response['data']);
}
print_r($response);
