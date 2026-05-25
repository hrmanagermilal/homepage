<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-heading" style="color:var(--primary)"></i> 섹션 타이틀 관리</h2>
    <?php if(hasPerm('introduction.create')): ?>
    <button class="btn btn-primary btn-sm" onclick="openModal('section-modal')"><i class="fas fa-plus"></i> 새 섹션 추가</button>
    <?php endif; ?>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>카테고리</th><th>제목</th><th>부제목</th><th style="width:100px">관리</th></tr></thead>
      <tbody id="section-tbody">
        <?php foreach($sectionTitles as $s): ?>
        <tr data-id="<?= $s['id'] ?>">
          <td><span class="badge badge-blue"><?= htmlspecialchars($s['category']) ?></span></td>
          <td class="fw-500"><?= htmlspecialchars($s['title']) ?></td>
          <td class="text-muted truncate"><?= htmlspecialchars($s['subtitle']??'') ?></td>
          <td><div class="flex gap-8">
            <?php if(hasPerm('introduction.edit')): ?>
            <button class="btn btn-warning btn-sm btn-icon" onclick="editSection(<?= $s['id'] ?>)"><i class="fas fa-pen"></i></button>
            <?php endif; ?>
            <?php if(hasPerm('introduction.delete')): ?>
            <button class="btn btn-danger btn-sm btn-icon" onclick="deleteSection(<?= $s['id'] ?>)"><i class="fas fa-trash"></i></button>
            <?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($sectionTitles)): ?><tr><td colspan="4" style="text-align:center;padding:30px;color:var(--text-muted)">등록된 섹션 타이틀이 없습니다.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 섹션 모달 -->
<div id="section-modal" class="modal-overlay hidden">
  <div class="modal modal-md">
    <div class="modal-header"><h3 id="section-modal-title">섹션 타이틀 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('section-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="sm-id">
      <div class="form-group"><label class="form-label">카테고리 <span class="req">*</span></label><input class="form-control" id="sm-category" placeholder="Sermon, Jubo, Worship, News, Directions, Community"></div>
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input class="form-control" id="sm-title" placeholder="최신 설교"></div>
      <div class="form-group"><label class="form-label">부제목</label><textarea class="form-control" id="sm-subtitle" rows="3" placeholder="섹션 부제목을 입력하세요."></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('section-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveSection()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<script>
function openSectionModal() { document.getElementById('sm-id').value=''; document.getElementById('sm-category').value=''; document.getElementById('sm-title').value=''; document.getElementById('sm-subtitle').value=''; document.getElementById('section-modal-title').textContent='섹션 타이틀 추가'; openModal('section-modal'); }
async function editSection(id) {
  const d = await api('/section-titles/detail', {id});
  if(!d.success){toast(d.message,'error');return;}
  document.getElementById('sm-id').value=d.data.id;
  document.getElementById('sm-category').value=d.data.category||'';
  document.getElementById('sm-title').value=d.data.title||'';
  document.getElementById('sm-subtitle').value=d.data.subtitle||'';
  document.getElementById('section-modal-title').textContent='섹션 타이틀 수정';
  openModal('section-modal');
}
async function saveSection() {
  const id=document.getElementById('sm-id').value;
  const d=await api('/section-titles/'+(id?'update':'create'),{id,category:document.getElementById('sm-category').value,title:document.getElementById('sm-title').value,subtitle:document.getElementById('sm-subtitle').value});
  if(d.success){toast(d.message);closeModal('section-modal');location.reload();}else toast(d.message,'error');
}
async function deleteSection(id) {
  confirmAction('이 섹션 타이틀을 삭제하시겠습니까?', async()=>{
    const d=await api('/section-titles/delete',{id});
    if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
