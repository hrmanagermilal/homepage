<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<div class="card">
  <div class="card-header">
    <h2><i class="fas fa-image" style="color:var(--primary)"></i> 배너 이미지 관리</h2>
  </div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">
      <div>
        <p class="text-sm text-muted mb-16">현재 배너 이미지:</p>
        <div style="border:2px dashed var(--border);border-radius:8px;min-height:160px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#fafafa">
          <?php if($banner && $banner['image_url']): ?>
          <img id="banner-img-preview" src="<?= BASE_URL.htmlspecialchars($banner['image_url']) ?>" style="width:100%;display:block">
          <?php else: ?>
          <img id="banner-img-preview" style="width:100%;display:none">
          <div id="banner-img-ph" style="display:flex;flex-direction:column;align-items:center;gap:8px;color:var(--text-muted);padding:20px"><i class="fas fa-image" style="font-size:32px"></i><span class="text-sm">배너 이미지 없음</span></div>
          <?php endif; ?>
        </div>
        <?php if($banner): ?>
        <p class="text-sm text-muted mt-16">상태: <span class="badge <?= $banner['is_active']?'badge-green':'badge-gray' ?>"><?= $banner['is_active']?'활성':'비활성' ?></span></p>
        <?php endif; ?>
      </div>
      <?php if(hasPerm('onlinegiving.edit')): ?>
      <div>
        <div class="form-group"><label class="form-label">새 이미지 업로드</label><input type="file" class="form-control" accept="image/*" onchange="previewBannerImg(this)"></div>
        <div class="form-group"><label class="form-label">Alt 텍스트</label><input type="text" class="form-control" id="banner-alt" value="<?= htmlspecialchars($banner['alt_text']??'') ?>"></div>
        <button class="btn btn-primary" style="width:100%" onclick="saveBanner()"><i class="fas fa-save"></i> 저장</button>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script>
let _bannerPending=null;
function previewBannerImg(input){if(!input.files[0])return;_bannerPending=input.files[0];const url=URL.createObjectURL(input.files[0]);const img=document.getElementById('banner-img-preview');img.src=url;img.style.display='block';const ph=document.getElementById('banner-img-ph');if(ph)ph.style.display='none';}
async function saveBanner(){const fd=new FormData();fd.append('alt_text',document.getElementById('banner-alt').value);fd.append('is_active',1);if(_bannerPending)fd.append('image',_bannerPending);const d=await apiUpload('/banner/banner-update',fd,'저장 중...');if(!d.success)return toast(d.message,'error');toast(d.message);}
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
