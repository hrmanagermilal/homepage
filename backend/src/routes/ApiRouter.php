<?php
/**
 * API Router
 * 경로 라우팅 및 요청 처리
 */

namespace MillalHomepage\Routes;

use MillalHomepage\Utils\ResponseFormatter;
use MillalHomepage\Routes\Handlers\AuthHandler;
use MillalHomepage\Routes\Handlers\HeroHandler;
use MillalHomepage\Routes\Handlers\HeroLinkHandler;
use MillalHomepage\Routes\Handlers\SermonHandler;
use MillalHomepage\Routes\Handlers\BulletinHandler;
use MillalHomepage\Routes\Handlers\AnnouncementHandler;
use MillalHomepage\Routes\Handlers\TogetherHandler;
use MillalHomepage\Routes\Handlers\DepartmentHandler;
use MillalHomepage\Routes\Handlers\NextGenHandler;
use MillalHomepage\Routes\Handlers\MinistryHandler;
use MillalHomepage\Routes\Handlers\NewsHandler;
use MillalHomepage\Routes\Handlers\MemberHandler;
use MillalHomepage\Routes\Handlers\UserHandler;
use MillalHomepage\Routes\Handlers\TrackingHandler;
use MillalHomepage\Routes\Handlers\AnalyticsHandler;
use MillalHomepage\Routes\Handlers\LandingTitleHandler;
use MillalHomepage\Routes\Handlers\DocsHandler;

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
                (new HeroHandler($method))->handle($id, $action, $sub_id);
                break;

            case 'hero-links':
                (new HeroLinkHandler($method))->handle($id, $action);
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