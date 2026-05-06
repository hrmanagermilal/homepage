<?php
class DashboardController extends BaseController {
    public function __construct() { AuthMiddleware::requireLogin(); }
    public function index(): void {
        $db=Database::getInstance();
        $stats=[
            'announcements'=>(int)$db->query('SELECT COUNT(*) FROM announcements WHERE is_active=1')->fetchColumn(),
            'news'=>(int)$db->query('SELECT COUNT(*) FROM news')->fetchColumn(),
            'sermons'=>(int)$db->query('SELECT COUNT(*) FROM sermons')->fetchColumn(),
            'bulletins'=>(int)$db->query('SELECT COUNT(*) FROM bulletins')->fetchColumn(),
            'members'=>(int)$db->query('SELECT COUNT(*) FROM members WHERE is_active=1')->fetchColumn(),
            'departments'=>(int)$db->query('SELECT COUNT(*) FROM departments WHERE is_active=1')->fetchColumn(),
            'heroes'=>(int)$db->query('SELECT COUNT(*) FROM heroes WHERE is_active=1')->fetchColumn(),
            'users'=>(int)$db->query('SELECT COUNT(*) FROM users WHERE is_active=1')->fetchColumn(),
        ];
        // 최근 7일 페이지뷰
      //  $pvRows=$db->query("SELECT DATE(viewed_at) AS d, COUNT(*) AS cnt FROM page_views WHERE viewed_at>=DATE_SUB(NOW(),INTERVAL 7 DAY) GROUP BY DATE(viewed_at) ORDER BY d ASC")->fetchAll(PDO::FETCH_ASSOC);
      //  $pvChart=[]; foreach($pvRows as $r)$pvChart[$r['d']]=$r['cnt'];
        // 기기별 비율
      //  $deviceRows=$db->query("SELECT device_type,COUNT(*) AS cnt FROM page_views WHERE viewed_at>=DATE_SUB(NOW(),INTERVAL 30 DAY) GROUP BY device_type")->fetchAll(PDO::FETCH_ASSOC);
      //  $deviceChart=[]; foreach($deviceRows as $r)$deviceChart[$r['device_type']]=$r['cnt'];
        // 최근 공지 5개
        $recentAnn=$db->query("SELECT a.id,a.title,a.category,a.created_at,u.name AS author FROM announcements a LEFT JOIN users u ON u.id=a.admin_id ORDER BY a.id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        // 최근 설교 5개
        $recentSermons=$db->query("SELECT id,title,preacher,sermon_date FROM sermons ORDER BY sermon_date DESC,id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $pageTitle='대시보드'; $currentPage='dashboard';
        include BASE_PATH.'/app/Views/dashboard/index.php';
    }
}
