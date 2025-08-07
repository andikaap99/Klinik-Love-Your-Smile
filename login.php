<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="pages/style.css" />
</head>

<body>
  <div class="container">
    <div class="login-split">
      <div class="login-image">
        <img src="pages/image/dentist.png" title="smile icons"></img>
      </div>

      <!-- Right Side (Form) -->
      <div class="login-form">
        <h1><span class="hi">Hi</span><br><span class="welcome">Welcome<br>Back!</span></h1>
        <form action="functions/login.php" method="post">
          <label for="role">Kamu adalah seorang ?</label>
          <select name="role" id="role" required>
            <option value="dokter">Dokter</option>
            <option value="resepsionis">Resepsionis</option>
            <option value="apoteker">Apoteker</option>
          </select>
          <label for="username">Username</label>
          <input placeholder="your username" type="text" id="username" name="username" required><br>
          <label for="password">Password</label>
          <input placeholder="your password" type="password" id="password" name="password" required><br>
          <button type="submit">Login</button>
        </form>
      </div>
    </div>
  </div>

<script>
  function showRegister() {
    document.getElementById('registerModal').style.display = 'block';
  }
  function closeRegister() {
    document.getElementById('registerModal').style.display = 'none';
  }
</script>

</body>

</html>