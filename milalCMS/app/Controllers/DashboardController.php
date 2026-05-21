<?php
class DashboardController extends BaseController {
    public function __construct() { AuthMiddleware::requireLogin(); }
    public function index(): void {
        $db = Database::getInstance();
        $safeCount = function(string $sql) use ($db): int {
            try { return (int)$db->query($sql)->fetchColumn(); }
            catch (PDOException $e) { return 0; }
        };
        $stats = [
            'announcements' => $safeCount('SELECT COUNT(*) FROM announcements WHERE is_active=1'),
            'news'          => $safeCount('SELECT COUNT(*) FROM news'),
            'sermons'       => $safeCount('SELECT COUNT(*) FROM sermons'),
            'bulletins'     => $safeCount('SELECT COUNT(*) FROM bulletins'),
            'members'       => $safeCount('SELECT COUNT(*) FROM members WHERE is_active=1'),
            'departments'   => $safeCount('SELECT COUNT(*) FROM departments WHERE is_active=1'),
            'heroes'        => $safeCount('SELECT COUNT(*) FROM heroes WHERE is_active=1'),
            'users'         => $safeCount('SELECT COUNT(*) FROM users WHERE is_active=1'),
        ];
        try {
            $recentAnn = $db->query("SELECT a.id,a.title,a.category,a.created_at,u.name AS author FROM announcements a LEFT JOIN users u ON u.id=a.admin_id ORDER BY a.id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { $recentAnn = []; }
        try {
            $recentSermons = $db->query("SELECT id,title,preacher,sermon_date FROM sermons ORDER BY sermon_date DESC,id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { $recentSermons = []; }
        $pageTitle='대시보드'; $currentPage='dashboard';
        include BASE_PATH.'/app/Views/dashboard/index.php';
    }
}
