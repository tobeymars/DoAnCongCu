<?php
class JWTHelper {
    private const SECRET_KEY = "abc123";
    public static function createToken($payload, $expiry = 86400) {
        $header = base64_encode(json_encode(["alg" => "HS256", "typ" => "JWT"]));
        $payload["exp"] = time() + $expiry;
        $payload = base64_encode(json_encode($payload));
        $signature = base64_encode(hash_hmac('sha256', "$header.$payload", self::SECRET_KEY, true));
        return "$header.$payload.$signature";
    }
    public static function verifyToken($token) {
        if (!$token) return null;

        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        list($header, $payload, $signature) = $parts;
        $expectedSignature = base64_encode(hash_hmac('sha256', "$header.$payload", self::SECRET_KEY, true));

        if ($signature !== $expectedSignature) return null;

        $payloadData = json_decode(base64_decode($payload), true);
        if ($payloadData["exp"] < time()) return null;

        return $payloadData;
    }
}
?>
