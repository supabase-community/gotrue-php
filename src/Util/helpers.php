<?php

class Helpers {
    public static function expiresAt($expiresIn) {
        return time() + $expiresIn;
    }

    public static function uuid() {
        return preg_replace_callback('/[xy]/', function($c) {
            $r = random_int(0, 15);
            $v = $c == 'x' ? $r : ($r & 0x3 | 0x8);
            return dechex($v);
        }, 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx');
    }

    public static function isRetryAble($res) {
        $body = $res->getBody();
        $status = $res->getStatusCode();

        return $status == 200 && json_encode($body)
    }

    public static function decodeBase64URL($str) {
        // $str = str_replace('_', '/', $str);
        // $str = str_replace('-', '+', $str);
        // $str = str_pad($str, strlen($str) % 4, '=', STR_PAD_RIGHT);
        return base64_decode($str);
    }

    /**  
     * Returns decoded JWT payload. 
     * @param {string} $jwt
     * 
     * */

    public static function decodeJWTPayload($jwt) {
        $parser = new Parser(new JoseEncoder());

        try {
            $token = $parser->parse($jwt);
            $claims = $token->getClaims();
            $payload = $claims->get('payload');
            $decoded = json_decode(Helpers::decodeBase64URL($payload));
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}