<?php

include __DIR__.'./header.php';
use Supabase\GoTrue\GoTrueClient;

$scheme = 'http';
$domain = 'localhost:3000';
$path = '/auth/v1';

$client = new GoTrueClient([
    'url'              => 'https://gpdefvsxamnscceccczu.supabase.co/auth/v1',
    'autoRefreshToken' => false,
    'persistSession'   => true,
    'storageKey'         => $api_key,
]);

$client->signUp([
    'email'=> 'example@email.com',
    'password'=> 'example-password',
  ]);