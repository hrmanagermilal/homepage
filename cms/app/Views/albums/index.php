<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canCreate = hasPerm('album.create'); $canDelete = hasPerm('album.delete'); ?>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-images" style="color:var(--primary)"></i> 앨범 관리</h2>
    <?php if ($canCreate): ?>
    <button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i> 새로 앨범 추가하기</button>
    <?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead>
        <tr><th>앨범 제목</th><th>등록 날짜</th><?php if ($canDelete): ?><th style="width:90px">관리</th><?php endif; ?></tr>
      </thead>
      <tbody>
      <?php foreach ($data['rows'] as $r): ?>
      <tr data-id="<?= $r['id'] ?>" style="cursor:pointer" onclick="goDetail(<?= $r['id'] ?>)">
        <td style="font-weight:500"><?= htmlspecialchars($r['title']) ?></td>
        <td class="text-sm text-muted"><?= date('Y-m-d', strtotime($r['created_at'])) ?></td>
        <?php if ($canDelete): ?>
        <td>
          <button type="button" class="btn btn-danger btn-sm btn-icon" title="삭제" onclick="event.stopPropagation(); deleteAlbum(<?= $r['id'] ?>)"><i class="fas fa-trash"></i></button>
        </td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($data['rows'])): ?>
      <tr><td colspan="<?= $canDelete ? '3' : '2' ?>" class="text-muted" style="text-align:center;padding:24px">등록된 앨범이 없습니다.</td></tr>
      <?php endif; ?>
      </tbody>
    </table></div>
  </div>

  <?php if ($pagination['total_pages'] > 1): ?>
  <div class="card-body" style="border-top:1px solid var(--border)">
    <div class="pagination">
      <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['current'] - 1 ?>">&laquo;</a><?php endif; ?>
      <?php for ($p = $pagination['start_page']; $p <= $pagination['end_page']; $p++): ?>
      <<?= $p === $pagination['current'] ? 'span class="active"' : 'a href="?page='.$p.'"' ?>><?= $p ?></<?= $p === $pagination['current'] ? 'span' : 'a' ?>>
      <?php endfor; ?>
      <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['current'] + 1 ?>">&raquo;</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<div class="modal-overlay hidden" id="album-modal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <h3>앨범 추가</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('album-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">제목 <span class="req">*</span></label>
        <input type="text" id="a-title" class="form-control" placeholder="앨범 제목">
      </div>
      <div class="form-group">
        <label class="form-label">내용 <span class="req">*</span></label>
        <textarea id="a-content" class="form-control" rows="6" placeholder="앨범 내용을 입력하세요."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">사진 추가 <span class="req">*</span></label>
        <input type="file" id="a-images" name="images[]" class="form-control" accept="image/*" multiple onchange="appendCreateImages(this)">
      </div>
      <div id="a-image-list" style="display:grid;gap:10px"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('album-modal')">취소</button>
      <button class="btn btn-primary" id="album-save-btn" onclick="saveAlbum()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<script>
let albumCreateFiles = [];

function goDetail(id){
  location.href = BASE_URL + '/albums/view?id=' + id;
}

async function deleteAlbum(id){
  confirmAction('이 앨범을 삭제하시겠습니까? 등록된 사진도 함께 삭제됩니다.', async()=>{
    const d = await api('/albums/delete', { id });
    if(d.success){
      toast(d.message || '삭제되었습니다.');
      document.querySelector(`tr[data-id="${id}"]`)?.remove();
    } else {
      toast(d.message || '삭제에 실패했습니다.','error');
    }
  });
}

function openCreate(){
  albumCreateFiles = [];
  document.getElementById('a-title').value = '';
  document.getElementById('a-content').value = '';
  document.getElementById('a-images').value = '';
  document.getElementById('a-image-list').innerHTML = '';
  openModal('album-modal');
}

function appendCreateImages(input){
  const picked = [...input.files];
  for(const f of picked){
    const exists = albumCreateFiles.some(x => x.name === f.name && x.size === f.size && x.lastModified === f.lastModified);
    if(!exists) albumCreateFiles.push(f);
  }
  input.value = '';
  renderCreateImageInputs();
}

function removeCreateImage(index){
  albumCreateFiles.splice(index, 1);
  renderCreateImageInputs();
}

function renderCreateImageInputs(){
  const box = document.getElementById('a-image-list');
  box.innerHTML = '';
  albumCreateFiles.forEach((f, i) => {
    const preview = URL.createObjectURL(f);
    const name = f.name.replace(/\.[^.]+$/, '');
    box.insertAdjacentHTML('beforeend', `
      <div style="display:grid;grid-template-columns:90px 1fr;gap:10px;align-items:center;padding:8px;border:1px solid var(--border);border-radius:8px">
        <img src="${preview}" alt="" style="width:90px;height:90px;object-fit:cover;border-radius:6px">
        <div>
          <div class="text-sm text-muted" style="margin-bottom:6px;display:flex;justify-content:space-between;align-items:center;gap:8px">
            <span>${f.name}</span>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeCreateImage(${i})"><i class="fas fa-trash"></i></button>
          </div>
          <input type="text" class="form-control album-image-title" data-index="${i}" value="${name}" placeholder="사진 제목">
        </div>
      </div>
    `);
  });
}

async function saveAlbum(){
  const title = document.getElementById('a-title').value.trim();
  const content = document.getElementById('a-content').value.trim();
  const files = albumCreateFiles;

  if(!title){ toast('제목을 입력하세요.','error'); return; }
  if(!content){ toast('내용을 입력하세요.','error'); return; }
  if(!files.length){ toast('사진을 한 장 이상 추가해주세요.','error'); return; }

  const fd = new FormData();
  fd.append('title', title);
  fd.append('content', content);

  const titleInputs = [...document.querySelectorAll('.album-image-title')];
  for(let i = 0; i < files.length; i++){
    fd.append('images[]', files[i]);
    const imageTitle = titleInputs.find(el => Number(el.dataset.index) === i)?.value?.trim() || '';
    fd.append('image_titles[]', imageTitle);
  }

  const btn = document.getElementById('album-save-btn');
  btn.disabled = true;
  showSpinner('앨범 저장 중...');

  let d;
  try {
    d = await fetch(BASE_URL + '/albums/create', { method:'POST', body: fd }).then(r => r.json());
  } catch(e){
    hideSpinner();
    btn.disabled = false;
    toast('서버 오류가 발생했습니다.','error');
    return;
  }

  hideSpinner();
  btn.disabled = false;

  if(d.success){
    toast(d.message);
    closeModal('album-modal');
    location.reload();
  } else {
    toast(d.message || '저장에 실패했습니다.','error');
  }
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
