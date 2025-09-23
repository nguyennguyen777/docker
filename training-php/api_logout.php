<?php
// API: POST Authorization: Bearer <token> -> revoke session
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
if (!$payload) {
    http_response_code(401);
    echo json_encode(['error' => 'invalid_token']);
    exit;
}

if (class_exists('Redis')) {
    $r = new Redis();
    $r->connect($redisHost, $redisPort, 1.5);
    $r->del('sess:'.($payload['jti'] ?? ''));
}

echo json_encode(['ok' => true]);


