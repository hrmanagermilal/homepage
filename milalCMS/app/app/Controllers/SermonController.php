<?php
class SermonController extends BaseController {
    private SermonModel $model;
    public function __construct() { $this->model=new SermonModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('sermons.view');
        $page=max(1,(int)($_GET['page']??1));
        $data=$this->model->getAll($page);
        $pagination=$this->model->buildPagination($data['total'],$page);
        $pageTitle='설교 관리'; $currentPage='sermons';
        include BASE_PATH.'/app/Views/sermons/index.php';
    }
    public function detail_page(): void {
        AuthMiddleware::requirePermission('sermons.view');
        $id=$this->intGet('id',0);
        $sermon=$this->model->findById($id);
        if(!$sermon){header('Location:'.BASE_URL.'/sermons');exit;}
        $pageTitle='설교 상세'; $currentPage='sermons';
        include BASE_PATH.'/app/Views/sermons/detail.php';
    }
    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.view');
        $this->success($this->model->getAll(max(1,$this->intPost('page',1))));
    }
    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.view');
        $row=$this->model->findById($this->intPost('id'));
        if(!$row) $this->error('설교를 찾을 수 없습니다.',404);
        $this->success($row);
    }
    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.create');
        $err=$this->validateRequired(['title'=>'제목','youtube_url'=>'유튜브 URL'],$_POST); if($err) $this->error($err);
        $url=trim($this->post('youtube_url'));
        if($this->model->urlExists($url)) $this->error('이미 등록된 유튜브 URL입니다.');
        $dateVal=trim($this->post('sermon_date',''));
        $id=$this->model->create([
            'title'=>trim($this->post('title')),
            'youtube_url'=>$url,
            'description'=>$this->post('description',''),
            'preacher'=>trim($this->post('preacher','')),
            'sermon_date'=>$dateVal!==''?$dateVal:null,
        ]);
        $this->success(['id'=>$id],'설교가 등록되었습니다.');
    }
    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.edit');
        $id=$this->intPost('id');
        if(!$this->model->findById($id)) $this->error('설교를 찾을 수 없습니다.',404);
        $url=trim($this->post('youtube_url'));
        if($this->model->urlExists($url,$id)) $this->error('이미 등록된 유튜브 URL입니다.');
        $dateVal=trim($this->post('sermon_date',''));
        $this->model->update($id,[
            'title'=>trim($this->post('title')),
            'youtube_url'=>$url,
            'description'=>$this->post('description',''),
            'preacher'=>trim($this->post('preacher','')),
            'sermon_date'=>$dateVal!==''?$dateVal:null,
        ]);
        $this->success([],'설교가 수정되었습니다.');
    }
    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.delete');
        $id=$this->intPost('id');
        if(!$this->model->findById($id)) $this->error('설교를 찾을 수 없습니다.',404);
        $this->model->delete($id);
        $this->success([],'설교가 삭제되었습니다.');
    }
}
