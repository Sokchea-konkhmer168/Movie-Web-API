<?php
require_once '../Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
      // Register new user
    public function register($data) {
        // Check if email already exists
        if ($this->getByEmail($data['email'])) {
            return false; // Email already exists
        }
        
        $role = isset($data['role']) ? $data['role'] : 'user'; // Default to 'user'
        
        $sql = "INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)";
        $params = [
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['phone'] ?? null,
            $role
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // Login user
    public function login($email, $password) {
        $user = $this->getByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    // Get user by email
    public function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->db->fetch($sql, [$email]);
    }
      // Get user by ID
    public function getById($id) {
        $sql = "SELECT id, name, email, phone, role, created_at FROM users WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Update user profile
    public function update($id, $data) {
        $sql = "UPDATE users SET name = ?, phone = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $params = [
            $data['name'],
            $data['phone'] ?? null,
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    // Get user bookings
    public function getBookings($userId) {
        $sql = "SELECT b.*, m.title as movie_title, c.name as cinema_name, 
                h.hall_number, s.show_time, s.price
                FROM bookings b
                JOIN shows s ON b.show_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN halls h ON s.hall_id = h.id
                JOIN cinemas c ON h.cinema_id = c.id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    // Get user bookings with details
    public function getBookingsWithDetails($userId) {
        $sql = "SELECT b.*, m.title as movie_title, s.show_time, h.hall_number, c.name as cinema_name
                FROM bookings b
                JOIN shows s ON b.show_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN halls h ON s.hall_id = h.id
                JOIN cinemas c ON h.cinema_id = c.id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    // Get all users (for verification purposes)
    public function getAllUsers() {
        $sql = "SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    // Update user role (admin function)
    public function updateRole($userId, $role) {
        if (!in_array($role, ['user', 'admin'])) {
            return false; // Invalid role
        }
        
        $sql = "UPDATE users SET role = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->execute($sql, [$role, $userId]);
    }
    
    // Check if user is admin
    public function isAdmin($userId) {
        $sql = "SELECT role FROM users WHERE id = ?";
        $user = $this->db->fetch($sql, [$userId]);
        return $user && $user['role'] === 'admin';
    }
    
    // Get users by role
    public function getUsersByRole($role = 'user') {
        $sql = "SELECT id, name, email, phone, role, created_at FROM users WHERE role = ? ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, [$role]);
    }
}
?>
