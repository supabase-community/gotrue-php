<?php

include __DIR__.'../../../header.php';
use Supabase\GoTrue\GoTrueAdminApi;

$scheme = 'https';
$domain = 'supabase.co';
$path = '/auth/v1';

$client = new GoTrueAdminApi($reference_id, $api_key, [
	'autoRefreshToken'   => false,
	'persistSession'     => true,
	'storageKey'         => $api_key,
], $domain, $scheme, $path);

$response = $client->listUsers($uid = '958b2bb4-20ed-4a90-a419-3b01d7e58bfd');
print_r($response);
