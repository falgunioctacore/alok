<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employee Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  /* ===== Reset & Base ===== */
  * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
  body {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }

  /* ===== Card Box ===== */
  .login-box {
    background: #fff;
    padding: 40px 35px;
    border-radius: 15px;
    width: 340px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    text-align: center;
    position: relative;
    animation: fadeIn 0.6s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* ===== Logo ===== */
  .login-box img {
    width: 200px;
    margin-bottom: 15px;
  }

  /* ===== Heading ===== */
  h2 {
    margin-bottom: 20px;
    color: #1e3a8a;
    letter-spacing: 0.5px;
  }

  /* ===== Input Fields ===== */
  input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: 0.3s;
  }

  input:focus {
    border-color: #2563eb;
    outline: none;
    box-shadow: 0 0 5px rgba(37,99,235,0.4);
  }

  input.error {
    border-color: #dc2626;
    box-shadow: 0 0 5px rgba(220,38,38,0.4);
    animation: shake 0.3s ease-in-out;
  }

  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
  }

  /* ===== Button ===== */
  button {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
  }

  button:hover {
    background: #1e40af;
  }

  /* ===== Message ===== */
  #msg {
    color: #dc2626;
    margin-top: 12px;
    font-size: 14px;
    min-height: 18px;
  }

  /* ===== Footer ===== */
  .footer {
    font-size: 12px;
    color: #555;
    margin-top: 15px;
  }
</style>
</head>
<body>

  <div class="login-box">
    <img src="{{ asset('img/logo.svg') }}" alt="Company Logo">
    <h2>Employee Login</h2>

    <input type="text" id="emp_code" placeholder="Employee Code">
    <input type="password" id="password" placeholder="Password">
    <button onclick="login()">Login</button>

    <p id="msg"></p>
    <div class="footer">© 2025 Alok Industry</div>
  </div>

<script>
async function login() {
  const emp_code = document.getElementById('emp_code');
  const password = document.getElementById('password');
  const msg = document.getElementById('msg');

  emp_code.classList.remove('error');
  password.classList.remove('error');
  msg.textContent = '';

  if (!emp_code.value.trim()) {
    emp_code.classList.add('error');
    msg.textContent = "Please enter your Employee Code.";
    emp_code.focus();
    return;
  }

  if (!password.value.trim()) {
    password.classList.add('error');
    msg.textContent = "Please enter your Password.";
    password.focus();
    return;
  }

  try {
    const res = await fetch('/api/employee/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ emp_code: emp_code.value, password: password.value })
    });

    const data = await res.json();

    if (data.status === 'success') {
      localStorage.setItem('emp_token', data.token);
      localStorage.setItem('employee', JSON.stringify(data.employee));
      window.location.href = '/mobile-attendence';
    } else {
      msg.textContent = data.message || "Invalid Employee Code or Password!";
      emp_code.classList.add('error');
      password.classList.add('error');
    }
  } catch (err) {
    msg.textContent = "Server not reachable. Please try again later.";
  }
}
</script>

</body>
</html>
