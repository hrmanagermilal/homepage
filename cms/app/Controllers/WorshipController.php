<?php
class WorshipController extends BaseController {
    private OnlineGivingModel $model;
    public function __construct() { $this->model = new OnlineGivingModel(); AuthMiddleware::requireLogin(); }
    public function index(): void {
        AuthMiddleware::requirePermission('worship.view');
        $serviceTimes = $this->model->getServiceTimes();
        $pageTitle = '예배 관리'; $currentPage = 'worship';
        include BASE_PATH.'/app/Views/worship/index.php';
    }
    public function serviceList(): void { $this->assertPost(); AuthMiddleware::requirePermission('worship.view'); $this->success(['items'=>$this->model->getServiceTimes()]); }
    public function serviceDetail(): void { $this->assertPost(); AuthMiddleware::requirePermission('worship.view'); $row=$this->model->findServiceTime($this->intPost('id')); if(!$row)$this->error('예배 시간을 찾을 수 없습니다.',404); $this->success($row); }
    public function serviceCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('worship.create');
        $err=$this->validateRequired(['category'=>'카테고리','name'=>'예배명','time'=>'시간'],$_POST); if($err)$this->error($err);
        $id=$this->model->createServiceTime(['category'=>trim($this->post('category')),'name'=>trim($this->post('name')),'day'=>trim($this->post('day','')),'time'=>trim($this->post('time')),'sort_order'=>$this->intPost('sort_order',0),'is_active'=>$this->intPost('is_active',1)]);
        $this->success(['id'=>$id],'예배 시간이 등록되었습니다.');
    }
    public function serviceUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('worship.edit');
        $id=$this->intPost('id'); if(!$this->model->findServiceTime($id))$this->error('예배 시간을 찾을 수 없습니다.',404);
        $this->model->updateServiceTime($id,['category'=>trim($this->post('category')),'name'=>trim($this->post('name')),'day'=>trim($this->post('day','')),'time'=>trim($this->post('time')),'sort_order'=>$this->intPost('sort_order',0),'is_active'=>$this->intPost('is_active',1)]);
        $this->success([],'예배 시간이 수정되었습니다.');
    }
    public function serviceDelete(): void { $this->assertPost(); AuthMiddleware::requirePermission('worship.delete'); if(!$this->model->findServiceTime($this->intPost('id')))$this->error('예배 시간을 찾을 수 없습니다.',404); $this->model->deleteServiceTime($this->intPost('id')); $this->success([],'예배 시간이 삭제되었습니다.'); }
    public function serviceReorder(): void { $this->assertPost(); AuthMiddleware::requirePermission('worship.edit'); $orders=json_decode($this->post('orders','[]'),true); if(empty($orders))$this->error('순서 데이터가 올바르지 않습니다.'); $this->model->reorderServiceTimes($orders); $this->success([],'순서가 업데이트되었습니다.'); }
}
