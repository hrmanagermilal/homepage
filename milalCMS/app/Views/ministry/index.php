<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="page-header-row">
  <h1 class="page-title-main"><i class="fas fa-hands-helping"></i> 사역 관리</h1>
  <?php if(hasPerm('ministry.create')): ?>
  <a href="<?= BASE_URL ?>/ministry/edit" class="btn btn-primary"><i class="fas fa-plus"></i> 사역 추가</a>
  <?php endif; ?>
</div>

<div class="info-banner">
  <i class="fas fa-info-circle"></i>
  홈페이지 사역 메뉴(소그룹, 양육, 가정, 선교, 장학, 가스펠프로젝트, 다니엘한글문화학교, 러브토론토)를 관리합니다.
</div>

<div id="ministry-grid" class="ministry-grid">
  <?php foreach($ministries as $m): ?>
  <div class="ministry-card" data-id="<?= $m['id'] ?>">
    <div class="ministry-card-img">
      <?php
        $imgSrc = $m['image'] ? UPLOAD_URL . ltrim($m['image'], '/') : '';
      ?>
      <?php if($imgSrc): ?>
      <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($m['name']) ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <div class="img-ph" style="display:none"><i class="fas fa-hands-helping"></i></div>
      <?php else: ?>
      <div class="img-ph"><i class="fas fa-hands-helping"></i></div>
      <?php endif; ?>
      <div class="ministry-card-overlay">
        <span class="ministry-key">#<?= htmlspecialchars($m['key']) ?></span>
        <span class="badge <?= $m['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $m['is_active'] ? '활성' : '비활성' ?></span>
      </div>
    </div>
    <div class="ministry-card-body">
      <div class="ministry-name"><?= htmlspecialchars($m['name']) ?></div>
      <?php if($m['subtitle']): ?><div class="ministry-subtitle"><?= htmlspecialchars($m['subtitle']) ?></div><?php endif; ?>
      <?php if($m['description']): ?><div class="ministry-desc"><?= htmlspecialchars(mb_substr($m['description'],0,80)) ?>...</div><?php endif; ?>
    </div>
    <div class="ministry-card-footer">
      <?php if(hasPerm('ministry.edit')): ?>
      <a href="<?= BASE_URL ?>/ministry/edit?id=<?= $m['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i> 수정</a>
      <?php endif; ?>
      <?php if(hasPerm('ministry.delete')): ?>
      <button class="btn btn-danger btn-sm" onclick="deleteMinistry(<?= $m['id'] ?>, '<?= htmlspecialchars($m['name'],ENT_QUOTES) ?>')"><i class="fas fa-trash"></i></button>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if(empty($ministries)): ?>
  <div class="empty-full"><i class="fas fa-hands-helping fa-3x"></i><p>등록된 사역이 없습니다.</p>
    <?php if(hasPerm('ministry.create')): ?><a href="<?= BASE_URL ?>/ministry/edit" class="btn btn-primary">첫 번째 사역 추가</a><?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<style>
.page-header-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.page-title-main{font-size:20px;font-weight:700;}
.info-banner{background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 16px;font-size:13px;color:#1d4ed8;margin-bottom:20px;display:flex;align-items:center;gap:8px;}
.ministry-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px;}
.ministry-card{background:var(--surface);border-radius:12px;box-shadow:var(--shadow);overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .2s;}
.ministry-card:hover{box-shadow:var(--shadow-md);}
.ministry-card-img{position:relative;height:140px;overflow:hidden;background:#f3f4f6;}
.ministry-card-img img{width:100%;height:100%;object-fit:cover;}
.img-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:36px;color:#d1d5db;}
.ministry-card-overlay{position:absolute;top:8px;right:8px;display:flex;flex-direction:column;align-items:flex-end;gap:4px;}
.ministry-key{background:rgba(0,0,0,.5);color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;}
.ministry-card-body{padding:14px;flex:1;}
.ministry-name{font-weight:700;font-size:15px;margin-bottom:4px;}
.ministry-subtitle{font-size:12px;color:var(--primary);font-weight:500;margin-bottom:6px;}
.ministry-desc{font-size:12px;color:var(--text-muted);line-height:1.5;}
.ministry-card-footer{padding:10px 14px;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:flex-end;}
.empty-full{grid-column:1/-1;text-align:center;padding:64px;color:var(--text-muted);}
.empty-full i{display:block;margin-bottom:16px;opacity:.2;}
.empty-full p{margin-bottom:16px;}
</style>

<script>
async function deleteMinistry(id, name) {
  confirmAction(`"${name}" 사역을 삭제하시겠습니까?`, async () => {
    const d = await api('/ministry/delete', {id});
    if (!d.success) return toast(d.message, 'error');
    toast(d.message);
    document.querySelector(`.ministry-card[data-id="${id}"]`)?.remove();
  });
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
