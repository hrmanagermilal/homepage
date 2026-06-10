<?php
AuthMiddleware::start();
$session = AuthMiddleware::getSession();
$isSuperAdmin = AuthMiddleware::isSuperAdmin();

if (!function_exists('hasPerm')) {
    function hasPerm(string $p): bool {
        if (AuthMiddleware::isSuperAdmin()) return true;
        $perms = $_SESSION['permissions'] ?? [];
        return !empty($perms[$p]);
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle??'') ?> — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap">
<script>(function(){var t=localStorage.getItem('milal-theme');if(t&&t!=='dark-green')document.documentElement.setAttribute('data-theme',t);})();</script>
<style>
:root{--sidebar-w:240px;--header-h:56px;--primary:#5c7840;--primary-dark:#4a6530;--secondary:#e07b39;--bg:#f4f5f1;--surface:#fff;--text:#1e2818;--text-muted:#4a5c34;--border:#c8d5b0;--success:#10b981;--warning:#f59e0b;--danger:#ef4444;--info:#3b82f6;--radius:12px;--shadow:0 1px 3px rgba(30,40,24,.08);--shadow-md:0 4px 12px rgba(30,40,24,.12);--sidebar-bg:#3a472b;--sidebar-text:#c5d4a8;--sidebar-accent:#9ab870;--sidebar-hover:rgba(92,120,64,.3);--focus-ring:rgba(92,120,64,.18);}
html[data-theme="dark-blue"]{--primary:#2563eb;--primary-dark:#1d4ed8;--secondary:#f59e0b;--bg:#f0f4fc;--text:#0f172a;--text-muted:#475569;--border:#c7d7f5;--sidebar-bg:#1a2c4e;--sidebar-text:#93c5fd;--sidebar-accent:#60a5fa;--sidebar-hover:rgba(37,99,235,.3);--focus-ring:rgba(37,99,235,.18);}
html[data-theme="dark-brown"]{--primary:#a0522d;--primary-dark:#8b3a1c;--secondary:#0d9488;--bg:#fdf5f0;--text:#1c0e08;--text-muted:#6b4228;--border:#e5d0bc;--sidebar-bg:#39221C;--sidebar-text:#d4b896;--sidebar-accent:#c87941;--sidebar-hover:rgba(160,82,45,.3);--focus-ring:rgba(160,82,45,.18);}
.theme-bar{padding:12px 16px 16px;border-top:1px solid rgba(255,255,255,.1);margin-top:auto;display:flex;align-items:center;justify-content:space-between;}
.theme-label{font-size:11px;font-weight:600;color:var(--sidebar-accent);letter-spacing:.06em;text-transform:uppercase;display:flex;align-items:center;gap:6px;}
.theme-dots{display:flex;gap:8px;}
.theme-dot{width:18px;height:18px;border-radius:50%;border:2px solid rgba(255,255,255,.25);cursor:pointer;padding:0;transition:transform .15s,border-color .15s;outline:none;}
.theme-dot:hover{transform:scale(1.25);}
.theme-dot.active{border-color:#fff;transform:scale(1.1);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:"Manrope","Pretendard","Apple SD Gothic Neo",sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;}
a{color:inherit;text-decoration:none;}
#sidebar{width:var(--sidebar-w);background:var(--sidebar-bg);color:var(--sidebar-text);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;transition:transform .25s;overflow-y:auto;}
#sidebar .logo{padding:18px 20px;font-size:15px;font-weight:700;color:#fff;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;gap:10px;}
#sidebar .logo i{color:var(--sidebar-accent);font-size:18px;}
#sidebar .nav-section{padding:10px 12px 4px;font-size:10px;font-weight:600;color:var(--sidebar-accent);letter-spacing:.08em;text-transform:uppercase;}
#sidebar .nav-item{display:flex;align-items:center;gap:10px;padding:9px 16px;border-radius:6px;margin:1px 8px;font-size:13px;transition:background .15s,color .15s;cursor:pointer;}
#sidebar .nav-item:hover{background:var(--sidebar-hover);color:#fff;}
#sidebar .nav-item.active{background:var(--primary);color:#fff;font-weight:500;}
#sidebar .nav-item i{width:16px;font-size:14px;text-align:center;opacity:.75;}
#sidebar .nav-item.active i{opacity:1;}
#sidebar .nav-group{margin:1px 0;}
#sidebar .nav-group-header{display:flex;align-items:center;gap:10px;padding:9px 16px;margin:1px 8px;border-radius:6px;font-size:13px;cursor:pointer;color:var(--sidebar-text);transition:background .15s,color .15s;}
#sidebar .nav-group-header:hover{background:var(--sidebar-hover);color:#fff;}
#sidebar .nav-group-header.open{color:#fff;}
#sidebar .nav-group-header span{flex:1;}
#sidebar .nav-group-header .toggle-icon{font-size:10px;transition:transform .2s;}
#sidebar .nav-group-header.open .toggle-icon{transform:rotate(90deg);}
#sidebar .nav-group-body{padding-left:0;}
#sidebar .nav-item.sub{padding-left:36px;font-size:12px;}
#sidebar .nav-item.sub2{padding-left:52px;font-size:11px;}
#header{position:fixed;top:0;left:var(--sidebar-w);right:0;height:var(--header-h);background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 24px;z-index:90;box-shadow:var(--shadow);}
#header .page-title{font-size:15px;font-weight:600;}
#header .header-right{display:flex;align-items:center;gap:16px;}
.user-badge{display:flex;align-items:center;gap:8px;cursor:pointer;padding:6px 10px;border-radius:6px;transition:background .15s;}
.user-badge:hover{background:var(--bg);}
.user-avatar{width:30px;height:30px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:600;}
#main{margin-left:var(--sidebar-w);margin-top:var(--header-h);flex:1;padding:28px;min-width:0;}
.card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}
.card-header h2{font-size:15px;font-weight:600;}
.card-body{padding:20px;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:6px;font-size:13px;font-weight:500;border:none;cursor:pointer;transition:background .15s,transform .1s;white-space:nowrap;}
.btn:active{transform:scale(.98);}
.btn-primary{background:var(--primary);color:#fff;}.btn-primary:hover{background:var(--primary-dark);}
.btn-success{background:var(--success);color:#fff;}.btn-success:hover{background:#059669;}
.btn-warning{background:var(--warning);color:#fff;}.btn-warning:hover{background:#d97706;}
.btn-danger{background:var(--danger);color:#fff;}.btn-danger:hover{background:#dc2626;}
.btn-secondary{background:var(--border);color:var(--text);}.btn-secondary:hover{background:#d1d5db;}
.btn-ghost{background:transparent;color:var(--text-muted);border:1px solid var(--border);}.btn-ghost:hover{background:var(--bg);}
.btn-sm{padding:4px 10px;font-size:12px;}
.btn-icon{padding:6px;width:32px;height:32px;justify-content:center;}
.table-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;font-size:13px;}
thead th{padding:10px 14px;background:#f9fafb;border-bottom:1px solid var(--border);text-align:left;font-weight:600;white-space:nowrap;}
tbody tr{border-bottom:1px solid #f3f4f6;transition:background .1s;}
tbody tr:hover{background:#fafafa;}
td{padding:10px 14px;vertical-align:middle;}
.badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:500;}
.badge-green{background:#d1fae5;color:#065f46;}.badge-red{background:#fee2e2;color:#991b1b;}
.badge-yellow{background:#fef3c7;color:#92400e;}.badge-blue{background:#dbeafe;color:#1e40af;}
.badge-purple{background:#ede9fe;color:#5b21b6;}.badge-gray{background:#f3f4f6;color:#374151;}
.form-group{margin-bottom:16px;}
.form-label{display:block;font-size:13px;font-weight:500;margin-bottom:6px;}
.form-label .req{color:var(--danger);margin-left:2px;}
.req{color:var(--danger);margin-left:2px;}
.form-control{width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;transition:border .15s,box-shadow .15s;background:var(--surface);}
.form-control:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--focus-ring);}
textarea.form-control{resize:vertical;min-height:100px;}
select.form-control{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M2 4l4 4 4-4' stroke='%236b7280' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:28px;}
.form-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:200;display:flex;align-items:center;justify-content:center;padding:20px;}
.modal-overlay.hidden{display:none;}
.modal{background:var(--surface);border-radius:var(--radius);box-shadow:0 20px 40px rgba(0,0,0,.15);width:100%;max-height:90vh;overflow-y:auto;}
.modal-sm{max-width:420px;}.modal-md{max-width:600px;}.modal-lg{max-width:800px;}.modal-xl{max-width:1000px;}
.modal-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-header h3{font-size:15px;font-weight:600;}
.modal-body{padding:20px;}
.modal-footer{padding:14px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;}
.alert{padding:12px 16px;border-radius:var(--radius);font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:10px;}
.alert-success{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;}
.alert-danger{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;}
#toast-container{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;}
.toast{padding:12px 18px;border-radius:var(--radius);font-size:13px;color:#fff;box-shadow:var(--shadow-md);animation:slideIn .25s ease;display:flex;align-items:center;gap:8px;}
.toast-success{background:#10b981;}.toast-error{background:#ef4444;}.toast-warning{background:#f59e0b;}
@keyframes slideIn{from{transform:translateX(100%);opacity:0;}to{transform:translateX(0);opacity:1;}}
.pagination{display:flex;align-items:center;gap:4px;flex-wrap:wrap;}
.pagination a,.pagination span{padding:5px 10px;border-radius:5px;font-size:13px;border:1px solid var(--border);}
.pagination a{color:var(--text);transition:background .15s;}.pagination a:hover{background:var(--bg);}
.pagination .active{background:var(--primary);color:#fff;border-color:var(--primary);}
.sortable-ghost{opacity:.4;background:#c8e6e8!important;}
.drag-handle{cursor:grab;color:var(--text-muted);}
.text-muted{color:var(--text-muted);}.text-sm{font-size:12px;}.fw-500{font-weight:500;}
.flex{display:flex;}.flex-center{align-items:center;}.gap-8{gap:8px;}.gap-12{gap:12px;}
.mt-16{margin-top:16px;}.mb-16{margin-bottom:16px;}
.truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:220px;}
.img-thumb{width:48px;height:48px;object-fit:cover;border-radius:4px;border:1px solid var(--border);}
.img-thumb-lg{width:80px;height:54px;object-fit:cover;border-radius:4px;}
@media(max-width:768px){
  #sidebar{transform:translateX(-100%);}
  #sidebar.open{transform:none;}
  #header{left:0;}
  #main{margin-left:0;}
}
</style>
</head>
<body>
<nav id="sidebar">
  <div class="logo"><i class="fas fa-church"></i><?= APP_NAME ?></div>

  <!-- ── 대시보드 ── -->
  <a href="<?= BASE_URL ?>/dashboard" class="nav-item <?= ($currentPage??'')==='dashboard'?'active':'' ?>">
    <i class="fas fa-chart-pie"></i>대시보드
  </a>

  <!-- ── 메인 ── -->
  <?php $mainActive=in_array($currentPage??'',['heroes','section-titles','sermons','bulletins','worship','traffic','banner']); ?>
  <div class="nav-group">
    <div class="nav-group-header <?= $mainActive?'open':'' ?>" onclick="toggleGroup(this)">
      <i class="fas fa-home"></i><span>메인</span><i class="fas fa-chevron-right toggle-icon"></i>
    </div>
    <div class="nav-group-body" style="<?= $mainActive?'':'display:none' ?>">
      <?php if(hasPerm('heroes.view')): ?>
      <a href="<?= BASE_URL ?>/heroes" class="nav-item sub <?= ($currentPage??'')==='heroes'?'active':'' ?>"><i class="fas fa-images"></i>히어로 슬라이드</a>
      <?php endif; ?>
      <?php if(hasPerm('introduction.view')): ?>
      <a href="<?= BASE_URL ?>/section-titles" class="nav-item sub <?= ($currentPage??'')==='section-titles'?'active':'' ?>"><i class="fas fa-heading"></i>섹션타이틀</a>
      <?php endif; ?>
      <?php if(hasPerm('sermons.view')): ?>
      <a href="<?= BASE_URL ?>/sermons" class="nav-item sub <?= ($currentPage??'')==='sermons'?'active':'' ?>"><i class="fas fa-video"></i>설교</a>
      <?php endif; ?>     
      <?php if(hasPerm('worship.view')): ?>
      <a href="<?= BASE_URL ?>/worship" class="nav-item sub <?= ($currentPage??'')==='worship'?'active':'' ?>"><i class="fas fa-clock"></i>예배</a>
      <?php endif; ?>
      <?php if(hasPerm('traffic.view')): ?>
      <a href="<?= BASE_URL ?>/traffic" class="nav-item sub <?= ($currentPage??'')==='traffic'?'active':'' ?>"><i class="fas fa-bus"></i>교통</a>
      <?php endif; ?>
      <?php if(hasPerm('onlinegiving.view')): ?>
      <a href="<?= BASE_URL ?>/banner" class="nav-item sub <?= ($currentPage??'')==='banner'?'active':'' ?>"><i class="fas fa-image"></i>배너</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- ── Introduction ── -->
  <?php $introActive=in_array($currentPage??'',['intro-vision','intro-pastor','intro-together','members']); ?>
  <div class="nav-group">
    <div class="nav-group-header <?= $introActive?'open':'' ?>" onclick="toggleGroup(this)">
      <i class="fas fa-church"></i><span>Introduction</span><i class="fas fa-chevron-right toggle-icon"></i>
    </div>
    <div class="nav-group-body" style="<?= $introActive?'':'display:none' ?>">
      <?php if(hasPerm('introduction.view')): ?>
      <a href="<?= BASE_URL ?>/introduction/vision" class="nav-item sub <?= ($currentPage??'')==='intro-vision'?'active':'' ?>"><i class="fas fa-star"></i>교회비전</a>
      <a href="<?= BASE_URL ?>/introduction/pastor" class="nav-item sub <?= ($currentPage??'')==='intro-pastor'?'active':'' ?>"><i class="fas fa-user-tie"></i>담임목사</a>
      <?php endif; ?>
      <?php if(hasPerm('members.view')): ?>
      <a href="<?= BASE_URL ?>/members" class="nav-item sub <?= ($currentPage??'')==='members'?'active':'' ?>"><i class="fas fa-users"></i>섬기는 분들</a>
      <?php endif; ?>
      <?php if(hasPerm('introduction.view')): ?>
      <a href="<?= BASE_URL ?>/introduction/together" class="nav-item sub <?= ($currentPage??'')==='intro-together'?'active':'' ?>"><i class="fas fa-handshake"></i>함께하는 교회</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- ── 다음세대 ── -->
  <?php $nextgenActive=($currentPage??'')==='departments-nextgen'; ?>
  <div class="nav-group">
    <div class="nav-group-header <?= $nextgenActive?'open':'' ?>" onclick="toggleGroup(this)">
      <i class="fas fa-child"></i><span>다음세대</span><i class="fas fa-chevron-right toggle-icon"></i>
    </div>
    <div class="nav-group-body" style="<?= $nextgenActive?'':'display:none' ?>">
      <?php if(hasPerm('departments.view')): ?>
      <a href="<?= BASE_URL ?>/departments?type=nextgen" class="nav-item sub <?= $nextgenActive?'active':'' ?>"><i class="fas fa-list"></i>전체 관리</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- ── 사역 ── -->
  <?php $ministryActive=in_array($currentPage??'',['ministry']); ?>
  <div class="nav-group">
    <div class="nav-group-header <?= $ministryActive?'open':'' ?>" onclick="toggleGroup(this)">
      <i class="fas fa-hands-helping"></i><span>사역</span><i class="fas fa-chevron-right toggle-icon"></i>
    </div>
    <div class="nav-group-body" style="<?= $ministryActive?'':'display:none' ?>">
      <?php if(hasPerm('ministry.view')): ?>
      <a href="<?= BASE_URL ?>/ministry" class="nav-item sub <?= ($currentPage??'')==='ministry'?'active':'' ?>"><i class="fas fa-list"></i>사역 관리</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- ── 소식 ── -->
  <?php $newsActive=in_array($currentPage??'',['bulletins-news','notice','obituary']); ?>
  <div class="nav-group">
    <div class="nav-group-header <?= $newsActive?'open':'' ?>" onclick="toggleGroup(this)">
      <i class="fas fa-newspaper"></i><span>소식</span><i class="fas fa-chevron-right toggle-icon"></i>
    </div>
    <div class="nav-group-body" style="<?= $newsActive?'':'display:none' ?>">
      <?php if(hasPerm('bulletins.view')): ?>
      <a href="<?= BASE_URL ?>/bulletins" class="nav-item sub <?= ($currentPage??'')==='bulletins-news'?'active':'' ?>"><i class="fas fa-newspaper"></i>온라인 주보</a>
      <?php endif; ?>
      <?php if(hasPerm('notice.view')): ?>
      <a href="<?= BASE_URL ?>/notice" class="nav-item sub <?= ($currentPage??'')==='notice'?'active':'' ?>"><i class="fas fa-bullhorn"></i>공지</a>
      <?php endif; ?>
      <?php if(hasPerm('obituary.view')): ?>
      <a href="<?= BASE_URL ?>/obituary" class="nav-item sub <?= ($currentPage??'')==='obituary'?'active':'' ?>"><i class="fas fa-dove"></i>부고</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- ── 시스템 ── -->
  <?php if(hasPerm('users.view')): ?>
  <a href="<?= BASE_URL ?>/users" class="nav-item <?= ($currentPage??'')==='users'?'active':'' ?>">
    <i class="fas fa-user-cog"></i>사용자 관리
  </a>
  <a href="<?= BASE_URL ?>/users/roles" class="nav-item <?= ($currentPage??'')==='roles'?'active':'' ?>">
    <i class="fas fa-shield-alt"></i>역할·권한
  </a>
  <?php endif; ?>

  <!-- ── 테마 ── -->
  <div class="theme-bar">
    <span class="theme-label"><i class="fas fa-palette"></i> 테마</span>
    <div class="theme-dots">
      <button class="theme-dot" data-t="dark-green" title="그린" style="background:#3a472b"></button>
      <button class="theme-dot" data-t="dark-blue" title="블루" style="background:#1a2c4e"></button>
      <button class="theme-dot" data-t="dark-brown" title="브라운" style="background:#39221C"></button>
    </div>
  </div>

</nav>

<!-- Header -->
<header id="header">
  <div style="display:flex;align-items:center;gap:12px;">
    <span class="page-title"><?= htmlspecialchars($pageTitle??'') ?></span>
  </div>
  <div class="header-right">
    <div class="user-badge" onclick="openModal('profile-modal')">
      <div class="user-avatar"><?= mb_substr($session['name']??'A',0,1) ?></div>
      <div>
        <div style="font-size:13px;font-weight:500;"><?= htmlspecialchars($session['name']??'') ?></div>
        <div style="font-size:11px;color:var(--text-muted);"><?= htmlspecialchars($session['role_name']??'') ?></div>
      </div>
    </div>
    <button class="btn btn-ghost btn-sm" onclick="doLogout()"><i class="fas fa-sign-out-alt"></i>로그아웃</button>
  </div>
</header>

<!-- Profile Modal -->
<div class="modal-overlay hidden" id="profile-modal">
  <div class="modal modal-sm">
    <div class="modal-header"><h3>프로필 수정</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('profile-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <form id="profile-form">
        <div class="form-group"><label class="form-label">이름</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($session['name']??'') ?>"></div>
        <div class="form-group"><label class="form-label">새 비밀번호 <span style="font-size:11px;color:var(--text-muted)">(변경 시만 입력)</span></label><input type="password" name="password" class="form-control" placeholder="최소 8자"></div>
        <div class="form-group"><label class="form-label">비밀번호 확인</label><input type="password" name="password_confirm" class="form-control"></div>
      </form>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('profile-modal')">취소</button><button class="btn btn-primary" onclick="saveProfile()">저장</button></div>
  </div>
</div>

<div id="toast-container"></div>
<main id="main">