<?php
class MemberController extends BaseController {
    private MemberModel $memberModel;
    public function __construct() { $this->memberModel=new MemberModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('members.view');
        $page=max(1,(int)($_GET['page']??1));
        $data=$this->memberModel->getAll($page);
        $pagination=$this->memberModel->buildPagination($data['total'],$page);
        $pageTitle='교인·목회자 관리'; $currentPage='members';
        include BASE_PATH.'/app/Views/members/index.php';
    }
    public function detail_page(): void {
        AuthMiddleware::requirePermission('members.view');
        $id=$this->intGet('id',0);
        $member=$this->memberModel->findById($id);
        if(!$member){header('Location:'.BASE_URL.'/members');exit;}
        $pageTitle='교인 상세'; $currentPage='members';
        include BASE_PATH.'/app/Views/members/detail.php';
    }
    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('members.view');
        $page=max(1,$this->intPost('page',1));
        $data=$this->memberModel->getAll($page);
        $this->success($data);
    }
    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('members.view');
        $m=$this->memberModel->findById($this->intPost('id'));
        if(!$m) $this->error('교인을 찾을 수 없습니다.',404);
        $this->success($m);
    }
    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('members.create');
        $err=$this->validateRequired(['name'=>'이름'],$_POST); if($err) $this->error($err);
        $data=['name'=>trim($this->post('name')),'email'=>trim($this->post('email','')),
               'title'=>trim($this->post('title','')),'role'=>trim($this->post('role','')),
               'position'=>trim($this->post('position','')),'biography'=>trim($this->post('biography','')),
               'sort_order'=>$this->intPost('sort_order',0),'is_active'=>$this->intPost('is_active',1)];
        if(!empty($_FILES['picture'])&&$_FILES['picture']['error']===UPLOAD_ERR_OK){
            $upload=UploadHelper::uploadImage($_FILES['picture'],'members');
            if(!$upload['success']) $this->error($upload['message']);
            $data['picture']=$upload['path'];
        }
        $id=$this->memberModel->create($data);
        $this->success(['id'=>$id],'교인이 등록되었습니다.');
    }
    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('members.edit');
        $id=$this->intPost('id');
        $m=$this->memberModel->findById($id);
        if(!$m) $this->error('교인을 찾을 수 없습니다.',404);
        $data=['name'=>trim($this->post('name',$m['name'])),'email'=>trim($this->post('email','')),
               'title'=>trim($this->post('title','')),'role'=>trim($this->post('role','')),
               'position'=>trim($this->post('position','')),'biography'=>trim($this->post('biography','')),
               'sort_order'=>$this->intPost('sort_order',0),'is_active'=>$this->intPost('is_active',1)];
        if(!empty($_FILES['picture'])&&$_FILES['picture']['error']===UPLOAD_ERR_OK){
            $upload=UploadHelper::uploadImage($_FILES['picture'],'members');
            if(!$upload['success']) $this->error($upload['message']);
            if($m['picture']) UploadHelper::deleteFile($m['picture']);
            $data['picture']=$upload['path'];
        }
        $this->memberModel->update($id,$data);
        $this->success([],'교인 정보가 수정되었습니다.');
    }
    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('members.delete');
        $id=$this->intPost('id');
        $m=$this->memberModel->findById($id);
        if(!$m) $this->error('교인을 찾을 수 없습니다.',404);
        if($m['picture']) UploadHelper::deleteFile($m['picture']);
        $this->memberModel->delete($id);
        $this->success([],'교인이 삭제되었습니다.');
    }
    public function reorder(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('members.edit');
        $orders=json_decode($this->post('orders','[]'),true);
        if(empty($orders)||!is_array($orders)) $this->error('순서 데이터가 올바르지 않습니다.');
        $this->memberModel->updateOrder($orders);
        $this->success([],'순서가 업데이트되었습니다.');
    }
}
