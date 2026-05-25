<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="page-header">
  <h1 class="page-title-main"><i class="fas fa-church"></i> 교회비전 관리</h1>
</div>

<!-- 탭 -->
<div class="tab-bar">
  <button class="tab-btn active" onclick="switchTab('vision-tab',this)"><i class="fas fa-star"></i> 비전 선언문</button>

</div>

<!-- ── 비전 선언문 탭 ── -->
<div id="vision-tab" class="tab-content">
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-list-ul"></i> 비전 선언문</h2>
      <?php if(hasPerm('introduction.create')): ?>
      <button class="btn btn-primary btn-sm" onclick="openVisionModal()"><i class="fas fa-plus"></i> 새 비전 추가</button>
      <?php endif; ?>
    </div>
    <div class="card-body p-0">
      <div id="vision-list" class="sortable-list">
        <?php foreach($visions as $v): ?>
        <div class="vision-item" data-id="<?= $v['id'] ?>">
          <div class="vision-drag"><i class="fas fa-grip-vertical"></i></div>
          <div class="vision-body">
            <div class="vision-title-row">
              <span class="vision-title"><?= htmlspecialchars($v['title']) ?></span>
              <?php if($v['title_en']): ?><span class="vision-title-en"><?= htmlspecialchars($v['title_en']) ?></span><?php endif; ?>
              <span class="badge <?= $v['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $v['is_active'] ? '활성' : '비활성' ?></span>
            </div>
            <?php if($v['points']): ?>
            <div class="vision-points"><?= nl2br(htmlspecialchars(mb_substr($v['points'],0,100))) ?>...</div>
            <?php endif; ?>
          </div>
          <div class="vision-actions">
            <?php if(hasPerm('introduction.edit')): ?>
            <button class="btn btn-ghost btn-sm" onclick="editVision(<?= $v['id'] ?>)"><i class="fas fa-edit"></i></button>
            <?php endif; ?>
            <?php if(hasPerm('introduction.delete')): ?>
            <button class="btn btn-danger btn-sm" onclick="deleteVision(<?= $v['id'] ?>)"><i class="fas fa-trash"></i></button>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($visions)): ?>
        <div class="empty-state"><i class="fas fa-star fa-2x"></i><p>등록된 비전이 없습니다.</p></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>


<!-- ── 비전 모달 ── -->
<div id="vision-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:680px">
    <div class="modal-header">
      <h3 id="vision-modal-title">비전 추가</h3>
      <button class="modal-close" onclick="closeModal('vision-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="vm-id">
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">제목 (한국어)<span class="req">*</span></label>
          <input class="form-control" id="vm-title" placeholder="예배 공동체"></div>
        <div class="form-group"><label class="form-label">제목 (영어)</label>
          <input class="form-control" id="vm-title-en" placeholder="Worship Community"></div>
      </div>
      <div class="form-group"><label class="form-label">비전 항목 (한국어) — 줄바꿈으로 구분</label>
        <textarea class="form-control" id="vm-points" rows="6" placeholder="찬양과 설교가 되는 역동적 예배&#10;각 예배의 차별화를 통한 영적 필요충족"></textarea></div>
      <div class="form-group"><label class="form-label">비전 항목 (영어) — 줄바꿈으로 구분</label>
        <textarea class="form-control" id="vm-points-en" rows="6" placeholder="Dynamic worship through praise and sermon&#10;Meeting spiritual needs through distinct services"></textarea></div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">순서</label>
          <input class="form-control" type="number" id="vm-order" value="0" min="0"></div>
        <div class="form-group"><label class="form-label">상태</label>
          <select class="form-control" id="vm-active"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('vision-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveVision()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<!-- ── 섹션 타이틀 모달 ── -->
<div id="section-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:600px">
    <div class="modal-header">
      <h3 id="section-modal-title">섹션 타이틀 추가</h3>
      <button class="modal-close" onclick="closeModal('section-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="sm-id">
      <div class="form-group"><label class="form-label">카테고리<span class="req">*</span></label>
        <input class="form-control" id="sm-category" placeholder="Sermon, Jubo, Worship, News, Directions, Community"></div>
      <div class="form-group"><label class="form-label">제목<span class="req">*</span></label>
        <input class="form-control" id="sm-title" placeholder="최신 설교"></div>
      <div class="form-group"><label class="form-label">부제목</label>
        <textarea class="form-control" id="sm-subtitle" rows="3" placeholder="섹션 부제목을 입력하세요."></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('section-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveSection()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<style>
.page-header{margin-bottom:20px;}.page-title-main{font-size:20px;font-weight:700;}
.tab-bar{display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);padding-bottom:0;}
.tab-btn{padding:10px 20px;border:none;background:none;font-size:13px;font-weight:500;cursor:pointer;color:var(--text-muted);border-bottom:2px solid transparent;margin-bottom:-2px;display:flex;align-items:center;gap:6px;transition:all .15s;}
.tab-btn:hover{color:var(--primary);}
.tab-btn.active{color:var(--primary);border-bottom-color:var(--primary);}
.tab-content{} .tab-content.hidden{display:none;}
.vision-item{display:flex;align-items:flex-start;gap:14px;padding:16px 20px;border-bottom:1px solid var(--border);transition:background .1s;}
.vision-item:hover{background:#fafafa;}
.vision-drag{cursor:grab;color:var(--text-muted);padding-top:2px;font-size:16px;}
.vision-body{flex:1;}
.vision-title-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px;}
.vision-title{font-weight:600;font-size:14px;}
.vision-title-en{font-size:12px;color:var(--text-muted);}
.vision-points{font-size:12px;color:var(--text-muted);line-height:1.6;}
.vision-actions{display:flex;gap:4px;flex-shrink:0;}
.sortable-ghost{opacity:.4;background:#ede9fe;}
.empty-state{padding:48px;text-align:center;color:var(--text-muted);}
.empty-state i{margin-bottom:12px;opacity:.3;}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.fw-500{font-weight:500;}
</style>

<script>
/* ── 탭 ── */
function switchTab(id, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.add('hidden'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById(id).classList.remove('hidden');
  btn.classList.add('active');
}

/* ── Vision CRUD ── */
function openVisionModal(data={}) {
  document.getElementById('vm-id').value       = data.id || '';
  document.getElementById('vm-title').value    = data.title || '';
  document.getElementById('vm-title-en').value = data.title_en || '';
  document.getElementById('vm-points').value   = data.points || '';
  document.getElementById('vm-points-en').value= data.points_en || '';
  document.getElementById('vm-order').value    = data.order || 0;
  document.getElementById('vm-active').value   = data.is_active ?? 1;
  document.getElementById('vision-modal-title').textContent = data.id ? '비전 수정' : '비전 추가';
  openModal('vision-modal');
}
async function editVision(id) {
  const d = await api('/introduction/vision-detail', {id});
  if (d.success) openVisionModal(d.data);
}
async function saveVision() {
  const id = document.getElementById('vm-id').value;
  const d  = await api('/introduction/' + (id ? 'vision-update' : 'vision-create'), {
    id, title: document.getElementById('vm-title').value,
    title_en: document.getElementById('vm-title-en').value,
    points: document.getElementById('vm-points').value,
    points_en: document.getElementById('vm-points-en').value,
    order: document.getElementById('vm-order').value,
    is_active: document.getElementById('vm-active').value,
  });
  if (!d.success) return toast(d.message, 'error');
  toast(d.message); closeModal('vision-modal'); location.reload();
}
function deleteVision(id) {
  confirmAction('이 비전을 삭제하시겠습니까?', async () => {
    const d = await api('/introduction/vision-delete', {id});
    if (!d.success) return toast(d.message, 'error');
    toast(d.message);
    document.querySelector(`.vision-item[data-id="${id}"]`)?.remove();
  });
}

/* ── Section CRUD ── */
function openSectionModal(data={}) {
  document.getElementById('sm-id').value       = data.id || '';
  document.getElementById('sm-category').value = data.category || '';
  document.getElementById('sm-title').value    = data.title || '';
  document.getElementById('sm-subtitle').value = data.subtitle || '';
  document.getElementById('section-modal-title').textContent = data.id ? '섹션 타이틀 수정' : '섹션 타이틀 추가';
  openModal('section-modal');
}
async function editSection(id) {
  const d = await api('/introduction/section-detail', {id});
  if (d.success) openSectionModal(d.data);
}
async function saveSection() {
  const id = document.getElementById('sm-id').value;
  const d  = await api('/introduction/' + (id ? 'section-update' : 'section-create'), {
    id, category: document.getElementById('sm-category').value,
    title: document.getElementById('sm-title').value,
    subtitle: document.getElementById('sm-subtitle').value,
  });
  if (!d.success) return toast(d.message, 'error');
  toast(d.message); closeModal('section-modal'); location.reload();
}
async function deleteSection(id) {
  confirmAction('이 섹션 타이틀을 삭제하시겠습니까?', async () => {
    const d = await api('/introduction/section-delete', {id});
    if (!d.success) return toast(d.message, 'error');
    toast(d.message);
    document.querySelector(`tr[data-id="${id}"]`)?.remove();
  });
}

/* ── Sortable (Vision) ── */
function pageInit() {
  const visionList = document.getElementById('vision-list');
  if (visionList && typeof Sortable !== 'undefined') {
    Sortable.create(visionList, {
      handle: '.vision-drag', animation: 150, ghostClass: 'sortable-ghost',
      onEnd: async () => {
        const orders = [...visionList.querySelectorAll('.vision-item')].map((el,i) => ({id:el.dataset.id, order:i}));
        await api('/introduction/vision-reorder', {orders: JSON.stringify(orders)});
      }
    });
  }
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
