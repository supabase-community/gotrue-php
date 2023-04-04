<?php

namespace Supabase\GoTrue;

use Psr\Http\Message\ResponseInterface;
use Supabase\Util\Request;

class GoTrueMFAApi
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

    public function enroll($params = [], $jwt)
    {
        try {
            $url = $this->url.'/factors';
            $this->headers['Authorization'] = "Bearer {$jwt}";
            $body = json_encode($params);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('POST', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function unenroll($params = [], $jwt)
    {
        try {
            $url = $this->url.'/factors';
            $this->headers['Authorization'] = "Bearer {$jwt}";
            $body = json_encode($params);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $this->__request('DELETE', $url, $headers, $body);

            return ['data' => null, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
