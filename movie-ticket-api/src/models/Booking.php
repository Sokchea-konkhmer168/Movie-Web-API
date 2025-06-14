<?php
require_once '../Database.php';

class Booking {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Create new booking
    public function create($data) {
        // Check if seat is available
        if (!$this->isSeatAvailable($data['show_id'], $data['seat_number'])) {
            return false; // Seat already booked
        }
        
        // Get show details for price calculation
        $show = $this->getShowById($data['show_id']);
        if (!$show) {
            return false; // Show not found
        }
        
        // Start transaction
        $this->db->getConnection()->beginTransaction();
        
        try {
            // Create booking
            $sql = "INSERT INTO bookings (user_id, show_id, seat_number, total_price) 
                    VALUES (?, ?, ?, ?)";
            $params = [
                $data['user_id'],
                $data['show_id'],
                $data['seat_number'],
                $show['price']
            ];
            
            $this->db->execute($sql, $params);
            $bookingId = $this->db->lastInsertId();
            
            // Update available seats
            $updateSql = "UPDATE shows SET available_seats = available_seats - 1 WHERE id = ?";
            $this->db->execute($updateSql, [$data['show_id']]);
            
            $this->db->getConnection()->commit();
            return $bookingId;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            throw $e;
        }
    }
    
    // Check if seat is available
    private function isSeatAvailable($showId, $seatNumber) {
        $sql = "SELECT id FROM bookings WHERE show_id = ? AND seat_number = ? AND status = 'confirmed'";
        $result = $this->db->fetch($sql, [$showId, $seatNumber]);
        return !$result; // Return true if no booking found (seat available)
    }
    
    // Get show details
    private function getShowById($id) {
        $sql = "SELECT * FROM shows WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Get booking by ID
    public function getById($id) {
        $sql = "SELECT b.*, m.title as movie_title, c.name as cinema_name, 
                h.hall_number, s.show_time, s.price
                FROM bookings b
                JOIN shows s ON b.show_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN halls h ON s.hall_id = h.id
                JOIN cinemas c ON h.cinema_id = c.id
                WHERE b.id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Get user's bookings
    public function getByUser($userId) {
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
    
    // Cancel booking
    public function cancel($id, $userId) {
        // Start transaction
        $this->db->getConnection()->beginTransaction();
        
        try {
            // Get booking details
            $booking = $this->db->fetch("SELECT * FROM bookings WHERE id = ? AND user_id = ?", [$id, $userId]);
            if (!$booking) {
                return false;
            }
            
            // Update booking status
            $sql = "UPDATE bookings SET status = 'cancelled', updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $this->db->execute($sql, [$id]);
            
            // Increase available seats
            $updateSql = "UPDATE shows SET available_seats = available_seats + 1 WHERE id = ?";
            $this->db->execute($updateSql, [$booking['show_id']]);
            
            $this->db->getConnection()->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            throw $e;
        }
    }
}
?>
