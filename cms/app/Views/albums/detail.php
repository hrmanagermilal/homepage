<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit = hasPerm('album.edit'); $canDelete = hasPerm('album.delete'); ?>

<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/albums" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
</div>

<!-- ── 앨범 정보 카드 ─────────────────────────────────────────── -->
<div class="card" style="margin-bottom:16px">
  <div class="card-header">
    <div style="flex:1">
      <h1 style="font-size:19px;font-weight:600"><?= htmlspecialchars($album['title']) ?></h1>
      <div style="font-size:13px;color:var(--text-muted);margin-top:6px;display:flex;gap:14px;flex-wrap:wrap">
        <span><i class="fas fa-calendar"></i> <?= htmlspecialchars($album['date'] ?? date('Y-m-d', strtotime($album['created_at']))) ?></span>
        <span><i class="fas fa-images"></i> 사진 <?= count($images) ?>장</span>
        <span class="badge <?= $album['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $album['is_active'] ? '활성' : '비활성' ?></span>
      </div>
    </div>
    <div class="flex gap-8" style="flex-shrink:0">
      <?php if($canEdit): ?>
      <button class="btn btn-primary btn-sm" onclick="openAddImagesModal()"><i class="fas fa-upload"></i> 사진 추가</button>
      <button class="btn btn-warning btn-sm" onclick="openEdit()"><i class="fas fa-pen"></i> 수정</button>
      <?php endif; ?>
      <?php if($canDelete): ?>
      <button class="btn btn-danger btn-sm" onclick="deleteAlbum(<?= $album['id'] ?>)"><i class="fas fa-trash"></i> 삭제</button>
      <?php endif; ?>
    </div>
  </div>
  <?php if(!empty($album['content'])): ?>
  <div class="card-body" style="padding:16px 20px">
    <div style="white-space:pre-wrap;line-height:1.75;color:var(--text);font-size:14px"><?= htmlspecialchars($album['content']) ?></div>
  </div>
  <?php endif; ?>
</div>

<!-- ── 사진 목록 카드 ─────────────────────────────────────────── -->
<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-image" style="color:var(--primary)"></i> 사진 목록</h2>
    <?php if($canEdit): ?>
    <span style="font-size:12px;color:var(--text-muted)"><i class="fas fa-info-circle"></i> 드래그로 순서 변경</span>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <?php if(empty($images)): ?>
    <div style="text-align:center;padding:40px;color:var(--text-muted)">
      <i class="fas fa-image" style="font-size:32px;margin-bottom:8px;display:block"></i>
      등록된 사진이 없습니다.
    </div>
    <?php else: ?>
    <div id="img-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px">
      <?php foreach($images as $img): ?>
      <div data-id="<?= $img['id'] ?>" style="position:relative;border:1px solid var(--border);border-radius:8px;overflow:hidden;background:var(--bg)">
        <img src="<?= rtrim(BASE_URL,'/').'/'.ltrim(htmlspecialchars($img['image_url']),'/') ?>"
             style="width:100%;aspect-ratio:4/3;object-fit:cover;display:block" alt="<?= htmlspecialchars($img['alt_text'] ?? '') ?>">
        <?php if($canEdit): ?>
        <div style="position:absolute;top:6px;right:6px;display:flex;gap:4px">
          <span class="drag-handle" title="드래그하여 순서 변경"
                style="background:rgba(0,0,0,.5);color:#fff;border-radius:4px;padding:4px 6px;cursor:grab;font-size:12px">
            <i class="fas fa-grip-vertical"></i>
          </span>
          <button onclick="deleteImg(<?= $img['id'] ?>, this.closest('[data-id]'))"
                  style="background:var(--danger);color:#fff;border:none;border-radius:4px;padding:4px 6px;cursor:pointer;font-size:12px"
                  title="삭제">
            <i class="fas fa-trash"></i>
          </button>
        </div>
        <?php endif; ?>
        <?php if(!empty($img['alt_text'])): ?>
        <div style="padding:6px 10px;font-size:12px;color:var(--text-muted);background:var(--bg)">
          <?= htmlspecialchars($img['alt_text']) ?>
        </div>
        <?php endif; ?>
        <div style="padding:2px 10px 6px;font-size:11px;color:var(--text-muted)">순서 <?= $img['sort_order'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ══ 사진 추가 모달 ══════════════════════════════════════════ -->
<div class="modal-overlay hidden" id="add-img-modal">
  <div class="modal modal-lg" style="max-width:680px">
    <div class="modal-header">
      <h3>사진 추가</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('add-img-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">사진 선택 <span class="text-muted text-sm">(여러 장 가능)</span></label>
        <input type="file" id="add-imgs" class="form-control" accept="image/*" multiple onchange="appendExtraImages(this)">
      </div>
      <div id="album-extra-image-list" style="display:grid;gap:10px"></div>
      <p id="new-img-count" style="font-size:12px;color:var(--text-muted);margin-top:6px"></p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('add-img-modal')">취소</button>
      <button class="btn btn-primary" id="add-img-btn" onclick="uploadImgs()"><i class="fas fa-upload"></i> 저장</button>
    </div>
  </div>
</div>

<!-- ══ 수정 모달 ═══════════════════════════════════════════════ -->
<div class="modal-overlay hidden" id="edit-modal">
  <div class="modal" style="max-width:520px">
    <div class="modal-header">
      <h3>앨범 수정</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('edit-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">제목 <span class="req">*</span></label>
        <input type="text" id="edit-title" class="form-control" value="<?= htmlspecialchars($album['title']) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">내용 <span class="req">*</span></label>
        <textarea id="edit-content" class="form-control" rows="5"><?= htmlspecialchars($album['content'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">날짜</label>
        <input type="date" id="edit-date" class="form-control"
               value="<?= htmlspecialchars(substr($album['date'] ?? $album['created_at'] ?? '', 0, 10)) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">상태</label>
        <select id="edit-active" class="form-control">
          <option value="1" <?= ($album['is_active'] ?? 1) ? 'selected' : '' ?>>활성</option>
          <option value="0" <?= !($album['is_active'] ?? 1) ? 'selected' : '' ?>>비활성</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('edit-modal')">취소</button>
      <button class="btn btn-primary" id="edit-save-btn" onclick="saveEdit()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<script>
const ALBUM_ID = <?= (int)$album['id'] ?>;
let albumExtraFiles = [];

/* ── 수정 모달 ── */
function openEdit(){ openModal('edit-modal'); }

async function saveEdit(){
  const title   = document.getElementById('edit-title').value.trim();
  const content = document.getElementById('edit-content').value.trim();
  if(!title)  { toast('제목을 입력하세요.','error'); return; }
  if(!content){ toast('내용을 입력하세요.','error'); return; }

  const fd = new FormData();
  fd.append('id',        ALBUM_ID);
  fd.append('title',     title);
  fd.append('content',   content);
  fd.append('date',      document.getElementById('edit-date').value);
  fd.append('is_active', document.getElementById('edit-active').value);

  const btn = document.getElementById('edit-save-btn'); btn.disabled = true;
  showSpinner('수정 중...');
  const d = await fetch(BASE_URL+'/albums/update', {method:'POST', body:fd}).then(r=>r.json());
  hideSpinner(); btn.disabled = false;

  if(d.success){ toast(d.message); closeModal('edit-modal'); location.reload(); }
  else toast(d.message,'error');
}

/* ── 사진 추가 ── */
function openAddImagesModal(){
  albumExtraFiles = [];
  document.getElementById('add-imgs').value = '';
  document.getElementById('album-extra-image-list').innerHTML = '';
  document.getElementById('new-img-count').textContent = '';
  openModal('add-img-modal');
}

function appendExtraImages(input){
  const picked = [...input.files];
  for(const f of picked){
    const exists = albumExtraFiles.some(x => x.name===f.name && x.size===f.size && x.lastModified===f.lastModified);
    if(!exists) albumExtraFiles.push(f);
  }
  input.value = '';
  renderExtraImageInputs();
}

function removeExtraImage(index){
  albumExtraFiles.splice(index, 1);
  renderExtraImageInputs();
}

function renderExtraImageInputs(){
  const box = document.getElementById('album-extra-image-list');
  box.innerHTML = '';
  albumExtraFiles.forEach((f, i) => {
    const preview = URL.createObjectURL(f);
    const defaultTitle = f.name.replace(/\.[^.]+$/, '');
    box.insertAdjacentHTML('beforeend', `
      <div style="display:grid;grid-template-columns:90px 1fr;gap:10px;align-items:center;padding:8px;border:1px solid var(--border);border-radius:8px">
        <img src="${preview}" alt="" style="width:90px;height:90px;object-fit:cover;border-radius:6px">
        <div>
          <div class="text-sm text-muted" style="margin-bottom:6px;display:flex;justify-content:space-between;align-items:center;gap:8px">
            <span>${f.name}</span>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeExtraImage(${i})"><i class="fas fa-trash"></i></button>
          </div>
          <input type="text" class="form-control album-extra-image-title" data-index="${i}" value="${defaultTitle}" placeholder="사진 제목">
        </div>
      </div>
    `);
  });
  document.getElementById('new-img-count').textContent =
    albumExtraFiles.length ? `${albumExtraFiles.length}장 선택됨 — 저장을 눌러야 업로드됩니다.` : '';
}

async function uploadImgs(){
  const files = albumExtraFiles;
  if(!files.length){ toast('사진을 선택하세요.','error'); return; }

  const fd = new FormData();
  fd.append('album_id', ALBUM_ID);
  const titleInputs = [...document.querySelectorAll('.album-extra-image-title')];
  for(let i = 0; i < files.length; i++){
    fd.append('images[]', files[i]);
    fd.append('image_titles[]', titleInputs.find(el => Number(el.dataset.index) === i)?.value?.trim() || '');
  }

  const btn = document.getElementById('add-img-btn'); btn.disabled = true;
  showSpinner(`사진 ${files.length}장 업로드 중...`);
  const d = await fetch(BASE_URL+'/albums/images-add', {method:'POST', body:fd}).then(r=>r.json());
  hideSpinner(); btn.disabled = false;

  if(d.success){ toast(d.message); closeModal('add-img-modal'); location.reload(); }
  else toast(d.message,'error');
}

/* ── 사진 삭제 ── */
async function deleteImg(id, el){
  if(!confirm('이 사진을 삭제하시겠습니까?')) return;
  const d = await api('/albums/image-delete', {id});
  if(d.success){ el.remove(); toast('삭제되었습니다.'); }
  else toast(d.message,'error');
}

/* ── 앨범 삭제 ── */
async function deleteAlbum(id){
  confirmAction('앨범과 모든 사진을 삭제하시겠습니까?', async()=>{
    const d = await api('/albums/delete', {id});
    if(d.success){ toast('삭제되었습니다.'); location.href = BASE_URL+'/albums'; }
    else toast(d.message,'error');
  });
}

/* ── Sortable (드래그 순서 변경) ── */
function pageInit(){
  const grid = document.getElementById('img-grid');
  if(grid && typeof Sortable !== 'undefined'){
    new Sortable(grid, {
      handle: '.drag-handle',
      animation: 150,
      onEnd: async () => {
        const orders = [...grid.querySelectorAll('[data-id]')].map((el, i) => ({
          id: parseInt(el.dataset.id),
          order: i + 1,
        }));
        const d = await api('/albums/image-reorder', {orders: JSON.stringify(orders)});
        if(d.success) toast('순서가 저장되었습니다.');
        else toast(d.message,'error');
      },
    });
  }
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
