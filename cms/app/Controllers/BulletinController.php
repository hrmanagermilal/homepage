<?php
class BulletinController extends BaseController {
    private BulletinModel $model;
    public function __construct() { $this->model=new BulletinModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('bulletins.view');
        $page=max(1,(int)($_GET['page']??1));
        $data=$this->model->getAll($page);
        $pagination=$this->model->buildPagination($data['total'],$page);
        $pageTitle='주보 관리'; $currentPage='bulletins';
        include BASE_PATH.'/app/Views/bulletins/index.php';
    }
    public function detail_page(): void {
        AuthMiddleware::requirePermission('bulletins.view');
        $id=$this->intGet('id',0);
        $bulletin=$this->model->findById($id);
        if(!$bulletin){header('Location:'.BASE_URL.'/bulletins');exit;}
        $images=$this->model->getImages($id);
        $pageTitle='주보 상세'; $currentPage='bulletins';
        include BASE_PATH.'/app/Views/bulletins/detail.php';
    }
    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('bulletins.view');
        $this->success($this->model->getAll(max(1,$this->intPost('page',1))));
    }
    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('bulletins.view');
        $id=$this->intPost('id');
        $row=$this->model->findById($id);
        if(!$row) $this->error('주보를 찾을 수 없습니다.',404);
        $row['images']=$this->model->getImages($id);
        $this->success($row);
    }
    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('bulletins.create');
        $err=$this->validateRequired(['title'=>'제목'],$_POST); if($err) $this->error($err);
        $weekNum=trim($this->post('week_number',''));
        $d=[
            'title'       => trim($this->post('title')),
            'week_number' => $weekNum!==''?(int)$weekNum:null,
            'year'        => (int)$this->post('year',date('Y')),
        ];
        // PDF 업로드
        if(!empty($_FILES['attachment'])&&$_FILES['attachment']['error']===UPLOAD_ERR_OK){
            $up=$this->uploadPdf($_FILES['attachment'],'bulletins');
            if(!$up['success']) $this->error($up['message']);
            $d['attachment']=$up['path'];
        }
        $id=$this->model->create($d);
        // 이미지: bulletins/{id}/ 폴더에 저장
        $uploaded=[];
        if(!empty($_FILES['images'])&&is_array($_FILES['images']['name'])){
            foreach($_FILES['images']['name'] as $i=>$name){
                if(empty($name)||$_FILES['images']['error'][$i]!==UPLOAD_ERR_OK) continue;
                $file=['name'=>$name,'type'=>$_FILES['images']['type'][$i],
                       'tmp_name'=>$_FILES['images']['tmp_name'][$i],
                       'error'=>$_FILES['images']['error'][$i],
                       'size'=>$_FILES['images']['size'][$i]];
                $up=UploadHelper::uploadImage($file,'bulletins/'.$id);
                if($up['success']){
                    $this->model->addImage((int)$id,$up['path'],$i);
                    $uploaded[]=['url'=>$up['url'],'order'=>$i];
                }
            }
        }
        $this->success(['id'=>$id,'uploaded'=>$uploaded],'주보가 등록되었습니다.');
    }
    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('bulletins.edit');
        $id=$this->intPost('id');
        $row=$this->model->findById($id);
        if(!$row) $this->error('주보를 찾을 수 없습니다.',404);
        $weekNum=trim($this->post('week_number',''));
        $d=[
            'title'       => trim($this->post('title')),
            'week_number' => $weekNum!==''?(int)$weekNum:null,
            'year'        => (int)$this->post('year',date('Y')),
        ];
        // PDF 교체
        if(!empty($_FILES['attachment'])&&$_FILES['attachment']['error']===UPLOAD_ERR_OK){
            $up=$this->uploadPdf($_FILES['attachment'],'bulletins');
            if(!$up['success']) $this->error($up['message']);
            if($row['attachment']) UploadHelper::deleteFile($row['attachment']);
            $d['attachment']=$up['path'];
        }
        // PDF 삭제 요청
        if($this->post('remove_attachment','')=='1'){
            if($row['attachment']) UploadHelper::deleteFile($row['attachment']);
            $d['attachment']=null;
        }
        $this->model->update($id,$d);
        $this->success([],'주보가 수정되었습니다.');
    }
    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('bulletins.delete');
        $id=$this->intPost('id');
        $row=$this->model->findById($id);
        if(!$row) $this->error('주보를 찾을 수 없습니다.',404);
        if($row['attachment']) UploadHelper::deleteFile($row['attachment']);
        foreach($this->model->getImages($id) as $img) UploadHelper::deleteFile($img['image_url']);
        $this->model->delete($id);
        $this->success([],'주보가 삭제되었습니다.');
    }
    public function addImage(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('bulletins.edit');
        $bulletinId=$this->intPost('bulletin_id');
        if(!$this->model->findById($bulletinId)) $this->error('주보를 찾을 수 없습니다.',404);
        if(empty($_FILES['image'])||$_FILES['image']['error']!==UPLOAD_ERR_OK) $this->error('이미지를 선택해주세요.');
        $up=UploadHelper::uploadImage($_FILES['image'],'bulletins/'.$bulletinId);
        if(!$up['success']) $this->error($up['message']);
        $imgId=$this->model->addImage($bulletinId,$up['path'],(int)$this->intPost('order',0));
        $this->success(['id'=>$imgId,'image_url'=>$up['path'],'url'=>$up['url']],'이미지가 추가되었습니다.');
    }
    public function addImages(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('bulletins.edit');
        $bulletinId=$this->intPost('bulletin_id');
        if(!$this->model->findById($bulletinId)) $this->error('주보를 찾을 수 없습니다.',404);
        $results=[];
        if(!empty($_FILES['images'])&&is_array($_FILES['images']['name'])){
            foreach($_FILES['images']['name'] as $i=>$name){
                if(empty($name)||$_FILES['images']['error'][$i]!==UPLOAD_ERR_OK) continue;
                $file=['name'=>$name,'type'=>$_FILES['images']['type'][$i],
                       'tmp_name'=>$_FILES['images']['tmp_name'][$i],
                       'error'=>$_FILES['images']['error'][$i],
                       'size'=>$_FILES['images']['size'][$i]];
                $up=UploadHelper::uploadImage($file,'bulletins/'.$bulletinId);
                if($up['success']){
                    $existingCount=count($this->model->getImages($bulletinId));
                    $imgId=$this->model->addImage($bulletinId,$up['path'],$existingCount+$i);
                    $results[]=['id'=>$imgId,'image_url'=>$up['path'],'url'=>$up['url']];
                }
            }
        }
        $this->success(['images'=>$results],count($results).'장 업로드 완료');
    }
    public function deleteImage(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('bulletins.edit');
        $row=$this->model->deleteImage($this->intPost('id'));
        if(!$row) $this->error('이미지를 찾을 수 없습니다.',404);
        UploadHelper::deleteFile($row['image_url']);
        $this->success([],'이미지가 삭제되었습니다.');
    }
    public function reorderImages(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('bulletins.edit');
        $orders=json_decode($this->post('orders','[]'),true);
        if(empty($orders)) $this->error('순서 데이터가 올바르지 않습니다.');
        $this->model->reorderImages($orders);
        $this->success([],'순서가 업데이트되었습니다.');
    }

    // PDF 전용 업로드 헬퍼
    private function uploadPdf(array $file, string $subDir): array {
        if($file['error']!==UPLOAD_ERR_OK) return ['success'=>false,'message'=>'파일 업로드 오류'];
        $allowedMimes=['application/pdf','application/x-pdf'];
        $finfo=finfo_open(FILEINFO_MIME_TYPE);
        $mime=finfo_file($finfo,$file['tmp_name']);
        finfo_close($finfo);
        if(!in_array($mime,$allowedMimes)&&!str_ends_with($file['name'],'.pdf'))
            return ['success'=>false,'message'=>'PDF 파일만 업로드 가능합니다.'];
        if($file['size']>20*1024*1024) return ['success'=>false,'message'=>'PDF는 최대 20MB까지 가능합니다.'];
        $dir=UPLOAD_PATH.rtrim($subDir,'/').'/' ;
        if(!is_dir($dir)) mkdir($dir,0755,true);
        $filename=date('Ymd_His').'_'.bin2hex(random_bytes(4)).'.pdf';
        $destPath=$dir.$filename;
        if(!move_uploaded_file($file['tmp_name'],$destPath)) return ['success'=>false,'message'=>'파일 저장 실패'];
        $relPath='/uploads/'.rtrim($subDir,'/').'/'.$filename;
        return ['success'=>true,'path'=>$relPath,'url'=>BASE_URL.$relPath];
    }
}
