<?php
/**
 * API Router
 * 경로 라우팅 및 요청 처리
 */

namespace MilalHomepage\Routes;

use MilalHomepage\Utils\ResponseFormatter;
use MilalHomepage\Routes\Handlers\AuthHandler;
use MilalHomepage\Routes\Handlers\HeroHandler;
use MilalHomepage\Routes\Handlers\QuickLinkHandler;
use MilalHomepage\Routes\Handlers\SermonHandler;
use MilalHomepage\Routes\Handlers\BulletinHandler;
use MilalHomepage\Routes\Handlers\AnnouncementHandler;
use MilalHomepage\Routes\Handlers\TogetherHandler;
use MilalHomepage\Routes\Handlers\DepartmentHandler;
use MilalHomepage\Routes\Handlers\NextGenHandler;
use MilalHomepage\Routes\Handlers\MinistryHandler;
use MilalHomepage\Routes\Handlers\NewsHandler;
use MilalHomepage\Routes\Handlers\MemberHandler;
use MilalHomepage\Routes\Handlers\UserHandler;
use MilalHomepage\Routes\Handlers\TrackingHandler;
use MilalHomepage\Routes\Handlers\AnalyticsHandler;
use MilalHomepage\Routes\Handlers\LandingTitleHandler;
use MilalHomepage\Routes\Handlers\SectionHandler;
use MilalHomepage\Routes\Handlers\VisionStatementHandler;
use MilalHomepage\Routes\Handlers\ServiceTimeHandler;
use MilalHomepage\Routes\Handlers\ShuttleBusScheduleHandler;
use MilalHomepage\Routes\Handlers\ParkingLotHandler;
use MilalHomepage\Routes\Handlers\ParkingMapHandler;
use MilalHomepage\Routes\Handlers\BannerImageHandler;
use MilalHomepage\Routes\Handlers\DocsHandler;

class ApiRouter {
    private $request_method;
    private $request_path;

    public function __construct() {
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // API 경로 정규화
        if (strpos($this->request_path, '/api/') === 0) {
            $this->request_path = substr($this->request_path, 4); // '/api' 제거
        }
    }

    /**
     * 라우트 매칭 및 처리
     */
    public function dispatch() {
        $path_parts = array_filter(explode('/', $this->request_path));

        if (empty($path_parts)) {
            echo ResponseFormatter::success(['message' => 'API is running'], 'API Ready');
            return;
        }

        $resource = array_shift($path_parts);
        $id       = array_shift($path_parts) ?? null;
        $action   = array_shift($path_parts) ?? null;
        $sub_id   = array_shift($path_parts) ?? null;

        $method = $this->request_method;

        switch ($resource) {
            case 'auth':
                (new AuthHandler($method))->handle($id, $action);
                break;

            case 'hero':
                (new HeroHandler($method))->handle($id, $action);
                break;

            case 'quick-links':
                (new QuickLinkHandler($method))->handle($id, $action);
                break;

            case 'sermons':
                (new SermonHandler($method))->handle($id, $action);
                break;

            case 'bulletins':
                (new BulletinHandler($method))->handle($id, $action);
                break;

            case 'announcements':
                (new AnnouncementHandler($method))->handle($id, $action);
                break;

            case 'together':
                (new TogetherHandler($method))->handle($id, $action);
                break;

            case 'departments':
                (new DepartmentHandler($method))->handle($id, $action);
                break;

            case 'nextgen':
                (new NextGenHandler($method))->handle($id, $action, $sub_id);
                break;

            case 'ministry':
                (new MinistryHandler($method))->handle($id, $action, $sub_id);
                break;

            case 'news':
                (new NewsHandler($method))->handle($id, $action, $sub_id);
                break;

            case 'members':
                (new MemberHandler($method))->handle($id, $action);
                break;

            case 'users':
                (new UserHandler($method))->handle($id, $action);
                break;

            case 'track':
                (new TrackingHandler($method))->handle($id, $action);
                break;

            case 'analytics':
                (new AnalyticsHandler($method))->handle($id, $action);
                break;

            case 'landing-titles':
                (new LandingTitleHandler($method))->handle($id, $action);
                break;

            case 'sections':
                (new SectionHandler($method))->handle($id, $action);
                break;

            case 'vision-statements':
                (new VisionStatementHandler($method))->handle($id, $action);
                break;

            case 'service-times':
                (new ServiceTimeHandler($method))->handle($id, $action);
                break;

            case 'shuttle-bus-schedule':
                (new ShuttleBusScheduleHandler($method))->handle($id, $action);
                break;

            case 'parking-lot':
                (new ParkingLotHandler($method))->handle($id, $action);
                break;

            case 'parking-map':
                (new ParkingMapHandler($method))->handle($id, $action);
                break;

            case 'banner-image':
                (new BannerImageHandler($method))->handle($id, $action);
                break;

            case 'docs':
                (new DocsHandler())->handleDocs();
                break;

            case 'openapi.json':
                (new DocsHandler())->handleOpenApiSpec();
                break;

            default:
                echo ResponseFormatter::error(
                    'NOT_FOUND',
                    'API endpoint not found: ' . $resource,
                    null,
                    404
                );
                break;
        }
    }
}
?>