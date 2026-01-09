<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Employee / Vehicle Attendance</title>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Html5Qrcode -->
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
  :root{
    --primary:#0f62ff;
    --muted:#6b7280;
    --success:#16a34a;
    --danger:#ef4444;
    --card:#ffffff;
    --bg:#f3f6fb;
    --glass: rgba(255,255,255,0.85);
  }

  *{box-sizing:border-box;font-family:Inter, Poppins, "Segoe UI", system-ui, -apple-system, sans-serif}
  body{margin:0;background:linear-gradient(180deg,#e9f2ff, #f6fbff);min-height:100vh;padding:18px 14px;color:#0f172a}

  .wrap{max-width:480px;margin:0 auto;display:flex;flex-direction:column;gap:14px}

  header{
    background:linear-gradient(135deg,var(--primary), #7c9bff);
    color:white;padding:16px;border-radius:14px;box-shadow:0 8px 30px rgba(15,98,255,0.12);
    display:flex;flex-direction:column;gap:8px;
  }
  header h1{margin:0;font-size:18px;font-weight:600}

  .search-row{display:flex;gap:8px;align-items:center}
  .search-row input{
    flex:1;padding:10px 12px;border-radius:10px;border:none;font-size:15px;border:1px solid rgba(255,255,255,0.12);
    background:rgba(255,255,255,0.08);color:#fff;outline:none;
  }
  .search-row button{
    padding:10px 12px;border-radius:10px;border:none;background:rgba(255,255,255,0.14);color:#fff;font-weight:700;cursor:pointer;
  }

  /* TYPE DROPDOWN */
  .type-select {
    width: 140px;
    padding: 10px 12px;
    border-radius: 10px;
    border: none;
    background: rgba(255,255,255,0.12);
    color: #fff;
    font-weight:700;
    cursor:pointer;
  }

  /* SCAN QR BUTTON */
  #scanBtn{
    background:rgba(255,255,255,0.25);
    font-weight:700;
    padding:10px 12px;
    border-radius:10px;
    cursor:pointer;
    border:none;
    color:white;
    display:flex;
    align-items:center;
    gap:6px;
  }

  /* QR MODAL */
  #qrModal{
    position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,.55);display:none;
    align-items:center;justify-content:center;
    z-index:9999;
  }
  #qrModal.active{display:flex;}
  #qrBox{
    background:white;border-radius:14px;
    padding:10px;width:90%;max-width:420px;
  }
  #closeQR{
    margin-top:10px;width:100%;padding:10px;border:none;
    background:#0f62ff;color:white;font-weight:700;
    border-radius:8px;
  }

  /* Employee UI */
  .emp-card{
    background:var(--card);padding:14px;border-radius:12px;box-shadow:0 8px 24px rgba(2,6,23,0.06);
    display:flex;gap:12px;align-items:center;
  }
  .avatar{
    width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,#eef2ff,#dfeaff);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--primary);
  }
  .emp-meta{flex:1}
  .emp-meta h3{margin:0;font-size:16px;color:#0b3d91}
  .emp-meta p{margin:4px 0;font-size:13px;color:var(--muted)}
  .status-badge{padding:6px 10px;border-radius:999px;font-weight:700;font-size:13px}
  .status-gray{background:#f1f5f9;color:#475569}
  .status-in{background:#ecfdf5;color:#065f46}
  .status-out{background:#fff1f2;color:#991b1b;border:1px solid #fecaca}

  .action-wrap{text-align:center}
  .mark-btn{
    display:inline-flex;align-items:center;gap:10px;padding:12px 18px;border-radius:12px;border:none;background:linear-gradient(90deg,var(--primary),#4f8bff);
    color:#fff;font-weight:700;font-size:15px;cursor:pointer;box-shadow:0 8px 24px rgba(15,98,255,0.12)
  }
  .mark-btn.secondary {
    background: linear-gradient(90deg,#16a34a,#059669);
  }
  .mark-btn.out {
    background: linear-gradient(90deg,#fb7185,#dc2626);
  }
  .mark-btn:disabled{opacity:.55;cursor:not-allowed}

  .history-title{margin:6px 0 0 4px;color:#0b3d91;font-weight:700;font-size:15px}
  .history-grid{display:grid;grid-template-columns:1fr;gap:12px;margin-top:10px;padding-bottom:10px}
  .hist-card{
    background:linear-gradient(180deg,#ffffff,#fbfdff);border-radius:12px;padding:12px;border:1px solid #eef2ff;
    box-shadow:0 6px 18px rgba(2,6,23,0.04);
  }
  .hist-row{display:flex;justify-content:space-between;gap:8px;align-items:center}
  .hist-left{display:flex;flex-direction:column;gap:6px}
  .point{font-size:13px;color:#0b3d91;font-weight:700}
  .time{font-size:13px;color:#374151}
  .meta{font-size:12px;color:var(--muted)}
  .reason{display:inline-block;margin-top:6px;padding:6px 8px;border-radius:8px;background:#fff7ed;color:#92400e;font-weight:600;font-size:12px;border:1px solid #ffe8cc}

  .empty{padding:18px;text-align:center;color:var(--muted);font-style:italic}

  @media(min-width:720px){
    .wrap{max-width:760px}
    .history-grid{grid-template-columns:repeat(2,1fr)}
  }

  .search-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 15px 0;
}

/* Input Style */
.search-row input {
    flex: 1;
    padding: 12px 14px;
    font-size: 16px;
    border: 2px solid #ddd;
    border-radius: 8px;
}

/* Buttons Same Style */
.search-row button {
    padding: 12px 16px;
    font-size: 15px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

/* FETCH button */
#fetchBtn {
    background: #642abf;
    color: #fff;
}

/* SCAN button */
#scanBtn {
    background: #28a745;
    color: #fff;
}

/* MOBILE RESPONSIVE FIX */
@media (max-width: 768px) {
    .search-row {
        flex-direction: column;
        align-items: stretch;
    }

    .search-row input,
    .search-row button, .type-select {
        width: 100%;
        font-size: 17px;
        padding: 14px;
    }

    #scanBtn {
        font-size: 18px;
    }
}

/* QR Modal */
#qrModal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(7px);
    background: rgba(0,0,0,0.55);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 999999;
}
#qrModal.active { display:flex; }
#qrModal .modal-content {
    width: 92%;
    max-width: 420px;
    background: rgba(255,255,255,0.95);
    padding: 20px 15px;
    border-radius: 20px;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0px 8px 25px rgba(0,0,0,0.15);
    position: relative;
}
#qrModal .close-btn {
    position: absolute;
    top: -15px;
    right: -15px;
    background: #ff4d6d;
    color: white;
    border: none;
    width: 38px;
    height: 38px;
    font-size: 20px;
    border-radius: 50%;
    cursor: pointer;
}
#reader {
    width: 100% !important;
    height: 360px !important;
    border-radius: 15px;
    overflow: hidden;
    background: rgba(255,255,255,0.3);
    border: 2px solid rgba(255,255,255,0.4);
}
.scan-text { margin-top: 12px; text-align: center; color: #0f172a; font-size: 16px; }
</style>

</head>
<body>

<div class="wrap">

  <header>
    <h1>Attendance</h1>

    <div class="search-row">
      <select id="typeSelect" class="type-select" title="Select Type">
        <option value="Plant">Plant</option>
        <option value="Vehicle">Vehicle</option>
      </select>

      <input id="emp_code" type="text" placeholder="Enter employee code (e.g. E101)" />
      <button id="fetchBtn">Fetch</button>
     <button id="scanBtn" class="btn btn-primary">
        <i class="fas fa-camera mr-2"></i> Scan
    </button>

    </div>
  <button id="clearBtn" style="
    background:#6c757d;
    color:#fff;
    padding:10px 12px;
    border:none;
    border-radius:10px;
    font-weight:700;
    cursor:pointer;
    ">Clear</button>

  </header>

  <!-- QR Modal -->
<div id="qrModal">
    <div class="modal-content">
        <button id="closeQR" class="close-btn">âœ•</button>

        <!-- Scanner Box -->
        <div id="reader"></div>

        <div class="scan-text">Align QR Code inside the box</div>
    </div>
</div>

  <!-- Employee / Vehicle Section -->
  <div id="empSection" style="display:none;">
    <div class="emp-card">
      <div class="avatar" id="avatarInitial">A</div>
      <div class="emp-meta">
        <h3 id="entity_name">Loading...</h3>
        {{--<p id="emp_code_display">Code: -</p>--}}
      </div>
      <div>
        <div id="statusPill" class="status-badge status-gray">-</div>
      </div>
    </div>

    <div class="action-wrap" style="margin-top:10px">
      <!-- BOTH buttons visible always -->
      <button id="markInBtn" class="mark-btn secondary">Mark IN</button>
      <button id="markOutBtn" class="mark-btn out" style="margin-left:8px">Mark OUT</button>
    </div>

    <div style="margin-top:8px">
      <div class="history-title">This Month History</div>
      <div id="historyList" class="history-grid">
        <div class="empty">Loading...</div>
      </div>
    </div>
  </div>

</div>

<script>
/* ========== CONFIG - set your real API roots ========== */
const baseEmployee = "/api/employee/attendance"; // used: index?emp_code=..., history?emp_code=..., mark
const baseVehicle  = "/api/vehicle/attendance";  // used: index?vehicle_no=..., history?vehicle_no=..., mark

const $ = id => document.getElementById(id);

/* ---------- Robust QR parse ---------- */
function tryParseScanned(raw) {
  if (!raw) return null;
  try {
    let s = String(raw).trim();
    // remove wrapping quotes
    if ((s.startsWith('"') && s.endsWith('"')) || (s.startsWith("'") && s.endsWith("'"))) {
      s = s.substring(1, s.length - 1);
    }
    // unescape common escapes
    s = s.replace(/\\"/g, '"').replace(/\\'/g, "'");
    try {
      const parsed = JSON.parse(s);
      if (typeof parsed === 'string') {
        return JSON.parse(parsed);
      }
      return parsed;
    } catch (e) {
      // not JSON -> return raw string
      return s;
    }
  } catch (e) {
    return null;
  }
}

/* ---------- UI Helper: format date/time dd/mm/yyyy & hh:mm ---------- */
function formatDateTime(input) {
  if (!input) return { date: '-', time: '-' };
  try {
    // Accept "YYYY-MM-DD HH:mm:ss" or ISO etc.
    let s = String(input).trim();
    if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(s)) s = s.replace(' ', 'T');
    const d = new Date(s);
    if (isNaN(d)) return { date: '-', time: '-' };
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const yyyy = d.getFullYear();
    const hh = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    return { date: `${dd}/${mm}/${yyyy}`, time: `${hh}:${min}` };
  } catch (e) {
    return { date: '-', time: '-' };
  }
}

/* ---------- Render history (expects month_records array with fields type, attendance_date_time, geo_fencing_point, reason) ---------- */
function renderHistory(records) {
  const container = $('historyList');
  container.innerHTML = '';

  if (!records || records.length === 0) {
    container.innerHTML = '<div class="empty">No records this month.</div>';
    return;
  }

  // group by date dd/mm/yyyy
  const groups = {};
  records.forEach(rec => {
    const dt = rec.attendance_date_time || rec.attendance_date || rec.datetime || '';
    const f = formatDateTime(dt);
    const key = f.date; // dd/mm/yyyy or '-'
    if (!groups[key]) groups[key] = [];
    groups[key].push({ rec, time: f.time });
  });

  // sort dates descending (most recent first)
  const dates = Object.keys(groups).sort((a,b) => {
    const conv = s => {
      const parts = s.split('/');
      if (parts.length !== 3) return 0;
      return new Date(`${parts[2]}-${parts[1]}-${parts[0]}`).getTime();
    };
    return conv(b) - conv(a);
  });

  dates.forEach(date => {
    // date title
    const dtEl = document.createElement('div');
    dtEl.className = 'date-title';
    dtEl.textContent = date;
    container.appendChild(dtEl);

    // records for date
    groups[date].forEach(item => {
      const rec = item.rec;
      const time = item.time || '-';

      const card = document.createElement('div');
      card.className = 'time-card hist-card';

      // inner HTML (keeps original classes)
      card.innerHTML = `
        <div class="hist-row">
          <div class="hist-left">
            <div class="point">${escapeHtml(rec.geo_fencing_point || '-')}</div>
            <div class="time">${escapeHtml(time)}</div>
            <div class="meta">Type: <strong>${escapeHtml((rec.type || '-').toUpperCase())}</strong></div>
          </div>
        </div>
        ${rec.reason ? `<div class="reason">${escapeHtml(rec.reason)}</div>` : ''}
      `;

      container.appendChild(card);
    });
  });
}

/* small helper to avoid XSS */
function escapeHtml(text) {
  if (text === null || text === undefined) return '';
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

/* ---------- Fetch index (employee/vehicle) and history ---------- */
async function fetchIndexAndHistory() {
  const code = $('emp_code').value.trim();
  const type = $('typeSelect').value;

  if (!code) {
    Swal.fire({ icon:'warning', title:'Enter Code / Vehicle No' });
    return;
  }

  // show loading placeholder
  $('historyList').innerHTML = '<div class="empty">Loading...</div>';

  try {
    let indexUrl, historyUrl;
    if (type === 'Plant') {
      indexUrl = `${baseEmployee}/index?emp_code=${encodeURIComponent(code)}`;
      historyUrl = `${baseEmployee}/history?emp_code=${encodeURIComponent(code)}`;
    } else {
      indexUrl = `${baseVehicle}/index?vehicle_no=${encodeURIComponent(code)}`;
      historyUrl = `${baseVehicle}/history?vehicle_no=${encodeURIComponent(code)}`;
    }

    // fetch index
    const idxResp = await fetch(indexUrl, { credentials:'include' });
    if (!idxResp.ok) throw new Error('Index fetch failed');
    const idxJson = await idxResp.json();

    // validate and populate UI
    let entityName = code;
    let typeio = null;
    if (type === 'Plant') {
      if (!idxJson || !idxJson.employee) {
        $('empSection').style.display = 'none';
        Swal.fire({ icon:'error', title:'Invalid Employee Code' });
        return;
      }
      entityName = idxJson.employee.emp_name || idxJson.employee.name || code;
      typeio = idxJson.attendance.type||'N.A';
    } else {
      // vehicle
      const vehicle = idxJson.vehicle || idxJson.record || idxJson;
      if (!vehicle || (!vehicle.vehicle_no && !vehicle.number && !vehicle.reg_no && !vehicle.id)) {
        $('empSection').style.display = 'none';
        Swal.fire({ icon:'error', title:'Invalid Vehicle No' });
        return;
      }
      entityName = vehicle.vehicle_no || vehicle.number || vehicle.reg_no || vehicle.name || code;
      typeio = idxJson?.attendance?.type ?? 'N.A';

    }

    // show basic UI
    $('empSection').style.display = 'block';
    $('entity_name').textContent = entityName;
    // $('emp_code_display').textContent = (type === 'Plant' ? "Code: " : "Reg: ") + code;
    $('avatarInitial').textContent = (entityName || code)[0]?.toUpperCase() || 'A';

    // determine and show status pill (try to use attendance/last_status fields)
    const attendanceInfo = idxJson.attendance || idxJson.last_record || idxJson.record || {};
    if (attendanceInfo.last_status === 'in' || (attendanceInfo.in_time && !attendanceInfo.out_time)) {
      $('statusPill').textContent = 'IN';
      $('statusPill').className = 'status-badge status-in';
    } else if (attendanceInfo.last_status === 'out' || (attendanceInfo.out_time && !attendanceInfo.in_time)) {
      $('statusPill').textContent = 'OUT';
      $('statusPill').className = 'status-badge status-out';
    } else {
      $('statusPill').textContent = 'Not Marked';
      $('statusPill').className = 'status-badge status-gray';
    }

    // fetch history
    const hResp = await fetch(historyUrl, { credentials:'include' });
    if (!hResp.ok) throw new Error('History fetch failed');
    const hJson = await hResp.json();

    // expect month_records or records or month_records array
    const records = hJson.month_records || hJson.records || hJson.history || [];
    renderHistory(records);

  } catch (err) {
    console.error(err);
    $('historyList').innerHTML = '<div class="empty">Error loading records.</div>';
    Swal.fire({ icon:'error', title:'Network or server error' });
  }
}

/* attach fetch button */
$('fetchBtn').addEventListener('click', fetchIndexAndHistory);

/* ---------- Mark IN / OUT handlers ---------- */
async function markAttendance(action) {
  const code = $('emp_code').value.trim();
  const type = $('typeSelect').value;
  const typeios = $('statusPill').innerText;
  if (!code) {
    Swal.fire({ icon:'warning', title:'Enter Code / Vehicle No' });
    return;
  }
  
   if (action == typeios.toLowerCase()) {
      Swal.fire({ icon:'warning', title:'You already in/out' });
      return;
  }
  
  console.log("action:-",action);
  console.log("typeios.:-",typeios.toLowerCase())

  // For OUT ask for reason optionally (follow earlier pattern)
  let reason = null;
  if (action === 'out') {
    const { value } = await Swal.fire({
      title: 'Select reason for OUT (optional)',
      input: 'select',
      inputOptions: {
        "Other Plant":"Other Plant",
        "Lunch / Dinner":"Lunch / Dinner",
        "Official Work":"Official Work",
        "Personal Work":"Personal Work",
        "End of Day":"End of Day",
        "Early Leave / OFF Duty":"Early Leave / OFF Duty"
      },
      inputPlaceholder: 'Select reason (optional)',
      showCancelButton: true
    });
    if (value === undefined) return; // cancelled
    reason = value || null;
  }

  // get geolocation
  if (!navigator.geolocation) {
    Swal.fire({ icon:'error', title:'Geolocation not supported' });
    return;
  }

  Swal.fire({ title:'Getting location...', allowOutsideClick:false, didOpen: ()=> Swal.showLoading() });
  navigator.geolocation.getCurrentPosition(async (pos) => {
    Swal.close();
    const lat = pos.coords.latitude;
    const lon = pos.coords.longitude;

    const payload = {
      latitude: lat,
      longitude: lon,
      type: action // send in request that user marked 'in' or 'out'
    };

    if (type === 'Plant') payload.emp_code = code;
    else payload.vehicle_no = code;

    if (reason) payload.reason = reason;

    try {
      const endpoint = (type === 'Plant') ? `${baseEmployee}/mark` : `${baseVehicle}/mark`;
      const res = await fetch(endpoint, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        credentials: 'include',
        body: JSON.stringify(payload),
      });
      const data = await res.json();

      // If backend asks for reason explicitly
      if (data && data.require_reason) {
        const { value } = await Swal.fire({
          title: 'Select reason for OUT',
          input: 'select',
          inputOptions: {
            "Other Plant":"Other Plant",
            "Lunch / Dinner":"Lunch / Dinner",
            "Official Work":"Official Work",
            "Personal Work":"Personal Work",
            "End of Day":"End of Day",
            "Early Leave / OFF Duty":"Early Leave / OFF Duty"
          },
          inputPlaceholder: 'Select reason',
          showCancelButton: true
        });
        if (!value) return;
        payload.reason = value;
        const res2 = await fetch(endpoint, {
          method:'POST', headers:{'Content-Type':'application/json'}, credentials:'include', body:JSON.stringify(payload)
        });
        const d2 = await res2.json();
        if (d2 && (d2.status === 'success' || d2.success === true)) {
          Swal.fire({ icon:'success', title: d2.message || 'Marked successfully' });
          fetchIndexAndHistory();
        } else {
          Swal.fire({ icon:'error', title: d2.message || 'Failed' });
        }
        return;
      }

      if (data && (data.status === 'success' || data.success === true)) {
        Swal.fire({ icon:'success', title: data.message || 'Marked successfully' });
        fetchIndexAndHistory();
      } else {
        Swal.fire({ icon:'error', title: (data && (data.message || data.error)) || 'Failed to mark attendance' });
      }

    } catch (err) {
      console.error(err);
      Swal.fire({ icon:'error', title:'Network or server error' });
    }

  }, (err) => {
    Swal.close();
    console.error(err);
    Swal.fire({ icon:'error', title:'Unable to get your location.' });
  }, { enableHighAccuracy:true, timeout:15000, maximumAge:0 });
}

/* attach mark buttons (both visible always) */
$('markInBtn').addEventListener('click', () => markAttendance('in'));
$('markOutBtn').addEventListener('click', () => markAttendance('out'));

/* ---------- QR Scanner (browser + Appilix fallback) ---------- */
let qrScanner = null;
function isAppilix() {
  return (typeof appilix !== "undefined");
}

$('scanBtn').addEventListener('click', () => {
  if (isAppilix()) {
    // native app layer
    appilix.postMessage(JSON.stringify({
      type: "qr_scanner_init",
      props: { enable_confirmation_popup: false}
    }));

    appilix.onmessage = function (event) {
      try {
        const msg = (typeof event.data === 'string') ? event.data : JSON.stringify(event.data);
        const resp = JSON.parse(msg);
        const resultRaw = resp?.response?.result ?? resp?.result ?? null;
        if (!resultRaw) return;
        const parsed = tryParseScanned(resultRaw);
        if (!parsed) {
          // fallback: plain code
          $('emp_code').value = resultRaw;
        //   fetchIndexAndHistory();
          return;
        }
        
        
        // if (typeof parsed === 'string') {
        //   $('emp_code').value = parsed;
        //   fetchIndexAndHistory();
        //   return;
        // }
        
        // let t = parsed.type || parsed.type_name || parsed.typeName || 'Plant';
        // t = String(t).trim();
        let codeVal = parsed.code ||parsed.employee_id || parsed.emp_code || parsed.vehicle_no || parsed.vehicle || parsed.id;
        if (!codeVal) {
          Swal.fire({ icon: "error", title: "Invalid QR", text: "Missing code / vehicle_no" });
          return;
        }
        // $('typeSelect').value = (t.toLowerCase().startsWith('v') ? 'Vehicle' : 'Plant');
        // $('emp_code').value = codeVal;
        // fetchIndexAndHistory();
        Swal.fire({
                 title: "Confirm QR?",
                 html: generateConfirmHTML(parsed), // function dynamically creates HTML
                 icon: "question",
                 showCancelButton: true,
                 confirmButtonText: "Yes, Use This",
                 cancelButtonText: "Cancel"
            }).then(result => {
                     if (!result.isConfirmed) return; // Cancel → do nothing
                   
                     // Confirm → Fill fields
                     let t = parsed?.type || parsed?.type_name || parsed?.typeName || 'Plant';
                     t = String(t).trim();
                   
                     $('typeSelect').value = (t.toLowerCase().startsWith('v') ? 'Vehicle' : 'Plant');
                     $('emp_code').value = codeVal;
                   
                     fetchIndexAndHistory(); // only after confirm
             });

      } catch (err) {
        console.error("Appilix parse error:", err);
      }
    };
    return;
  }

  // Browser mode
  $('qrModal').classList.add('active');

  setTimeout(() => {
    if (qrScanner) {
      try { qrScanner.stop(); } catch(e){/*ignore*/ }
      qrScanner = null;
    }

    qrScanner = new Html5Qrcode("reader");
    qrScanner.start(
      { facingMode: "environment" },
      { fps: 10, qrbox: 250 },
      decoded => {
        const parsed = tryParseScanned(decoded);
        try {
          // stop scanner
          qrScanner.stop().catch(()=>{/*ignore*/});
        } catch(e){}
        qrScanner = null;
        $('qrModal').classList.remove('active');

        if (!parsed) {
          // fallback: scanned string
          $('emp_code').value = decoded;
          fetchIndexAndHistory();
          return;
        }
        if (typeof parsed === 'string') {
          $('emp_code').value = parsed;
          fetchIndexAndHistory();
          return;
        }

        let t = parsed.type || parsed.type_name || parsed.typeName || 'Plant';
        t = String(t).trim();
        let codeVal = parsed.code || parsed.emp_code || parsed.vehicle_no || parsed.vehicle || parsed.id;
        if (!codeVal) {
          Swal.fire({ icon: "error", title: "Invalid QR", text: "Missing code / vehicle_no in QR JSON" });
          return;
        }

        $('typeSelect').value = (t.toLowerCase().startsWith('v') ? 'Vehicle' : 'Plant');
        $('emp_code').value = codeVal;
        fetchIndexAndHistory();
      },
      error => { /* ignore decode errors */ }
    ).catch(err => {
      console.error("QR start error:", err);
      Swal.fire({ icon: "error", title: "Cannot access camera", text: err.message || err });
      $('qrModal').classList.remove('active');
    });

  }, 200);
});

/* close QR modal */
$('closeQR').addEventListener('click', () => {
  if (qrScanner) {
    try { qrScanner.stop(); } catch(e){/*ignore*/ }
    qrScanner = null;
  }
  $('qrModal').classList.remove('active');
});

/* ---------- Init: optional code from query params ---------- */
(function init() {
  const p = new URLSearchParams(window.location.search);
  const q = p.get('code') || p.get('emp_code') || '';
  if (q) {
    $('emp_code').value = q;
    // do not auto-fetch; let user press fetch to avoid accidental network calls
  }
})();




$('clearBtn').addEventListener('click', () => {

    // Clear input
    $('emp_code').value = "";

    // Hide section
    $('empSection').style.display = 'none';

    // Reset history
    $('historyList').innerHTML = '<div class="empty">No records.</div>';

    // Reset status badge
    $('statusPill').textContent = '-';
    $('statusPill').className = 'status-badge status-gray';

    // Reset name & avatar
    $('entity_name').textContent = '';
    // $('emp_code_display').textContent = 'Code: -';
    $('avatarInitial').textContent = 'A';

    // Stop QR scanner if open
    try {
        if (window.html5QrCode) {
            window.html5QrCode.stop();
        }
    } catch (e) {}

    // Close QR modal
    $('qrModal').classList.remove('active');
});

function generateConfirmHTML(parsed) {
  let type = (parsed?.type || parsed?.type_name || parsed?.typeName || 'Plant').toLowerCase();
  let html = '<div style="text-align:left; font-size:16px;">';

  if (type.startsWith('v')) {
    // Vehicle fields
    // html += `<b>Vehicle No:</b>${parsed.vehicle_no}<br>`;
    html += `<b>Vehicle Type:</b> ${parsed.vehicle_type || '-'}<br>`;
    html += `<b>Pass No:</b> ${parsed.pass_no || '-'}<br>`;
    html += `<b>Name:</b> ${parsed.name || '-'}<br>`;
    html += `<b>mobile No:</b> ${parsed.mobile_no || '-'}<br>`;

  } else {
    // Plant fields
   
    html += `<b>Name:</b> ${parsed.name || '-'}<br>`;
    html += `<b>Mobile No:</b> ${parsed.mobile || '-'}<br>`;
  }

  html += '</div>';
  return html;
}


</script>

</body>
</html>
