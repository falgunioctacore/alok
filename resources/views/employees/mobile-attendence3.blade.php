<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Employee Attendance</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

  /* Container (mobile-first) */
  .wrap{max-width:480px;margin:0 auto;display:flex;flex-direction:column;gap:14px}

  /* Header */
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

  /* Employee info card */
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

  /* Action button */
  .action-wrap{text-align:center}
  .mark-btn{
    display:inline-flex;align-items:center;gap:10px;padding:12px 18px;border-radius:12px;border:none;background:linear-gradient(90deg,var(--primary),#4f8bff);
    color:#fff;font-weight:700;font-size:15px;cursor:pointer;box-shadow:0 8px 24px rgba(15,98,255,0.12)
  }
  .mark-btn:disabled{opacity:.55;cursor:not-allowed}

  /* History cards grid */
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

  /* Tablet: 2-column history */
  @media(min-width:720px){
    .wrap{max-width:760px}
    .history-grid{grid-template-columns:repeat(2,1fr)}
  }
</style>
</head>
<body>
  <div class="wrap">
    <header>
      <h1>Attendance</h1>
      <div class="search-row">
        <input id="emp_code" type="text" placeholder="Enter employee code (e.g. E101)" />
        <button id="fetchBtn">Fetch</button>
      </div>
    </header>

    <!-- Employee card (hidden until fetch) -->
    <div id="empSection" style="display:none;">
      <div class="emp-card">
        <div class="avatar" id="avatarInitial">A</div>
        <div class="emp-meta">
          <h3 id="emp_name">Loading...</h3>
          <p id="emp_code_display">Code: -</p>
        </div>
        <div>
          <div id="statusPill" class="status-badge status-gray">-</div>
        </div>
      </div>

      <div class="action-wrap" style="margin-top:10px">
        <button id="markBtn" class="mark-btn">Mark Attendance</button>
      </div>

      <div style="margin-top:8px">
        <div class="history-title">This Month Attendance</div>
        <div id="historyList" class="history-grid" style="margin-top:8px">
          <div class="empty">Loading...</div>
        </div>
      </div>
    </div>
  </div>

<script>
const baseUrl = "/api/employee/attendance";

// helpers
const $ = id => document.getElementById(id);

function setStatusUI(att){
  const pill = $('statusPill');
  const markBtn = $('markBtn');

  if(!att || !att.in_time){
    pill.textContent = 'Not Marked';
    pill.className = 'status-badge status-gray';
    markBtn.textContent = 'Mark IN';
    markBtn.dataset.action = 'in';
  } else if (att.in_time && !att.out_time){
    pill.textContent = 'IN';
    pill.className = 'status-badge status-in';
    markBtn.textContent = 'Mark OUT';
    markBtn.dataset.action = 'out';
  } else {
    pill.textContent = 'OUT';
    pill.className = 'status-badge status-out';
    markBtn.textContent = 'Mark IN';
    markBtn.dataset.action = 'in';
  }
}

// FETCH employee + today's attendance
$('fetchBtn').addEventListener('click', async () => {
  const code = $('emp_code').value.trim();
  if(!code) return Swal.fire({icon:'warning', title:'Enter Employee Code'});

  try{
    $('historyList').innerHTML = '<div class="empty">Loading...</div>';
    const res = await fetch(`${baseUrl}/index?emp_code=${encodeURIComponent(code)}`);
    const data = await res.json();
    if(!data.employee){
      $('empSection').style.display = 'none';
      return Swal.fire({icon:'error', title:'Invalid Employee Code'});
    }

    // show UI
    $('empSection').style.display = 'block';
    $('emp_name').textContent = data.employee.emp_name;
    $('emp_code_display').textContent = 'Code: ' + data.employee.emp_code;
    // avatar initial
    $('avatarInitial').textContent = (data.employee.emp_name || 'A').trim()[0]?.toUpperCase() || 'A';

    setStatusUI(data.attendance);

    // load month history (cards)
    loadMonthData(code);
  }catch(err){
    console.error(err);
    $('historyList').innerHTML = '<div class="empty">Error loading records.</div>';
    Swal.fire({icon:'error', title:'Server error occurred'});
  }
});

// Load month records - render as cards (mobile friendly)
async function loadMonthData(empCode){
  try{
    const res = await fetch(`${baseUrl}/history?emp_code=${encodeURIComponent(empCode)}`);
    const data = await res.json();
    const list = data.month_records || [];

    const container = $('historyList');
    container.innerHTML = '';
    if(!list.length){
      container.innerHTML = '<div class="empty">No attendance records for this month.</div>';
      return;
    }

    list.forEach(rec => {
      const card = document.createElement('div');
      card.className = 'hist-card';

      // build friendly date/time display
      const inTime = rec.in_time ? rec.in_time.replace(' ', ' • ') : '-';
      const outTime = rec.out_time ? rec.out_time.replace(' ', ' • ') : '-';
      const inPoint = rec.in_geo_fencing_point || '-';
      const outPoint = rec.out_geo_fencing_point || '-';
      const reason = rec.reason || '';

      card.innerHTML = `
        <div class="hist-row">
          <div class="hist-left">
            <div class="point">${inPoint} → ${outPoint}</div>
            <div class="time">${inTime}</div>
            <div class="meta">Out: ${outTime}</div>
          </div>
          <div style="text-align:right;min-width:80px">
            ${ reason ? `<div class="reason">${escapeHtml(reason)}</div>` : '' }
            <div style="margin-top:8px;color:${rec.out_time ? '#0b3d91' : '#6b7280'};font-weight:700">
              ${rec.out_time ? 'Completed' : 'Active'}
            </div>
          </div>
        </div>
      `;
      container.appendChild(card);
    });

  }catch(e){
    console.error(e);
    $('historyList').innerHTML = '<div class="empty">Error loading records.</div>';
  }
}

// Mark IN/OUT handler (uses geolocation & same /mark endpoint)
$('markBtn').addEventListener('click', async () => {
  const empCode = $('emp_code').value.trim();
  if(!empCode) return Swal.fire({icon:'warning', title:'Enter Employee Code'});

  if(!navigator.geolocation){
    return Swal.fire({icon:'error', title:'Geolocation not supported'});
  }

  const action = $('markBtn').dataset.action; // 'in' or 'out'

  navigator.geolocation.getCurrentPosition(async (pos) => {
    const lat = pos.coords.latitude;
    const lon = pos.coords.longitude;
    const payload = { emp_code: empCode, latitude: lat, longitude: lon };

    if(action === 'out'){
      const { value: reason } = await Swal.fire({
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
      if(!reason) return; // cancelled or not selected
      payload.reason = reason;
    }

    try{
      const res = await fetch(`${baseUrl}/mark`, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.status === 'success'){
        Swal.fire({icon:'success', title: data.message});
        // refresh
        $('fetchBtn').click();
      }else if(data.require_reason){
        // If backend asks for reason explicitly (edge-case)
        const { value: reason } = await Swal.fire({
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
        if(reason){
          payload.reason = reason;
          const res2 = await fetch(`${baseUrl}/mark`, {
            method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)
          });
          const d2 = await res2.json();
          if(d2.status === 'success'){
            Swal.fire({icon:'success', title: d2.message});
            $('fetchBtn').click();
          } else {
            Swal.fire({icon:'error', title: d2.message || 'Failed'});
          }
        }
      } else {
        Swal.fire({icon:'error', title: data.message || 'Failed to mark attendance'});
      }
    }catch(err){
      console.error(err);
      Swal.fire({icon:'error', title:'Network or server error'});
    }

  }, (err) => {
    console.error(err);
    Swal.fire({icon:'error', title:'Unable to get your location.'});
  }, { enableHighAccuracy:true, timeout:15000, maximumAge:0 });
});

// small helper to avoid XSS in innerHTML
function escapeHtml(text){
  if(!text) return text;
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
</script>
</body>
</html>
