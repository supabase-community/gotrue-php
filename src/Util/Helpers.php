<?php

namespace Supabase\Util;

class Helpers
{
	public static function expiresAt($expiresIn)
	{
		return time() + $expiresIn;
	}

	public static function uuid()
	{
		return preg_replace_callback('/[xy]/', function ($c) {
			$r = random_int(0, 15);
			$v = $c == 'x' ? $r : ($r & 0x3 | 0x8);

			return dechex($v);
		}, 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx');
	}

	public static function isRetryAble($res)
	{
		$body = $res->getBody();
		$status = $res->getStatusCode();

		return $status == 200 && json_encode($body);
	}

	public function decodeBase64URL($str)
	{
		return base64_decode($str);
	}

	/**
	 * Returns decoded JWT payload.
	 *
	 * @param {string} $jwt
	 *
	 * */
	public static function decodeJWTPayload($token)
	{
		// Regex checks for base64url format
		$base64UrlRegex = '/^([a-z0-9_-]{4})*($|[a-z0-9_-]{3}=?$|[a-z0-9_-]{2}(==)?$)/i';

		$parts = explode('.', $token);

		if (count($parts) !== 3) {
			throw new \Exception('JWT is not valid: not a JWT structure');
		}

		if (! preg_match($base64UrlRegex, $parts[1])) {
			throw new \Exception('JWT is not valid: payload is not in base64url format');
		}

		$base64Url = $parts[1];

		return json_decode(Helpers::base64url_decode($base64Url), true);
	}

	public static function generatePKCEVerifier()
	{
		$verifierLength = 56;
		$array = [];

		if (function_exists('random_bytes')) {
			$array = unpack('C*', random_bytes($verifierLength));
		} elseif (function_exists('openssl_random_pseudo_bytes')) {
			$array = unpack('C*', openssl_random_pseudo_bytes($verifierLength));
		} else {
			$charSet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~';
			$charSetLen = strlen($charSet);
			$verifier = '';
			for ($i = 0; $i < $verifierLength; $i++) {
				$verifier .= $charSet[mt_rand(0, $charSetLen - 1)];
			}

			return $verifier;
		}

		return implode('', array_map('dechex', $array));
	}

	public static function generatePKCEChallenge($verifier)
	{
		if (! function_exists('hash')) {
			trigger_error('hash() function is not supported. Code challenge method will default to use plain instead of sha256.', E_USER_WARNING);

			return $verifier;
		}
		$hashed = hash('sha256', $verifier, true);

		return base64_encode($hashed);
	}

	public static function base64url_decode($base64Url)
	{
		$base64 = strtr($base64Url, '-_', '+/');

		return base64_decode($base64);
	}
}
