<?php
// API: GET -> returns CSRF token
header('Content-Type: application/json');
require_once __DIR__.'/utils/Csrf.php';

// Generate or get existing CSRF token
$token = Csrf::getToken() ?: Csrf::generateToken();

echo json_encode(['token' => $token]);
