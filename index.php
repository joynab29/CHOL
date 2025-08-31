
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_index.css">
    <title>Chol | Login</title>
</head>
<body>
  <nav>
    <div class="logo">CHOL</div>
    <ul>
      <li><a href="home.php">HOME</a></li>
      <li><a href="#">ABOUT US</a></li>
      <li><a href="#">CONTACT</a></li>
    </ul>
  </nav>

  <!-- Login Box -->
  <div class="login-box">
    <h2>Log In</h2>
    <form action="login.php" method="post">
      <div class="input-group" >
        <input type="text" placeholder="Username" name='username' required>
      </div>
      <div class="input-group">
        <input type="password" placeholder="Password" name='pass' required>
      </div>
      <div class="options">
        <label><input type="checkbox"> Remember Me</label>
        <a href="#">Forgot Password?</a> 
      </div>
      <button type="submit" class="btn">Log in</button>
      <div class="signup-link">
         <a href="signup.php"> Click to Sign up</a>
      </div>
    </form>
  </div>
</body>
</html>

   



