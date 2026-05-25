<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=hasPerm('users.edit'); $canCreate=hasPerm('users.create'); $canDelete=hasPerm('users.delete'); $isSuperAdmin=AuthMiddleware::isSuperAdmin(); ?>

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
    <div class="modal-header">
      <h3 id="user-modal-title">사용자 추가</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('user-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="user-id">
      <div class="form-group"><label class="form-label">이름 <span class="req">*</span></label>
        <input type="text" id="u-name" class="form-control"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">아이디 <span class="req">*</span></label>
          <input type="text" id="u-username" class="form-control" placeholder="영문/숫자">
          <small id="u-username-note" class="text-muted" style="font-size:11px;display:none">수정 시 변경 불가</small>
        </div>
        <div class="form-group"><label class="form-label">이메일 <span class="req">*</span></label>
          <input type="email" id="u-email" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">비밀번호 <span id="u-pw-req" class="req">*</span>
            <span id="u-pw-note" class="text-muted text-sm" style="display:none">(변경 시만 입력)</span>
          </label>
          <input type="password" id="u-pw" class="form-control" placeholder="최소 8자">
        </div>
        <div class="form-group"><label class="form-label">역할 <span class="req">*</span></label>
          <select id="u-role" class="form-control">
            <?php foreach($roles as $role): ?>
            <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group"><label class="form-label">상태</label>
        <select id="u-active" class="form-control">
          <option value="1">활성</option>
          <option value="0">비활성</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('user-modal')">취소</button>
      <button class="btn btn-primary" id="user-save-btn" onclick="saveUser()">저장</button>
    </div>
  </div>
</div>

<script>
let _isEdit = false;

function openCreate() {
  _isEdit = false;
  document.getElementById('user-modal-title').textContent = '사용자 추가';
  document.getElementById('user-id').value = '';
  ['u-name','u-username','u-email','u-pw'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('u-active').value = 1;
  // 추가 시: 아이디/이메일 편집 가능, 비밀번호 필수
  document.getElementById('u-username').readOnly = false;
  document.getElementById('u-email').readOnly = false;
  document.getElementById('u-username-note').style.display = 'none';
  document.getElementById('u-pw-req').style.display = '';
  document.getElementById('u-pw-note').style.display = 'none';
  openModal('user-modal');
}

async function openEdit(id) {
  const d = await api('/users/detail', {id});
  if (!d.success) { toast(d.message, 'error'); return; }
  _isEdit = true;
  const r = d.data;
  document.getElementById('user-modal-title').textContent = '사용자 수정';
  document.getElementById('user-id').value   = r.id;
  document.getElementById('u-name').value    = r.name;
  document.getElementById('u-username').value= r.username;
  document.getElementById('u-email').value   = r.email;
  document.getElementById('u-pw').value      = '';
  document.getElementById('u-role').value    = r.role_id;
  document.getElementById('u-active').value  = r.is_active;
  // 수정 시: 아이디 읽기전용, 비밀번호 선택사항
  document.getElementById('u-username').readOnly = true;
  document.getElementById('u-email').readOnly = false;
  document.getElementById('u-username-note').style.display = '';
  document.getElementById('u-pw-req').style.display = 'none';
  document.getElementById('u-pw-note').style.display = '';
  openModal('user-modal');
}

async function saveUser() {
  const id   = document.getElementById('user-id').value;
  const name = document.getElementById('u-name').value.trim();
  const pw   = document.getElementById('u-pw').value;

  if (!name) return toast('이름을 입력하세요.', 'error');
  if (!_isEdit && !pw) return toast('비밀번호를 입력하세요.', 'error');
  if (pw && pw.length < 8) return toast('비밀번호는 최소 8자 이상이어야 합니다.', 'error');

  const fd = new FormData();
  if (id) fd.append('id', id);
  fd.append('name',      name);
  fd.append('email',     document.getElementById('u-email').value.trim());
  fd.append('role_id',   document.getElementById('u-role').value);
  fd.append('is_active', document.getElementById('u-active').value);
  if (!_isEdit) fd.append('username', document.getElementById('u-username').value.trim());
  if (pw)       fd.append('password', pw);

  const btn = document.getElementById('user-save-btn');
  btn.disabled = true;
  const res = await fetch(BASE_URL + (id ? '/users/update' : '/users/create'), {method:'POST', body:fd}).then(r=>r.json());
  btn.disabled = false;

  if (res.success) {
    toast(res.message);
    closeModal('user-modal');
    // 즉시 DOM 갱신
    if (id) {
      const tr = document.querySelector(`tr[data-id="${id}"]`);
      if (tr) {
        tr.querySelector('td:nth-child(1)').textContent = name;
        tr.querySelector('td:nth-child(3)').textContent = document.getElementById('u-email').value.trim();
        const badge = tr.querySelector('td:nth-child(6) .badge');
        const activeVal = document.getElementById('u-active').value;
        if (badge) { badge.className = `badge ${activeVal=='1'?'badge-green':'badge-red'}`; badge.textContent = activeVal=='1'?'활성':'비활성'; }
      }
    } else {
      location.reload();
    }
  } else {
    toast(res.message, 'error');
  }
}

async function deleteRow(id) {
  confirmAction('이 사용자를 삭제하시겠습니까?', async () => {
    const d = await api('/users/delete', {id});
    if (d.success) { toast('삭제되었습니다.'); document.querySelector(`tr[data-id="${id}"]`)?.remove(); }
    else toast(d.message, 'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
