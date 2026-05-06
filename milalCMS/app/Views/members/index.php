<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('members.edit'); $canCreate=AuthMiddleware::hasPermission('members.create'); $canDelete=AuthMiddleware::hasPermission('members.delete'); ?>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-users" style="color:var(--primary)"></i> 교인·목회자 관리</h2>
    <?php if($canCreate): ?><button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i>추가</button><?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead><tr><th style="width:40px"></th><th>사진</th><th>이름</th><th>직함</th><th>직위</th><th>역할</th><th>상태</th><th style="width:90px">관리</th></tr></thead>
      <tbody id="member-tbody">
      <?php foreach($data['rows'] as $r): ?>
      <tr data-id="<?= $r['id'] ?>">
        <?php if($canEdit): ?><td class="drag-handle" style="cursor:grab"><i class="fas fa-grip-vertical" style="color:var(--text-muted)"></i></td><?php else: ?><td></td><?php endif; ?>
        <td>
          <a href="<?= BASE_URL ?>/members/view?id=<?= $r['id'] ?>">
          <?php if($r['picture']): ?>
          <img src="<?= UPLOAD_URL.htmlspecialchars($r['picture']) ?>" class="img-thumb" style="border-radius:50%" alt="">
          <?php else: ?>
          <div style="width:40px;height:40px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:500"><?= mb_substr($r['name'],0,1) ?></div>
          <?php endif; ?>
          </a>
        </td>
        <td>
          <a href="<?= BASE_URL ?>/members/view?id=<?= $r['id'] ?>" style="color:var(--text);font-weight:500">
            <?= htmlspecialchars($r['name']) ?>
          </a>
        </td>
        <td class="text-sm"><?= htmlspecialchars($r['title']??'-') ?></td>
        <td class="text-sm"><?= htmlspecialchars($r['position']??'-') ?></td>
        <td class="text-sm text-muted"><?= htmlspecialchars($r['role']??'-') ?></td>
        <td><span class="badge <?= $r['is_active']?'badge-green':'badge-gray' ?>"><?= $r['is_active']?'활성':'비활성' ?></span></td>
        <td><div class="flex gap-8">
          <a href="<?= BASE_URL ?>/members/view?id=<?= $r['id'] ?>" class="btn btn-ghost btn-sm btn-icon" title="상세보기"><i class="fas fa-eye"></i></a>
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

<!-- Create Modal -->
<div class="modal-overlay hidden" id="member-modal">
  <div class="modal modal-lg">
    <div class="modal-header"><h3>교인 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('member-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <div style="display:grid;grid-template-columns:100px 1fr;gap:20px;align-items:start">
        <div>
          <div id="member-pic-preview" style="width:90px;height:90px;background:var(--bg);border-radius:50%;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-muted);margin-bottom:8px;overflow:hidden"><i class="fas fa-user" style="font-size:28px"></i></div>
          <label class="btn btn-ghost btn-sm" style="width:100%;justify-content:center"><i class="fas fa-camera"></i>사진<input type="file" id="member-pic" accept="image/*" style="display:none" onchange="previewPic(this)"></label>
        </div>
        <div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">이름 <span class="req">*</span></label><input type="text" id="m-name" class="form-control"></div>
            <div class="form-group"><label class="form-label">이메일</label><input type="email" id="m-email" class="form-control"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">직함</label><input type="text" id="m-title" class="form-control" placeholder="예: 담임목사"></div>
            <div class="form-group"><label class="form-label">직위</label><input type="text" id="m-pos" class="form-control" placeholder="예: 목사"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">역할</label><input type="text" id="m-role" class="form-control" placeholder="예: 담임"></div>
            <div class="form-group"><label class="form-label">정렬 순서</label><input type="number" id="m-sort" class="form-control" value="0"></div>
          </div>
        </div>
      </div>
      <div class="form-group" style="margin-top:8px"><label class="form-label">약력</label><textarea id="m-bio" class="form-control" rows="4"></textarea></div>
      <div class="form-group"><label class="form-label">상태</label><select id="m-active" class="form-control"><option value="1">활성</option><option value="0">비활성</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('member-modal')">취소</button><button class="btn btn-primary" id="member-save-btn" onclick="saveMember()">저장</button></div>
  </div>
</div>

<script>
function previewPic(input){if(input.files[0]){const r=new FileReader();r.onload=e=>{const el=document.getElementById('member-pic-preview');el.innerHTML=`<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;};r.readAsDataURL(input.files[0]);}}
function openCreate(){
  ['m-name','m-email','m-title','m-pos','m-role','m-bio'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('m-sort').value=0;document.getElementById('m-active').value=1;
  document.getElementById('member-pic-preview').innerHTML='<i class="fas fa-user" style="font-size:28px"></i>';
  openModal('member-modal');
}
async function saveMember(){
  const fd=new FormData();
  fd.append('name',document.getElementById('m-name').value);
  fd.append('email',document.getElementById('m-email').value);
  fd.append('title',document.getElementById('m-title').value);
  fd.append('position',document.getElementById('m-pos').value);
  fd.append('role',document.getElementById('m-role').value);
  fd.append('biography',document.getElementById('m-bio').value);
  fd.append('sort_order',document.getElementById('m-sort').value);
  fd.append('is_active',document.getElementById('m-active').value);
  const pic=document.getElementById('member-pic').files[0];if(pic)fd.append('picture',pic);
  const btn=document.getElementById('member-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+'/members/create',{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('member-modal');location.reload();}else toast(d.message,'error');
}
async function deleteRow(id){
  confirmAction('이 교인을 삭제하시겠습니까?',async()=>{
    const d=await api('/members/delete',{id});
    if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}else toast(d.message,'error');
  });
}
// Sortable: footer의 pageInit() 호출 타이밍에 실행
function pageInit(){
  const tbody=document.getElementById('member-tbody');
  <?php if($canEdit): ?>
  if(tbody&&typeof Sortable!=='undefined'){
    new Sortable(tbody,{handle:'.drag-handle',animation:150,onEnd:async()=>{
      const orders=[...tbody.querySelectorAll('tr')].map((r,i)=>({id:parseInt(r.dataset.id),order:i+1}));
      const d=await api('/members/reorder',{orders:JSON.stringify(orders)});
      if(d.success)toast('순서가 저장되었습니다.');
    }});
  }
  <?php endif; ?>
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
