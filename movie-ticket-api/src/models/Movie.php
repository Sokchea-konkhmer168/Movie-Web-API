<?php
require_once 'Database.php';

class Movie {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all movies
    public function getAll() {
        $sql = "SELECT * FROM movies ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    // Get movie by ID
    public function getById($id) {
        $sql = "SELECT * FROM movies WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Create new movie
    public function create($data) {
        $sql = "INSERT INTO movies (title, genre, rating, duration, release_date, description, poster_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['title'],
            $data['genre'],
            $data['rating'],
            $data['duration'],
            $data['release_date'],
            $data['description'] ?? null,
            $data['poster_url'] ?? null
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // Update movie
    public function update($id, $data) {
        $sql = "UPDATE movies SET title = ?, genre = ?, rating = ?, duration = ?, 
                release_date = ?, description = ?, poster_url = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        $params = [
            $data['title'],
            $data['genre'],
            $data['rating'],
            $data['duration'],
            $data['release_date'],
            $data['description'] ?? null,
            $data['poster_url'] ?? null,
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    // Delete movie
    public function delete($id) {
        $sql = "DELETE FROM movies WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    // Search movies by title
    public function searchByTitle($title) {
        $sql = "SELECT * FROM movies WHERE title LIKE ? ORDER BY created_at DESC";
        $searchTerm = '%' . $title . '%';
        return $this->db->fetchAll($sql, [$searchTerm]);
    }
    
    // Search movies by ID (exact match)
    public function searchById($id) {
        return $this->getById($id);
    }
    
    // Advanced search - search by title, genre, or description
    public function search($query) {
        $sql = "SELECT * FROM movies 
                WHERE title LIKE ? 
                OR genre LIKE ? 
                OR description LIKE ? 
                ORDER BY 
                    CASE 
                        WHEN title LIKE ? THEN 1
                        WHEN genre LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END,
                    created_at DESC";
        
        $searchTerm = '%' . $query . '%';
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
        return $this->db->fetchAll($sql, $params);
    }
    
    // Get movies by genre
    public function getByGenre($genre) {
        $sql = "SELECT * FROM movies WHERE genre = ? ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, [$genre]);
    }
    
    // Get movies by rating
    public function getByRating($rating) {
        $sql = "SELECT * FROM movies WHERE rating = ? ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, [$rating]);
    }
    
    // Get movies released after a certain date
    public function getByReleaseDate($date, $operator = '>=') {
        $allowedOperators = ['=', '>', '>=', '<', '<=', '!='];
        if (!in_array($operator, $allowedOperators)) {
            $operator = '>=';
        }
        
        $sql = "SELECT * FROM movies WHERE release_date $operator ? ORDER BY release_date DESC";
        return $this->db->fetchAll($sql, [$date]);
    }
}
?>
