<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('bulletins.edit'); $canDelete=AuthMiddleware::hasPermission('bulletins.delete'); ?>

<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/bulletins" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
</div>

<div class="card">
  <div class="card-header">
    <div style="flex:1">
      <h1 style="font-size:19px;font-weight:600"><?= htmlspecialchars($bulletin['title']) ?></h1>
      <div style="font-size:13px;color:var(--text-muted);margin-top:6px;display:flex;gap:14px;flex-wrap:wrap">
        <?php if($bulletin['year']): ?><span><i class="fas fa-calendar"></i> <?= $bulletin['year'] ?>년</span><?php endif; ?>
        <?php if($bulletin['week_number']): ?><span><i class="fas fa-list-ol"></i> <?= $bulletin['week_number'] ?>주차</span><?php endif; ?>
        <span><i class="fas fa-images"></i> 이미지 <?= count($images) ?>장</span>
        <span><i class="fas fa-clock"></i> 등록 <?= date('Y-m-d',strtotime($bulletin['created_at'])) ?></span>
      </div>
    </div>
    <div class="flex gap-8" style="flex-shrink:0">
      <?php if($canEdit): ?><button class="btn btn-primary btn-sm" onclick="openAddImg()"><i class="fas fa-upload"></i> 이미지 추가</button><?php endif; ?>
      <?php if($canEdit): ?><button class="btn btn-warning btn-sm" onclick="openEdit()"><i class="fas fa-pen"></i> 수정</button><?php endif; ?>
      <?php if($canDelete): ?><button class="btn btn-danger btn-sm" onclick="deleteBulletin(<?= $bulletin['id'] ?>)"><i class="fas fa-trash"></i> 삭제</button><?php endif; ?>
    </div>
  </div>
  <div class="card-body">
    <?php if(empty($images)): ?>
    <div style="text-align:center;padding:40px;color:var(--text-muted)"><i class="fas fa-image" style="font-size:32px;margin-bottom:8px;display:block"></i>이미지 없음</div>
    <?php else: ?>
    <div id="img-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px">
      <?php foreach($images as $img): ?>
      <div data-id="<?= $img['id'] ?>" style="position:relative;border:1px solid var(--border);border-radius:8px;overflow:hidden;background:var(--bg)">
        <img src="<?= UPLOAD_URL.htmlspecialchars($img['image_url']) ?>" style="width:100%;aspect-ratio:3/4;object-fit:cover;display:block" alt="">
        <?php if($canEdit): ?>
        <div style="position:absolute;top:6px;right:6px;display:flex;gap:4px">
          <span class="drag-handle" style="background:rgba(0,0,0,.5);color:#fff;border-radius:4px;padding:4px 6px;cursor:grab;font-size:12px"><i class="fas fa-grip-vertical"></i></span>
          <button onclick="deleteImg(<?= $img['id'] ?>,this.closest('[data-id]'))" style="background:var(--danger);color:#fff;border:none;border-radius:4px;padding:4px 6px;cursor:pointer;font-size:12px"><i class="fas fa-trash"></i></button>
        </div>
        <?php endif; ?>
        <div style="padding:6px 8px;font-size:11px;color:var(--text-muted);text-align:center">순서 <?= $img['order'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay hidden" id="edit-modal">
  <div class="modal modal-sm">
    <div class="modal-header"><h3>주보 수정</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('edit-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="bul-title" class="form-control" value="<?= htmlspecialchars($bulletin['title']) ?>"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">연도</label><input type="number" id="bul-year" class="form-control" value="<?= $bulletin['year']??date('Y') ?>"></div>
        <div class="form-group"><label class="form-label">주차</label><input type="number" id="bul-week" class="form-control" value="<?= $bulletin['week_number']??'' ?>"></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('edit-modal')">취소</button><button class="btn btn-primary" id="edit-save-btn" onclick="saveEdit()">저장</button></div>
  </div>
</div>

<!-- Add Image Modal -->
<div class="modal-overlay hidden" id="add-img-modal">
  <div class="modal modal-sm">
    <div class="modal-header"><h3>이미지 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('add-img-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">이미지 <span class="text-muted text-sm">(여러 장 선택 가능)</span></label><input type="file" id="add-imgs" class="form-control" accept="image/*" multiple></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('add-img-modal')">취소</button><button class="btn btn-primary" id="add-img-btn" onclick="uploadImgs()">업로드</button></div>
  </div>
</div>

<script>
const BULLETIN_ID=<?= $bulletin['id'] ?>;
function openEdit(){openModal('edit-modal');}
function openAddImg(){openModal('add-img-modal');}

async function saveEdit(){
  const fd=new FormData();
  fd.append('id',BULLETIN_ID);
  fd.append('title',document.getElementById('bul-title').value);
  fd.append('year',document.getElementById('bul-year').value);
  fd.append('week_number',document.getElementById('bul-week').value);
  const btn=document.getElementById('edit-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+'/bulletins/update',{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('edit-modal');location.reload();}else toast(d.message,'error');
}

async function uploadImgs(){
  const files=document.getElementById('add-imgs').files;
  if(!files.length){toast('이미지를 선택하세요.','error');return;}
  const btn=document.getElementById('add-img-btn');btn.disabled=true;btn.textContent='업로드 중...';
  let ok=0;
  for(let i=0;i<files.length;i++){
    const fd=new FormData();fd.append('bulletin_id',BULLETIN_ID);fd.append('image',files[i]);fd.append('order',i);
    const d=await fetch(BASE_URL+'/bulletins/image-add',{method:'POST',body:fd}).then(r=>r.json());
    if(d.success)ok++;
  }
  btn.disabled=false;
  toast(`${ok}장 업로드 완료`);closeModal('add-img-modal');location.reload();
}

async function deleteImg(id,el){
  if(!confirm('이 이미지를 삭제하시겠습니까?'))return;
  const d=await api('/bulletins/image-delete',{id});
  if(d.success){el.remove();toast('이미지가 삭제되었습니다.');}else toast(d.message,'error');
}

async function deleteBulletin(id){
  confirmAction('주보와 모든 이미지를 삭제하시겠습니까?',async()=>{
    const d=await api('/bulletins/delete',{id});
    if(d.success){toast('삭제되었습니다.');location.href=BASE_URL+'/bulletins';}
    else toast(d.message,'error');
  });
}

function pageInit(){
  const grid=document.getElementById('img-grid');
  if(grid&&typeof Sortable!=='undefined'){
    new Sortable(grid,{handle:'.drag-handle',animation:150,onEnd:async()=>{
      const orders=[...grid.querySelectorAll('[data-id]')].map((el,i)=>({id:parseInt(el.dataset.id),order:i+1}));
      await api('/bulletins/image-reorder',{orders:JSON.stringify(orders)});
      toast('순서가 저장되었습니다.');
    }});
  }
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
