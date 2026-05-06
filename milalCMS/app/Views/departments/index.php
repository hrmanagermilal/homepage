<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('departments.edit'); $canCreate=AuthMiddleware::hasPermission('departments.create'); $canDelete=AuthMiddleware::hasPermission('departments.delete'); ?>

<div class="card">
  <div class="card-header">
    <div class="flex flex-center gap-12">
      <h2><i class="fas fa-sitemap" style="color:var(--primary)"></i> 부서 관리</h2>
      <div class="flex gap-8">
        <a href="?type=" class="btn btn-sm <?= $type===''?'btn-primary':'btn-ghost' ?>">전체</a>
        <a href="?type=nextgen" class="btn btn-sm <?= $type==='nextgen'?'btn-primary':'btn-ghost' ?>">다음세대</a>
        <a href="?type=ministry" class="btn btn-sm <?= $type==='ministry'?'btn-primary':'btn-ghost' ?>">사역부서</a>
      </div>
    </div>
    <?php if($canCreate): ?><button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i>부서 추가</button><?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead><tr><th>이미지</th><th>부서명</th><th>유형</th><th>예배</th><th>담당자</th><th>상태</th><th style="width:170px">관리</th></tr></thead>
      <tbody>
      <?php foreach($departments as $r): ?>
      <tr data-id="<?= $r['id'] ?>">
        <td><?php if($r['image']): ?><img src="<?= UPLOAD_URL.htmlspecialchars($r['image']) ?>" class="img-thumb" alt=""><?php else: ?><div style="width:40px;height:40px;background:var(--bg);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--text-muted)"><i class="fas fa-sitemap"></i></div><?php endif; ?></td>
        <td>
          <a href="<?= BASE_URL ?>/departments/view?id=<?= $r['id'] ?>" style="color:var(--text);font-weight:500">
            <?= htmlspecialchars($r['name']) ?>
          </a>
          <?php if($r['age_group']): ?><div class="text-sm text-muted"><?= htmlspecialchars($r['age_group']) ?></div><?php endif; ?>
        </td>
        <td><span class="badge <?= $r['department_type']==='nextgen'?'badge-blue':'badge-purple' ?>"><?= $r['department_type']==='nextgen'?'다음세대':'사역' ?></span></td>
        <td class="text-sm"><?= htmlspecialchars(($r['worship_day']??'').($r['worship_time']?' '.$r['worship_time']:'')) ?></td>
        <td class="text-sm"><?= htmlspecialchars($r['clergy_name']??'-') ?><?php if($r['clergy_position']): ?> <span class="text-muted">(<?= htmlspecialchars($r['clergy_position']) ?>)</span><?php endif; ?></td>
        <td><span class="badge <?= $r['is_active']?'badge-green':'badge-gray' ?>"><?= $r['is_active']?'활성':'비활성' ?></span></td>
        <td><div class="flex gap-8">
          <a href="<?= BASE_URL ?>/departments/view?id=<?= $r['id'] ?>" class="btn btn-ghost btn-sm btn-icon" title="상세"><i class="fas fa-eye"></i></a>
          <a href="<?= BASE_URL ?>/departments/announcements?dept_id=<?= $r['id'] ?>" class="btn btn-ghost btn-sm btn-icon" title="공지"><i class="fas fa-bullhorn"></i></a>
          <?php if($canEdit): ?><button class="btn btn-warning btn-sm btn-icon" onclick="openEdit(<?= $r['id'] ?>)"><i class="fas fa-pen"></i></button><?php endif; ?>
          <?php if($canDelete): ?><button class="btn btn-danger btn-sm btn-icon" onclick="deleteRow(<?= $r['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
        </div></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
</div>

<!-- Modal -->
<div class="modal-overlay hidden" id="dept-modal">
  <div class="modal modal-xl">
    <div class="modal-header"><h3 id="dept-modal-title">부서 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('dept-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="dept-id">
      <div class="form-row">
        <div class="form-group"><label class="form-label">부서명 <span class="req">*</span></label><input type="text" id="d-name" class="form-control"></div>
        <div class="form-group"><label class="form-label">유형</label><select id="d-type" class="form-control"><option value="nextgen">다음세대</option><option value="ministry">사역부서</option></select></div>
        <div class="form-group"><label class="form-label">연령대</label><input type="text" id="d-age" class="form-control" placeholder="예: 0-4세"></div>
      </div>
      <div class="form-group"><label class="form-label">설명</label><textarea id="d-desc" class="form-control" rows="3"></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">예배 요일</label><input type="text" id="d-wday" class="form-control" placeholder="예: 주일"></div>
        <div class="form-group"><label class="form-label">예배 시간</label><input type="text" id="d-wtime" class="form-control" placeholder="예: 오전 11:00"></div>
        <div class="form-group"><label class="form-label">예배 장소</label><input type="text" id="d-wloc" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">담당자명</label><input type="text" id="d-cname" class="form-control"></div>
        <div class="form-group"><label class="form-label">직책</label><input type="text" id="d-cpos" class="form-control"></div>
        <div class="form-group"><label class="form-label">연락처</label><input type="tel" id="d-cphone" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">이미지</label><input type="file" id="d-img" class="form-control" accept="image/*"><div id="d-img-preview" style="margin-top:8px"></div></div>
        <div class="form-group"><label class="form-label">정렬 순서</label><input type="number" id="d-order" class="form-control" value="0"></div>
        <div class="form-group"><label class="form-label">상태</label><select id="d-active" class="form-control"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('dept-modal')">취소</button><button class="btn btn-primary" id="dept-save-btn" onclick="saveDept()">저장</button></div>
  </div>
</div>

<script>
function openCreate(){
  document.getElementById('dept-modal-title').textContent='부서 추가';
  document.getElementById('dept-id').value='';
  ['d-name','d-desc','d-age','d-wday','d-wtime','d-wloc','d-cname','d-cpos','d-cphone'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('d-type').value='ministry';
  document.getElementById('d-order').value=0;
  document.getElementById('d-active').value=1;
  document.getElementById('d-img-preview').innerHTML='';
  openModal('dept-modal');
}
async function openEdit(id){
  const d=await api('/departments/detail',{id});
  if(!d.success){toast(d.message,'error');return;}
  const r=d.data;
  document.getElementById('dept-modal-title').textContent='부서 수정';
  document.getElementById('dept-id').value=r.id;
  document.getElementById('d-name').value=r.name;
  document.getElementById('d-type').value=r.department_type;
  document.getElementById('d-age').value=r.age_group||'';
  document.getElementById('d-desc').value=r.description||'';
  document.getElementById('d-wday').value=r.worship_day||'';
  document.getElementById('d-wtime').value=r.worship_time||'';
  document.getElementById('d-wloc').value=r.worship_location||'';
  document.getElementById('d-cname').value=r.clergy_name||'';
  document.getElementById('d-cpos').value=r.clergy_position||'';
  document.getElementById('d-cphone').value=r.clergy_phone||'';
  document.getElementById('d-order').value=r.order||0;
  document.getElementById('d-active').value=r.is_active;
  document.getElementById('d-img-preview').innerHTML=r.image?`<img src="${BASE_URL+'/uploads/'+r.image}" style="max-height:80px;border-radius:4px">`:'';
  openModal('dept-modal');
}
async function saveDept(){
  const id=document.getElementById('dept-id').value;
  const fd=new FormData();
  if(id)fd.append('id',id);
  const fields={department_type:'d-type',name:'d-name',age_group:'d-age',description:'d-desc',worship_day:'d-wday',worship_time:'d-wtime',worship_location:'d-wloc',clergy_name:'d-cname',clergy_position:'d-cpos',clergy_phone:'d-cphone',order:'d-order',is_active:'d-active'};
  for(const[k,eid]of Object.entries(fields))fd.append(k,document.getElementById(eid).value);
  const img=document.getElementById('d-img').files[0];if(img)fd.append('image',img);
  const btn=document.getElementById('dept-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+(id?'/departments/update':'/departments/create'),{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('dept-modal');location.reload();}else toast(d.message,'error');
}
async function deleteRow(id){
  confirmAction('이 부서를 삭제하시겠습니까?',async()=>{
    const d=await api('/departments/delete',{id});
    if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
