<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../src/models/Booking.php';
require_once '../src/Auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode('/', trim($uri, '/'));

$bookingId = isset($uriParts[2]) ? (int)$uriParts[2] : null;

$booking = new Booking();

try {
    switch ($method) {
        case 'GET':
            // Authentication required
            $authUser = Auth::requireAuth();
            
            if ($bookingId) {
                // Get specific booking
                $result = $booking->getById($bookingId);
                if ($result && $result['user_id'] == $authUser['id']) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Booking not found']);
                }
            } else {
                // Get user's bookings
                $result = $booking->getByUser($authUser['id']);
                echo json_encode(['success' => true, 'data' => $result]);
            }
            break;
            
        case 'POST':
            // Authentication required for creating bookings
            $authUser = Auth::requireAuth();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['show_id', 'seat_number'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['error' => "Field '$field' is required"]);
                    exit;
                }
            }
            
            // Add user ID to booking data
            $data['user_id'] = $authUser['id'];
            
            $bookingId = $booking->create($data);
            if ($bookingId) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Booking created successfully', 
                    'booking_id' => $bookingId
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Seat not available or booking failed']);
            }
            break;
            
        case 'DELETE':
            // Authentication required for cancelling bookings
            $authUser = Auth::requireAuth();
            
            if (!$bookingId) {
                http_response_code(400);
                echo json_encode(['error' => 'Booking ID is required']);
                exit;
            }
            
            $cancelled = $booking->cancel($bookingId, $authUser['id']);
            if ($cancelled) {
                echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Booking not found or cannot be cancelled']);
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
