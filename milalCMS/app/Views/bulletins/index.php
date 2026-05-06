<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('bulletins.edit'); $canCreate=AuthMiddleware::hasPermission('bulletins.create'); $canDelete=AuthMiddleware::hasPermission('bulletins.delete'); ?>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-book-open" style="color:var(--primary)"></i> 주보 관리</h2>
    <?php if($canCreate): ?><button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i>주보 추가</button><?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead><tr><th>제목</th><th>연도</th><th>주차</th><th>이미지수</th><th>등록일</th><th style="width:130px">관리</th></tr></thead>
      <tbody>
      <?php foreach($data['rows'] as $r): ?>
      <tr data-id="<?= $r['id'] ?>">
        <td>
          <a href="<?= BASE_URL ?>/bulletins/view?id=<?= $r['id'] ?>" style="color:var(--text);font-weight:500">
            <?= htmlspecialchars($r['title']) ?>
          </a>
        </td>
        <td><?= $r['year']??'-' ?></td>
        <td><?= $r['week_number'] ? $r['week_number'].'주차' : '-' ?></td>
        <td><span class="badge badge-blue"><?= $r['image_count'] ?>장</span></td>
        <td class="text-sm text-muted"><?= date('Y-m-d',strtotime($r['created_at'])) ?></td>
        <td><div class="flex gap-8">
          <a href="<?= BASE_URL ?>/bulletins/view?id=<?= $r['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-eye"></i></a>
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
      <?php if($pagination['has_prev']): ?><a href="?page=<?= $pagination['current']-1 ?>">&laquo;</a><?php endif; ?>
      <?php for($p=$pagination['start_page'];$p<=$pagination['end_page'];$p++): ?><<?= $p===$pagination['current']?'span class="active"':'a href="?page='.$p.'"' ?>><?= $p ?></<?= $p===$pagination['current']?'span':'a' ?>><?php endfor; ?>
      <?php if($pagination['has_next']): ?><a href="?page=<?= $pagination['current']+1 ?>">&raquo;</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Create Modal -->
<div class="modal-overlay hidden" id="bulletin-modal">
  <div class="modal modal-sm">
    <div class="modal-header"><h3 id="bul-modal-title">주보 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('bulletin-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="bul-id">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="bul-title" class="form-control" placeholder="예: 2025년 28주차 주보"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">연도</label><input type="number" id="bul-year" class="form-control" value="<?= date('Y') ?>"></div>
        <div class="form-group"><label class="form-label">주차</label><input type="number" id="bul-week" class="form-control" placeholder="예: 28"></div>
      </div>
      <div class="form-group" id="bul-img-group">
        <label class="form-label">이미지 일괄 업로드 <span class="text-muted text-sm">(여러 장 선택 가능)</span></label>
        <input type="file" id="bul-imgs" class="form-control" accept="image/*" multiple>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('bulletin-modal')">취소</button><button class="btn btn-primary" id="bul-save-btn" onclick="saveBulletin()">저장</button></div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay hidden" id="bul-edit-modal">
  <div class="modal modal-sm">
    <div class="modal-header"><h3>주보 수정</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('bul-edit-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="bul-edit-id">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="bul-edit-title" class="form-control"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">연도</label><input type="number" id="bul-edit-year" class="form-control"></div>
        <div class="form-group"><label class="form-label">주차</label><input type="number" id="bul-edit-week" class="form-control"></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('bul-edit-modal')">취소</button><button class="btn btn-primary" id="bul-edit-btn" onclick="saveEdit()">저장</button></div>
  </div>
</div>

<script>
function openCreate(){
  document.getElementById('bul-modal-title').textContent='주보 추가';
  document.getElementById('bul-id').value='';
  document.getElementById('bul-title').value='';
  document.getElementById('bul-year').value='<?= date('Y') ?>';
  document.getElementById('bul-week').value='';
  document.getElementById('bul-img-group').style.display='block';
  openModal('bulletin-modal');
}
async function openEdit(id){
  const d=await api('/bulletins/detail',{id});
  if(!d.success){toast(d.message,'error');return;}
  const r=d.data;
  document.getElementById('bul-edit-id').value=r.id;
  document.getElementById('bul-edit-title').value=r.title;
  document.getElementById('bul-edit-year').value=r.year||'<?= date('Y') ?>';
  document.getElementById('bul-edit-week').value=r.week_number||'';
  openModal('bul-edit-modal');
}
async function saveBulletin(){
  const title=document.getElementById('bul-title').value.trim();
  if(!title){toast('제목을 입력하세요.','error');return;}
  const fd=new FormData();
  fd.append('title',title);
  fd.append('year',document.getElementById('bul-year').value);
  fd.append('week_number',document.getElementById('bul-week').value);
  const files=document.getElementById('bul-imgs').files;
  for(const f of files) fd.append('images[]',f);
  const btn=document.getElementById('bul-save-btn');btn.disabled=true;btn.textContent='저장 중...';
  const d=await fetch(BASE_URL+'/bulletins/create',{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;btn.textContent='저장';
  if(d.success){toast(d.message);closeModal('bulletin-modal');location.reload();}else toast(d.message,'error');
}
async function saveEdit(){
  const fd=new FormData();
  fd.append('id',document.getElementById('bul-edit-id').value);
  fd.append('title',document.getElementById('bul-edit-title').value);
  fd.append('year',document.getElementById('bul-edit-year').value);
  fd.append('week_number',document.getElementById('bul-edit-week').value);
  const btn=document.getElementById('bul-edit-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+'/bulletins/update',{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('bul-edit-modal');location.reload();}else toast(d.message,'error');
}
async function deleteRow(id){
  confirmAction('주보와 모든 이미지를 삭제하시겠습니까?',async()=>{
    const d=await api('/bulletins/delete',{id});
    if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
