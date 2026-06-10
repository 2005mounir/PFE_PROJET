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
    public function getTotaleMessages() {
            $query = "SELECT COUNT(*) as total FROM messages ";
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



     //get last 5 messages
       public function getUnreadRecentMessages() {
              $query = "SELECT * FROM messages WHERE status = 'unread' ORDER BY created_at DESC LIMIT 5";
              $stmt = $this->db->prepare($query);
              $stmt->execute();
               return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }



      //get total unread messages 
        public function getUnreadMessagesCount() {
                $query = "SELECT COUNT(*) as total FROM messages WHERE status = :status";
                $stmt = $this->db->prepare($query);
                $stmt->execute(['status' => 'unread']);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);        
                return $result['total'] ?? 0;
        }



      // Retrieve all messages for the table, ordered by date (newest to oldest)
        public function getAllMessages() {
            $query = "SELECT * FROM messages ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        public function updateMessageStatus($id, $status) {
            $stmt = $this->pdo->prepare("UPDATE messages SET status = ? WHERE id_message = ?");
            $stmt->execute([$status, $id]);
    }

}






?>