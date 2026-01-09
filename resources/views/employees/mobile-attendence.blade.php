<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Employee Attendance</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  /* ---------- BASIC ---------- */
  *{box-sizing:border-box;margin:0;padding:0;font-family:Inter, Poppins, 'Segoe UI', system-ui, sans-serif}
  html,body{height:100%}
  body{
    background: linear-gradient(180deg,#eaf2ff 0%, #f8fbff 100%);
    color:#0f172a;
    -webkit-font-smoothing:antialiased;
    display:flex;flex-direction:column;align-items:center;padding:18px;
  }

  /* ---------- CONTAINER (mobile width) ---------- */
  .wrap{width:100%;max-width:460px;display:flex;flex-direction:column;gap:16px;}

  /* ---------- HEADER ---------- */
  header{
    background:linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%);
    color:#fff;padding:18px;border-radius:16px;position:relative;
    box-shadow:0 10px 30px rgba(99,102,241,0.18);
    overflow:hidden;
  }
  header h1{font-size:18px;font-weight:400;letter-spacing:0.8px}
  .header-row{display:flex;align-items:center;justify-content:space-between;gap:8px}
  .user-block{display:flex;flex-direction:column;gap:6px}
  .user-name{font-size:15px;background:rgba(255,255,255,0.12);padding:6px 12px;border-radius:999px;display:inline-block}
  .datetime{font-size:13px;opacity:0.95}
  .logout-btn{position:absolute;right:12px;top:12px;background:rgba(255,255,255,0.12);border:none;color:#fff;padding:6px 10px;border-radius:10px;cursor:pointer;font-weight:600}

  /* ---------- EMPLOYEE CARD ---------- */
  .employee-card{
    background:linear-gradient(180deg, rgba(255,255,255,0.92), rgba(255,255,255,0.86));
    border-radius:16px;padding:16px;box-shadow:0 10px 30px rgba(2,6,23,0.06);
    display:flex;flex-direction:column;gap:8px;
    align-items:center;
  }
  .employee-name{font-size:20px;font-weight:700;color:#0b3d91}
  .status{font-size:14px}
  .status .pill{display:inline-block;padding:6px 14px;border-radius:999px;font-weight:700}
  .pill.in{background:#ecfdf5;color:#065f46}
  .pill.out{background:#fff1f2;color:#991b1b;border:1px solid #fecaca}

  /* ---------- ACTIONS ---------- */
  .actions{display:flex;gap:12px}
  .big-btn{
    flex:1;padding:14px;border-radius:14px;border:none;font-size:16px;color:#fff;
    cursor:pointer;box-shadow:0 8px 24px rgba(2,6,23,0.08);transition:transform .14s ease,opacity .14s;
  }
  .big-btn:active{transform:translateY(1px)}
  .big-btn:disabled{opacity:.5;cursor:not-allowed}
  .btn-in{background:linear-gradient(90deg,#16a34a,#22c55e)}
  .btn-out{background:linear-gradient(90deg,#ef4444,#f97316)}

  /* ---------- HISTORY (table-like cards) ---------- */
  .history{
    background:transparent;border-radius:12px;padding:0;
  }
  .history h3{font-size:16px;color:#0b3d91;text-align:center;margin-bottom:8px;font-weight:700}
  .history-list{display:flex;flex-direction:column;gap:10px;max-height:320px;overflow:auto;padding:6px 2px}
  .row-card{
    background:linear-gradient(180deg,#ffffff,#fbfbff);
    border-radius:12px;padding:10px 12px;box-shadow:0 6px 18px rgba(2,6,23,0.04);
    display:grid;grid-template-columns: 48px 1fr 1fr;gap:8px;align-items:center;
  }
  .row-left{padding-left:8px;display:flex;flex-direction:column;align-items:center;justify-content:center}
  .sr{background:#eef2ff;color:#3730a3;padding:6px 8px;border-radius:10px;font-weight:700}
  .date{font-weight:700;color:#0b3d91}
  .col{font-size:13px;color:#0f172a;display:flex;flex-direction:column;gap:10px}
  .small{font-size:12px;color:#475569}
  .status-badge{padding:6px 8px;border-radius:999px;font-size:12px}
  .status-present{background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0}
  .status-absent{background:#fff1f2;color:#9f1239;border:1px solid #fecaca}
  .in_out{
    padding:5px 20px;
  }

  /* duration */
  .duration{font-weight:700;color:#0b3d91}

  /* ---------- MODAL (simple) ---------- */
  .modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(2,6,23,0.45);z-index:999}
  .modal.active{display:flex}
  .modal-card{background:#fff;padding:18px;border-radius:12px;width:90%;max-width:360px;box-shadow:0 12px 30px rgba(2,6,23,0.3)}
  .modal-card h4{font-size:16px;margin-bottom:10px;color:#0b3d91}
  .select, .modal-card .submit{width:100%;padding:10px;border-radius:10px;border:1px solid #e6e9ef}
  .submit{background:linear-gradient(90deg,#2563eb,#7c3aed);color:#fff;border:none;margin-top:12px;font-weight:700;cursor:pointer}

  /* ---------- FOOTER ---------- */
  footer{padding:12px 0;text-align:center;color:#475569;font-size:13px}

  /* small screens adjustments */
  @media (max-width:420px){
    .row-card{grid-template-columns:44px 1fr 1fr;gap:6px;padding:10px}
    .employee-name{font-size:18px}
  }
</style>
</head>
<body>
  <div class="wrap">

    <!-- HEADER -->
    <header>
      <div class="header-row">
        <div>
          <h1>Attendance</h1>
          <div class="user-block">
            <div id="userName" class="user-name">Loading...</div>
            <div id="liveDateTime" class="datetime">-- / -- / ---- &nbsp; 00:00:00</div>
          </div>
        </div>
        <button class="logout-btn" id="logoutBtn">Logout</button>
      </div>
    </header>

    <!-- EMPLOYEE CARD -->
    <section class="employee-card">
      <div id="empName" class="employee-name">Loading...</div>
      <div class="status">Status: <span id="statusPill" class="pill out">Fetching...</span></div>
    </section>

    <!-- ACTIONS -->
    <section class="actions">
      <button id="markInBtn" class="big-btn btn-in">📍 Mark IN</button>
      <button id="markOutBtn" class="big-btn btn-out">🚪 Mark OUT</button>
    </section>

    <!-- HISTORY -->
    <section class="history">
      <h3>Attendance History</h3>
      <div id="historyList" class="history-list">
        <div style="text-align:center;color:#6b7280;padding:12px">Loading...</div>
      </div>
    </section>

    <footer>© 2025 Octacore Technologies</footer>
  </div>

  <!-- OUT Reason Modal -->
  <div id="reasonModal" class="modal">
    <div class="modal-card">
      <h4>Select reason for OUT</h4>
      <select id="reasonSelect" class="select">
        <option value="">Select Reason</option>
        <option>Other Plant</option>
        <option>Lunch / Dinner</option>
        <option>Early Leave / OFF Duty</option>
        <option>OD</option>
        <option>Alok City</option>
        <option>Gate Pass</option>
      </select>
      <button id="reasonSubmit" class="submit">Submit</button>
    </div>
  </div>

<script>
/*
  NOTE:
  - This JS keeps your API endpoints and main logic exactly as before:
    'api/employee/index', 'api/employee/attendance/history', 'api/employee/attendance/mark', 'api/employee/logout'
  - Token read from localStorage key: 'emp_token' (same as your original)
  - Date formatting: dd/mm/yyyy
  - Duration calculated (HH:mm)
*/

const token = localStorage.getItem('emp_token');
if(!token) {
  // if no token, go to login (same as your previous behaviour)
  window.location.href = 'emp-login';
}

// DOM refs
const userNameEl = document.getElementById('userName');
const empNameEl = document.getElementById('empName');
const statusPill = document.getElementById('statusPill');
const historyList = document.getElementById('historyList');
const liveDateTime = document.getElementById('liveDateTime');
const markInBtn = document.getElementById('markInBtn');
const markOutBtn = document.getElementById('markOutBtn');
const logoutBtn = document.getElementById('logoutBtn');

const reasonModal = document.getElementById('reasonModal');
const reasonSelect = document.getElementById('reasonSelect');
const reasonSubmit = document.getElementById('reasonSubmit');

// ----------------- live clock (dd/mm/yyyy HH:MM:SS) -----------------
function pad(n){return n<10?('0'+n):n}
function formatDateDDMMYYYY(d){
  const day = pad(d.getDate()), mon = pad(d.getMonth()+1), year = d.getFullYear();
  return `${day}/${mon}/${year}`;
}
function formatTimeHHMMSS(d){
  return `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
}
function updateClock(){
  const now = new Date();
  liveDateTime.textContent = `${formatDateDDMMYYYY(now)}  ${formatTimeHHMMSS(now)}`;
}
setInterval(updateClock, 1000);
updateClock();

// ----------------- helpers -----------------
function calcDuration(inTime, outTime){
  // Accepts time strings (ISO or 'HH:MM:SS' or 'HH:MM')
  if(!inTime || !outTime) return '-';
  try{
    const a = new Date(inTime);
    const b = new Date(outTime);
    // If date parsing fails (invalid date), try parse time-only on today
    if(isNaN(a.getTime())){
      const today = new Date();
      const [h1,m1,s1='0'] = String(inTime).split(':');
      a.setHours(Number(h1||0), Number(m1||0), Number(s1||0),0);
    }
    if(isNaN(b.getTime())){
      const today = new Date();
      const [h2,m2,s2='0'] = String(outTime).split(':');
      b.setHours(Number(h2||0), Number(m2||0), Number(s2||0),0);
    }
    let diff = Math.max(0, (b.getTime() - a.getTime())); // ms
    const hours = Math.floor(diff / (1000*60*60));
    diff -= hours * (1000*60*60);
    const minutes = Math.floor(diff / (1000*60));
    return `${pad(hours)}:${pad(minutes)}`;
  }catch(e){
    return '-';
  }
}

// format incoming date (accepts ISO, yyyy-mm-dd, etc) to dd/mm/yyyy
function toDDMMYYYY(val){
  if(!val) return '-';
  try{
    const d = new Date(val);
    if(!isNaN(d.getTime())){
      return formatDateDDMMYYYY(d);
    }
    // fallback parse yyyy-mm-dd
    const parts = String(val).split(' ')[0].split('-');
    if(parts.length>=3){
      return `${pad(Number(parts[2]))}/${pad(Number(parts[1]))}/${parts[0]}`;
    }
    return val;
  }catch(e){
    return val;
  }
}

// small toast helper (SweetAlert)
function toastSuccess(msg){
  Swal.fire({toast:true,position:'top-end',icon:'success',title:msg,showConfirmButton:false,timer:1400});
}
function toastError(msg){
  Swal.fire({toast:true,position:'top-end',icon:'error',title:msg,showConfirmButton:false,timer:1800});
}

// ----------------- API calls (same endpoints as your original code) -----------------
async function loadAttendance(){
  try{
    const res = await fetch('api/employee/index', { headers: { 'Authorization': 'Bearer ' + token } });
    const data = await res.json();

    // show name
    const empName = data?.employee?.emp_code || data?.employee?.name || 'Employee';
    console.log(empName);
    empNameEl.textContent = empName;
    userNameEl.textContent = `👤 ${empName}`;

    // status (isIn if attendance exists and out_time absent)
    const isIn = data.attendance && !data.attendance.out_time;
    statusPill.textContent = isIn ? 'IN' : 'OUT';
    statusPill.className = 'pill ' + (isIn ? 'in' : 'out');

    // enable/disable buttons
    markInBtn.disabled = !!isIn;
    markOutBtn.disabled = !isIn;

    // then fetch history
    await loadAttendanceHistory();
  }catch(err){
    console.error(err);
    Swal.fire('Error', 'Failed to load employee info', 'error');
  }
}

async function loadAttendanceHistory(){
  historyList.innerHTML = `<div style="text-align:center;color:#6b7280;padding:12px">Loading...</div>`;
  try{
    const res = await fetch('api/employee/attendance/history', { headers: { 'Authorization': 'Bearer ' + token } });
    const data = await res.json();

    const rows = data.month_records || data.records || [];
    if(!rows.length){
      historyList.innerHTML = `<div style="text-align:center;color:#6b7280;padding:12px">No records found</div>`;
      return;
    }

    // Build cards: need Sr No, Date (dd/mm/yyyy), IN, OUT, Duration, Status
    historyList.innerHTML = rows.map((r, idx) => {
      const date = toDDMMYYYY(r.date || r.att_date || r.created_at || r.in_time || '-');
      // IN/OUT values: r.in_time/r.out_time may be full timestamps; keep time part HH:MM
      const inTimeRaw = r.in_time || r.in_at || r.in;
      const outTimeRaw = r.out_time || r.out_at || r.out;
      const inTime = inTimeRaw ? (new Date(inTimeRaw).toTimeString?.()?.split(' ')[0] || String(inTimeRaw).split(' ')[1] || String(inTimeRaw).split('T')[1] || inTimeRaw) : '-';
      const outTime = outTimeRaw ? (new Date(outTimeRaw).toTimeString?.()?.split(' ')[0] || String(outTimeRaw).split(' ')[1] || String(outTimeRaw).split('T')[1] || outTimeRaw) : '-';
      const duration = (inTimeRaw && outTimeRaw) ? calcDuration(inTimeRaw, outTimeRaw) : '-';
      const statusText = (r.out_time || r.out_at) ? 'Completed' : 'Active';
      const statusClass = (r.out_time || r.out_at) ? 'status-present' : 'status-absent';
      return `
        <div class="row-card" role="row">
          <div class="row-left">
            <div class="sr">${idx+1}</div>
            <div class="date small" style="margin-top:6px">${date}</div>
          </div>
          <div class="col in_out">
            <div class="small">IN: <span style="font-weight:700">${inTime || '-'}</span></div>
            <div class="small">OUT: <span style="font-weight:700">${outTime || '-'}</span></div>
          </div>
          <div class="col" style="text-align:right">
            <div class="duration">⏱ ${duration}</div>
            <div style="height:8px"></div>
            <div>
            <span class="status-badge ${statusClass}">${statusText}</span>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }catch(err){
    console.error(err);
    historyList.innerHTML = `<div style="text-align:center;color:red;padding:12px">Error fetching data</div>`;
  }
}

// ----------------- mark attendance (keeps original API usage) -----------------
function getPosition(){ // helper that returns a Promise resolving {latitude,longitude}
  return new Promise((resolve, reject) => {
    if(!navigator.geolocation) return reject(new Error('Geolocation not supported'));
    navigator.geolocation.getCurrentPosition(pos => {
      resolve({ latitude: pos.coords.latitude, longitude: pos.coords.longitude });
    }, err => reject(err), { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 });
  });
}

async function markAttendance(type, reason=null){
  try{
    // get coords
    const coords = await getPosition().catch(e=>{
      Swal.fire('Location required', 'Please enable location to mark attendance', 'warning');
      throw e;
    });

    // prepare body (kept same fields as your original)
    const body = { latitude: coords.latitude, longitude: coords.longitude };
    if(reason) body.reason = reason;

    // POST (same endpoint)
    const res = await fetch('api/employee/attendance/mark', {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'Authorization':'Bearer ' + token },
      body: JSON.stringify(body)
    });
    const data = await res.json();
    if(data?.status === 'success' || res.ok){
      toastSuccess(data.message || `Marked ${type.toUpperCase()} successfully`);
      // reload state
      await loadAttendance();
    } else {
      toastError(data?.message || 'Failed to mark attendance');
    }
  }catch(err){
    console.error(err);
    if(err && err.code === 1){
      Swal.fire('Permission denied', 'Allow location permission to proceed', 'error');
    } else {
      // generic
      toastError('Unable to mark attendance (network/location).');
    }
  }
}

// ----------------- UI bindings -----------------
markInBtn.addEventListener('click', () => {
  // keep original behaviour: directly mark IN
  markAttendance('in');
});

markOutBtn.addEventListener('click', () => {
  // open reason modal
  reasonSelect.value = '';
  reasonModal.classList.add('active');
});

reasonSubmit.addEventListener('click', () => {
  const reason = reasonSelect.value.trim();
  if(!reason){
    Swal.fire('Select reason', 'Please choose a reason before submitting', 'warning');
    return;
  }
  reasonModal.classList.remove('active');
  markAttendance('out', reason);
});

// close modal on click outside
reasonModal.addEventListener('click', (e)=>{
  if(e.target === reasonModal) reasonModal.classList.remove('active');
});

// logout behaviour (same as original)
logoutBtn.addEventListener('click', async () => {
  try{
    await fetch('api/employee/logout', { method:'POST', headers:{ 'Authorization':'Bearer ' + token }});
  }catch(e){
    // ignore
  } finally {
    localStorage.clear();
    window.location.href = 'emp-login';
  }
});

// initial load
loadAttendance();
</script>
</body>
</html>
