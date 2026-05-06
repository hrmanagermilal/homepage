<?php
class HeroController extends BaseController {
    private HeroModel $heroModel;
    public function __construct() { $this->heroModel=new HeroModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('heroes.view');
        $heroes=$this->heroModel->getAll();
        $links=$this->heroModel->getLinks();
        $pageTitle='히어로 관리'; $currentPage='heroes';
        include BASE_PATH.'/app/Views/heroes/index.php';
    }
    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.view');
        $this->success(['heroes'=>$this->heroModel->getAll(),'links'=>$this->heroModel->getLinks()]);
    }
    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.view');
        $id=$this->intPost('id');
        $hero=$this->heroModel->findById($id);
        if(!$hero) $this->error('히어로를 찾을 수 없습니다.',404);
        $hero['bg_images']=$this->heroModel->getBgImages($id);
        $hero['front_image']=$this->heroModel->getFrontImage($id);
        $this->success($hero);
    }
    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.create');
        $id=$this->heroModel->create([
            'title'    =>$this->post('title',''),
            'subtitle' =>$this->post('subtitle',''),
            'is_active'=>$this->intPost('is_active',1),
        ]);
        $this->success(['id'=>$id],'히어로가 추가되었습니다.');
    }
    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        $id=$this->intPost('id');
        if(!$this->heroModel->findById($id)) $this->error('히어로를 찾을 수 없습니다.',404);
        $this->heroModel->update($id,[
            'title'    =>$this->post('title',''),
            'subtitle' =>$this->post('subtitle',''),
            'is_active'=>$this->intPost('is_active',1),
        ]);
        $this->success([],'히어로가 수정되었습니다.');
    }
    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.delete');
        $id=$this->intPost('id');
        if(!$this->heroModel->findById($id)) $this->error('히어로를 찾을 수 없습니다.',404);
        foreach($this->heroModel->getBgImages($id) as $bg) UploadHelper::deleteFile($bg['image_url']);
        $fi=$this->heroModel->getFrontImage($id);
        if($fi) UploadHelper::deleteFile($fi['image_url']);
        $this->heroModel->delete($id);
        $this->success([],'히어로가 삭제되었습니다.');
    }

    // ── Hero Links ────────────────────────────────────────
    public function linkList(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.view');
        $this->success(['links'=>$this->heroModel->getLinks()]);
    }
    public function linkDetail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.view');
        $row=$this->heroModel->findLink($this->intPost('id'));
        if(!$row) $this->error('링크를 찾을 수 없습니다.',404);
        $this->success($row);
    }
    public function linkCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.create');
        $err=$this->validateRequired(['title'=>'제목','link_url'=>'링크 URL'],$_POST);
        if($err) $this->error($err);
        $iconPath=null;
        if(!empty($_FILES['icon'])&&$_FILES['icon']['error']===UPLOAD_ERR_OK){
            $up=UploadHelper::uploadImage($_FILES['icon'],'heroes/icons');
            if(!$up['success']) $this->error($up['message']);
            $iconPath=$up['path'];
        }
        $id=$this->heroModel->createLink([
            'title'   =>trim($this->post('title')),
            'icon_url'=>$iconPath,
            'link_url'=>trim($this->post('link_url')),
        ]);
        $this->success(['id'=>$id],'링크가 추가되었습니다.');
    }
    public function linkUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        $id=$this->intPost('id');
        $row=$this->heroModel->findLink($id);
        if(!$row) $this->error('링크를 찾을 수 없습니다.',404);
        $iconPath=$row['icon_url'];
        if(!empty($_FILES['icon'])&&$_FILES['icon']['error']===UPLOAD_ERR_OK){
            $up=UploadHelper::uploadImage($_FILES['icon'],'heroes/icons');
            if(!$up['success']) $this->error($up['message']);
            if($iconPath) UploadHelper::deleteFile($iconPath);
            $iconPath=$up['path'];
        }
        $this->heroModel->updateLink($id,[
            'title'   =>trim($this->post('title')),
            'icon_url'=>$iconPath,
            'link_url'=>trim($this->post('link_url')),
        ]);
        $this->success([],'링크가 수정되었습니다.');
    }
    public function linkDelete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.delete');
        $id=$this->intPost('id');
        $row=$this->heroModel->findLink($id);
        if(!$row) $this->error('링크를 찾을 수 없습니다.',404);
        if($row['icon_url']) UploadHelper::deleteFile($row['icon_url']);
        $this->heroModel->deleteLink($id);
        $this->success([],'링크가 삭제되었습니다.');
    }

    // ── Background Images ─────────────────────────────────
    public function addBgImage(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        $heroId=$this->intPost('hero_id');
        if(!$this->heroModel->findById($heroId)) $this->error('히어로를 찾을 수 없습니다.',404);
        if(empty($_FILES['image'])||$_FILES['image']['error']!==UPLOAD_ERR_OK) $this->error('이미지를 선택해주세요.');
        $upload=UploadHelper::uploadImage($_FILES['image'],'heroes');
        if(!$upload['success']) $this->error($upload['message']);
        $id=$this->heroModel->addBgImage($heroId,$upload['path'],$this->intPost('order',0),$this->post('alt_text',''));
        $this->success(['id'=>$id,'image_url'=>$upload['url']],'배경 이미지가 추가되었습니다.');
    }
    public function deleteBgImage(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        $row=$this->heroModel->deleteBgImage($this->intPost('id'));
        if(!$row) $this->error('이미지를 찾을 수 없습니다.',404);
        UploadHelper::deleteFile($row['image_url']);
        $this->success([],'배경 이미지가 삭제되었습니다.');
    }
    public function reorderBgImages(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        $orders=json_decode($this->post('orders','[]'),true);
        if(empty($orders)) $this->error('순서 데이터가 올바르지 않습니다.');
        $this->heroModel->reorderBgImages($orders);
        $this->success([],'순서가 업데이트되었습니다.');
    }

    // ── Front Image ───────────────────────────────────────
    public function upsertFrontImage(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        $heroId=$this->intPost('hero_id');
        if(!$this->heroModel->findById($heroId)) $this->error('히어로를 찾을 수 없습니다.',404);
        if(empty($_FILES['image'])||$_FILES['image']['error']!==UPLOAD_ERR_OK) $this->error('이미지를 선택해주세요.');
        $existing=$this->heroModel->getFrontImage($heroId);
        if($existing) UploadHelper::deleteFile($existing['image_url']);
        $upload=UploadHelper::uploadImage($_FILES['image'],'heroes');
        if(!$upload['success']) $this->error($upload['message']);
        $this->heroModel->upsertFrontImage($heroId,$upload['path'],$this->post('alt_text',''));
        $this->success(['image_url'=>$upload['url']],'전면 이미지가 업데이트되었습니다.');
    }
    public function deleteFrontImage(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        $heroId=$this->intPost('hero_id');
        $row=$this->heroModel->deleteFrontImage($heroId);
        if($row) UploadHelper::deleteFile($row['image_url']);
        $this->success([],'전면 이미지가 삭제되었습니다.');
    }
}
