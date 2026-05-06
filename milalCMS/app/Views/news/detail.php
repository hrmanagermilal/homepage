<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('news.edit'); $canDelete=AuthMiddleware::hasPermission('news.delete'); ?>
<?php $catMap=['news'=>['badge-blue','뉴스'],'update'=>['badge-green','업데이트'],'photo'=>['badge-purple','사진']]; [$bc,$bl]=$catMap[$news['category']]??['badge-gray','기타']; ?>

<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/news" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
</div>

<div class="card">
  <div class="card-header">
    <div style="flex:1;min-width:0">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <span class="badge <?= $bc ?>"><?= $bl ?></span>
        <?php if($news['tags']): ?>
          <?php foreach(explode(',',$news['tags']) as $tag): ?>
          <span class="badge badge-gray" style="font-size:11px"><?= htmlspecialchars(trim($tag)) ?></span>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <h1 style="font-size:20px;font-weight:600;line-height:1.4"><?= htmlspecialchars($news['title']) ?></h1>
      <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;font-size:13px;color:var(--text-muted);margin-top:8px">
        <span><i class="fas fa-user"></i> <?= htmlspecialchars($news['author']??'편집팀') ?></span>
        <span><i class="fas fa-calendar"></i> <?= date('Y년 m월 d일',strtotime($news['created_at'])) ?></span>
        <span><i class="fas fa-eye"></i> 조회 <?= number_format($news['views']) ?>회</span>
      </div>
    </div>
    <div class="flex gap-8" style="flex-shrink:0">
      <?php if($canEdit): ?><button class="btn btn-warning btn-sm" onclick="openEdit()"><i class="fas fa-pen"></i> 수정</button><?php endif; ?>
      <?php if($canDelete): ?><button class="btn btn-danger btn-sm" onclick="deleteNews(<?= $news['id'] ?>)"><i class="fas fa-trash"></i> 삭제</button><?php endif; ?>
    </div>
  </div>
  <div class="card-body">
    <?php if($news['image']): ?>
    <img src="<?= UPLOAD_URL.htmlspecialchars($news['image']) ?>" style="max-width:100%;border-radius:8px;margin-bottom:16px" alt="">
    <?php endif; ?>
    <div style="font-size:14px;line-height:1.8;white-space:pre-wrap;word-break:break-word"><?= htmlspecialchars($news['content']) ?></div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay hidden" id="news-modal">
  <div class="modal modal-lg">
    <div class="modal-header"><h3>뉴스 수정</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('news-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="n-title" class="form-control" value="<?= htmlspecialchars($news['title']) ?>"></div>
      <div class="form-group"><label class="form-label">내용 <span class="req">*</span></label><textarea id="n-content" class="form-control" rows="8"><?= htmlspecialchars($news['content']) ?></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">카테고리</label><select id="n-cat" class="form-control"><option value="news" <?= $news['category']==='news'?'selected':'' ?>>뉴스</option><option value="update" <?= $news['category']==='update'?'selected':'' ?>>업데이트</option><option value="photo" <?= $news['category']==='photo'?'selected':'' ?>>사진</option></select></div>
        <div class="form-group"><label class="form-label">작성자</label><input type="text" id="n-author" class="form-control" value="<?= htmlspecialchars($news['author']??'') ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">이미지 교체</label><input type="file" id="n-img" class="form-control" accept="image/*"><?php if($news['image']): ?><div style="margin-top:6px"><img src="<?= UPLOAD_URL.htmlspecialchars($news['image']) ?>" style="max-height:60px;border-radius:4px"></div><?php endif; ?></div>
        <div class="form-group"><label class="form-label">태그 <span class="text-muted text-sm">(쉼표 구분)</span></label><input type="text" id="n-tags" class="form-control" value="<?= htmlspecialchars($news['tags']??'') ?>"></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('news-modal')">취소</button><button class="btn btn-primary" id="n-save-btn" onclick="saveNews()">저장</button></div>
  </div>
</div>

<script>
function openEdit(){openModal('news-modal');}
async function saveNews(){
  const fd=new FormData();
  fd.append('id','<?= $news['id'] ?>');
  fd.append('title',document.getElementById('n-title').value);
  fd.append('content',document.getElementById('n-content').value);
  fd.append('category',document.getElementById('n-cat').value);
  fd.append('author',document.getElementById('n-author').value);
  fd.append('tags',document.getElementById('n-tags').value);
  const img=document.getElementById('n-img').files[0];if(img)fd.append('image',img);
  const btn=document.getElementById('n-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+'/news/update',{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('news-modal');location.reload();}else toast(d.message,'error');
}
async function deleteNews(id){
  confirmAction('이 뉴스를 삭제하시겠습니까?',async()=>{
    const d=await api('/news/delete',{id});
    if(d.success){toast('삭제되었습니다.');location.href=BASE_URL+'/news';}
    else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
