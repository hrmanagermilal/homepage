<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('departments.edit'); $canCreate=AuthMiddleware::hasPermission('departments.create'); $canDelete=AuthMiddleware::hasPermission('departments.delete'); ?>

<div style="margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap">
  <a href="<?= BASE_URL ?>/departments" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 부서 목록</a>
  <a href="<?= BASE_URL ?>/departments/view?id=<?= $dept['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-info-circle"></i> 부서 상세</a>
</div>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-bullhorn" style="color:var(--primary)"></i>
      <span style="color:var(--text-muted);font-weight:400"><?= htmlspecialchars($dept['name']) ?></span> 공지사항
    </h2>
    <?php if($canCreate): ?><button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i>공지 추가</button><?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead><tr><th>제목</th><th>링크</th><th>등록일</th><th style="width:90px">관리</th></tr></thead>
      <tbody>
      <?php if(empty($data['rows'])): ?>
      <tr><td colspan="4" style="text-align:center;padding:30px;color:var(--text-muted)">공지사항이 없습니다.</td></tr>
      <?php endif; ?>
      <?php foreach($data['rows'] as $r): ?>
      <tr data-id="<?= $r['id'] ?>">
        <td class="fw-500"><?= htmlspecialchars($r['title']) ?></td>
        <td><?php if($r['link']): ?><a href="<?= htmlspecialchars($r['link']) ?>" target="_blank" class="text-sm" style="color:var(--info)"><?= htmlspecialchars(mb_substr($r['link'],0,50)) ?>...</a><?php else: ?><span class="text-muted">-</span><?php endif; ?></td>
        <td class="text-sm text-muted"><?= date('Y-m-d',strtotime($r['created_at'])) ?></td>
        <td><div class="flex gap-8">
          <?php if($canEdit): ?><button class="btn btn-warning btn-sm btn-icon" onclick="openEdit(<?= $r['id'] ?>)"><i class="fas fa-pen"></i></button><?php endif; ?>
          <?php if($canDelete): ?><button class="btn btn-danger btn-sm btn-icon" onclick="deleteRow(<?= $r['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
        </div></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
  <?php if($pagination['total_pages']>1): ?>
  <div class="card-body" style="border-top:1px solid var(--border)">
    <div class="pagination">
      <?php if($pagination['has_prev']): ?><a href="?dept_id=<?= $dept['id'] ?>&page=<?= $pagination['current']-1 ?>">&laquo;</a><?php endif; ?>
      <?php for($p=$pagination['start_page'];$p<=$pagination['end_page'];$p++): ?>
        <<?= $p===$pagination['current']?'span class="active"':'a href="?dept_id='.$dept['id'].'&page='.$p.'"' ?>><?= $p ?></<?= $p===$pagination['current']?'span':'a' ?>>
      <?php endfor; ?>
      <?php if($pagination['has_next']): ?><a href="?dept_id=<?= $dept['id'] ?>&page=<?= $pagination['current']+1 ?>">&raquo;</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Modal -->
<div class="modal-overlay hidden" id="da-modal">
  <div class="modal modal-md">
    <div class="modal-header"><h3 id="da-modal-title">공지 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('da-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="da-id">
      <input type="hidden" id="da-dept-id" value="<?= $dept['id'] ?>">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="da-title" class="form-control"></div>
      <div class="form-group"><label class="form-label">내용 <span class="req">*</span></label><textarea id="da-content" class="form-control" rows="6"></textarea></div>
      <div class="form-group"><label class="form-label">링크</label><input type="url" id="da-link" class="form-control" placeholder="https://"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('da-modal')">취소</button><button class="btn btn-primary" id="da-save-btn" onclick="saveDa()">저장</button></div>
  </div>
</div>

<script>
function openCreate(){
  document.getElementById('da-modal-title').textContent='공지 추가';
  document.getElementById('da-id').value='';
  ['da-title','da-content','da-link'].forEach(id=>document.getElementById(id).value='');
  openModal('da-modal');
}
async function openEdit(id){
  const d=await api('/departments/announcement-detail',{id});
  if(!d.success){toast(d.message,'error');return;}
  const r=d.data;
  document.getElementById('da-modal-title').textContent='공지 수정';
  document.getElementById('da-id').value=r.id;
  document.getElementById('da-title').value=r.title;
  document.getElementById('da-content').value=r.content;
  document.getElementById('da-link').value=r.link||'';
  openModal('da-modal');
}
async function saveDa(){
  const id=document.getElementById('da-id').value;
  const fd=new FormData();
  if(id)fd.append('id',id);
  fd.append('dept_id',document.getElementById('da-dept-id').value);
  fd.append('title',document.getElementById('da-title').value);
  fd.append('content',document.getElementById('da-content').value);
  fd.append('link',document.getElementById('da-link').value);
  const btn=document.getElementById('da-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+(id?'/departments/announcement-update':'/departments/announcement-create'),{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('da-modal');location.reload();}else toast(d.message,'error');
}
async function deleteRow(id){
  confirmAction('삭제하시겠습니까?',async()=>{
    const d=await api('/departments/announcement-delete',{id});
    if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
