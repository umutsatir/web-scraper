<?php
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    class PDOClass {
        private $pdo;
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
            $this->pdo = new PDO("mysql:host={$this->db_host};dbname={$this->db_name}", $this->db_user, $this->db_pass);
            return $this->pdo;
        }
    }