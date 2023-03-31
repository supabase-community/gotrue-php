<?php
namespace Supabase\Util;
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

        return $status == 200 && json_encode($body);
    }

    public function decodeBase64URL($str) {
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

    public static function decodeJWTPayloadtest($jwt) {

        return null;
        $parser = new Parser(new JoseEncoder());

        try {
            $token = $parser->parse($jwt);
            $claims = $token->getClaims();
            $payload = $claims->get('payload');
            $decoded = json_decode(Helpers::decodeBase64URL($payload));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

   public static function  decodeJWTPayload($token) {
        // Regex checks for base64url format
        $base64UrlRegex = '/^([a-z0-9_-]{4})*($|[a-z0-9_-]{3}=?$|[a-z0-9_-]{2}(==)?$)/i';
      
        $parts = explode('.', $token);
      
        if (count($parts) !== 3) {
          throw new \Exception('JWT is not valid: not a JWT structure');
        }
      
        if (!preg_match($base64UrlRegex, $parts[1])) {
          throw new \Exception('JWT is not valid: payload is not in base64url format');
        }
      
        $base64Url = $parts[1];
        return json_decode(Helpers::base64url_decode($base64Url), true);
      }
      
      public static function base64url_decode($base64Url) {
        $base64 = strtr($base64Url, '-_', '+/');
        return base64_decode($base64);
      }
}