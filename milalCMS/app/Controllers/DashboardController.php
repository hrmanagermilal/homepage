<?php
class DashboardController extends BaseController {
    public function __construct() { AuthMiddleware::requireLogin(); }
    public function index(): void {
        $db = Database::getInstance();

        // 새 스키마 기준 통계
        $stats = [
            'notice'      => (int)$db->query('SELECT COUNT(*) FROM notice')->fetchColumn(),
            'sermons'     => (int)$db->query('SELECT COUNT(*) FROM sermons')->fetchColumn(),
            'bulletins'   => (int)$db->query('SELECT COUNT(*) FROM bulletins')->fetchColumn(),
            'members'     => (int)$db->query('SELECT COUNT(*) FROM members WHERE is_active=1')->fetchColumn(),
            'departments' => (int)$db->query('SELECT COUNT(*) FROM departments WHERE is_active=1')->fetchColumn(),
            'ministry'    => (int)$db->query('SELECT COUNT(*) FROM ministry WHERE is_active=1')->fetchColumn(),
            'obituary'    => (int)$db->query('SELECT COUNT(*) FROM obituary WHERE is_active=1')->fetchColumn(),
            'users'       => (int)$db->query('SELECT COUNT(*) FROM users WHERE is_active=1')->fetchColumn(),
        ];

        // 최근 공지 5개
        $recentNotice = $db->query(
            "SELECT id, title, emergency_level, writer_name, created_date
             FROM notice ORDER BY created_date DESC, id DESC LIMIT 5"
        )->fetchAll(PDO::FETCH_ASSOC);

        // 최근 설교 5개
        $recentSermons = $db->query(
            "SELECT id, title, preacher, sermon_date
             FROM sermons ORDER BY sermon_date DESC, id DESC LIMIT 5"
        )->fetchAll(PDO::FETCH_ASSOC);

        // 최근 부고 3개
        $recentObituary = $db->query(
            "SELECT id, title, date FROM obituary ORDER BY date DESC, id DESC LIMIT 3"
        )->fetchAll(PDO::FETCH_ASSOC);

        $pageTitle = '대시보드'; $currentPage = 'dashboard';
        include BASE_PATH.'/app/Views/dashboard/index.php';
    }
}
