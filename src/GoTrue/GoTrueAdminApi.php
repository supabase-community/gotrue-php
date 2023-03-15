<?php

namespace Supabase\GoTrue;

class GoTrueAdminApi
{
    public function __construct($opts)
    {
        $this->url = $opts['url'];
        $this->headers = isset($opts['headers']) && $opts['headers'];
        $this->mfa = [];
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

    public function listUsers()
    {
        return _request('GET', $this->url.'/admin/users', [
            'headers' => $this->headers,
        ]);
    }

    public function getUserById($uid)
    {
        return _request('GET', $this->url.'/admin/users/'.$uid, [
            'headers' => $this->headers,
            'xform'   => _userResponse,
        ]);
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
