<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="page-header-row">
  <h1 class="page-title-main"><i class="fas fa-bullhorn"></i> 공지 관리</h1>
  <?php if(hasPerm('notice.create')): ?>
  <button class="btn btn-primary" onclick="openNoticeModal()"><i class="fas fa-plus"></i> 공지 추가</button>
  <?php endif; ?>
</div>

<div class="filter-bar">
  <a href="?level=" class="filter-btn <?= ($level??'')=='' ? 'active' : '' ?>">전체</a>
  <a href="?level=urgent" class="filter-btn <?= ($level??'')=='urgent' ? 'active' : '' ?>"><i class="fas fa-exclamation-circle"></i> 긴급</a>
  <a href="?level=important" class="filter-btn <?= ($level??'')=='important' ? 'active' : '' ?>"><i class="fas fa-exclamation-triangle"></i> 중요</a>
  <a href="?level=normal" class="filter-btn <?= ($level??'')=='normal' ? 'active' : '' ?>">일반</a>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:70px">등급</th>
          <th>제목</th>
          <th style="width:80px">작성자</th>
          <th style="width:80px">날짜</th>
          <th style="width:60px">조회</th>
          <th style="width:70px">이미지</th>
          <th style="width:100px">관리</th>
        </tr>
      </thead>
      <tbody id="notice-tbody">
        <?php foreach($data['rows'] as $n): ?>
        <tr data-id="<?= $n['id'] ?>" data-image="<?= htmlspecialchars($n['image']??'') ?>">
          <td><?php
            $lvl = $n['emergency_level'];
            $badge = match($lvl) { 'urgent'=>'badge-red', 'important'=>'badge-yellow', default=>'badge-gray' };
            $label = match($lvl) { 'urgent'=>'긴급', 'important'=>'중요', default=>'일반' };
          ?><span class="badge <?= $badge ?>"><?= $label ?></span></td>
          <td>
            <div class="notice-title-cell">
              <?php if($lvl==='urgent'): ?><i class="fas fa-exclamation-circle text-danger"></i><?php endif; ?>
              <span class="fw-500 truncate-text"><?= htmlspecialchars($n['title']) ?></span>
            </div>
            <?php if($n['link']): ?><a href="<?= htmlspecialchars($n['link']) ?>" target="_blank" class="link-badge"><i class="fas fa-link"></i> <?= htmlspecialchars($n['link_text']?:'링크') ?></a><?php endif; ?>
          </td>
          <td class="text-muted"><?= htmlspecialchars($n['writer_name']) ?></td>
          <td class="text-muted"><?= $n['created_date'] ?></td>
          <td class="text-muted"><?= number_format($n['views']) ?></td>
          <td>
            <?php if($n['image']): ?>
            <button class="img-thumb-btn" onclick="showImgPopup('<?= UPLOAD_URL.htmlspecialchars($n['image']) ?>')" title="이미지 보기">
              <img src="<?= UPLOAD_URL.htmlspecialchars($n['image']) ?>" class="img-thumb-mini" onerror="this.parentElement.innerHTML='<i class=\'fas fa-image text-muted\'></i>'">
            </button>
            <?php else: ?><span class="text-muted">—</span><?php endif; ?>
          </td>
          <td>
            <?php if(hasPerm('notice.edit')): ?>
            <button class="btn btn-ghost btn-sm" onclick="editNotice(<?= $n['id'] ?>)"><i class="fas fa-edit"></i></button>
            <?php endif; ?>
            <?php if(hasPerm('notice.delete')): ?>
            <button class="btn btn-danger btn-sm" onclick="deleteNotice(<?= $n['id'] ?>)"><i class="fas fa-trash"></i></button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($data['rows'])): ?>
        <tr><td colspan="7" class="empty-td"><i class="fas fa-bullhorn"></i> 공지가 없습니다.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if($pagination['total_pages']>1): ?>
  <div class="pagination">
    <?php if($pagination['has_prev']): ?><a href="?page=<?= $pagination['current']-1 ?>&level=<?= $level??'' ?>" class="page-btn"><i class="fas fa-chevron-left"></i></a><?php endif; ?>
    <?php for($p=$pagination['start_page'];$p<=$pagination['end_page'];$p++): ?>
    <a href="?page=<?= $p ?>&level=<?= $level??'' ?>" class="page-btn <?= $p==$pagination['current']?'active':'' ?>"><?= $p ?></a>
    <?php endfor; ?>
    <?php if($pagination['has_next']): ?><a href="?page=<?= $pagination['current']+1 ?>&level=<?= $level??'' ?>" class="page-btn"><i class="fas fa-chevron-right"></i></a><?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<!-- 이미지 팝업 -->
<div id="img-popup" class="modal-overlay hidden" onclick="if(event.target===this)closeImgPopup()">
  <div style="background:#fff;border-radius:12px;padding:12px;max-width:90vw;max-height:90vh;display:flex;flex-direction:column;align-items:center;gap:8px">
    <div style="display:flex;justify-content:flex-end;width:100%">
      <button class="btn btn-ghost btn-sm btn-icon" onclick="closeImgPopup()"><i class="fas fa-times"></i></button>
    </div>
    <img id="img-popup-src" src="" style="max-width:80vw;max-height:75vh;object-fit:contain;border-radius:6px">
  </div>
</div>

<!-- 공지 모달 -->
<div id="notice-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:680px">
    <div class="modal-header">
      <h3 id="notice-modal-title">공지 추가</h3>
      <button class="modal-close" onclick="closeModal('notice-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="nm-id">
      <div class="form-group"><label class="form-label">제목<span class="req">*</span></label>
        <input class="form-control" id="nm-title"></div>
      <div class="form-group"><label class="form-label">내용<span class="req">*</span></label>
        <textarea class="form-control" id="nm-content" rows="5"></textarea></div>
      <div class="form-grid-3">
        <div class="form-group"><label class="form-label">작성자<span class="req">*</span></label>
          <input class="form-control" id="nm-writer" placeholder="행정부"></div>
        <div class="form-group"><label class="form-label">중요도</label>
          <select class="form-control" id="nm-level">
            <option value="normal">일반</option>
            <option value="important">중요</option>
            <option value="urgent">긴급</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">날짜<span class="req">*</span></label>
          <input class="form-control" type="date" id="nm-date"></div>
      </div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">링크 URL</label>
          <input class="form-control" id="nm-link" placeholder="https://..."></div>
        <div class="form-group"><label class="form-label">링크 버튼 텍스트</label>
          <input class="form-control" id="nm-link-text" placeholder="신청하러 가기"></div>
      </div>

      <!-- 이미지 섹션 -->
      <div class="form-group">
        <label class="form-label">이미지 (최대 1MB)</label>
        <!-- 기존 이미지 (수정 시) -->
        <div id="nm-current-img-wrap" style="display:none;margin-bottom:8px">
          <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px"><i class="fas fa-check-circle" style="color:#16a34a"></i> 현재 저장된 이미지</div>
          <img id="nm-current-img" src="" style="max-height:100px;border-radius:6px;border:1px solid var(--border)">
        </div>
        <!-- 새 이미지 선택 -->
        <div class="img-preview-row">
          <div class="img-thumb-box" id="nm-img-box" style="display:none">
            <div style="font-size:10px;color:var(--primary);font-weight:600;padding:2px 4px;background:#eff6ff;border-radius:3px;position:absolute;top:2px;left:2px">NEW</div>
            <img id="nm-img-preview" src="" style="max-height:80px;object-fit:contain;border-radius:4px;">
          </div>
          <div>
            <input type="file" id="nm-image" accept="image/*" onchange="previewNoticeImg(this)">
            <div id="nm-img-change-note" style="display:none;font-size:11px;color:#d97706;margin-top:4px"><i class="fas fa-arrow-right"></i> 위 이미지로 교체됩니다</div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('notice-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveNotice()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<style>
.page-header-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.page-title-main{font-size:20px;font-weight:700;}
.filter-bar{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;}
.filter-btn{padding:6px 14px;border-radius:20px;font-size:12px;font-weight:500;border:1px solid var(--border);background:var(--surface);color:var(--text-muted);cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:4px;}
.filter-btn:hover,.filter-btn.active{background:var(--primary);color:#fff;border-color:var(--primary);}
.notice-title-cell{display:flex;align-items:center;gap:6px;}
.truncate-text{max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:inline-block;}
.link-badge{display:inline-flex;align-items:center;gap:4px;font-size:11px;color:var(--primary);margin-top:2px;}
.text-danger{color:var(--danger);}
.fw-500{font-weight:500;}
.empty-td{text-align:center;padding:40px;color:var(--text-muted);}
.pagination{display:flex;justify-content:center;padding:16px;gap:4px;}
.page-btn{padding:6px 10px;border:1px solid var(--border);border-radius:4px;font-size:13px;background:var(--surface);color:var(--text);}
.page-btn.active{background:var(--primary);color:#fff;border-color:var(--primary);}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
.img-preview-row{display:flex;align-items:flex-start;gap:12px;}
.img-thumb-box{position:relative;border:1px solid var(--border);border-radius:6px;min-width:90px;height:70px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#fafafa;}
.img-thumb-btn{border:none;background:none;padding:0;cursor:pointer;}
.img-thumb-mini{width:48px;height:40px;object-fit:cover;border-radius:4px;border:1px solid var(--border);display:block;}
</style>

<script>
let _pendingImg = null;
const UPLOAD_URL_BASE = '<?= UPLOAD_URL ?>';

function showImgPopup(url) {
  document.getElementById('img-popup-src').src = url;
  openModal('img-popup');
}
function closeImgPopup() { closeModal('img-popup'); }

function previewNoticeImg(input) {
  if (!input.files[0]) return;
  _pendingImg = input.files[0];
  const url = URL.createObjectURL(input.files[0]);
  const box = document.getElementById('nm-img-box');
  document.getElementById('nm-img-preview').src = url;
  box.style.display = 'flex';
  document.getElementById('nm-img-change-note').style.display = document.getElementById('nm-current-img-wrap').style.display !== 'none' ? '' : 'none';
}

function openNoticeModal(data={}) {
  _pendingImg = null;
  document.getElementById('nm-id').value         = data.id || '';
  document.getElementById('nm-title').value      = data.title || '';
  document.getElementById('nm-content').value    = data.content || '';
  document.getElementById('nm-writer').value     = data.writer_name || '';
  document.getElementById('nm-level').value      = data.emergency_level || 'normal';
  document.getElementById('nm-date').value       = data.created_date || new Date().toISOString().split('T')[0];
  document.getElementById('nm-link').value       = data.link || '';
  document.getElementById('nm-link-text').value  = data.link_text || '';
  document.getElementById('nm-image').value      = '';

  // 기존 이미지 처리
  const curWrap = document.getElementById('nm-current-img-wrap');
  const curImg  = document.getElementById('nm-current-img');
  const newBox  = document.getElementById('nm-img-box');
  const changeNote = document.getElementById('nm-img-change-note');

  if (data.image) {
    curImg.src = UPLOAD_URL_BASE + data.image;
    curWrap.style.display = '';
  } else {
    curWrap.style.display = 'none';
  }
  newBox.style.display = 'none';
  changeNote.style.display = 'none';

  document.getElementById('notice-modal-title').textContent = data.id ? '공지 수정' : '공지 추가';
  openModal('notice-modal');
}

async function editNotice(id) {
  const d = await api('/notice/detail', {id});
  if (d.success) openNoticeModal(d.data);
}

async function saveNotice() {
  const id  = document.getElementById('nm-id').value;
  const fd  = new FormData();
  fd.append('id',             id);
  fd.append('title',          document.getElementById('nm-title').value);
  fd.append('content',        document.getElementById('nm-content').value);
  fd.append('writer_name',    document.getElementById('nm-writer').value);
  fd.append('emergency_level',document.getElementById('nm-level').value);
  fd.append('created_date',   document.getElementById('nm-date').value);
  fd.append('link',           document.getElementById('nm-link').value);
  fd.append('link_text',      document.getElementById('nm-link-text').value);
  if (_pendingImg) fd.append('image', _pendingImg);

  const d = await apiUpload('/notice/' + (id ? 'update' : 'create'), fd, '저장 중...');
  if (!d.success) return toast(d.message, 'error');
  toast(d.message);
  closeModal('notice-modal');

  if (id) {
    const tr = document.querySelector(`tr[data-id="${id}"]`);
    if (tr) {
      const lvl   = document.getElementById('nm-level').value;
      const badge = {urgent:'badge-red',important:'badge-yellow',normal:'badge-gray'}[lvl];
      const label = {urgent:'긴급',important:'중요',normal:'일반'}[lvl];
      tr.querySelector('td:nth-child(1) .badge').className = `badge ${badge}`;
      tr.querySelector('td:nth-child(1) .badge').textContent = label;
      tr.querySelector('.truncate-text').textContent = document.getElementById('nm-title').value;
      tr.querySelector('td:nth-child(3)').textContent = document.getElementById('nm-writer').value;
      tr.querySelector('td:nth-child(4)').textContent = document.getElementById('nm-date').value;
    }
  } else {
    location.reload();
  }
}

async function deleteNotice(id) {
  confirmAction('이 공지를 삭제하시겠습니까?', async () => {
    const d = await api('/notice/delete', {id});
    if (!d.success) return toast(d.message, 'error');
    toast(d.message);
    document.querySelector(`tr[data-id="${id}"]`)?.remove();
  });
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
