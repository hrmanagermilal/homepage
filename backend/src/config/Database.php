<?php
/**
 * Database Configuration and Connection
 * 
 * PDO를 사용한 MySQL 데이터베이스 연결 클래스
 */

namespace MillalHomepage\Config;

class Database {
    private $host = 'localhost';
    private $db_name;
    private $user;
    private $password;
    private $conn;
    
    public function __construct() {
        $this->db_name = getenv('DB_NAME') ?: 'milal_homepage';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->host = getenv('DB_HOST') ?: 'localhost';
    }
    
    /**
     * 데이터베이스 연결
     */
    public function connect() {
        $this->conn = null;
        
        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';
            
            $this->conn = new \PDO(
                $dsn,
                $this->user,
                $this->password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            
            // Ensure UTF-8 encoding
            $this->conn->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            return $this->conn;
        } catch (\PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new \Exception("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }
    
    /**
     * 현재 연결 반환
     */
    public function getConnection() {
        if ($this->conn === null) {
            return $this->connect();
        }
        return $this->conn;
    }
}
?>
