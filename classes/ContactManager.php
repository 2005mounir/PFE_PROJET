<?php
class ContactManager {
    private $db;
    //  path of log file
    private $logFile = __DIR__ . "/../erreurs/erreurs.log";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /*
       method to storage data in database;
    */
    
    public function sendMessage($name, $email, $subject, $message) {
        try {
            $sql = "INSERT INTO messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$name, $email, $subject, $message]);
        } catch (PDOException $e) {
            $this->logError("Insert Error: " . $e->getMessage());
            return false;
        }
    }

    /*
       Professional error logging 
     */
    private function logError($message) {
        $timestamp = date("Y-m-d H:i:s");
        $logEntry = "[$timestamp] CONTACT ERROR: $message" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
}