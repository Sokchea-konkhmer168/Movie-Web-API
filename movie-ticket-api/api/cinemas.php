<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../src/models/Cinema.php';
require_once '../src/Auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode('/', trim($uri, '/'));

// Get cinema ID if provided
$cinemaId = isset($_GET['id']) ? (int)$_GET['id'] : null;

$cinema = new Cinema();

try {
    switch ($method) {
        case 'GET':
            if ($cinemaId) {
                // Get specific cinema with halls
                $result = $cinema->getByIdWithHalls($cinemaId);
                if ($result) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Cinema not found']);
                }
            } else {
                // Get all cinemas
                $result = $cinema->getAllWithHalls();
                echo json_encode(['success' => true, 'data' => $result, 'total' => count($result)]);
            }
            break;
            
        case 'POST':
            // Authentication required for creating cinemas
            Auth::requireAuth();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['name', 'location', 'number_of_halls', 'address'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['error' => "Field '$field' is required"]);
                    exit;
                }
            }
            
            $cinemaId = $cinema->create($data);
            echo json_encode(['success' => true, 'message' => 'Cinema created successfully', 'id' => $cinemaId]);
            break;
            
        case 'PUT':
            // Authentication required for updating cinemas
            Auth::requireAuth();
            
            if (!$cinemaId) {
                http_response_code(400);
                echo json_encode(['error' => 'Cinema ID is required']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $updated = $cinema->update($cinemaId, $data);
            if ($updated) {
                echo json_encode(['success' => true, 'message' => 'Cinema updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Cinema not found']);
            }
            break;
            
        case 'DELETE':
            // Authentication required for deleting cinemas
            Auth::requireAuth();
            
            if (!$cinemaId) {
                http_response_code(400);
                echo json_encode(['error' => 'Cinema ID is required']);
                exit;
            }
            
            $deleted = $cinema->delete($cinemaId);
            if ($deleted) {
                echo json_encode(['success' => true, 'message' => 'Cinema deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Cinema not found']);
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
