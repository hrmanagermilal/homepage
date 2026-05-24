<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php
$canEdit   = AuthMiddleware::hasPermission('heroes.edit');
$canCreate = AuthMiddleware::hasPermission('heroes.create');
$canDelete = AuthMiddleware::hasPermission('heroes.delete');
?>

<style>
.tab-bar{display:flex;gap:0;border-bottom:1px solid var(--border);margin-bottom:20px;}
.tab-btn{padding:8px 20px;font-size:13px;font-weight:500;border:none;background:none;cursor:pointer;color:var(--text-muted);border-bottom:2px solid transparent;margin-bottom:-1px;transition:color .15s;}
.tab-btn.active{color:var(--primary);border-bottom-color:var(--primary);}
.tab-panel{display:none;}.tab-panel.active{display:block;}
.img-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;min-height:60px;}
.img-card{position:relative;border:1px solid var(--border);border-radius:6px;overflow:hidden;}
.img-card img{width:100%;aspect-ratio:16/9;object-fit:cover;display:block;}
.img-card .img-actions{position:absolute;top:4px;right:4px;}
.link-card{display:flex;align-items:center;gap:12px;padding:12px 16px;border:1px solid var(--border);border-radius:8px;margin-bottom:8px;background:var(--surface);}
.link-icon{width:44px;height:44px;border-radius:8px;background:var(--bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;}
</style>

<!-- 탭 -->
<div class="tab-bar">
  <button class="tab-btn active" onclick="switchTab('bg-tab',this)"><i class="fas fa-images"></i> 배경 이미지</button>
  <button class="tab-btn" onclick="switchTab('front-tab',this)"><i class="fas fa-image"></i> 전면 이미지</button>
  <button class="tab-btn" onclick="switchTab('link-tab',this)"><i class="fas fa-link"></i> 퀵 링크</button>
</div>

<!-- ① 배경 이미지 탭 -->
<div id="bg-tab" class="tab-panel active">
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-images" style="color:var(--primary)"></i> 배경 이미지 관리 <span class="text-muted text-sm">(드래그로 순서 변경)</span></h2>
      <?php if($canEdit): ?>
      <label class="btn btn-primary"><i class="fas fa-upload"></i> 이미지 추가
        <input type="file" id="bg-upload" accept="image/*" multiple style="display:none" onchange="uploadBgImages(this)">
      </label>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <div id="bg-list" class="img-grid">
        <?php foreach($bgImages as $img): ?>
        <div class="img-card" data-id="<?= $img['id'] ?>">
          <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="<?= htmlspecialchars($img['alt_text']??'') ?>">
          <?php if($canEdit): ?>
          <div class="img-actions">
            <button onclick="deleteBgImg(<?= $img['id'] ?>,this.closest('.img-card'))" style="background:var(--danger);color:#fff;border:none;border-radius:4px;padding:3px 6px;font-size:11px;cursor:pointer"><i class="fas fa-trash"></i></button>
          </div>
          <?php endif; ?>
          <?php if(!empty($img['alt_text'])): ?>
          <div style="padding:4px 8px;font-size:11px;color:var(--text-muted);background:var(--surface)"><?= htmlspecialchars($img['alt_text']) ?></div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php if(empty($bgImages)): ?>
        <div class="text-muted text-sm" style="padding:30px;text-align:center;grid-column:1/-1;border:2px dashed var(--border);border-radius:6px">배경 이미지가 없습니다.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- ② 전면 이미지 탭 -->
<div id="front-tab" class="tab-panel">
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-image" style="color:var(--primary)"></i> 전면 이미지 관리 <span class="text-muted text-sm">(1개)</span></h2>
      <?php if($canEdit): ?>
      <label class="btn btn-primary"><i class="fas fa-upload"></i> 업로드
        <input type="file" id="front-upload" accept="image/*" style="display:none" onchange="uploadFrontImage(this)">
      </label>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <div id="front-preview">
        <?php if($frontImage): ?>
        <div style="max-width:400px">
          <img src="<?= htmlspecialchars($frontImage['image_url']) ?>" style="width:100%;border-radius:6px;border:1px solid var(--border)">
          <?php if(!empty($frontImage['alt_text'])): ?>
          <div class="text-sm text-muted" style="margin-top:6px;white-space:pre-line"><?= htmlspecialchars($frontImage['alt_text']) ?></div>
          <?php endif; ?>
          <?php if($canEdit): ?>
          <button onclick="deleteFrontImg()" class="btn btn-danger btn-sm" style="margin-top:10px"><i class="fas fa-trash"></i> 전면 이미지 삭제</button>
          <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="text-muted text-sm" style="padding:30px;text-align:center;border:2px dashed var(--border);border-radius:6px;max-width:400px"><i class="fas fa-image" style="font-size:28px;margin-bottom:8px;display:block"></i>전면 이미지가 없습니다.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- ③ 퀵 링크 탭 -->
<div id="link-tab" class="tab-panel">
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-link" style="color:var(--primary)"></i> 퀵 링크 관리</h2>
      <?php if($canCreate): ?><button class="btn btn-primary" onclick="openLinkCreate()"><i class="fas fa-plus"></i> 링크 추가</button><?php endif; ?>
    </div>
    <div class="card-body">
      <?php if(empty($links)): ?>
      <div style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-link" style="font-size:28px;margin-bottom:8px;display:block"></i>등록된 링크가 없습니다.</div>
      <?php else: ?>
      <?php foreach($links as $lk): ?>
      <div class="link-card" data-id="<?= $lk['id'] ?>">
        <div class="link-icon">
          <?php if(!empty($lk['image'])): ?>
          <img src="<?= htmlspecialchars($lk['image']) ?>" alt="" style="width:100%;height:100%;object-fit:cover">
          <?php else: ?>
          <i class="fas fa-link" style="color:var(--text-muted)"></i>
          <?php endif; ?>
        </div>
        <div style="flex:1;min-width:0">
          <div class="fw-500"><?= htmlspecialchars($lk['title']??'') ?></div>
          <div class="text-sm text-muted truncate"><a href="<?= htmlspecialchars($lk['link']??'') ?>" target="_blank" style="color:var(--info)"><?= htmlspecialchars($lk['link']??'') ?></a></div>
          <?php if(!empty($lk['desc'])): ?><div class="text-sm text-muted"><?= htmlspecialchars($lk['desc']) ?></div><?php endif; ?>
        </div>
        <div class="flex gap-8">
          <?php if($canEdit): ?><button class="btn btn-warning btn-sm btn-icon" onclick="openLinkEdit(<?= $lk['id'] ?>)"><i class="fas fa-pen"></i></button><?php endif; ?>
          <?php if($canDelete): ?><button class="btn btn-danger btn-sm btn-icon" onclick="deleteLink(<?= $lk['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Link Modal -->
<div class="modal-overlay hidden" id="link-modal">
  <div class="modal modal-sm">
    <div class="modal-header"><h3 id="link-modal-title">링크 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('link-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="link-id">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="lk-title" class="form-control" placeholder="예: 예배 시간 안내"></div>
      <div class="form-group"><label class="form-label">링크 URL <span class="req">*</span></label><input type="text" id="lk-link" class="form-control" placeholder="https://"></div>
      <div class="form-group"><label class="form-label">설명</label><input type="text" id="lk-desc" class="form-control" placeholder="짧은 설명"></div>
      <div class="form-group">
        <label class="form-label">아이콘 이미지</label>
        <input type="file" id="lk-image" class="form-control" accept="image/*">
        <div id="lk-image-preview" style="margin-top:8px"></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('link-modal')">취소</button><button class="btn btn-primary" id="link-save-btn" onclick="saveLink()">저장</button></div>
  </div>
</div>

<script>
// ── 탭 전환
function switchTab(tabId, btn) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById(tabId).classList.add('active');
  btn.classList.add('active');
}

// ── 배경 이미지
async function uploadBgImages(input) {
  let added = 0;
  for (const file of input.files) {
    const fd = new FormData();
    fd.append('image', file);
    const d = await fetch(BASE_URL+'/heroes/bg-image-add', {method:'POST',body:fd}).then(r=>r.json());
    if (d.success) {
      const el = document.getElementById('bg-list');
      el.querySelector('[style*="dashed"]')?.remove();
      const div = document.createElement('div');
      div.className = 'img-card'; div.dataset.id = d.data.id;
      div.innerHTML = `<img src="${d.data.image_url}" alt="">
        <div class="img-actions">
          <button onclick="deleteBgImg(${d.data.id},this.closest('.img-card'))" style="background:var(--danger);color:#fff;border:none;border-radius:4px;padding:3px 6px;font-size:11px;cursor:pointer"><i class="fas fa-trash"></i></button>
        </div>`;
      el.appendChild(div);
      added++;
    }
  }
  if (added) { toast(`${added}장 업로드 완료`); initSortable(); }
  input.value = '';
}
async function deleteBgImg(id, el) {
  const d = await api('/heroes/bg-image-delete', {id});
  if (d.success) { el.remove(); toast('이미지가 삭제되었습니다.'); }
  else toast(d.message, 'error');
}
function initSortable() {
  const el = document.getElementById('bg-list');
  if (el && typeof Sortable !== 'undefined') {
    if (el._sortable) el._sortable.destroy();
    el._sortable = new Sortable(el, {animation:150, onEnd: () => {
      const orders = [...el.querySelectorAll('[data-id]')].map((r,i) => ({id:parseInt(r.dataset.id),order:i+1}));
      api('/heroes/bg-image-reorder', {orders: JSON.stringify(orders)});
    }});
  }
}

// ── 전면 이미지
async function uploadFrontImage(input) {
  const fd = new FormData();
  fd.append('image', input.files[0]);
  const d = await fetch(BASE_URL+'/heroes/front-image-upsert', {method:'POST',body:fd}).then(r=>r.json());
  if (d.success) {
    document.getElementById('front-preview').innerHTML = `<div style="max-width:400px">
      <img src="${d.data.image_url}" style="width:100%;border-radius:6px;border:1px solid var(--border)">
      <button onclick="deleteFrontImg()" class="btn btn-danger btn-sm" style="margin-top:10px"><i class="fas fa-trash"></i> 전면 이미지 삭제</button>
    </div>`;
    toast('전면 이미지가 업데이트되었습니다.');
  } else toast(d.message, 'error');
  input.value = '';
}
async function deleteFrontImg() {
  const d = await api('/heroes/front-image-delete', {});
  if (d.success) {
    document.getElementById('front-preview').innerHTML = `<div class="text-muted text-sm" style="padding:30px;text-align:center;border:2px dashed var(--border);border-radius:6px;max-width:400px"><i class="fas fa-image" style="font-size:28px;margin-bottom:8px;display:block"></i>전면 이미지가 없습니다.</div>`;
    toast('전면 이미지가 삭제되었습니다.');
  } else toast(d.message, 'error');
}

// ── 퀵 링크
function openLinkCreate() {
  document.getElementById('link-modal-title').textContent = '링크 추가';
  document.getElementById('link-id').value = '';
  document.getElementById('lk-title').value = '';
  document.getElementById('lk-link').value = '';
  document.getElementById('lk-desc').value = '';
  document.getElementById('lk-image-preview').innerHTML = '';
  openModal('link-modal');
}
async function openLinkEdit(id) {
  const d = await api('/heroes/link-detail', {id});
  if (!d.success) { toast(d.message,'error'); return; }
  const lk = d.data;
  document.getElementById('link-modal-title').textContent = '링크 수정';
  document.getElementById('link-id').value = lk.id;
  document.getElementById('lk-title').value = lk.title || '';
  document.getElementById('lk-link').value = lk.link || '';
  document.getElementById('lk-desc').value = lk.desc || '';
  document.getElementById('lk-image-preview').innerHTML = lk.image
    ? `<img src="${lk.image}" style="max-height:50px;border-radius:4px">` : '';
  openModal('link-modal');
}
async function saveLink() {
  const id = document.getElementById('link-id').value;
  const fd = new FormData();
  if (id) fd.append('id', id);
  fd.append('title', document.getElementById('lk-title').value);
  fd.append('link',  document.getElementById('lk-link').value);
  fd.append('desc',  document.getElementById('lk-desc').value);
  const img = document.getElementById('lk-image').files[0];
  if (img) fd.append('image', img);
  const btn = document.getElementById('link-save-btn'); btn.disabled = true;
  const d = await fetch(BASE_URL+(id?'/heroes/link-update':'/heroes/link-create'), {method:'POST',body:fd}).then(r=>r.json());
  btn.disabled = false;
  if (d.success) { toast(d.message); closeModal('link-modal'); location.reload(); }
  else toast(d.message, 'error');
}
async function deleteLink(id) {
  confirmAction('이 링크를 삭제하시겠습니까?', async () => {
    const d = await api('/heroes/link-delete', {id});
    if (d.success) { toast('삭제되었습니다.'); document.querySelector(`.link-card[data-id="${id}"]`)?.remove(); }
    else toast(d.message, 'error');
  });
}

function pageInit() { initSortable(); }
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
