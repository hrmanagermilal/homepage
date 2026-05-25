<?php
class BannerController extends BaseController {
    private OnlineGivingModel $model;
    public function __construct() { $this->model = new OnlineGivingModel(); AuthMiddleware::requireLogin(); }
    public function index(): void {
        AuthMiddleware::requirePermission('onlinegiving.view');
        $banner=$this->model->getBanner();
        $pageTitle='배너 관리'; $currentPage='banner';
        include BASE_PATH.'/app/Views/banner/index.php';
    }
    public function bannerUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('onlinegiving.edit');
        $d=['alt_text'=>trim($this->post('alt_text','')),'is_active'=>$this->intPost('is_active',1)];
        if(!empty($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){$up=UploadHelper::uploadImage($_FILES['image'],'banner'); if(!$up['success'])$this->error($up['message']); $d['image_url']=$up['path'];}
        $this->model->upsertBanner($d); $this->success([],'배너 이미지가 저장되었습니다.');
    }
}
