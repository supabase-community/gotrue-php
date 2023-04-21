<?php

namespace Supabase\GoTrue;

use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;
use Supabase\Util\Request;
use Supabase\Util\Constants;


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
        $this->headers = array_merge(Constants::getDefaultHeaders(), $headers);
        $this->mfa = [];
    }

    public function __request($method, $url, $headers, $body = null): ResponseInterface
    {
        return Request::request($method, $url, $headers, $body);
    }

    /**
     * Removes a logged-in session.
     * @param string $jwt A valid, logged-in JWT.
     */
    public function signOut($jwt)
    {
        try {
            $url = $this->url . '/logout';
            $this->headers['Authorization'] = "Bearer {$jwt}";
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json']);
            $response = $this->__request('POST', $url, $headers);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * Sends an invite link to an email address.
     * @param string email The email address of the user.
     * @param string options.redirectTo A URL or mobile deeplink to send the user to after they are confirmed.
     * @param string options.data Optional user metadata
     */
    public function inviteUserByEmail($email, $options = [])
    {
        $redirectTo = isset($options['redirectTo']) ? ($options['redirectTo'] ? '?redirect_to=true' : null) : null;
        $data = ['email' => $email, 'data' => $options['data'] ?? null];

        try {
            $url = $this->url . '/invite' . $redirectTo;
            $body = json_encode($data);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('POST', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Generates email links and OTPs to be sent via a custom email provider.
     * @param string email The user's email.
     * @param string options.password User password. For signup only.
     * @param array options.data Optional user metadata. For signup only.
     * @param string options.redirectTo The redirect url which should be appended to the generated link
     */
    public function generateLink($params, $options = [])
    {
        try {
            $redirectTo = isset($options['redirectTo']) ? "?redirect_to={$options['redirectTo']}" : null;
            if (isset($params['newEmail'])) {
                $params['new_email'] = $params['newEmail'];
                unset($params['newEmail']);
            }
            $data = $params;
            $url = $this->url . '/admin/generate_link' . $redirectTo;
            $body = json_encode($data);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('POST', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // User Admin API
    /**
     * Creates a new user.
     * This function should only be called on a server. Never expose your `service_role` key in the browser.
     */
    public function createUser($attrs)
    {
        try {
            $url = $this->url . '/admin/users';
            $body = json_encode($attrs);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('POST', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get a list of users.
     *
     * This function should only be called on a server. Never expose your `service_role` key in the browser.
     * @param params An object which supports `page` and `perPage` as numbers, to alter the paginated results.
     */
    public function listUsers($params = [])
    {
        try {
            $path = isset($params['page'], $params['perPage']) ? "?page={$params['page']}&per_page={$params['perPage']}" : '';
            $url = $this->url . '/admin/users' . $path;
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

    /**
     * Get user by id.
     *
     * @param uid The user's unique identifier
     *
     * This function should only be called on a server. Never expose your `service_role` key in the browser.
     */
    public function getUserById($uid)
    {
        try {
            $url = $this->url . '/admin/users/' . $uid;
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json']);
            $response = $this->__request('GET', $url, $headers);
            $data = json_decode($response->getBody(), true);

            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Updates the user data.
     *
     * @param attributes The data you want to update.
     *
     * This function should only be called on a server. Never expose your `service_role` key in the browser.
     */
    public function updateUserById($uid, $attrs)
    {
        try {
            $url = $this->url . '/admin/users/' . $uid;
            $body = json_encode($attrs);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('PUT', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }

        return _request('PUT', $this->url . '/admin/users/' . $uid, [
            'body'    => $attrs,
            'headers' => $this->headers,
            'xform'   => _userResponse,
        ]);
    }

    /**
     * Delete a user. Requires a `service_role` key.
     *
     * @param id The user id you want to remove.
     * @param shouldSoftDelete If true, then the user will be soft-deleted from the auth schema.
     * Defaults to false for backward compatibility.
     *
     * This function should only be called on a server. Never expose your `service_role` key in the browser.
     */
    public function deleteUser($uid, $shouldSoftDelete = false)
    {
        try {
            $url = $this->url . "/admin/users/{$uid}";
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
            $url = $this->url . '/recover' . $redirectTo;
            $body = json_encode($data);
            $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
            $response = $this->__request('POST', $url, $headers, $body);
            $data = json_decode($response->getBody(), true);

            return ['data' => $data, 'error' => null];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function _listFactors($uid)
    {
        $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
        $response = $this->__request('GET', $this->url . '/admin/users/' . $uid . '/factors', $headers);
        $data = json_decode($response->getBody(), true);

        return ['data' => $data, 'error' => null];
    }

    public function _deleteFactor($uid, $factorId)
    {
        $headers = array_merge($this->headers, ['Content-Type' => 'application/json', 'noResolveJson' => true]);
        $response = $this->__request('DELETE', $this->url . '/admin/users/' . $uid . '/factors/' . $factorId, $headers);
        $data = json_decode($response->getBody(), true);

        return ['data' => $data, 'error' => null];
    }
}
