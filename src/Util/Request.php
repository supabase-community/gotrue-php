<?php

namespace Supabase\Util;

use Psr\Http\Message\ResponseInterface;

class Request
{
	public static function request($method, $url, $headers, $body = null): ResponseInterface
	{
		try {
			$request = new \GuzzleHttp\Psr7\Request($method, $url, $headers, $body);
			$client = new \GuzzleHttp\Client();
			$promise = $client->sendAsync($request)->then(function ($response) {
				return $response;
			});

			$response = $promise->wait();

			return $response;
		} catch (\Exception $e) {
			throw self::handleError($e);
		}
	}

	public static function handleError($error)
	{
		if (method_exists($error, 'getResponse')) {
			$response = $error->getResponse();
			$data = json_decode($response->getBody(), true);
			fwrite(STDERR, print_r($data, true));
			//$error = new GoTrueApiError($data['error'], $data['error_description'], $data['error'], $data['error_description'], $response);
			$error = new GoTrueApiError($data['code'], $data['msg'], $data['code'], $data['msg'], $response);
		} else {
			$error = new GoTrueUnknownError($error->getMessage(), $error->getCode());
		}

		return $error;
	}
}
