<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('news.edit'); $canCreate=AuthMiddleware::hasPermission('news.create'); $canDelete=AuthMiddleware::hasPermission('news.delete'); ?>

<div class="card">
  <div class="card-header">
    <div class="flex flex-center gap-12">
      <h2><i class="fas fa-newspaper" style="color:var(--primary)"></i> 뉴스 관리</h2>
      <div class="flex gap-8">
        <?php foreach([''=> '전체','news'=>'뉴스','update'=>'업데이트','photo'=>'사진'] as $val=>$label): ?>
        <a href="?category=<?= $val ?>" class="btn btn-sm <?= $cat===$val?'btn-primary':'btn-ghost' ?>"><?= $label ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php if($canCreate): ?><button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i>뉴스 추가</button><?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead><tr><th style="width:60px">이미지</th><th>제목</th><th>카테고리</th><th>작성자</th><th>태그</th><th>조회</th><th>등록일</th><th style="width:90px">관리</th></tr></thead>
      <tbody>
      <?php foreach($data['rows'] as $r):
        $catMap=['news'=>['badge-blue','뉴스'],'update'=>['badge-green','업데이트'],'photo'=>['badge-purple','사진']];
        [$bc,$bl]=$catMap[$r['category']]??['badge-gray','기타']; ?>
      <tr data-id="<?= $r['id'] ?>">
        <td><?php if($r['image']): ?><img src="<?= UPLOAD_URL.htmlspecialchars($r['image']) ?>" class="img-thumb" alt=""><?php else: ?><span class="text-muted">-</span><?php endif; ?></td>
        <td>
          <a href="<?= BASE_URL ?>/news/view?id=<?= $r['id'] ?>" style="color:var(--text);font-weight:500;display:block" class="truncate" style="max-width:240px">
            <?= htmlspecialchars($r['title']) ?>
          </a>
        </td>
        <td><span class="badge <?= $bc ?>"><?= $bl ?></span></td>
        <td class="text-sm"><?= htmlspecialchars($r['author']??'-') ?></td>
        <td class="text-sm text-muted truncate" style="max-width:120px"><?= htmlspecialchars($r['tags']??'') ?></td>
        <td class="text-sm text-muted"><?= number_format($r['views']) ?></td>
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
      <?php if($pagination['has_prev']): ?><a href="?page=<?= $pagination['current']-1 ?>&category=<?= $cat ?>">&laquo;</a><?php endif; ?>
      <?php for($p=$pagination['start_page'];$p<=$pagination['end_page'];$p++): ?><<?= $p===$pagination['current']?'span class="active"':'a href="?page='.$p.'&category='.$cat.'"' ?>><?= $p ?></<?= $p===$pagination['current']?'span':'a' ?>><?php endfor; ?>
      <?php if($pagination['has_next']): ?><a href="?page=<?= $pagination['current']+1 ?>&category=<?= $cat ?>">&raquo;</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<div class="modal-overlay hidden" id="news-modal">
  <div class="modal modal-lg">
    <div class="modal-header"><h3 id="news-modal-title">뉴스 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('news-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="news-id">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label><input type="text" id="n-title" class="form-control"></div>
      <div class="form-group"><label class="form-label">내용 <span class="req">*</span></label><textarea id="n-content" class="form-control" rows="6"></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">카테고리</label><select id="n-cat" class="form-control"><option value="news">뉴스</option><option value="update">업데이트</option><option value="photo">사진</option></select></div>
        <div class="form-group"><label class="form-label">작성자 <span class="text-muted text-sm">(미입력 시 로그인 계정)</span></label><input type="text" id="n-author" class="form-control" placeholder="<?= htmlspecialchars($_SESSION['name']??$_SESSION['name']??'') ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">이미지</label><input type="file" id="n-img" class="form-control" accept="image/*"><div id="n-img-preview" style="margin-top:8px"></div></div>
        <div class="form-group"><label class="form-label">태그 <span class="text-muted text-sm">(쉼표 구분)</span></label><input type="text" id="n-tags" class="form-control" placeholder="교회,설교,행사"></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('news-modal')">취소</button><button class="btn btn-primary" id="news-save-btn" onclick="saveNews()">저장</button></div>
  </div>
</div>
<script>
function openCreate(){
  document.getElementById('news-modal-title').textContent='뉴스 추가';
  document.getElementById('news-id').value='';
  ['n-title','n-content','n-tags'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('n-author').value='';
  document.getElementById('n-cat').value='news';
  document.getElementById('n-img-preview').innerHTML='';
  openModal('news-modal');
}
async function openEdit(id){
  const d=await api('/news/detail',{id});
  if(!d.success){toast(d.message,'error');return;}
  const r=d.data;
  document.getElementById('news-modal-title').textContent='뉴스 수정';
  document.getElementById('news-id').value=r.id;
  document.getElementById('n-title').value=r.title;
  document.getElementById('n-content').value=r.content;
  document.getElementById('n-cat').value=r.category;
  document.getElementById('n-author').value=r.author||'';
  document.getElementById('n-tags').value=r.tags||'';
  document.getElementById('n-img-preview').innerHTML=r.image?`<img src="${BASE_URL+'/uploads/'+r.image}" style="max-height:80px;border-radius:4px">`:'';
  openModal('news-modal');
}
async function saveNews(){
  const id=document.getElementById('news-id').value;
  const fd=new FormData();
  if(id)fd.append('id',id);
  fd.append('title',document.getElementById('n-title').value);
  fd.append('content',document.getElementById('n-content').value);
  fd.append('category',document.getElementById('n-cat').value);
  fd.append('author',document.getElementById('n-author').value);
  fd.append('tags',document.getElementById('n-tags').value);
  const img=document.getElementById('n-img').files[0];if(img)fd.append('image',img);
  const btn=document.getElementById('news-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+(id?'/news/update':'/news/create'),{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('news-modal');location.reload();}else toast(d.message,'error');
}
async function deleteRow(id){
  confirmAction('이 뉴스를 삭제하시겠습니까?',async()=>{
    const d=await api('/news/delete',{id});
    if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
