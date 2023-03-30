<?php

namespace Supabase\GoTrue;

use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;
use Supabase\Util\Request;

class GoTrueAdminApi
{
    protected $url;
    protected $headers;
    protected $mfa;

    public function __construct($reference_id, $api_key, $options = [], $domain = 'supabase.co', $scheme = 'https', $path = '/auth/v1')
    {
        $headers = ['Authorization' => "Bearer {$api_key}", 'apikey' => $api_key];
        $this->url = !empty($reference_id) ? "{$scheme}://{$reference_id}.{$domain}{$path}" : "{$scheme}://{$domain}{$path}";
        $this->headers = $headers ?? null;
        $this->mfa = [];
    }

    public function __request($method, $url, $headers, $body = null): ResponseInterface
    {
        return Request::request($method, $url, $headers, $body);
    }

    public function signOut($jwt)
    {
        $response = _request('POST', $this->url.'/admin/users/logout', [
            'headers'       => $this->headers,
            'jwt'           => $jwt,
            'noResolveJson' => true,
        ]);

        return ['data' => null, 'error' => null];
    }

    public function inviteUserByEmail($email, $options = [])
    {
        $redirectTo = isset($options['redirectTo']) ? ($options['redirectTo'] ? '?redirect_to=true' : null) : null;
        $data = ['email' => $email, 'data' => $options['data'] ?? null];

        try {
            $url = $this->url.'/invite'.$redirectTo;
            print_r($url);
            $body = json_encode($data);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('POST', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }

        return _request('POST', $this->url.'/invite', [
            'body'       => ['email' => $email, 'data' => $opts['data']],
            'headers'    => $this->headers,
            'redirectTo' => $opts['redirectTo'],
            'xform'      => _userResponse,
        ]);
    }

    public function generateLink($params, $options = [])
    {
        try {
            $redirectTo = isset($options['redirectTo']) ? "?redirect_to={$options['redirectTo']}" : null;
            if (isset($params['newEmail'])) {
                $params['new_email'] = $params['newEmail'];
                unset($params['newEmail']);
            }
            $data = $params;
            $url = $this->url.'/admin/generate_link'.$redirectTo;
            $body = json_encode($data);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('POST', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
        $body = array_merge([], $params->rest);
        $body = array_merge($body, $params->options);

        if (isset($params->rest->newEmail)) {
            $body->new_email = $params->rest->newEmail;
        }

        return _request('POST', $this->url.'/admin/generate_link', [
            'body'       => $body,
            'headers'    => $this->headers,
            'redirectTo' => $opts['redirectTo'],
            'xform'      => _generateLinkResponse,
        ]);
    }

    public function createUser($attrs)
    {
        try {
            $url = $this->url.'/admin/users';
            $body = json_encode($attrs);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('POST', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function listUsers($params = [])
    {
        try {
            $path = isset($params['page'], $params['perPage']) ? "?page={$params['page']}&per_page={$params['perPage']}" : '';
            $url = $this->url.'/admin/users'.$path;
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('GET', $url, $headers);
            $users = json_decode($response->getBody(), true);
            $total = $response->getHeader('x-total-count') ?? 0;
            $pagination = $response->getHeader('link') ? Psr7\Header::parse($response->getHeader('link')) : [];

            return ['data' => array_merge($users, $pagination), 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getUserById($uid)
    {
        try {
            $url = $this->url.'/admin/users/'.$uid;
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json']);
            $response = $this->__request('GET', $url, $headers);
            $data = json_decode($response->getBody(), true);

            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateUserById($uid, $attrs)
    {
        try {
            $url = $this->url.'/admin/users/'.$uid;
            $body = json_encode($attrs);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('PUT', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }

        return _request('PUT', $this->url.'/admin/users/'.$uid, [
            'body'    => $attrs,
            'headers' => $this->headers,
            'xform'   => _userResponse,
        ]);
    }

    public function deleteUser($uid, $shouldSoftDelete = false)
    {
        try {
            $url = $this->url."/admin/users/{$uid}";
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $body = json_encode(['should_soft_delete' => $shouldSoftDelete]);
            $response = $this->__request('DELETE', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function resetPasswordForEmail($email, $options = [])
    {
        try {
            $redirectTo = isset($options['redirectTo']) ? "?redirect_to={$options['redirectTo']}" : null;
            $captchaToken = isset($options['captchaToken']) ? $options['captchaToken'] : '';
            $data = [
                'email'                => $email,
                'gotrue_meta_security' => [
                    'captcha_token' => $captchaToken,
                ],
            ];
            $url = $this->url.'/recover'.$redirectTo;
            $body = json_encode($data);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('POST', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function _listFactors($params)
    {
        return _request('GET', $this->url.'/admin/users/'.$uid.'/factors', [
            'headers' => $this->headers,
        ]);
    }

    private function _deleteFactor($params)
    {
        return _request('DELETE', $this->url.'/admin/users/'.$userId.'/factors / $params->id', [
            'headers' => $this->headers,
        ]);
    }
}
