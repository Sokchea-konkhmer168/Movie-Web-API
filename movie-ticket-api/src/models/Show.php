<?php
require_once '../Database.php';

class Show {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all shows with movie and cinema details
    public function getAll() {
        $sql = "SELECT s.*, m.title as movie_title, m.genre, m.rating, m.duration,
                c.name as cinema_name, c.location, h.hall_number, h.capacity
                FROM shows s
                JOIN movies m ON s.movie_id = m.id
                JOIN halls h ON s.hall_id = h.id
                JOIN cinemas c ON h.cinema_id = c.id
                ORDER BY s.show_time ASC";
        return $this->db->fetchAll($sql);
    }
    
    // Get shows by movie ID
    public function getByMovie($movieId) {
        $sql = "SELECT s.*, c.name as cinema_name, c.location, h.hall_number, h.capacity
                FROM shows s
                JOIN halls h ON s.hall_id = h.id
                JOIN cinemas c ON h.cinema_id = c.id
                WHERE s.movie_id = ?
                ORDER BY s.show_time ASC";
        return $this->db->fetchAll($sql, [$movieId]);
    }
    
    // Get show by ID
    public function getById($id) {
        $sql = "SELECT s.*, m.title as movie_title, m.genre, m.rating, m.duration,
                c.name as cinema_name, c.location, h.hall_number, h.capacity
                FROM shows s
                JOIN movies m ON s.movie_id = m.id
                JOIN halls h ON s.hall_id = h.id
                JOIN cinemas c ON h.cinema_id = c.id
                WHERE s.id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Create new show
    public function create($data) {
        $sql = "INSERT INTO shows (movie_id, hall_id, show_time, price, available_seats) 
                VALUES (?, ?, ?, ?, ?)";
        
        // Get hall capacity for available seats
        $hall = $this->db->fetch("SELECT capacity FROM halls WHERE id = ?", [$data['hall_id']]);
        $availableSeats = $hall ? $hall['capacity'] : $data['available_seats'];
        
        $params = [
            $data['movie_id'],
            $data['hall_id'],
            $data['show_time'],
            $data['price'],
            $availableSeats
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // Update show
    public function update($id, $data) {
        $sql = "UPDATE shows SET movie_id = ?, hall_id = ?, show_time = ?, price = ?, 
                updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $params = [
            $data['movie_id'],
            $data['hall_id'],
            $data['show_time'],
            $data['price'],
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    // Delete show
    public function delete($id) {
        $sql = "DELETE FROM shows WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    // Get upcoming shows
    public function getUpcoming() {
        $sql = "SELECT s.*, m.title as movie_title, m.genre, m.rating,
                c.name as cinema_name, c.location, h.hall_number
                FROM shows s
                JOIN movies m ON s.movie_id = m.id
                JOIN halls h ON s.hall_id = h.id
                JOIN cinemas c ON h.cinema_id = c.id
                WHERE s.show_time > NOW()
                ORDER BY s.show_time ASC";
        return $this->db->fetchAll($sql);
    }
}
?>
