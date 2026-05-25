<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<?php $canEdit=hasPerm('bulletins.edit'); $canCreate=hasPerm('bulletins.create'); $canDelete=hasPerm('bulletins.delete'); ?>

<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-book-open" style="color:var(--primary)"></i> 주보 관리</h2>
    <?php if($canCreate): ?><button class="btn btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i>주보 추가</button><?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div class="table-wrap"><table>
      <thead><tr><th>제목</th><th>연도</th><th>주차</th><th>PDF</th><th>이미지</th><th>등록일</th><th style="width:130px">관리</th></tr></thead>
      <tbody>
      <?php foreach($data['rows'] as $r): ?>
      <tr data-id="<?= $r['id'] ?>">
        <td>
          <a href="<?= BASE_URL ?>/bulletins/view?id=<?= $r['id'] ?>" style="color:var(--text);font-weight:500">
            <?= htmlspecialchars($r['title']) ?>
          </a>
        </td>
        <td><?= $r['year']??'-' ?></td>
        <td><?= $r['week_number'] ? $r['week_number'].'주차' : '-' ?></td>
        <td>
          <?php if($r['attachment'] ?? null): ?>
          <a href="<?= BASE_URL.htmlspecialchars($r['attachment']) ?>" target="_blank" class="btn btn-ghost btn-sm" title="PDF 열기">
            <i class="fas fa-file-pdf" style="color:#dc2626"></i>
          </a>
          <?php else: ?><span class="text-muted">—</span><?php endif; ?>
        </td>
        <td><span class="badge badge-blue"><?= $r['image_count'] ?>장</span></td>
        <td class="text-sm text-muted"><?= date('Y-m-d',strtotime($r['created_at'])) ?></td>
        <td><div class="flex gap-8">
          <a href="<?= BASE_URL ?>/bulletins/view?id=<?= $r['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-eye"></i></a>
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
      <?php for($p=$pagination['start_page'];$p<=$pagination['end_page'];$p++): ?>
      <<?= $p===$pagination['current']?'span class="active"':'a href="?page='.$p.'"' ?>><?= $p ?></<?= $p===$pagination['current']?'span':'a' ?>>
      <?php endfor; ?>
      <?php if($pagination['has_next']): ?><a href="?page=<?= $pagination['current']+1 ?>">&raquo;</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- 추가 모달 -->
<div class="modal-overlay hidden" id="bulletin-modal">
  <div class="modal" style="max-width:560px">
    <div class="modal-header">
      <h3>주보 추가</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('bulletin-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="bul-id">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label>
        <input type="text" id="bul-title" class="form-control" placeholder="예: 2025년 28주차 주보"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">연도</label>
          <input type="number" id="bul-year" class="form-control" value="<?= date('Y') ?>"></div>
        <div class="form-group"><label class="form-label">주차</label>
          <input type="number" id="bul-week" class="form-control" placeholder="예: 28"></div>
      </div>

      <!-- PDF (이미지보다 앞에 배치) -->
      <div class="form-group">
        <label class="form-label"><i class="fas fa-file-pdf" style="color:#dc2626"></i> PDF 파일 (최대 10MB)</label>
        <div id="bul-pdf-preview" style="display:none;padding:8px 12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;margin-bottom:8px;font-size:12px">
          <i class="fas fa-file-pdf" style="color:#dc2626"></i>
          <span id="bul-pdf-name"></span>
          <button type="button" onclick="clearPdf()" style="float:right;background:none;border:none;cursor:pointer;color:var(--danger)"><i class="fas fa-times"></i></button>
        </div>
        <input type="file" id="bul-pdf" class="form-control" accept=".pdf,application/pdf" onchange="previewPdf(this)">
      </div>

      <!-- 이미지 (미리보기 후 저장) -->
      <div class="form-group">
        <label class="form-label">이미지 일괄 업로드 <span class="text-muted text-sm">(여러 장, 미리보기 후 저장)</span></label>
        <input type="file" id="bul-imgs" class="form-control" accept="image/*" multiple onchange="previewBulletinImgs(this)">
        <div id="bul-img-preview-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:8px;margin-top:8px"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('bulletin-modal')">취소</button>
      <button class="btn btn-primary" id="bul-save-btn" onclick="saveBulletin()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<!-- 수정 모달 -->
<div class="modal-overlay hidden" id="bul-edit-modal">
  <div class="modal" style="max-width:500px">
    <div class="modal-header">
      <h3>주보 수정</h3>
      <button class="btn btn-ghost btn-icon" onclick="closeModal('bul-edit-modal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="bul-edit-id">
      <div class="form-group"><label class="form-label">제목 <span class="req">*</span></label>
        <input type="text" id="bul-edit-title" class="form-control"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">연도</label>
          <input type="number" id="bul-edit-year" class="form-control"></div>
        <div class="form-group"><label class="form-label">주차</label>
          <input type="number" id="bul-edit-week" class="form-control"></div>
      </div>
      <!-- PDF 수정 -->
      <div class="form-group">
        <label class="form-label"><i class="fas fa-file-pdf" style="color:#dc2626"></i> PDF 파일</label>
        <div id="edit-current-pdf" style="display:none;padding:8px 12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;margin-bottom:8px;font-size:12px">
          <i class="fas fa-file-pdf" style="color:#dc2626"></i>
          <span id="edit-current-pdf-name">현재 PDF</span>
          <a id="edit-current-pdf-link" href="#" target="_blank" style="margin-left:8px;font-size:11px">열기</a>
          <button type="button" onclick="removeCurrentPdf()" style="float:right;background:none;border:none;cursor:pointer;color:var(--danger)"><i class="fas fa-times"></i></button>
        </div>
        <div id="edit-new-pdf-preview" style="display:none;padding:8px 12px;background:#fffbeb;border:1px solid #fcd34d;border-radius:6px;margin-bottom:8px;font-size:12px">
          <i class="fas fa-arrow-right" style="color:#d97706"></i> 교체될 PDF:
          <span id="edit-new-pdf-name"></span>
          <button type="button" onclick="clearEditPdf()" style="float:right;background:none;border:none;cursor:pointer;color:var(--danger)"><i class="fas fa-times"></i></button>
        </div>
        <input type="file" id="bul-edit-pdf" class="form-control" accept=".pdf,application/pdf" onchange="previewEditPdf(this)">
      </div>
      <input type="hidden" id="bul-remove-pdf" value="0">
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('bul-edit-modal')">취소</button>
      <button class="btn btn-primary" id="bul-edit-btn" onclick="saveEdit()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<script>
const UPLOAD_URL_BASE = '<?= BASE_URL ?>';
let _pendingPdf = null;
let _editPendingPdf = null;

/* ── PDF 미리보기 ── */
function previewPdf(input) {
  if(!input.files[0]) return;
  _pendingPdf = input.files[0];
  document.getElementById('bul-pdf-name').textContent = input.files[0].name;
  document.getElementById('bul-pdf-preview').style.display = '';
}
function clearPdf() {
  _pendingPdf = null;
  document.getElementById('bul-pdf').value = '';
  document.getElementById('bul-pdf-preview').style.display = 'none';
}
function previewEditPdf(input) {
  if(!input.files[0]) return;
  _editPendingPdf = input.files[0];
  document.getElementById('edit-new-pdf-name').textContent = input.files[0].name;
  document.getElementById('edit-new-pdf-preview').style.display = '';
}
function clearEditPdf() {
  _editPendingPdf = null;
  document.getElementById('bul-edit-pdf').value = '';
  document.getElementById('edit-new-pdf-preview').style.display = 'none';
}
function removeCurrentPdf() {
  document.getElementById('edit-current-pdf').style.display = 'none';
  document.getElementById('bul-remove-pdf').value = '1';
}

/* ── 이미지 미리보기 (저장 전) ── */
function previewBulletinImgs(input) {
  const grid = document.getElementById('bul-img-preview-grid');
  grid.innerHTML = '';
  [...input.files].forEach((f,i) => {
    const url = URL.createObjectURL(f);
    grid.innerHTML += `<div style="position:relative;border-radius:6px;overflow:hidden;aspect-ratio:3/4;background:#f3f4f6">
      <img src="${url}" style="width:100%;height:100%;object-fit:cover">
      <span style="position:absolute;bottom:2px;left:2px;font-size:9px;background:rgba(0,0,0,.5);color:#fff;padding:1px 4px;border-radius:3px">${i+1}</span>
    </div>`;
  });
}

/* ── 추가 저장 ── */
function openCreate() {
  _pendingPdf = null;
  document.getElementById('bul-title').value = '';
  document.getElementById('bul-year').value  = '<?= date('Y') ?>';
  document.getElementById('bul-week').value  = '';
  document.getElementById('bul-pdf').value   = '';
  document.getElementById('bul-imgs').value  = '';
  document.getElementById('bul-pdf-preview').style.display = 'none';
  document.getElementById('bul-img-preview-grid').innerHTML = '';
  openModal('bulletin-modal');
}

async function saveBulletin() {
  const title = document.getElementById('bul-title').value.trim();
  if(!title){toast('제목을 입력하세요.','error');return;}
  const fd = new FormData();
  fd.append('title',       title);
  fd.append('year',        document.getElementById('bul-year').value);
  fd.append('week_number', document.getElementById('bul-week').value);
  if(_pendingPdf) fd.append('attachment', _pendingPdf);
  const files = document.getElementById('bul-imgs').files;
  for(const f of files) fd.append('images[]', f);
  const btn = document.getElementById('bul-save-btn'); btn.disabled = true;
  showSpinner('주보 저장 중...');
  let d;
  try {
    const res = await fetch(BASE_URL+'/bulletins/create',{method:'POST',body:fd});
    d = await res.json();

    // call convert API if PDF was uploaded
    if(d.success && _pendingPdf){
      showSpinner('PDF 이미지 변환 중...');
      try{
        const fd2 = new URLSearchParams();
        fd2.append('file_path', d.data.stored_pdf_filename);
        fd2.append('bulletin_id', d.data.id);
        console.log('[transform-pdf] file_path:', d.data.stored_pdf_filename, 'bulletin_id:', d.data.id);
        const cr = await fetch('<?= rtrim(getenv("BACKEND_API_URL") ?: "http://localhost:8080", "/") ?>/api/bulletins/transform-pdf',
          {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:fd2});
        const cd = await cr.json();
        if(cd.success){
          toast(`PDF 이미지 ${cd.data.saved_images?.length||0}장 변환 완료`);
          console.log('[transform-pdf] saved images:', cd.data.saved_images?.map(i=>i.image_url));
        } else {
          toast('PDF 변환 실패: '+(cd.message||''), 'warning');
        }
      }catch(e){
        console.error('[transform-pdf] error:', e);
        toast('PDF 변환 중 오류가 발생했습니다.','warning');
      }finally{
        hideSpinner();
      }
    }
  } catch(e) {
    hideSpinner(); btn.disabled = false;
    toast('서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.','error');
    return;
  }
  hideSpinner(); btn.disabled = false;
  if(d.success){toast(d.message);closeModal('bulletin-modal');location.reload();}
  else toast(d.message||'저장에 실패했습니다.','error');
}

/* ── 수정 ── */
async function openEdit(id) {
  const d = await api('/bulletins/detail',{id});
  if(!d.success){toast(d.message,'error');return;}
  const r = d.data;
  _editPendingPdf = null;
  document.getElementById('bul-edit-id').value    = r.id;
  document.getElementById('bul-edit-title').value = r.title;
  document.getElementById('bul-edit-year').value  = r.year||'<?= date('Y') ?>';
  document.getElementById('bul-edit-week').value  = r.week_number||'';
  document.getElementById('bul-edit-pdf').value   = '';
  document.getElementById('bul-remove-pdf').value = '0';
  document.getElementById('edit-new-pdf-preview').style.display = 'none';
  // 현재 PDF 표시
  const curPdf = document.getElementById('edit-current-pdf');
  if(r.attachment){
    document.getElementById('edit-current-pdf-name').textContent = r.attachment.split('/').pop();
    document.getElementById('edit-current-pdf-link').href = UPLOAD_URL_BASE + r.attachment;
    curPdf.style.display = '';
  } else {
    curPdf.style.display = 'none';
  }
  openModal('bul-edit-modal');
}

async function saveEdit() {
  const fd = new FormData();
  fd.append('id',            document.getElementById('bul-edit-id').value);
  fd.append('title',         document.getElementById('bul-edit-title').value);
  fd.append('year',          document.getElementById('bul-edit-year').value);
  fd.append('week_number',   document.getElementById('bul-edit-week').value);
  fd.append('remove_attachment', document.getElementById('bul-remove-pdf').value);
  if(_editPendingPdf) fd.append('attachment', _editPendingPdf);
  const btn = document.getElementById('bul-edit-btn'); btn.disabled = true;
  let d;
  try {
    const res = await fetch(BASE_URL+'/bulletins/update',{method:'POST',body:fd});
    d = await res.json();
  } catch(e) {
    btn.disabled = false;
    toast('서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.','error');
    return;
  }
  btn.disabled = false;
  if(d.success){
    toast(d.message); closeModal('bul-edit-modal');
    const id2 = document.getElementById('bul-edit-id').value;
    const tr  = document.querySelector(`tr[data-id="${id2}"]`);
    if(tr){
      const a = tr.querySelector('td:first-child a');
      if(a) a.textContent = document.getElementById('bul-edit-title').value;
      tr.querySelector('td:nth-child(2)').textContent = document.getElementById('bul-edit-year').value||'-';
      const wk = document.getElementById('bul-edit-week').value;
      tr.querySelector('td:nth-child(3)').textContent = wk ? wk+'주차' : '-';
    }
  } else toast(d.message,'error');
}

async function deleteRow(id) {
  confirmAction('주보와 모든 파일을 삭제하시겠습니까?', async()=>{
    const d = await api('/bulletins/delete',{id});
    if(d.success){toast('삭제되었습니다.');document.querySelector(`tr[data-id="${id}"]`)?.remove();}
    else toast(d.message,'error');
  });
}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
