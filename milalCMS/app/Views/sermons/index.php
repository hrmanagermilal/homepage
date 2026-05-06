<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('sermons.edit'); $canCreate=AuthMiddleware::hasPermission('sermons.create'); $canDelete=AuthMiddleware::hasPermission('sermons.delete'); ?>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-video" style="color:var(--primary)"></i> 설교 관리</h2>
    <?php if($canCreate): ?><button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i>설교 추가</button><?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead><tr><th style="width:100px">썸네일</th><th>제목</th><th>설교자</th><th>설교일</th><th>등록일</th><th style="width:90px">관리</th></tr></thead>
      <tbody>
      <?php foreach($data['rows'] as $r): ?>
      <tr data-id="<?= $r['id'] ?>">
        <td>
          <?php if($r['thumbnail']): ?>
          <a href="<?= BASE_URL ?>/sermons/view?id=<?= $r['id'] ?>"><img src="<?= htmlspecialchars($r['thumbnail']) ?>" class="img-thumb-lg" alt=""></a>
          <?php else: ?>
          <div style="width:80px;height:50px;background:var(--bg);border-radius:4px;display:flex;align-items:center;justify-content:center;color:var(--text-muted)"><i class="fab fa-youtube" style="font-size:18px"></i></div>
          <?php endif; ?>
        </td>
        <td>
          <a href="<?= BASE_URL ?>/sermons/view?id=<?= $r['id'] ?>" style="color:var(--text);font-weight:500;display:block" class="truncate" style="max-width:280px">
            <?= htmlspecialchars($r['title']) ?>
          </a>
        </td>
        <td class="text-sm"><?= htmlspecialchars($r['preacher']??'-') ?></td>
        <td class="text-sm text-muted"><?= $r['sermon_date']??'-' ?></td>
        <td class="text-sm text-muted"><?= date('Y-m-d',strtotime($r['created_at'])) ?></td>
        <td><div class="flex gap-8">
          <?php if($canEdit): ?><button class="btn btn-warning btn-sm btn-icon" onclick="openEdit(<?= $r['id'] ?>)"><i class="fas fa-pen"></i></button><?php endif; ?>
          <?php if($canDelete): ?><button class="btn btn-danger btn-sm btn-icon" onclick="deleteRow(<?= $r['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
        </div></td>
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

<!-- Modal -->
<div class="modal-overlay hidden" id="sermon-modal">
  <div class="modal modal-md">
    <div class="modal-header"><h3 id="sermon-modal-title">설교 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('sermon-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="sermon-id">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="s-title" class="form-control"></div>
      <div class="form-group">
        <label class="form-label">유튜브 URL <span class="req">*</span></label>
        <input type="url" id="s-url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." oninput="previewYt()">
        <div id="yt-preview" style="margin-top:8px"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">설교자</label><input type="text" id="s-preacher" class="form-control"></div>
        <div class="form-group"><label class="form-label">설교일</label><input type="date" id="s-date" class="form-control"></div>
      </div>
      <div class="form-group"><label class="form-label">설명</label><textarea id="s-desc" class="form-control" rows="3"></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('sermon-modal')">취소</button><button class="btn btn-primary" id="sermon-save-btn" onclick="saveSermon()">저장</button></div>
  </div>
</div>

<script>
function openCreate(){
  document.getElementById('sermon-modal-title').textContent='설교 추가';
  document.getElementById('sermon-id').value='';
  ['s-title','s-url','s-preacher','s-desc'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('s-date').value='';
  document.getElementById('yt-preview').innerHTML='';
  openModal('sermon-modal');
}
async function openEdit(id){
  const d=await api('/sermons/detail',{id});
  if(!d.success){toast(d.message,'error');return;}
  const r=d.data;
  document.getElementById('sermon-modal-title').textContent='설교 수정';
  document.getElementById('sermon-id').value=r.id;
  document.getElementById('s-title').value=r.title;
  document.getElementById('s-url').value=r.youtube_url;
  document.getElementById('s-preacher').value=r.preacher||'';
  document.getElementById('s-date').value=r.sermon_date||'';
  document.getElementById('s-desc').value=r.description||'';
  if(r.youtube_id)document.getElementById('yt-preview').innerHTML=`<img src="https://img.youtube.com/vi/${r.youtube_id}/mqdefault.jpg" style="max-width:100%;border-radius:6px">`;
  openModal('sermon-modal');
}
function extractYtId(url){const m=url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/);return m?m[1]:null;}
function previewYt(){const id=extractYtId(document.getElementById('s-url').value);document.getElementById('yt-preview').innerHTML=id?`<img src="https://img.youtube.com/vi/${id}/mqdefault.jpg" style="max-width:100%;border-radius:6px">`:''}
async function saveSermon(){
  const id=document.getElementById('sermon-id').value;
  const fd=new FormData();
  if(id)fd.append('id',id);
  fd.append('title',document.getElementById('s-title').value);
  fd.append('youtube_url',document.getElementById('s-url').value);
  fd.append('preacher',document.getElementById('s-preacher').value);
  fd.append('sermon_date',document.getElementById('s-date').value);
  fd.append('description',document.getElementById('s-desc').value);
  const btn=document.getElementById('sermon-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+(id?'/sermons/update':'/sermons/create'),{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('sermon-modal');location.reload();}else toast(d.message,'error');
}
async function deleteRow(id){
  confirmAction('이 설교를 삭제하시겠습니까?',async()=>{
    const d=await api('/sermons/delete',{id});
    if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
