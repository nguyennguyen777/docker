<?php

class Jwt
{
    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }

    public static function sign(array $payload, string $secret, string $alg = 'HS256'): string
    {
        $header = ['typ' => 'JWT', 'alg' => $alg];
        $segments = [
            self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES)),
            self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES)),
        ];
        $signingInput = implode('.', $segments);
        $signature = self::hmac($signingInput, $secret, $alg);
        $segments[] = self::base64UrlEncode($signature);
        return implode('.', $segments);
    }

    public static function verify(string $jwt, string $secret, string $alg = 'HS256'): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;
        [$h, $p, $s] = $parts;
        $payload = json_decode(self::base64UrlDecode($p), true);
        if (!is_array($payload)) return null;
        $expected = self::base64UrlEncode(self::hmac($h.'.'.$p, $secret, $alg));
        if (!hash_equals($expected, $s)) return null;
        return $payload;
    }

    private static function hmac(string $data, string $secret, string $alg): string
    {
        $algo = match ($alg) {
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
            default => 'sha256',
        };
        return hash_hmac($algo, $data, $secret, true);
    }
}



