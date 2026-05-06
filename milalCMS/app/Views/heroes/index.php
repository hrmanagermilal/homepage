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
.img-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;min-height:50px;}
.img-card{position:relative;border:1px solid var(--border);border-radius:6px;overflow:hidden;}
.img-card img{width:100%;aspect-ratio:16/9;object-fit:cover;display:block;}
.img-card .img-actions{position:absolute;top:4px;right:4px;display:flex;gap:4px;}
.link-card{display:flex;align-items:center;gap:12px;padding:12px 16px;border:1px solid var(--border);border-radius:8px;margin-bottom:8px;background:var(--surface);}
.link-icon{width:40px;height:40px;border-radius:8px;object-fit:cover;background:var(--bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
</style>

<!-- 탭 -->
<div class="tab-bar">
  <button class="tab-btn active" onclick="switchTab('hero-tab',this)"><i class="fas fa-images"></i> 히어로 배너</button>
  <button class="tab-btn" onclick="switchTab('link-tab',this)"><i class="fas fa-link"></i> 히어로 링크</button>
</div>

<!-- ① 히어로 배너 탭 -->
<div id="hero-tab" class="tab-panel active">
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-images" style="color:var(--primary)"></i> 히어로 배너 관리</h2>
      <?php if($canCreate): ?><button class="btn btn-primary" onclick="openHeroCreate()"><i class="fas fa-plus"></i>배너 추가</button><?php endif; ?>
    </div>
    <div class="card-body" style="padding:0">
      <div class="table-wrap"><table>
        <thead><tr><th>제목</th><th>부제목</th><th>배경이미지</th><th>전면이미지</th><th>상태</th><th style="width:160px">관리</th></tr></thead>
        <tbody>
        <?php foreach($heroes as $h): ?>
        <tr data-id="<?= $h['id'] ?>">
          <td class="fw-500"><?= htmlspecialchars($h['title']??'(제목 없음)') ?></td>
          <td class="text-sm text-muted truncate"><?= htmlspecialchars(mb_substr($h['subtitle']??'',0,40)) ?></td>
          <td>
            <?php $bgs=$heroModel->getBgImages($h['id']); ?>
            <span class="badge badge-blue"><?= count($bgs) ?>장</span>
          </td>
          <td>
            <?php $fi=$heroModel->getFrontImage($h['id']); ?>
            <?php if($fi): ?><span class="badge badge-green">있음</span><?php else: ?><span class="badge badge-gray">없음</span><?php endif; ?>
          </td>
          <td><span class="badge <?= $h['is_active']?'badge-green':'badge-gray' ?>"><?= $h['is_active']?'활성':'비활성' ?></span></td>
          <td><div class="flex gap-8">
            <?php if($canEdit): ?><button class="btn btn-ghost btn-sm" onclick="openImgManager(<?= $h['id'] ?>, '<?= htmlspecialchars(addslashes($h['title']??'히어로')) ?>')"><i class="fas fa-images"></i>이미지</button><?php endif; ?>
            <?php if($canEdit): ?><button class="btn btn-warning btn-sm btn-icon" onclick="openHeroEdit(<?= $h['id'] ?>)"><i class="fas fa-pen"></i></button><?php endif; ?>
            <?php if($canDelete): ?><button class="btn btn-danger btn-sm btn-icon" onclick="deleteHero(<?= $h['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div>

<!-- ② 히어로 링크 탭 -->
<div id="link-tab" class="tab-panel">
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-link" style="color:var(--primary)"></i> 히어로 링크 관리</h2>
      <?php if($canCreate): ?><button class="btn btn-primary" onclick="openLinkCreate()"><i class="fas fa-plus"></i>링크 추가</button><?php endif; ?>
    </div>
    <div class="card-body">
      <?php if(empty($links)): ?>
      <div style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-link" style="font-size:28px;margin-bottom:8px;display:block"></i>등록된 링크가 없습니다.</div>
      <?php else: ?>
      <?php foreach($links as $lk): ?>
      <div class="link-card" data-id="<?= $lk['id'] ?>">
        <div class="link-icon">
          <?php if($lk['icon_url']): ?>
          <img src="<?= UPLOAD_URL.htmlspecialchars($lk['icon_url']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:8px">
          <?php else: ?>
          <i class="fas fa-link" style="color:var(--text-muted)"></i>
          <?php endif; ?>
        </div>
        <div style="flex:1;min-width:0">
          <div class="fw-500"><?= htmlspecialchars($lk['title']??'') ?></div>
          <div class="text-sm text-muted truncate"><a href="<?= htmlspecialchars($lk['link_url']??'') ?>" target="_blank" style="color:var(--info)"><?= htmlspecialchars($lk['link_url']??'') ?></a></div>
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

<!-- Hero Create/Edit Modal -->
<div class="modal-overlay hidden" id="hero-modal">
  <div class="modal modal-md">
    <div class="modal-header"><h3 id="hero-modal-title">배너 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('hero-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="hero-id">
      <div class="form-group"><label class="form-label">제목</label><input type="text" id="h-title" class="form-control" placeholder="예: 메인 배너"></div>
      <div class="form-group"><label class="form-label">부제목</label><textarea id="h-subtitle" class="form-control" rows="3" placeholder="배너 아래 표시될 설명"></textarea></div>
      <div class="form-group"><label class="form-label">상태</label><select id="h-active" class="form-control"><option value="1">활성</option><option value="0">비활성</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('hero-modal')">취소</button><button class="btn btn-primary" id="hero-save-btn" onclick="saveHero()">저장</button></div>
  </div>
</div>

<!-- Image Manager Modal -->
<div class="modal-overlay hidden" id="img-modal">
  <div class="modal modal-xl">
    <div class="modal-header"><h3 id="img-modal-title">이미지 관리</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('img-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
        <!-- 배경 이미지 -->
        <div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <strong style="font-size:13px">배경 이미지 <span class="text-muted text-sm">(드래그 순서 변경)</span></strong>
            <label class="btn btn-primary btn-sm"><i class="fas fa-upload"></i>추가<input type="file" id="bg-upload" accept="image/*" multiple style="display:none" onchange="uploadBgImages(this)"></label>
          </div>
          <div id="bg-list" class="img-grid"></div>
        </div>
        <!-- 전면 이미지 -->
        <div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <strong style="font-size:13px">전면 이미지 <span class="text-muted text-sm">(1개)</span></strong>
            <label class="btn btn-primary btn-sm"><i class="fas fa-upload"></i>업로드<input type="file" id="front-upload" accept="image/*" style="display:none" onchange="uploadFrontImage(this)"></label>
          </div>
          <div id="front-preview" style="min-height:50px"></div>
        </div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('img-modal')">닫기</button></div>
  </div>
</div>

<!-- Link Create/Edit Modal -->
<div class="modal-overlay hidden" id="link-modal">
  <div class="modal modal-sm">
    <div class="modal-header"><h3 id="link-modal-title">링크 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('link-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="link-id">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="lk-title" class="form-control" placeholder="예: 유튜브 채널"></div>
      <div class="form-group"><label class="form-label">링크 URL <span class="req">*</span></label><input type="url" id="lk-url" class="form-control" placeholder="https://"></div>
      <div class="form-group">
        <label class="form-label">아이콘 이미지</label>
        <input type="file" id="lk-icon" class="form-control" accept="image/*">
        <div id="lk-icon-preview" style="margin-top:8px"></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('link-modal')">취소</button><button class="btn btn-primary" id="link-save-btn" onclick="saveLink()">저장</button></div>
  </div>
</div>

<script>
let currentHeroId = null;

// ── 탭 전환 ───────────────────────────────────────────────
function switchTab(tabId, btn) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById(tabId).classList.add('active');
  btn.classList.add('active');
}

// ── Hero CRUD ─────────────────────────────────────────────
function openHeroCreate() {
  document.getElementById('hero-modal-title').textContent = '배너 추가';
  document.getElementById('hero-id').value = '';
  document.getElementById('h-title').value = '';
  document.getElementById('h-subtitle').value = '';
  document.getElementById('h-active').value = 1;
  openModal('hero-modal');
}
async function openHeroEdit(id) {
  const d = await api('/heroes/detail', {id});
  if (!d.success) { toast(d.message,'error'); return; }
  const h = d.data;
  document.getElementById('hero-modal-title').textContent = '배너 수정';
  document.getElementById('hero-id').value = h.id;
  document.getElementById('h-title').value = h.title || '';
  document.getElementById('h-subtitle').value = h.subtitle || '';
  document.getElementById('h-active').value = h.is_active;
  openModal('hero-modal');
}
async function saveHero() {
  const id = document.getElementById('hero-id').value;
  const fd = new FormData();
  if (id) fd.append('id', id);
  fd.append('title',     document.getElementById('h-title').value);
  fd.append('subtitle',  document.getElementById('h-subtitle').value);
  fd.append('is_active', document.getElementById('h-active').value);
  const btn = document.getElementById('hero-save-btn'); btn.disabled = true;
  const d = await fetch(BASE_URL + (id ? '/heroes/update' : '/heroes/create'), {method:'POST',body:fd}).then(r=>r.json());
  btn.disabled = false;
  if (d.success) { toast(d.message); closeModal('hero-modal'); location.reload(); }
  else toast(d.message, 'error');
}
async function deleteHero(id) {
  confirmAction('이 배너를 삭제하시겠습니까? 연결된 이미지도 모두 삭제됩니다.', async () => {
    const d = await api('/heroes/delete', {id});
    if (d.success) { toast('삭제되었습니다.'); document.querySelector(`tr[data-id="${id}"]`)?.remove(); }
    else toast(d.message, 'error');
  });
}

// ── Image Manager ─────────────────────────────────────────
async function openImgManager(id, title) {
  currentHeroId = id;
  document.getElementById('img-modal-title').textContent = title + ' — 이미지 관리';
  const d = await api('/heroes/detail', {id});
  if (!d.success) { toast(d.message,'error'); return; }
  renderBgImages(d.data.bg_images || []);
  renderFrontImage(d.data.front_image);
  openModal('img-modal');
}
function renderBgImages(imgs) {
  const el = document.getElementById('bg-list');
  if (!imgs.length) { el.innerHTML = '<div class="text-muted text-sm">배경 이미지 없음</div>'; return; }
  el.innerHTML = '';
  imgs.forEach(img => {
    const div = document.createElement('div');
    div.className = 'img-card'; div.dataset.id = img.id;
    div.innerHTML = `<img src="${BASE_URL+'/uploads/'+img.image_url}" alt="">
      <div class="img-actions">
        <button onclick="deleteBgImg(${img.id},this.closest('.img-card'))" style="background:var(--danger);color:#fff;border:none;border-radius:4px;padding:3px 6px;font-size:11px;cursor:pointer"><i class="fas fa-trash"></i></button>
      </div>`;
    el.appendChild(div);
  });
  if (typeof Sortable !== 'undefined') {
    new Sortable(el, {animation:150, onEnd: () => {
      const orders = [...el.querySelectorAll('[data-id]')].map((r,i) => ({id:parseInt(r.dataset.id),order:i+1}));
      api('/heroes/bg-image-reorder', {orders: JSON.stringify(orders)});
    }});
  }
}
function renderFrontImage(fi) {
  const el = document.getElementById('front-preview');
  if (fi) {
    el.innerHTML = `<div style="position:relative;display:inline-block;width:100%">
      <img src="${BASE_URL+'/uploads/'+fi.image_url}" style="width:100%;border-radius:6px;max-height:160px;object-fit:cover">
      <button onclick="deleteFrontImg()" class="btn btn-danger btn-sm" style="margin-top:8px;width:100%"><i class="fas fa-trash"></i> 전면 이미지 삭제</button>
    </div>`;
  } else {
    el.innerHTML = '<div class="text-muted text-sm" style="padding:20px;text-align:center;border:2px dashed var(--border);border-radius:6px">전면 이미지 없음</div>';
  }
}
async function uploadBgImages(input) {
  for (const file of input.files) {
    const fd = new FormData();
    fd.append('hero_id', currentHeroId);
    fd.append('image', file);
    await fetch(BASE_URL+'/heroes/bg-image-add', {method:'POST',body:fd});
  }
  const d = await api('/heroes/detail', {id: currentHeroId});
  renderBgImages(d.data.bg_images || []);
  toast(`${input.files.length}장 업로드 완료`);
  input.value = '';
}
async function deleteBgImg(id, el) {
  const d = await api('/heroes/bg-image-delete', {id});
  if (d.success) { el.remove(); toast('이미지가 삭제되었습니다.'); }
  else toast(d.message, 'error');
}
async function uploadFrontImage(input) {
  const fd = new FormData();
  fd.append('hero_id', currentHeroId);
  fd.append('image', input.files[0]);
  const d = await fetch(BASE_URL+'/heroes/front-image-upsert', {method:'POST',body:fd}).then(r=>r.json());
  if (d.success) { renderFrontImage({image_url: d.data.image_url.replace(BASE_URL+'/uploads/','')}); toast('전면 이미지가 업데이트되었습니다.'); }
  else toast(d.message, 'error');
  input.value = '';
}
async function deleteFrontImg() {
  const d = await api('/heroes/front-image-delete', {hero_id: currentHeroId});
  if (d.success) { renderFrontImage(null); toast('전면 이미지가 삭제되었습니다.'); }
  else toast(d.message, 'error');
}

// ── Link CRUD ─────────────────────────────────────────────
function openLinkCreate() {
  document.getElementById('link-modal-title').textContent = '링크 추가';
  document.getElementById('link-id').value = '';
  document.getElementById('lk-title').value = '';
  document.getElementById('lk-url').value = '';
  document.getElementById('lk-icon-preview').innerHTML = '';
  openModal('link-modal');
}
async function openLinkEdit(id) {
  const d = await api('/heroes/link-detail', {id});
  if (!d.success) { toast(d.message,'error'); return; }
  const lk = d.data;
  document.getElementById('link-modal-title').textContent = '링크 수정';
  document.getElementById('link-id').value = lk.id;
  document.getElementById('lk-title').value = lk.title || '';
  document.getElementById('lk-url').value = lk.link_url || '';
  document.getElementById('lk-icon-preview').innerHTML = lk.icon_url
    ? `<img src="${BASE_URL+'/uploads/'+lk.icon_url}" style="max-height:50px;border-radius:4px">` : '';
  openModal('link-modal');
}
async function saveLink() {
  const id = document.getElementById('link-id').value;
  const fd = new FormData();
  if (id) fd.append('id', id);
  fd.append('title',    document.getElementById('lk-title').value);
  fd.append('link_url', document.getElementById('lk-url').value);
  const icon = document.getElementById('lk-icon').files[0];
  if (icon) fd.append('icon', icon);
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

function pageInit() {
  // Sortable은 openImgManager 내부에서 초기화
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
