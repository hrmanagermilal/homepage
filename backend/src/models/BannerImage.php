<?php
/**
 * BannerImage Model
 */

namespace MilalHomepage\Models;

use MilalHomepage\Utils\Database;

class BannerImage {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getActive() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM banner_image
                WHERE is_active = 1
                ORDER BY id DESC
                LIMIT 1
            ");
            $stmt->execute();
            return $stmt->fetch() ?: null;
        } catch (\PDOException $e) {
            error_log("Get banner image error: " . $e->getMessage());
            return null;
        }
    }
}
