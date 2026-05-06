<?php
class AuthController extends BaseController {
    private UserModel $userModel;
    public function __construct() { $this->userModel=new UserModel(); AuthMiddleware::start(); }

    public function loginPage(): void {
        if(!empty($_SESSION['user_id'])) $this->redirect(BASE_URL.'/dashboard');
        include BASE_PATH.'/app/Views/auth/login.php';
    }

    public function login(): void {
        $this->assertPost();
        $username=trim($this->post('username',''));
        $password=$this->post('password','');
        if(empty($username)||empty($password)) $this->error('아이디와 비밀번호를 입력하세요.');
        $user=$this->userModel->findByUsername($username);
        if(!$user||!password_verify($password,$user['password_hash'])) $this->error('아이디 또는 비밀번호가 올바르지 않습니다.');
        if(!$user['is_active']) $this->error('비활성화된 계정입니다. 관리자에게 문의하세요.');
        $perms=$this->userModel->getPermissions((int)$user['id']);
        AuthMiddleware::login($user,$perms);
        $this->userModel->updateLastLogin((int)$user['id']);
        $this->success(['redirect'=>BASE_URL.'/dashboard'],'로그인 성공');
    }

    public function logout(): void {
        AuthMiddleware::logout();
        $this->success([],'로그아웃 되었습니다.');
    }

    public function updateProfile(): void {
        $this->assertPost();
        AuthMiddleware::requireLogin();
        $userId=AuthMiddleware::getUserId();
        $name=trim($this->post('name',''));
        $pw=$this->post('password','');
        $pw2=$this->post('password_confirm','');
        if(empty($name)) $this->error('이름을 입력하세요.');
        $data=['role_id'=>$_SESSION['role_id'],'name'=>$name,'is_active'=>1];
        if(!empty($pw)){
            if(strlen($pw)<8) $this->error('비밀번호는 최소 8자 이상이어야 합니다.');
            if($pw!==$pw2) $this->error('비밀번호가 일치하지 않습니다.');
            $data['password']=$pw;
        }
        $this->userModel->update($userId,$data);
        $_SESSION['name']=$name;
        $this->success([],'프로필이 업데이트되었습니다.');
    }
}
