<?php

namespace Supabase\GoTrue;

use Psr\Http\Message\ResponseInterface;
use Supabase\Util\Constants;
use Supabase\Util\GoTrueError;
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
    protected GoTrueAdminApi $admin;
    protected $url;
    protected $headers;

    public function __construct($reference_id, $api_key, $options = [], $domain = 'supabase.co', $scheme = 'https', $path = '/auth/v1')
    {
        $headers = ['Authorization' => "Bearer {$api_key}", 'apikey'=>$api_key];
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
        $this->admin = new GoTrueAdminApi($reference_id, $api_key,[
            'url'     => $this->url,
            'headers' => $this->headers,
        ]);
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
}
