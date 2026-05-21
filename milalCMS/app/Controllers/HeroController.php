<?php
class HeroController extends BaseController {
    private HeroModel $heroModel;
    public function __construct() { $this->heroModel=new HeroModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('heroes.view');
        $bgImages   = $this->heroModel->getBgImages();
        $frontImage = $this->heroModel->getFrontImage();
        $links      = $this->heroModel->getLinks();
        $pageTitle='히어로 관리'; $currentPage='heroes';
        include BASE_PATH.'/app/Views/heroes/index.php';
    }
    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.view');
        $this->success([
            'bg_images'   => $this->heroModel->getBgImages(),
            'front_image' => $this->heroModel->getFrontImage(),
            'links'       => $this->heroModel->getLinks(),
        ]);
    }

    // -- Background Images
    public function addBgImage(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        if(empty($_FILES['image'])||$_FILES['image']['error']!==UPLOAD_ERR_OK) $this->error('이미지를 선택해주세요.');
        $upload=UploadHelper::uploadImage($_FILES['image'],'heroes');
        if(!$upload['success']) $this->error($upload['message']);
        $id=$this->heroModel->addBgImage($upload['path'],$this->intPost('order',0),$this->post('alt_text',''));
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

    // -- Front Image
    public function upsertFrontImage(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        if(empty($_FILES['image'])||$_FILES['image']['error']!==UPLOAD_ERR_OK) $this->error('이미지를 선택해주세요.');
        $existing=$this->heroModel->getFrontImage();
        if($existing) UploadHelper::deleteFile($existing['image_url']);
        $upload=UploadHelper::uploadImage($_FILES['image'],'heroes');
        if(!$upload['success']) $this->error($upload['message']);
        $this->heroModel->upsertFrontImage($upload['path'],$this->post('alt_text',''));
        $this->success(['image_url'=>$upload['url']],'전면 이미지가 업데이트되었습니다.');
    }
    public function deleteFrontImage(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        $row=$this->heroModel->deleteFrontImage();
        if($row) UploadHelper::deleteFile($row['image_url']);
        $this->success([],'전면 이미지가 삭제되었습니다.');
    }

    // -- Quick Links
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
        $err=$this->validateRequired(['title'=>'제목','link'=>'링크 URL'],$_POST);
        if($err) $this->error($err);
        $imagePath=null;
        if(!empty($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){
            $up=UploadHelper::uploadImage($_FILES['image'],'heroes/icons');
            if(!$up['success']) $this->error($up['message']);
            $imagePath=$up['path'];
        }
        $id=$this->heroModel->createLink([
            'title'=>trim($this->post('title')),
            'link' =>trim($this->post('link')),
            'image'=>$imagePath,
            'desc' =>trim($this->post('desc','')),
        ]);
        $this->success(['id'=>$id],'링크가 추가되었습니다.');
    }
    public function linkUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.edit');
        $id=$this->intPost('id');
        $row=$this->heroModel->findLink($id);
        if(!$row) $this->error('링크를 찾을 수 없습니다.',404);
        $imagePath=$row['image'];
        if(!empty($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){
            $up=UploadHelper::uploadImage($_FILES['image'],'heroes/icons');
            if(!$up['success']) $this->error($up['message']);
            if($imagePath) UploadHelper::deleteFile($imagePath);
            $imagePath=$up['path'];
        }
        $this->heroModel->updateLink($id,[
            'title'=>trim($this->post('title')),
            'link' =>trim($this->post('link')),
            'image'=>$imagePath,
            'desc' =>trim($this->post('desc','')),
        ]);
        $this->success([],'링크가 수정되었습니다.');
    }
    public function linkDelete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('heroes.delete');
        $id=$this->intPost('id');
        $row=$this->heroModel->findLink($id);
        if(!$row) $this->error('링크를 찾을 수 없습니다.',404);
        if($row['image']) UploadHelper::deleteFile($row['image']);
        $this->heroModel->deleteLink($id);
        $this->success([],'링크가 삭제되었습니다.');
    }
}
