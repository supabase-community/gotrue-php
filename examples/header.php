<?php

include __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
$random_string = '';
$domain = 'example.com'; // change this to your desired domain
$length = 10;
for ($i = 0; $i < $length; $i++) {
	$random_string .= $characters[rand(0, strlen($characters) - 1)];
}
$ramdom_email = $random_string.'@'.$domain;
$api_key = getenv('API_KEY');
$reference_id = getenv('REFERENCE_ID');
