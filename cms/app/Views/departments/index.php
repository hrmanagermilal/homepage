<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="page-header-row">
  <h1 class="page-title-main">
    <i class="fas fa-sitemap"></i>
    <?php $typeLabel=['nextgen'=>'다음세대','ministry'=>'사역부서',''=>'전체 부서'][$_GET['type']??'']??'부서 관리'; ?>
    <?= $typeLabel ?>
  </h1>
  <div class="header-actions">
    <div class="type-filter">
      <a href="?type=" class="filter-btn <?= ($_GET['type']??'')=='' ?'active':'' ?>">전체</a>
      <a href="?type=nextgen" class="filter-btn <?= ($_GET['type']??'')=='nextgen'?'active':'' ?>">다음세대</a>
      <a href="?type=ministry" class="filter-btn <?= ($_GET['type']??'')=='ministry'?'active':'' ?>">사역부서</a>
    </div>
    <?php if(hasPerm('departments.create')): ?>
    <button class="btn btn-primary" onclick="openDeptModal()"><i class="fas fa-plus"></i> 부서 추가</button>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-list"></i> 부서 목록</h2>
    <span class="text-muted" style="font-size:12px"><i class="fas fa-info-circle"></i> 드래그로 순서 변경 가능</span>
  </div>
  <div id="dept-list" class="sortable-list">
    <?php foreach($departments as $d): ?>
    <div class="dept-item" data-id="<?= $d['id'] ?>">
      <div class="dept-drag"><i class="fas fa-grip-vertical"></i></div>
      <div class="dept-img">
        <?php if($d['image']): ?><img src="<?= BASE_URL.htmlspecialchars($d['image']) ?>" alt="<?= htmlspecialchars($d['name']) ?>" onerror="this.style.display='none'">
        <?php else: ?><div class="dept-img-ph"><i class="fas fa-users"></i></div><?php endif; ?>
      </div>
      <div class="dept-info">
        <div class="dept-name"><?= htmlspecialchars($d['name']) ?></div>
        <div class="dept-meta">
          <span class="badge <?= $d['department_type']==='nextgen'?'badge-blue':'badge-purple' ?>"><?= $d['department_type']==='nextgen'?'다음세대':'사역' ?></span>
          <?php if($d['worship_time']): ?><span class="dept-time"><i class="fas fa-clock"></i> <?= htmlspecialchars($d['worship_day']??'') ?> <?= htmlspecialchars($d['worship_time']) ?></span><?php endif; ?>
          <?php if($d['clergy_name']): ?><span class="dept-clergy"><i class="fas fa-user"></i> <?= htmlspecialchars($d['clergy_name']) ?></span><?php endif; ?>
        </div>
      </div>
      <div class="dept-status"><span class="badge <?= $d['is_active']?'badge-green':'badge-gray' ?>"><?= $d['is_active']?'활성':'비활성' ?></span></div>
      <div class="dept-actions">
        <a href="<?= BASE_URL ?>/departments/view?id=<?= $d['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-eye"></i></a>
        <?php if(hasPerm('departments.edit')): ?>
        <button class="btn btn-ghost btn-sm" onclick="editDept(<?= $d['id'] ?>)"><i class="fas fa-edit"></i> 수정</button>
        <?php endif; ?>
        <?php if(hasPerm('departments.delete')): ?>
        <button class="btn btn-danger btn-sm" onclick="deleteDept(<?= $d['id'] ?>)"><i class="fas fa-trash"></i></button>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if(empty($departments)): ?>
    <div class="empty-state"><i class="fas fa-sitemap fa-2x"></i><p>등록된 부서가 없습니다.</p></div>
    <?php endif; ?>
  </div>
</div>

<!-- 부서 모달 (큰 폼, 새 컬럼 포함) -->
<div id="dept-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:780px;max-height:90vh;overflow-y:auto">
    <div class="modal-header">
      <h3 id="dept-modal-title">부서 추가</h3>
      <button class="modal-close" onclick="closeModal('dept-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="dm-id">

      <!-- 이미지 섹션 -->
      <div class="img-section-block">
        <div class="img-section-item">
          <div class="img-section-header"><i class="fas fa-user-circle"></i> 부서 담당자 사진 <span class="img-section-sub">(최대 1MB)</span></div>
          <div id="dm-current-wrap" style="display:none">
            <div class="saved-img-label"><i class="fas fa-check-circle"></i> 현재 저장된 이미지</div>
            <img id="dm-current-img" src="" class="img-display">
          </div>
          <input type="file" id="dm-image" accept="image/*" onchange="previewDeptImg(this,'dm-img-preview','dm-new-wrap')">
          <div id="dm-new-wrap" class="new-img-preview-wrap" style="display:none">
            <div class="new-img-label"><i class="fas fa-upload"></i> 새 이미지 미리보기</div>
            <img id="dm-img-preview" src="" class="img-display">
          </div>
        </div>
        <div class="img-section-divider"></div>
        <div class="img-section-item">
          <div class="img-section-header"><i class="fas fa-panorama"></i> 히어로 이미지 <span class="img-section-sub">(최대 1MB)</span></div>
          <div id="dm-hero-current-wrap" style="display:none">
            <div class="saved-img-label"><i class="fas fa-check-circle"></i> 현재 저장된 이미지</div>
            <img id="dm-hero-current-img" src="" class="img-display">
          </div>
          <input type="file" id="dm-hero-image" accept="image/*" onchange="previewDeptImg(this,'dm-hero-preview','dm-hero-new-wrap')">
          <div id="dm-hero-new-wrap" class="new-img-preview-wrap" style="display:none">
            <div class="new-img-label"><i class="fas fa-upload"></i> 새 이미지 미리보기</div>
            <img id="dm-hero-preview" src="" class="img-display">
          </div>
        </div>
      </div>

      <div class="form-grid-3">
        <div class="form-group"><label class="form-label">부서 유형<span class="req">*</span></label>
          <select class="form-control" id="dm-type">
            <option value="nextgen">다음세대</option>
            <option value="ministry">사역부서</option>
          </select>
        </div>
        <div class="form-group col-span-2"><label class="form-label">부서명<span class="req">*</span></label>
          <input class="form-control" id="dm-name" placeholder="청년부"></div>
      </div>

      <div class="form-group"><label class="form-label">소개 (description)</label>
        <textarea class="form-control" id="dm-desc" rows="3" placeholder="부서 소개를 입력하세요."></textarea></div>
      <div class="form-group"><label class="form-label">헤딩 타이틀 (heading_title) — 페이지 상단 그라데이션 문구</label>
        <textarea class="form-control" id="dm-heading" rows="2" placeholder="Milight, Time to Shine..."></textarea></div>

      <div class="form-section-title">예배 정보</div>
      <div class="form-grid-3">
        <div class="form-group"><label class="form-label">연령대</label>
          <input class="form-control" id="dm-age" placeholder="19-29세"></div>
        <div class="form-group"><label class="form-label">예배 요일</label>
          <input class="form-control" id="dm-day" placeholder="주일"></div>
        <div class="form-group"><label class="form-label">예배 시간</label>
          <input class="form-control" id="dm-time" placeholder="오후 2시"></div>
      </div>
      <div class="form-group"><label class="form-label">예배 장소</label>
        <input class="form-control" id="dm-location" placeholder="밀알교회 1층 본당"></div>

      <div class="form-section-title">담당자 정보</div>
      <div class="form-grid-3">
        <div class="form-group"><label class="form-label">담당자 이름</label>
          <input class="form-control" id="dm-clergy-name" placeholder="신효성 목사"></div>
        <div class="form-group"><label class="form-label">직책</label>
          <input class="form-control" id="dm-clergy-pos" placeholder="담당 목사"></div>
        <div class="form-group"><label class="form-label">이메일</label>
          <input class="form-control" type="email" id="dm-pastor-email" placeholder="rev.shin@milalchurch.com"></div>
      </div>

      <div class="form-section-title">카카오톡 채널</div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">SNS 링크</label>
          <input class="form-control" id="dm-kakao-link" placeholder="https://pf.kakao.com/..."></div>
        <div class="form-group"><label class="form-label">SNS 버튼 Text 내용</label>
          <input class="form-control" id="dm-kakao-label" placeholder="카카오톡 채널 추가하기"></div>
      </div>

      <div class="form-section-title">공지 박스</div>
      <div class="form-group"><label class="form-label">공지 제목</label>
        <input class="form-control" id="dm-notice-title" placeholder="부서 소식"></div>
      <div class="form-group"><label class="form-label">공지 내용</label>
        <textarea class="form-control" id="dm-notice-desc" rows="2" placeholder="공지 내용을 입력하세요."></textarea></div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">버튼 텍스트</label>
          <input class="form-control" id="dm-notice-btn-label" placeholder="공지사항 다운로드"></div>
        <div class="form-group"><label class="form-label">버튼 링크</label>
          <input class="form-control" id="dm-notice-btn-href" placeholder="#"></div>
      </div>

      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">순서</label>
          <input class="form-control" type="number" id="dm-order" value="0" min="0"></div>
        <div class="form-group"><label class="form-label">상태</label>
          <select class="form-control" id="dm-active"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('dept-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveDept()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<style>
.page-header-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;}
.page-title-main{font-size:20px;font-weight:700;}
.header-actions{display:flex;gap:8px;align-items:center;}
.type-filter{display:flex;gap:4px;}
.filter-btn{padding:6px 14px;border-radius:20px;font-size:12px;font-weight:500;border:1px solid var(--border);background:var(--surface);color:var(--text-muted);cursor:pointer;transition:all .15s;}
.filter-btn.active,.filter-btn:hover{background:var(--primary);color:#fff;border-color:var(--primary);}
.dept-item{display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid var(--border);transition:background .1s;}
.dept-item:hover{background:#fafafa;}
.dept-drag{cursor:grab;color:var(--text-muted);font-size:16px;}
.dept-img{width:56px;height:56px;flex-shrink:0;border-radius:8px;overflow:hidden;background:#f3f4f6;display:flex;align-items:center;justify-content:center;}
.dept-img img{width:100%;height:100%;object-fit:cover;}
.dept-img-ph{color:#d1d5db;font-size:20px;}
.dept-info{flex:1;}
.dept-name{font-weight:600;font-size:14px;margin-bottom:6px;}
.dept-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.dept-time,.dept-clergy{font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:4px;}
.dept-status,.dept-actions{flex-shrink:0;}
.dept-actions{display:flex;gap:4px;}
.sortable-ghost{opacity:.4;background:#ede9fe;}
.empty-state{padding:48px;text-align:center;color:var(--text-muted);}
.empty-state i{display:block;margin-bottom:12px;opacity:.3;}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
.col-span-2{grid-column:span 2;}
.form-section-title{font-size:12px;font-weight:600;color:var(--primary);text-transform:uppercase;letter-spacing:.05em;margin:20px 0 10px;padding-bottom:6px;border-bottom:1px solid var(--border);}
.img-section-block{border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:16px;}
.img-section-item{padding:14px 16px;}
.img-section-divider{height:1px;background:var(--border);}
.img-section-header{font-size:13px;font-weight:600;color:var(--text);margin-bottom:10px;display:flex;align-items:center;gap:6px;}
.img-section-sub{font-weight:400;font-size:11px;color:var(--text-muted);}
.img-display{width:100%;max-width:300px;height:140px;object-fit:cover;border-radius:6px;border:1px solid var(--border);display:block;}
.saved-img-label{font-size:11px;color:#16a34a;font-weight:500;margin-bottom:6px;display:flex;align-items:center;gap:4px;}
.new-img-preview-wrap{margin-top:10px;padding:10px;background:#f9fafb;border:1px dashed var(--border);border-radius:6px;}
.new-img-label{font-size:11px;color:var(--text-muted);font-weight:500;margin-bottom:6px;display:flex;align-items:center;gap:4px;}
</style>

<script>
let _deptPendingImg = null;
let _deptPendingHeroImg = null;

function previewDeptImg(input, previewId, wrapId) {
  if(!input.files[0]) return;
  const url = URL.createObjectURL(input.files[0]);
  document.getElementById(previewId).src = url;
  document.getElementById(wrapId).style.display = 'block';
  if(input.id === 'dm-image') _deptPendingImg = input.files[0];
  else if(input.id === 'dm-hero-image') _deptPendingHeroImg = input.files[0];
}

function openDeptModal(data={}) {
  _deptPendingImg = null;
  _deptPendingHeroImg = null;
  document.getElementById('dm-id').value           = data.id||'';
  document.getElementById('dm-type').value         = data.department_type||'nextgen';
  document.getElementById('dm-name').value         = data.name||'';
  document.getElementById('dm-desc').value         = data.description||'';
  document.getElementById('dm-heading').value      = data.heading_title||'';
  document.getElementById('dm-age').value          = data.age_group||'';
  document.getElementById('dm-day').value          = data.worship_day||'';
  document.getElementById('dm-time').value         = data.worship_time||'';
  document.getElementById('dm-location').value     = data.worship_location||'';
  document.getElementById('dm-clergy-name').value  = data.clergy_name||'';
  document.getElementById('dm-clergy-pos').value   = data.clergy_position||'';
  document.getElementById('dm-pastor-email').value = data.pastor_email||'';
  document.getElementById('dm-kakao-link').value   = data.kakao_link||'';
  document.getElementById('dm-kakao-label').value  = data.kakao_label||'';
  document.getElementById('dm-notice-title').value = data.notice_title||'';
  document.getElementById('dm-notice-desc').value  = data.notice_description||'';
  document.getElementById('dm-notice-btn-label').value = data.notice_button_label||'';
  document.getElementById('dm-notice-btn-href').value  = data.notice_button_href||'';
  document.getElementById('dm-order').value        = data.order||0;
  document.getElementById('dm-active').value       = data.is_active??1;

  // 담당자 사진
  const curWrap = document.getElementById('dm-current-wrap');
  const curImg  = document.getElementById('dm-current-img');
  if(data.image){ curImg.src = BASE_URL+data.image; curWrap.style.display=''; }
  else { curWrap.style.display='none'; }
  document.getElementById('dm-new-wrap').style.display = 'none';
  document.getElementById('dm-img-preview').src = '';
  document.getElementById('dm-image').value = '';

  // 히어로 이미지
  const heroWrap = document.getElementById('dm-hero-current-wrap');
  const heroImg  = document.getElementById('dm-hero-current-img');
  if(data.hero_image){ heroImg.src = BASE_URL+data.hero_image; heroWrap.style.display=''; }
  else { heroWrap.style.display='none'; }
  document.getElementById('dm-hero-new-wrap').style.display = 'none';
  document.getElementById('dm-hero-preview').src = '';
  document.getElementById('dm-hero-image').value = '';

  document.getElementById('dept-modal-title').textContent = data.id?'부서 수정':'부서 추가';
  openModal('dept-modal');
}

async function editDept(id) {
  const d = await api('/departments/detail', {id});
  if(d.success) openDeptModal(d.data);
}

async function saveDept() {
  const id = document.getElementById('dm-id').value;
  const fd = new FormData();
  const fields = {
    id, department_type:'dm-type', name:'dm-name', description:'dm-desc', heading_title:'dm-heading',
    age_group:'dm-age', worship_day:'dm-day', worship_time:'dm-time', worship_location:'dm-location',
    clergy_name:'dm-clergy-name', clergy_position:'dm-clergy-pos', pastor_email:'dm-pastor-email',
    kakao_link:'dm-kakao-link', kakao_label:'dm-kakao-label',
    notice_title:'dm-notice-title', notice_description:'dm-notice-desc',
    notice_button_label:'dm-notice-btn-label', notice_button_href:'dm-notice-btn-href',
    order:'dm-order', is_active:'dm-active'
  };
  for(const[k,v] of Object.entries(fields)){
    fd.append(k, typeof v==='string'&&v.startsWith('dm-') ? document.getElementById(v).value : v);
  }
  if(_deptPendingImg) fd.append('image', _deptPendingImg);
  if(_deptPendingHeroImg) fd.append('hero_image', _deptPendingHeroImg);
  const d = await apiUpload('/departments/'+(id?'update':'create'), fd, '저장 중...');
  if(!d.success) return toast(d.message,'error');
  toast(d.message); closeModal('dept-modal'); location.reload();
}

async function deleteDept(id) {
  confirmAction('이 부서를 삭제하시겠습니까?', async()=>{
    const d=await api('/departments/delete',{id});
    if(!d.success) return toast(d.message,'error');
    toast(d.message); document.querySelector(`.dept-item[data-id="${id}"]`)?.remove();
  });
}

function pageInit() {
  const list=document.getElementById('dept-list');
  if(list && typeof Sortable!=='undefined') {
    Sortable.create(list,{handle:'.dept-drag',animation:150,ghostClass:'sortable-ghost',
      onEnd: async()=>{
        const orders=[...list.querySelectorAll('.dept-item')].map((el,i)=>({id:el.dataset.id,order:i}));
        await api('/departments/reorder',{orders:JSON.stringify(orders)});
      }
    });
  }
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
