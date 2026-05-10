<?php
class AnnouncementController extends BaseController {
    private AnnouncementModel $model;
    public function __construct() { $this->model=new AnnouncementModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('announcements.view');
        $page=max(1,(int)($_GET['page']??1));
        $category=$_GET['category']??'';
        $data=$this->model->getAll($page,ITEMS_PER_PAGE,$category);
        $pagination=$this->model->buildPagination($data['total'],$page);
        $pageTitle='공지사항 관리'; $currentPage='announcements';
        include BASE_PATH.'/app/Views/announcements/index.php';
    }
    public function detail_page(): void {
        AuthMiddleware::requirePermission('announcements.view');
        $id=$this->intGet('id',0);
        $announcement=$this->model->findById($id);
        if(!$announcement){header('Location:'.BASE_URL.'/announcements');exit;}
        $this->model->incrementViews($id);
        $pageTitle='공지사항 상세'; $currentPage='announcements';
        include BASE_PATH.'/app/Views/announcements/detail.php';
    }
    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('announcements.view');
        $page=max(1,$this->intPost('page',1));
        $cat=$this->post('category','');
        $this->success($this->model->getAll($page,ITEMS_PER_PAGE,$cat));
    }
    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('announcements.view');
        $row=$this->model->findById($this->intPost('id'));
        if(!$row) $this->error('공지사항을 찾을 수 없습니다.',404);
        $this->success($row);
    }
    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('announcements.create');
        $err=$this->validateRequired(['title'=>'제목','content'=>'내용'],$_POST); if($err) $this->error($err);
        $data=['title'=>trim($this->post('title')),'content'=>$this->post('content'),
               'link'=>trim($this->post('link','')),'category'=>$this->post('category','general'),
               'is_pinned'=>$this->intPost('is_pinned',0),'is_active'=>$this->intPost('is_active',1)];
        if(!empty($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){
            $upload=UploadHelper::uploadImage($_FILES['image'],'announcements');
            if(!$upload['success']) $this->error($upload['message']);
            $data['image']=$upload['path'];
        }
        $id=$this->model->create($data,AuthMiddleware::getUserId());
        $this->success(['id'=>$id],'공지사항이 등록되었습니다.');
    }
    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('announcements.edit');
        $id=$this->intPost('id');
        $row=$this->model->findById($id);
        if(!$row) $this->error('공지사항을 찾을 수 없습니다.',404);
        $data=['title'=>trim($this->post('title')),'content'=>$this->post('content'),
               'link'=>trim($this->post('link','')),'category'=>$this->post('category','general'),
               'is_pinned'=>$this->intPost('is_pinned',0),'is_active'=>$this->intPost('is_active',1)];
        if(!empty($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){
            $upload=UploadHelper::uploadImage($_FILES['image'],'announcements');
            if(!$upload['success']) $this->error($upload['message']);
            if($row['image']) UploadHelper::deleteFile($row['image']);
            $data['image']=$upload['path'];
        }
        $this->model->update($id,$data);
        $this->success([],'공지사항이 수정되었습니다.');
    }
    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('announcements.delete');
        $id=$this->intPost('id');
        $row=$this->model->findById($id);
        if(!$row) $this->error('공지사항을 찾을 수 없습니다.',404);
        if($row['image']) UploadHelper::deleteFile($row['image']);
        $this->model->delete($id);
        $this->success([],'공지사항이 삭제되었습니다.');
    }
    public function togglePin(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('announcements.edit');
        $id=$this->intPost('id');
        if(!$this->model->findById($id)) $this->error('공지사항을 찾을 수 없습니다.',404);
        $this->model->togglePin($id);
        $this->success([],'핀 상태가 변경되었습니다.');
    }
}
