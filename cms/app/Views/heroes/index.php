<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php
$canEdit   = hasPerm('heroes.edit');
$canCreate = hasPerm('heroes.create');
$canDelete = hasPerm('heroes.delete');
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
            <span class="badge badge-blue"><?= $h['bg_count'] ?>장</span>
          </td>
          <td>
            <?php if($h['has_front']): ?><span class="badge badge-green">있음</span><?php else: ?><span class="badge badge-gray">없음</span><?php endif; ?>
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
          <img src="<?= BASE_URL.htmlspecialchars($lk['icon_url']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:8px">
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
            <label class="btn btn-primary btn-sm"><i class="fas fa-upload"></i>파일 선택<input type="file" id="bg-upload" accept="image/*" multiple style="display:none" onchange="previewBgImages(this)"></label>
          </div>
          <div id="bg-list" class="img-grid"></div>
        </div>
        <!-- 전면 이미지 -->
        <div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <strong style="font-size:13px">전면 이미지 <span class="text-muted text-sm">(1개)</span></strong>
            <label class="btn btn-primary btn-sm"><i class="fas fa-upload"></i>파일 선택<input type="file" id="front-upload" accept="image/*" style="display:none" onchange="previewFrontImage(this)"></label>
          </div>
          <div id="front-preview" style="min-height:50px"></div>
        </div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('img-modal')">닫기</button><button class="btn btn-primary" id="img-save-btn" onclick="saveImgChanges()"><i class="fas fa-save"></i> 저장</button></div>
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
  showSpinner('배너 저장 중...');
  const d = await fetch(BASE_URL + (id ? '/heroes/update' : '/heroes/create'), {method:'POST',body:fd}).then(r=>r.json());
  hideSpinner(); btn.disabled = false;
  if (d.success) {
    toast(d.message); closeModal('hero-modal');
    if(id){
      const tr=document.querySelector(`tr[data-id="${id}"]`);
      if(tr){
        const titleEl=tr.querySelector('td:first-child');
        if(titleEl) titleEl.textContent=document.getElementById('h-title').value||'(제목 없음)';
        const av=document.getElementById('h-active').value;
        const badge=tr.querySelector('td:nth-child(5) .badge');
        if(badge){badge.className='badge '+(av==='1'?'badge-green':'badge-gray');badge.textContent=av==='1'?'활성':'비활성';}
      }
    } else { location.reload(); }
  } else toast(d.message, 'error');
}
async function deleteHero(id) {
  confirmAction('이 배너를 삭제하시겠습니까? 연결된 이미지도 모두 삭제됩니다.', async () => {
    const d = await api('/heroes/delete', {id});
    if (d.success) { toast('삭제되었습니다.'); document.querySelector(`tr[data-id="${id}"]`)?.remove(); }
    else toast(d.message, 'error');
  });
}

// ── Image Manager ─────────────────────────────────────────
// 로컬 상태: 삭제 예정 목록, 새로 추가할 파일 목록
let bgDeleteQueue   = [];   // 서버에서 삭제할 id 목록
let bgNewFiles      = [];   // 새로 추가할 File 객체 배열
let frontNewFile    = null; // 새 전면 이미지 File 객체
let frontDeleteFlag = false;// 전면 이미지 삭제 예정 여부
let currentFrontUrl = null; // 현재 서버에 있는 전면이미지 URL

async function openImgManager(id, title) {
  currentHeroId   = id;
  bgDeleteQueue   = [];
  bgNewFiles      = [];
  frontNewFile    = null;
  frontDeleteFlag = false;
  currentFrontUrl = null;
  document.getElementById('bg-upload').value   = '';
  document.getElementById('front-upload').value = '';
  document.getElementById('img-modal-title').textContent = title + ' — 이미지 관리';
  const d = await api('/heroes/detail', {id});
  if (!d.success) { toast(d.message,'error'); return; }
  renderBgImages(d.data.bg_images || []);
  renderFrontImage(d.data.front_image);
  if (d.data.front_image) currentFrontUrl = d.data.front_image.image_url;
  openModal('img-modal');
}

/* 배경이미지: 서버 데이터 렌더 */
function renderBgImages(imgs) {
  const el = document.getElementById('bg-list');
  el.innerHTML = '';
  if (!imgs.length && !bgNewFiles.length) {
    el.innerHTML = '<div class="text-muted text-sm">배경 이미지 없음<br><small>파일 선택 버튼으로 추가하세요</small></div>';
    return;
  }
  // 서버에 이미 있는 이미지 (삭제 큐에 없는 것만)
  imgs.forEach(img => {
    if (bgDeleteQueue.includes(img.id)) return;
    const div = document.createElement('div');
    div.className = 'img-card'; div.dataset.id = img.id;
    div.innerHTML = `<img src="${BASE_URL+img.image_url}" alt="">
      <div class="img-actions">
        <button onclick="markBgDelete(${img.id},this.closest('.img-card'))"
          style="background:var(--danger);color:#fff;border:none;border-radius:4px;padding:3px 6px;font-size:11px;cursor:pointer">
          <i class="fas fa-trash"></i>
        </button>
      </div>`;
    el.appendChild(div);
  });
  // 새로 선택한 파일 미리보기
  bgNewFiles.forEach((file, idx) => {
    const url = URL.createObjectURL(file);
    const div = document.createElement('div');
    div.className = 'img-card'; div.dataset.newIdx = idx;
    div.style.outline = '2px solid var(--success)'; // 새 파일 표시
    div.innerHTML = `<img src="${url}" alt="">
      <div class="img-actions">
        <button onclick="cancelNewBg(${idx},this.closest('.img-card'))"
          style="background:var(--danger);color:#fff;border:none;border-radius:4px;padding:3px 6px;font-size:11px;cursor:pointer">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(16,185,129,.85);color:#fff;font-size:10px;text-align:center;padding:2px">미저장</div>`;
    el.appendChild(div);
  });
  if (typeof Sortable !== 'undefined' && el.children.length > 1) {
    new Sortable(el, {animation:150, onEnd: () => {
      // 순서 변경은 저장 시 반영 (서버 기존 이미지만 재정렬)
      const ids = [...el.querySelectorAll('[data-id]')].map(r => parseInt(r.dataset.id));
      el.dataset.order = JSON.stringify(ids);
    }});
  }
}

/* 전면이미지: 서버 데이터 또는 로컬 미리보기 렌더 */
function renderFrontImage(fi) {
  const el = document.getElementById('front-preview');
  if (frontDeleteFlag && !frontNewFile) {
    el.innerHTML = '<div class="text-muted text-sm" style="padding:20px;text-align:center;border:2px dashed var(--danger);border-radius:6px;color:var(--danger)">삭제 예정 (저장 시 반영)</div>';
    return;
  }
  if (frontNewFile) {
    const url = URL.createObjectURL(frontNewFile);
    el.innerHTML = `<div style="position:relative">
      <img src="${url}" style="width:100%;border-radius:6px;max-height:160px;object-fit:cover">
      <div style="background:rgba(16,185,129,.85);color:#fff;font-size:11px;text-align:center;padding:3px;border-radius:0 0 6px 6px">미저장 (새 파일)</div>
      <button onclick="cancelFrontNew()" class="btn btn-danger btn-sm" style="margin-top:6px;width:100%"><i class="fas fa-times"></i> 선택 취소</button>
    </div>`;
    return;
  }
  if (fi) {
    el.innerHTML = `<div>
      <img src="${BASE_URL+fi.image_url}" style="width:100%;border-radius:6px;max-height:160px;object-fit:cover">
      <button onclick="markFrontDelete()" class="btn btn-danger btn-sm" style="margin-top:8px;width:100%"><i class="fas fa-trash"></i> 전면 이미지 삭제</button>
    </div>`;
  } else {
    el.innerHTML = '<div class="text-muted text-sm" style="padding:20px;text-align:center;border:2px dashed var(--border);border-radius:6px">전면 이미지 없음<br><small>파일 선택 버튼으로 추가하세요</small></div>';
  }
}

/* 배경이미지: 파일 선택 → 로컬 미리보기만 */
function previewBgImages(input) {
  const limit = 1*1024*1024;
  for (const f of input.files) {
    if (f.size > limit) { toast(`"${f.name}" 1MB 초과`, 'error'); continue; }
    bgNewFiles.push(f);
  }
  // 현재 서버 이미지 목록 유지하며 재렌더
  refreshBgRender();
  input.value = '';
}
async function refreshBgRender() {
  const d = await api('/heroes/detail', {id: currentHeroId});
  renderBgImages(d.data.bg_images || []);
}

/* 배경이미지: 서버 이미지 삭제 예약 */
function markBgDelete(id, el) {
  bgDeleteQueue.push(id);
  el.remove();
}

/* 배경이미지: 새 파일 선택 취소 */
function cancelNewBg(idx, el) {
  bgNewFiles.splice(idx, 1);
  el.remove();
  // idx 이후 data-new-idx 재번호
  document.querySelectorAll('[data-new-idx]').forEach((el2, i) => { el2.dataset.newIdx = i; });
}

/* 전면이미지: 파일 선택 → 로컬 미리보기만 */
function previewFrontImage(input) {
  const f = input.files[0]; if (!f) return;
  if (f.size > 1*1024*1024) { toast('1MB 초과 파일입니다.','error'); input.value=''; return; }
  frontNewFile    = f;
  frontDeleteFlag = false;
  renderFrontImage(null);
  input.value = '';
}

/* 전면이미지: 삭제 예약 */
function markFrontDelete() {
  frontDeleteFlag = true;
  frontNewFile    = null;
  currentFrontUrl = null;
  renderFrontImage(null);
}

/* 전면이미지: 새 파일 선택 취소 */
function cancelFrontNew() {
  frontNewFile = null;
  // 원래 서버 이미지 복원
  renderFrontImage(currentFrontUrl ? {image_url: currentFrontUrl} : null);
}

/* ★ 최종 저장 - 저장 버튼 클릭 시 */
async function saveImgChanges() {
  const btn = document.getElementById('img-save-btn');
  btn.disabled = true;
  showSpinner('이미지 저장 중...');
  let totalSteps = bgDeleteQueue.length + bgNewFiles.length + (frontDeleteFlag ? 1 : 0) + (frontNewFile ? 1 : 0);
  let done = 0;

  try {
    // 1. 배경이미지 삭제
    for (const id of bgDeleteQueue) {
      document.getElementById('upload-spinner-msg').textContent = `삭제 중... (${++done}/${totalSteps})`;
      await api('/heroes/bg-image-delete', {id});
    }

    // 2. 배경이미지 새 파일 업로드
    for (const file of bgNewFiles) {
      document.getElementById('upload-spinner-msg').textContent = `이미지 업로드 중... (${++done}/${totalSteps})`;
      const fd = new FormData();
      fd.append('hero_id', currentHeroId);
      fd.append('image', file);
      await fetch(BASE_URL+'/heroes/bg-image-add', {method:'POST',body:fd});
    }

    // 3. 배경이미지 순서 저장
    const bgEl = document.getElementById('bg-list');
    const orderIds = bgEl.dataset.order ? JSON.parse(bgEl.dataset.order) : null;
    if (orderIds && orderIds.length) {
      await api('/heroes/bg-image-reorder', {orders: JSON.stringify(orderIds.map((id,i)=>({id,order:i+1})))});
    }

    // 4. 전면이미지 삭제
    if (frontDeleteFlag) {
      document.getElementById('upload-spinner-msg').textContent = `전면 이미지 삭제 중... (${++done}/${totalSteps})`;
      await api('/heroes/front-image-delete', {hero_id: currentHeroId});
    }

    // 5. 전면이미지 새 파일 업로드
    if (frontNewFile) {
      document.getElementById('upload-spinner-msg').textContent = `전면 이미지 업로드 중... (${++done}/${totalSteps})`;
      const fd = new FormData();
      fd.append('hero_id', currentHeroId);
      fd.append('image', frontNewFile);
      await fetch(BASE_URL+'/heroes/front-image-upsert', {method:'POST',body:fd});
    }

    // 완료 후 상태 초기화 및 화면 갱신
    bgDeleteQueue   = [];
    bgNewFiles      = [];
    frontNewFile    = null;
    frontDeleteFlag = false;
    const d = await api('/heroes/detail', {id: currentHeroId});
    renderBgImages(d.data.bg_images || []);
    renderFrontImage(d.data.front_image);
    if (d.data.front_image) currentFrontUrl = d.data.front_image.image_url;
    // 테이블의 배경이미지 수 업데이트
    const tr = document.querySelector(`tr[data-id="${currentHeroId}"]`);
    if (tr) {
      const bgBadge = tr.querySelector('td:nth-child(3) .badge');
      if (bgBadge) bgBadge.textContent = (d.data.bg_images||[]).length + '장';
      const fiBadge = tr.querySelector('td:nth-child(4) .badge');
      if (fiBadge) { fiBadge.className='badge '+(d.data.front_image?'badge-green':'badge-gray'); fiBadge.textContent=d.data.front_image?'있음':'없음'; }
    }
    toast('이미지가 저장되었습니다.');

  } catch(e) {
    toast('저장 중 오류가 발생했습니다.','error');
  } finally {
    hideSpinner();
    btn.disabled = false;
  }
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
    ? `<img src="${BASE_URL+lk.icon_url}" style="max-height:50px;border-radius:4px">` : '';
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
  showSpinner('링크 저장 중...');
  const d = await fetch(BASE_URL+(id?'/heroes/link-update':'/heroes/link-create'), {method:'POST',body:fd}).then(r=>r.json());
  hideSpinner(); btn.disabled = false;
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
