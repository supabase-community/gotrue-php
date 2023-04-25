<?php

namespace Supabase\Util;

class Constants
{
	public static $VERSION = '0.0.1';
	public static $GOTRUE_URL = 'http://localhost:9999';
	public static $STORAGE_KEY = 'supabase.auth.token';
	public static $AUDIENCE = '';
	public static $EXPIRY_MARGIN = 10;
	public static $NETWORK_FAILURE = ['MAX_RETIRES' => 10, 'RETRY_INTERVAL' => 2];

	public static function getDefaultHeaders()
	{
		return ['X-Client-Info' => 'gotrue-php/'.self::$VERSION];
	}

	public static function getDefaultOptions()
	{
		return [
			'url'                => self::$GOTRUE_URL,
			'storageKey'         => self::$STORAGE_KEY,
			'autoRefreshToken'   => true,
			'persistSession'     => true,
			'detectSessionInUrl' => true,
			'headers'            => self::$DEFAULT_HEADERS,
		];
	}
}
