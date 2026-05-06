<?php
// ── Bootstrap ──────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH.'/config/app.php';
require_once BASE_PATH.'/config/database.php';
require_once BASE_PATH.'/app/Models/BaseModel.php';
require_once BASE_PATH.'/app/Models/UserModel.php';
require_once BASE_PATH.'/app/Models/RoleModel.php';
require_once BASE_PATH.'/app/Models/HeroModel.php';
require_once BASE_PATH.'/app/Models/MemberModel.php';
require_once BASE_PATH.'/app/Models/AnnouncementModel.php';
require_once BASE_PATH.'/app/Models/SermonModel.php';
require_once BASE_PATH.'/app/Models/BulletinModel.php';
require_once BASE_PATH.'/app/Models/DepartmentModel.php';
require_once BASE_PATH.'/app/Models/NewsModel.php';
require_once BASE_PATH.'/app/Models/CmsModel.php';
require_once BASE_PATH.'/app/Middleware/AuthMiddleware.php';
require_once BASE_PATH.'/app/Helpers/UploadHelper.php';
require_once BASE_PATH.'/app/Controllers/BaseController.php';
require_once BASE_PATH.'/app/Controllers/AuthController.php';
require_once BASE_PATH.'/app/Controllers/DashboardController.php';
require_once BASE_PATH.'/app/Controllers/UserController.php';
require_once BASE_PATH.'/app/Controllers/HeroController.php';
require_once BASE_PATH.'/app/Controllers/MemberController.php';
require_once BASE_PATH.'/app/Controllers/AnnouncementController.php';
require_once BASE_PATH.'/app/Controllers/SermonController.php';
require_once BASE_PATH.'/app/Controllers/BulletinController.php';
require_once BASE_PATH.'/app/Controllers/DepartmentController.php';
require_once BASE_PATH.'/app/Controllers/NewsController.php';
require_once BASE_PATH.'/app/Controllers/CmsController.php';

// ── Router ─────────────────────────────────────────────────
$uri  = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
if ($base && strpos($uri, $base) === 0) $uri = trim(substr($uri, strlen($base)), '/');
$parts  = explode('/', $uri);
$module = $parts[0] ?? '';
$action = $parts[1] ?? '';

$routes = [
    // ── Auth ──────────────────────────────────────────────
    'auth' => [
        ''        => ['AuthController','loginPage'],
        'login'   => ['AuthController','loginPage'],
        'do-login'=> ['AuthController','login'],
        'logout'  => ['AuthController','logout'],
        'profile' => ['AuthController','updateProfile'],
    ],
    // ── Dashboard ─────────────────────────────────────────
    'dashboard' => ['' => ['DashboardController','index']],
    // ── Heroes ────────────────────────────────────────────
    'heroes' => [
        ''                   => ['HeroController','index'],
        'list'               => ['HeroController','list'],
        'detail'             => ['HeroController','detail'],
        'create'             => ['HeroController','create'],
        'update'             => ['HeroController','update'],
        'delete'             => ['HeroController','delete'],
        'bg-image-add'       => ['HeroController','addBgImage'],
        'bg-image-delete'    => ['HeroController','deleteBgImage'],
        'bg-image-reorder'   => ['HeroController','reorderBgImages'],
        'front-image-upsert' => ['HeroController','upsertFrontImage'],
        'front-image-delete' => ['HeroController','deleteFrontImage'],
        'link-list'          => ['HeroController','linkList'],
        'link-detail'        => ['HeroController','linkDetail'],
        'link-create'        => ['HeroController','linkCreate'],
        'link-update'        => ['HeroController','linkUpdate'],
        'link-delete'        => ['HeroController','linkDelete'],
    ],
    // ── Members ───────────────────────────────────────────
    'members' => [
        ''       => ['MemberController','index'],
        'list'   => ['MemberController','list'],
        'detail' => ['MemberController','detail'],
        'create' => ['MemberController','create'],
        'update' => ['MemberController','update'],
        'delete' => ['MemberController','delete'],
        'reorder'=> ['MemberController','reorder'],
        'view'   => ['MemberController','detail_page'],
    ],
    // ── Announcements ─────────────────────────────────────
    'announcements' => [
        ''            => ['AnnouncementController','index'],
        'list'        => ['AnnouncementController','list'],
        'detail'      => ['AnnouncementController','detail'],
        'create'      => ['AnnouncementController','create'],
        'update'      => ['AnnouncementController','update'],
        'delete'      => ['AnnouncementController','delete'],
        'toggle-pin'  => ['AnnouncementController','togglePin'],
        'view'        => ['AnnouncementController','detail_page'],
    ],
    // ── Sermons ───────────────────────────────────────────
    'sermons' => [
        ''       => ['SermonController','index'],
        'list'   => ['SermonController','list'],
        'detail' => ['SermonController','detail'],
        'create' => ['SermonController','create'],
        'update' => ['SermonController','update'],
        'delete' => ['SermonController','delete'],
        'view'   => ['SermonController','detail_page'],
    ],
    // ── Bulletins ─────────────────────────────────────────
    'bulletins' => [
        ''              => ['BulletinController','index'],
        'list'          => ['BulletinController','list'],
        'detail'        => ['BulletinController','detail'],
        'create'        => ['BulletinController','create'],
        'update'        => ['BulletinController','update'],
        'delete'        => ['BulletinController','delete'],
        'image-add'     => ['BulletinController','addImage'],
        'image-delete'  => ['BulletinController','deleteImage'],
        'image-reorder' => ['BulletinController','reorderImages'],
        'view'         => ['BulletinController','detail_page'],
    ],
    // ── Departments ───────────────────────────────────────
    'departments' => [
        ''                    => ['DepartmentController','index'],
        'list'                => ['DepartmentController','list'],
        'detail'              => ['DepartmentController','detail'],
        'create'              => ['DepartmentController','create'],
        'update'              => ['DepartmentController','update'],
        'delete'              => ['DepartmentController','delete'],
        'reorder'             => ['DepartmentController','reorder'],
        'announcements'       => ['DepartmentController','announcements'],
        'announcement-list'   => ['DepartmentController','announcementList'],
        'announcement-detail' => ['DepartmentController','announcementDetail'],
        'announcement-create' => ['DepartmentController','announcementCreate'],
        'announcement-update' => ['DepartmentController','announcementUpdate'],
        'announcement-delete' => ['DepartmentController','announcementDelete'],
        'view'                => ['DepartmentController','detail_page'],
    ],
    // ── News ──────────────────────────────────────────────
    'news' => [
        ''       => ['NewsController','index'],
        'list'   => ['NewsController','list'],
        'detail' => ['NewsController','detail'],
        'create' => ['NewsController','create'],
        'update' => ['NewsController','update'],
        'delete' => ['NewsController','delete'],
        'view'   => ['NewsController','detail_page'],
    ],
    // ── Users & Roles ─────────────────────────────────────
    'users' => [
        ''              => ['UserController','index'],
        'list'          => ['UserController','list'],
        'detail'        => ['UserController','detail'],
        'create'        => ['UserController','create'],
        'update'        => ['UserController','update'],
        'delete'        => ['UserController','delete'],
        'roles'         => ['UserController','rolesPage'],
        'role-list'     => ['UserController','roles'],
        'role-detail'   => ['UserController','roleDetail'],
        'role-create'   => ['UserController','roleCreate'],
        'role-update'   => ['UserController','roleUpdate'],
        'role-delete'   => ['UserController','roleDelete'],
    ],
    // ── CMS (Pages/Sections/Texts) ────────────────────────
    'cms' => [
        ''              => ['CmsController','index'],
        'page-list'     => ['CmsController','pageList'],
        'page-detail'   => ['CmsController','pageDetail'],
        'page-create'   => ['CmsController','pageCreate'],
        'page-update'   => ['CmsController','pageUpdate'],
        'page-delete'   => ['CmsController','pageDelete'],
        'section-list'  => ['CmsController','sectionList'],
        'section-detail'=> ['CmsController','sectionDetail'],
        'section-create'=> ['CmsController','sectionCreate'],
        'section-update'=> ['CmsController','sectionUpdate'],
        'section-delete'=> ['CmsController','sectionDelete'],
        'text-list'     => ['CmsController','textList'],
        'text-detail'   => ['CmsController','textDetail'],
        'text-create'   => ['CmsController','textCreate'],
        'text-update'   => ['CmsController','textUpdate'],
        'text-delete'   => ['CmsController','textDelete'],
    ],
];

// 구 CMS 경로 호환 리다이렉트 (notices → announcements, slides → heroes, pastors → members)
$legacyRedirects = [
    'notices'  => 'announcements',
    'slides'   => 'heroes',
    'pastors'  => 'members',
    'admins'   => 'users',
];
if (isset($legacyRedirects[$module])) {
    $newModule = $legacyRedirects[$module];
    $newAction = $action ? '/' . $action : '';
    header('Location: ' . BASE_URL . '/' . $newModule . $newAction);
    exit;
}

// 루트 → 로그인 or 대시보드 리다이렉트
if ($module === '') {
    AuthMiddleware::start();
    $dest = empty($_SESSION['user_id']) ? BASE_URL.'/auth/login' : BASE_URL.'/dashboard';
    header('Location: '.$dest); exit;
}

if (isset($routes[$module][$action])) {
    [$class, $method] = $routes[$module][$action];
    (new $class)->$method();
} else {
    http_response_code(404);
    echo '<h1>404 Not Found</h1><p>요청하신 페이지를 찾을 수 없습니다.</p>';
}
