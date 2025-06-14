<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../src/models/Movie.php';
require_once '../src/Auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode('/', trim($uri, '/'));

// Get movie ID if provided
$movieId = isset($uriParts[2]) ? (int)$uriParts[2] : null;

$movie = new Movie();

try {
    switch ($method) {        case 'GET':
            if ($movieId) {
                // Get specific movie by ID
                $result = $movie->getById($movieId);
                if ($result) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Movie not found']);
                }
            } else {
                // Handle different types of searches
                $search = $_GET['search'] ?? null;
                $id = $_GET['id'] ?? null;
                $title = $_GET['title'] ?? null;
                $genre = $_GET['genre'] ?? null;
                $rating = $_GET['rating'] ?? null;
                $releaseDate = $_GET['release_date'] ?? null;
                $dateOperator = $_GET['date_operator'] ?? '>=';
                  if ($id) {
                    // Search by specific ID
                    $result = $movie->searchById((int)$id);
                    if ($result) {
                        echo json_encode(['success' => true, 'data' => [$result], 'search_type' => 'id', 'query' => $id]);
                    } else {
                        echo json_encode(['success' => true, 'data' => [], 'message' => 'No movie found with ID: ' . $id, 'search_type' => 'id']);
                    }
                }elseif ($title) {
                    // Search by title
                    $result = $movie->searchByTitle($title);
                    echo json_encode(['success' => true, 'data' => $result, 'search_type' => 'title', 'query' => $title]);
                } elseif ($search) {
                    // General search (title, genre, description)
                    $result = $movie->search($search);
                    echo json_encode(['success' => true, 'data' => $result, 'search_type' => 'general', 'query' => $search]);
                } elseif ($genre) {
                    // Search by genre
                    $result = $movie->getByGenre($genre);
                    echo json_encode(['success' => true, 'data' => $result, 'search_type' => 'genre', 'query' => $genre]);
                } elseif ($rating) {
                    // Search by rating
                    $result = $movie->getByRating($rating);
                    echo json_encode(['success' => true, 'data' => $result, 'search_type' => 'rating', 'query' => $rating]);
                } elseif ($releaseDate) {
                    // Search by release date
                    $result = $movie->getByReleaseDate($releaseDate, $dateOperator);
                    echo json_encode(['success' => true, 'data' => $result, 'search_type' => 'release_date', 'query' => $releaseDate, 'operator' => $dateOperator]);
                } else {
                    // Get all movies
                    $result = $movie->getAll();
                    echo json_encode(['success' => true, 'data' => $result, 'search_type' => 'all']);
                }
            }
            break;
            
        case 'POST':
            // Authentication required for creating movies
            Auth::requireAuth();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['title', 'genre', 'rating', 'duration', 'release_date'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['error' => "Field '$field' is required"]);
                    exit;
                }
            }
            
            $movieId = $movie->create($data);
            echo json_encode(['success' => true, 'message' => 'Movie created successfully', 'id' => $movieId]);
            break;
            
        case 'PUT':
            // Authentication required for updating movies
            Auth::requireAuth();
            
            if (!$movieId) {
                http_response_code(400);
                echo json_encode(['error' => 'Movie ID is required']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $updated = $movie->update($movieId, $data);
            if ($updated) {
                echo json_encode(['success' => true, 'message' => 'Movie updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Movie not found']);
            }
            break;
            
        case 'DELETE':
            // Authentication required for deleting movies
            Auth::requireAuth();
            
            if (!$movieId) {
                http_response_code(400);
                echo json_encode(['error' => 'Movie ID is required']);
                exit;
            }
            
            $deleted = $movie->delete($movieId);
            if ($deleted) {
                echo json_encode(['success' => true, 'message' => 'Movie deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Movie not found']);
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
