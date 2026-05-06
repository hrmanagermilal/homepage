<?php
class CmsController extends BaseController {
    private CmsModel $model;
    public function __construct() { $this->model=new CmsModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('cms.view');
        $pages=$this->model->getPages();
        $pageTitle='페이지 관리'; $currentPage='cms';
        include BASE_PATH.'/app/Views/cms/index.php';
    }
    // Pages
    public function pageList(): void { $this->assertPost(); AuthMiddleware::requirePermission('cms.view'); $this->success(['pages'=>$this->model->getPages()]); }
    public function pageDetail(): void { $this->assertPost(); AuthMiddleware::requirePermission('cms.view'); $p=$this->model->findPage($this->intPost('id')); if(!$p)$this->error('페이지 없음',404); $p['sections']=$this->model->getSectionsByPage($p['id']); $this->success($p); }
    public function pageCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('cms.create');
        $err=$this->validateRequired(['name'=>'페이지명','slug'=>'슬러그'],$_POST); if($err)$this->error($err);
        $slug=trim($this->post('slug'));
        if($this->model->pageSlugExists($slug)) $this->error('이미 사용 중인 슬러그입니다.');
        $id=$this->model->createPage(['name'=>trim($this->post('name')),'slug'=>$slug,'description'=>trim($this->post('description','')),'is_active'=>$this->intPost('is_active',1)]);
        $this->success(['id'=>$id],'페이지가 생성되었습니다.');
    }
    public function pageUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('cms.edit');
        $id=$this->intPost('id'); $slug=trim($this->post('slug'));
        if($this->model->pageSlugExists($slug,$id)) $this->error('이미 사용 중인 슬러그입니다.');
        $this->model->updatePage($id,['name'=>trim($this->post('name')),'slug'=>$slug,'description'=>trim($this->post('description','')),'is_active'=>$this->intPost('is_active',1)]);
        $this->success([],'페이지가 수정되었습니다.');
    }
    public function pageDelete(): void { $this->assertPost(); AuthMiddleware::requirePermission('cms.delete'); $this->model->deletePage($this->intPost('id')); $this->success([],'페이지가 삭제되었습니다.'); }
    // Sections
    public function sectionList(): void { $this->assertPost(); AuthMiddleware::requirePermission('cms.view'); $this->success(['sections'=>$this->model->getSectionsByPage($this->intPost('page_id'))]); }
    public function sectionDetail(): void { $this->assertPost(); AuthMiddleware::requirePermission('cms.view'); $s=$this->model->findSection($this->intPost('id')); if(!$s)$this->error('섹션 없음',404); $s['texts']=$this->model->getTextsBySection($s['id']); $this->success($s); }
    public function sectionCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('cms.create');
        $err=$this->validateRequired(['page_id'=>'페이지','name'=>'섹션명','slug'=>'슬러그'],$_POST); if($err)$this->error($err);
        $pageId=$this->intPost('page_id'); $slug=trim($this->post('slug'));
        if($this->model->sectionSlugExists($pageId,$slug)) $this->error('이 페이지에 이미 같은 슬러그의 섹션이 있습니다.');
        $id=$this->model->createSection(['page_id'=>$pageId,'name'=>trim($this->post('name')),'slug'=>$slug,'description'=>trim($this->post('description','')),'sort_order'=>$this->intPost('sort_order',0),'is_active'=>$this->intPost('is_active',1)]);
        $this->success(['id'=>$id],'섹션이 생성되었습니다.');
    }
    public function sectionUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('cms.edit');
        $id=$this->intPost('id'); $s=$this->model->findSection($id); if(!$s)$this->error('섹션 없음',404);
        $slug=trim($this->post('slug'));
        if($this->model->sectionSlugExists($s['page_id'],$slug,$id)) $this->error('이 페이지에 이미 같은 슬러그의 섹션이 있습니다.');
        $this->model->updateSection($id,['name'=>trim($this->post('name')),'slug'=>$slug,'description'=>trim($this->post('description','')),'sort_order'=>$this->intPost('sort_order',0),'is_active'=>$this->intPost('is_active',1)]);
        $this->success([],'섹션이 수정되었습니다.');
    }
    public function sectionDelete(): void { $this->assertPost(); AuthMiddleware::requirePermission('cms.delete'); $this->model->deleteSection($this->intPost('id')); $this->success([],'섹션이 삭제되었습니다.'); }
    // Texts
    public function textList(): void { $this->assertPost(); AuthMiddleware::requirePermission('cms.view'); $this->success(['texts'=>$this->model->getTextsBySection($this->intPost('section_id'))]); }
    public function textDetail(): void { $this->assertPost(); AuthMiddleware::requirePermission('cms.view'); $t=$this->model->findText($this->intPost('id')); if(!$t)$this->error('텍스트 없음',404); $this->success($t); }
    public function textCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('cms.create');
        $err=$this->validateRequired(['section_id'=>'섹션','key_name'=>'키'],$_POST); if($err)$this->error($err);
        $sectionId=$this->intPost('section_id'); $key=trim($this->post('key_name'));
        if($this->model->textKeyExists($sectionId,$key)) $this->error('이 섹션에 같은 키가 이미 있습니다.');
        $id=$this->model->createText(['section_id'=>$sectionId,'key_name'=>$key,'content_ko'=>$this->post('content_ko',''),'content_en'=>$this->post('content_en',''),'type'=>$this->post('type','text'),'sort_order'=>$this->intPost('sort_order',0)]);
        $this->success(['id'=>$id],'텍스트가 생성되었습니다.');
    }
    public function textUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('cms.edit');
        $id=$this->intPost('id'); $t=$this->model->findText($id); if(!$t)$this->error('텍스트 없음',404);
        $key=trim($this->post('key_name'));
        if($this->model->textKeyExists($t['section_id'],$key,$id)) $this->error('이 섹션에 같은 키가 이미 있습니다.');
        $this->model->updateText($id,['key_name'=>$key,'content_ko'=>$this->post('content_ko',''),'content_en'=>$this->post('content_en',''),'type'=>$this->post('type','text'),'sort_order'=>$this->intPost('sort_order',0)]);
        $this->success([],'텍스트가 수정되었습니다.');
    }
    public function textDelete(): void { $this->assertPost(); AuthMiddleware::requirePermission('cms.delete'); $this->model->deleteText($this->intPost('id')); $this->success([],'텍스트가 삭제되었습니다.'); }
}
