<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="page-header-row">
  <h1 class="page-title-main"><i class="fas fa-church"></i> 예배 & 교통 관리</h1>
</div>

<!-- 탭 -->
<div class="tab-bar">
  <button class="tab-btn active" onclick="switchTab('service-tab',this)"><i class="fas fa-clock"></i> 예배 시간</button>
  <button class="tab-btn" onclick="switchTab('shuttle-tab',this)"><i class="fas fa-bus"></i> 셔틀버스</button>
  <button class="tab-btn" onclick="switchTab('parking-tab',this)"><i class="fas fa-parking"></i> 주차 안내</button>
  <button class="tab-btn" onclick="switchTab('banner-tab',this)"><i class="fas fa-image"></i> 배너</button>
</div>

<!-- ── 예배 시간 탭 ── -->
<div id="service-tab" class="tab-content">
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-clock"></i> 예배 시간표</h2>
      <?php if(hasPerm('onlinegiving.create')): ?>
      <button class="btn btn-primary btn-sm" onclick="openServiceModal()"><i class="fas fa-plus"></i> 추가</button>
      <?php endif; ?>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>카테고리</th><th>예배명</th><th>요일</th><th>시간</th><th style="width:60px">순서</th><th style="width:60px">상태</th><th style="width:90px">관리</th></tr></thead>
        <tbody id="service-tbody">
          <?php foreach($serviceTimes as $s): ?>
          <tr data-id="<?= $s['id'] ?>">
            <td><span class="badge badge-blue"><?= htmlspecialchars($s['category']) ?></span></td>
            <td class="fw-500"><?= htmlspecialchars($s['name']) ?></td>
            <td class="text-muted"><?= htmlspecialchars($s['day']??'') ?></td>
            <td><?= htmlspecialchars($s['time']) ?></td>
            <td class="text-muted"><?= $s['sort_order'] ?></td>
            <td><span class="badge <?= $s['is_active']?'badge-green':'badge-gray' ?>"><?= $s['is_active']?'활성':'비활성' ?></span></td>
            <td>
              <?php if(hasPerm('onlinegiving.edit')): ?>
              <button class="btn btn-ghost btn-sm" onclick="editService(<?= $s['id'] ?>)"><i class="fas fa-edit"></i></button>
              <?php endif; ?>
              <?php if(hasPerm('onlinegiving.delete')): ?>
              <button class="btn btn-danger btn-sm" onclick="deleteService(<?= $s['id'] ?>)"><i class="fas fa-trash"></i></button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($serviceTimes)): ?><tr><td colspan="7" class="empty-td">예배 시간이 없습니다.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ── 셔틀버스 탭 ── -->
<div id="shuttle-tab" class="tab-content hidden">
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-bus"></i> 셔틀버스 시간표</h2>
      <?php if(hasPerm('onlinegiving.create')): ?>
      <button class="btn btn-primary btn-sm" onclick="openShuttleModal()"><i class="fas fa-plus"></i> 추가</button>
      <?php endif; ?>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>방향</th><th>시간</th><th>구분</th><th style="width:60px">순서</th><th style="width:60px">상태</th><th style="width:90px">관리</th></tr></thead>
        <tbody id="shuttle-tbody">
          <?php foreach($shuttleBus as $s): ?>
          <tr data-id="<?= $s['id'] ?>">
            <td><?php $dir=$s['direction']==='finch_to_church'; ?><span class="badge <?= $dir?'badge-blue':'badge-purple' ?>"><?= $dir?'Finch→교회':'교회→Finch' ?></span></td>
            <td class="fw-500"><?= htmlspecialchars($s['time']) ?></td>
            <td class="text-muted"><?= htmlspecialchars($s['service_label']) ?></td>
            <td class="text-muted"><?= $s['sort_order'] ?></td>
            <td><span class="badge <?= $s['is_active']?'badge-green':'badge-gray' ?>"><?= $s['is_active']?'활성':'비활성' ?></span></td>
            <td>
              <?php if(hasPerm('onlinegiving.edit')): ?>
              <button class="btn btn-ghost btn-sm" onclick="editShuttle(<?= $s['id'] ?>)"><i class="fas fa-edit"></i></button>
              <?php endif; ?>
              <?php if(hasPerm('onlinegiving.delete')): ?>
              <button class="btn btn-danger btn-sm" onclick="deleteShuttle(<?= $s['id'] ?>)"><i class="fas fa-trash"></i></button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($shuttleBus)): ?><tr><td colspan="6" class="empty-td">셔틀버스 시간이 없습니다.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ── 주차 안내 탭 ── -->
<div id="parking-tab" class="tab-content hidden">
  <div class="two-col-parking">
    <!-- 주차 텍스트 -->
    <div class="card">
      <div class="card-header">
        <h2><i class="fas fa-align-left"></i> 주차 안내 텍스트</h2>
        <?php if(hasPerm('onlinegiving.create')): ?>
        <button class="btn btn-primary btn-sm" onclick="openParkingModal()"><i class="fas fa-plus"></i> 추가</button>
        <?php endif; ?>
      </div>
      <div id="parking-list" class="sortable-list">
        <?php foreach($parkingItems as $p): ?>
        <div class="parking-item" data-id="<?= $p['id'] ?>">
          <div class="park-drag"><i class="fas fa-grip-vertical"></i></div>
          <div class="park-content"><?= htmlspecialchars(mb_substr($p['content'],0,120)) ?>...</div>
          <div style="display:flex;gap:4px;align-items:center;">
            <span class="badge <?= $p['is_active']?'badge-green':'badge-gray' ?>"><?= $p['is_active']?'활성':'비활성' ?></span>
            <?php if(hasPerm('onlinegiving.edit')): ?><button class="btn btn-ghost btn-sm" onclick="editParking(<?= $p['id'] ?>)"><i class="fas fa-edit"></i></button><?php endif; ?>
            <?php if(hasPerm('onlinegiving.delete')): ?><button class="btn btn-danger btn-sm" onclick="deleteParking(<?= $p['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($parkingItems)): ?><div class="empty-td">주차 안내가 없습니다.</div><?php endif; ?>
      </div>
    </div>

    <!-- 주차 지도 -->
    <div class="card">
      <div class="card-header"><h2><i class="fas fa-map"></i> 주차장 지도</h2></div>
      <div class="card-body">
        <div class="img-upload-box" id="map-img-box">
          <img id="map-img-preview" src="<?= htmlspecialchars($parkingMap['image_url']??'') ?>" style="<?= ($parkingMap['image_url']??'')?'':'display:none' ?>;max-width:100%;border-radius:6px;">
          <div id="map-img-ph" class="img-ph" style="<?= ($parkingMap['image_url']??'')?'display:none':'' ?>"><i class="fas fa-map fa-2x" style="opacity:.2"></i><span style="font-size:12px">지도 이미지</span></div>
        </div>
        <input type="file" id="map-image" accept="image/*" style="margin-top:10px;width:100%" onchange="previewMapImg(this)">
        <div class="form-group" style="margin-top:12px"><label class="form-label">대체 텍스트</label>
          <input class="form-control" id="map-alt" value="<?= htmlspecialchars($parkingMap['alt_text']??'') ?>" placeholder="밀알교회 주차장 안내 지도"></div>
        <?php if(hasPerm('onlinegiving.edit')): ?>
        <button class="btn btn-primary" style="width:100%;margin-top:8px" onclick="saveMap()"><i class="fas fa-save"></i> 저장</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- ── 배너 탭 ── -->
<div id="banner-tab" class="tab-content hidden">
  <div class="card" style="max-width:500px">
    <div class="card-header"><h2><i class="fas fa-image"></i> 배너 이미지</h2></div>
    <div class="card-body">
      <div class="img-upload-box" id="banner-img-box">
        <img id="banner-img-preview" src="<?= htmlspecialchars($banner['image_url']??'') ?>" style="<?= ($banner['image_url']??'')?'':'display:none' ?>;max-width:100%;border-radius:6px;">
        <div id="banner-img-ph" class="img-ph" style="<?= ($banner['image_url']??'')?'display:none':'' ?>"><i class="fas fa-image fa-2x" style="opacity:.2"></i><span style="font-size:12px">배너 이미지</span></div>
      </div>
      <input type="file" id="banner-image" accept="image/*" style="margin-top:10px;width:100%" onchange="previewBannerImg(this)">
      <div class="form-group" style="margin-top:12px"><label class="form-label">대체 텍스트</label>
        <input class="form-control" id="banner-alt" value="<?= htmlspecialchars($banner['alt_text']??'') ?>" placeholder="교회같은 가정, 가정같은 교회"></div>
      <?php if(hasPerm('onlinegiving.edit')): ?>
      <button class="btn btn-primary" style="width:100%;margin-top:8px" onclick="saveBanner()"><i class="fas fa-save"></i> 저장</button>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- 예배 시간 모달 -->
<div id="service-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:500px">
    <div class="modal-header"><h3 id="svc-modal-title">예배 시간 추가</h3>
      <button class="modal-close" onclick="closeModal('service-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="svc-id">
      <div class="form-group"><label class="form-label">카테고리<span class="req">*</span></label>
        <select class="form-control" id="svc-category">
          <option value="주일예배">주일예배</option>
          <option value="주중예배">주중예배</option>
          <option value="교육부예배">교육부예배</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">예배명<span class="req">*</span></label>
        <input class="form-control" id="svc-name" placeholder="1부"></div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">요일 (선택)</label>
          <input class="form-control" id="svc-day" placeholder="주일, 평일, 수요일 등"></div>
        <div class="form-group"><label class="form-label">시간<span class="req">*</span></label>
          <input class="form-control" id="svc-time" placeholder="오전 8:00"></div>
      </div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">순서</label>
          <input class="form-control" type="number" id="svc-order" value="0" min="0"></div>
        <div class="form-group"><label class="form-label">상태</label>
          <select class="form-control" id="svc-active"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('service-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveService()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<!-- 셔틀버스 모달 -->
<div id="shuttle-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:460px">
    <div class="modal-header"><h3 id="sh-modal-title">셔틀버스 시간 추가</h3>
      <button class="modal-close" onclick="closeModal('shuttle-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="sh-id">
      <div class="form-group"><label class="form-label">방향<span class="req">*</span></label>
        <select class="form-control" id="sh-direction">
          <option value="finch_to_church">Finch → 교회</option>
          <option value="church_to_finch">교회 → Finch</option>
        </select>
      </div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">시간<span class="req">*</span></label>
          <input class="form-control" id="sh-time" placeholder="오전 9시 15분"></div>
        <div class="form-group"><label class="form-label">구분<span class="req">*</span></label>
          <input class="form-control" id="sh-label" placeholder="2부"></div>
      </div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">순서</label>
          <input class="form-control" type="number" id="sh-order" value="0" min="0"></div>
        <div class="form-group"><label class="form-label">상태</label>
          <select class="form-control" id="sh-active"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('shuttle-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveShuttle()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<!-- 주차 안내 모달 -->
<div id="parking-modal" class="modal-overlay hidden">
  <div class="modal" style="max-width:520px">
    <div class="modal-header"><h3 id="pk-modal-title">주차 안내 추가</h3>
      <button class="modal-close" onclick="closeModal('parking-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="pk-id">
      <div class="form-group"><label class="form-label">안내 내용<span class="req">*</span></label>
        <textarea class="form-control" id="pk-content" rows="5" placeholder="주차 안내 내용을 입력하세요."></textarea></div>
      <div class="form-grid-2">
        <div class="form-group"><label class="form-label">순서</label>
          <input class="form-control" type="number" id="pk-order" value="0" min="0"></div>
        <div class="form-group"><label class="form-label">상태</label>
          <select class="form-control" id="pk-active"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('parking-modal')">취소</button>
      <button class="btn btn-primary" onclick="saveParking()"><i class="fas fa-save"></i> 저장</button>
    </div>
  </div>
</div>

<style>
.page-header-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.page-title-main{font-size:20px;font-weight:700;}
.tab-bar{display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);}
.tab-btn{padding:10px 20px;border:none;background:none;font-size:13px;font-weight:500;cursor:pointer;color:var(--text-muted);border-bottom:2px solid transparent;margin-bottom:-2px;display:flex;align-items:center;gap:6px;transition:all .15s;}
.tab-btn:hover{color:var(--primary);}.tab-btn.active{color:var(--primary);border-bottom-color:var(--primary);}
.tab-content.hidden{display:none;}
.fw-500{font-weight:500;}.text-muted{color:var(--text-muted);}
.empty-td{text-align:center;padding:32px;color:var(--text-muted);}
.two-col-parking{display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;}
.parking-item{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border);}
.park-drag{cursor:grab;color:var(--text-muted);padding-top:2px;}
.park-content{flex:1;font-size:13px;color:var(--text-muted);line-height:1.5;}
.img-upload-box{border:2px dashed var(--border);border-radius:8px;min-height:140px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#fafafa;}
.img-ph{display:flex;flex-direction:column;align-items:center;gap:8px;color:var(--text-muted);padding:20px;}
.sortable-ghost{opacity:.4;background:#ede9fe;}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
</style>

<script>
/* ── 탭 ── */
function switchTab(id, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.add('hidden'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById(id).classList.remove('hidden');
  btn.classList.add('active');
}

/* ── 예배 시간 ── */
function openServiceModal(data={}) {
  document.getElementById('svc-id').value       = data.id||'';
  document.getElementById('svc-category').value = data.category||'주일예배';
  document.getElementById('svc-name').value     = data.name||'';
  document.getElementById('svc-day').value      = data.day||'';
  document.getElementById('svc-time').value     = data.time||'';
  document.getElementById('svc-order').value    = data.sort_order||0;
  document.getElementById('svc-active').value   = data.is_active??1;
  document.getElementById('svc-modal-title').textContent = data.id?'예배 시간 수정':'예배 시간 추가';
  openModal('service-modal');
}
async function editService(id) {
  const d = await api('/online-giving/service-detail',{id});
  if(d.success) openServiceModal(d.data);
}
async function saveService() {
  const id = document.getElementById('svc-id').value;
  const d  = await api('/online-giving/'+(id?'service-update':'service-create'),{
    id, category:document.getElementById('svc-category').value,
    name:document.getElementById('svc-name').value,
    day:document.getElementById('svc-day').value,
    time:document.getElementById('svc-time').value,
    sort_order:document.getElementById('svc-order').value,
    is_active:document.getElementById('svc-active').value,
  });
  if(!d.success) return toast(d.message,'error');
  toast(d.message); closeModal('service-modal');
  if(id){ const tr=document.querySelector(`tr[data-id="${id}"]`);
    if(tr){ tr.querySelector('td:nth-child(2)').textContent=document.getElementById('svc-name').value;
            tr.querySelector('td:nth-child(3)').textContent=document.getElementById('svc-day').value;
            tr.querySelector('td:nth-child(4)').textContent=document.getElementById('svc-time').value; }
  } else location.reload();
}
async function deleteService(id) {
  confirmAction('이 예배 시간을 삭제하시겠습니까?', async()=>{
    const d=await api('/online-giving/service-delete',{id});
    if(!d.success) return toast(d.message,'error');
    toast(d.message); document.querySelector(`tr[data-id="${id}"]`)?.remove();
  });
}

/* ── 셔틀버스 ── */
function openShuttleModal(data={}) {
  document.getElementById('sh-id').value        = data.id||'';
  document.getElementById('sh-direction').value = data.direction||'finch_to_church';
  document.getElementById('sh-time').value      = data.time||'';
  document.getElementById('sh-label').value     = data.service_label||'';
  document.getElementById('sh-order').value     = data.sort_order||0;
  document.getElementById('sh-active').value    = data.is_active??1;
  document.getElementById('sh-modal-title').textContent = data.id?'셔틀버스 수정':'셔틀버스 시간 추가';
  openModal('shuttle-modal');
}
async function editShuttle(id) {
  const d=await api('/online-giving/shuttle-detail',{id});
  if(d.success) openShuttleModal(d.data);
}
async function saveShuttle() {
  const id=document.getElementById('sh-id').value;
  const d=await api('/online-giving/'+(id?'shuttle-update':'shuttle-create'),{
    id, direction:document.getElementById('sh-direction').value,
    time:document.getElementById('sh-time').value,
    service_label:document.getElementById('sh-label').value,
    sort_order:document.getElementById('sh-order').value,
    is_active:document.getElementById('sh-active').value,
  });
  if(!d.success) return toast(d.message,'error');
  toast(d.message); closeModal('shuttle-modal');
  if(id){ const tr=document.querySelector(`#shuttle-tbody tr[data-id="${id}"]`);
    if(tr){ tr.querySelector('td:nth-child(2)').textContent=document.getElementById('sh-time').value;
            tr.querySelector('td:nth-child(3)').textContent=document.getElementById('sh-label').value; }
  } else location.reload();
}
async function deleteShuttle(id) {
  confirmAction('이 셔틀버스 시간을 삭제하시겠습니까?', async()=>{
    const d=await api('/online-giving/shuttle-delete',{id});
    if(!d.success) return toast(d.message,'error');
    toast(d.message); document.querySelector(`#shuttle-tbody tr[data-id="${id}"]`)?.remove();
  });
}

/* ── 주차 안내 ── */
function openParkingModal(data={}) {
  document.getElementById('pk-id').value      = data.id||'';
  document.getElementById('pk-content').value = data.content||'';
  document.getElementById('pk-order').value   = data.sort_order||0;
  document.getElementById('pk-active').value  = data.is_active??1;
  document.getElementById('pk-modal-title').textContent = data.id?'주차 안내 수정':'주차 안내 추가';
  openModal('parking-modal');
}
async function editParking(id) {
  const d=await api('/online-giving/parking-detail',{id});
  if(d.success) openParkingModal(d.data);
}
async function saveParking() {
  const id=document.getElementById('pk-id').value;
  const d=await api('/online-giving/'+(id?'parking-update':'parking-create'),{
    id, content:document.getElementById('pk-content').value,
    sort_order:document.getElementById('pk-order').value,
    is_active:document.getElementById('pk-active').value,
  });
  if(!d.success) return toast(d.message,'error');
  toast(d.message); closeModal('parking-modal'); location.reload();
}
async function deleteParking(id) {
  confirmAction('이 주차 안내를 삭제하시겠습니까?', async()=>{
    const d=await api('/online-giving/parking-delete',{id});
    if(!d.success) return toast(d.message,'error');
    toast(d.message); document.querySelector(`.parking-item[data-id="${id}"]`)?.remove();
  });
}

/* ── 지도/배너 이미지 ── */
let _mapPending=null, _bannerPending=null;
function previewMapImg(input) {
  if(!input.files[0]) return; _mapPending=input.files[0];
  const url=URL.createObjectURL(input.files[0]);
  document.getElementById('map-img-preview').src=url; document.getElementById('map-img-preview').style.display='block';
  document.getElementById('map-img-ph').style.display='none';
}
function previewBannerImg(input) {
  if(!input.files[0]) return; _bannerPending=input.files[0];
  const url=URL.createObjectURL(input.files[0]);
  document.getElementById('banner-img-preview').src=url; document.getElementById('banner-img-preview').style.display='block';
  document.getElementById('banner-img-ph').style.display='none';
}
async function saveMap() {
  const fd=new FormData();
  fd.append('alt_text',document.getElementById('map-alt').value);
  fd.append('is_active',1);
  if(_mapPending) fd.append('image',_mapPending);
  const d=await apiUpload('/online-giving/parking-map-update',fd,'저장 중...');
  if(!d.success) return toast(d.message,'error'); toast(d.message);
}
async function saveBanner() {
  const fd=new FormData();
  fd.append('alt_text',document.getElementById('banner-alt').value);
  fd.append('is_active',1);
  if(_bannerPending) fd.append('image',_bannerPending);
  const d=await apiUpload('/online-giving/banner-update',fd,'저장 중...');
  if(!d.success) return toast(d.message,'error'); toast(d.message);
}

/* ── Sortable (주차) ── */
function pageInit() {
  const pl=document.getElementById('parking-list');
  if(pl && typeof Sortable!=='undefined') {
    Sortable.create(pl,{handle:'.park-drag',animation:150,ghostClass:'sortable-ghost',
      onEnd: async()=>{
        const orders=[...pl.querySelectorAll('.parking-item')].map((el,i)=>({id:el.dataset.id,order:i}));
        await api('/online-giving/parking-reorder',{orders:JSON.stringify(orders)});
      }
    });
  }
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
