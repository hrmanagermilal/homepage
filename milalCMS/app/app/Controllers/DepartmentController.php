<?php
class DepartmentController extends BaseController {
    private DepartmentModel $model;
    public function __construct() { $this->model=new DepartmentModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('departments.view');
        $type=$_GET['type']??'';
        $departments=$this->model->getAll($type);
        $pageTitle='부서 관리'; $currentPage='departments';
        include BASE_PATH.'/app/Views/departments/index.php';
    }
    public function detail_page(): void {
        AuthMiddleware::requirePermission('departments.view');
        $id=$this->intGet('id',0);
        $dept=$this->model->findById($id);
        if(!$dept){header('Location:'.BASE_URL.'/departments');exit;}
        $page=max(1,(int)($_GET['page']??1));
        $annData=$this->model->getAnnouncements($id,$page);
        $pagination=$this->paginateDept($annData['total'],$page);
        $pageTitle=$dept['name'].' 상세'; $currentPage='departments';
        include BASE_PATH.'/app/Views/departments/detail.php';
    }
    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.view');
        $this->success(['departments'=>$this->model->getAll($this->post('type',''))]);
    }
    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.view');
        $row=$this->model->findById($this->intPost('id'));
        if(!$row) $this->error('부서를 찾을 수 없습니다.',404);
        $this->success($row);
    }
    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.create');
        $err=$this->validateRequired(['name'=>'부서명'],$_POST); if($err) $this->error($err);
        $data=['department_type'=>$this->post('department_type','ministry'),'name'=>trim($this->post('name')),
               'description'=>$this->post('description',''),'age_group'=>trim($this->post('age_group','')),
               'ministry_type'=>trim($this->post('ministry_type','')),'worship_day'=>trim($this->post('worship_day','')),
               'worship_time'=>trim($this->post('worship_time','')),'worship_location'=>trim($this->post('worship_location','')),
               'clergy_name'=>trim($this->post('clergy_name','')),'clergy_position'=>trim($this->post('clergy_position','')),
               'clergy_phone'=>trim($this->post('clergy_phone','')),'order'=>$this->intPost('order',0),
               'is_active'=>$this->intPost('is_active',1)];
        if(!empty($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){
            $up=UploadHelper::uploadImage($_FILES['image'],'departments');
            if(!$up['success']) $this->error($up['message']);
            $data['image']=$up['path'];
        }
        $id=$this->model->create($data);
        $this->success(['id'=>$id],'부서가 등록되었습니다.');
    }
    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.edit');
        $id=$this->intPost('id');
        $row=$this->model->findById($id);
        if(!$row) $this->error('부서를 찾을 수 없습니다.',404);
        $data=['department_type'=>$this->post('department_type','ministry'),'name'=>trim($this->post('name')),
               'description'=>$this->post('description',''),'age_group'=>trim($this->post('age_group','')),
               'ministry_type'=>trim($this->post('ministry_type','')),'worship_day'=>trim($this->post('worship_day','')),
               'worship_time'=>trim($this->post('worship_time','')),'worship_location'=>trim($this->post('worship_location','')),
               'clergy_name'=>trim($this->post('clergy_name','')),'clergy_position'=>trim($this->post('clergy_position','')),
               'clergy_phone'=>trim($this->post('clergy_phone','')),'order'=>$this->intPost('order',0),
               'is_active'=>$this->intPost('is_active',1)];
        if(!empty($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){
            $up=UploadHelper::uploadImage($_FILES['image'],'departments');
            if(!$up['success']) $this->error($up['message']);
            if($row['image']) UploadHelper::deleteFile($row['image']);
            $data['image']=$up['path'];
        }
        $this->model->update($id,$data);
        $this->success([],'부서가 수정되었습니다.');
    }
    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.delete');
        $id=$this->intPost('id');
        $row=$this->model->findById($id);
        if(!$row) $this->error('부서를 찾을 수 없습니다.',404);
        if($row['image']) UploadHelper::deleteFile($row['image']);
        $this->model->delete($id);
        $this->success([],'부서가 삭제되었습니다.');
    }
    public function reorder(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.edit');
        $orders=json_decode($this->post('orders','[]'),true);
        if(empty($orders)) $this->error('순서 데이터가 올바르지 않습니다.');
        $this->model->updateOrder($orders);
        $this->success([],'순서가 업데이트되었습니다.');
    }
    public function announcements(): void {
        AuthMiddleware::requirePermission('departments.view');
        $deptId=$this->intGet('dept_id',0);
        $dept=$this->model->findById($deptId);
        if(!$dept){header('Location:'.BASE_URL.'/departments');exit;}
        $page=max(1,(int)($_GET['page']??1));
        $data=$this->model->getAnnouncements($deptId,$page);
        $pagination=$this->paginateDept($data['total'],$page);
        $pageTitle=$dept['name'].' - 부서 공지'; $currentPage='departments';
        include BASE_PATH.'/app/Views/departments/announcements.php';
    }
    public function announcementList(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.view');
        $this->success($this->model->getAnnouncements($this->intPost('dept_id'),$this->intPost('page',1)));
    }
    public function announcementDetail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.view');
        $row=$this->model->findAnnouncement($this->intPost('id'));
        if(!$row) $this->error('공지를 찾을 수 없습니다.',404);
        $this->success($row);
    }
    public function announcementCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.create');
        $err=$this->validateRequired(['title'=>'제목','content'=>'내용','dept_id'=>'부서'],$_POST); if($err) $this->error($err);
        $deptId=$this->intPost('dept_id');
        if(!$this->model->findById($deptId)) $this->error('부서를 찾을 수 없습니다.',404);
        $id=$this->model->createAnnouncement($deptId,['title'=>trim($this->post('title')),'content'=>$this->post('content'),'link'=>trim($this->post('link',''))]);
        $this->success(['id'=>$id],'부서 공지가 등록되었습니다.');
    }
    public function announcementUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.edit');
        $id=$this->intPost('id');
        if(!$this->model->findAnnouncement($id)) $this->error('공지를 찾을 수 없습니다.',404);
        $this->model->updateAnnouncement($id,['title'=>trim($this->post('title')),'content'=>$this->post('content'),'link'=>trim($this->post('link',''))]);
        $this->success([],'부서 공지가 수정되었습니다.');
    }
    public function announcementDelete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('departments.delete');
        if(!$this->model->findAnnouncement($this->intPost('id'))) $this->error('공지를 찾을 수 없습니다.',404);
        $this->model->deleteAnnouncement($this->intPost('id'));
        $this->success([],'부서 공지가 삭제되었습니다.');
    }
    private function paginateDept(int $total, int $cur): array {
        $perPage=ITEMS_PER_PAGE;
        $totalPages=max(1,(int)ceil($total/$perPage));
        $half=(int)floor(PAGE_RANGE/2);
        $start=max(1,$cur-$half); $end=min($totalPages,$start+PAGE_RANGE-1);
        if($end-$start+1<PAGE_RANGE) $start=max(1,$end-PAGE_RANGE+1);
        return ['total'=>$total,'per_page'=>$perPage,'current'=>$cur,'total_pages'=>$totalPages,
                'start_page'=>$start,'end_page'=>$end,'has_prev'=>$cur>1,'has_next'=>$cur<$totalPages];
    }
}
