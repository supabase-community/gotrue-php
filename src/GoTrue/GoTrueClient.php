<?php

namespace Supabase\GoTrue;

use Psr\Http\Message\ResponseInterface;
use Supabase\Util\AuthSessionMissingError;
use Supabase\Util\Constants;
use Supabase\Util\GoTrueError;
use Supabase\Util\Helpers;
use Supabase\Util\Request;
use Supabase\Util\Storage;

class GoTrueClient
{
    protected $stateChangeEmitters;
    protected $networkRetries = 0;
    protected $refreshingDeferred;
    protected $initializePromise;
    protected $detectSessionInUrl;
    protected $settings;
    protected $inMemorySession;
    protected $storageKey;
    protected $autoRefreshToken;
    protected $persistSession;
    protected $storage;
    public GoTrueAdminApi $admin;
    public GoTrueMFAApi $mfa;
    protected $url;
    protected $headers;

    public function __construct($reference_id, $api_key, $options = [], $domain = 'supabase.co', $scheme = 'https', $path = '/auth/v1')
    {
        $headers = ['Authorization' => "Bearer {$api_key}", 'apikey' => $api_key];
        $this->url = !empty($reference_id) ? "{$scheme}://{$reference_id}.{$domain}{$path}" : "{$scheme}://{$domain}{$path}";
        $this->settings = array_merge(Constants::getDefaultHeaders(), $options);
        $this->storageKey = $this->settings['storageKey'] ?? null;
        $this->autoRefreshToken = $this->settings['autoRefreshToken'];
        $this->persistSession = $this->settings['persistSession'];
        $this->detectSessionInUrl = $this->settings['detectSessionInUrl'] ?? false;

        if (!$this->url) {
            throw new \Exception('No URL provided');
        }

        $this->headers = $headers ?? null;
        $this->admin = new GoTrueAdminApi($reference_id, $api_key, [
            'url'     => $this->url,
            'headers' => $this->headers,
        ], $domain, $scheme, $path);

        $this->mfa = new GoTrueMFAApi($reference_id, $api_key, [
            'url'     => $this->url,
            'headers' => $this->headers,
        ], $domain, $scheme, $path);
        $this->storage = new Storage();
        $this->stateChangeEmitters = [];
        $this->initializePromise = $this->initialize();
    }

    public function initialize()
    {
        if (!$this->initializePromise) {
            $this->initializePromise = $this->_initialize();
        }

        return $this->initializePromise;
    }

    public function _initialize()
    {
        if ($this->initializePromise) {
            return $this->initializePromise;
        }

        if ($this->detectSessionInUrl && $this->_isImplicitGrantFlow()) {
            try {
                $data = $this->_getSessionFromUrl();
            } catch (\Exception $e) {
                return ['error' => $e];
            }

            $session = $data->session;

            $this->_saveSession($session);
            $this->_notifyAllSubscribers('SIGNED_IN', $session);

            if ($data->redirectType == 'recovery') {
                $this->_notifyAllSubscribers('PASSWORD_RECOVERY', $session);
            }

            return ['error' => null];
        }

        $this->_recoverAndRefresh();

        return ['error' => null];
    }

    public function __request($method, $url, $headers, $body = null): ResponseInterface
    {
        return Request::request($method, $url, $headers, $body);
    }

    private function _recoverAndRefresh()
    {
    }

    private function _removeSession()
    {
    }

    public function signUp($credentials)
    {
        try {
            $this->_removeSession();
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json']);
            $body = json_encode($credentials);
            if (isset($credentials['email'])) {
                $response = $this->__request('POST', $this->url.'/signup', $headers, $body);
            } elseif (isset($credentials['phone'])) {
                $response = $this->__request('POST', $this->url.'/signup', $headers, $body);
            } else {
                throw new GoTrueError('You must provide either an email or phone number and a password');
            }

            $status = $response->getStatusCode();
            $statusText = $response->getReasonPhrase();
            $error = null;

            if ($status != 200) {
                return ['data' => ['user' => null, 'session' => null], 'error' => $response];
            }

            $data = json_decode($response->getBody(), true);

            $session = isset($data['session']) ? $data['session'] : null;
            $user = $data;

            if (isset($data['session'])) {
                $this->_saveSession($session);
                $this->_notifyAllSubscribers('SIGNED_IN', $session);
            }

            return ['data' => ['user' => $user, 'session' => $session], 'error' => $error];
        } catch (\Exception $e) {
            if (GoTrueError::isGoTrueError($e)) {
                return ['data' => ['user' => null, 'session' => null], 'error' => $e];
            }

            throw $e;
        }
    }

    public function signInWithPassword($credentials)
    {
        try {
            $this->_removeSession();
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json']);
            $body = json_encode($credentials);
            if (isset($credentials['email'])) {
                $response = $this->__request('POST', $this->url.'/token?grant_type=password', $headers, $body);
            } elseif (isset($credentials['phone'])) {
                $response = $this->__request('POST', $this->url.'/token?grant_type=password', $headers, $body);
            } else {
                throw new GoTrueError('You must provide either an email or phone number and a password');
            }

            $status = $response->getStatusCode();
            $statusText = $response->getReasonPhrase();
            $error = null;

            if ($status != 200) {
                return ['data' => ['user' => null, 'session' => null], 'error' => $response];
            }

            $data = json_decode($response->getBody(), true);

            $session = isset($data['session']) ? $data['session'] : null;
            $user = $data;

            if (isset($data['session'])) {
                $this->_saveSession($session);
                $this->_notifyAllSubscribers('SIGNED_IN', $session);
            }

            return ['data' => $data, 'error' => $error];
        } catch (\Exception $e) {
            if (GoTrueError::isGoTrueError($e)) {
                return ['data' => ['user' => null, 'session' => null], 'error' => $e];
            }

            throw $e;
        }
    }

    public function signInWithOtp($credentials)
    {
        try {
            $this->_removeSession();
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json']);
            $body = json_encode($credentials);
            if (isset($credentials['email'])) {
                $response = $this->__request('POST', $this->url.'/otp', $headers, $body);
            } elseif (isset($credentials['phone'])) {
                $response = $this->__request('POST', $this->url.'/otp', $headers, $body);
            } else {
                throw new GoTrueError('You must provide either an email or phone number and a password');
            }

            $status = $response->getStatusCode();
            $statusText = $response->getReasonPhrase();
            $error = null;

            if ($status != 200) {
                return ['data' => ['user' => null, 'session' => null], 'error' => $response];
            }

            $data = json_decode($response->getBody(), true);

            $session = isset($data['session']) ? $data['session'] : null;
            $user = $data;

            if (isset($data['session'])) {
                $this->_saveSession($session);
                $this->_notifyAllSubscribers('SIGNED_IN', $session);
            }

            return ['data' => $data, 'error' => $error];
        } catch (\Exception $e) {
            if (GoTrueError::isGoTrueError($e)) {
                return ['data' => ['user' => null, 'session' => null], 'error' => $e];
            }

            throw $e;
        }
    }

    public function getUser($jwt = null)
    {
        try {
            if (!$jwt) {
                $sessionResult = $this->getSession();
                $sessionData = $sessionResult['data'];
                $sessionError = $sessionResult['error'];

                if ($sessionError) {
                    throw $sessionError;
                }

                // Default to Authorization header if there is no existing session
                $jwt = $sessionData['session']['access_token'] ?? null;
            }
            $this->headers['Authorization'] = "Bearer {$jwt}";
            $url = $this->url.'/user';
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('GET', $url, $headers);
            $user = json_decode($response->getBody(), true);

            return $user;

            /*return await _request($this->fetch, 'GET', $this->url.'/user', [
                'headers' => $this->headers,
                'jwt' => $jwt,
                'xform' => '_userResponse'
            ]);*/
        } catch (\Exception $e) {
            if (GoTrueError::isGoTrueError($e)) {
                return ['data' => ['user' => null], 'error' => $e];
            }

            throw $e;
        }
    }

    public function updateUser($attrs, $jwt = null, $options = [])
    {
        try {
            if (!$jwt) {
                $sessionResult = $this->getSession();
                $sessionData = $sessionResult['data'];
                $sessionError = $sessionResult['error'];

                if ($sessionError) {
                    throw $sessionError;
                }

                // Default to Authorization header if there is no existing session
                $jwt = $sessionData['session']['access_token'] ?? null;
            }
            $this->headers['Authorization'] = "Bearer {$jwt}";
            $redirectTo = isset($options['redirectTo']) ? "?redirect_to={$options['redirectTo']}" : null;
            $url = $this->url.'/user'.$redirectTo;
            $body = json_encode($attrs);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('PUT', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            if (GoTrueError::isGoTrueError($e)) {
                return ['data' => ['user' => null], 'error' => $e];
            }

            throw $e;
        }
    }

    public function setSession($currentSession = [])
    {
        try {
            if (empty($currentSession['access_token']) || empty($currentSession['refresh_token'])) {
                throw new AuthSessionMissingError();
            }

            $timeNow = time();
            $expiresAt = $timeNow;
            $hasExpired = true;
            $session = null;
            $payload = Helpers::decodeJWTPayload($currentSession['access_token']);
            if (!empty($payload['exp'])) {
                $expiresAt = $payload['exp'];
                $hasExpired = $expiresAt <= $timeNow ? true : false;
            }

            //return $hasExpired;

            if ($hasExpired) {
                $result = $this->_callRefreshToken($currentSession['refresh_token']);
                if (!empty($result['error'])) {
                    return ['data' => ['user' => null, 'session' => null], 'error' => $result['error']];
                }

                if (empty($result['session'])) {
                    return ['data' => ['user' => null, 'session' => null], 'error' => null];
                }
                $session = $result['session'];
            } else {
                $result = $this->getUser($currentSession['access_token']);
                if (!empty($result['error'])) {
                    throw $result['error'];
                }

                $session = [
                    'access_token'  => $currentSession['access_token'],
                    'refresh_token' => $currentSession['refresh_token'],
                    'user'          => $result['identities'],
                    'token_type'    => 'bearer',
                    'expires_in'    => $expiresAt - $timeNow,
                    'expires_at'    => $expiresAt,
                ];

                //$this->_saveSession($session);
                //$this->_notifyAllSubscribers('SIGNED_IN', $session);
            }

            return ['data' => ['user' => $session['user'], 'session' => $session], 'error' => null];
        } catch (\Exception $e) {
            if (isAuthError($e)) {
                return ['data' => ['session' => null, 'user' => null], 'error' => $e];
            }

            throw $e;
        }
    }

    public function signOut($access_token = null)
    {
        /**
         * Inside a browser context, `sign_out` will remove the logged in user from the
         * browser session and log them out - removing all items from localstorage and
         * then trigger a `"SIGNED_OUT"` event.
         * For server-side management, you can revoke all refresh tokens for a user by
         * passing a user's JWT through to `api.sign_out`.
         * There is no way to revoke a user's access token jwt until it expires.
         * It is recommended to set a shorter expiry on the jwt for this reason.
         */
        $session = $this->getSession($access_token);
        $access_token = $session ? $session['access_token'] : null;

        if ($access_token) {
            $this->admin->signOut($access_token);
        }

        //$this->_remove_session();
        //$this->_notify_all_subscribers("SIGNED_OUT", null);
    }

    public function listFactors($jwt)
    {
        try {
            $user = $this->getUser($jwt);

            $factors = isset($user['factors']) ? $user['factors'] : [];
            $totp = array_filter($factors, function ($factor) {
                return $factor['factor_type'] === 'totp' && $factor['status'] === 'verified';
            });

            return ['data' => ['all'=> $factors, 'totp' =>$totp], 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getSession($access_token)
    {
        return ['access_token'=>$access_token];
    }

    public function _getAuthenticatorAssuranceLevel($access_token)
    {
        $user = $this->getUser($access_token);

        try {
            $sessionResponse = $this->getUser($access_token);
            $session = $sessionResponse;
            $sessionError = isset($sessionResponse['error']) ? $sessionResponse['error'] : false;

            if ($sessionError) {
                $response['data'] = null;
                $response['error'] = $sessionError;

                return $response;
            }

            if (!$session) {
                $response['data']['currentLevel'] = null;
                $response['data']['nextLevel'] = null;
                $response['data']['currentAuthenticationMethods'] = [];
                $response['error'] = null;

                return $response;
            }

            $payload = Helpers::decodeJWTPayload($access_token);

            $currentLevel = null;

            if (isset($payload['aal'])) {
                $currentLevel = $payload['aal'];
            }

            $nextLevel = $currentLevel;

            $verifiedFactors = array_filter($session['user']['factors'], function ($factor) {
                return $factor['status'] === 'verified';
            });

            if (count($verifiedFactors) > 0) {
                $nextLevel = 'aal2';
            }

            $currentAuthenticationMethods = $payload['amr'] ?? [];

            $response['data']['currentLevel'] = $currentLevel;
            $response['data']['nextLevel'] = $nextLevel;
            $response['data']['currentAuthenticationMethods'] = $currentAuthenticationMethods;
            $response['error'] = null;
        } catch (\Exception $e) {
            $response['data'] = null;
            $response['error'] = $e->getMessage();
        }

        return $response;
    }

    private function _callRefreshToken(string $refreshToken)
    {
        try {
            if (!$refreshToken) {
                throw new AuthSessionMissingError();
            }
            $data = $this->_refreshAccessToken($refreshToken);
            $error = $data['error'];

            if ($error) {
                throw $error;
            }

            if (!$data['session']) {
                throw new AuthSessionMissingError();
            }

            //await $this->_saveSession($data['session']);
            //$this->_notifyAllSubscribers('TOKEN_REFRESHED', $data['session']);

            $result = ['session' => $data['session'], 'error' => null];

            return $result;
        } catch (\Exception $e) {
            if (isAuthError($e)) {
                $result = ['session' => null, 'error' => $e];

                return $result;
            }

            throw $e;
        }
    }

    public function _refreshAccessToken($refreshToken)
    {
        try {
            $url = $this->url.'/token?grant_type=refresh_token';
            $body = json_encode(['refresh_token' => $refreshToken]);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('POST', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['session' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
