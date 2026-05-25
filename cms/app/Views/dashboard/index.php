<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<style>
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px;}
.stat-card{background:var(--surface);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);display:flex;align-items:center;gap:16px;text-decoration:none;color:inherit;transition:box-shadow .2s,transform .15s;}
.stat-card:hover{box-shadow:var(--shadow-md);transform:translateY(-2px);}
.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.stat-val{font-size:24px;font-weight:700;line-height:1;}
.stat-label{font-size:12px;color:var(--text-muted);margin-top:4px;}
.recent-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;}
.text-sm{font-size:12px;}.text-muted{color:var(--text-muted);}
.truncate{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;display:inline-block;}
@media(max-width:1100px){.recent-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:700px){.recent-grid{grid-template-columns:1fr;}}
</style>

<!-- 통계 카드 -->
<div class="stat-grid">
<?php
$cards = [
  ['label'=>'공지',    'val'=>$stats['notice'],      'icon'=>'bullhorn',      'bg'=>'#ede9fe','color'=>'#7c3aed','url'=>'/notice'],
  ['label'=>'설교',    'val'=>$stats['sermons'],     'icon'=>'video',         'bg'=>'#d1fae5','color'=>'#059669','url'=>'/sermons'],
  ['label'=>'주보',    'val'=>$stats['bulletins'],   'icon'=>'book-open',     'bg'=>'#fef3c7','color'=>'#d97706','url'=>'/bulletins'],
  ['label'=>'섬기는분들','val'=>$stats['members'],   'icon'=>'users',         'bg'=>'#fce7f3','color'=>'#db2777','url'=>'/members'],
  ['label'=>'부서',    'val'=>$stats['departments'], 'icon'=>'sitemap',       'bg'=>'#e0f2fe','color'=>'#0284c7','url'=>'/departments'],
  ['label'=>'사역',    'val'=>$stats['ministry'],    'icon'=>'hands-helping', 'bg'=>'#f0fdf4','color'=>'#16a34a','url'=>'/ministry'],
  ['label'=>'부고',    'val'=>$stats['obituary'],    'icon'=>'dove',          'bg'=>'#f3f4f6','color'=>'#6b7280','url'=>'/obituary'],
  ['label'=>'사용자',  'val'=>$stats['users'],       'icon'=>'user-cog',      'bg'=>'#f5f3ff','color'=>'#7c3aed','url'=>'/users'],
];
foreach($cards as $c): ?>
<a href="<?= BASE_URL . $c['url'] ?>" class="stat-card">
  <div class="stat-icon" style="background:<?= $c['bg'] ?>;color:<?= $c['color'] ?>">
    <i class="fas fa-<?= $c['icon'] ?>"></i>
  </div>
  <div>
    <div class="stat-val"><?= number_format($c['val']) ?></div>
    <div class="stat-label"><?= $c['label'] ?></div>
  </div>
</a>
<?php endforeach; ?>
</div>

<!-- 최근 목록 -->
<div class="recent-grid">
  <!-- 최근 공지 -->
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-bullhorn" style="color:#7c3aed"></i> 최근 공지</h2>
      <a href="<?= BASE_URL ?>/notice" class="btn btn-ghost btn-sm">전체 <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="card-body" style="padding:0">
      <table><thead><tr><th>제목</th><th>등급</th><th>날짜</th></tr></thead><tbody>
      <?php foreach($recentNotice as $r):
        $lvl = $r['emergency_level'];
        $bc = ['urgent'=>'badge-red','important'=>'badge-yellow','normal'=>'badge-gray'][$lvl]??'badge-gray';
        $bl = ['urgent'=>'긴급','important'=>'중요','normal'=>'일반'][$lvl]??'일반';
      ?>
      <tr>
        <td><span class="truncate"><?= htmlspecialchars($r['title']) ?></span></td>
        <td><span class="badge <?= $bc ?>"><?= $bl ?></span></td>
        <td class="text-muted text-sm"><?= $r['created_date'] ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($recentNotice)): ?><tr><td colspan="3" class="text-muted text-sm" style="padding:16px;text-align:center">공지가 없습니다.</td></tr><?php endif; ?>
      </tbody></table>
    </div>
  </div>

  <!-- 최근 설교 -->
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-video" style="color:#059669"></i> 최근 설교</h2>
      <a href="<?= BASE_URL ?>/sermons" class="btn btn-ghost btn-sm">전체 <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="card-body" style="padding:0">
      <table><thead><tr><th>제목</th><th>설교자</th><th>날짜</th></tr></thead><tbody>
      <?php foreach($recentSermons as $r): ?>
      <tr>
        <td><span class="truncate"><?= htmlspecialchars($r['title']) ?></span></td>
        <td class="text-muted text-sm"><?= htmlspecialchars($r['preacher']??'-') ?></td>
        <td class="text-muted text-sm"><?= $r['sermon_date'] ? date('m/d', strtotime($r['sermon_date'])) : '-' ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($recentSermons)): ?><tr><td colspan="3" class="text-muted text-sm" style="padding:16px;text-align:center">설교가 없습니다.</td></tr><?php endif; ?>
      </tbody></table>
    </div>
  </div>

  <!-- 최근 부고 -->
  <div class="card">
    <div class="card-header">
      <h2><i class="fas fa-dove" style="color:#6b7280"></i> 최근 부고</h2>
      <a href="<?= BASE_URL ?>/obituary" class="btn btn-ghost btn-sm">전체 <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="card-body" style="padding:0">
      <table><thead><tr><th>제목</th><th>날짜</th></tr></thead><tbody>
      <?php foreach($recentObituary as $r): ?>
      <tr>
        <td><span class="truncate"><?= htmlspecialchars(strip_tags($r['title'])) ?></span></td>
        <td class="text-muted text-sm"><?= $r['date'] ?? '-' ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($recentObituary)): ?><tr><td colspan="2" class="text-muted text-sm" style="padding:16px;text-align:center">부고가 없습니다.</td></tr><?php endif; ?>
      </tbody></table>
    </div>
  </div>
</div>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
