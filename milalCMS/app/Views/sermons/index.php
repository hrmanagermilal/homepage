<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=hasPerm('sermons.edit'); $canCreate=hasPerm('sermons.create'); $canDelete=hasPerm('sermons.delete'); ?>

<!-- 탭 -->
<div class="tab-bar">
  <button class="tab-btn <?= ($_GET['tab']??'sermons')==='sermons'?'active':'' ?>" onclick="switchTab('sermons-tab',this)">
    <i class="fas fa-video"></i> 설교 목록
  </button>
  <button class="tab-btn <?= ($_GET['tab']??'')==='categories'?'active':'' ?>" onclick="switchTab('category-tab',this)">
    <i class="fas fa-tags"></i> 카테고리 관리
    <span class="badge badge-blue" style="margin-left:4px"><?= count($categories) ?></span>
  </button>
</div>

<!-- ══ 설교 목록 탭 ══════════════════════════════════════════ -->
<div id="sermons-tab" class="tab-content <?= ($_GET['tab']??'sermons')==='sermons'?'':'hidden' ?>">
  <div class="card">
    <div class="card-header">
      <div style="display:flex;align-items:center;gap:12px;flex:1;flex-wrap:wrap">
        <h2><i class="fas fa-video" style="color:var(--primary)"></i> 설교 관리</h2>
        <!-- 카테고리 필터 -->
        <select id="cat-filter" class="form-control" style="width:auto;font-size:13px" onchange="filterByCategory(this.value)">
          <option value="0">전체 카테고리</option>
          <?php foreach($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= ($categoryId??0)==$cat['id']?'selected':'' ?>>
            <?= htmlspecialchars($cat['title']) ?> (<?= $cat['sermon_count'] ?>)
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php if($canCreate): ?>
      <button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i> 설교 추가</button>
      <?php endif; ?>
    </div>
    <div class="card-body" style="padding:0">
      <div class="table-wrap"><table>
        <thead><tr>
          <th style="width:90px">썸네일</th>
          <th>제목</th>
          <th style="width:100px">카테고리</th>
          <th style="width:80px">설교자</th>
          <th style="width:80px">설교일</th>
          <th style="width:90px">관리</th>
        </tr></thead>
        <tbody id="sermon-tbody">
        <?php foreach($data['rows'] as $r): ?>
        <tr data-id="<?= $r['id'] ?>">
          <td>
            <?php if($r['thumbnail']): ?>
            <a href="<?= BASE_URL ?>/sermons/view?id=<?= $r['id'] ?>">
              <img src="<?= htmlspecialchars($r['thumbnail']) ?>" style="width:80px;height:50px;object-fit:cover;border-radius:4px" alt="">
            </a>
            <?php else: ?>
            <div style="width:80px;height:50px;background:var(--bg);border-radius:4px;display:flex;align-items:center;justify-content:center;color:var(--text-muted)">
              <i class="fab fa-youtube" style="font-size:18px"></i>
            </div>
            <?php endif; ?>
          </td>
          <td>
            <a href="<?= BASE_URL ?>/sermons/view?id=<?= $r['id'] ?>" style="color:var(--text);font-weight:500;display:block;max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              <?= htmlspecialchars($r['title']) ?>
            </a>
          </td>
          <td>
            <?php if($r['category_name']): ?>
            <span class="badge badge-purple"><?= htmlspecialchars($r['category_name']) ?></span>
            <?php else: ?><span class="text-muted">—</span><?php endif; ?>
          </td>
          <td class="text-sm"><?= htmlspecialchars($r['preacher']??'-') ?></td>
          <td class="text-sm text-muted"><?= $r['sermon_date']??'-' ?></td>
          <td><div class="flex gap-8">
            <?php if($canEdit): ?>
            <button class="btn btn-warning btn-sm btn-icon" onclick="openEdit(<?= $r['id'] ?>)"><i class="fas fa-pen"></i></button>
            <?php endif; ?>
            <?php if($canDelete): ?>
            <button class="btn btn-danger btn-sm btn-icon" onclick="deleteSermon(<?= $r['id'] ?>)"><i class="fas fa-trash"></i></button>
            <?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($data['rows'])): ?>
        <tr><td colspan="6" class="empty-td"><i class="fas fa-video"></i> 설교가 없습니다.</td></tr>
        <?php endif; ?>
        </tbody>
      </table></div>
    </div>
    <?php if($pagination['total_pages']>1): ?>
    <div class="card-body" style="border-top:1px solid var(--border)">
      <div class="pagination">
        <?php if($pagination['has_prev']): ?><a href="?page=<?= $pagination['current']-1 ?>&category=<?= $categoryId??0 ?>">&laquo;</a><?php endif; ?>
        <?php for($p=$pagination['start_page'];$p<=$pagination['end_page'];$p++): ?>
        <<?= $p===$pagination['current']?'span class="active"':'a href="?page='.$p.'&category='.($categoryId??0).'"' ?>><?= $p ?></<?= $p===$pagination['current']?'span':'a' ?>>
        <?php endfor; ?>
        <?php if($pagination['has_next']): ?><a href="?page=<?= $pagination['current']+1 ?>&category=<?= $categoryId??0 ?>">&raquo;</a><?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ══ 카테고리 탭 ════════════════════════════════════════════ -->
<div id="category-tab" class="tab-content <?= ($_GET['tab']??'')==='categories'?'':'hidden' ?>">
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-tags" style="color:var(--primary)"></i> 설교 카테고리</h2>
      <?php if($canCreate): ?>
      <button class="btn btn-primary" onclick="openCatCreate()"><i class="fas fa-plus"></i> 카테고리 추가</button>
      <?php endif; ?>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr>
          <th style="width:80px">이미지</th>
          <th>카테고리명</th>
          <th style="width:80px">설교 수</th>
          <th style="width:120px">등록일</th>
          <th style="width:100px">관리</th>
        </tr></thead>
        <tbody id="cat-tbody">
          <?php foreach($categories as $cat): ?>
          <tr data-id="<?= $cat['id'] ?>">
            <td>
              <?php if($cat['image']): ?>
              <img src="<?= UPLOAD_URL.htmlspecialchars($cat['image']) ?>" style="width:56px;height:40px;object-fit:cover;border-radius:4px" onerror="this.style.display='none'" alt="">
              <?php else: ?>
              <div style="width:56px;height:40px;background:var(--bg);border-radius:4px;display:flex;align-items:center;justify-content:center;color:var(--text-muted)">
                <i class="fas fa-tag"></i>
              </div>
              <?php endif; ?>
            </td>
            <td class="fw-500"><?= htmlspecialchars($cat['title']) ?></td>
            <td><span class="badge badge-blue"><?= $cat['sermon_count'] ?>편</span></td>
            <td class="text-sm text-muted"><?= date('Y-m-d', strtotime($cat['created_at'])) ?></td>
            <td><div class="flex gap-8">
              <?php if($canEdit): ?>
              <button class="btn btn-warning btn-sm btn-icon" onclick="openCatEdit(<?= $cat['id'] ?>)"><i class="fas fa-pen"></i></button>
              <?php endif; ?>
              <?php if($canDelete): ?>
              <button class="btn btn-danger btn-sm btn-icon" onclick="deleteCat(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['title'],ENT_QUOTES) ?>', <?= $cat['sermon_count'] ?>)"><i class="fas fa-trash"></i></button>
              <?php endif; ?>
            </div></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($categories)): ?>
          <tr><td colspan="5" class="empty-td"><i class="fas fa-tags"></i> 등록된 카테고리가 없습니다.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ══ 설교 모달 ══════════════════════════════════════════════ -->
<div class="modal-overlay hidden" id="sermon-modal">
  <div class="modal modal-md">
    <div class="modal-header">
      <h3 id="sermon-modal-title">설교 추가</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('sermon-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="sermon-id">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label>
        <input type="text" id="s-title" class="form-control"></div>
      <div class="form-group">
        <label class="form-label">유튜브 URL <span class="req">*</span></label>
        <input type="url" id="s-url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." oninput="previewYt()">
        <div id="yt-preview" style="margin-top:8px"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">카테고리</label>
          <select id="s-category" class="form-control">
            <option value="0">카테고리 없음</option>
            <?php foreach($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label class="form-label">설교자</label>
          <input type="text" id="s-preacher" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">설교일</label>
          <input type="date" id="s-date" class="form-control"></div>
      </div>
      <div class="form-group"><label class="form-label">설명</label>
        <textarea id="s-desc" class="form-control" rows="3"></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('sermon-modal')">취소</button>
      <button class="btn btn-primary" id="sermon-save-btn" onclick="saveSermon()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<!-- ══ 카테고리 모달 ═══════════════════════════════════════════ -->
<div class="modal-overlay hidden" id="cat-modal">
  <div class="modal" style="max-width:460px">
    <div class="modal-header">
      <h3 id="cat-modal-title">카테고리 추가</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('cat-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="cat-id">
      <div class="form-group"><label class="form-label">카테고리명 <span class="req">*</span></label>
        <input type="text" id="cat-title" class="form-control" placeholder="예: 주일설교, 수요설교, 특별집회"></div>
      <div class="form-group">
        <label class="form-label">대표 이미지 (선택)</label>
        <div id="cat-current-wrap" style="display:none;margin-bottom:8px">
          <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px"><i class="fas fa-check-circle" style="color:#16a34a"></i> 현재 이미지</div>
          <img id="cat-current-img" src="" style="max-height:80px;border-radius:4px;border:1px solid var(--border)">
        </div>
        <div id="cat-new-wrap" style="display:none;margin-bottom:8px">
          <div style="font-size:11px;color:#d97706;font-weight:600;margin-bottom:4px"><i class="fas fa-arrow-right"></i> 교체될 이미지</div>
          <img id="cat-new-img" src="" style="max-height:80px;border-radius:4px;border:1px solid #fcd34d">
        </div>
        <input type="file" id="cat-image" class="form-control" accept="image/*,image/svg+xml" onchange="previewCatImg(this)">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('cat-modal')">취소</button>
      <button class="btn btn-primary" id="cat-save-btn" onclick="saveCat()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<style>
.tab-bar{display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);}
.tab-btn{padding:10px 20px;border:none;background:none;font-size:13px;font-weight:500;cursor:pointer;color:var(--text-muted);border-bottom:2px solid transparent;margin-bottom:-2px;display:flex;align-items:center;gap:6px;transition:all .15s;}
.tab-btn:hover{color:var(--primary);}
.tab-btn.active{color:var(--primary);border-bottom-color:var(--primary);}
.tab-content.hidden{display:none;}
.empty-td{text-align:center;padding:40px;color:var(--text-muted);}
.fw-500{font-weight:500;}
.pagination{display:flex;gap:4px;justify-content:center;}
.pagination a,.pagination span{padding:5px 10px;border:1px solid var(--border);border-radius:4px;font-size:13px;color:var(--text);}
.pagination span.active{background:var(--primary);color:#fff;border-color:var(--primary);}
</style>

<script>
/* ── 탭 ── */
function switchTab(id, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.add('hidden'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById(id).classList.remove('hidden');
  btn.classList.add('active');
}

/* ── 카테고리 필터 ── */
function filterByCategory(val) {
  location.href = '?category=' + val;
}

/* ── 설교 CRUD ── */
function openCreate() {
  document.getElementById('sermon-modal-title').textContent = '설교 추가';
  document.getElementById('sermon-id').value = '';
  ['s-title','s-url','s-preacher','s-desc'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('s-date').value = '';
  document.getElementById('s-category').value = '0';
  document.getElementById('yt-preview').innerHTML = '';
  openModal('sermon-modal');
}

async function openEdit(id) {
  const d = await api('/sermons/detail', {id});
  if (!d.success) { toast(d.message, 'error'); return; }
  const r = d.data;
  document.getElementById('sermon-modal-title').textContent = '설교 수정';
  document.getElementById('sermon-id').value    = r.id;
  document.getElementById('s-title').value      = r.title;
  document.getElementById('s-url').value        = r.youtube_url;
  document.getElementById('s-preacher').value   = r.preacher || '';
  document.getElementById('s-date').value       = r.sermon_date || '';
  document.getElementById('s-desc').value       = r.description || '';
  document.getElementById('s-category').value   = r.category_id || '0';
  document.getElementById('yt-preview').innerHTML = r.youtube_id
    ? `<img src="https://img.youtube.com/vi/${r.youtube_id}/mqdefault.jpg" style="max-width:100%;border-radius:6px">`
    : '';
  openModal('sermon-modal');
}

function extractYtId(url) {
  const m = url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/);
  return m ? m[1] : null;
}
function previewYt() {
  const id = extractYtId(document.getElementById('s-url').value);
  document.getElementById('yt-preview').innerHTML = id
    ? `<img src="https://img.youtube.com/vi/${id}/mqdefault.jpg" style="max-width:100%;border-radius:6px">`
    : '';
}

async function saveSermon() {
  const id  = document.getElementById('sermon-id').value;
  const fd  = new FormData();
  if (id) fd.append('id', id);
  fd.append('title',       document.getElementById('s-title').value);
  fd.append('youtube_url', document.getElementById('s-url').value);
  fd.append('preacher',    document.getElementById('s-preacher').value);
  fd.append('sermon_date', document.getElementById('s-date').value);
  fd.append('description', document.getElementById('s-desc').value);
  fd.append('category_id', document.getElementById('s-category').value);
  const btn = document.getElementById('sermon-save-btn'); btn.disabled = true;
  showSpinner('설교 저장 중...');
  const d = await fetch(BASE_URL + (id ? '/sermons/update' : '/sermons/create'), {method:'POST',body:fd}).then(r=>r.json());
  hideSpinner(); btn.disabled = false;
  if (d.success) {
    toast(d.message); closeModal('sermon-modal');
    if (id) {
      const tr = document.querySelector(`tr[data-id="${id}"]`);
      if (tr) {
        tr.querySelector('td:nth-child(2) a').textContent = document.getElementById('s-title').value;
        tr.querySelector('td:nth-child(4)').textContent   = document.getElementById('s-preacher').value || '-';
        tr.querySelector('td:nth-child(5)').textContent   = document.getElementById('s-date').value || '-';
      }
    } else { location.reload(); }
  } else toast(d.message, 'error');
}

async function deleteSermon(id) {
  confirmAction('이 설교를 삭제하시겠습니까?', async () => {
    const d = await api('/sermons/delete', {id});
    if (d.success) { toast('삭제되었습니다.'); document.querySelector(`tr[data-id="${id}"]`)?.remove(); }
    else toast(d.message, 'error');
  });
}

/* ── 카테고리 CRUD ── */
let _catPendingImg = null;

function previewCatImg(input) {
  if (!input.files[0]) return;
  _catPendingImg = input.files[0];
  const url = URL.createObjectURL(input.files[0]);
  document.getElementById('cat-new-img').src = url;
  document.getElementById('cat-new-wrap').style.display = '';
}

function openCatCreate() {
  _catPendingImg = null;
  document.getElementById('cat-modal-title').textContent = '카테고리 추가';
  document.getElementById('cat-id').value    = '';
  document.getElementById('cat-title').value = '';
  document.getElementById('cat-image').value = '';
  document.getElementById('cat-current-wrap').style.display = 'none';
  document.getElementById('cat-new-wrap').style.display     = 'none';
  openModal('cat-modal');
}

async function openCatEdit(id) {
  const d = await api('/sermons/category-detail', {id});
  if (!d.success) { toast(d.message, 'error'); return; }
  const r = d.data;
  _catPendingImg = null;
  document.getElementById('cat-modal-title').textContent = '카테고리 수정';
  document.getElementById('cat-id').value    = r.id;
  document.getElementById('cat-title').value = r.title;
  document.getElementById('cat-image').value = '';
  document.getElementById('cat-new-wrap').style.display = 'none';
  const curWrap = document.getElementById('cat-current-wrap');
  const curImg  = document.getElementById('cat-current-img');
  if (r.image) {
    curImg.src = '<?= UPLOAD_URL ?>' + r.image;
    curWrap.style.display = '';
  } else {
    curWrap.style.display = 'none';
  }
  openModal('cat-modal');
}

async function saveCat() {
  const id = document.getElementById('cat-id').value;
  const title = document.getElementById('cat-title').value.trim();
  if (!title) return toast('카테고리명을 입력하세요.', 'error');
  const fd = new FormData();
  if (id) fd.append('id', id);
  fd.append('title', title);
  if (_catPendingImg) fd.append('image', _catPendingImg);
  const btn = document.getElementById('cat-save-btn'); btn.disabled = true;
  const d = await apiUpload('/sermons/' + (id ? 'category-update' : 'category-create'), fd, '저장 중...');
  btn.disabled = false;
  if (d.success) {
    toast(d.message); closeModal('cat-modal');
    // 즉시 DOM 갱신
    if (id) {
      const tr = document.querySelector(`#cat-tbody tr[data-id="${id}"]`);
      if (tr) tr.querySelector('td:nth-child(2)').textContent = title;
    } else { location.reload(); }
  } else toast(d.message, 'error');
}

async function deleteCat(id, name, count) {
  if (count > 0) {
    toast(`"${name}"에 설교 ${count}편이 있어 삭제할 수 없습니다.`, 'error');
    return;
  }
  confirmAction(`"${name}" 카테고리를 삭제하시겠습니까?`, async () => {
    const d = await api('/sermons/category-delete', {id});
    if (d.success) { toast('삭제되었습니다.'); document.querySelector(`#cat-tbody tr[data-id="${id}"]`)?.remove(); }
    else toast(d.message, 'error');
  });
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
