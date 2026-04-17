<?php
/**
 * Department Controller
 * 부서/팀 관리 API 처리 (NextGen, Ministry)
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\Department;
use MillalHomepage\Utils\{ResponseFormatter, Validators};
use MillalHomepage\Middleware\AuthMiddleware;

class DepartmentController {
    private $departmentModel;
    private $auth;
    
    public function __construct() {
        $this->departmentModel = new Department();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * GET /api/nextgen
     * NextGen 부서 목록 조회
     */
    public function getNextGen() {
        try {
            $departments = $this->departmentModel->getByType('nextgen');
            
            return ResponseFormatter::success(
                $departments,
                'NextGen departments retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch NextGen departments: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/ministry
     * Ministry 부서 목록 조회
     */
    public function getMinistry() {
        try {
            $departments = $this->departmentModel->getByType('ministry');
            
            return ResponseFormatter::success(
                $departments,
                'Ministry departments retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch Ministry departments: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/nextgen/{id} / GET /api/ministry/{id}
     * 특정 부서 조회
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid department ID',
                    null,
                    400
                );
            }
            
            $department = $this->departmentModel->getById($id);
            
            if (!$department) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Department not found',
                    null,
                    404
                );
            }
            
            return ResponseFormatter::success($department, 'Department');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch department: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/nextgen / POST /api/ministry
     * 새 부서 생성
     */
    public function create($type) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions',
                    null,
                    403
                );
            }
            
            if (!in_array($type, ['nextgen', 'ministry'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid department type',
                    null,
                    400
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // 필드 검증
            if (!Validators::validateRequired($data, ['name'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required field: name',
                    null,
                    400
                );
            }
            
            $data['department_type'] = $type;
            
            $departmentId = $this->departmentModel->create($data);
            
            if (!$departmentId) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to create department',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['department_id' => $departmentId],
                'Department created successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in DepartmentController::create: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * PUT /api/nextgen/{id} / PUT /api/ministry/{id}
     * 부서 정보 수정
     */
    public function update($id) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions',
                    null,
                    403
                );
            }
            
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid department ID',
                    null,
                    400
                );
            }
            
            $department = $this->departmentModel->getById($id);
            if (!$department) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Department not found',
                    null,
                    404
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = $this->departmentModel->update($id, $data);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to update department',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Department updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * DELETE /api/nextgen/{id} / DELETE /api/ministry/{id}
     * 부서 삭제
     */
    public function delete($id) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'admin')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions',
                    null,
                    403
                );
            }
            
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid department ID',
                    null,
                    400
                );
            }
            
            $department = $this->departmentModel->getById($id);
            if (!$department) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Department not found',
                    null,
                    404
                );
            }
            
            $result = $this->departmentModel->delete($id);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete department',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Department deleted successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/nextgen/{id}/announcements / POST /api/ministry/{id}/announcements
     * 부서 공지사항 추가
     */
    public function addAnnouncement($departmentId) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions',
                    null,
                    403
                );
            }
            
            if (!Validators::validateNumber($departmentId)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid department ID',
                    null,
                    400
                );
            }
            
            $department = $this->departmentModel->getById($departmentId);
            if (!$department) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Department not found',
                    null,
                    404
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!Validators::validateRequired($data, ['title', 'content'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required fields: title, content',
                    null,
                    400
                );
            }
            
            $announcementId = $this->departmentModel->addAnnouncement($departmentId, $data);
            
            if (!$announcementId) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to add announcement',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['announcement_id' => $announcementId],
                'Announcement added successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in addAnnouncement: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * PUT /api/nextgen/{id}/announcements/{anncId} / PUT /api/ministry/{id}/announcements/{anncId}
     * 부서 공지사항 수정
     */
    public function updateAnnouncement($departmentId, $announcementId) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions',
                    null,
                    403
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = $this->departmentModel->updateAnnouncement($announcementId, $data);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to update announcement',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Announcement updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * DELETE /api/nextgen/{id}/announcements/{anncId} / DELETE /api/ministry/{id}/announcements/{anncId}
     * 부서 공지사항 삭제
     */
    public function deleteAnnouncement($departmentId, $announcementId) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'admin')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions',
                    null,
                    403
                );
            }
            
            $result = $this->departmentModel->deleteAnnouncement($announcementId);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete announcement',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Announcement deleted successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
}
?>
