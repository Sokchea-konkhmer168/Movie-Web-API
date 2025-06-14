<?php
class Auth {
    public static function authenticate() {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization header missing']);
            exit;
        }
        
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        // Simple token validation (in production, use JWT or proper session management)
        if (!self::validateToken($token)) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }
        
        return self::getUserFromToken($token);
    }
    
    public static function generateToken($userId) {
        // Simple token generation (in production, use JWT)
        return base64_encode($userId . ':' . time() . ':' . md5($userId . time()));
    }
    
    public static function validateToken($token) {
        // Simple token validation (in production, implement proper JWT validation)
        $decoded = base64_decode($token);
        $parts = explode(':', $decoded);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        $userId = $parts[0];
        $timestamp = $parts[1];
        $hash = $parts[2];
        
        // Check if token is not expired (24 hours)
        if (time() - $timestamp > 86400) {
            return false;
        }
        
        // Verify hash
        return $hash === md5($userId . $timestamp);
    }
    
    public static function getUserFromToken($token) {
        $decoded = base64_decode($token);
        $parts = explode(':', $decoded);
        
        if (count($parts) !== 3) {
            return null;
        }
        
        return ['id' => $parts[0]];
    }
    
    public static function requireAuth() {
        return self::authenticate();
    }
}
?>
