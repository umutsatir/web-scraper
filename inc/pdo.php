<?php
    session_start();
    $dir = dirname(__DIR__);
    require $dir . '/vendor/autoload.php';
    $dotenv = \Dotenv\Dotenv::createImmutable($dir);
    $dotenv->load();

    class PDOClass {
        private $pdo;
        private $db_name;
        private $db_user;
        private $db_pass;
        private $db_host;

        public function __construct() {
            $this-> db_name = $_ENV['DB_NAME'];
            $this-> db_user = $_ENV['DB_USER'];
            $this-> db_pass = $_ENV['DB_PASS'];
            $this-> db_host = $_ENV['DB_HOST'];
        }

        public function connect() {
            $this->pdo = new PDO("mysql:host={$this->db_host};dbname={$this->db_name}", $this->db_user, $this->db_pass);
            return $this->pdo;
        }
    }
?>