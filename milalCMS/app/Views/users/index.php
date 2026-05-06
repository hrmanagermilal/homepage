<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('users.edit'); $canCreate=AuthMiddleware::hasPermission('users.create'); $canDelete=AuthMiddleware::hasPermission('users.delete'); $isSuperAdmin=AuthMiddleware::isSuperAdmin(); ?>

<div style="display:flex;gap:12px;margin-bottom:16px">
  <a href="<?= BASE_URL ?>/users" class="btn btn-primary btn-sm"><i class="fas fa-user-cog"></i>사용자 목록</a>
  <a href="<?= BASE_URL ?>/users/roles" class="btn btn-ghost btn-sm"><i class="fas fa-shield-alt"></i>역할·권한 관리</a>
</div>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-user-cog" style="color:var(--primary)"></i> 사용자 관리</h2>
    <?php if($canCreate): ?><button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i>사용자 추가</button><?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead><tr><th>이름</th><th>아이디</th><th>이메일</th><th>역할</th><th>최종 로그인</th><th>상태</th><th>가입일</th><th style="width:100px">관리</th></tr></thead>
      <tbody>
      <?php foreach($data['rows'] as $r): ?>
      <tr data-id="<?= $r['id'] ?>">
        <td class="fw-500"><?= htmlspecialchars($r['name']) ?></td>
        <td class="text-sm"><?= htmlspecialchars($r['username']) ?></td>
        <td class="text-sm"><?= htmlspecialchars($r['email']) ?></td>
        <td><span class="badge badge-purple"><?= htmlspecialchars($r['role_name']) ?></span></td>
        <td class="text-sm text-muted"><?= $r['last_login'] ? date('Y-m-d H:i',strtotime($r['last_login'])) : '-' ?></td>
        <td><span class="badge <?= $r['is_active']?'badge-green':'badge-red' ?>"><?= $r['is_active']?'활성':'비활성' ?></span></td>
        <td class="text-sm text-muted"><?= date('Y-m-d',strtotime($r['created_at'])) ?></td>
        <td><div class="flex gap-8">
          <?php if($canEdit): ?><button class="btn btn-warning btn-sm btn-icon" onclick="openEdit(<?= $r['id'] ?>)"><i class="fas fa-pen"></i></button><?php endif; ?>
          <?php if($canDelete&&$isSuperAdmin&&$r['id']!=AuthMiddleware::getUserId()): ?><button class="btn btn-danger btn-sm btn-icon" onclick="deleteRow(<?= $r['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
        </div></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
</div>

<div class="modal-overlay hidden" id="user-modal">
  <div class="modal modal-md">
    <div class="modal-header"><h3 id="user-modal-title">사용자 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('user-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="user-id">
      <div class="form-group"><label class="form-label">이름 <span class="req">*</span></label><input type="text" id="u-name" class="form-control"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">아이디 <span class="req">*</span></label><input type="text" id="u-username" class="form-control"></div>
        <div class="form-group"><label class="form-label">이메일 <span class="req">*</span></label><input type="email" id="u-email" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">비밀번호 <span class="req">*</span><span class="text-muted text-sm">(수정 시 변경할 때만 입력)</span></label><input type="password" id="u-pw" class="form-control" placeholder="최소 8자"></div>
        <div class="form-group"><label class="form-label">역할 <span class="req">*</span></label><select id="u-role" class="form-control"><?php foreach($roles as $role): ?><option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option><?php endforeach; ?></select></div>
      </div>
      <div class="form-group"><label class="form-label">상태</label><select id="u-active" class="form-control"><option value="1">활성</option><option value="0">비활성</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('user-modal')">취소</button><button class="btn btn-primary" id="user-save-btn" onclick="saveUser()">저장</button></div>
  </div>
</div>
<script>
function openCreate(){document.getElementById('user-modal-title').textContent='사용자 추가';document.getElementById('user-id').value='';['u-name','u-fname','u-username','u-email','u-pw'].forEach(id=>document.getElementById(id).value='');document.getElementById('u-active').value=1;openModal('user-modal');}
async function openEdit(id){const d=await api('/users/detail',{id});if(!d.success){toast(d.message,'error');return;}const r=d.data;document.getElementById('user-modal-title').textContent='사용자 수정';document.getElementById('user-id').value=r.id;document.getElementById('u-name').value=r.name;document.getElementById('u-username').value=r.username;document.getElementById('u-email').value=r.email;document.getElementById('u-pw').value='';document.getElementById('u-role').value=r.role_id;document.getElementById('u-active').value=r.is_active;openModal('user-modal');}
async function saveUser(){const id=document.getElementById('user-id').value;const fd=new FormData();if(id)fd.append('id',id);fd.append('name',document.getElementById('u-name').value);fd.append('username',document.getElementById('u-username').value);fd.append('email',document.getElementById('u-email').value);fd.append('password',document.getElementById('u-pw').value);fd.append('role_id',document.getElementById('u-role').value);fd.append('is_active',document.getElementById('u-active').value);const btn=document.getElementById('user-save-btn');btn.disabled=true;const d=await fetch(BASE_URL+(id?'/users/update':'/users/create'),{method:'POST',body:fd}).then(r=>r.json());btn.disabled=false;if(d.success){toast(d.message);closeModal('user-modal');location.reload();}else toast(d.message,'error');}
async function deleteRow(id){confirmAction('이 사용자를 삭제하시겠습니까?',async()=>{const d=await api('/users/delete',{id});if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}else toast(d.message,'error');});}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
