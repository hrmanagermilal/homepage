<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="page-header-row">
  <div>
    <a href="<?= BASE_URL ?>/departments" class="back-link"><i class="fas fa-arrow-left"></i> 부서 목록</a>
    <h1 class="page-title-main" style="margin-top:6px"><i class="fas fa-sitemap"></i> <?= htmlspecialchars($dept['name']) ?></h1>
  </div>
  <?php if(hasPerm('departments.edit')): ?>
  <button class="btn btn-primary" onclick="editDept()"><i class="fas fa-edit"></i> 수정</button>
  <?php endif; ?>
</div>

<!-- 헤딩 카드 -->
<?php if($dept['heading_title']): ?>
<div class="heading-banner">
  <?php if($dept['image']): ?><img src="<?= htmlspecialchars($dept['image']) ?>" alt="" class="heading-bg"><?php endif; ?>
  <div class="heading-text"><?= nl2br(htmlspecialchars($dept['heading_title'])) ?></div>
</div>
<?php endif; ?>

<div class="detail-grid">
  <!-- 좌: 기본 정보 -->
  <div style="flex:1;display:flex;flex-direction:column;gap:20px">

    <div class="card">
      <div class="card-header"><h2><i class="fas fa-info-circle"></i> 기본 정보</h2></div>
      <div class="card-body">
        <div class="info-row"><span class="info-label">유형</span>
          <span class="badge <?= $dept['department_type']==='nextgen'?'badge-blue':'badge-purple' ?>"><?= $dept['department_type']==='nextgen'?'다음세대':'사역부서' ?></span>
        </div>
        <div class="info-row"><span class="info-label">상태</span>
          <span class="badge <?= $dept['is_active']?'badge-green':'badge-gray' ?>"><?= $dept['is_active']?'활성':'비활성' ?></span>
        </div>
        <?php if($dept['description']): ?>
        <div class="info-row"><span class="info-label">소개</span><p class="info-val"><?= nl2br(htmlspecialchars($dept['description'])) ?></p></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h2><i class="fas fa-clock"></i> 예배 정보</h2></div>
      <div class="card-body">
        <?php if($dept['age_group']): ?><div class="info-row"><span class="info-label">연령대</span><span><?= htmlspecialchars($dept['age_group']) ?></span></div><?php endif; ?>
        <?php if($dept['worship_day']||$dept['worship_time']): ?><div class="info-row"><span class="info-label">예배 시간</span><span><?= htmlspecialchars($dept['worship_day']??'') ?> <?= htmlspecialchars($dept['worship_time']??'') ?></span></div><?php endif; ?>
        <?php if($dept['worship_location']): ?><div class="info-row"><span class="info-label">예배 장소</span><span><?= htmlspecialchars($dept['worship_location']) ?></span></div><?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h2><i class="fas fa-user-tie"></i> 담당자</h2></div>
      <div class="card-body">
        <?php if($dept['clergy_name']): ?><div class="info-row"><span class="info-label">이름</span><span><?= htmlspecialchars($dept['clergy_name']) ?> <span class="text-muted"><?= htmlspecialchars($dept['clergy_position']??'') ?></span></span></div><?php endif; ?>
        <?php if($dept['pastor_email']): ?><div class="info-row"><span class="info-label">이메일</span><a href="mailto:<?= htmlspecialchars($dept['pastor_email']) ?>"><?= htmlspecialchars($dept['pastor_email']) ?></a></div><?php endif; ?>
        <?php if($dept['kakao_link']): ?>
        <div class="info-row"><span class="info-label">카카오</span>
          <a href="<?= htmlspecialchars($dept['kakao_link']) ?>" target="_blank" class="btn btn-warning btn-sm" style="background:#FEE500;color:#3C1E1E;">
            <i class="fas fa-comment"></i> <?= htmlspecialchars($dept['kakao_label']??'카카오톡 채널') ?>
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <?php if($dept['notice_title']||$dept['notice_description']): ?>
    <div class="card notice-box-card">
      <div class="card-header"><h2><i class="fas fa-bell"></i> 공지 박스</h2></div>
      <div class="card-body">
        <?php if($dept['notice_title']): ?><div class="notice-title"><?= htmlspecialchars($dept['notice_title']) ?></div><?php endif; ?>
        <?php if($dept['notice_description']): ?><div class="notice-desc"><?= nl2br(htmlspecialchars($dept['notice_description'])) ?></div><?php endif; ?>
        <?php if($dept['notice_button_label']&&$dept['notice_button_href']): ?>
        <a href="<?= htmlspecialchars($dept['notice_button_href']) ?>" class="btn btn-primary btn-sm" style="margin-top:10px"><?= htmlspecialchars($dept['notice_button_label']) ?></a>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>

  <!-- 우: 부서 공지 + 이미지 -->
  <div style="width:360px;flex-shrink:0;display:flex;flex-direction:column;gap:20px">

    <?php if($dept['image']): ?>
    <div class="card">
      <div class="card-header"><h2><i class="fas fa-image"></i> 대표 이미지</h2></div>
      <div class="card-body p-0">
        <img src="<?= BASE_URL.htmlspecialchars($dept['image']) ?>" alt="<?= htmlspecialchars($dept['name']) ?>" style="width:100%;display:block;border-radius:0 0 8px 8px;max-height:200px;object-fit:cover;">
      </div>
    </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header">
        <h2><i class="fas fa-bullhorn"></i> 부서 공지</h2>
        <?php if(hasPerm('departments.create')): ?>
        <button class="btn btn-primary btn-sm" onclick="openAnnModal()"><i class="fas fa-plus"></i> 추가</button>
        <?php endif; ?>
      </div>
      <div id="ann-list">
        <?php foreach($annData['rows'] as $ann): ?>
        <div class="ann-item" data-id="<?= $ann['id'] ?>">
          <div class="ann-body">
            <div class="ann-title"><?= htmlspecialchars($ann['title']) ?></div>
            <div class="ann-date"><?= date('Y-m-d', strtotime($ann['created_at'])) ?></div>
            <?php if($ann['link']): ?><a href="<?= htmlspecialchars($ann['link']) ?>" target="_blank" class="ann-link"><i class="fas fa-link"></i> 링크</a><?php endif; ?>
          </div>
          <div class="ann-actions">
            <?php if(hasPerm('departments.edit')): ?><button class="btn btn-ghost btn-sm" onclick="editAnn(<?= $ann['id'] ?>)"><i class="fas fa-edit"></i></button><?php endif; ?>
            <?php if(hasPerm('departments.delete')): ?><button class="btn btn-danger btn-sm" onclick="deleteAnn(<?= $ann['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($annData['rows'])): ?><div class="empty-ann">등록된 공지가 없습니다.</div><?php endif; ?>
      </div>
    </div>

  </div>
</div>

<!-- 부서 수정 모달 (전체 필드) -->
<div id="dept-edit-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:780px;max-height:90vh;overflow-y:auto">
    <div class="modal-header"><h3>부서 수정</h3>
      <button class="modal-close" onclick="closeModal('dept-edit-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="em-id" value="<?= $dept['id'] ?>">
      <div class="form-group"><label class="form-label">대표 이미지 (최대 1MB)</label>
        <div class="img-preview-row">
          <div class="img-thumb-box">
            <img id="em-img-preview" src="<?= ($dept['image']??'') ? BASE_URL.htmlspecialchars($dept['image']) : '' ?>" style="<?= ($dept['image']??'')?'':'display:none' ?>;max-height:80px;object-fit:contain;border-radius:4px;">
            <div id="em-img-ph" class="img-ph-sm" style="<?= ($dept['image']??'')?'display:none':'' ?>"><i class="fas fa-image"></i></div>
          </div>
          <input type="file" id="em-image" accept="image/*" onchange="previewEditImg(this)">
        </div>
      </div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">부서 유형</label>
          <select class="form-control" id="em-type"><option value="nextgen" <?= $dept['department_type']==='nextgen'?'selected':'' ?>>다음세대</option><option value="ministry" <?= $dept['department_type']==='ministry'?'selected':'' ?>>사역부서</option></select></div>
        <div class="form-group"><label class="form-label">부서명<span class="req">*</span></label>
          <input class="form-control" id="em-name" value="<?= htmlspecialchars($dept['name']) ?>"></div>
      </div>
      <div class="form-group"><label class="form-label">소개</label>
        <textarea class="form-control" id="em-desc" rows="3"><?= htmlspecialchars($dept['description']??'') ?></textarea></div>
      <div class="form-group"><label class="form-label">헤딩 타이틀</label>
        <textarea class="form-control" id="em-heading" rows="2"><?= htmlspecialchars($dept['heading_title']??'') ?></textarea></div>
      <div class="form-section-title">예배 정보</div>
      <div class="form-grid-3">
        <div class="form-group"><label class="form-label">연령대</label><input class="form-control" id="em-age" value="<?= htmlspecialchars($dept['age_group']??'') ?>"></div>
        <div class="form-group"><label class="form-label">예배 요일</label><input class="form-control" id="em-day" value="<?= htmlspecialchars($dept['worship_day']??'') ?>"></div>
        <div class="form-group"><label class="form-label">예배 시간</label><input class="form-control" id="em-time" value="<?= htmlspecialchars($dept['worship_time']??'') ?>"></div>
      </div>
      <div class="form-group"><label class="form-label">예배 장소</label><input class="form-control" id="em-location" value="<?= htmlspecialchars($dept['worship_location']??'') ?>"></div>
      <div class="form-section-title">담당자</div>
      <div class="form-grid-3">
        <div class="form-group"><label class="form-label">이름</label><input class="form-control" id="em-clergy-name" value="<?= htmlspecialchars($dept['clergy_name']??'') ?>"></div>
        <div class="form-group"><label class="form-label">직책</label><input class="form-control" id="em-clergy-pos" value="<?= htmlspecialchars($dept['clergy_position']??'') ?>"></div>
        <div class="form-group"><label class="form-label">이메일</label><input class="form-control" id="em-pastor-email" value="<?= htmlspecialchars($dept['pastor_email']??'') ?>"></div>
      </div>
      <div class="form-section-title">카카오톡 채널</div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">SNS 링크</label><input class="form-control" id="em-kakao-link" value="<?= htmlspecialchars($dept['kakao_link']??'') ?>"></div>
        <div class="form-group"><label class="form-label">SNS 버튼 Text 내용</label><input class="form-control" id="em-kakao-label" value="<?= htmlspecialchars($dept['kakao_label']??'') ?>"></div>
      </div>
      <div class="form-section-title">공지 박스</div>
      <div class="form-group"><label class="form-label">공지 제목</label><input class="form-control" id="em-notice-title" value="<?= htmlspecialchars($dept['notice_title']??'') ?>"></div>
      <div class="form-group"><label class="form-label">공지 내용</label><textarea class="form-control" id="em-notice-desc" rows="2"><?= htmlspecialchars($dept['notice_description']??'') ?></textarea></div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">버튼 텍스트</label><input class="form-control" id="em-notice-btn-label" value="<?= htmlspecialchars($dept['notice_button_label']??'') ?>"></div>
        <div class="form-group"><label class="form-label">버튼 링크</label><input class="form-control" id="em-notice-btn-href" value="<?= htmlspecialchars($dept['notice_button_href']??'') ?>"></div>
      </div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">순서</label><input class="form-control" type="number" id="em-order" value="<?= $dept['order']??0 ?>" min="0"></div>
        <div class="form-group"><label class="form-label">상태</label>
          <select class="form-control" id="em-active"><option value="1" <?= $dept['is_active']?'selected':'' ?>>활성</option><option value="0" <?= !$dept['is_active']?'selected':'' ?>>비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('dept-edit-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveEdit()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<!-- 공지 모달 -->
<div id="ann-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:520px">
    <div class="modal-header"><h3 id="ann-modal-title">공지 추가</h3>
      <button class="modal-close" onclick="closeModal('ann-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="am-id">
      <div class="form-group"><label class="form-label">제목<span class="req">*</span></label><input class="form-control" id="am-title"></div>
      <div class="form-group"><label class="form-label">내용<span class="req">*</span></label><textarea class="form-control" id="am-content" rows="4"></textarea></div>
      <div class="form-group"><label class="form-label">링크 URL</label><input class="form-control" id="am-link" placeholder="https://..."></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('ann-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveAnn()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<style>
.page-header-row{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;}
.page-title-main{font-size:20px;font-weight:700;}
.back-link{font-size:13px;color:var(--text-muted);display:inline-flex;align-items:center;gap:6px;}
.back-link:hover{color:var(--primary);}
.heading-banner{position:relative;border-radius:12px;overflow:hidden;margin-bottom:20px;min-height:120px;display:flex;align-items:center;background:linear-gradient(135deg,#4f46e5,#7c3aed);}
.heading-bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.3;}
.heading-text{position:relative;padding:28px 32px;color:#fff;font-size:18px;font-weight:600;line-height:1.6;text-shadow:0 1px 3px rgba(0,0,0,.3);}
.detail-grid{display:flex;gap:20px;align-items:flex-start;}
.info-row{display:flex;align-items:flex-start;gap:16px;padding:10px 0;border-bottom:1px solid #f3f4f6;}
.info-row:last-child{border-bottom:none;}
.info-label{font-size:12px;font-weight:600;color:var(--text-muted);width:70px;flex-shrink:0;padding-top:2px;}
.info-val{flex:1;font-size:13px;line-height:1.6;}
.text-muted{color:var(--text-muted);}
.notice-box-card .notice-title{font-weight:600;margin-bottom:6px;}
.notice-box-card .notice-desc{font-size:13px;color:var(--text-muted);line-height:1.6;}
.ann-item{display:flex;align-items:center;gap:10px;padding:12px 16px;border-bottom:1px solid var(--border);}
.ann-body{flex:1;}
.ann-title{font-weight:500;font-size:13px;}
.ann-date{font-size:11px;color:var(--text-muted);margin-top:2px;}
.ann-link{font-size:11px;color:var(--primary);}
.ann-actions{display:flex;gap:4px;}
.empty-ann{padding:24px;text-align:center;color:var(--text-muted);font-size:13px;}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
.form-section-title{font-size:12px;font-weight:600;color:var(--primary);text-transform:uppercase;letter-spacing:.05em;margin:20px 0 10px;padding-bottom:6px;border-bottom:1px solid var(--border);}
.img-preview-row{display:flex;align-items:center;gap:12px;}
.img-thumb-box{border:1px solid var(--border);border-radius:6px;width:90px;height:70px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#fafafa;}
.img-ph-sm{color:var(--text-muted);font-size:20px;opacity:.4;}
.p-0{padding:0;}
</style>

<script>
const DEPT_ID = <?= $dept['id'] ?>;
let _editPendingImg = null;

function previewEditImg(input) {
  if(!input.files[0]) return; _editPendingImg=input.files[0];
  const url=URL.createObjectURL(input.files[0]);
  document.getElementById('em-img-preview').src=url; document.getElementById('em-img-preview').style.display='block';
  document.getElementById('em-img-ph').style.display='none';
}
function editDept() { openModal('dept-edit-modal'); }
async function saveEdit() {
  const fd=new FormData();
  const map={'id':'em-id','department_type':'em-type','name':'em-name','description':'em-desc','heading_title':'em-heading',
             'age_group':'em-age','worship_day':'em-day','worship_time':'em-time','worship_location':'em-location',
             'clergy_name':'em-clergy-name','clergy_position':'em-clergy-pos','pastor_email':'em-pastor-email',
             'kakao_link':'em-kakao-link','kakao_label':'em-kakao-label',
             'notice_title':'em-notice-title','notice_description':'em-notice-desc',
             'notice_button_label':'em-notice-btn-label','notice_button_href':'em-notice-btn-href',
             'order':'em-order','is_active':'em-active'};
  for(const[k,v] of Object.entries(map)) fd.append(k, document.getElementById(v).value);
  if(_editPendingImg) fd.append('image',_editPendingImg);
  const d=await apiUpload('/departments/update',fd,'저장 중...');
  if(!d.success) return toast(d.message,'error');
  toast(d.message); closeModal('dept-edit-modal');
  setTimeout(()=>location.reload(),800);
}

// 공지 CRUD
function openAnnModal(data={}) {
  document.getElementById('am-id').value      = data.id||'';
  document.getElementById('am-title').value   = data.title||'';
  document.getElementById('am-content').value = data.content||'';
  document.getElementById('am-link').value    = data.link||'';
  document.getElementById('ann-modal-title').textContent = data.id?'공지 수정':'공지 추가';
  openModal('ann-modal');
}
async function editAnn(id) {
  const d=await api('/departments/announcement-detail',{id});
  if(d.success) openAnnModal(d.data);
}
async function saveAnn() {
  const id=document.getElementById('am-id').value;
  const d=await api('/departments/'+(id?'announcement-update':'announcement-create'),{
    id, dept_id:DEPT_ID, title:document.getElementById('am-title').value,
    content:document.getElementById('am-content').value, link:document.getElementById('am-link').value,
  });
  if(!d.success) return toast(d.message,'error');
  toast(d.message); closeModal('ann-modal'); location.reload();
}
async function deleteAnn(id) {
  confirmAction('이 공지를 삭제하시겠습니까?', async()=>{
    const d=await api('/departments/announcement-delete',{id});
    if(!d.success) return toast(d.message,'error');
    toast(d.message); document.querySelector(`.ann-item[data-id="${id}"]`)?.remove();
  });
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
