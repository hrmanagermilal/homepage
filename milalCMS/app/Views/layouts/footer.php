</main><!-- /#main -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
const BASE_URL='<?= BASE_URL ?>';
function toast(msg,type='success'){
  const t=document.createElement('div');t.className='toast toast-'+type;
  t.innerHTML='<i class="fas fa-'+(type==='success'?'check-circle':type==='error'?'exclamation-circle':'exclamation-triangle')+'"></i>'+msg;
  document.getElementById('toast-container').appendChild(t);
  setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},3000);
}
async function api(url,data={}){
  const fd=new FormData();for(const[k,v]of Object.entries(data))fd.append(k,v);
  return fetch(BASE_URL+url,{method:'POST',body:fd}).then(r=>r.json());
}
function closeModal(id){document.getElementById(id)?.classList.add('hidden');}
function openModal(id){document.getElementById(id)?.classList.remove('hidden');}
function confirmAction(msg,fn){if(confirm(msg))fn();}
async function doLogout(){await api('/auth/logout');location.href=BASE_URL+'/auth/login';}
async function saveProfile(){
  const fd=new FormData(document.getElementById('profile-form'));
  const d=await fetch(BASE_URL+'/auth/profile',{method:'POST',body:fd}).then(r=>r.json());
  if(d.success){toast('프로필이 업데이트되었습니다.');closeModal('profile-modal');}else toast(d.message,'error');
}
const menuBtn=document.getElementById('menu-btn');if(menuBtn)menuBtn.style.display='block';
document.querySelectorAll('.modal-overlay').forEach(o=>{o.addEventListener('click',e=>{if(e.target===o)o.classList.add('hidden');});});
// Sortable 로드 이후 각 페이지 init 실행
if(typeof pageInit==='function')pageInit();
</script>
</body></html>
