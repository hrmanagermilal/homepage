<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit = hasPerm('album.edit'); ?>

<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/albums" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
</div>

<div class="card" style="margin-bottom:16px">
  <div class="card-header">
    <div style="flex:1">
      <h1 style="font-size:19px;font-weight:600"><?= htmlspecialchars($album['title']) ?></h1>
      <div style="font-size:13px;color:var(--text-muted);margin-top:6px;display:flex;gap:14px;flex-wrap:wrap">
        <span><i class="fas fa-calendar"></i> 등록일 <?= date('Y-m-d', strtotime($album['created_at'])) ?></span>
        <span><i class="fas fa-images"></i> 이미지 <?= count($images) ?>장</span>
      </div>
    </div>
  </div>
  <div class="card-body">
    <h3 style="font-size:14px;margin-bottom:8px">상세 내용</h3>
    <div style="white-space:pre-wrap;line-height:1.7;color:var(--text)"><?= htmlspecialchars($album['content'] ?? '') ?></div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-image" style="color:var(--primary)"></i> 사진 목록</h2>
    <?php if ($canEdit): ?>
    <button class="btn btn-primary btn-sm" onclick="openAddImagesModal()"><i class="fas fa-upload"></i> 사진 추가</button>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <?php if (empty($images)): ?>
    <div style="text-align:center;padding:40px;color:var(--text-muted)">
      <i class="fas fa-image" style="font-size:32px;margin-bottom:8px;display:block"></i>
      등록된 이미지가 없습니다.
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px">
      <?php foreach ($images as $img): ?>
      <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;background:var(--bg)">
        <img src="<?= rtrim(BASE_URL, '/').'/'.ltrim(htmlspecialchars($img['image_url']), '/') ?>" alt="<?= htmlspecialchars($img['alt_text'] ?? '') ?>" style="width:100%;aspect-ratio:3/4;object-fit:cover;display:block">
        <div style="padding:10px 10px 12px">
          <div style="font-size:13px;font-weight:600;line-height:1.4"><?= htmlspecialchars($img['alt_text'] ?? '') ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php if ($canEdit): ?>
<div class="modal-overlay hidden" id="album-add-images-modal">
  <div class="modal" style="max-width:720px">
    <div class="modal-header">
      <h3>사진 여러 장 추가</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('album-add-images-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">사진 선택 <span class="req">*</span></label>
        <input type="file" id="album-extra-images" name="images[]" class="form-control" accept="image/*" multiple onchange="appendExtraImages(this)">
      </div>
      <div id="album-extra-image-list" style="display:grid;gap:10px"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('album-add-images-modal')">취소</button>
      <button class="btn btn-primary" id="album-extra-save-btn" onclick="saveExtraImages()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<script>
const ALBUM_ID = <?= (int)$album['id'] ?>;
let albumExtraFiles = [];

function openAddImagesModal(){
  albumExtraFiles = [];
  document.getElementById('album-extra-images').value = '';
  document.getElementById('album-extra-image-list').innerHTML = '';
  openModal('album-add-images-modal');
}

function appendExtraImages(input){
  const picked = [...input.files];
  for(const f of picked){
    const exists = albumExtraFiles.some(x => x.name === f.name && x.size === f.size && x.lastModified === f.lastModified);
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
}

async function saveExtraImages(){
  const files = albumExtraFiles;
  if(!files.length){
    toast('사진을 한 장 이상 선택해주세요.','error');
    return;
  }

  const fd = new FormData();
  fd.append('album_id', ALBUM_ID);

  const titleInputs = [...document.querySelectorAll('.album-extra-image-title')];
  for(let i = 0; i < files.length; i++){
    fd.append('images[]', files[i]);
    const imageTitle = titleInputs.find(el => Number(el.dataset.index) === i)?.value?.trim() || '';
    fd.append('image_titles[]', imageTitle);
  }

  const btn = document.getElementById('album-extra-save-btn');
  btn.disabled = true;
  showSpinner('사진 업로드 중...');

  let d;
  try {
    d = await fetch(BASE_URL + '/albums/images-add', { method:'POST', body: fd }).then(r => r.json());
  } catch (e) {
    hideSpinner();
    btn.disabled = false;
    toast('서버 오류가 발생했습니다.','error');
    return;
  }

  hideSpinner();
  btn.disabled = false;

  if(d.success){
    toast(d.message);
    closeModal('album-add-images-modal');
    location.reload();
  } else {
    toast(d.message || '업로드에 실패했습니다.','error');
  }
}
</script>
<?php endif; ?>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
