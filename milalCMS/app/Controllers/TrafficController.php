<?php
class TrafficController extends BaseController {
    private OnlineGivingModel $model;
    public function __construct() { $this->model = new OnlineGivingModel(); AuthMiddleware::requireLogin(); }
    public function index(): void {
        AuthMiddleware::requirePermission('traffic.view');
        $shuttleBus=$this->model->getShuttleBus(); $parkingItems=$this->model->getParkingItems(); $parkingMap=$this->model->getParkingMap();
        $pageTitle='교통 관리'; $currentPage='traffic';
        include BASE_PATH.'/app/Views/traffic/index.php';
    }
    public function shuttleList(): void { $this->assertPost(); AuthMiddleware::requirePermission('traffic.view'); $this->success(['items'=>$this->model->getShuttleBus()]); }
    public function shuttleDetail(): void { $this->assertPost(); AuthMiddleware::requirePermission('traffic.view'); $row=$this->model->findShuttle($this->intPost('id')); if(!$row)$this->error('셔틀 정보를 찾을 수 없습니다.',404); $this->success($row); }
    public function shuttleCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('traffic.create');
        $err=$this->validateRequired(['direction'=>'방향','time'=>'시간','service_label'=>'구분'],$_POST); if($err)$this->error($err);
        $id=$this->model->createShuttle(['direction'=>$this->post('direction'),'time'=>trim($this->post('time')),'service_label'=>trim($this->post('service_label')),'sort_order'=>$this->intPost('sort_order',0),'is_active'=>$this->intPost('is_active',1)]);
        $this->success(['id'=>$id],'셔틀버스 시간이 등록되었습니다.');
    }
    public function shuttleUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('traffic.edit');
        $id=$this->intPost('id'); if(!$this->model->findShuttle($id))$this->error('셔틀 정보를 찾을 수 없습니다.',404);
        $this->model->updateShuttle($id,['direction'=>$this->post('direction'),'time'=>trim($this->post('time')),'service_label'=>trim($this->post('service_label')),'sort_order'=>$this->intPost('sort_order',0),'is_active'=>$this->intPost('is_active',1)]);
        $this->success([],'셔틀버스 시간이 수정되었습니다.');
    }
    public function shuttleDelete(): void { $this->assertPost(); AuthMiddleware::requirePermission('traffic.delete'); if(!$this->model->findShuttle($this->intPost('id')))$this->error('셔틀 정보를 찾을 수 없습니다.',404); $this->model->deleteShuttle($this->intPost('id')); $this->success([],'셔틀버스 시간이 삭제되었습니다.'); }
    public function parkingList(): void { $this->assertPost(); AuthMiddleware::requirePermission('traffic.view'); $this->success(['items'=>$this->model->getParkingItems()]); }
    public function parkingDetail(): void { $this->assertPost(); AuthMiddleware::requirePermission('traffic.view'); $row=$this->model->findParkingItem($this->intPost('id')); if(!$row)$this->error('주차 안내를 찾을 수 없습니다.',404); $this->success($row); }
    public function parkingCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('traffic.create');
        $err=$this->validateRequired(['content'=>'내용'],$_POST); if($err)$this->error($err);
        $id=$this->model->createParkingItem(['content'=>$this->post('content'),'sort_order'=>$this->intPost('sort_order',0),'is_active'=>$this->intPost('is_active',1)]);
        $this->success(['id'=>$id],'주차 안내가 등록되었습니다.');
    }
    public function parkingUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('traffic.edit');
        $id=$this->intPost('id'); if(!$this->model->findParkingItem($id))$this->error('주차 안내를 찾을 수 없습니다.',404);
        $this->model->updateParkingItem($id,['content'=>$this->post('content'),'sort_order'=>$this->intPost('sort_order',0),'is_active'=>$this->intPost('is_active',1)]);
        $this->success([],'주차 안내가 수정되었습니다.');
    }
    public function parkingDelete(): void { $this->assertPost(); AuthMiddleware::requirePermission('traffic.delete'); if(!$this->model->findParkingItem($this->intPost('id')))$this->error('주차 안내를 찾을 수 없습니다.',404); $this->model->deleteParkingItem($this->intPost('id')); $this->success([],'주차 안내가 삭제되었습니다.'); }
    public function parkingReorder(): void { $this->assertPost(); AuthMiddleware::requirePermission('traffic.edit'); $orders=json_decode($this->post('orders','[]'),true); if(empty($orders))$this->error('순서 데이터가 올바르지 않습니다.'); $this->model->reorderParkingItems($orders); $this->success([],'순서가 업데이트되었습니다.'); }
    public function parkingMapUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('traffic.edit');
        $d=['alt_text'=>trim($this->post('alt_text','')),'is_active'=>$this->intPost('is_active',1)];
        if(!empty($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){$up=UploadHelper::uploadImage($_FILES['image'],'parking'); if(!$up['success'])$this->error($up['message']); $d['image_url']=$up['path'];}
        $this->model->upsertParkingMap($d); $this->success([],'주차 지도가 저장되었습니다.');
    }
}
