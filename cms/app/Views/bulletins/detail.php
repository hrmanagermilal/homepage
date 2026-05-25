<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=hasPerm('bulletins.edit'); $canDelete=hasPerm('bulletins.delete'); ?>

<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/bulletins" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
</div>

<div class="card">
  <div class="card-header">
    <div style="flex:1">
      <h1 style="font-size:19px;font-weight:600"><?= htmlspecialchars($bulletin['title']) ?></h1>
      <div style="font-size:13px;color:var(--text-muted);margin-top:6px;display:flex;gap:14px;flex-wrap:wrap">
        <?php if($bulletin['year']): ?><span><i class="fas fa-calendar"></i> <?= $bulletin['year'] ?>년</span><?php endif; ?>
        <?php if($bulletin['week_number']): ?><span><i class="fas fa-list-ol"></i> <?= $bulletin['week_number'] ?>주차</span><?php endif; ?>
        <?php if($bulletin['attachment']): ?>
        <a href="<?= BASE_URL.htmlspecialchars($bulletin['attachment']) ?>" target="_blank" style="color:#dc2626">
          <i class="fas fa-file-pdf"></i> PDF 보기
        </a>
        <?php endif; ?>
        <span><i class="fas fa-images"></i> 이미지 <?= count($images) ?>장</span>
      </div>
    </div>
    <div class="flex gap-8" style="flex-shrink:0">
      <?php if($canEdit): ?>
      <button class="btn btn-primary btn-sm" onclick="openAddImgModal()"><i class="fas fa-upload"></i> 이미지 추가</button>
      <button class="btn btn-warning btn-sm" onclick="openEdit()"><i class="fas fa-pen"></i> 수정</button>
      <?php endif; ?>
      <?php if($canDelete): ?>
      <button class="btn btn-danger btn-sm" onclick="deleteBulletin(<?= $bulletin['id'] ?>)"><i class="fas fa-trash"></i> 삭제</button>
      <?php endif; ?>
    </div>
  </div>
  <div class="card-body">
    <?php if(empty($images)): ?>
    <div style="text-align:center;padding:40px;color:var(--text-muted)"><i class="fas fa-image" style="font-size:32px;margin-bottom:8px;display:block"></i>이미지 없음</div>
    <?php else: ?>
    <div id="img-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px">
      <?php foreach($images as $img): ?>
      <div data-id="<?= $img['id'] ?>" style="position:relative;border:1px solid var(--border);border-radius:8px;overflow:hidden;background:var(--bg)">
        <img src="<?= BASE_URL.htmlspecialchars($img['image_url']) ?>" style="width:100%;aspect-ratio:3/4;object-fit:cover;display:block" alt="">
        <?php if($canEdit): ?>
        <div style="position:absolute;top:6px;right:6px;display:flex;gap:4px">
          <span class="drag-handle" style="background:rgba(0,0,0,.5);color:#fff;border-radius:4px;padding:4px 6px;cursor:grab;font-size:12px"><i class="fas fa-grip-vertical"></i></span>
          <button onclick="deleteImg(<?= $img['id'] ?>,this.closest('[data-id]'))" style="background:var(--danger);color:#fff;border:none;border-radius:4px;padding:4px 6px;cursor:pointer;font-size:12px"><i class="fas fa-trash"></i></button>
        </div>
        <?php endif; ?>
        <div style="padding:6px 8px;font-size:11px;color:var(--text-muted);text-align:center">순서 <?= $img['order'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- 이미지 추가 모달 (미리보기 후 저장) -->
<div class="modal-overlay hidden" id="add-img-modal">
  <div class="modal" style="max-width:560px">
    <div class="modal-header">
      <h3>이미지 추가</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('add-img-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">이미지 선택 <span class="text-muted text-sm">(여러 장 가능)</span></label>
        <input type="file" id="add-imgs" class="form-control" accept="image/*" multiple onchange="previewNewImgs(this)">
      </div>
      <div id="new-img-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px;margin-top:8px"></div>
      <p id="new-img-count" style="font-size:12px;color:var(--text-muted);margin-top:6px"></p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('add-img-modal')">취소</button>
      <button class="btn btn-primary" id="add-img-btn" onclick="uploadImgs()"><i class="fas fa-upload"></i> 저장</button>
    </div>
  </div>
</div>

<!-- 수정 모달 -->
<div class="modal-overlay hidden" id="edit-modal">
  <div class="modal modal-sm">
    <div class="modal-header"><h3>주보 수정</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('edit-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label>
        <input type="text" id="bul-title" class="form-control" value="<?= htmlspecialchars($bulletin['title']) ?>"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">연도</label>
          <input type="number" id="bul-year" class="form-control" value="<?= $bulletin['year']??date('Y') ?>"></div>
        <div class="form-group"><label class="form-label">주차</label>
          <input type="number" id="bul-week" class="form-control" value="<?= $bulletin['week_number']??'' ?>"></div>
      </div>
      <!-- PDF -->
      <div class="form-group">
        <label class="form-label"><i class="fas fa-file-pdf" style="color:#dc2626"></i> PDF</label>
        <?php if($bulletin['attachment']): ?>
        <div id="det-current-pdf" style="padding:8px 12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;margin-bottom:8px;font-size:12px">
          <i class="fas fa-file-pdf" style="color:#dc2626"></i>
          <?= htmlspecialchars(basename($bulletin['attachment'])) ?>
          <a href="<?= BASE_URL.htmlspecialchars($bulletin['attachment']) ?>" target="_blank" style="margin-left:8px;font-size:11px">열기</a>
          <button type="button" onclick="this.closest('#det-current-pdf').style.display='none';document.getElementById('det-remove-pdf').value='1'" style="float:right;background:none;border:none;cursor:pointer;color:var(--danger)"><i class="fas fa-times"></i></button>
        </div>
        <?php endif; ?>
        <div id="det-new-pdf-preview" style="display:none;padding:8px 12px;background:#fffbeb;border:1px solid #fcd34d;border-radius:6px;margin-bottom:8px;font-size:12px">
          <i class="fas fa-arrow-right" style="color:#d97706"></i> 교체될 PDF: <span id="det-new-pdf-name"></span>
        </div>
        <input type="file" id="det-pdf" class="form-control" accept=".pdf,application/pdf" onchange="previewDetPdf(this)">
      </div>
      <input type="hidden" id="det-remove-pdf" value="0">
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('edit-modal')">취소</button>
      <button class="btn btn-primary" id="edit-save-btn" onclick="saveEdit()">저장</button>
    </div>
  </div>
</div>

<script>
const BULLETIN_ID = <?= $bulletin['id'] ?>;
const UPLOAD_URL_BASE = '<?= BASE_URL ?>';

function openEdit(){ openModal('edit-modal'); }
function openAddImgModal(){
  document.getElementById('add-imgs').value='';
  document.getElementById('new-img-grid').innerHTML='';
  document.getElementById('new-img-count').textContent='';
  openModal('add-img-modal');
}

/* ── 이미지 미리보기 ── */
function previewNewImgs(input) {
  const grid = document.getElementById('new-img-grid');
  const count = document.getElementById('new-img-count');
  grid.innerHTML = '';
  [...input.files].forEach((f,i) => {
    const url = URL.createObjectURL(f);
    grid.innerHTML += `<div style="border-radius:6px;overflow:hidden;aspect-ratio:3/4;background:#f3f4f6;position:relative">
      <img src="${url}" style="width:100%;height:100%;object-fit:cover">
      <span style="position:absolute;bottom:2px;right:2px;font-size:9px;background:rgba(0,0,0,.5);color:#fff;padding:1px 4px;border-radius:3px">${i+1}</span>
    </div>`;
  });
  count.textContent = input.files.length ? `${input.files.length}장 선택됨 — 저장을 눌러야 업로드됩니다.` : '';
}

/* ── 이미지 저장 ── */
async function uploadImgs() {
  const files = document.getElementById('add-imgs').files;
  if(!files.length){toast('이미지를 선택하세요.','error');return;}
  const fd = new FormData();
  fd.append('bulletin_id', BULLETIN_ID);
  for(const f of files) fd.append('images[]', f);
  const btn = document.getElementById('add-img-btn'); btn.disabled=true;
  showSpinner(`이미지 ${files.length}장 업로드 중...`);
  const d = await fetch(BASE_URL+'/bulletins/images-add',{method:'POST',body:fd}).then(r=>r.json());
  hideSpinner(); btn.disabled=false;
  if(d.success){toast(d.message);closeModal('add-img-modal');location.reload();}
  else toast(d.message,'error');
}

/* ── 이미지 삭제 ── */
async function deleteImg(id, el) {
  if(!confirm('이 이미지를 삭제하시겠습니까?')) return;
  const d = await api('/bulletins/image-delete',{id});
  if(d.success){el.remove();toast('삭제되었습니다.');}
  else toast(d.message,'error');
}

/* ── PDF (상세 수정 모달) ── */
function previewDetPdf(input) {
  if(!input.files[0]) return;
  document.getElementById('det-new-pdf-name').textContent = input.files[0].name;
  document.getElementById('det-new-pdf-preview').style.display = '';
}

/* ── 주보 수정 ── */
async function saveEdit() {
  const fd = new FormData();
  fd.append('id',          BULLETIN_ID);
  fd.append('title',       document.getElementById('bul-title').value);
  fd.append('year',        document.getElementById('bul-year').value);
  fd.append('week_number', document.getElementById('bul-week').value);
  fd.append('remove_attachment', document.getElementById('det-remove-pdf').value);
  const pdfFile = document.getElementById('det-pdf').files[0];
  if(pdfFile) fd.append('attachment', pdfFile);
  const btn = document.getElementById('edit-save-btn'); btn.disabled=true;
  showSpinner('수정 중...');
  const d = await fetch(BASE_URL+'/bulletins/update',{method:'POST',body:fd}).then(r=>r.json());
  hideSpinner(); btn.disabled=false;
  if(d.success){toast(d.message);closeModal('edit-modal');location.reload();}
  else toast(d.message,'error');
}

/* ── 삭제 ── */
async function deleteBulletin(id) {
  confirmAction('주보와 모든 파일을 삭제하시겠습니까?', async()=>{
    const d = await api('/bulletins/delete',{id});
    if(d.success){toast('삭제되었습니다.');location.href=BASE_URL+'/bulletins';}
    else toast(d.message,'error');
  });
}

/* ── Sortable ── */
function pageInit() {
  const grid = document.getElementById('img-grid');
  if(grid && typeof Sortable!=='undefined'){
    new Sortable(grid,{handle:'.drag-handle',animation:150,onEnd:async()=>{
      const orders=[...grid.querySelectorAll('[data-id]')].map((el,i)=>({id:parseInt(el.dataset.id),order:i+1}));
      await api('/bulletins/image-reorder',{orders:JSON.stringify(orders)});
      toast('순서가 저장되었습니다.');
    }});
  }
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
