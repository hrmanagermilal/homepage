<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-clock" style="color:var(--primary)"></i> 예배 시간표</h2>
    <?php if(hasPerm('worship.create')): ?>
    <button class="btn btn-primary btn-sm" onclick="openServiceModal()"><i class="fas fa-plus"></i> 추가</button>
    <?php endif; ?>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>카테고리</th><th>예배명</th><th>요일</th><th>시간</th><th style="width:60px">순서</th><th style="width:60px">상태</th><th style="width:90px">관리</th></tr></thead>
      <tbody id="service-tbody">
        <?php foreach($serviceTimes as $s): ?>
        <tr data-id="<?= $s['id'] ?>">
          <td><span class="badge badge-blue"><?= htmlspecialchars($s['category']) ?></span></td>
          <td class="fw-500"><?= htmlspecialchars($s['name']) ?></td>
          <td class="text-muted"><?= htmlspecialchars($s['day']??'') ?></td>
          <td><?= htmlspecialchars($s['time']) ?></td>
          <td class="text-muted"><?= $s['sort_order'] ?></td>
          <td><span class="badge <?= $s['is_active']?'badge-green':'badge-gray' ?>"><?= $s['is_active']?'활성':'비활성' ?></span></td>
          <td><div class="flex gap-8">
            <?php if(hasPerm('worship.edit')): ?><button class="btn btn-warning btn-sm btn-icon" onclick="editService(<?= $s['id'] ?>)"><i class="fas fa-pen"></i></button><?php endif; ?>
            <?php if(hasPerm('worship.delete')): ?><button class="btn btn-danger btn-sm btn-icon" onclick="deleteService(<?= $s['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($serviceTimes)): ?><tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted)">등록된 예배 시간이 없습니다.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 예배 시간 모달 -->
<div id="service-modal" class="modal-overlay hidden">
  <div class="modal modal-md">
    <div class="modal-header"><h3 id="svc-modal-title">예배 시간 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('service-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="svc-id">
      <div class="form-group"><label class="form-label">카테고리 <span class="req">*</span></label>
        <select class="form-control" id="svc-category"><option value="주일예배">주일예배</option><option value="주중예배">주중예배</option><option value="교육부예배">교육부예배</option></select></div>
      <div class="form-group"><label class="form-label">예배명 <span class="req">*</span></label><input class="form-control" id="svc-name" placeholder="1부"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">요일</label><input class="form-control" id="svc-day" placeholder="주일, 수요일 등"></div>
        <div class="form-group"><label class="form-label">시간 <span class="req">*</span></label><input class="form-control" id="svc-time" placeholder="오전 8:00"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">순서</label><input class="form-control" type="number" id="svc-order" value="0" min="0"></div>
        <div class="form-group"><label class="form-label">상태</label><select class="form-control" id="svc-active"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('service-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveService()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<script>
function openServiceModal(data={}) {
  document.getElementById('svc-id').value=data.id||'';
  document.getElementById('svc-category').value=data.category||'주일예배';
  document.getElementById('svc-name').value=data.name||'';
  document.getElementById('svc-day').value=data.day||'';
  document.getElementById('svc-time').value=data.time||'';
  document.getElementById('svc-order').value=data.sort_order||0;
  document.getElementById('svc-active').value=data.is_active??1;
  document.getElementById('svc-modal-title').textContent=data.id?'예배 시간 수정':'예배 시간 추가';
  openModal('service-modal');
}
async function editService(id) { const d=await api('/worship/service-detail',{id}); if(d.success)openServiceModal(d.data); }
async function saveService() {
  const id=document.getElementById('svc-id').value;
  const d=await api('/worship/'+(id?'service-update':'service-create'),{
    id,category:document.getElementById('svc-category').value,
    name:document.getElementById('svc-name').value,
    day:document.getElementById('svc-day').value,
    time:document.getElementById('svc-time').value,
    sort_order:document.getElementById('svc-order').value,
    is_active:document.getElementById('svc-active').value,
  });
  if(!d.success)return toast(d.message,'error');
  toast(d.message);closeModal('service-modal');
  if(id){const tr=document.querySelector(`tr[data-id="${id}"]`);if(tr){tr.querySelectorAll('td')[1].textContent=document.getElementById('svc-name').value;tr.querySelectorAll('td')[2].textContent=document.getElementById('svc-day').value;tr.querySelectorAll('td')[3].textContent=document.getElementById('svc-time').value;}}else location.reload();
}
async function deleteService(id) {
  confirmAction('이 예배 시간을 삭제하시겠습니까?',async()=>{
    const d=await api('/worship/service-delete',{id});
    if(!d.success)return toast(d.message,'error');
    toast(d.message);document.querySelector(`tr[data-id="${id}"]`)?.remove();
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
