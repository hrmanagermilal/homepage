<?php include BASE_PATH.'/app/Views/layouts/header.php'; ?>

<div class="page-header-row">
  <h1 class="page-title-main"><i class="fas fa-user-tie"></i> 담임목사 소개 관리</h1>
  <?php if(hasPerm('introduction.edit')): ?>
  <button class="btn btn-primary" onclick="savePastor()"><i class="fas fa-save"></i> 저장</button>
  <?php endif; ?>
</div>

<div class="two-col">
  <!-- 좌: 미리보기 카드 -->
  <div class="card preview-card">
    <div class="card-header"><h2><i class="fas fa-eye"></i> 미리보기</h2></div>
    <div class="card-body">
      <div class="pastor-preview">
        <div class="pastor-title-preview">
          <div class="pt-line1" id="prev-line1"><?= htmlspecialchars($pastor['title_line1_ko']??'복음으로 하나 되어,') ?></div>
          <div class="pt-line2" id="prev-line2"><?= htmlspecialchars($pastor['title_line2_ko']??'세상으로 나아가는 교회') ?></div>
        </div>
        <div class="pastor-meta-preview">
          <span id="prev-role"><?= htmlspecialchars($pastor['pastor_role_ko']??'담임목사') ?></span>
          <strong id="prev-name"><?= htmlspecialchars($pastor['pastor_name_ko']??'박형일') ?></strong>
        </div>
      </div>
    </div>
  </div>

  <!-- 우: 편집 폼 -->
  <div class="card" style="flex:2">
    <div class="card-header"><h2><i class="fas fa-edit"></i> 내용 편집</h2></div>
    <div class="card-body">

      <!-- 탭 -->
      <div class="lang-tabs">
        <button class="lang-tab active" onclick="switchLang('ko',this)">🇰🇷 한국어</button>
        <button class="lang-tab" onclick="switchLang('en',this)">🇺🇸 영어</button>
      </div>

      <div id="lang-ko">
        <div class="form-grid-2">
          <div class="form-group"><label class="form-label">사진 대체 텍스트</label>
            <input class="form-control" id="photo_alt_ko" value="<?= htmlspecialchars($pastor['photo_alt_ko']??'') ?>"></div>
          <div class="form-group"><label class="form-label">직책</label>
            <input class="form-control" id="pastor_role_ko" value="<?= htmlspecialchars($pastor['pastor_role_ko']??'') ?>" oninput="document.getElementById('prev-role').textContent=this.value"></div>
        </div>
        <div class="form-grid-2">
          <div class="form-group"><label class="form-label">제목 1줄</label>
            <input class="form-control" id="title_line1_ko" value="<?= htmlspecialchars($pastor['title_line1_ko']??'') ?>" oninput="document.getElementById('prev-line1').textContent=this.value"></div>
          <div class="form-group"><label class="form-label">제목 2줄</label>
            <input class="form-control" id="title_line2_ko" value="<?= htmlspecialchars($pastor['title_line2_ko']??'') ?>" oninput="document.getElementById('prev-line2').textContent=this.value"></div>
        </div>
        <div class="form-group"><label class="form-label">목사님 성함</label>
          <input class="form-control" id="pastor_name_ko" value="<?= htmlspecialchars($pastor['pastor_name_ko']??'') ?>" oninput="document.getElementById('prev-name').textContent=this.value"></div>
        <div class="form-group"><label class="form-label">소개 본문 (줄바꿈 지원)</label>
          <textarea class="form-control" id="paragraphs_ko" rows="8"><?= htmlspecialchars($pastor['paragraphs_ko']??'') ?></textarea></div>
        <div class="form-grid-2">
          <div class="form-group"><label class="form-label">약력 제목</label>
            <input class="form-control" id="career_title_ko" value="<?= htmlspecialchars($pastor['career_title_ko']??'약력') ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">약력 내용 (줄바꿈으로 구분)</label>
          <textarea class="form-control" id="career_ko" rows="6"><?= htmlspecialchars($pastor['career_ko']??'') ?></textarea></div>
      </div>

      <div id="lang-en" class="hidden">
        <div class="form-grid-2">
          <div class="form-group"><label class="form-label">Photo Alt Text</label>
            <input class="form-control" id="photo_alt_en" value="<?= htmlspecialchars($pastor['photo_alt_en']??'') ?>"></div>
          <div class="form-group"><label class="form-label">Pastor Role</label>
            <input class="form-control" id="pastor_role_en" value="<?= htmlspecialchars($pastor['pastor_role_en']??'') ?>"></div>
        </div>
        <div class="form-grid-2">
          <div class="form-group"><label class="form-label">Title Line 1</label>
            <input class="form-control" id="title_line1_en" value="<?= htmlspecialchars($pastor['title_line1_en']??'') ?>"></div>
          <div class="form-group"><label class="form-label">Title Line 2</label>
            <input class="form-control" id="title_line2_en" value="<?= htmlspecialchars($pastor['title_line2_en']??'') ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">Pastor Name (EN)</label>
          <input class="form-control" id="pastor_name_en" value="<?= htmlspecialchars($pastor['pastor_name_en']??'') ?>"></div>
        <div class="form-group"><label class="form-label">Introduction Paragraphs</label>
          <textarea class="form-control" id="paragraphs_en" rows="8"><?= htmlspecialchars($pastor['paragraphs_en']??'') ?></textarea></div>
        <div class="form-grid-2">
          <div class="form-group"><label class="form-label">Career Section Title</label>
            <input class="form-control" id="career_title_en" value="<?= htmlspecialchars($pastor['career_title_en']??'Career') ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">Career Content (line-separated)</label>
          <textarea class="form-control" id="career_en" rows="6"><?= htmlspecialchars($pastor['career_en']??'') ?></textarea></div>
      </div>

    </div>
  </div>
</div>

<style>
.page-header-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.page-title-main{font-size:20px;font-weight:700;}
.two-col{display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start;}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.preview-card{position:sticky;top:calc(var(--header-h) + 20px);}
.pastor-preview{text-align:center;padding:20px 10px;}
.pastor-title-preview{margin-bottom:20px;}
.pt-line1{font-size:16px;color:var(--text-muted);}
.pt-line2{font-size:18px;font-weight:700;color:var(--primary);}
.pastor-meta-preview{font-size:13px;}
.pastor-meta-preview strong{display:block;font-size:18px;font-weight:700;margin-top:4px;}
.lang-tabs{display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);}
.lang-tab{padding:8px 16px;border:none;background:none;font-size:13px;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;font-weight:500;color:var(--text-muted);}
.lang-tab.active{color:var(--primary);border-bottom-color:var(--primary);}
.hidden{display:none;}
</style>

<script>
function switchLang(lang, btn) {
  ['ko','en'].forEach(l => {
    document.getElementById('lang-'+l).classList.toggle('hidden', l!==lang);
  });
  document.querySelectorAll('.lang-tab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

async function savePastor() {
  const fields = ['photo_alt_ko','photo_alt_en','title_line1_ko','title_line2_ko','title_line1_en','title_line2_en',
                   'paragraphs_ko','paragraphs_en','pastor_role_ko','pastor_role_en','pastor_name_ko','pastor_name_en',
                   'career_title_ko','career_title_en','career_ko','career_en'];
  const data = {};
  fields.forEach(f => { const el = document.getElementById(f); if(el) data[f] = el.value; });
  data.is_active = 1;
  const d = await api('/introduction/pastor-update', data);
  if (!d.success) return toast(d.message, 'error');
  toast(d.message);
}
</script>

<?php include BASE_PATH.'/app/Views/layouts/footer.php'; ?>
