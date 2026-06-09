<?php



class AdminDashboard {
    private $db;

    //this function works authotamiqully where we use this class;
    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

//Calculate the total number of registered users
    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }


// Calculate the total number of properties in the system
    public function getTotalProperties() {
        $query = "SELECT COUNT(*) as total FROM properties";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }


// Calculate the total number of approved (active) properties
    public function getApprovedProperties() {
        $query = "SELECT COUNT(*) as total FROM properties WHERE status = 'approved'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

//  Calculate the total number of pending properties awaiting review
    public function getPendingProperties() {
        $query = "SELECT COUNT(*) as total FROM properties WHERE status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }


    //Calculate the total number of unread messages
    public function getUnreadMessages() {
            $query = "SELECT COUNT(*) as total FROM messages WHERE status = 'unread'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
    }


    //Calculate the total number of banned users
        public function blockedUsers() {
            $query = "SELECT COUNT(*) as total FROM users WHERE status = 'blocked'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        }



    // Calculate the number of rejected properties
        public function getRejectedProperties() {
            $query = "SELECT COUNT(*) as total FROM properties WHERE status = 'rejected'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        }
    }


?>