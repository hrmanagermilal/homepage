<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>로그인 — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:"Pretendard","Apple SD Gothic Neo",sans-serif;background:linear-gradient(135deg,#1e1b4b 0%,#312e81 50%,#4f46e5 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:#fff;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,.25);width:100%;max-width:400px;padding:40px}
.logo{text-align:center;margin-bottom:32px}
.logo i{font-size:40px;color:#4f46e5;margin-bottom:12px;display:block}
.logo h1{font-size:22px;font-weight:700;color:#1e1b4b;margin-bottom:4px}
.logo p{font-size:13px;color:#6b7280}
.fg{margin-bottom:16px}
label{display:block;font-size:13px;font-weight:500;margin-bottom:6px;color:#374151}
.iw{position:relative}
.iw i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:14px}
input{width:100%;padding:10px 12px 10px 36px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;transition:border .15s,box-shadow .15s}
input:focus{outline:none;border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.1)}
.btn{width:100%;padding:12px;background:#4f46e5;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s;margin-top:8px;display:flex;align-items:center;justify-content:center;gap:8px}
.btn:hover{background:#4338ca}
.btn:disabled{background:#9ca3af;cursor:not-allowed}
.err{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <i class="fas fa-church"></i>
    <h1><?= APP_NAME ?></h1>
    <p>관리자 로그인</p>
  </div>
  <div id="err" style="display:none" class="err"><i class="fas fa-exclamation-circle"></i><span id="err-msg"></span></div>
  <div class="fg"><label>아이디</label><div class="iw"><i class="fas fa-user"></i><input type="text" id="username" placeholder="아이디" autocomplete="username"></div></div>
  <div class="fg"><label>비밀번호</label><div class="iw"><i class="fas fa-lock"></i><input type="password" id="password" placeholder="비밀번호" autocomplete="current-password"></div></div>
  <button class="btn" id="btn" onclick="doLogin()"><i class="fas fa-sign-in-alt"></i>로그인</button>
</div>
<script>
const BASE_URL='<?= BASE_URL ?>';
async function doLogin(){
  const btn=document.getElementById('btn');
  const u=document.getElementById('username').value.trim();
  const p=document.getElementById('password').value;
  if(!u||!p){show('아이디와 비밀번호를 입력하세요.');return;}
  btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i>로그인 중...';
  const fd=new FormData();fd.append('username',u);fd.append('password',p);
  const r=await fetch(BASE_URL+'/auth/do-login',{method:'POST',body:fd}).then(r=>r.json()).catch(()=>({success:false,message:'서버 오류'}));
  if(r.success){location.href=r.data.redirect;}
  else{show(r.message);btn.disabled=false;btn.innerHTML='<i class="fas fa-sign-in-alt"></i>로그인';}
}
function show(m){const e=document.getElementById('err');e.style.display='flex';document.getElementById('err-msg').textContent=m;}
document.addEventListener('keydown',e=>{if(e.key==='Enter')doLogin();});
</script>
</body></html>
