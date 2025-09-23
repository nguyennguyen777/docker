<?php
// Simple Redis demo: set and get a key
header('Content-Type: text/plain; charset=utf-8');

$host = getenv('REDIS_HOST') ?: 'web-redis';
$port = (int)(getenv('REDIS_PORT') ?: 6379);

if (!class_exists('Redis')) {
    http_response_code(500);
    echo "Redis extension not loaded";
    exit;
}

$r = new Redis();
try {
    $r->connect($host, $port, 1.5);
} catch (Throwable $e) {
    http_response_code(500);
    echo "Cannot connect to Redis at {$host}:{$port}: ".$e->getMessage();
    exit;
}

$key = isset($_GET['key']) ? $_GET['key'] : 'demo:key';
$val = isset($_GET['value']) ? $_GET['value'] : null;

if ($val !== null) {
    $r->set($key, $val);
    echo "SET {$key}={$val}\n";
}

$got = $r->get($key);
echo "GET {$key} => ".var_export($got, true)."\n";



