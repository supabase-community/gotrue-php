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
$data = $client->mfa->enroll(['factor_type'=> 'totp'], $access_token);
print_r($data);
$data = $client->mfa->unenroll(['factor_id'=> $data['factorId']], $access_token);
