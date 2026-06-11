<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=hasPerm('members.edit'); $canCreate=hasPerm('members.create'); $canDelete=hasPerm('members.delete'); ?>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-users" style="color:var(--primary)"></i> 교인·목회자 관리</h2>
    <?php if($canCreate): ?><button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i> 추가</button><?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead><tr>
        <th style="width:36px"></th>
        <th style="width:52px">사진</th>
        <th>이름</th>
        <th>카테고리</th>
        <th>직함</th>
        <th>직위</th>
        <th>역할</th>
        <th>태그</th>
        <th style="width:64px">상태</th>
        <th style="width:120px">관리</th>
      </tr></thead>
      <tbody id="member-tbody">
      <?php foreach($data['rows'] as $r): ?>
      <tr data-id="<?= $r['id'] ?>">
        <?php if($canEdit): ?><td class="drag-handle" style="cursor:grab;text-align:center"><i class="fas fa-grip-vertical" style="color:var(--text-muted)"></i></td><?php else: ?><td></td><?php endif; ?>
        <td>
          <a href="<?= BASE_URL ?>/members/view?id=<?= $r['id'] ?>">
          <?php if($r['picture']): ?>
            <img src="<?= BASE_URL.htmlspecialchars($r['picture']) ?>" class="img-thumb" style="border-radius:50%" alt="">
          <?php else: ?>
            <div style="width:40px;height:40px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:600"><?= mb_substr($r['name'],0,1) ?></div>
          <?php endif; ?>
          </a>
        </td>
        <td>
          <a href="<?= BASE_URL ?>/members/view?id=<?= $r['id'] ?>" style="color:var(--text);font-weight:600"><?= htmlspecialchars($r['name']) ?></a>
          <?php if(!empty($r['name_en'])): ?><div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($r['name_en']) ?></div><?php endif; ?>
        </td>
        <td class="text-sm"><?= htmlspecialchars($r['category']??'-') ?></td>
        <td class="text-sm"><?= htmlspecialchars($r['title']??'-') ?></td>
        <td class="text-sm"><?= htmlspecialchars($r['position']??'-') ?></td>
        <td class="text-sm text-muted"><?= htmlspecialchars($r['role']??'-') ?></td>
        <td>
          <?php if(!empty($r['tags'])): ?>
            <div style="display:flex;flex-wrap:wrap;gap:3px">
            <?php foreach(explode("\n",trim($r['tags'])) as $t): $t=trim($t); if($t==='')continue; ?>
              <span class="tag-badge"><?= htmlspecialchars($t) ?></span>
            <?php endforeach; ?>
            </div>
          <?php else: ?><span class="text-muted" style="font-size:12px">-</span><?php endif; ?>
        </td>
        <td><span class="badge <?= $r['is_active']?'badge-green':'badge-gray' ?>"><?= $r['is_active']?'활성':'비활성' ?></span></td>
        <td>
          <div class="flex gap-8">
            <a href="<?= BASE_URL ?>/members/view?id=<?= $r['id'] ?>" class="btn btn-ghost btn-sm btn-icon" title="상세보기"><i class="fas fa-eye"></i></a>
            <?php if($canEdit): ?><button class="btn btn-warning btn-sm btn-icon" onclick="openEdit(<?= $r['id'] ?>)" title="수정"><i class="fas fa-pen"></i></button><?php endif; ?>
            <?php if($canDelete): ?><button class="btn btn-danger btn-sm btn-icon" onclick="deleteRow(<?= $r['id'] ?>)" title="삭제"><i class="fas fa-trash"></i></button><?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
  <?php if($pagination['total_pages']>1): ?>
  <div class="card-body" style="border-top:1px solid var(--border)">
    <div class="pagination">
      <?php if($pagination['has_prev']): ?><a href="?page=<?= $pagination['current']-1 ?>">&laquo;</a><?php endif; ?>
      <?php for($p=$pagination['start_page'];$p<=$pagination['end_page'];$p++): ?><<?= $p===$pagination['current']?'span class="active"':'a href="?page='.$p.'"' ?>><?= $p ?></<?= $p===$pagination['current']?'span':'a' ?>><?php endfor; ?>
      <?php if($pagination['has_next']): ?><a href="?page=<?= $pagination['current']+1 ?>">&raquo;</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- ══ 등록/수정 모달 ══════════════════════════════════════════ -->
<div class="modal-overlay hidden" id="member-modal">
  <div class="modal" style="max-width:720px;max-height:92vh;overflow-y:auto">
    <div class="modal-header">
      <h3 id="modal-title">교인 추가</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('member-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="m-id">

      <!-- 사진 -->
      <div style="display:flex;gap:16px;align-items:flex-start;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
        <div style="flex-shrink:0;text-align:center">
          <div id="member-pic-preview" style="width:88px;height:88px;background:var(--bg);border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-muted);margin-bottom:8px;overflow:hidden"><i class="fas fa-user" style="font-size:30px"></i></div>
          <label class="btn btn-ghost btn-sm" style="font-size:11px;padding:4px 8px"><i class="fas fa-camera"></i> 사진 변경<input type="file" id="member-pic" accept="image/*" style="display:none" onchange="previewPic(this,'member-pic-preview')"></label>
        </div>
        <div style="flex:1">
          <div class="modal-section-title">기본 정보</div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">이름 <span class="req">*</span></label><input type="text" id="m-name" class="form-control" placeholder="홍길동"></div>
            <div class="form-group"><label class="form-label">영문 이름</label><input type="text" id="m-name-en" class="form-control" placeholder="Gildong Hong"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">이메일</label><input type="email" id="m-email" class="form-control" placeholder="example@church.com"></div>
            <div class="form-group"><label class="form-label">카테고리</label>
              <select id="m-category" class="form-control">
                <option value="간사">간사</option>
                <option value="목사">목사</option>
                <option value="부목사">부목사</option>
                <option value="전도사">전도사</option>
                <option value="장로">장로</option>
                <option value="권사">권사</option>
                <option value="집사">집사</option>
                <option value="기타">기타</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- 직책 정보 -->
      <div class="modal-section-title">직책 정보</div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">직함 <span class="form-hint">예: 담임목사</span></label><input type="text" id="m-title" class="form-control" placeholder="담임목사"></div>
        <div class="form-group"><label class="form-label">직위 <span class="form-hint">예: 목사</span></label><input type="text" id="m-pos" class="form-control" placeholder="목사"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">역할 <span class="form-hint">예: 담임</span></label><input type="text" id="m-role" class="form-control" placeholder="담임"></div>
        <div class="form-group"><label class="form-label">소속/부서 (position)</label><input type="text" id="m-pos2" class="form-control" placeholder="예배부(1부/2부 찬양인도)"></div>
      </div>

      <!-- 태그 (한글) -->
      <div class="modal-section-title">태그</div>
      <div class="form-group">
        <label class="form-label">태그 (한글) <span class="form-hint">각 태그를 추가 — \n 구분 저장</span></label>
        <div id="tag-list" class="tag-input-box"></div>
        <div style="display:flex;gap:6px;margin-top:6px">
          <input type="text" id="tag-input" class="form-control" placeholder="태그 입력 후 Enter 또는 추가" onkeydown="if(event.key==='Enter'){event.preventDefault();addTag('tag-input','m-tags','tag-list');}">
          <button type="button" class="btn btn-secondary btn-sm" onclick="addTag('tag-input','m-tags','tag-list')" style="white-space:nowrap"><i class="fas fa-plus"></i> 추가</button>
        </div>
        <input type="hidden" id="m-tags">
      </div>
      <div class="form-group">
        <label class="form-label">태그 (영문) <span class="form-hint">영문 태그 — \n 구분 저장</span></label>
        <div id="tag-en-list" class="tag-input-box"></div>
        <div style="display:flex;gap:6px;margin-top:6px">
          <input type="text" id="tag-en-input" class="form-control" placeholder="English tag + Enter or Add" onkeydown="if(event.key==='Enter'){event.preventDefault();addTag('tag-en-input','m-tags-en','tag-en-list');}">
          <button type="button" class="btn btn-secondary btn-sm" onclick="addTag('tag-en-input','m-tags-en','tag-en-list')" style="white-space:nowrap"><i class="fas fa-plus"></i> 추가</button>
        </div>
        <input type="hidden" id="m-tags-en">
      </div>

      <!-- 약력 -->
      <div class="modal-section-title">약력 및 설정</div>
      <div class="form-group"><label class="form-label">약력</label><textarea id="m-bio" class="form-control" rows="4" placeholder="약력을 입력하세요."></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">정렬 순서</label><input type="number" id="m-sort" class="form-control" value="0" min="0"></div>
        <div class="form-group"><label class="form-label">상태</label><select id="m-active" class="form-control"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('member-modal')">취소</button>
      <button class="btn btn-primary" id="member-save-btn" onclick="saveMember()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<style>
.tag-badge{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:20px;padding:2px 8px;font-size:11px;font-weight:500;white-space:nowrap;}
.tag-chip{display:inline-flex;align-items:center;gap:4px;background:#ede9fe;color:var(--primary);border:1px solid #c4b5fd;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:500;}
.tag-chip-en{background:#eff6ff;color:#2563eb;border-color:#bfdbfe;}
.tag-chip button{background:none;border:none;cursor:pointer;padding:0;line-height:1;opacity:.6;font-size:11px;}
.tag-chip button:hover{opacity:1;}
.tag-input-box{display:flex;flex-wrap:wrap;gap:5px;min-height:36px;padding:8px;border:1px solid var(--border);border-radius:6px;background:var(--bg);}
.modal-section-title{font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.06em;margin:18px 0 10px;padding-bottom:6px;border-bottom:1px solid var(--border);}
.form-hint{font-weight:400;font-size:11px;color:var(--text-muted);margin-left:4px;}
</style>

<script>
// ── 태그 빌더 (범용) ─────────────────────────────────────────
function escHtml(s){const d=document.createElement('div');d.textContent=s;return d.innerHTML;}

function renderTags(hiddenId, listId, isEn){
  const tags=(document.getElementById(hiddenId).value||'').split('\n').filter(t=>t.trim());
  const list=document.getElementById(listId);
  list.innerHTML='';
  tags.forEach((t,i)=>{
    const chip=document.createElement('span');
    chip.className='tag-chip'+(isEn?' tag-chip-en':'');
    chip.innerHTML=`${escHtml(t)}<button onclick="removeTag('${hiddenId}','${listId}',${i},${isEn?'true':'false'})" title="삭제"><i class="fas fa-times"></i></button>`;
    list.appendChild(chip);
  });
}
function addTag(inputId, hiddenId, listId){
  const inp=document.getElementById(inputId);
  const val=inp.value.trim(); if(!val) return;
  const isEn=listId.includes('-en-');
  const cur=document.getElementById(hiddenId).value||'';
  const tags=cur.split('\n').filter(t=>t.trim()); tags.push(val);
  document.getElementById(hiddenId).value=tags.join('\n');
  renderTags(hiddenId,listId,isEn);
  inp.value=''; inp.focus();
}
function removeTag(hiddenId, listId, idx, isEn){
  const tags=(document.getElementById(hiddenId).value||'').split('\n').filter(t=>t.trim());
  tags.splice(idx,1);
  document.getElementById(hiddenId).value=tags.join('\n');
  renderTags(hiddenId,listId,isEn);
}
function setTags(hiddenId, listId, raw, isEn){
  document.getElementById(hiddenId).value=(raw||'').split('\n').filter(t=>t.trim()).join('\n');
  renderTags(hiddenId,listId,isEn);
}

// ── 사진 미리보기 ─────────────────────────────────────────────
function previewPic(input,previewId){
  if(!input.files[0]) return;
  const r=new FileReader();
  r.onload=e=>{document.getElementById(previewId).innerHTML=`<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;};
  r.readAsDataURL(input.files[0]);
}

// ── 등록 모달 ─────────────────────────────────────────────────
function openCreate(){
  document.getElementById('m-id').value='';
  document.getElementById('modal-title').textContent='교인 추가';
  ['m-name','m-name-en','m-email','m-title','m-pos','m-pos2','m-role','m-bio'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('m-sort').value=0;
  document.getElementById('m-active').value=1;
  document.getElementById('m-category').value='간사';
  document.getElementById('member-pic-preview').innerHTML='<i class="fas fa-user" style="font-size:30px"></i>';
  document.getElementById('member-pic').value='';
  setTags('m-tags','tag-list','',false);
  setTags('m-tags-en','tag-en-list','',true);
  openModal('member-modal');
}

// ── 수정 모달 ─────────────────────────────────────────────────
async function openEdit(id){
  const d=await api('/members/detail',{id});
  if(!d.success) return toast(d.message,'error');
  const m=d.data;
  document.getElementById('m-id').value=m.id;
  document.getElementById('modal-title').textContent='교인 수정';
  document.getElementById('m-name').value=m.name||'';
  document.getElementById('m-name-en').value=m.name_en||'';
  document.getElementById('m-email').value=m.email||'';

  // 카테고리 — DB 값이 select 옵션에 없으면 동적으로 추가 후 선택
  const catSel=document.getElementById('m-category');
  const catVal=m.category||'간사';
  if(![...catSel.options].some(o=>o.value===catVal)){
    const opt=document.createElement('option');
    opt.value=catVal; opt.textContent=catVal;
    catSel.appendChild(opt);
  }
  catSel.value=catVal;
  document.getElementById('m-title').value=m.title||'';
  document.getElementById('m-pos').value=m.position||'';
  document.getElementById('m-pos2').value=m.position||'';  // position 필드 하나를 두 곳에 바인딩
  document.getElementById('m-role').value=m.role||'';
  document.getElementById('m-bio').value=m.biography||'';
  document.getElementById('m-sort').value=m.sort_order||0;
  document.getElementById('m-active').value=m.is_active??1;
  setTags('m-tags','tag-list',m.tags||'',false);
  setTags('m-tags-en','tag-en-list',m.tags_en||'',true);
  const prev=document.getElementById('member-pic-preview');
  prev.innerHTML=m.picture
    ?`<img src="${BASE_URL+m.picture}" style="width:100%;height:100%;object-fit:cover">`
    :'<i class="fas fa-user" style="font-size:30px"></i>';
  document.getElementById('member-pic').value='';
  openModal('member-modal');
}

// ── 저장 ────────────────────────────────────────────────────
async function saveMember(){
  const id=document.getElementById('m-id').value;
  const fd=new FormData();
  if(id) fd.append('id',id);
  fd.append('name',     document.getElementById('m-name').value);
  fd.append('name_en',  document.getElementById('m-name-en').value);
  fd.append('email',    document.getElementById('m-email').value);
  fd.append('category', document.getElementById('m-category').value);
  fd.append('title',    document.getElementById('m-title').value);
  fd.append('position', document.getElementById('m-pos').value);
  fd.append('role',     document.getElementById('m-role').value);
  fd.append('tags',     document.getElementById('m-tags').value);
  fd.append('tags_en',  document.getElementById('m-tags-en').value);
  fd.append('biography',document.getElementById('m-bio').value);
  fd.append('sort_order',document.getElementById('m-sort').value);
  fd.append('is_active', document.getElementById('m-active').value);
  const pic=document.getElementById('member-pic').files[0]; if(pic) fd.append('picture',pic);
  const btn=document.getElementById('member-save-btn'); btn.disabled=true;
  showSpinner('저장 중...');
  const endpoint=id?'/members/update':'/members/create';
  const d=await fetch(BASE_URL+endpoint,{method:'POST',body:fd}).then(r=>r.json());
  hideSpinner(); btn.disabled=false;
  if(d.success){toast(d.message);closeModal('member-modal');location.reload();}
  else toast(d.message,'error');
}

// ── 삭제 ────────────────────────────────────────────────────
async function deleteRow(id){
  confirmAction('이 교인을 삭제하시겠습니까?',async()=>{
    const d=await api('/members/delete',{id});
    if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}
    else toast(d.message,'error');
  });
}

// ── Sortable ─────────────────────────────────────────────────
function pageInit(){
  const tbody=document.getElementById('member-tbody');
  <?php if($canEdit): ?>
  if(tbody&&typeof Sortable!=='undefined'){
    new Sortable(tbody,{handle:'.drag-handle',animation:150,onEnd:async()=>{
      const orders=[...tbody.querySelectorAll('tr')].map((r,i)=>({id:parseInt(r.dataset.id),order:i+1}));
      const d=await api('/members/reorder',{orders:JSON.stringify(orders)});
      if(d.success) toast('순서가 저장되었습니다.');
    }});
  }
  <?php endif; ?>
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
