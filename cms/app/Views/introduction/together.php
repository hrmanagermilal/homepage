<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="page-header-row">
  <h1 class="page-title-main"><i class="fas fa-handshake"></i> 함께하는 교회 관리</h1>
  <?php if(hasPerm('introduction.create')): ?>
  <button class="btn btn-primary" onclick="openModal('add-modal')"><i class="fas fa-plus"></i> 교회 추가</button>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-church"></i> 파트너 교회 목록</h2>
    <span class="text-muted" style="font-size:12px"><i class="fas fa-info-circle"></i> 드래그로 순서를 변경할 수 있습니다.</span>
  </div>
  <div id="together-list" class="sortable-list">
    <?php foreach($items as $item): ?>
    <div class="together-item" data-id="<?= $item['id'] ?>">
      <div class="together-drag"><i class="fas fa-grip-vertical"></i></div>
      <div class="together-logo">
        <?php if($item['image']): ?>
        <img src="<?= BASE_URL.htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" style="height:48px;object-fit:contain;max-width:120px;" onerror="this.style.display='none'">
        <?php else: ?>
        <div class="logo-placeholder"><i class="fas fa-church"></i></div>
        <?php endif; ?>
      </div>
      <div class="together-info">
        <div class="together-name"><?= htmlspecialchars($item['title']) ?></div>
        <?php if($item['description']): ?><div class="together-desc"><?= htmlspecialchars($item['description']) ?></div><?php endif; ?>
        <?php if($item['link']): ?><a href="<?= htmlspecialchars($item['link']) ?>" target="_blank" class="together-link"><i class="fas fa-external-link-alt"></i> <?= htmlspecialchars($item['link']) ?></a><?php endif; ?>
      </div>
      <div class="together-status">
        <span class="badge <?= $item['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $item['is_active'] ? '활성' : '비활성' ?></span>
      </div>
      <div class="together-actions">
        <?php if(hasPerm('introduction.edit')): ?>
        <button class="btn btn-ghost btn-sm" onclick="editItem(<?= $item['id'] ?>)"><i class="fas fa-edit"></i> 수정</button>
        <?php endif; ?>
        <?php if(hasPerm('introduction.delete')): ?>
        <button class="btn btn-danger btn-sm" onclick="deleteItem(<?= $item['id'] ?>)"><i class="fas fa-trash"></i></button>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if(empty($items)): ?>
    <div class="empty-state"><i class="fas fa-handshake fa-2x"></i><p>등록된 파트너 교회가 없습니다.</p></div>
    <?php endif; ?>
  </div>
</div>

<!-- 추가/수정 모달 -->
<div id="add-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:560px">
    <div class="modal-header">
      <h3 id="modal-title">교회 추가</h3>
      <button class="modal-close" onclick="closeModal('add-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="item-id">

      <!-- 이미지 미리보기 (저장버튼 누를 때 서버 전송) -->
      <div class="form-group">
        <label class="form-label">로고 이미지 (최대 1MB)</label>
        <div id="together-current-wrap" style="display:none;margin-bottom:8px">
          <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px"><i class="fas fa-check-circle" style="color:#16a34a"></i> 현재 저장된 이미지</div>
          <img id="together-current-img" src="" style="max-height:80px;border-radius:4px;border:1px solid var(--border)">
        </div>
        <div class="img-preview-box" id="img-preview-box" style="display:none">
          <span style="font-size:10px;background:#d97706;color:#fff;padding:1px 5px;border-radius:3px;margin-bottom:4px">새 이미지</span>
          <img id="img-preview" src="" style="max-height:100px;object-fit:contain;">
        </div>
        <div id="img-placeholder" class="img-ph" style="border:2px dashed var(--border);border-radius:8px;padding:16px;text-align:center;background:#fafafa;margin-bottom:6px"><i class="fas fa-image"></i><span>이미지 선택</span></div>
        <input type="file" id="item-image" accept="image/*" style="margin-top:4px" onchange="togetherPreviewImg(this)">
      </div>

      <div class="form-group"><label class="form-label">교회 이름<span class="req">*</span></label>
        <input class="form-control" id="item-title" placeholder="하늘씨앗 교회"></div>
      <div class="form-group"><label class="form-label">설명</label>
        <input class="form-control" id="item-desc" placeholder="복음 안에서 함께하는 파트너 교회"></div>
      <div class="form-group"><label class="form-label">홈페이지 링크</label>
        <input class="form-control" id="item-link" placeholder="https://example.com"></div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">순서</label>
          <input class="form-control" type="number" id="item-order" value="0" min="0"></div>
        <div class="form-group"><label class="form-label">상태</label>
          <select class="form-control" id="item-active"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('add-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveItem()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<style>
.page-header-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.page-title-main{font-size:20px;font-weight:700;}
.together-item{display:flex;align-items:center;gap:16px;padding:16px 20px;border-bottom:1px solid var(--border);}
.together-item:hover{background:#fafafa;}
.together-drag{cursor:grab;color:var(--text-muted);font-size:16px;}
.together-logo{width:130px;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
.logo-placeholder{width:60px;height:48px;background:var(--border);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--text-muted);}
.together-info{flex:1;}
.together-name{font-weight:600;font-size:14px;}
.together-desc{font-size:12px;color:var(--text-muted);margin-top:2px;}
.together-link{font-size:11px;color:var(--primary);margin-top:4px;display:inline-block;}
.together-status,.together-actions{flex-shrink:0;}
.together-actions{display:flex;gap:4px;}
.img-preview-box{border:2px dashed var(--border);border-radius:8px;padding:16px;text-align:center;background:#fafafa;min-height:80px;display:flex;align-items:center;justify-content:center;}
.img-ph{display:flex;flex-direction:column;align-items:center;gap:6px;color:var(--text-muted);font-size:12px;}
.img-ph i{font-size:24px;opacity:.4;}
.sortable-ghost{opacity:.4;background:#ede9fe;}
.empty-state{padding:48px;text-align:center;color:var(--text-muted);}
.empty-state i{margin-bottom:12px;opacity:.3;display:block;}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
</style>

<script>
let _pendingImg = null;

function togetherPreviewImg(input) {
  if (!input.files[0]) return;
  _pendingImg = input.files[0];
  const url = URL.createObjectURL(input.files[0]);
  document.getElementById('img-preview').src = url;
  document.getElementById('img-preview-box').style.display = '';
  document.getElementById('img-placeholder').style.display = 'none';
}
function previewImg(input, previewId, placeholderId) {
  if (!input.files[0]) return;
  _pendingImg = input.files[0];
  const url = URL.createObjectURL(input.files[0]);
  const img = document.getElementById(previewId);
  const ph  = document.getElementById(placeholderId);
  img.src = url; img.style.display = 'block';
  if (ph) ph.style.display = 'none';
}

function openAddModal() {
  _pendingImg = null;
  document.getElementById('item-id').value    = '';
  document.getElementById('item-title').value = '';
  document.getElementById('item-desc').value  = '';
  document.getElementById('item-link').value  = '';
  document.getElementById('item-order').value = 0;
  document.getElementById('item-active').value= 1;
  document.getElementById('img-preview').src = '';
  document.getElementById('img-preview-box').style.display = 'none';
  document.getElementById('img-placeholder').style.display = '';
  document.getElementById('together-current-wrap').style.display = 'none';
  document.getElementById('item-image').value = '';
  document.getElementById('modal-title').textContent = '교회 추가';
  openModal('add-modal');
}

async function editItem(id) {
  const d = await api('/introduction/together-detail', {id});
  if (!d.success) return toast(d.message,'error');
  const row = d.data;
  _pendingImg = null;
  document.getElementById('item-id').value    = row.id;
  document.getElementById('item-title').value = row.title||'';
  document.getElementById('item-desc').value  = row.description||'';
  document.getElementById('item-link').value  = row.link||'';
  document.getElementById('item-order').value = row.order||0;
  document.getElementById('item-active').value= row.is_active||1;
  const img = document.getElementById('img-preview');
  const ph  = document.getElementById('img-placeholder');
  const curW=document.getElementById('together-current-wrap');
  const curI=document.getElementById('together-current-img');
  if(row.image){curI.src=BASE_URL+row.image;curW.style.display='';}
  else{curW.style.display='none';}
  document.getElementById('img-preview-box').style.display='none';
  document.getElementById('img-placeholder').style.display='';
  document.getElementById('item-image').value = '';
  document.getElementById('modal-title').textContent = '교회 수정';
  openModal('add-modal');
}

async function saveItem() {
  const id  = document.getElementById('item-id').value;
  const url = '/introduction/' + (id ? 'together-update' : 'together-create');
  const fd  = new FormData();
  fd.append('id', id);
  fd.append('title', document.getElementById('item-title').value);
  fd.append('description', document.getElementById('item-desc').value);
  fd.append('link', document.getElementById('item-link').value);
  fd.append('order', document.getElementById('item-order').value);
  fd.append('is_active', document.getElementById('item-active').value);
  if (_pendingImg) fd.append('image', _pendingImg);
  const d = await apiUpload(url, fd, '이미지 저장 중...');
  if (!d.success) return toast(d.message, 'error');
  toast(d.message); closeModal('add-modal'); location.reload();
}

async function deleteItem(id) {
  confirmAction('이 항목을 삭제하시겠습니까?', async () => {
    const d = await api('/introduction/together-delete', {id});
    if (!d.success) return toast(d.message, 'error');
    toast(d.message);
    document.querySelector(`.together-item[data-id="${id}"]`)?.remove();
  });
}

function pageInit() {
  const list = document.getElementById('together-list');
  if (list && typeof Sortable !== 'undefined') {
    Sortable.create(list, {
      handle: '.together-drag', animation: 150, ghostClass: 'sortable-ghost',
      onEnd: async () => {
        const orders = [...list.querySelectorAll('.together-item')].map((el,i) => ({id:el.dataset.id, order:i}));
        await api('/introduction/together-reorder', {orders: JSON.stringify(orders)});
      }
    });
  }
}

// 페이지 로드 시 추가 버튼 연결
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.querySelector('[onclick="openModal(\'add-modal\')"]');
  if (btn) btn.setAttribute('onclick', 'openAddModal()');
});
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
