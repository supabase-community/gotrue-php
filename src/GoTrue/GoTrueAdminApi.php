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

    public function inviteUserByEmail($email, $opts)
    {
        return _request('POST', $this->url.'/invite', [
            'body'       => ['email' => $email, 'data' => $opts['data']],
            'headers'    => $this->headers,
            'redirectTo' => $opts['redirectTo'],
            'xform'      => _userResponse,
        ]);
    }

    public function generateLink($params)
    {
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
        return _request('POST', $this->url.'/admin/users', [
            'body'    => $attrs,
            'headers' => $this->headers,
            'xform'   => _userResponse,
        ]);
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

        return _request('GET', $this->url.'/admin/users', [
            'headers' => $this->headers,
        ]);
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
        return _request('PUT', $this->url.'/admin/users/'.$uid, [
            'body'    => $attrs,
            'headers' => $this->headers,
            'xform'   => _userResponse,
        ]);
    }

    public function deleteUserById($uid)
    {
        return _request('DELETE', $this->url.'/admin/users/'.$uid, [
            'headers' => $this->headers,
            'xform'   => _userResponse,
        ]);
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
