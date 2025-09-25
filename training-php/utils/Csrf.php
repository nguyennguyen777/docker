<?php
/**
 * CSRF Protection Utility
 * Provides methods for generating and validating CSRF tokens
 */
class Csrf
{
    /**
     * Generate a new CSRF token
     * @return string
     */
    public static function generateToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }
    
    /**
     * Get current CSRF token
     * @return string|null
     */
    public static function getToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['csrf_token'] ?? null;
    }
    
    /**
     * Validate CSRF token
     * @param string $token
     * @param int $maxAge Maximum age in seconds (default: 3600 = 1 hour)
     * @return bool
     */
    public static function validateToken($token, $maxAge = 3600)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Check token age
        if (time() - $_SESSION['csrf_token_time'] > $maxAge) {
            self::clearToken();
            return false;
        }
        
        // Validate token
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Clear CSRF token
     */
    public static function clearToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
    }
    
    /**
     * Generate CSRF token HTML input
     * @return string
     */
    public static function getTokenInput()
    {
        $token = self::getToken() ?: self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Validate request method and CSRF token
     * @param string $method Required HTTP method (GET, POST, etc.)
     * @return bool
     */
    public static function validateRequest($method = 'POST')
    {
        // Check HTTP method
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            return false;
        }
        
        // Get token from request
        $token = null;
        if ($method === 'POST') {
            $token = $_POST['csrf_token'] ?? null;
        } elseif ($method === 'GET') {
            $token = $_GET['csrf_token'] ?? null;
        }
        
        if (!$token) {
            return false;
        }
        
        return self::validateToken($token);
    }
    
    /**
     * Check if request is AJAX and validate accordingly
     * @return bool
     */
    public static function validateAjaxRequest()
    {
        // Check if it's an AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            return false;
        }
        
        // For AJAX, check token in header or body
        $token = null;
        
        // Check X-CSRF-Token header
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }
        // Check in POST data
        elseif (isset($_POST['csrf_token'])) {
            $token = $_POST['csrf_token'];
        }
        // Check in JSON body
        else {
            $input = json_decode(file_get_contents('php://input'), true);
            $token = $input['csrf_token'] ?? null;
        }
        
        if (!$token) {
            return false;
        }
        
        return self::validateToken($token);
    }
}
