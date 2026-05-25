<?php
class UserController extends BaseController {
    private UserModel $userModel;
    private RoleModel $roleModel;
    public function __construct() { $this->userModel=new UserModel(); $this->roleModel=new RoleModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('users.view');
        $page=max(1,(int)($_GET['page']??1));
        $data=$this->userModel->getAll($page,20);
        $roles=$this->userModel->getRoles();
        $pageTitle='사용자 관리'; $currentPage='users';
        include BASE_PATH.'/app/Views/users/index.php';
    }
    public function list(): void { $this->assertPost(); AuthMiddleware::requirePermission('users.view'); $this->success($this->userModel->getAll(max(1,$this->intPost('page',1)),20)); }
    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('users.view');
        $user=$this->userModel->findById($this->intPost('id'));
        if(!$user) $this->error('사용자를 찾을 수 없습니다.',404);
        unset($user['password_hash']);
        $this->success($user);
    }
    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('users.create');
        $err=$this->validateRequired(['username'=>'아이디','email'=>'이메일','password'=>'비밀번호','name'=>'이름','role_id'=>'역할'],$_POST);
        if($err) $this->error($err);
        $username=trim($this->post('username')); $email=trim($this->post('email'));
        if(strlen($this->post('password'))<8) $this->error('비밀번호는 최소 8자 이상이어야 합니다.');
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $this->error('올바른 이메일 형식이 아닙니다.');
        if($this->userModel->usernameExists($username)) $this->error('이미 사용 중인 아이디입니다.');
        if($this->userModel->emailExists($email)) $this->error('이미 사용 중인 이메일입니다.');
        $id=$this->userModel->create(['username'=>$username,'email'=>$email,'password'=>$this->post('password'),'name'=>trim($this->post('name')),'role_id'=>$this->intPost('role_id'),'is_active'=>$this->intPost('is_active',1)]);
        $this->success(['id'=>$id],'사용자가 생성되었습니다.');
    }
    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('users.edit');
        $id=$this->intPost('id');
        $user=$this->userModel->findById($id);
        if(!$user) $this->error('사용자를 찾을 수 없습니다.',404);
        $data=['role_id'=>$this->intPost('role_id',$user['role_id']),'name'=>trim($this->post('name',$user['name'])),'is_active'=>$this->intPost('is_active',1)];
        $pw=$this->post('password','');
        if(!empty($pw)){if(strlen($pw)<8)$this->error('비밀번호는 최소 8자 이상이어야 합니다.');$data['password']=$pw;}
        $this->userModel->update($id,$data);
        $this->success([],'사용자 정보가 수정되었습니다.');
    }
    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('users.delete');
        $id=$this->intPost('id');
        if($id===AuthMiddleware::getUserId()) $this->error('자신의 계정은 삭제할 수 없습니다.');
        $this->userModel->delete($id);
        $this->success([],'사용자가 삭제되었습니다.');
    }
    public function roles(): void { $this->assertPost(); AuthMiddleware::requirePermission('users.view'); $this->success(['roles'=>$this->userModel->getRoles()]); }
    public function rolesPage(): void {
        AuthMiddleware::requirePermission('users.view');
        $roles=$this->roleModel->getAll();
        $permissions=$this->roleModel->getPermissionsGrouped();
        $pageTitle='역할 & 권한 관리'; $currentPage='roles';
        include BASE_PATH.'/app/Views/users/roles.php';
    }
    public function roleDetail(): void { $this->assertPost(); AuthMiddleware::requirePermission('users.view'); $id=$this->intPost('id'); $role=$this->roleModel->findById($id); if(!$role)$this->error('역할을 찾을 수 없습니다.',404); $this->success(['role'=>$role,'permissions'=>$this->roleModel->getPermissionsByRole($id)]); }
    public function roleCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('users.create');
        $err=$this->validateRequired(['name'=>'역할명','slug'=>'슬러그'],$_POST); if($err)$this->error($err);
        $slug=trim($this->post('slug'));
        if($this->roleModel->slugExists($slug)) $this->error('이미 사용 중인 슬러그입니다.');
        $id=$this->roleModel->create(['name'=>trim($this->post('name')),'slug'=>$slug,'description'=>trim($this->post('description','')),'permissions'=>$_POST['permissions']??[]]);
        $this->success(['id'=>$id],'역할이 생성되었습니다.');
    }
    public function roleUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('users.edit');
        $id=$this->intPost('id'); $slug=trim($this->post('slug'));
        if($this->roleModel->slugExists($slug,$id)) $this->error('이미 사용 중인 슬러그입니다.');
        $this->roleModel->update($id,['name'=>trim($this->post('name')),'slug'=>$slug,'description'=>trim($this->post('description','')),'permissions'=>$_POST['permissions']??[]]);
        $this->success([],'역할이 수정되었습니다.');
    }
    public function roleDelete(): void { $this->assertPost(); AuthMiddleware::requirePermission('users.delete'); $this->roleModel->delete($this->intPost('id')); $this->success([],'역할이 삭제되었습니다.'); }
}
