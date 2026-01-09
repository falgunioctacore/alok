<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Employee Attendance</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 0; }
  body {
    background: linear-gradient(180deg, #f0f4ff, #ffffff);
    color: #111827;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  header {
    background: linear-gradient(90deg, #2563eb, #1e40af);
    color: white;
    padding: 15px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
  }

  header h1 {
    font-size: 20px;
    font-weight: 600;
  }

  .user-info {
    font-size: 15px;
    font-weight: 500;
    background: rgba(255,255,255,0.2);
    padding: 6px 12px;
    border-radius: 6px;
  }

  .logout-btn {
    background: #ef4444;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
  }

  .logout-btn:hover {
    background: #b91c1c;
  }

  main {
    flex: 1;
    padding: 25px 40px;
    display: flex;
    flex-direction: column;
    gap: 25px;
  }

  .employee-info {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .employee-name {
    font-size: 22px;
    font-weight: 600;
    color: #1e3a8a;
  }

  .status span {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
  }
  .status .in { background: #dcfce7; color: #15803d; }
  .status .out { background: #fee2e2; color: #b91c1c; }

  .actions {
    text-align: center;
  }
  .actions button {
    padding: 14px 35px;
    margin: 0 15px;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    cursor: pointer;
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    transition: transform 0.2s;
  }
  .actions button:hover { transform: scale(1.05); }

  .in-btn { background: #16a34a; }
  .out-btn { background: #dc2626; }

  .actions button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
  }

  .history {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .history h2 {
    margin-bottom: 15px;
    color: #1e3a8a;
    font-size: 18px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    padding: 10px 12px;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
    font-size: 14px;
  }

  th {
    background: #2563eb;
    color: white;
    font-weight: 600;
  }

  tr:nth-child(even) {
    background: #f9fafb;
  }

  footer {
    text-align: center;
    padding: 12px;
    font-size: 13px;
    color: #6b7280;
  }

  @media (max-width: 768px) {
    main { padding: 20px; }
    .employee-info { flex-direction: column; gap: 8px; text-align: center; }
    .actions button { margin: 10px 5px; width: 45%; }
  }

  /* Modal Style */
  .modal {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none; justify-content: center; align-items: center;
  }
  .modal.active { display: flex; }
  .modal-content {
    background: white; padding: 25px; border-radius: 10px; width: 300px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    text-align: center;
  }
  .modal select {
    width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; margin-top: 10px;
  }
  .modal button {
    margin-top: 15px; padding: 10px 25px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer;
  }
  .modal button:hover { background: #1e40af; }
</style>
</head>
<body>

<header>
  <h1>Employee Attendance</h1>
  <div class="user-info" id="userName">Loading...</div>
  <button class="logout-btn" onclick="logout()">Logout</button>
</header>

<main>
  <section class="employee-info">
    <div class="employee-name" id="empName">Loading...</div>
    <div class="status">
      Status: <span id="status" class="out">Fetching...</span>
    </div>
  </section>

  <section class="actions">
    <button id="markIn" class="in-btn">Mark IN</button>
    <button id="markOut" class="out-btn">Mark OUT</button>
  </section>

  <section class="history">
    <h2>Attendance History</h2>
    <table id="attendanceTable">
      <thead>
        <tr>
          <th>Date</th>
          <th>IN Time</th>
          <th>IN Geo Point</th>
          <th>OUT Time</th>
          <th>OUT Geo Point</th>
          <th>Reason</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="6" style="text-align:center;">Loading...</td></tr>
      </tbody>
    </table>
  </section>
</main>

<footer>© 2025 Your Company Name</footer>

<!-- OUT Reason Modal -->
<div id="reasonModal" class="modal">
  <div class="modal-content">
    <h3>Select Reason for OUT</h3>
    <select id="reasonSelect" name="reason">
      <option value="">Select Reason</option>
      <option name="Other Plant">Other Plant</option>
      <option name="Lunch / Dinner">Lunch / Dinner</option>
      <option name="Early Leave / OFF Duty">Early Leave / OFF Duty</option>
      <option>OD</option>
      <option>Alok City</option>
      <option>Gate Pass</option>
    </select>
    <button id="submitReason">Submit</button>
  </div>
</div>

<script>
const token = localStorage.getItem('emp_token');
console.log("token:-",token)
if (!token) window.location.href = 'emp-login';

async function loadAttendance() {
  try {
    const res = await fetch('api/employee/index', { headers: { 'Authorization': 'Bearer ' + token } });
    const data = await res.json();

    document.getElementById('empName').textContent = data.employee.emp_name;
    document.getElementById('userName').textContent = data.employee.emp_name;

    const isIn = data.attendance && !data.attendance.out_time;
    const statusEl = document.getElementById('status');
    statusEl.textContent = isIn ? 'IN' : 'OUT';
    statusEl.className = isIn ? 'in' : 'out';
    document.getElementById('markIn').disabled = isIn;
    document.getElementById('markOut').disabled = !isIn;

    loadAttendanceHistory();
  } catch {
    Swal.fire('Error', 'Failed to load employee info', 'error');
  }
}

async function loadAttendanceHistory() {
  const tbody = document.querySelector("#attendanceTable tbody");
  tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Loading...</td></tr>';
  try {
    const res = await fetch('api/employee/attendance/history', {
      headers: { 'Authorization': 'Bearer ' + token }
    });
    const data = await res.json();
    if (data.month_records?.length) {
      tbody.innerHTML = data.month_records.map((r, i) => `
        <tr>
          <td>${i+1}</td>
          <td>${r.in_time || '-'}</td>
          <td>${r.in_geo_fencing_point || '-'}</td>
          <td>${r.out_time || '-'}</td>
          <td>${r.out_geo_fencing_point || '-'}</td>
          <td>${r.reason || '-'}</td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No attendance found</td></tr>';
    }
  } catch {
    tbody.innerHTML = '<tr><td colspan="6" style="color:red;text-align:center;">Error fetching data</td></tr>';
  }
}

async function markAttendance(type, reason = null) {
  if (!navigator.geolocation) {
    Swal.fire('Error', 'Enable location to mark attendance', 'error');
    return;
  }
  navigator.geolocation.getCurrentPosition(async pos => {
    const body = { latitude: pos.coords.latitude, longitude: pos.coords.longitude };
    if (reason) body.reason = reason;
    const res = await fetch('api/employee/attendance/mark', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
      body: JSON.stringify(body)
    });
    const data = await res.json();

    if (data.status === 'success') {
      Swal.fire('Success', data.message, 'success');
      loadAttendance();
    } else {
      Swal.fire('Error', data.message, 'error');
    }
  });
}

document.getElementById('markIn').onclick = () => markAttendance('in');
document.getElementById('markOut').onclick = () => {
  document.getElementById('reasonModal').classList.add('active');
};

document.getElementById('submitReason').onclick = () => {
  const reason = document.getElementById('reasonSelect').value;
  if (!reason) {
    Swal.fire('Select Reason', 'Please choose a reason before submitting', 'warning');
    return;
  }
  document.getElementById('reasonModal').classList.remove('active');
  markAttendance('out', reason);
};

async function logout() {
  await fetch('api/employee/logout', { method: 'POST', headers: { 'Authorization': 'Bearer ' + token } });
  localStorage.clear();
  window.location.href = 'emp-login';
}

loadAttendance();
</script>
</body>
</html>
