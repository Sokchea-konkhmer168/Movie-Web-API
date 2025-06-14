<?php
require_once '../Database.php';

class Cinema {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all cinemas
    public function getAll() {
        $sql = "SELECT c.*, COUNT(h.id) as total_halls 
                FROM cinemas c 
                LEFT JOIN halls h ON c.id = h.cinema_id 
                GROUP BY c.id 
                ORDER BY c.name";
        return $this->db->fetchAll($sql);
    }
    
    // Get cinema by ID
    public function getById($id) {
        $sql = "SELECT * FROM cinemas WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Create new cinema
    public function create($data) {
        $sql = "INSERT INTO cinemas (name, location, number_of_halls, phone, address) 
                VALUES (?, ?, ?, ?, ?)";
        $params = [
            $data['name'],
            $data['location'],
            $data['number_of_halls'],
            $data['phone'] ?? null,
            $data['address'] ?? null
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // Update cinema
    public function update($id, $data) {
        $sql = "UPDATE cinemas SET name = ?, location = ?, number_of_halls = ?, 
                phone = ?, address = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        $params = [
            $data['name'],
            $data['location'],
            $data['number_of_halls'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    // Delete cinema
    public function delete($id) {
        $sql = "DELETE FROM cinemas WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    // Get cinema halls
    public function getHalls($cinemaId) {
        $sql = "SELECT * FROM halls WHERE cinema_id = ? ORDER BY hall_number";
        return $this->db->fetchAll($sql, [$cinemaId]);
    }
    
    // Get all cinemas with their halls
    public function getAllWithHalls() {
        $cinemas = $this->getAll();
        foreach ($cinemas as &$cinema) {
            $cinema['halls'] = $this->getHalls($cinema['id']);
        }
        return $cinemas;
    }
    
    // Get cinema by ID with halls
    public function getByIdWithHalls($id) {
        $cinema = $this->getById($id);
        if ($cinema) {
            $cinema['halls'] = $this->getHalls($id);
        }
        return $cinema;
    }
}
?>
