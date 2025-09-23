<?php
// API: POST username, password -> returns {token}
header('Content-Type: application/json');
require_once __DIR__.'/models/UserModel.php';
require_once __DIR__.'/utils/Jwt.php';

$secret = getenv('JWT_SECRET') ?: 'dev-secret';
$ttl = (int)(getenv('JWT_TTL') ?: 3600);
$redisHost = getenv('REDIS_HOST') ?: 'web-redis';
$redisPort = (int)(getenv('REDIS_PORT') ?: 6379);

$input = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

$userModel = new UserModel();
$user = $userModel->auth($username, $password);
if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'invalid_credentials']);
    exit;
}

$uid = $user[0]['id'];
$now = time();
$jti = bin2hex(random_bytes(8));
$payload = [
    'sub' => $uid,
    'name' => $username,
    'iat' => $now,
    'exp' => $now + $ttl,
    'jti' => $jti
];
$token = Jwt::sign($payload, $secret, 'HS256');

// store session in Redis keyed by jti
if (class_exists('Redis')) {
    $r = new Redis();
    $r->connect($redisHost, $redisPort, 1.5);
    $r->setex('sess:'.$jti, $ttl, json_encode(['uid' => $uid, 'name' => $username]));
}

echo json_encode(['token' => $token]);


