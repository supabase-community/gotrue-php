<?php

//require 'vendor/autoload.php';

function handleError($err)
{
	if ($err->hasResponse() === false) {
		return $err;
	}

	$errResponse = $err->getResponse();

	$NETWORK_ERROR_CODE = [502, 503, 504];
	if (! Helpers::isRetryAble($errResponse)) {
		return new AuthRetryableFetchError($err->getReasonPhrase(), 0);
	} elseif (in_array($err->status, $NETWORK_ERROR_CODE)) {
		return new AuthRetryableFetchError($err->getResonPhrase(), $errResponse->getStatusCode());
	} else {
		return new AuthApiError($err->getReasonPhrase(), $err->getStatusCode());
	}
}

function getRequestParams($method, $options, $params, $body)
{
	$_params = ['method' => $method, 'headers' => (isset($options) && isset($options->headers) ? $options->headers : [])];

	if ($method == 'GET') {
		return $_params;
	}

	$_params['headers'] = array_merge($params['headers'], ['Content-Type' => 'application/json;charset=UTF-8']);
	$_param['body'] = json_encode($body);

	return array_merge($_params, $params);
}

function _request($method, $url, $options)
{
	$_a = '';
	$headers = (isset($options) && isset($options->headers) ? $options->headers : []);
	if (isset($options)) {
		$headers['Authorization'] = 'Bearer '.$options['headers'];
	}

	$qs = ($_a = isset($options) == false ? null : $options->query) !== null && $_a != 0 ? $_a : [];

	if (isset($options) == false || isset($options->redirectTo) == false) {
		$qs->redirectTo = $options->redirectTo;
	}

	$queryString = generateQueryString($qs);
	//$data = _handleRequest($fetcher, $method, $url . $queryString, $options);
	//return (isset(options) ? null : options.xform) ? isset(options) ? false : options.xform(data) : ['data' => $data, 'error' => null ];
}

function _handleRequest($fetcher, $method, $url, $options, $params, $body)
{
	$client = new \GuzzleHttp\Client();
	$opts = getRequestParams($method, $options, $params, $body);
	$request = new \GuzzleHttp\Psr7\Request($method, $url, $opts);

	try {
		$promise = $client->sendAsync($request)->then(function ($response) {
			return json_decode($response->getBody());
		});

		$response = $promise->wait();

		if (! $response->ok) {
			throw $response;
		}

		if (isset($options) && isset($options->noResolveJson) && $options->noResolveJson) {
			return $response;
		}

		return $response->json();
	} catch(\Exception $e) {
		throw handleError($e);
	}
}

function _userResponse($response)
{
	$user = new User($response->data);

	return ['data' => ['user' => $user], 'error' => null];
}

function _ssoResponse($data)
{
	return ['data' => $data, 'error' => null];
}

function generateQueryString($params)
{
	$qs = '';
	if (count($params) > 0) {
		$qs .= '?'.implode('&', array_map(function ($item) {
			return $item[0].'='.$item[1];
		}, array_map(null, array_keys($params), $params)));
	}

	return $qs;
}

function sessionResponse($data)
{
	$_a = '';
	$session = null;
	if (hasSession($data)) {
		$session = $data;
		$session->expires_at = $data->expires_in;
	}

	$user = ($_a = $data->user) !== null && $_a != 0 ? $_a : null;

	return ['data' => ['user' => $user, 'session' => $session], 'error' => null];
}

function userResponse($data)
{
	$_a = '';
	$user = ($_a = $data->user) !== null && $_a !== 0 ? $_a : null;

	return ['data' => ['user' => $user], 'error' => null];
}

function ssoResponse($data)
{
	return ['data' => $data, 'error' => null];
}

function generateLinkResponse($data)
{
	$user = '';

	$properties = [
		'action_link'       => $data->action_link,
		'email_otp'         => $data->email_otp,
		'hashed_token'      => $data->hashed_token,
		'redirect_to'       => $data->redirect_to,
		'verification_type' => $data->verification_type,
	];

	return [
		'data' => [
			'properties' => $properties,
			'user'       => $user,
		],
		'error' => null,
	];
}

function hasSession($data)
{
	return $data->access_token && $data->refresh_token && $data->expires_in;
}
