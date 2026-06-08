<?php

class Database {

   private $host = "127.0.0.1"; 
    private $dbname = "rentora_db";
    private $username = "root";
    private $password = "";

    private $pdo;


    public function connect() {

        if ($this->pdo === null) {

            try {

                $this->pdo = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                    $this->username,
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );

               

            } catch (PDOException $e) {

                // log error
                $this->logError($e->getMessage());

                // safe output (no system leak)
                die("Database connection error. Please try again later.");
            }
        }

        return $this->pdo;
    }

   
    private function logError($message) {

        $file = __DIR__ . "/../storage/logs/errors.log";

        $log = "[" . date("Y-m-d H:i:s") . "] DATABASE ERROR: " . $message . PHP_EOL;

        file_put_contents($file, $log, FILE_APPEND);
    }
}



