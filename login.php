<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-container">

  <div class="login-card">

    <!-- TOP SECTION -->
    <div class="login-top">

      <img src="logo.png" alt="University Logo" class="logo">

      <h1>RegisCore</h1>

      <p class="catchphrase">
        Smarter scheduling, Smarter Learning
      </p>

    </div>

    <!-- CENTER SECTION -->
    <div class="login-middle">

      <h2>Welcome Back</h2>

      <form id="loginForm" action="process_login.php" method="POST">

        <label>Student ID</label>
        <input type="text" name="student_id" placeholder="Enter Student ID">

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter Password">

      </form>

      <p style="margin-top:16px; font-size:13px; color:#666; text-align:center; line-height:1.6;">
        Demo account<br>
        <strong>Student ID:</strong> 202201245 &nbsp;·&nbsp; <strong>Password:</strong> test123
      </p>

    </div>

    <!-- BOTTOM SECTION -->
    <div class="login-bottom">

      <button type="submit" form="loginForm">
        Login
      </button>

      <a href="#">
        Forgot Password?
      </a>

    </div>

  </div>

</div>

</body>
</html>