<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>
<style>
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px;}
.stat-card{background:var(--surface);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);display:flex;align-items:center;gap:16px;text-decoration:none;color:inherit;transition:box-shadow .2s,transform .15s;cursor:pointer;}
.stat-card:hover{box-shadow:var(--shadow-md);transform:translateY(-2px);}
.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.stat-val{font-size:24px;font-weight:700;line-height:1;}
.stat-label{font-size:12px;color:var(--text-muted);margin-top:4px;}
.charts-row{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px;}
.recent-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
@media(max-width:900px){.charts-row,.recent-grid{grid-template-columns:1fr;}}
</style>

<!-- 통계 카드 (클릭 시 해당 페이지로 이동) -->
<div class="stat-grid">
<?php
$cards=[
  ['label'=>'공지사항','val'=>$stats['announcements'],'icon'=>'bullhorn','bg'=>'#ede9fe','color'=>'#7c3aed','url'=>'/announcements'],
  ['label'=>'뉴스',    'val'=>$stats['news'],         'icon'=>'newspaper','bg'=>'#dbeafe','color'=>'#2563eb','url'=>'/news'],
  ['label'=>'설교',    'val'=>$stats['sermons'],      'icon'=>'video',    'bg'=>'#d1fae5','color'=>'#059669','url'=>'/sermons'],
  ['label'=>'주보',    'val'=>$stats['bulletins'],    'icon'=>'book-open','bg'=>'#fef3c7','color'=>'#d97706','url'=>'/bulletins'],
  ['label'=>'교인',    'val'=>$stats['members'],      'icon'=>'users',    'bg'=>'#fce7f3','color'=>'#db2777','url'=>'/members'],
  ['label'=>'부서',    'val'=>$stats['departments'],  'icon'=>'sitemap',  'bg'=>'#e0f2fe','color'=>'#0284c7','url'=>'/departments'],
  ['label'=>'히어로',  'val'=>$stats['heroes'],       'icon'=>'images',   'bg'=>'#f0fdf4','color'=>'#16a34a','url'=>''],
  ['label'=>'사용자',  'val'=>$stats['users'],        'icon'=>'user-cog', 'bg'=>'#f5f3ff','color'=>'#7c3aed','url'=>'/users'],
];

//['label'=>'히어로',  'val'=>$stats['heroes'],       'icon'=>'images',   'bg'=>'#f0fdf4','color'=>'#16a34a','url'=>'/heroes'],
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

<!-- 차트
<div class="charts-row">
  <div class="card">
    <div class="card-header"><h2><i class="fas fa-chart-bar" style="color:var(--primary)"></i> 최근 7일 페이지뷰</h2></div>
    <div class="card-body"><canvas id="pvChart" height="100"></canvas></div>
  </div>
  <div class="card">
    <div class="card-header"><h2><i class="fas fa-chart-pie" style="color:var(--info)"></i> 기기 비율 (30일)</h2></div>
    <div class="card-body"><canvas id="deviceChart"></canvas></div>
  </div>
</div> -->

<!-- 최근 목록 -->
<div class="recent-grid">
  <div class="card">
    <div class="card-header">
      <h2>최근 공지사항</h2>
      <a href="<?= BASE_URL ?>/announcements" class="btn btn-ghost btn-sm">전체 보기 <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="card-body" style="padding:0">
      <table><thead><tr><th>제목</th><th>카테고리</th><th>작성일</th></tr></thead><tbody>
      <?php foreach($recentAnn as $r):
        $catMap=['general'=>['badge-gray','일반'],'event'=>['badge-blue','이벤트'],'urgent'=>['badge-red','긴급']];
        [$bc,$bl]=$catMap[$r['category']]??['badge-gray','기타']; ?>
      <tr>
        <td>
          <a href="<?= BASE_URL ?>/announcements/view?id=<?= $r['id'] ?>" style="color:var(--text)" class="truncate" style="display:block">
            <?= htmlspecialchars($r['title']) ?>
          </a>
        </td>
        <td><span class="badge <?= $bc ?>"><?= $bl ?></span></td>
        <td class="text-muted text-sm"><?= date('m/d',strtotime($r['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody></table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h2>최근 설교</h2>
      <a href="<?= BASE_URL ?>/sermons" class="btn btn-ghost btn-sm">전체 보기 <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="card-body" style="padding:0">
      <table><thead><tr><th>제목</th><th>설교자</th><th>날짜</th></tr></thead><tbody>
      <?php foreach($recentSermons as $r): ?>
      <tr>
        <td>
          <a href="<?= BASE_URL ?>/sermons/view?id=<?= $r['id'] ?>" style="color:var(--text)" class="truncate">
            <?= htmlspecialchars($r['title']) ?>
          </a>
        </td>
        <td class="text-muted text-sm"><?= htmlspecialchars($r['preacher']??'-') ?></td>
        <td class="text-muted text-sm"><?= $r['sermon_date']?date('m/d',strtotime($r['sermon_date'])):'-' ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody></table>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const pvData=<?= json_encode($pvChart,JSON_UNESCAPED_UNICODE) ?>;
const last7=[...Array(7)].map((_,i)=>{const d=new Date();d.setDate(d.getDate()-6+i);return d.toISOString().slice(0,10);});
new Chart(document.getElementById('pvChart'),{
  type:'bar',
  data:{labels:last7.map(d=>d.slice(5)),datasets:[{label:'페이지뷰',data:last7.map(d=>pvData[d]||0),backgroundColor:'rgba(79,70,229,.7)',borderRadius:4}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1}}}}
});
const devData=<?= json_encode($deviceChart,JSON_UNESCAPED_UNICODE) ?>;
const devLabels={'mobile':'모바일','tablet':'태블릿','desktop':'데스크톱'};
new Chart(document.getElementById('deviceChart'),{
  type:'doughnut',
  data:{labels:Object.keys(devData).map(k=>devLabels[k]||k),datasets:[{data:Object.values(devData),backgroundColor:['#6366f1','#f59e0b','#10b981']}]},
  options:{responsive:true,plugins:{legend:{position:'bottom'}}}
});
</script>
<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
