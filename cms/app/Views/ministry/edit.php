<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="page-header-row">
  <h1 class="page-title-main"><i class="fas fa-hands-helping"></i> <?= $ministry ? '사역 수정 — '.$ministry['name'] : '새 사역 추가' ?></h1>
  <div class="header-actions">
    <a href="<?= BASE_URL ?>/ministry" class="btn btn-ghost"><i class="fas fa-arrow-left"></i> 목록</a>
    <?php if(hasPerm('ministry.edit') || hasPerm('ministry.create')): ?>
    <button class="btn btn-primary" onclick="saveMinistry()"><i class="fas fa-save"></i> 저장</button>
    <?php endif; ?>
  </div>
</div>

<div class="edit-layout">
  <!-- 좌: 메인 폼 -->
  <div style="flex:1;display:flex;flex-direction:column;gap:20px">

    <div class="card">
      <div class="card-header"><h2><i class="fas fa-info-circle"></i> 기본 정보</h2></div>
      <div class="card-body">
        <input type="hidden" id="ministry-id" value="<?= $ministry['id']??'' ?>">
        <div class="form-grid-3">
          <div class="form-group"><label class="form-label">사역 키 (key)<span class="req">*</span>
            <span class="hint">URL 앵커로 사용 (예: ministry01)</span></label>
            <input class="form-control" id="m-key" value="<?= htmlspecialchars($ministry['key']??'') ?>" placeholder="ministry01" <?= $ministry ? 'readonly' : '' ?>></div>
          <div class="form-group"><label class="form-label">이름<span class="req">*</span></label>
            <input class="form-control" id="m-name" value="<?= htmlspecialchars($ministry['name']??'') ?>" placeholder="양육"></div>
          <div class="form-group"><label class="form-label">순서</label>
            <input class="form-control" type="number" id="m-order" value="<?= $ministry['order']??0 ?>" min="0"></div>
        </div>
        <div class="form-group"><label class="form-label">부제목 (subtitle)</label>
          <input class="form-control" id="m-subtitle" value="<?= htmlspecialchars($ministry['subtitle']??'') ?>" placeholder="우리는 밀알 공동체입니다."></div>
        <div class="form-group"><label class="form-label">타이틀 (title)</label>
          <input class="form-control" id="m-title" value="<?= htmlspecialchars($ministry['title']??'') ?>" placeholder="Milal MBA — 말씀으로 세워가는 훈련 과정"></div>
        <div class="form-group"><label class="form-label">소개 내용 (description)</label>
          <textarea class="form-control" id="m-desc" rows="5" placeholder="사역 소개 내용을 입력하세요."><?= htmlspecialchars($ministry['description']??'') ?></textarea></div>
        <div class="form-group"><label class="form-label">포인트 항목 (points) — 줄바꿈으로 구분</label>
          <textarea class="form-control" id="m-points" rows="6" placeholder="거실반 2.0 — 소그룹 중심&#10;구약/신약 성경대학 — 체계적 성경&#10;성장반 — 심화 훈련"><?= htmlspecialchars($ministry['points']??'') ?></textarea></div>
        <div class="form-group">
          <label class="form-label">상태</label>
          <select class="form-control" id="m-active" style="max-width:200px">
            <option value="1" <?= ($ministry['is_active']??1)==1 ? 'selected' : '' ?>>활성</option>
            <option value="0" <?= ($ministry['is_active']??1)==0 ? 'selected' : '' ?>>비활성</option>
          </select>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h2><i class="fas fa-bell"></i> 공지 박스 (Notice Box)</h2></div>
      <div class="card-body">
        <div class="form-group"><label class="form-label">공지 제목</label>
          <input class="form-control" id="m-notice-title" value="<?= htmlspecialchars($ministry['notice_title']??'') ?>" placeholder="Milal MBA 등록 안내"></div>
        <div class="form-group"><label class="form-label">공지 내용</label>
          <textarea class="form-control" id="m-notice-desc" rows="3" placeholder="공지 내용을 입력하세요."><?= htmlspecialchars($ministry['notice_description']??'') ?></textarea></div>
        <div class="form-grid-2">
          <div class="form-group"><label class="form-label">버튼 텍스트</label>
            <input class="form-control" id="m-notice-btn-label" value="<?= htmlspecialchars($ministry['notice_button_label']??'') ?>" placeholder="등록 안내 다운로드"></div>
          <div class="form-group"><label class="form-label">버튼 링크</label>
            <input class="form-control" id="m-notice-btn-href" value="<?= htmlspecialchars($ministry['notice_button_href']??'') ?>" placeholder="#"></div>
        </div>
        <div class="form-group"><label class="form-label">새 탭으로 열기</label>
          <select class="form-control" id="m-notice-external" style="max-width:200px">
            <option value="0" <?= ($ministry['notice_button_external']??0)==0 ? 'selected' : '' ?>>현재 탭</option>
            <option value="1" <?= ($ministry['notice_button_external']??0)==1 ? 'selected' : '' ?>>새 탭</option>
          </select>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h2><i class="fas fa-mouse-pointer"></i> CTA 버튼</h2></div>
      <div class="card-body">
        <div class="form-grid-2">
          <div class="form-group"><label class="form-label">버튼 텍스트</label>
            <input class="form-control" id="m-cta-label" value="<?= htmlspecialchars($ministry['cta_label']??'') ?>" placeholder="홈페이지 바로가기"></div>
          <div class="form-group"><label class="form-label">버튼 링크</label>
            <input class="form-control" id="m-cta-href" value="<?= htmlspecialchars($ministry['cta_href']??'') ?>" placeholder="https://..."></div>
        </div>
        <div class="form-group"><label class="form-label">새 탭으로 열기</label>
          <select class="form-control" id="m-cta-external" style="max-width:200px">
            <option value="0" <?= ($ministry['cta_external']??0)==0 ? 'selected' : '' ?>>현재 탭</option>
            <option value="1" <?= ($ministry['cta_external']??0)==1 ? 'selected' : '' ?>>새 탭</option>
          </select>
        </div>
      </div>
    </div>

  </div>

  <!-- 우: 이미지 -->
  <div style="width:280px;flex-shrink:0">
    <div class="card" style="position:sticky;top:calc(var(--header-h)+20px)">
      <div class="card-header"><h2><i class="fas fa-image"></i> 대표 이미지</h2></div>
      <div class="card-body">
        <div class="img-upload-box" id="img-box">
<div id="img-current-wrap"
     style="<?= ($ministry['image']??'') ? 'margin-bottom:16px' : 'display:none' ?>">
          <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px"><i class="fas fa-check-circle" style="color:#16a34a"></i> 현재 저장된 이미지</div>
          <img id="img-current" src="<?= ($ministry['image']??'') ? BASE_URL.htmlspecialchars($ministry['image']) : '' ?>" style="max-width:100%;border-radius:6px;">
        </div>
        <div id="img-new-card"
     style="display:none;margin-top:16px;">

  <div style="
      font-size:12px;
      font-weight:600;
      color:#374151;
      margin-bottom:8px;
  ">
      교체될 새 이미지
  </div>

  <div style="
      border:1px solid #d1d5db;
      border-radius:8px;
      padding:10px;
      background:#fff;
  ">
      <img id="img-preview"
           src=""
           style="
             max-width:100%;
             border-radius:6px;
             display:block;
           ">
  </div>

</div>




        <div id="img-ph" class="img-ph" style="<?= ($ministry['image']??'') ? 'display:none' : '' ?>">
            <i class="fas fa-image" style="font-size:40px;opacity:.2;"></i>
            <span style="font-size:12px;color:var(--text-muted);margin-top:8px;">이미지를 선택하세요</span>
          </div>
        </div>
        <div id="img-error"
     style="display:none;font-size:12px;color:#dc2626;margin-top:8px;">
</div>
        <input type="file" id="m-image" accept="image/*" style="margin-top:10px;width:100%" onchange="previewImg(this)">
        <p style="font-size:11px;color:var(--text-muted);margin-top:6px;"><i class="fas fa-info-circle"></i> 최대 1MB. 저장 버튼을 눌러야 서버에 적용됩니다.</p>
        <?php if(($ministry['image']??'')): ?>
        <button class="btn btn-danger btn-sm" style="margin-top:8px;width:100%" onclick="clearImage()"><i class="fas fa-trash"></i> 이미지 제거</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
.page-header-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.page-title-main{font-size:20px;font-weight:700;}
.header-actions{display:flex;gap:8px;}
.edit-layout{display:flex;gap:20px;align-items:flex-start;}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-grid-3{display:grid;grid-template-columns:1fr 1fr 120px;gap:16px;}
.hint{font-size:11px;color:var(--text-muted);font-weight:400;margin-left:4px;}
.img-upload-box{
  border:2px dashed var(--border);
  border-radius:8px;
  min-height:160px;
  padding:12px;
  background:#fafafa;
}
.img-ph{display:flex;flex-direction:column;align-items:center;padding:20px;}
</style>

<script>
let _pendingImg = null;
let _clearImg   = false;

function previewImg(input) {

  const file = input.files[0];

  if (!file) return;

  const previewWrap = document.getElementById('img-new-card');
  const previewImg  = document.getElementById('img-preview');

  const errorBox    = document.getElementById('img-error');

  errorBox.style.display = 'none';
  errorBox.innerHTML = '';

  if (file.size > 1024 * 1024) {

    _pendingImg = file;
    _clearImg   = false;

    previewWrap.style.display = 'none';

    errorBox.innerHTML =
      '<i class="fas fa-exclamation-circle"></i> 1MB 초과 이미지는 미리보기를 지원하지 않습니다.';
    errorBox.style.display = 'block';

    return;
  }

  _pendingImg = file;
  _clearImg   = false;

  const url = URL.createObjectURL(file);

  previewImg.src = url;

previewImg.style.display = 'block';

document.getElementById('img-new-card').style.display = 'block';

document.getElementById('img-ph').style.display = 'none';
}

function clearImage() {
  _pendingImg = null;
  _clearImg   = true;
  document.getElementById('img-preview').src = '';
  document.getElementById('img-preview').style.display = 'none';
  document.getElementById('img-ph').style.display = 'flex';
  document.getElementById('m-image').value = '';
  document.getElementById('img-new-card').style.display = 'none';
   document.getElementById('img-error').style.display = 'none';

document.getElementById('img-error').style.display = 'none';
}

async function saveMinistry() {
  const id  = document.getElementById('ministry-id').value;
  const key = document.getElementById('m-key').value.trim();
  const name= document.getElementById('m-name').value.trim();
  if (!key)  return toast('사역 키를 입력해주세요.', 'error');
  if (!name) return toast('이름을 입력해주세요.', 'error');

  const fd = new FormData();
  fd.append('id',                      id);
  fd.append('key',                     key);
  fd.append('name',                    name);
  fd.append('subtitle',                document.getElementById('m-subtitle').value);
  fd.append('title',                   document.getElementById('m-title').value);
  fd.append('description',             document.getElementById('m-desc').value);
  fd.append('points',                  document.getElementById('m-points').value);
  fd.append('notice_title',            document.getElementById('m-notice-title').value);
  fd.append('notice_description',      document.getElementById('m-notice-desc').value);
  fd.append('notice_button_label',     document.getElementById('m-notice-btn-label').value);
  fd.append('notice_button_href',      document.getElementById('m-notice-btn-href').value);
  fd.append('notice_button_external',  document.getElementById('m-notice-external').value);
  fd.append('cta_label',               document.getElementById('m-cta-label').value);
  fd.append('cta_href',                document.getElementById('m-cta-href').value);
  fd.append('cta_external',            document.getElementById('m-cta-external').value);
  fd.append('order',                   document.getElementById('m-order').value);
  fd.append('is_active',               document.getElementById('m-active').value);
  if (_pendingImg) fd.append('image', _pendingImg);

  const url = '/ministry/' + (id ? 'update' : 'create');
  const d   = await apiUpload(url, fd, '저장 중...');
  if (!d.success) return toast(d.message, 'error');
  toast(d.message);
  if (!id && d.data?.id) {
    setTimeout(() => { window.location.href = BASE_URL + '/ministry/edit?id=' + d.data.id; }, 800);
  }
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
