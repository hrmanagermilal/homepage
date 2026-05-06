<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('users.edit'); $canCreate=AuthMiddleware::hasPermission('users.create'); $canDelete=AuthMiddleware::hasPermission('users.delete'); ?>

<div style="display:flex;gap:12px;margin-bottom:16px">
  <a href="<?= BASE_URL ?>/users" class="btn btn-ghost btn-sm"><i class="fas fa-user-cog"></i>사용자 목록</a>
  <a href="<?= BASE_URL ?>/users/roles" class="btn btn-primary btn-sm"><i class="fas fa-shield-alt"></i>역할·권한 관리</a>
</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start">
  <!-- 역할 목록 -->
  <div class="card">
    <div class="card-header"><h2>역할 목록</h2><?php if($canCreate): ?><button class="btn btn-primary btn-sm" onclick="openCreateRole()"><i class="fas fa-plus"></i></button><?php endif; ?></div>
    <div class="card-body" style="padding:0">
      <?php foreach($roles as $role): ?>
      <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);cursor:pointer" onclick="loadRole(<?= $role['id'] ?>)" id="role-item-<?= $role['id'] ?>">
        <div><div class="fw-500"><?= htmlspecialchars($role['name']) ?></div><div class="text-sm text-muted"><?= htmlspecialchars($role['slug']) ?></div></div>
        <div class="flex gap-8">
          <?php if($canDelete): ?><button class="btn btn-danger btn-sm btn-icon" onclick="event.stopPropagation();deleteRole(<?= $role['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- 권한 편집 -->
  <div class="card" id="perm-panel">
    <div class="card-header"><h2 id="perm-title">역할을 선택하세요</h2><button class="btn btn-primary btn-sm hidden" id="perm-save-btn" onclick="saveRole()">저장</button></div>
    <div class="card-body" id="perm-body">
      <p class="text-muted">왼쪽에서 역할을 선택하면 권한을 설정할 수 있습니다.</p>
    </div>
  </div>
</div>

<!-- Create Role Modal -->
<div class="modal-overlay hidden" id="role-modal">
  <div class="modal modal-md">
    <div class="modal-header"><h3>역할 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('role-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">역할명 <span class="req">*</span></label><input type="text" id="r-name" class="form-control"></div>
      <div class="form-group"><label class="form-label">슬러그 <span class="req">*</span></label><input type="text" id="r-slug" class="form-control" placeholder="예: editor"></div>
      <div class="form-group"><label class="form-label">설명</label><textarea id="r-desc" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('role-modal')">취소</button><button class="btn btn-primary" onclick="createRole()">생성</button></div>
  </div>
</div>

<script>
const allPerms=<?= json_encode($permissions,JSON_UNESCAPED_UNICODE) ?>;
const moduleLabels={'heroes':'히어로','members':'교인','announcements':'공지사항','news':'뉴스','sermons':'설교','bulletins':'주보','departments':'부서','cms':'페이지 CMS','users':'사용자/권한'};
const actionLabels={'view':'조회','create':'등록','edit':'수정','delete':'삭제'};
let currentRoleId=null;

async function loadRole(id){
  currentRoleId=id;
  document.querySelectorAll('[id^=role-item-]').forEach(el=>el.style.background='');
  document.getElementById('role-item-'+id).style.background='var(--bg)';
  const d=await api('/users/role-detail',{id});
  if(!d.success){toast(d.message,'error');return;}
  const {role,permissions:assignedIds}=d.data;
  document.getElementById('perm-title').textContent=role.name+' — 권한 설정';
  document.getElementById('perm-save-btn').classList.remove('hidden');
  let html='<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">';
  for(const[mod,perms]of Object.entries(allPerms)){
    html+=`<div style="border:1px solid var(--border);border-radius:6px;overflow:hidden"><div style="padding:10px 14px;background:var(--bg);font-weight:500;font-size:13px">${moduleLabels[mod]||mod}</div><div style="padding:10px 14px;display:flex;flex-wrap:wrap;gap:8px">`;
    for(const p of perms){const checked=assignedIds.includes(p.id)?'checked':'';html+=`<label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer"><input type="checkbox" name="permissions" value="${p.id}" ${checked}>${actionLabels[p.action]||p.action}</label>`;}
    html+='</div></div>';
  }
  html+='</div>';
  document.getElementById('perm-body').innerHTML=html;
}

async function saveRole(){
  if(!currentRoleId)return;
  const checked=[...document.querySelectorAll('input[name=permissions]:checked')].map(el=>el.value);
  const d=await api('/users/role-update',{id:currentRoleId,name:document.querySelector(`#role-item-${currentRoleId} .fw-500`).textContent,slug:document.querySelector(`#role-item-${currentRoleId} .text-sm`).textContent,'permissions[]':checked.join(',')});
  // rebuild FormData for array
  const fd=new FormData();fd.append('id',currentRoleId);
  const nameEl=document.querySelector(`#role-item-${currentRoleId} .fw-500`);
  const slugEl=document.querySelector(`#role-item-${currentRoleId} .text-muted`);
  if(nameEl)fd.append('name',nameEl.textContent);
  if(slugEl)fd.append('slug',slugEl.textContent);
  checked.forEach(v=>fd.append('permissions[]',v));
  const r=await fetch(BASE_URL+'/users/role-update',{method:'POST',body:fd}).then(r=>r.json());
  if(r.success)toast('역할 권한이 저장되었습니다.');
  else toast(r.message,'error');
}

function openCreateRole(){['r-name','r-slug','r-desc'].forEach(id=>document.getElementById(id).value='');openModal('role-modal');}
async function createRole(){
  const fd=new FormData();fd.append('name',document.getElementById('r-name').value);fd.append('slug',document.getElementById('r-slug').value);fd.append('description',document.getElementById('r-desc').value);
  const d=await fetch(BASE_URL+'/users/role-create',{method:'POST',body:fd}).then(r=>r.json());
  if(d.success){toast('역할이 생성되었습니다.');closeModal('role-modal');location.reload();}
  else toast(d.message,'error');
}
async function deleteRole(id){confirmAction('이 역할을 삭제하시겠습니까?',async()=>{const d=await api('/users/role-delete',{id});if(d.success){toast('삭제되었습니다.');location.reload();}else toast(d.message,'error');});}

document.getElementById('r-name')?.addEventListener('input',e=>{document.getElementById('r-slug').value=e.target.value.toLowerCase().replace(/\s+/g,'-').replace(/[^a-z0-9-]/g,'');});
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
