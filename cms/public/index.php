<?php
// ── Bootstrap ──────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH.'/config/app.php';
require_once BASE_PATH.'/config/database.php';
require_once BASE_PATH.'/app/Models/BaseModel.php';
require_once BASE_PATH.'/app/Models/UserModel.php';
require_once BASE_PATH.'/app/Models/RoleModel.php';
require_once BASE_PATH.'/app/Models/IntroductionModel.php';
require_once BASE_PATH.'/app/Models/MinistryModel.php';
require_once BASE_PATH.'/app/Models/NoticeModel.php';
require_once BASE_PATH.'/app/Models/ObituaryModel.php';
require_once BASE_PATH.'/app/Models/OnlineGivingModel.php';
require_once BASE_PATH.'/app/Models/MemberModel.php';
require_once BASE_PATH.'/app/Models/SermonModel.php';
require_once BASE_PATH.'/app/Models/BulletinModel.php';
require_once BASE_PATH.'/app/Models/DepartmentModel.php';
require_once BASE_PATH.'/app/Middleware/AuthMiddleware.php';
require_once BASE_PATH.'/app/Helpers/UploadHelper.php';
require_once BASE_PATH.'/app/Controllers/BaseController.php';
require_once BASE_PATH.'/app/Controllers/AuthController.php';
require_once BASE_PATH.'/app/Controllers/DashboardController.php';
require_once BASE_PATH.'/app/Controllers/UserController.php';
require_once BASE_PATH.'/app/Controllers/IntroductionController.php';
require_once BASE_PATH.'/app/Controllers/MinistryController.php';
require_once BASE_PATH.'/app/Controllers/NoticeController.php';
require_once BASE_PATH.'/app/Controllers/ObituaryController.php';
require_once BASE_PATH.'/app/Controllers/OnlineGivingController.php';
require_once BASE_PATH.'/app/Controllers/MemberController.php';
require_once BASE_PATH.'/app/Controllers/SermonController.php';
require_once BASE_PATH.'/app/Controllers/BulletinController.php';
require_once BASE_PATH.'/app/Controllers/DepartmentController.php';
// 선택적 파일 (없어도 에러 안 남)
if(file_exists(BASE_PATH.'/app/Models/HeroModel.php'))              require_once BASE_PATH.'/app/Models/HeroModel.php';
if(file_exists(BASE_PATH.'/app/Controllers/HeroController.php'))     require_once BASE_PATH.'/app/Controllers/HeroController.php';
if(file_exists(BASE_PATH.'/app/Controllers/WorshipController.php'))  require_once BASE_PATH.'/app/Controllers/WorshipController.php';
if(file_exists(BASE_PATH.'/app/Controllers/TrafficController.php'))  require_once BASE_PATH.'/app/Controllers/TrafficController.php';
if(file_exists(BASE_PATH.'/app/Controllers/BannerController.php'))   require_once BASE_PATH.'/app/Controllers/BannerController.php';
if(file_exists(BASE_PATH.'/app/Controllers/SectionTitleController.php')) require_once BASE_PATH.'/app/Controllers/SectionTitleController.php';
if(file_exists(BASE_PATH.'/app/Controllers/SettingsController.php'))     require_once BASE_PATH.'/app/Controllers/SettingsController.php';
if(file_exists(BASE_PATH.'/app/Models/CmsModel.php'))                require_once BASE_PATH.'/app/Models/CmsModel.php';
if(file_exists(BASE_PATH.'/app/Controllers/CmsController.php'))      require_once BASE_PATH.'/app/Controllers/CmsController.php';

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
        ''         => ['AuthController','loginPage'],
        'login'    => ['AuthController','loginPage'],
        'do-login' => ['AuthController','login'],
        'logout'   => ['AuthController','logout'],
        'profile'  => ['AuthController','updateProfile'],
    ],
    // ── Dashboard ─────────────────────────────────────────
    'dashboard' => ['' => ['DashboardController','index']],

    // ── Settings (API for theme etc.) ─────────────────────
    'settings' => [
        'theme' => ['SettingsController','setTheme'],
    ],

    'introduction' => [
        'vision'           => ['IntroductionController','vision'],
        'vision-list'      => ['IntroductionController','visionList'],
        'vision-detail'    => ['IntroductionController','visionDetail'],
        'vision-create'    => ['IntroductionController','visionCreate'],
        'vision-update'    => ['IntroductionController','visionUpdate'],
        'vision-delete'    => ['IntroductionController','visionDelete'],
        'vision-reorder'   => ['IntroductionController','visionReorder'],
        'section-list'     => ['IntroductionController','sectionList'],
        'section-detail'   => ['IntroductionController','sectionDetail'],
        'section-create'   => ['IntroductionController','sectionCreate'],
        'section-update'   => ['IntroductionController','sectionUpdate'],
        'section-delete'   => ['IntroductionController','sectionDelete'],
        'pastor'           => ['IntroductionController','pastor'],
        'pastor-detail'    => ['IntroductionController','pastorDetail'],
        'pastor-update'    => ['IntroductionController','pastorUpdate'],
        'together'         => ['IntroductionController','together'],
        'together-list'    => ['IntroductionController','togetherList'],
        'together-detail'  => ['IntroductionController','togetherDetail'],
        'together-create'  => ['IntroductionController','togetherCreate'],
        'together-update'  => ['IntroductionController','togetherUpdate'],
        'together-delete'  => ['IntroductionController','togetherDelete'],
        'together-reorder' => ['IntroductionController','togetherReorder'],
    ],
    // ── Members (섬기는 분들) ──────────────────────────────
    'members' => [
        ''        => ['MemberController','index'],
        'list'    => ['MemberController','list'],
        'detail'  => ['MemberController','detail'],
        'create'  => ['MemberController','create'],
        'update'  => ['MemberController','update'],
        'delete'  => ['MemberController','delete'],
        'reorder' => ['MemberController','reorder'],
        'view'    => ['MemberController','detail_page'],
    ],
    // ── Departments (다음세대 / 사역부서) ─────────────────
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
    // ── Ministry (사역) ───────────────────────────────────
    'ministry' => [
        ''        => ['MinistryController','index'],
        'edit'    => ['MinistryController','edit'],
        'list'    => ['MinistryController','list'],
        'detail'  => ['MinistryController','detail'],
        'create'  => ['MinistryController','create'],
        'update'  => ['MinistryController','update'],
        'delete'  => ['MinistryController','delete'],
        'reorder' => ['MinistryController','reorder'],
    ],
    // ── Sermons (설교) ────────────────────────────────────
    'sermons' => [
        ''                => ['SermonController','index'],
        'list'            => ['SermonController','list'],
        'detail'          => ['SermonController','detail'],
        'create'          => ['SermonController','create'],
        'update'          => ['SermonController','update'],
        'delete'          => ['SermonController','delete'],
        'view'            => ['SermonController','detail_page'],
        'category-list'   => ['SermonController','categoryList'],
        'category-detail' => ['SermonController','categoryDetail'],
        'category-create' => ['SermonController','categoryCreate'],
        'category-update' => ['SermonController','categoryUpdate'],
        'category-delete' => ['SermonController','categoryDelete'],
    ],
    // ── Bulletins (주보) ──────────────────────────────────
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
        'images-add'    => ['BulletinController','addImages'],
        'view'          => ['BulletinController','detail_page'],
    ],
    // ── Notice (공지) ─────────────────────────────────────
    'notice' => [
        ''       => ['NoticeController','index'],
        'list'   => ['NoticeController','list'],
        'detail' => ['NoticeController','detail'],
        'create' => ['NoticeController','create'],
        'update' => ['NoticeController','update'],
        'delete' => ['NoticeController','delete'],
    ],
    // ── Obituary (부고) ───────────────────────────────────
    'obituary' => [
        ''       => ['ObituaryController','index'],
        'list'   => ['ObituaryController','list'],
        'detail' => ['ObituaryController','detail'],
        'create' => ['ObituaryController','create'],
        'update' => ['ObituaryController','update'],
        'delete' => ['ObituaryController','delete'],
    ],
    // ── Online Giving (예배 & 교통) ───────────────────────
    'online-giving' => [
        ''                   => ['OnlineGivingController','index'],
        'service-list'       => ['OnlineGivingController','serviceList'],
        'service-detail'     => ['OnlineGivingController','serviceDetail'],
        'service-create'     => ['OnlineGivingController','serviceCreate'],
        'service-update'     => ['OnlineGivingController','serviceUpdate'],
        'service-delete'     => ['OnlineGivingController','serviceDelete'],
        'service-reorder'    => ['OnlineGivingController','serviceReorder'],
        'shuttle-list'       => ['OnlineGivingController','shuttleList'],
        'shuttle-detail'     => ['OnlineGivingController','shuttleDetail'],
        'shuttle-create'     => ['OnlineGivingController','shuttleCreate'],
        'shuttle-update'     => ['OnlineGivingController','shuttleUpdate'],
        'shuttle-delete'     => ['OnlineGivingController','shuttleDelete'],
        'parking-list'       => ['OnlineGivingController','parkingList'],
        'parking-detail'     => ['OnlineGivingController','parkingDetail'],
        'parking-create'     => ['OnlineGivingController','parkingCreate'],
        'parking-update'     => ['OnlineGivingController','parkingUpdate'],
        'parking-delete'     => ['OnlineGivingController','parkingDelete'],
        'parking-reorder'    => ['OnlineGivingController','parkingReorder'],
        'parking-map-update' => ['OnlineGivingController','parkingMapUpdate'],
        'banner-update'      => ['OnlineGivingController','bannerUpdate'],
    ],
    // ── Users & Roles ─────────────────────────────────────
    'users' => [
        ''            => ['UserController','index'],
        'list'        => ['UserController','list'],
        'detail'      => ['UserController','detail'],
        'create'      => ['UserController','create'],
        'update'      => ['UserController','update'],
        'delete'      => ['UserController','delete'],
        'roles'       => ['UserController','rolesPage'],
        'role-list'   => ['UserController','roles'],
        'role-detail' => ['UserController','roleDetail'],
        'role-create' => ['UserController','roleCreate'],
        'role-update' => ['UserController','roleUpdate'],
        'role-delete' => ['UserController','roleDelete'],
    ],
    // ── Heroes (히어로) ───────────────────────────────────
    'heroes' => [
        ''                   => ['HeroController', 'index'],
        'list'               => ['HeroController', 'list'],
        'detail'             => ['HeroController', 'detail'],
        'create'             => ['HeroController', 'create'],
        'update'             => ['HeroController', 'update'],
        'delete'             => ['HeroController', 'delete'],
        'link-list'          => ['HeroController', 'linkList'],
        'link-detail'        => ['HeroController', 'linkDetail'],
        'link-create'        => ['HeroController', 'linkCreate'],
        'link-update'        => ['HeroController', 'linkUpdate'],
        'link-delete'        => ['HeroController', 'linkDelete'],
        'bg-image-add'       => ['HeroController', 'bgImageAdd'],
        'bg-image-delete'    => ['HeroController', 'bgImageDelete'],
        'bg-image-reorder'   => ['HeroController', 'bgImageReorder'],
        'front-image-upsert' => ['HeroController', 'frontImageUpsert'],
        'front-image-delete' => ['HeroController', 'frontImageDelete'],
    ],
    // ── Worship (예배) ────────────────────────────────────
    'worship' => [
        ''                => ['WorshipController', 'index'],
        'service-list'    => ['WorshipController', 'serviceList'],
        'service-detail'  => ['WorshipController', 'serviceDetail'],
        'service-create'  => ['WorshipController', 'serviceCreate'],
        'service-update'  => ['WorshipController', 'serviceUpdate'],
        'service-delete'  => ['WorshipController', 'serviceDelete'],
        'service-reorder' => ['WorshipController', 'serviceReorder'],
    ],
    // ── Traffic (교통) ────────────────────────────────────
    'traffic' => [
        ''                   => ['TrafficController', 'index'],
        'shuttle-list'       => ['TrafficController', 'shuttleList'],
        'shuttle-detail'     => ['TrafficController', 'shuttleDetail'],
        'shuttle-create'     => ['TrafficController', 'shuttleCreate'],
        'shuttle-update'     => ['TrafficController', 'shuttleUpdate'],
        'shuttle-delete'     => ['TrafficController', 'shuttleDelete'],
        'parking-list'       => ['TrafficController', 'parkingList'],
        'parking-detail'     => ['TrafficController', 'parkingDetail'],
        'parking-create'     => ['TrafficController', 'parkingCreate'],
        'parking-update'     => ['TrafficController', 'parkingUpdate'],
        'parking-delete'     => ['TrafficController', 'parkingDelete'],
        'parking-reorder'    => ['TrafficController', 'parkingReorder'],
        'parking-map-update' => ['TrafficController', 'parkingMapUpdate'],
    ],
    // ── Banner (배너) ─────────────────────────────────────
    'banner' => [
        ''              => ['BannerController', 'index'],
        'banner-update' => ['BannerController', 'bannerUpdate'],
    ],
    // ── Section Titles (섹션타이틀) ───────────────────────
    'section-titles' => [
        ''       => ['SectionTitleController', 'index'],
        'list'   => ['SectionTitleController', 'list'],
        'detail' => ['SectionTitleController', 'detail'],
        'create' => ['SectionTitleController', 'create'],
        'update' => ['SectionTitleController', 'update'],
        'delete' => ['SectionTitleController', 'delete'],
    ],
    // ── CMS ───────────────────────────────────────────────
    'cms' => [
        ''               => ['CmsController','index'],
        'page-list'      => ['CmsController','pageList'],
        'page-detail'    => ['CmsController','pageDetail'],
        'page-create'    => ['CmsController','pageCreate'],
        'page-update'    => ['CmsController','pageUpdate'],
        'page-delete'    => ['CmsController','pageDelete'],
        'section-list'   => ['CmsController','sectionList'],
        'section-detail' => ['CmsController','sectionDetail'],
        'section-create' => ['CmsController','sectionCreate'],
        'section-update' => ['CmsController','sectionUpdate'],
        'section-delete' => ['CmsController','sectionDelete'],
        'text-list'      => ['CmsController','textList'],
        'text-detail'    => ['CmsController','textDetail'],
        'text-create'    => ['CmsController','textCreate'],
        'text-update'    => ['CmsController','textUpdate'],
        'text-delete'    => ['CmsController','textDelete'],
    ],
];

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
