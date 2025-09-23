<?php
// API: GET with Authorization: Bearer <token> -> returns user info
header('Content-Type: application/json');
require_once __DIR__.'/utils/Jwt.php';

$secret = getenv('JWT_SECRET') ?: 'dev-secret';
$redisHost = getenv('REDIS_HOST') ?: 'web-redis';
$redisPort = (int)(getenv('REDIS_PORT') ?: 6379);

$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s+(.*)$/i', $auth, $m)) {
    http_response_code(401);
    echo json_encode(['error' => 'missing_token']);
    exit;
}
$token = trim($m[1]);
$payload = Jwt::verify($token, $secret, 'HS256');
if (!$payload || ($payload['exp'] ?? 0) < time()) {
    http_response_code(401);
    echo json_encode(['error' => 'invalid_or_expired']);
    exit;
}

$ok = true;
if (class_exists('Redis')) {
    $r = new Redis();
    $r->connect($redisHost, $redisPort, 1.5);
    $session = $r->get('sess:'.($payload['jti'] ?? ''));
    if (!$session) $ok = false;
}
if (!$ok) {
    http_response_code(401);
    echo json_encode(['error' => 'session_revoked']);
    exit;
}

echo json_encode(['sub' => $payload['sub'] ?? null, 'name' => $payload['name'] ?? null]);


