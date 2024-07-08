<?php
    require 'vendor/autoload.php';

    use ezsql\Database;
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    class DB {
        private $db;
        private $db_name;
        private $db_user;
        private $db_pass;
        private $db_host;

        public function __construct() {
            $this-> db_name = $_SERVER['DB_NAME'];
            $this-> db_user = $_SERVER['DB_USER'];
            $this-> db_pass = $_SERVER['DB_PASS'];
            $this-> db_host = $_SERVER['DB_HOST'];
        }

        public function connect() {
            $this->db = Database::initialize('mysqli', [$this->db_user, $this->db_pass, $this->db_name, $this->db_host]);
            return $this->db;
        }
    }
?>