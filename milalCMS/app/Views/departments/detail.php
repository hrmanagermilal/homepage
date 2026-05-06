<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=AuthMiddleware::hasPermission('departments.edit'); $canDelete=AuthMiddleware::hasPermission('departments.delete'); $canCreate=AuthMiddleware::hasPermission('departments.create'); ?>

<div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap">
  <a href="<?= BASE_URL ?>/departments" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
  <a href="<?= BASE_URL ?>/departments/announcements?dept_id=<?= $dept['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-bullhorn"></i> 부서 공지</a>
</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start">
  <!-- 좌: 대표 카드 -->
  <div class="card">
    <div class="card-body" style="padding:24px;text-align:center">
      <?php if($dept['image']): ?>
      <img src="<?= UPLOAD_URL.htmlspecialchars($dept['image']) ?>" style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;margin-bottom:14px" alt="">
      <?php else: ?>
      <div style="width:80px;height:80px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:28px;color:#fff"><i class="fas fa-sitemap"></i></div>
      <?php endif; ?>
      <h2 style="font-size:18px;font-weight:600"><?= htmlspecialchars($dept['name']) ?></h2>
      <div style="margin:8px 0">
        <span class="badge <?= $dept['department_type']==='nextgen'?'badge-blue':'badge-purple' ?>"><?= $dept['department_type']==='nextgen'?'다음세대':'사역부서' ?></span>
        <span class="badge <?= $dept['is_active']?'badge-green':'badge-gray' ?>" style="margin-left:4px"><?= $dept['is_active']?'활성':'비활성' ?></span>
      </div>
      <?php if($dept['description']): ?><p style="font-size:13px;color:var(--text-muted);margin-top:8px"><?= htmlspecialchars($dept['description']) ?></p><?php endif; ?>
    </div>
    <div style="padding:0 16px 16px;display:flex;flex-direction:column;gap:8px">
      <?php if($canEdit): ?><button class="btn btn-warning" onclick="openEdit()"><i class="fas fa-pen"></i> 수정</button><?php endif; ?>
      <?php if($canDelete): ?><button class="btn btn-danger" onclick="deleteDept(<?= $dept['id'] ?>)"><i class="fas fa-trash"></i> 삭제</button><?php endif; ?>
    </div>
  </div>

  <!-- 우: 상세 정보 -->
  <div style="display:flex;flex-direction:column;gap:16px">
    <!-- 예배 정보 -->
    <div class="card">
      <div class="card-header"><h2><i class="fas fa-church" style="color:var(--primary)"></i> 예배 정보</h2></div>
      <div class="card-body" style="padding:0">
        <table style="width:100%;border-collapse:collapse;font-size:14px">
          <?php $infoRows=[['예배 요일',$dept['worship_day']??''],['예배 시간',$dept['worship_time']??''],['예배 장소',$dept['worship_location']??''],['연령대',$dept['age_group']??''],['사역 유형',$dept['ministry_type']??'']]; foreach($infoRows as [$label,$val]) if($val): ?>
          <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:10px 16px;font-weight:500;color:var(--text-muted);width:120px;white-space:nowrap"><?= $label ?></td>
            <td style="padding:10px 16px"><?= htmlspecialchars($val) ?></td>
          </tr>
          <?php endif; endforeach; ?>
        </table>
      </div>
    </div>

    <!-- 담당자 정보 -->
    <?php if($dept['clergy_name']||$dept['clergy_position']||$dept['clergy_phone']): ?>
    <div class="card">
      <div class="card-header"><h2><i class="fas fa-user-tie" style="color:var(--primary)"></i> 담당자 정보</h2></div>
      <div class="card-body" style="padding:0">
        <table style="width:100%;border-collapse:collapse;font-size:14px">
          <?php foreach([['담당자명',$dept['clergy_name']??''],['직책',$dept['clergy_position']??''],['연락처',$dept['clergy_phone']??'']] as [$label,$val]) if($val): ?>
          <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:10px 16px;font-weight:500;color:var(--text-muted);width:120px"><?= $label ?></td>
            <td style="padding:10px 16px"><?= htmlspecialchars($val) ?></td>
          </tr>
          <?php endif; endforeach; ?>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <!-- 최근 공지 -->
    <div class="card">
      <div class="card-header">
        <h2><i class="fas fa-bullhorn" style="color:var(--primary)"></i> 최근 공지사항</h2>
        <a href="<?= BASE_URL ?>/departments/announcements?dept_id=<?= $dept['id'] ?>" class="btn btn-ghost btn-sm">전체 보기</a>
      </div>
      <div class="card-body" style="padding:0">
        <?php if(empty($annData['rows'])): ?>
        <div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px">공지사항 없음</div>
        <?php else: ?>
        <table style="width:100%;border-collapse:collapse;font-size:13px">
          <thead><tr><th style="padding:8px 14px;background:var(--bg);text-align:left">제목</th><th style="padding:8px 14px;background:var(--bg);text-align:left;white-space:nowrap">등록일</th></tr></thead>
          <tbody>
          <?php foreach($annData['rows'] as $r): ?>
          <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:9px 14px"><?= htmlspecialchars($r['title']) ?></td>
            <td style="padding:9px 14px;color:var(--text-muted);white-space:nowrap"><?= date('Y-m-d',strtotime($r['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <!-- 페이징 -->
        <?php if($pagination['total_pages']>1): ?>
        <div style="padding:12px 14px;display:flex;gap:4px">
          <?php if($pagination['has_prev']): ?><a href="?id=<?= $dept['id'] ?>&page=<?= $pagination['current']-1 ?>" class="btn btn-ghost btn-sm">&laquo;</a><?php endif; ?>
          <?php for($p=$pagination['start_page'];$p<=$pagination['end_page'];$p++): ?>
          <<?= $p===$pagination['current']?'span':'a href="?id='.$dept['id'].'&page='.$p.'"' ?> class="btn btn-sm <?= $p===$pagination['current']?'btn-primary':'btn-ghost' ?>"><?= $p ?></<?= $p===$pagination['current']?'span':'a' ?>>
          <?php endfor; ?>
          <?php if($pagination['has_next']): ?><a href="?id=<?= $dept['id'] ?>&page=<?= $pagination['current']+1 ?>" class="btn btn-ghost btn-sm">&raquo;</a><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay hidden" id="dept-modal">
  <div class="modal modal-xl">
    <div class="modal-header"><h3>부서 수정</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('dept-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label class="form-label">부서명 <span class="req">*</span></label><input type="text" id="d-name" class="form-control" value="<?= htmlspecialchars($dept['name']) ?>"></div>
        <div class="form-group"><label class="form-label">유형</label><select id="d-type" class="form-control"><option value="nextgen" <?= $dept['department_type']==='nextgen'?'selected':'' ?>>다음세대</option><option value="ministry" <?= $dept['department_type']==='ministry'?'selected':'' ?>>사역부서</option></select></div>
        <div class="form-group"><label class="form-label">연령대</label><input type="text" id="d-age" class="form-control" value="<?= htmlspecialchars($dept['age_group']??'') ?>"></div>
      </div>
      <div class="form-group"><label class="form-label">설명</label><textarea id="d-desc" class="form-control" rows="3"><?= htmlspecialchars($dept['description']??'') ?></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">예배 요일</label><input type="text" id="d-wday" class="form-control" value="<?= htmlspecialchars($dept['worship_day']??'') ?>"></div>
        <div class="form-group"><label class="form-label">예배 시간</label><input type="text" id="d-wtime" class="form-control" value="<?= htmlspecialchars($dept['worship_time']??'') ?>"></div>
        <div class="form-group"><label class="form-label">예배 장소</label><input type="text" id="d-wloc" class="form-control" value="<?= htmlspecialchars($dept['worship_location']??'') ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">담당자명</label><input type="text" id="d-cname" class="form-control" value="<?= htmlspecialchars($dept['clergy_name']??'') ?>"></div>
        <div class="form-group"><label class="form-label">직책</label><input type="text" id="d-cpos" class="form-control" value="<?= htmlspecialchars($dept['clergy_position']??'') ?>"></div>
        <div class="form-group"><label class="form-label">연락처</label><input type="tel" id="d-cphone" class="form-control" value="<?= htmlspecialchars($dept['clergy_phone']??'') ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">이미지 교체</label><input type="file" id="d-img" class="form-control" accept="image/*"><?php if($dept['image']): ?><div style="margin-top:6px"><img src="<?= UPLOAD_URL.htmlspecialchars($dept['image']) ?>" style="max-height:60px;border-radius:4px"></div><?php endif; ?></div>
        <div class="form-group"><label class="form-label">정렬 순서</label><input type="number" id="d-order" class="form-control" value="<?= $dept['order']??0 ?>"></div>
        <div class="form-group"><label class="form-label">상태</label><select id="d-active" class="form-control"><option value="1" <?= $dept['is_active']?'selected':'' ?>>활성</option><option value="0" <?= !$dept['is_active']?'selected':'' ?>>비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('dept-modal')">취소</button><button class="btn btn-primary" id="d-save-btn" onclick="saveDept()">저장</button></div>
  </div>
</div>

<script>
function openEdit(){openModal('dept-modal');}
async function saveDept(){
  const fd=new FormData();
  fd.append('id','<?= $dept['id'] ?>');
  const m={department_type:'d-type',name:'d-name',description:'d-desc',age_group:'d-age',worship_day:'d-wday',worship_time:'d-wtime',worship_location:'d-wloc',clergy_name:'d-cname',clergy_position:'d-cpos',clergy_phone:'d-cphone',order:'d-order',is_active:'d-active'};
  for(const[k,eid]of Object.entries(m))fd.append(k,document.getElementById(eid).value);
  const img=document.getElementById('d-img').files[0];if(img)fd.append('image',img);
  const btn=document.getElementById('d-save-btn');btn.disabled=true;
  const d=await fetch(BASE_URL+'/departments/update',{method:'POST',body:fd}).then(r=>r.json());
  btn.disabled=false;
  if(d.success){toast(d.message);closeModal('dept-modal');location.reload();}else toast(d.message,'error');
}
async function deleteDept(id){
  confirmAction('이 부서를 삭제하시겠습니까?',async()=>{
    const d=await api('/departments/delete',{id});
    if(d.success){toast('삭제되었습니다.');location.href=BASE_URL+'/departments';}
    else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
