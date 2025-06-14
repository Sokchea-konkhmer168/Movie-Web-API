<?php
require_once '../Database.php';

class Hall {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all halls with cinema details
    public function getAll() {
        $sql = "SELECT h.*, c.name as cinema_name, c.location 
                FROM halls h 
                JOIN cinemas c ON h.cinema_id = c.id 
                ORDER BY c.name, h.hall_number";
        return $this->db->fetchAll($sql);
    }
    
    // Get hall by ID
    public function getById($id) {
        $sql = "SELECT h.*, c.name as cinema_name, c.location 
                FROM halls h 
                JOIN cinemas c ON h.cinema_id = c.id 
                WHERE h.id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Get halls by cinema
    public function getByCinema($cinemaId) {
        $sql = "SELECT * FROM halls WHERE cinema_id = ? ORDER BY hall_number";
        return $this->db->fetchAll($sql, [$cinemaId]);
    }
    
    // Create new hall
    public function create($data) {
        $sql = "INSERT INTO halls (hall_number, capacity, cinema_id) VALUES (?, ?, ?)";
        $params = [
            $data['hall_number'],
            $data['capacity'],
            $data['cinema_id']
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // Update hall
    public function update($id, $data) {
        $sql = "UPDATE halls SET hall_number = ?, capacity = ?, cinema_id = ?, 
                updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $params = [
            $data['hall_number'],
            $data['capacity'],
            $data['cinema_id'],
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    // Delete hall
    public function delete($id) {
        $sql = "DELETE FROM halls WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}
?>
