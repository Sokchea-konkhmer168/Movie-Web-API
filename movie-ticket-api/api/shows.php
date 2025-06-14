<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../src/models/Show.php';
require_once '../src/Auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode('/', trim($uri, '/'));

$showId = isset($uriParts[2]) ? (int)$uriParts[2] : null;

$show = new Show();

try {
    switch ($method) {
        case 'GET':
            if ($showId) {
                // Get specific show
                $result = $show->getById($showId);
                if ($result) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Show not found']);
                }
            } else {
                // Check if filtering by movie
                $movieId = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : null;
                
                if ($movieId) {
                    $result = $show->getByMovie($movieId);
                } else {
                    $result = $show->getUpcoming(); // Only show upcoming shows by default
                }
                
                echo json_encode(['success' => true, 'data' => $result]);
            }
            break;
            
        case 'POST':
            // Authentication required for creating shows
            Auth::requireAuth();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['movie_id', 'hall_id', 'show_time', 'price'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['error' => "Field '$field' is required"]);
                    exit;
                }
            }
            
            // Validate show time format
            $showTime = DateTime::createFromFormat('Y-m-d H:i:s', $data['show_time']);
            if (!$showTime) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid show time format. Use Y-m-d H:i:s']);
                exit;
            }
            
            $showId = $show->create($data);
            echo json_encode(['success' => true, 'message' => 'Show created successfully', 'id' => $showId]);
            break;
            
        case 'PUT':
            // Authentication required for updating shows
            Auth::requireAuth();
            
            if (!$showId) {
                http_response_code(400);
                echo json_encode(['error' => 'Show ID is required']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $updated = $show->update($showId, $data);
            if ($updated) {
                echo json_encode(['success' => true, 'message' => 'Show updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Show not found']);
            }
            break;
            
        case 'DELETE':
            // Authentication required for deleting shows
            Auth::requireAuth();
            
            if (!$showId) {
                http_response_code(400);
                echo json_encode(['error' => 'Show ID is required']);
                exit;
            }
            
            $deleted = $show->delete($showId);
            if ($deleted) {
                echo json_encode(['success' => true, 'message' => 'Show deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Show not found']);
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
