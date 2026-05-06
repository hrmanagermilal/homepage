<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('sermons.edit'); $canDelete=AuthMiddleware::hasPermission('sermons.delete'); ?>

<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/sermons" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start">
  <!-- 좌: 정보 -->
  <div class="card">
    <div class="card-header">
      <div style="flex:1">
        <h1 style="font-size:19px;font-weight:600;line-height:1.4;margin-bottom:8px"><?= htmlspecialchars($sermon['title']) ?></h1>
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;font-size:13px;color:var(--text-muted)">
          <?php if($sermon['preacher']): ?><span><i class="fas fa-microphone"></i> <?= htmlspecialchars($sermon['preacher']) ?></span><?php endif; ?>
          <?php if($sermon['sermon_date']): ?><span><i class="fas fa-calendar"></i> <?= date('Y년 m월 d일',strtotime($sermon['sermon_date'])) ?></span><?php endif; ?>
          <span><i class="fas fa-clock"></i> 등록 <?= date('Y-m-d',strtotime($sermon['created_at'])) ?></span>
        </div>
      </div>
      <div class="flex gap-8" style="flex-shrink:0">
        <?php if($canEdit): ?><button class="btn btn-warning btn-sm" onclick="openEdit()"><i class="fas fa-pen"></i> 수정</button><?php endif; ?>
        <?php if($canDelete): ?><button class="btn btn-danger btn-sm" onclick="deleteSermon(<?= $sermon['id'] ?>)"><i class="fas fa-trash"></i> 삭제</button><?php endif; ?>
      </div>
    </div>
    <div class="card-body">
      <?php if($sermon['description']): ?>
      <div style="background:var(--bg);border-radius:8px;padding:14px;margin-bottom:16px;font-size:14px;line-height:1.7;white-space:pre-wrap"><?= htmlspecialchars($sermon['description']) ?></div>
      <?php endif; ?>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <?php if($sermon['youtube_url']): ?>
        <a href="<?= htmlspecialchars($sermon['youtube_url']) ?>" target="_blank" class="btn btn-danger btn-sm"><i class="fab fa-youtube"></i> 유튜브에서 보기</a>
        <?php endif; ?>
        <?php if($sermon['youtube_id']): ?>
        <span class="text-muted text-sm" style="display:flex;align-items:center;gap:4px"><i class="fab fa-youtube"></i> ID: <?= htmlspecialchars($sermon['youtube_id']) ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <!-- 우: 유튜브 플레이어 -->
  <div class="card">
    <div class="card-header"><h2><i class="fab fa-youtube" style="color:#ff0000"></i> 영상 미리보기</h2></div>
    <div class="card-body" style="padding:0">
      <?php if($sermon['youtube_id']): ?>
      <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:0 0 8px 8px">
        <iframe
          src="https://www.youtube.com/embed/<?= htmlspecialchars($sermon['youtube_id']) ?>?rel=0"
          style="position:absolute;top:0;left:0;width:100%;height:100%;border:none"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen>
        </iframe>
      </div>
      <?php elseif($sermon['thumbnail']): ?>
      <img src="<?= htmlspecialchars($sermon['thumbnail']) ?>" style="width:100%;border-radius:0 0 8px 8px" alt="">
      <?php else: ?>
      <div style="padding:40px;text-align:center;color:var(--text-muted)"><i class="fab fa-youtube" style="font-size:40px;margin-bottom:8px;display:block"></i>영상 없음</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay hidden" id="sermon-modal">
  <div class="modal modal-md">
    <div class="modal-header"><h3>설교 수정</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('sermon-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="s-title" class="form-control" value="<?= htmlspecialchars($sermon['title']) ?>"></div>
      <div class="form-group">
        <label class="form-label">유튜브 URL <span class="req">*</span></label>
        <input type="url" id="s-url" class="form-control" value="<?= htmlspecialchars($sermon['youtube_url']) ?>" oninput="previewYt()">
        <div id="yt-preview" style="margin-top:8px"><?php if($sermon['youtube_id']): ?><img src="https://img.youtube.com/vi/<?= htmlspecialchars($sermon['youtube_id']) ?>/mqdefault.jpg" style="max-width:100%;border-radius:6px"><?php endif; ?></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">설교자</label><input type="text" id="s-preacher" class="form-control" value="<?= htmlspecialchars($sermon['preacher']??'') ?>"></div>
        <div class="form-group"><label class="form-label">설교일</label><input type="date" id="s-date" class="form-control" value="<?= htmlspecialchars($sermon['sermon_date']??'') ?>"></div>
      </div>
      <div class="form-group"><label class="form-label">설명</label><textarea id="s-desc" class="form-control" rows="3"><?= htmlspecialchars($sermon['description']??'') ?></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('sermon-modal')">취소</button><button class="btn btn-primary" id="s-save-btn" onclick="saveSermon()">저장</button></div>
  </div>
</div>

<script>
function openEdit(){openModal('sermon-modal');}
function extractYtId(url){const m=url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/);return m?m[1]:null;}
function previewYt(){const id=extractYtId(document.getElementById('s-url').value);document.getElementById('yt-preview').innerHTML=id?`<img src="https://img.youtube.com/vi/${id}/mqdefault.jpg" style="max-width:100%;border-radius:6px">`:''}
async function saveSermon(){
  const fd=new FormData();
  fd.append('id','<?= $sermon['id'] ?>');
  fd.append('title',document.getElementById('s-title').value);
  fd.append('youtube_url',document.getElementById('s-url').value);
  fd.append('preacher',document.getElementById('s-preacher').value);
  fd.append('sermon_date',document.getElementById('s-date').value);
  fd.append('description',document.getElementById('s-desc').value);
  const btn=document.getElementById('s-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+'/sermons/update',{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('sermon-modal');location.reload();}else toast(d.message,'error');
}
async function deleteSermon(id){
  confirmAction('이 설교를 삭제하시겠습니까?',async()=>{
    const d=await api('/sermons/delete',{id});
    if(d.success){toast('삭제되었습니다.');location.href=BASE_URL+'/sermons';}
    else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
