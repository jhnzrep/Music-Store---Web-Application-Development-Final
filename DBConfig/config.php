<?php
    class DB {
        protected $pdo;

        private $host = "localhost";
        private $db_name = "chinook_abridged";
        private $username = "root";
        private $password = "";

        public function __construct() {        
            try {
                $this->pdo = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password, [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
            } catch (\PDOException $e) {
                echo 'Connection unsuccessful';
                die('Connection unsuccessful: ' . $e->getMessage());
                exit();
            }
        }

        /**
         * Closes a connection to the database
         */
        public function disconnect() {
            $this->pdo = null;
        }
    }
?>