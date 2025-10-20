<?php
class Database {
    private $conn;

    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;

    public function __construct() {
        $this->host = getenv("DB_HOST");
        $this->db_name = getenv("DB_NAME");
        $this->username = getenv("DB_USER");
        $this->password = getenv("DB_PASS");
        $this->port = getenv("DB_PORT");
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            error_log("DB Connection failed: " . $exception->getMessage());
            return null;
        }
        return $this->conn;
    }
}
