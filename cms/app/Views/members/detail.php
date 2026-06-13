<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=hasPerm('members.edit'); $canDelete=hasPerm('members.delete'); ?>

<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/members" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
</div>

<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start">

  <!-- ── 좌: 프로필 카드 ──────────────────────────────── -->
  <div class="card">
    <div class="card-body" style="text-align:center;padding:28px 20px">
      <?php if($member['picture']): ?>
        <img src="<?= BASE_URL.htmlspecialchars($member['picture']) ?>" style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:3px solid var(--border);margin-bottom:14px" alt="">
      <?php else: ?>
        <div style="width:110px;height:110px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:38px;color:#fff;font-weight:700"><?= mb_substr($member['name'],0,1) ?></div>
      <?php endif; ?>

      <?php if(!empty($member['category'])): ?>
        <div style="font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px"><?= htmlspecialchars($member['category']) ?></div>
      <?php endif; ?>
      <h2 style="font-size:20px;font-weight:700;margin-bottom:2px"><?= htmlspecialchars($member['name']) ?></h2>
      <?php if(!empty($member['name_en'])): ?>
        <div style="font-size:13px;color:var(--text-muted);margin-bottom:4px"><?= htmlspecialchars($member['name_en']) ?></div>
      <?php endif; ?>
      <?php if(!empty($member['title'])): ?>
        <div style="font-size:14px;color:var(--primary);font-weight:500;margin-bottom:2px"><?= htmlspecialchars($member['title']) ?></div>
      <?php endif; ?>
      <?php if(!empty($member['position'])): ?>
        <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px"><?= htmlspecialchars($member['position']) ?></div>
      <?php endif; ?>

      <!-- 태그 (한글) -->
      <?php if(!empty($member['tags'])): ?>
      <div style="display:flex;flex-wrap:wrap;gap:5px;justify-content:center;margin:10px 0 4px">
        <?php foreach(explode("\n",trim($member['tags'])) as $t): $t=trim($t); if($t==='')continue; ?>
          <span class="tag-badge-ko"><?= htmlspecialchars($t) ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <!-- 태그 (영문) -->
      <?php if(!empty($member['tags_en'])): ?>
      <div style="display:flex;flex-wrap:wrap;gap:5px;justify-content:center;margin:4px 0 8px">
        <?php foreach(explode("\n",trim($member['tags_en'])) as $t): $t=trim($t); if($t==='')continue; ?>
          <span class="tag-badge-en"><?= htmlspecialchars($t) ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div style="margin-top:10px"><span class="badge <?= $member['is_active']?'badge-green':'badge-gray' ?>"><?= $member['is_active']?'활성':'비활성' ?></span></div>
      <?php if(!empty($member['email'])): ?>
        <div style="margin-top:10px;font-size:13px;color:var(--text-muted)"><i class="fas fa-envelope"></i> <?= htmlspecialchars($member['email']) ?></div>
      <?php endif; ?>
    </div>
    <div style="padding:0 16px 16px;display:flex;flex-direction:column;gap:8px">
      <?php if($canEdit): ?><button class="btn btn-warning" onclick="openEdit()"><i class="fas fa-pen"></i> 수정</button><?php endif; ?>
      <?php if($canDelete): ?><button class="btn btn-danger" onclick="deleteMember(<?= $member['id'] ?>)"><i class="fas fa-trash"></i> 삭제</button><?php endif; ?>
    </div>
  </div>

  <!-- ── 우: 상세 정보 ───────────────────────────────── -->
  <div class="card">
    <div class="card-header"><h2>상세 정보</h2></div>
    <div class="card-body" style="padding:0">
      <table class="detail-table">
        <tbody>
        <?php
        $rows=[
          ['이름 (한글)',   $member['name']],
          ['이름 (영문)',   $member['name_en']??''],
          ['이메일',        $member['email']??''],
          ['카테고리',      $member['category']??''],
          ['직함',          $member['title']??''],
          ['직위/구분',     $member['position']??''],
          ['역할',          $member['role']??''],
          ['정렬 순서',     $member['sort_order']],
          ['상태',          $member['is_active'] ? '활성' : '비활성'],
          ['등록일',        date('Y년 m월 d일 H:i', strtotime($member['created_at']))],
          ['최종 수정',     $member['updated_at'] ? date('Y년 m월 d일 H:i', strtotime($member['updated_at'])) : '-'],
        ];
        foreach($rows as [$label,$val]):
          if((string)$val==='') continue; ?>
        <tr>
          <th><?= $label ?></th>
          <td><?= htmlspecialchars((string)$val) ?></td>
        </tr>
        <?php endforeach; ?>

        <!-- 태그 (한글) -->
        <tr>
          <th>태그 (한글)</th>
          <td>
            <?php if(!empty($member['tags'])): ?>
              <div style="display:flex;flex-wrap:wrap;gap:5px">
                <?php foreach(explode("\n",trim($member['tags'])) as $t): $t=trim($t); if($t==='')continue; ?>
                  <span class="tag-badge-ko"><?= htmlspecialchars($t) ?></span>
                <?php endforeach; ?>
              </div>
            <?php else: ?><span style="color:var(--text-muted)">-</span><?php endif; ?>
          </td>
        </tr>

        <!-- 태그 (영문) -->
        <tr>
          <th>태그 (영문)</th>
          <td>
            <?php if(!empty($member['tags_en'])): ?>
              <div style="display:flex;flex-wrap:wrap;gap:5px">
                <?php foreach(explode("\n",trim($member['tags_en'])) as $t): $t=trim($t); if($t==='')continue; ?>
                  <span class="tag-badge-en"><?= htmlspecialchars($t) ?></span>
                <?php endforeach; ?>
              </div>
            <?php else: ?><span style="color:var(--text-muted)">-</span><?php endif; ?>
          </td>
        </tr>
        </tbody>
      </table>

      <?php if(!empty($member['biography'])): ?>
      <div style="padding:20px">
        <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">약력</div>
        <div style="font-size:14px;line-height:1.85;white-space:pre-wrap;background:var(--bg);border-radius:8px;padding:16px"><?= htmlspecialchars($member['biography']) ?></div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ══ 수정 모달 ════════════════════════════════════════════ -->
<div class="modal-overlay hidden" id="member-modal">
  <div class="modal" style="max-width:720px;max-height:92vh;overflow-y:auto">
    <div class="modal-header">
      <h3>교인 수정</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('member-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">

      <!-- 사진 -->
      <div style="display:flex;gap:16px;align-items:flex-start;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
        <div style="flex-shrink:0;text-align:center">
          <div id="pic-preview" style="width:88px;height:88px;border-radius:50%;overflow:hidden;background:var(--bg);border:2px solid var(--border);display:flex;align-items:center;justify-content:center;margin-bottom:8px">
            <?php if($member['picture']): ?><img src="<?= BASE_URL.htmlspecialchars($member['picture']) ?>" style="width:100%;height:100%;object-fit:cover"><?php else: ?><i class="fas fa-user" style="font-size:30px;color:var(--text-muted)"></i><?php endif; ?>
          </div>
          <label class="btn btn-ghost btn-sm" style="font-size:11px;padding:4px 8px"><i class="fas fa-camera"></i> 사진 변경<input type="file" id="m-pic" accept="image/*" style="display:none" onchange="previewPic(this,'pic-preview')"></label>
        </div>
        <div style="flex:1">
          <div class="modal-section-title" style="margin-top:0">기본 정보</div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">이름 <span class="req">*</span></label><input type="text" id="m-name" class="form-control" value="<?= htmlspecialchars($member['name']) ?>"></div>
            <div class="form-group"><label class="form-label">영문 이름</label><input type="text" id="m-name-en" class="form-control" value="<?= htmlspecialchars($member['name_en']??'') ?>"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">이메일</label><input type="email" id="m-email" class="form-control" value="<?= htmlspecialchars($member['email']??'') ?>"></div>
            <div class="form-group"><label class="form-label">카테고리</label>
              <select id="m-category" class="form-control">
                <?php
                $cats=['간사','목사','부목사','전도사','장로','권사','집사','기타'];
                $currentCat=$member['category']??'';
                // DB 값이 목록에 없으면 맨 위에 추가
                if($currentCat!==''&&!in_array($currentCat,$cats,true)) array_unshift($cats,$currentCat);
                foreach($cats as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $currentCat===$cat?'selected':'' ?>><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- 직책 정보 -->
      <div class="modal-section-title">직책 정보</div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">직함 <span class="form-hint">예: 담임목사</span></label><input type="text" id="m-title" class="form-control" value="<?= htmlspecialchars($member['title']??'') ?>"></div>
        <div class="form-group"><label class="form-label">직위 (position) <span class="form-hint">예: 목사</span></label><input type="text" id="m-pos" class="form-control" value="<?= htmlspecialchars($member['position']??'') ?>"></div>
      </div>
      <div class="form-group"><label class="form-label">역할 (role) <span class="form-hint">예: 담임</span></label><input type="text" id="m-role" class="form-control" value="<?= htmlspecialchars($member['role']??'') ?>"></div>

      <!-- 태그 -->
      <div class="modal-section-title">태그</div>
      <div class="form-group">
        <label class="form-label">태그 (한글) <span class="form-hint">\n 구분 저장</span></label>
        <div id="tag-list" class="tag-input-box"></div>
        <div style="display:flex;gap:6px;margin-top:6px">
          <input type="text" id="tag-input" class="form-control" placeholder="태그 입력 후 Enter 또는 추가" onkeydown="if(event.key==='Enter'){event.preventDefault();addTag('tag-input','m-tags','tag-list',false);}">
          <button type="button" class="btn btn-secondary btn-sm" onclick="addTag('tag-input','m-tags','tag-list',false)" style="white-space:nowrap"><i class="fas fa-plus"></i> 추가</button>
        </div>
        <input type="hidden" id="m-tags">
      </div>
      <div class="form-group">
        <label class="form-label">태그 (영문) <span class="form-hint">\n 구분 저장</span></label>
        <div id="tag-en-list" class="tag-input-box"></div>
        <div style="display:flex;gap:6px;margin-top:6px">
          <input type="text" id="tag-en-input" class="form-control" placeholder="English tag + Enter or Add" onkeydown="if(event.key==='Enter'){event.preventDefault();addTag('tag-en-input','m-tags-en','tag-en-list',true);}">
          <button type="button" class="btn btn-secondary btn-sm" onclick="addTag('tag-en-input','m-tags-en','tag-en-list',true)" style="white-space:nowrap"><i class="fas fa-plus"></i> 추가</button>
        </div>
        <input type="hidden" id="m-tags-en">
      </div>

      <!-- 약력 + 설정 -->
      <div class="modal-section-title">약력 및 설정</div>
      <div class="form-group"><label class="form-label">약력</label><textarea id="m-bio" class="form-control" rows="5"><?= htmlspecialchars($member['biography']??'') ?></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">정렬 순서</label><input type="number" id="m-sort" class="form-control" value="<?= $member['sort_order'] ?>" min="0"></div>
        <div class="form-group"><label class="form-label">상태</label><select id="m-active" class="form-control"><option value="1" <?= $member['is_active']?'selected':'' ?>>활성</option><option value="0" <?= !$member['is_active']?'selected':'' ?>>비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('member-modal')">취소</button>
      <button class="btn btn-primary" id="m-save-btn" onclick="saveMember()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<style>
.tag-badge-ko{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:500;}
.tag-badge-en{background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:500;}
.tag-chip{display:inline-flex;align-items:center;gap:4px;background:#ede9fe;color:var(--primary);border:1px solid #c4b5fd;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:500;}
.tag-chip.en{background:#eff6ff;color:#2563eb;border-color:#bfdbfe;}
.tag-chip button{background:none;border:none;cursor:pointer;padding:0;line-height:1;opacity:.6;font-size:11px;}
.tag-chip button:hover{opacity:1;}
.tag-input-box{display:flex;flex-wrap:wrap;gap:5px;min-height:36px;padding:8px;border:1px solid var(--border);border-radius:6px;background:var(--bg);}
.modal-section-title{font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.06em;margin:18px 0 10px;padding-bottom:6px;border-bottom:1px solid var(--border);}
.form-hint{font-weight:400;font-size:11px;color:var(--text-muted);margin-left:4px;}
.detail-table{width:100%;border-collapse:collapse;font-size:14px;}
.detail-table th{padding:10px 16px;font-weight:500;color:var(--text-muted);width:130px;white-space:nowrap;border-bottom:1px solid var(--border);background:var(--bg);text-align:left;vertical-align:top;}
.detail-table td{padding:10px 16px;border-bottom:1px solid var(--border);vertical-align:top;}
</style>

<script>
function escHtml(s){const d=document.createElement('div');d.textContent=s;return d.innerHTML;}

function renderTags(hiddenId, listId, isEn){
  const tags=(document.getElementById(hiddenId).value||'').split('\n').filter(t=>t.trim());
  const list=document.getElementById(listId);
  list.innerHTML='';
  tags.forEach((t,i)=>{
    const chip=document.createElement('span');
    chip.className='tag-chip'+(isEn?' en':'');
    chip.innerHTML=`${escHtml(t)}<button onclick="removeTag('${hiddenId}','${listId}',${i},${isEn})" title="삭제"><i class="fas fa-times"></i></button>`;
    list.appendChild(chip);
  });
}
function addTag(inputId, hiddenId, listId, isEn){
  const inp=document.getElementById(inputId);
  const val=inp.value.trim(); if(!val) return;
  const tags=(document.getElementById(hiddenId).value||'').split('\n').filter(t=>t.trim());
  tags.push(val);
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
function previewPic(input,previewId){
  if(!input.files[0]) return;
  const r=new FileReader();
  r.onload=e=>{document.getElementById(previewId).innerHTML=`<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;};
  r.readAsDataURL(input.files[0]);
}

function openEdit(){
  setTags('m-tags','tag-list',<?= json_encode($member['tags']??'') ?>,false);
  setTags('m-tags-en','tag-en-list',<?= json_encode($member['tags_en']??'') ?>,true);
  openModal('member-modal');
}

async function saveMember(){
  const fd=new FormData();
  fd.append('id','<?= $member['id'] ?>');
  fd.append('name',      document.getElementById('m-name').value);
  fd.append('name_en',   document.getElementById('m-name-en').value);
  fd.append('email',     document.getElementById('m-email').value);
  fd.append('category',  document.getElementById('m-category').value);
  fd.append('title',     document.getElementById('m-title').value);
  fd.append('position',  document.getElementById('m-pos').value);
  fd.append('role',      document.getElementById('m-role').value);
  fd.append('tags',      document.getElementById('m-tags').value);
  fd.append('tags_en',   document.getElementById('m-tags-en').value);
  fd.append('biography', document.getElementById('m-bio').value);
  fd.append('sort_order',document.getElementById('m-sort').value);
  fd.append('is_active', document.getElementById('m-active').value);
  const pic=document.getElementById('m-pic').files[0]; if(pic) fd.append('picture',pic);
  const btn=document.getElementById('m-save-btn'); btn.disabled=true;
  const d=await fetch(BASE_URL+'/members/update',{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('member-modal');location.reload();}
  else toast(d.message,'error');
}

async function deleteMember(id){
  confirmAction('이 교인을 삭제하시겠습니까?',async()=>{
    const d=await api('/members/delete',{id});
    if(d.success){toast('삭제되었습니다.');location.href=BASE_URL+'/members';}
    else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
