</main><!-- /#main -->

<!-- 전역 업로드 스피너 -->
<div id="upload-spinner" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:14px">
  <div style="width:56px;height:56px;border:5px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite"></div>
  <div id="upload-spinner-msg" style="color:#fff;font-size:14px;font-weight:500">업로드 중...</div>
</div>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
const BASE_URL='<?= BASE_URL ?>';

/* ── 스피너 ─────────────────────────────────────── */
function showSpinner(msg='업로드 중...'){
  const el=document.getElementById('upload-spinner');
  document.getElementById('upload-spinner-msg').textContent=msg;
  el.style.display='flex';
}
function hideSpinner(){
  document.getElementById('upload-spinner').style.display='none';
}

/* ── API 헬퍼 ────────────────────────────────────── */
// 일반 POST (FormData)
async function api(url, data={}){
  const fd=new FormData();
  for(const[k,v]of Object.entries(data)) fd.append(k,v);
  return fetch(BASE_URL+url,{method:'POST',body:fd}).then(r=>r.json());
}

// 파일 포함 POST - 스피너 자동 표시
async function apiUpload(url, formData, msg='이미지 업로드 중...'){
  showSpinner(msg);
  try {
    const r=await fetch(BASE_URL+url,{method:'POST',body:formData}).then(r=>r.json());
    return r;
  } finally {
    hideSpinner();
  }
}

/* ── 테이블 행 즉시 갱신 헬퍼 ───────────────────── */
// 특정 행의 셀 텍스트를 key:value 객체로 업데이트
// colMap: { th인덱스: 새값 }  예) {2:'새제목', 4:'활성'}
function updateRow(id, colMap){
  const tr=document.querySelector(`tr[data-id="${id}"]`);
  if(!tr) return;
  for(const[idx,val]of Object.entries(colMap)){
    const td=tr.querySelectorAll('td')[idx];
    if(td&&typeof val==='string') td.querySelector('.fw-500,[class*=truncate]')
      ? (td.querySelector('.fw-500,[class*=truncate]').textContent=val)
      : (td.textContent=val);
  }
}
// 행 전체 다시 그리기보다 간단한 새로고침 대신
// saveXxx() 함수에서 직접 DOM을 업데이트하거나 아래 함수를 씁니다.
function refreshList(){
  // 페이지 상태를 유지하면서 목록만 AJAX로 재렌더
  if(typeof reloadList==='function') reloadList();
}

/* ── Toast ───────────────────────────────────────── */
function toast(msg, type='success'){
  const t=document.createElement('div');
  t.className='toast toast-'+type;
  t.innerHTML='<i class="fas fa-'+(type==='success'?'check-circle':type==='error'?'exclamation-circle':'exclamation-triangle')+'"></i>'+msg;
  document.getElementById('toast-container').appendChild(t);
  setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},3000);
}

/* ── 모달 ────────────────────────────────────────── */
function closeModal(id){document.getElementById(id)?.classList.add('hidden');}
function openModal(id){document.getElementById(id)?.classList.remove('hidden');}
function confirmAction(msg,fn){if(confirm(msg))fn();}

/* ── 로그아웃 / 프로필 ───────────────────────────── */
async function doLogout(){await api('/auth/logout');location.href=BASE_URL+'/auth/login';}
async function saveProfile(){
  const fd=new FormData(document.getElementById('profile-form'));
  const d=await fetch(BASE_URL+'/auth/profile',{method:'POST',body:fd}).then(r=>r.json());
  if(d.success){toast('프로필이 업데이트되었습니다.');closeModal('profile-modal');}
  else toast(d.message,'error');
}

/* ── 파일 input 1MB 클라이언트 체크 ─────────────── */
document.addEventListener('change', e=>{
  const input=e.target;
  if(input.type!=='file') return;
  const limit=1*1024*1024;
  for(const f of input.files){
    if(f.size>limit){
      toast(`"${f.name}" 파일이 1MB를 초과합니다. 더 작은 파일을 선택해 주세요.`,'error');
      input.value='';
      return;
    }
  }
});

/* ── Nav group toggle ───────────────────────────── */
function toggleGroup(header){
  header.classList.toggle("open");
  const body=header.nextElementSibling;
  if(body) body.style.display=body.style.display==="none"?"block":"none";
}
/* ── Mobile sidebar ─────────────────────────────── */
document.querySelectorAll('.modal-overlay').forEach(o=>{
  o.addEventListener('click',e=>{if(e.target===o)o.classList.add('hidden');});
});

/* ── pageInit (Sortable 로드 후 실행) ────────────── */
if(typeof pageInit==='function') pageInit();

/* ── 테마 switcher ───────────────────────────────── */
(function(){
  function saveTheme(t){
    fetch(BASE_URL+'/settings/theme',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({theme:t})
    }).catch(function(){});
  }
  function applyTheme(t,persist){
    if(t==='dark-green') document.documentElement.removeAttribute('data-theme');
    else document.documentElement.setAttribute('data-theme',t);
    localStorage.setItem('milal-theme',t);
    document.querySelectorAll('.theme-dot').forEach(function(b){
      b.classList.toggle('active',b.dataset.t===t);
    });
    if(persist) saveTheme(t);
  }
  applyTheme(localStorage.getItem('milal-theme')||'dark-green', false);
  document.querySelectorAll('.theme-dot').forEach(function(b){
    b.addEventListener('click',function(){applyTheme(this.dataset.t, true);});
  });
})();
</script>
</body></html>
