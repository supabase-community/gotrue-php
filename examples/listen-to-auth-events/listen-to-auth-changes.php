<?php

include __DIR__.'../../header.php';
use Ratchet\RFC6455\Messaging\MessageInterface;
use Supabase\GoTrue\GoTrueClient;

$scheme = 'https';
$domain = 'supabase.co';
$path = '/auth/v1';

$client = new GoTrueClient($reference_id, $api_key, [
	'autoRefreshToken'   => false,
	'persistSession'     => true,
	'storageKey'         => $api_key,
], $domain, $scheme, $path);

$loop = React\EventLoop\Factory::create();

$connector = new Ratchet\Client\Connector($loop);
$connection = $connector('ws://127.0.0.1:9000/echo')->then(
	function (Ratchet\Client\WebSocket $conn) {
		$conn->on('message', function (MessageInterface $msg) use ($conn) {
			echo "{$msg}\n";
			$conn->close();
		});

		$conn->send('Hello world !');
	},
	function (Throwable $e) {
		echo "Could not connect: {$e->getMessage()}\n";
	}
);

$loop->run();
