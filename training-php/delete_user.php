<?php
require_once __DIR__.'/utils/Csrf.php';
require_once 'models/UserModel.php';
$userModel = new UserModel();

// Only accept POST with valid CSRF token
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo 'CSRF validation failed';
    exit;
}

$id = !empty($_POST['id']) ? $_POST['id'] : null;
if ($id !== null) {
    $userModel->deleteUserById($id);
}
header('location: list_users.php');
?>