<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../src/models/User.php';
require_once '../src/Auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode('/', trim($uri, '/'));

$user = new User();

try {
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($uriParts[2]) && $uriParts[2] === 'login') {
                // Login endpoint
                if (!isset($data['email']) || !isset($data['password'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Email and password are required']);
                    exit;
                }
                
                $loginResult = $user->login($data['email'], $data['password']);
                if ($loginResult) {
                    $token = Auth::generateToken($loginResult['id']);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Login successful',
                        'token' => $token,
                        'user' => [
                            'id' => $loginResult['id'],
                            'name' => $loginResult['name'],
                            'email' => $loginResult['email']
                        ]
                    ]);
                } else {
                    http_response_code(401);
                    echo json_encode(['error' => 'Invalid email or password']);
                }
                
            } elseif (isset($uriParts[2]) && $uriParts[2] === 'register') {
                // Register endpoint
                $required = ['name', 'email', 'password'];
                foreach ($required as $field) {
                    if (!isset($data[$field]) || empty($data[$field])) {
                        http_response_code(400);
                        echo json_encode(['error' => "Field '$field' is required"]);
                        exit;
                    }
                }
                
                // Validate email format
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid email format']);
                    exit;
                }
                
                // Validate password length
                if (strlen($data['password']) < 6) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Password must be at least 6 characters']);
                    exit;
                }
                
                $userId = $user->register($data);
                if ($userId) {
                    $token = Auth::generateToken($userId);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registration successful',
                        'token' => $token,
                        'user_id' => $userId
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Email already exists']);
                }
                
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint not found']);
            }
            break;
            
        case 'GET':
            // Get user profile (requires authentication)
            $authUser = Auth::requireAuth();
            
            if (isset($uriParts[2]) && $uriParts[2] === 'profile') {
                $profile = $user->getById($authUser['id']);
                if ($profile) {
                    echo json_encode(['success' => true, 'data' => $profile]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'User not found']);
                }
            } elseif (isset($uriParts[2]) && $uriParts[2] === 'bookings') {
                $bookings = $user->getBookings($authUser['id']);
                echo json_encode(['success' => true, 'data' => $bookings]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint not found']);
            }
            break;
            
        case 'PUT':
            // Update user profile (requires authentication)
            $authUser = Auth::requireAuth();
            
            if (isset($uriParts[2]) && $uriParts[2] === 'profile') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                $updated = $user->update($authUser['id'], $data);
                if ($updated) {
                    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Failed to update profile']);
                }
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint not found']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}
?>
