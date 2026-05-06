<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('cms.edit'); $canCreate=AuthMiddleware::hasPermission('cms.create'); $canDelete=AuthMiddleware::hasPermission('cms.delete'); ?>

<div style="display:grid;grid-template-columns:260px 1fr 1fr;gap:20px;align-items:start">

  <!-- Pages -->
  <div class="card">
    <div class="card-header" style="flex-direction:column;align-items:stretch">
      <div style="display:flex;align-items:center;justify-content:space-between"><h2>페이지</h2><?php if($canCreate): ?><button class="btn btn-primary btn-sm" onclick="openCreatePage()"><i class="fas fa-plus"></i></button><?php endif; ?></div>
    </div>
    <div id="page-list" style="padding:0">
      <?php foreach($pages as $pg): ?>
      <div data-page-id="<?= $pg['id'] ?>" onclick="loadSections(<?= $pg['id'] ?>)" style="display:flex;align-items:center;justify-content:space-between;padding:11px 16px;border-bottom:1px solid var(--border);cursor:pointer">
        <div><div class="fw-500 text-sm"><?= htmlspecialchars($pg['name']) ?></div><div style="font-size:11px;color:var(--text-muted)">/<?= htmlspecialchars($pg['slug']) ?></div></div>
        <div class="flex gap-8">
          <span class="badge <?= $pg['is_active']?'badge-green':'badge-gray' ?>"><?= $pg['is_active']?'활성':'비' ?></span>
          <?php if($canEdit): ?><button class="btn btn-warning btn-sm btn-icon" onclick="event.stopPropagation();editPage(<?= $pg['id'] ?>)"><i class="fas fa-pen"></i></button><?php endif; ?>
          <?php if($canDelete): ?><button class="btn btn-danger btn-sm btn-icon" onclick="event.stopPropagation();deletePage(<?= $pg['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Sections -->
  <div class="card">
    <div class="card-header"><h2 id="section-panel-title">페이지를 선택하세요</h2><?php if($canCreate): ?><button class="btn btn-primary btn-sm hidden" id="add-section-btn" onclick="openCreateSection()"><i class="fas fa-plus"></i></button><?php endif; ?></div>
    <div id="section-list" style="padding:12px"><p class="text-muted text-sm">페이지를 선택하면 섹션이 표시됩니다.</p></div>
  </div>

  <!-- Texts -->
  <div class="card">
    <div class="card-header"><h2 id="text-panel-title">섹션을 선택하세요</h2><?php if($canCreate): ?><button class="btn btn-primary btn-sm hidden" id="add-text-btn" onclick="openCreateText()"><i class="fas fa-plus"></i></button><?php endif; ?></div>
    <div id="text-list" style="padding:12px"><p class="text-muted text-sm">섹션을 선택하면 텍스트가 표시됩니다.</p></div>
  </div>
</div>

<!-- Page Modal -->
<div class="modal-overlay hidden" id="page-modal">
  <div class="modal modal-sm">
    <div class="modal-header"><h3 id="page-modal-title">페이지 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('page-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="page-id">
      <div class="form-group"><label class="form-label">페이지명 <span class="req">*</span></label><input type="text" id="pg-name" class="form-control"></div>
      <div class="form-group"><label class="form-label">슬러그 <span class="req">*</span></label><input type="text" id="pg-slug" class="form-control" placeholder="예: home"></div>
      <div class="form-group"><label class="form-label">설명</label><textarea id="pg-desc" class="form-control" rows="2"></textarea></div>
      <div class="form-group"><label class="form-label">상태</label><select id="pg-active" class="form-control"><option value="1">활성</option><option value="0">비활성</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('page-modal')">취소</button><button class="btn btn-primary" onclick="savePage()">저장</button></div>
  </div>
</div>

<!-- Section Modal -->
<div class="modal-overlay hidden" id="section-modal">
  <div class="modal modal-sm">
    <div class="modal-header"><h3 id="section-modal-title">섹션 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('section-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="section-id"><input type="hidden" id="section-page-id">
      <div class="form-group"><label class="form-label">섹션명 <span class="req">*</span></label><input type="text" id="sc-name" class="form-control"></div>
      <div class="form-group"><label class="form-label">슬러그 <span class="req">*</span></label><input type="text" id="sc-slug" class="form-control"></div>
      <div class="form-group"><label class="form-label">설명</label><textarea id="sc-desc" class="form-control" rows="2"></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">정렬</label><input type="number" id="sc-sort" class="form-control" value="0"></div>
        <div class="form-group"><label class="form-label">상태</label><select id="sc-active" class="form-control"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('section-modal')">취소</button><button class="btn btn-primary" onclick="saveSection()">저장</button></div>
  </div>
</div>

<!-- Text Modal -->
<div class="modal-overlay hidden" id="text-modal">
  <div class="modal modal-lg">
    <div class="modal-header"><h3 id="text-modal-title">텍스트 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('text-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="text-id"><input type="hidden" id="text-section-id">
      <div class="form-row">
        <div class="form-group"><label class="form-label">키 이름 <span class="req">*</span></label><input type="text" id="tx-key" class="form-control" placeholder="예: main_title"></div>
        <div class="form-group"><label class="form-label">타입</label><select id="tx-type" class="form-control"><option value="text">text (한 줄)</option><option value="textarea">textarea (여러 줄)</option><option value="html">html</option></select></div>
        <div class="form-group"><label class="form-label">정렬</label><input type="number" id="tx-sort" class="form-control" value="0"></div>
      </div>
      <div class="form-group"><label class="form-label">내용 (한글) <span class="req">*</span></label><textarea id="tx-ko" class="form-control" rows="4"></textarea></div>
      <div class="form-group"><label class="form-label">내용 (English)</label><textarea id="tx-en" class="form-control" rows="4"></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('text-modal')">취소</button><button class="btn btn-primary" onclick="saveText()">저장</button></div>
  </div>
</div>

<script>
let curPageId=null,curSectionId=null;

// ── Pages ──
function openCreatePage(){document.getElementById('page-modal-title').textContent='페이지 추가';document.getElementById('page-id').value='';['pg-name','pg-slug','pg-desc'].forEach(id=>document.getElementById(id).value='');document.getElementById('pg-active').value=1;openModal('page-modal');}
async function editPage(id){const d=await api('/cms/page-detail',{id});if(!d.success)return;const r=d.data;document.getElementById('page-modal-title').textContent='페이지 수정';document.getElementById('page-id').value=r.id;document.getElementById('pg-name').value=r.name;document.getElementById('pg-slug').value=r.slug;document.getElementById('pg-desc').value=r.description||'';document.getElementById('pg-active').value=r.is_active;openModal('page-modal');}
async function savePage(){const id=document.getElementById('page-id').value;const fd=new FormData();if(id)fd.append('id',id);fd.append('name',document.getElementById('pg-name').value);fd.append('slug',document.getElementById('pg-slug').value);fd.append('description',document.getElementById('pg-desc').value);fd.append('is_active',document.getElementById('pg-active').value);const d=await fetch(BASE_URL+(id?'/cms/page-update':'/cms/page-create'),{method:'POST',body:fd}).then(r=>r.json());if(d.success){toast(d.message);closeModal('page-modal');location.reload();}else toast(d.message,'error');}
async function deletePage(id){confirmAction('이 페이지와 모든 섹션/텍스트를 삭제하시겠습니까?',async()=>{const d=await api('/cms/page-delete',{id});if(d.success){toast('삭제됨');location.reload();}else toast(d.message,'error');});}

// ── Sections ──
async function loadSections(pageId){
  curPageId=pageId;curSectionId=null;
  document.querySelectorAll('[data-page-id]').forEach(el=>el.style.background='');
  document.querySelector(`[data-page-id="${pageId}"]`).style.background='var(--bg)';
  const d=await api('/cms/section-list',{page_id:pageId});
  if(!d.success)return;
  const title=document.querySelector(`[data-page-id="${pageId}"] .fw-500`).textContent;
  document.getElementById('section-panel-title').textContent=title+' - 섹션';
  document.getElementById('add-section-btn')?.classList.remove('hidden');
  const el=document.getElementById('section-list');
  el.innerHTML=d.data.sections.length?'':'<p class="text-muted text-sm">섹션 없음</p>';
  d.data.sections.forEach(s=>{
    const div=document.createElement('div');div.dataset.sectionId=s.id;div.onclick=()=>loadTexts(s.id);
    div.style.cssText='display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border:1px solid var(--border);border-radius:6px;margin-bottom:6px;cursor:pointer';
    div.innerHTML=`<div><div class="fw-500 text-sm">${s.name}</div><div style="font-size:11px;color:var(--text-muted)">${s.slug}</div></div><div class="flex gap-8"><span class="badge ${s.is_active?'badge-green':'badge-gray'}">${s.is_active?'활성':'비'}</span><button class="btn btn-warning btn-sm btn-icon" onclick="event.stopPropagation();editSection(${s.id})"><i class="fas fa-pen"></i></button><button class="btn btn-danger btn-sm btn-icon" onclick="event.stopPropagation();deleteSection(${s.id},this.closest('[data-section-id]'))"><i class="fas fa-trash"></i></button></div>`;
    el.appendChild(div);
  });
  document.getElementById('text-list').innerHTML='<p class="text-muted text-sm">섹션을 선택하세요.</p>';
  document.getElementById('text-panel-title').textContent='섹션을 선택하세요';
  document.getElementById('add-text-btn')?.classList.add('hidden');
}
function openCreateSection(){document.getElementById('section-modal-title').textContent='섹션 추가';document.getElementById('section-id').value='';document.getElementById('section-page-id').value=curPageId;['sc-name','sc-slug','sc-desc'].forEach(id=>document.getElementById(id).value='');document.getElementById('sc-sort').value=0;document.getElementById('sc-active').value=1;openModal('section-modal');}
async function editSection(id){const d=await api('/cms/section-detail',{id});if(!d.success)return;const r=d.data;document.getElementById('section-modal-title').textContent='섹션 수정';document.getElementById('section-id').value=r.id;document.getElementById('section-page-id').value=r.page_id;document.getElementById('sc-name').value=r.name;document.getElementById('sc-slug').value=r.slug;document.getElementById('sc-desc').value=r.description||'';document.getElementById('sc-sort').value=r.sort_order;document.getElementById('sc-active').value=r.is_active;openModal('section-modal');}
async function saveSection(){const id=document.getElementById('section-id').value;const fd=new FormData();if(id)fd.append('id',id);fd.append('page_id',document.getElementById('section-page-id').value);fd.append('name',document.getElementById('sc-name').value);fd.append('slug',document.getElementById('sc-slug').value);fd.append('description',document.getElementById('sc-desc').value);fd.append('sort_order',document.getElementById('sc-sort').value);fd.append('is_active',document.getElementById('sc-active').value);const d=await fetch(BASE_URL+(id?'/cms/section-update':'/cms/section-create'),{method:'POST',body:fd}).then(r=>r.json());if(d.success){toast(d.message);closeModal('section-modal');loadSections(curPageId);}else toast(d.message,'error');}
async function deleteSection(id,el){confirmAction('이 섹션과 모든 텍스트를 삭제하시겠습니까?',async()=>{const d=await api('/cms/section-delete',{id});if(d.success){toast('삭제됨');el?.remove();}else toast(d.message,'error');});}

// ── Texts ──
async function loadTexts(sectionId){
  curSectionId=sectionId;
  document.querySelectorAll('[data-section-id]').forEach(el=>el.style.background='');
  document.querySelector(`[data-section-id="${sectionId}"]`).style.background='var(--bg)';
  const d=await api('/cms/text-list',{section_id:sectionId});
  if(!d.success)return;
  const title=document.querySelector(`[data-section-id="${sectionId}"] .fw-500`).textContent;
  document.getElementById('text-panel-title').textContent=title+' - 텍스트';
  document.getElementById('add-text-btn')?.classList.remove('hidden');
  const el=document.getElementById('text-list');
  el.innerHTML=d.data.texts.length?'':'<p class="text-muted text-sm">텍스트 없음</p>';
  d.data.texts.forEach(t=>{
    const div=document.createElement('div');
    div.style.cssText='padding:10px 12px;border:1px solid var(--border);border-radius:6px;margin-bottom:6px';
    div.innerHTML=`<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px"><span class="fw-500 text-sm">${t.key_name}</span><div class="flex gap-8"><span class="badge badge-gray">${t.type}</span><button class="btn btn-warning btn-sm btn-icon" onclick="editText(${t.id})"><i class="fas fa-pen"></i></button><button class="btn btn-danger btn-sm btn-icon" onclick="deleteText(${t.id},this.closest('div[style]'))"><i class="fas fa-trash"></i></button></div></div><div style="font-size:12px;color:var(--text-muted);line-height:1.4">${t.content_ko?.slice(0,60)||'(비어있음)'}${t.content_ko?.length>60?'...':''}</div>`;
    el.appendChild(div);
  });
}
function openCreateText(){document.getElementById('text-modal-title').textContent='텍스트 추가';document.getElementById('text-id').value='';document.getElementById('text-section-id').value=curSectionId;['tx-key','tx-ko','tx-en'].forEach(id=>document.getElementById(id).value='');document.getElementById('tx-type').value='text';document.getElementById('tx-sort').value=0;openModal('text-modal');}
async function editText(id){const d=await api('/cms/text-detail',{id});if(!d.success)return;const r=d.data;document.getElementById('text-modal-title').textContent='텍스트 수정';document.getElementById('text-id').value=r.id;document.getElementById('text-section-id').value=r.section_id;document.getElementById('tx-key').value=r.key_name;document.getElementById('tx-ko').value=r.content_ko;document.getElementById('tx-en').value=r.content_en;document.getElementById('tx-type').value=r.type;document.getElementById('tx-sort').value=r.sort_order;openModal('text-modal');}
async function saveText(){const id=document.getElementById('text-id').value;const fd=new FormData();if(id)fd.append('id',id);fd.append('section_id',document.getElementById('text-section-id').value);fd.append('key_name',document.getElementById('tx-key').value);fd.append('content_ko',document.getElementById('tx-ko').value);fd.append('content_en',document.getElementById('tx-en').value);fd.append('type',document.getElementById('tx-type').value);fd.append('sort_order',document.getElementById('tx-sort').value);const d=await fetch(BASE_URL+(id?'/cms/text-update':'/cms/text-create'),{method:'POST',body:fd}).then(r=>r.json());if(d.success){toast(d.message);closeModal('text-modal');if(curSectionId)loadTexts(curSectionId);}else toast(d.message,'error');}
async function deleteText(id,el){confirmAction('이 텍스트를 삭제하시겠습니까?',async()=>{const d=await api('/cms/text-delete',{id});if(d.success){toast('삭제됨');el?.remove();}else toast(d.message,'error');});}

document.getElementById('pg-name')?.addEventListener('input',e=>{if(!document.getElementById('page-id').value)document.getElementById('pg-slug').value=e.target.value.toLowerCase().replace(/\s+/g,'-').replace(/[^a-z0-9-]/g,'');});
document.getElementById('sc-name')?.addEventListener('input',e=>{if(!document.getElementById('section-id').value)document.getElementById('sc-slug').value=e.target.value.toLowerCase().replace(/\s+/g,'-').replace(/[^a-z0-9-]/g,'');});
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
