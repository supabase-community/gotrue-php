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

    public function __construct($options)
    {
        $this->settings = array_merge(Constants::getDefaultHeaders(), $options);

        echo $this->settings['url'];

        $this->storageKey = $this->settings['storageKey'] ?? null;
        $this->autoRefreshToken = $this->settings['autoRefreshToken'];
        $this->persistSession = $this->settings['persistSession'];
        $this->detectSessionInUrl = $this->settings['detectSessionInUrl'] ?? false;
        $this->url = $this->settings['url'];

        if (!$this->url) {
            throw new \Exception('No URL provided');
        }

        $this->headers = $this->settings['headers'] ?? null;
        $this->admin = new GoTrueAdminApi([
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
            } catch(\Exception $e) {
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

            if (isset($credentials['email'])) {
                $data = $this->__request('POST', $this->url.'/signup', [
                    'body' => [
                        'email'                => $credentials['email'],
                        'password'             => $credentials['password'],
                        'data'                 => isset($credentials['data']) ? $credentials['data'] : [],
                        'gotrue_meta_security' => [
                            //'captcha_token' => (isset($credentials->options->captchaToken) ? $credentials->options->captchaToken : null),
                        ],
                    ],
                    'headers' => $this->headers,
                ]);

                return sessionResponse($data);
            } elseif (isset($credentials->phone)) {
                $res = _request('POST', $this->url.'/signup', [
                    'body' => [
                        'phone'                => $credentials->phone,
                        'password'             => $credentials->password,
                        'data'                 => $credentials->data,
                        'gotrue_meta_security' => [
                            'captcha_token' => (isset($credentials->options->captchaToken) ? $credentials->options->captchaToken : null),
                        ],
                    ],
                    'headers' => $this->headers,
                ]);

                return sessionResponse($res);
            } else {
                throw new AuthInvalidCredentialsError('You must provide either an email or phone number and a password');
            }

            $error = $res->error;
            $data = $res->data;

            if ($error || !$data) {
                return ['data' => ['user' => null, 'session' => null], 'error' => $error];
            }

            $session = $data->session;
            $user = $data->user;

            if ($data->session) {
                $this->_saveSession($session);
                $this->_notifyAllSubscribers('SIGNED_IN', $session);
            }

            return ['data' => ['user' => $user, 'session' => $session], 'error' => $error];
        } catch(\Exception $e) {
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

            if (isset($credentials->email)) {
                $res = _request('POST', $this->url.'/token?grant_type=password', [
                    'body' => [
                        'email'                => $credentials->email,
                        'password'             => $credentials->password,
                        'data'                 => $credentials->data,
                        'gotrue_meta_security' => [
                            'captcha_token' => isset($credentials->options->captchaToken) ? $credentials->options->captchaToken : null,
                        ],
                    ],
                    'headers' => $this->headers,
                    'xform'   => _sessionResponse,
                ]);
            } elseif (isset($credentials->phone)) {
                $res = _request('POST', $this->url.'/token?grant_type=password', [
                    'body' => [
                        'phone'                => $credentials->phone,
                        'password'             => $credentials->password,
                        'data'                 => $credentials->data,
                        'gotrue_meta_security' => [
                            'captcha_token' => isset($credentials->options->captchaToken) ? $credentials->options->captchaToken : null,
                        ],
                    ],
                    'headers' => $this->headers,
                    'xform'   => _sessionResponse,
                ]);
            } else {
                throw new AuthInvalidCredentialsError('You must provide either an email or phone number and a password');
            }

            $session = $data->session;
            $user = $data->user;

            if ($data->session) {
                $this->_saveSession($session);
                $this->_notifyAllSubscribers('SIGNED_IN', $session);
            }

            return ['data' => ['user' => $user, 'session' => $session], 'error' => $error];
        } catch(\Exception $e) {
            if (isAuthError($e)) {
                return ['data' => ['user' => null, 'session' => null], 'error' => $e];
            }

            throw $e;
        }
    }
}
