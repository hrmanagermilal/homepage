<?php
class NewsController extends BaseController {
    private NewsModel $model;
    public function __construct() { $this->model=new NewsModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('news.view');
        $page=max(1,(int)($_GET['page']??1));
        $cat=$_GET['category']??'';
        $data=$this->model->getAll($page,ITEMS_PER_PAGE,$cat);
        $pagination=$this->model->buildPagination($data['total'],$page);
        $pageTitle='뉴스 관리'; $currentPage='news';
        include BASE_PATH.'/app/Views/news/index.php';
    }
    public function detail_page(): void {
        AuthMiddleware::requirePermission('news.view');
        $id=$this->intGet('id',0);
        $news=$this->model->findById($id);
        if(!$news){header('Location:'.BASE_URL.'/news');exit;}
        $this->model->incrementViews($id);
        $pageTitle='뉴스 상세'; $currentPage='news';
        include BASE_PATH.'/app/Views/news/detail.php';
    }
    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('news.view');
        $this->success($this->model->getAll(max(1,$this->intPost('page',1)),ITEMS_PER_PAGE,$this->post('category','')));
    }
    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('news.view');
        $row=$this->model->findById($this->intPost('id'));
        if(!$row) $this->error('뉴스를 찾을 수 없습니다.',404);
        $this->success($row);
    }
    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('news.create');
        $err=$this->validateRequired(['title'=>'제목','content'=>'내용'],$_POST); if($err) $this->error($err);
        // 작성자: 로그인 사용자 이름 디폴트
        $author=trim($this->post('author',''));
        if($author==='') $author=$_SESSION['name']??$_SESSION['name']??'';
        $data=['title'=>trim($this->post('title')),'content'=>$this->post('content'),
               'author'=>$author,'category'=>$this->post('category','news'),
               'tags'=>trim($this->post('tags',''))];
        if(!empty($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){
            $up=UploadHelper::uploadImage($_FILES['image'],'news');
            if(!$up['success']) $this->error($up['message']);
            $data['image']=$up['path'];
        }
        $id=$this->model->create($data);
        $this->success(['id'=>$id],'뉴스가 등록되었습니다.');
    }
    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('news.edit');
        $id=$this->intPost('id');
        $row=$this->model->findById($id);
        if(!$row) $this->error('뉴스를 찾을 수 없습니다.',404);
        $author=trim($this->post('author',''));
        if($author==='') $author=$_SESSION['name']??$_SESSION['name']??'';
        $data=['title'=>trim($this->post('title')),'content'=>$this->post('content'),
               'author'=>$author,'category'=>$this->post('category','news'),
               'tags'=>trim($this->post('tags',''))];
        if(!empty($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){
            $up=UploadHelper::uploadImage($_FILES['image'],'news');
            if(!$up['success']) $this->error($up['message']);
            if($row['image']) UploadHelper::deleteFile($row['image']);
            $data['image']=$up['path'];
        }
        $this->model->update($id,$data);
        $this->success([],'뉴스가 수정되었습니다.');
    }
    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('news.delete');
        $id=$this->intPost('id');
        $row=$this->model->findById($id);
        if(!$row) $this->error('뉴스를 찾을 수 없습니다.',404);
        if($row['image']) UploadHelper::deleteFile($row['image']);
        $this->model->delete($id);
        $this->success([],'뉴스가 삭제되었습니다.');
    }
}
