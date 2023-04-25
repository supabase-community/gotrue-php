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

// generate an email change link to be sent to the current email address
$params = [
	'type'    => 'email_change_current',
	'email'   => 'current.email@example.com',
	'newEmail'=> 'new.email@example.com',
];

$response = $client->admin->generateLink($params);
if ($response['error']) {
	print_r($response);
} else {
	print_r($response['data']);
}
print_r($response);

// generate an email change link to be sent to the new email address
$params = [
	'type'    => 'email_change_new',
	'email'   => 'current.email@example.com',
	'newEmail'=> 'new.email@example.com',
];

$response = $client->admin->generateLink($params);
if ($response['error']) {
	print_r($response);
} else {
	print_r($response['data']);
}
print_r($response);
