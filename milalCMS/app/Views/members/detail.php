<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('members.edit'); $canDelete=AuthMiddleware::hasPermission('members.delete'); ?>

<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/members" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
</div>

<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start">
  <!-- 좌: 프로필 카드 -->
  <div class="card">
    <div class="card-body" style="text-align:center;padding:28px 20px">
      <?php if($member['picture']): ?>
      <img src="<?= UPLOAD_URL.htmlspecialchars($member['picture']) ?>" style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:3px solid var(--border);margin-bottom:14px" alt="">
      <?php else: ?>
      <div style="width:110px;height:110px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:36px;color:#fff"><?= mb_substr($member['name'],0,1) ?></div>
      <?php endif; ?>
      <h2 style="font-size:18px;font-weight:600"><?= htmlspecialchars($member['name']) ?></h2>
      <?php if($member['title']): ?><p style="font-size:14px;color:var(--primary);margin:4px 0"><?= htmlspecialchars($member['title']) ?></p><?php endif; ?>
      <?php if($member['position']): ?><p style="font-size:13px;color:var(--text-muted)"><?= htmlspecialchars($member['position']) ?></p><?php endif; ?>
      <div style="margin-top:12px"><span class="badge <?= $member['is_active']?'badge-green':'badge-gray' ?>"><?= $member['is_active']?'활성':'비활성' ?></span></div>
      <?php if($member['email']): ?>
      <div style="margin-top:12px;font-size:13px;color:var(--text-muted)"><i class="fas fa-envelope"></i> <?= htmlspecialchars($member['email']) ?></div>
      <?php endif; ?>
    </div>
    <div style="padding:0 16px 16px;display:flex;flex-direction:column;gap:8px">
      <?php if($canEdit): ?><button class="btn btn-warning" onclick="openEdit()"><i class="fas fa-pen"></i> 수정</button><?php endif; ?>
      <?php if($canDelete): ?><button class="btn btn-danger" onclick="deleteMember(<?= $member['id'] ?>)"><i class="fas fa-trash"></i> 삭제</button><?php endif; ?>
    </div>
  </div>

  <!-- 우: 상세 정보 -->
  <div class="card">
    <div class="card-header"><h2>상세 정보</h2></div>
    <div class="card-body">
      <table style="width:100%;border-collapse:collapse;font-size:14px">
        <?php $rows=[['역할',$member['role']??''],['정렬 순서',$member['sort_order']],['등록일',date('Y년 m월 d일',strtotime($member['created_at']))]]; foreach($rows as [$label,$val]): ?>
        <tr style="border-bottom:1px solid var(--border)">
          <td style="padding:10px 12px;font-weight:500;color:var(--text-muted);width:120px;white-space:nowrap"><?= $label ?></td>
          <td style="padding:10px 12px"><?= htmlspecialchars((string)$val) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
      <?php if($member['biography']): ?>
      <div style="margin-top:20px">
        <div style="font-size:13px;font-weight:500;color:var(--text-muted);margin-bottom:8px">약력</div>
        <div style="font-size:14px;line-height:1.8;white-space:pre-wrap;background:var(--bg);border-radius:8px;padding:14px"><?= htmlspecialchars($member['biography']) ?></div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay hidden" id="member-modal">
  <div class="modal modal-lg">
    <div class="modal-header"><h3>교인 수정</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('member-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <div style="display:grid;grid-template-columns:100px 1fr;gap:20px;align-items:start;margin-bottom:16px">
        <div>
          <div id="pic-preview" style="width:90px;height:90px;border-radius:50%;overflow:hidden;background:var(--bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;margin-bottom:8px">
            <?php if($member['picture']): ?><img src="<?= UPLOAD_URL.htmlspecialchars($member['picture']) ?>" style="width:100%;height:100%;object-fit:cover"><?php else: ?><i class="fas fa-user" style="font-size:28px;color:var(--text-muted)"></i><?php endif; ?>
          </div>
          <label class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;font-size:11px"><i class="fas fa-camera"></i>사진<input type="file" id="m-pic" accept="image/*" style="display:none" onchange="previewPic(this)"></label>
        </div>
        <div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">이름 <span class="req">*</span></label><input type="text" id="m-name" class="form-control" value="<?= htmlspecialchars($member['name']) ?>"></div>
            <div class="form-group"><label class="form-label">이메일</label><input type="email" id="m-email" class="form-control" value="<?= htmlspecialchars($member['email']??'') ?>"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">직함</label><input type="text" id="m-title" class="form-control" value="<?= htmlspecialchars($member['title']??'') ?>"></div>
            <div class="form-group"><label class="form-label">직위</label><input type="text" id="m-pos" class="form-control" value="<?= htmlspecialchars($member['position']??'') ?>"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">역할</label><input type="text" id="m-role" class="form-control" value="<?= htmlspecialchars($member['role']??'') ?>"></div>
            <div class="form-group"><label class="form-label">정렬 순서</label><input type="number" id="m-sort" class="form-control" value="<?= $member['sort_order'] ?>"></div>
          </div>
        </div>
      </div>
      <div class="form-group"><label class="form-label">약력</label><textarea id="m-bio" class="form-control" rows="5"><?= htmlspecialchars($member['biography']??'') ?></textarea></div>
      <div class="form-group"><label class="form-label">상태</label><select id="m-active" class="form-control"><option value="1" <?= $member['is_active']?'selected':'' ?>>활성</option><option value="0" <?= !$member['is_active']?'selected':'' ?>>비활성</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('member-modal')">취소</button><button class="btn btn-primary" id="m-save-btn" onclick="saveMember()">저장</button></div>
  </div>
</div>

<script>
function openEdit(){openModal('member-modal');}
function previewPic(input){if(input.files[0]){const r=new FileReader();r.onload=e=>{const el=document.getElementById('pic-preview');el.innerHTML=`<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;};r.readAsDataURL(input.files[0]);}}
async function saveMember(){
  const fd=new FormData();
  fd.append('id','<?= $member['id'] ?>');
  fd.append('name',document.getElementById('m-name').value);
  fd.append('email',document.getElementById('m-email').value);
  fd.append('title',document.getElementById('m-title').value);
  fd.append('position',document.getElementById('m-pos').value);
  fd.append('role',document.getElementById('m-role').value);
  fd.append('biography',document.getElementById('m-bio').value);
  fd.append('sort_order',document.getElementById('m-sort').value);
  fd.append('is_active',document.getElementById('m-active').value);
  const pic=document.getElementById('m-pic').files[0];if(pic)fd.append('picture',pic);
  const btn=document.getElementById('m-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+'/members/update',{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('member-modal');location.reload();}else toast(d.message,'error');
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
