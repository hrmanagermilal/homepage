<?php
/**
 * Database Helper (Alias for Config\Database)
 */

namespace MillalHomepage\Utils;

use MillalHomepage\Config\Database as DatabaseConfig;

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $db = new DatabaseConfig();
        $this->connection = $db->connect();
    }
    
    /**
     * Singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * PDO connection 반환
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Query 실행
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            error_log("Query Error: " . $e->getMessage() . " SQL: " . $sql);
            throw new \Exception("데이터베이스 쿼리 실패");
        }
    }
}
?>
