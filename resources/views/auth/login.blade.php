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

  .login-box img {
    width: 200px;
    margin-bottom: 15px;
  }

  h2 {
    margin-bottom: 20px;
    color: #1e3a8a;
    letter-spacing: 0.5px;
  }

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

  #msg {
    color: #dc2626;
    margin-top: 12px;
    font-size: 14px;
    min-height: 18px;
  }

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
    <h2>Login</h2>

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <input type="text"
             name="email"
             placeholder="Username"
             value="{{ old('email') }}"
             required>

      <input type="password"
             name="password"
             placeholder="Password"
             required>

      <button type="submit">Login</button>

      @error('email')
        <p id="msg">{{ $message }}</p>
      @enderror

      @error('password')
        <p id="msg">{{ $message }}</p>
      @enderror

    </form>

    <div class="footer">© 2025 Alok Industries</div>
  </div>

</body>
</html>
