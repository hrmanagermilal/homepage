<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('announcements.edit'); $canCreate=AuthMiddleware::hasPermission('announcements.create'); $canDelete=AuthMiddleware::hasPermission('announcements.delete'); ?>

<div class="card">
  <div class="card-header">
    <div class="flex flex-center gap-12">
      <h2><i class="fas fa-bullhorn" style="color:var(--primary)"></i> 공지사항 관리</h2>
      <div class="flex gap-8">
        <?php foreach([''=> '전체','general'=>'일반','event'=>'이벤트','urgent'=>'긴급'] as $val=>$label): ?>
        <a href="?category=<?= $val ?>" class="btn btn-sm <?= $category===$val?'btn-primary':'btn-ghost' ?>"><?= $label ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php if($canCreate): ?><button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i>공지 추가</button><?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead><tr><th style="width:32px">핀</th><th style="width:60px">이미지</th><th>제목</th><th>카테고리</th><th>작성자</th><th>조회</th><th>상태</th><th>작성일</th><th style="width:110px">관리</th></tr></thead>
      <tbody>
      <?php foreach($data['rows'] as $r):
        $catMap=['general'=>['badge-gray','일반'],'event'=>['badge-blue','이벤트'],'urgent'=>['badge-red','긴급']];
        [$bc,$bl]=$catMap[$r['category']]??['badge-gray','기타']; ?>
      <tr data-id="<?= $r['id'] ?>">
        <td><?php if($r['is_pinned']): ?><i class="fas fa-thumbtack" style="color:var(--danger)"></i><?php endif; ?></td>
        <td><?php if($r['image']): ?><img src="<?= UPLOAD_URL.htmlspecialchars($r['image']) ?>" class="img-thumb" alt=""><?php else: ?><span class="text-muted">-</span><?php endif; ?></td>
        <td>
          <a href="<?= BASE_URL ?>/announcements/view?id=<?= $r['id'] ?>" style="color:var(--text);font-weight:500" class="truncate" style="display:block">
            <?= htmlspecialchars($r['title']) ?>
          </a>
        </td>
        <td><span class="badge <?= $bc ?>"><?= $bl ?></span></td>
        <td class="text-sm text-muted"><?= htmlspecialchars($r['author_name']??'-') ?></td>
        <td class="text-sm text-muted"><?= number_format($r['views']) ?></td>
        <td><span class="badge <?= $r['is_active']?'badge-green':'badge-gray' ?>"><?= $r['is_active']?'활성':'비활성' ?></span></td>
        <td class="text-sm text-muted"><?= date('Y-m-d',strtotime($r['created_at'])) ?></td>
        <td><div class="flex gap-8">
          <?php if($canEdit): ?><button class="btn btn-ghost btn-sm btn-icon" onclick="togglePin(<?= $r['id'] ?>)" title="핀 고정/해제"><i class="fas fa-thumbtack"></i></button><?php endif; ?>
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
      <?php if($pagination['has_prev']): ?><a href="?page=<?= $pagination['current']-1 ?>&category=<?= $category ?>">&laquo;</a><?php endif; ?>
      <?php for($p=$pagination['start_page'];$p<=$pagination['end_page'];$p++): ?>
      <<?= $p===$pagination['current']?'span class="active"':'a href="?page='.$p.'&category='.$category.'"' ?>><?= $p ?></<?= $p===$pagination['current']?'span':'a' ?>>
      <?php endfor; ?>
      <?php if($pagination['has_next']): ?><a href="?page=<?= $pagination['current']+1 ?>&category=<?= $category ?>">&raquo;</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Create/Edit Modal -->
<div class="modal-overlay hidden" id="ann-modal">
  <div class="modal modal-lg">
    <div class="modal-header"><h3 id="ann-modal-title">공지사항 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('ann-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="ann-id">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="ann-title" class="form-control"></div>
      <div class="form-group"><label class="form-label">내용 <span class="req">*</span></label><textarea id="ann-content" class="form-control" rows="6"></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">카테고리</label><select id="ann-cat" class="form-control"><option value="general">일반</option><option value="event">이벤트</option><option value="urgent">긴급</option></select></div>
        <div class="form-group"><label class="form-label">링크</label><input type="url" id="ann-link" class="form-control" placeholder="https://"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">이미지</label><input type="file" id="ann-img" class="form-control" accept="image/*"><div id="ann-img-preview" style="margin-top:8px"></div></div>
        <div class="form-group">
          <label class="form-label">상태</label><select id="ann-active" class="form-control"><option value="1">활성</option><option value="0">비활성</option></select>
          <label style="display:flex;align-items:center;gap:6px;margin-top:10px;font-size:13px;cursor:pointer"><input type="checkbox" id="ann-pin"> 상단 고정</label>
        </div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('ann-modal')">취소</button><button class="btn btn-primary" id="ann-save-btn" onclick="saveAnn()">저장</button></div>
  </div>
</div>

<script>
const defaultAuthor='<?= htmlspecialchars(addslashes($_SESSION['name']??$_SESSION['name']??'')) ?>';
function openCreate(){
  document.getElementById('ann-modal-title').textContent='공지사항 추가';
  document.getElementById('ann-id').value='';
  ['ann-title','ann-content','ann-link'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('ann-cat').value='general';
  document.getElementById('ann-active').value=1;
  document.getElementById('ann-pin').checked=false;
  document.getElementById('ann-img-preview').innerHTML='';
  openModal('ann-modal');
}
async function openEdit(id){
  const d=await api('/announcements/detail',{id});
  if(!d.success){toast(d.message,'error');return;}
  const r=d.data;
  document.getElementById('ann-modal-title').textContent='공지사항 수정';
  document.getElementById('ann-id').value=r.id;
  document.getElementById('ann-title').value=r.title;
  document.getElementById('ann-content').value=r.content;
  document.getElementById('ann-link').value=r.link||'';
  document.getElementById('ann-cat').value=r.category;
  document.getElementById('ann-active').value=r.is_active;
  document.getElementById('ann-pin').checked=r.is_pinned==1;
  document.getElementById('ann-img-preview').innerHTML=r.image?`<img src="${BASE_URL+'/uploads/'+r.image}" style="max-height:80px;border-radius:4px">`:'';
  openModal('ann-modal');
}
async function saveAnn(){
  const id=document.getElementById('ann-id').value;
  const fd=new FormData();
  if(id)fd.append('id',id);
  fd.append('title',document.getElementById('ann-title').value);
  fd.append('content',document.getElementById('ann-content').value);
  fd.append('link',document.getElementById('ann-link').value);
  fd.append('category',document.getElementById('ann-cat').value);
  fd.append('is_active',document.getElementById('ann-active').value);
  fd.append('is_pinned',document.getElementById('ann-pin').checked?1:0);
  const img=document.getElementById('ann-img').files[0];if(img)fd.append('image',img);
  const btn=document.getElementById('ann-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+(id?'/announcements/update':'/announcements/create'),{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('ann-modal');location.reload();}else toast(d.message,'error');
}
async function deleteRow(id){
  confirmAction('삭제하시겠습니까?',async()=>{
    const d=await api('/announcements/delete',{id});
    if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}
    else toast(d.message,'error');
  });
}
async function togglePin(id){
  const d=await api('/announcements/toggle-pin',{id});
  if(d.success){toast('변경되었습니다.');location.reload();}else toast(d.message,'error');
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
