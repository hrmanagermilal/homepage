<?php
class SettingsController extends BaseController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /** POST settings/theme — save active theme to site_settings table */
    public function setTheme(): void {
        $data  = json_decode(file_get_contents('php://input'), true) ?? [];
        $theme = trim($data['theme'] ?? '');
        $allowed = ['dark-green', 'dark-blue', 'dark-brown'];
        if (!in_array($theme, $allowed, true)) {
            $this->error('유효하지 않은 테마입니다.', 400);
        }
        $stmt = $this->db->prepare(
            "INSERT INTO site_settings (`key`, value) VALUES ('theme', ?)
             ON DUPLICATE KEY UPDATE value = ?"
        );
        $stmt->execute([$theme, $theme]);
        $this->success(['theme' => $theme]);
    }
}
