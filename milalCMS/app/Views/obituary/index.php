<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="page-header-row">
  <h1 class="page-title-main"><i class="fas fa-dove"></i> 부고 관리</h1>
  <?php if(hasPerm('obituary.create')): ?>
  <button class="btn btn-primary" onclick="openObitModal()"><i class="fas fa-plus"></i> 부고 추가</button>
  <?php endif; ?>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>제목</th>
          <th style="width:200px">설명 (요약)</th>
          <th style="width:100px">날짜</th>
          <th style="width:70px">상태</th>
          <th style="width:100px">관리</th>
        </tr>
      </thead>
      <tbody id="obit-tbody">
        <?php foreach($data['rows'] as $o): ?>
        <tr data-id="<?= $o['id'] ?>">
          <td>
            <div class="fw-500" style="max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars(strip_tags($o['title'])) ?></div>
          </td>
          <td class="text-muted" style="font-size:12px;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars(mb_substr(strip_tags($o['description']??''),0,80)) ?></td>
          <td class="text-muted"><?= $o['date'] ?? '—' ?></td>
          <td><span class="badge <?= $o['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $o['is_active'] ? '활성' : '비활성' ?></span></td>
          <td>
            <?php if(hasPerm('obituary.edit')): ?>
            <button class="btn btn-ghost btn-sm" onclick="editObit(<?= $o['id'] ?>)"><i class="fas fa-edit"></i></button>
            <?php endif; ?>
            <?php if(hasPerm('obituary.delete')): ?>
            <button class="btn btn-danger btn-sm" onclick="deleteObit(<?= $o['id'] ?>)"><i class="fas fa-trash"></i></button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($data['rows'])): ?>
        <tr><td colspan="5" class="empty-td"><i class="fas fa-dove"></i> 등록된 부고가 없습니다.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if($pagination['total_pages']>1): ?>
  <div class="pagination">
    <?php if($pagination['has_prev']): ?><a href="?page=<?= $pagination['current']-1 ?>" class="page-btn"><i class="fas fa-chevron-left"></i></a><?php endif; ?>
    <?php for($p=$pagination['start_page'];$p<=$pagination['end_page'];$p++): ?>
    <a href="?page=<?= $p ?>" class="page-btn <?= $p==$pagination['current']?'active':'' ?>"><?= $p ?></a>
    <?php endfor; ?>
    <?php if($pagination['has_next']): ?><a href="?page=<?= $pagination['current']+1 ?>" class="page-btn"><i class="fas fa-chevron-right"></i></a><?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<!-- 부고 모달 -->
<div id="obit-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:640px">
    <div class="modal-header">
      <h3 id="obit-modal-title">부고 추가</h3>
      <button class="modal-close" onclick="closeModal('obit-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="om-id">
      <div class="form-group">
        <label class="form-label">제목<span class="req">*</span>
          <span class="hint">HTML 태그 사용 가능 (예: 박OO 집사&lt;br&gt;모친 OOO 소천)</span>
        </label>
        <input class="form-control" id="om-title" placeholder="박OO 집사&lt;br&gt;모친 OOO 소천(영광 O순)">
      </div>
      <div class="form-group">
        <label class="form-label">요약 설명 (description)</label>
        <textarea class="form-control" id="om-description" rows="3" placeholder="OOO 권사님(딸: 박OO 집사)께서 2026년 4월 17일(금) 향년 84세로..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">상세 내용 (content) — HTML 사용 가능</label>
        <textarea class="form-control" id="om-content" rows="5" placeholder="상세 부고 내용을 입력하세요. HTML 가능 (br 태그 등)"></textarea>
      </div>
      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">날짜</label>
          <input class="form-control" type="date" id="om-date">
        </div>
        <div class="form-group">
          <label class="form-label">상태</label>
          <select class="form-control" id="om-active">
            <option value="1">활성</option>
            <option value="0">비활성</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('obit-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveObit()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<style>
.page-header-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.page-title-main{font-size:20px;font-weight:700;}
.fw-500{font-weight:500;}
.text-muted{color:var(--text-muted);}
.empty-td{text-align:center;padding:40px;color:var(--text-muted);}
.pagination{display:flex;justify-content:center;padding:16px;gap:4px;}
.page-btn{padding:6px 10px;border:1px solid var(--border);border-radius:4px;font-size:13px;background:var(--surface);color:var(--text);}
.page-btn.active{background:var(--primary);color:#fff;border-color:var(--primary);}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.hint{font-size:11px;color:var(--text-muted);font-weight:400;margin-left:4px;}
</style>

<script>
function openObitModal(data={}) {
  document.getElementById('om-id').value          = data.id || '';
  document.getElementById('om-title').value       = data.title || '';
  document.getElementById('om-description').value = data.description || '';
  document.getElementById('om-content').value     = data.content || '';
  document.getElementById('om-date').value        = data.date || '';
  document.getElementById('om-active').value      = data.is_active ?? 1;
  document.getElementById('obit-modal-title').textContent = data.id ? '부고 수정' : '부고 추가';
  openModal('obit-modal');
}

async function editObit(id) {
  const d = await api('/obituary/detail', {id});
  if (d.success) openObitModal(d.data);
}

async function saveObit() {
  const id  = document.getElementById('om-id').value;
  const d   = await api('/obituary/' + (id ? 'update' : 'create'), {
    id,
    title:       document.getElementById('om-title').value,
    description: document.getElementById('om-description').value,
    content:     document.getElementById('om-content').value,
    date:        document.getElementById('om-date').value,
    is_active:   document.getElementById('om-active').value,
  });
  if (!d.success) return toast(d.message, 'error');
  toast(d.message);
  closeModal('obit-modal');
  if (id) {
    const tr = document.querySelector(`tr[data-id="${id}"]`);
    if (tr) {
      tr.querySelector('td:nth-child(1) .fw-500').textContent = document.getElementById('om-title').value.replace(/<br>/gi,' ');
      tr.querySelector('td:nth-child(3)').textContent = document.getElementById('om-date').value || '—';
      const activeVal = document.getElementById('om-active').value;
      const badge = tr.querySelector('.badge');
      badge.className = `badge ${activeVal=='1'?'badge-green':'badge-gray'}`;
      badge.textContent = activeVal=='1'?'활성':'비활성';
    }
  } else {
    location.reload();
  }
}

async function deleteObit(id) {
  confirmAction('이 부고를 삭제하시겠습니까?', async () => {
    const d = await api('/obituary/delete', {id});
    if (!d.success) return toast(d.message, 'error');
    toast(d.message);
    document.querySelector(`tr[data-id="${id}"]`)?.remove();
  });
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
