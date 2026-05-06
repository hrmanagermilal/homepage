<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('announcements.edit'); $canDelete=AuthMiddleware::hasPermission('announcements.delete'); ?>
<?php $catMap=['general'=>['badge-gray','일반'],'event'=>['badge-blue','이벤트'],'urgent'=>['badge-red','긴급']]; [$bc,$bl]=$catMap[$announcement['category']]??['badge-gray','기타']; ?>

<style>
.detail-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px}
.detail-meta{display:flex;align-items:center;gap:12px;flex-wrap:wrap;font-size:13px;color:var(--text-muted)}
.detail-content{font-size:14px;line-height:1.8;color:var(--text);white-space:pre-wrap;word-break:break-word}
.detail-img{max-width:100%;border-radius:8px;margin-bottom:16px}
.back-btn-row{margin-bottom:16px}
</style>

<div class="back-btn-row">
  <a href="<?= BASE_URL ?>/announcements" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
</div>

<div class="card">
  <div class="card-header">
    <div style="flex:1;min-width:0">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <?php if($announcement['is_pinned']): ?><i class="fas fa-thumbtack" style="color:var(--danger)"></i><?php endif; ?>
        <span class="badge <?= $bc ?>"><?= $bl ?></span>
        <span class="badge <?= $announcement['is_active']?'badge-green':'badge-gray' ?>"><?= $announcement['is_active']?'활성':'비활성' ?></span>
      </div>
      <h1 style="font-size:20px;font-weight:600;line-height:1.4"><?= htmlspecialchars($announcement['title']) ?></h1>
      <div class="detail-meta" style="margin-top:8px">
        <span><i class="fas fa-user"></i> <?= htmlspecialchars($announcement['author_name']??'알 수 없음') ?></span>
        <span><i class="fas fa-calendar"></i> <?= date('Y년 m월 d일',strtotime($announcement['created_at'])) ?></span>
        <span><i class="fas fa-eye"></i> 조회 <?= number_format($announcement['views']) ?>회</span>
        <?php if($announcement['link']): ?><a href="<?= htmlspecialchars($announcement['link']) ?>" target="_blank" style="color:var(--info)"><i class="fas fa-external-link-alt"></i> 링크</a><?php endif; ?>
      </div>
    </div>
    <div class="flex gap-8" style="flex-shrink:0">
      <?php if($canEdit): ?>
      <button class="btn btn-warning btn-sm" onclick="openEdit(<?= $announcement['id'] ?>)"><i class="fas fa-pen"></i> 수정</button>
      <?php endif; ?>
      <?php if($canDelete): ?>
      <button class="btn btn-danger btn-sm" onclick="deleteAnn(<?= $announcement['id'] ?>)"><i class="fas fa-trash"></i> 삭제</button>
      <?php endif; ?>
    </div>
  </div>
  <div class="card-body">
    <?php if($announcement['image']): ?>
    <img src="<?= UPLOAD_URL.htmlspecialchars($announcement['image']) ?>" class="detail-img" alt="">
    <?php endif; ?>
    <div class="detail-content"><?= htmlspecialchars($announcement['content']) ?></div>
  </div>
</div>

<!-- Edit Modal (목록 페이지와 동일) -->
<div class="modal-overlay hidden" id="ann-modal">
  <div class="modal modal-lg">
    <div class="modal-header"><h3>공지사항 수정</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('ann-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="ann-id" value="<?= $announcement['id'] ?>">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="ann-title" class="form-control" value="<?= htmlspecialchars($announcement['title']) ?>"></div>
      <div class="form-group"><label class="form-label">내용 <span class="req">*</span></label><textarea id="ann-content" class="form-control" rows="8"><?= htmlspecialchars($announcement['content']) ?></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">카테고리</label><select id="ann-cat" class="form-control"><option value="general" <?= $announcement['category']==='general'?'selected':'' ?>>일반</option><option value="event" <?= $announcement['category']==='event'?'selected':'' ?>>이벤트</option><option value="urgent" <?= $announcement['category']==='urgent'?'selected':'' ?>>긴급</option></select></div>
        <div class="form-group"><label class="form-label">링크</label><input type="url" id="ann-link" class="form-control" value="<?= htmlspecialchars($announcement['link']??'') ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">이미지 교체</label><input type="file" id="ann-img" class="form-control" accept="image/*"><?php if($announcement['image']): ?><div style="margin-top:6px"><img src="<?= UPLOAD_URL.htmlspecialchars($announcement['image']) ?>" style="max-height:60px;border-radius:4px"></div><?php endif; ?></div>
        <div class="form-group">
          <label class="form-label">상태</label><select id="ann-active" class="form-control"><option value="1" <?= $announcement['is_active']?'selected':'' ?>>활성</option><option value="0" <?= !$announcement['is_active']?'selected':'' ?>>비활성</option></select>
          <label style="display:flex;align-items:center;gap:6px;margin-top:10px;font-size:13px;cursor:pointer"><input type="checkbox" id="ann-pin" <?= $announcement['is_pinned']?'checked':'' ?>> 상단 고정</label>
        </div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('ann-modal')">취소</button><button class="btn btn-primary" id="ann-save-btn" onclick="saveAnn()">저장</button></div>
  </div>
</div>

<script>
function openEdit(id){openModal('ann-modal');}
async function saveAnn(){
  const id=document.getElementById('ann-id').value;
  const fd=new FormData();
  fd.append('id',id);
  fd.append('title',document.getElementById('ann-title').value);
  fd.append('content',document.getElementById('ann-content').value);
  fd.append('link',document.getElementById('ann-link').value);
  fd.append('category',document.getElementById('ann-cat').value);
  fd.append('is_active',document.getElementById('ann-active').value);
  fd.append('is_pinned',document.getElementById('ann-pin').checked?1:0);
  const img=document.getElementById('ann-img').files[0];if(img)fd.append('image',img);
  const btn=document.getElementById('ann-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+'/announcements/update',{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('ann-modal');location.reload();}else toast(d.message,'error');
}
async function deleteAnn(id){
  confirmAction('이 공지사항을 삭제하시겠습니까?',async()=>{
    const d=await api('/announcements/delete',{id});
    if(d.success){toast('삭제되었습니다.');location.href=BASE_URL+'/announcements';}
    else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
