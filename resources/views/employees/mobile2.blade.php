<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employee Attendance</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  /* ===== Reset & Base ===== */
  * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 0; }
  body {
    background: #f3f4f6;
    color: #111827;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  /* ===== Header ===== */
  header {
    background: linear-gradient(90deg, #2563eb, #1e40af);
    color: white;
    padding: 15px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  }

  header h1 {
    font-size: 22px;
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  .logout-btn {
    background: #ef4444;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
  }

  .logout-btn:hover {
    background: #b91c1c;
  }

  /* ===== Main Section ===== */
  main {
    flex: 1;
    padding: 30px 50px;
    display: flex;
    flex-direction: column;
    gap: 30px;
  }

  /* ===== Employee Info ===== */
  .employee-info {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .employee-name {
    font-size: 22px;
    font-weight: 600;
    color: #1e3a8a;
  }

  .status {
    font-size: 16px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .status span {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
  }

  .status .in { background: #dcfce7; color: #15803d; }
  .status .out { background: #fee2e2; color: #b91c1c; }

  /* ===== Actions ===== */
  .actions {
    text-align: center;
    margin-top: 10px;
  }

  .actions button {
    padding: 14px 32px;
    margin: 0 15px;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    cursor: pointer;
    color: white;
    transition: all 0.3s;
    font-weight: 600;
  }

  .in-btn { background: #16a34a; }
  .in-btn:hover { background: #15803d; }

  .out-btn { background: #dc2626; }
  .out-btn:hover { background: #b91c1c; }

  .actions button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  /* ===== Attendance History ===== */
  .history {
    background: white;
    border-radius: 10px;
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
    border-radius: 8px;
    overflow: hidden;
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

  /* ===== Footer ===== */
  footer {
    text-align: center;
    padding: 10px;
    font-size: 13px;
    color: #6b7280;
  }

  @media (max-width: 768px) {
    main { padding: 20px; }
    .employee-info { flex-direction: column; gap: 10px; text-align: center; }
    .actions button { margin: 10px 5px; width: 45%; }
  }
</style>
</head>
<body>

<header>
  <h1>Employee Attendance Portal</h1>
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
          <th>IN Geo Fencing Point</th>
          <th>OUT Time</th>
          <th>OUT Geo Fencing Point</th>
          <th>Reason</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="5" style="text-align:center;">Loading...</td></tr>
      </tbody>
    </table>
  </section>
</main>

<footer>© 2025 Your Company Name</footer>

<script>
const token = localStorage.getItem('emp_token');
if (!token) window.location.href = 'emp-login';

async function loadAttendance() {
  try {
    const res = await fetch('api/employee/index', {
      headers: { 'Authorization': 'Bearer ' + token }
    });
    const data = await res.json();

    // Set Employee name & status
    document.getElementById('empName').textContent = data.employee.emp_name;
    const isIn = data.attendance && !data.attendance.out_time;
    const statusEl = document.getElementById('status');
    statusEl.textContent = isIn ? 'IN' : 'OUT';
    statusEl.className = isIn ? 'in' : 'out';
    document.getElementById('markIn').disabled = isIn;
    document.getElementById('markOut').disabled = !isIn;

    loadAttendanceHistory();
  } catch (err) {
    document.getElementById('empName').textContent = "Error loading data";
  }
}

async function loadAttendanceHistory() {
  const tbody = document.querySelector("#attendanceTable tbody");
  tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Loading...</td></tr>';
  try {
    const res = await fetch('api/employee/attendance/history', {
      headers: { 'Authorization': 'Bearer ' + token }
    });
    const data = await res.json();
    console.log(data.month_records);
    if (data.month_records && data.month_records.length > 0) {
      i=1;
      tbody.innerHTML = data.month_records.map(r => `
        <tr>
          <td>${i++|| '-'}</td>
          <td>${r.in_time || '-'}</td>
          <td>${r.in_geo_fencing_point || '-'}</td>
          <td>${r.out_time || '-'}</td>
          <td>${r.out_geo_fencing_point || '-'}</td>
          <td>${r.reason||''}<td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No attendance found</td></tr>';
    }
  } catch (e) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:red;">Error fetching data</td></tr>';
  }
}

async function markAttendance() {
  if (!navigator.geolocation) return alert("Enable location access to mark attendance");
  navigator.geolocation.getCurrentPosition(async pos => {
    const body = { latitude: pos.coords.latitude, longitude: pos.coords.longitude };
    const res = await fetch('\api/employee/attendance/mark', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
      body: JSON.stringify(body)
    });
    const data = await res.json();
    alert(data.message);
    loadAttendance();
  }, () => alert("Location access denied!"));
}

document.getElementById('markIn').onclick = markAttendance;
document.getElementById('markOut').onclick = markAttendance;

async function logout() {
  await fetch('api/employee/logout', {
    method: 'POST',
    headers: { 'Authorization': 'Bearer ' + token }
  });
  localStorage.clear();
  window.location.href = 'emp-login';
}

loadAttendance();
</script>
</body>
</html>
