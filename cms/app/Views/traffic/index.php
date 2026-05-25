<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="tab-bar">
  <button class="tab-btn active" onclick="switchTab('shuttle-tab',this)"><i class="fas fa-bus"></i> 셔틀버스</button>
  <button class="tab-btn" onclick="switchTab('parking-tab',this)"><i class="fas fa-parking"></i> 주차 안내</button>
</div>

<!-- 셔틀버스 -->
<div id="shuttle-tab" class="tab-content">
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-bus" style="color:var(--primary)"></i> 셔틀버스 시간표</h2>
      <?php if(hasPerm('traffic.create')): ?><button class="btn btn-primary btn-sm" onclick="openShuttleModal()"><i class="fas fa-plus"></i> 추가</button><?php endif; ?>
    </div>
    <div class="table-wrap"><table>
      <thead><tr><th>방향</th><th>시간</th><th>구분</th><th style="width:60px">순서</th><th style="width:60px">상태</th><th style="width:90px">관리</th></tr></thead>
      <tbody id="shuttle-tbody">
        <?php foreach($shuttleBus as $s): ?>
        <tr data-id="<?= $s['id'] ?>">
          <td><?php $dir=$s['direction']==='finch_to_church'; ?><span class="badge <?= $dir?'badge-blue':'badge-purple' ?>"><?= $dir?'Finch→교회':'교회→Finch' ?></span></td>
          <td class="fw-500"><?= htmlspecialchars($s['time']) ?></td>
          <td class="text-muted"><?= htmlspecialchars($s['service_label']) ?></td>
          <td class="text-muted"><?= $s['sort_order'] ?></td>
          <td><span class="badge <?= $s['is_active']?'badge-green':'badge-gray' ?>"><?= $s['is_active']?'활성':'비활성' ?></span></td>
          <td><div class="flex gap-8">
            <?php if(hasPerm('traffic.edit')): ?><button class="btn btn-warning btn-sm btn-icon" onclick="editShuttle(<?= $s['id'] ?>)"><i class="fas fa-pen"></i></button><?php endif; ?>
            <?php if(hasPerm('traffic.delete')): ?><button class="btn btn-danger btn-sm btn-icon" onclick="deleteShuttle(<?= $s['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($shuttleBus)): ?><tr><td colspan="6" style="text-align:center;padding:30px;color:var(--text-muted)">등록된 셔틀버스 정보가 없습니다.</td></tr><?php endif; ?>
      </tbody>
    </table></div>
  </div>
</div>

<!-- 주차 안내 -->
<div id="parking-tab" class="tab-content hidden">
  <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">
    <div class="card">
      <div class="card-header">
        <h2><i class="fas fa-align-left" style="color:var(--primary)"></i> 주차 안내 텍스트</h2>
        <?php if(hasPerm('traffic.create')): ?><button class="btn btn-primary btn-sm" onclick="openParkingModal()"><i class="fas fa-plus"></i> 추가</button><?php endif; ?>
      </div>
      <div id="parking-list">
        <?php foreach($parkingItems as $p): ?>
        <div class="parking-item" data-id="<?= $p['id'] ?>" style="display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border)">
          <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
          <div style="flex:1;font-size:13px;color:var(--text-muted);line-height:1.5"><?= nl2br(htmlspecialchars($p['content'])) ?></div>
          <div class="flex gap-8">
            <?php if(hasPerm('traffic.edit')): ?><button class="btn btn-warning btn-sm btn-icon" onclick="editParking(<?= $p['id'] ?>)"><i class="fas fa-pen"></i></button><?php endif; ?>
            <?php if(hasPerm('traffic.delete')): ?><button class="btn btn-danger btn-sm btn-icon" onclick="deleteParking(<?= $p['id'] ?>)"><i class="fas fa-trash"></i></button><?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($parkingItems)): ?><div style="text-align:center;padding:30px;color:var(--text-muted)">등록된 주차 안내가 없습니다.</div><?php endif; ?>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h2><i class="fas fa-map" style="color:var(--primary)"></i> 주차 지도</h2></div>
      <div class="card-body">
        <div style="border:2px dashed var(--border);border-radius:8px;min-height:140px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#fafafa;margin-bottom:12px">
          <?php if($parkingMap && $parkingMap['image_url']): ?>
          <img id="map-img-preview" src="<?= BASE_URL.htmlspecialchars($parkingMap['image_url']) ?>" style="width:100%;display:block">
          <?php else: ?>
          <img id="map-img-preview" style="width:100%;display:none">
          <div id="map-img-ph" style="display:flex;flex-direction:column;align-items:center;gap:8px;color:var(--text-muted);padding:20px"><i class="fas fa-map" style="font-size:28px"></i><span style="font-size:12px">지도 이미지 없음</span></div>
          <?php endif; ?>
        </div>
        <?php if(hasPerm('traffic.edit')): ?>
        <div class="form-group"><label class="form-label">이미지 변경</label><input type="file" class="form-control" accept="image/*" onchange="previewMapImg(this)"></div>
        <div class="form-group"><label class="form-label">Alt 텍스트</label><input type="text" class="form-control" id="map-alt" value="<?= htmlspecialchars($parkingMap['alt_text']??'') ?>"></div>
        <button class="btn btn-primary" style="width:100%" onclick="saveMap()"><i class="fas fa-save"></i> 저장</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- 셔틀 모달 -->
<div id="shuttle-modal" class="modal-overlay hidden">
  <div class="modal modal-sm">
    <div class="modal-header"><h3 id="sh-modal-title">셔틀버스 시간 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('shuttle-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="sh-id">
      <div class="form-group"><label class="form-label">방향 <span class="req">*</span></label><select class="form-control" id="sh-direction"><option value="finch_to_church">Finch → 교회</option><option value="church_to_finch">교회 → Finch</option></select></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">시간 <span class="req">*</span></label><input class="form-control" id="sh-time" placeholder="오전 9:15"></div>
        <div class="form-group"><label class="form-label">구분 <span class="req">*</span></label><input class="form-control" id="sh-label" placeholder="2부"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">순서</label><input class="form-control" type="number" id="sh-order" value="0"></div>
        <div class="form-group"><label class="form-label">상태</label><select class="form-control" id="sh-active"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('shuttle-modal')">취소</button><button class="btn btn-primary" onclick="saveShuttle()"><i class="fas fa-save"></i> 저장</button></div>
  </div>
</div>

<!-- 주차 모달 -->
<div id="parking-modal" class="modal-overlay hidden">
  <div class="modal modal-md">
    <div class="modal-header"><h3 id="pk-modal-title">주차 안내 추가</h3><button class="btn btn-ghost btn-icon" onclick="closeModal('parking-modal')"><i class="fas fa-times"></i></button></div>
    <div class="modal-body">
      <input type="hidden" id="pk-id">
      <div class="form-group"><label class="form-label">안내 내용 <span class="req">*</span></label><textarea class="form-control" id="pk-content" rows="5" placeholder="주차 안내 내용을 입력하세요."></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">순서</label><input class="form-control" type="number" id="pk-order" value="0"></div>
        <div class="form-group"><label class="form-label">상태</label><select class="form-control" id="pk-active"><option value="1">활성</option><option value="0">비활성</option></select></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('parking-modal')">취소</button><button class="btn btn-primary" onclick="saveParking()"><i class="fas fa-save"></i> 저장</button></div>
  </div>
</div>

<style>
.tab-bar{display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);}
.tab-btn{padding:10px 20px;border:none;background:none;font-size:13px;font-weight:500;cursor:pointer;color:var(--text-muted);border-bottom:2px solid transparent;margin-bottom:-2px;display:flex;align-items:center;gap:6px;transition:all .15s;}
.tab-btn:hover{color:var(--primary);}.tab-btn.active{color:var(--primary);border-bottom-color:var(--primary);}
.tab-content.hidden{display:none;}
</style>

<script>
function switchTab(id,btn){document.querySelectorAll('.tab-content').forEach(t=>t.classList.add('hidden'));document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));document.getElementById(id).classList.remove('hidden');btn.classList.add('active');}
function openShuttleModal(data={}) {
  document.getElementById('sh-id').value=data.id||'';document.getElementById('sh-direction').value=data.direction||'finch_to_church';
  document.getElementById('sh-time').value=data.time||'';document.getElementById('sh-label').value=data.service_label||'';
  document.getElementById('sh-order').value=data.sort_order||0;document.getElementById('sh-active').value=data.is_active??1;
  document.getElementById('sh-modal-title').textContent=data.id?'셔틀버스 수정':'셔틀버스 시간 추가';openModal('shuttle-modal');
}
async function editShuttle(id){const d=await api('/traffic/shuttle-detail',{id});if(d.success)openShuttleModal(d.data);}
async function saveShuttle(){
  const id=document.getElementById('sh-id').value;
  const d=await api('/traffic/'+(id?'shuttle-update':'shuttle-create'),{id,direction:document.getElementById('sh-direction').value,time:document.getElementById('sh-time').value,service_label:document.getElementById('sh-label').value,sort_order:document.getElementById('sh-order').value,is_active:document.getElementById('sh-active').value});
  if(!d.success)return toast(d.message,'error');toast(d.message);closeModal('shuttle-modal');location.reload();
}
async function deleteShuttle(id){confirmAction('이 셔틀버스 시간을 삭제하시겠습니까?',async()=>{const d=await api('/traffic/shuttle-delete',{id});if(!d.success)return toast(d.message,'error');toast(d.message);document.querySelector(`#shuttle-tbody tr[data-id="${id}"]`)?.remove();});}
function openParkingModal(data={}){document.getElementById('pk-id').value=data.id||'';document.getElementById('pk-content').value=data.content||'';document.getElementById('pk-order').value=data.sort_order||0;document.getElementById('pk-active').value=data.is_active??1;document.getElementById('pk-modal-title').textContent=data.id?'주차 안내 수정':'주차 안내 추가';openModal('parking-modal');}
async function editParking(id){const d=await api('/traffic/parking-detail',{id});if(d.success)openParkingModal(d.data);}
async function saveParking(){const id=document.getElementById('pk-id').value;const d=await api('/traffic/'+(id?'parking-update':'parking-create'),{id,content:document.getElementById('pk-content').value,sort_order:document.getElementById('pk-order').value,is_active:document.getElementById('pk-active').value});if(!d.success)return toast(d.message,'error');toast(d.message);closeModal('parking-modal');location.reload();}
async function deleteParking(id){confirmAction('이 주차 안내를 삭제하시겠습니까?',async()=>{const d=await api('/traffic/parking-delete',{id});if(!d.success)return toast(d.message,'error');toast(d.message);document.querySelector(`.parking-item[data-id="${id}"]`)?.remove();});}
let _mapPending=null;
function previewMapImg(input){if(!input.files[0])return;_mapPending=input.files[0];const url=URL.createObjectURL(input.files[0]);const img=document.getElementById('map-img-preview');img.src=url;img.style.display='block';const ph=document.getElementById('map-img-ph');if(ph)ph.style.display='none';}
async function saveMap(){const fd=new FormData();fd.append('alt_text',document.getElementById('map-alt').value);fd.append('is_active',1);if(_mapPending)fd.append('image',_mapPending);const d=await apiUpload('/traffic/parking-map-update',fd,'저장 중...');if(!d.success)return toast(d.message,'error');toast(d.message);}
function pageInit(){const pl=document.getElementById('parking-list');if(pl&&typeof Sortable!=='undefined'){Sortable.create(pl,{handle:'.drag-handle',animation:150,onEnd:async()=>{const orders=[...pl.querySelectorAll('.parking-item')].map((el,i)=>({id:el.dataset.id,order:i}));await api('/traffic/parking-reorder',{orders:JSON.stringify(orders)});}});}}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
