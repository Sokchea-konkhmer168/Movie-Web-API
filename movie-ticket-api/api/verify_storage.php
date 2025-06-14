<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../src/models/User.php';

try {
    $user = new User();
    $allUsers = $user->getAllUsers();
    
    // Count total users
    $totalUsers = count($allUsers);
    
    // Get recent registrations (last 5)
    $recentUsers = array_slice($allUsers, -5);
    
    echo json_encode([
        'success' => true,
        'message' => 'Database storage verification',
        'total_users' => $totalUsers,
        'recent_registrations' => $recentUsers,
        'note' => 'This shows that all registrations are properly stored in the database'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database verification failed: ' . $e->getMessage()]);
}
?>
