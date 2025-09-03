
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_home.css">
    <title>Chol | Sign Up</title>
</head>
<style>
     * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
 }

    body {
      height: 100vh;
      width: 100%;
      background: url("bg.jpg") no-repeat center center/cover;
      position: relative;
      color: white;
      background-color: black;
    }

    /* Overlay */
    body::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      margin-bottom: 0px;
      height: 135%;
      background: rgba(0, 0, 0, 0.55);
      z-index: 0;
    }

    /* Navbar */
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 60px;
      position: relative;
      z-index: 2;
    }

    nav .logo {
      font-size: 28px;
      font-weight: bold;
      color: #ffffffff;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 30px;
    }

    nav ul li a {
      text-decoration: none;
      color: white;
      font-size: 16px;
      transition: 0.3s;
    }

    nav ul li a:hover {
      color: #ff1add;
    }

    /* Login box */
    .login-box {
      position: relative;
      z-index: 2;
      background: rgba(0, 0, 0, 0.6);
      max-width: 450px;
      margin: 80px auto;
      padding: 40px;
      border-radius: 12px;
      text-align: center;
    }

    .login-box h2 {
      margin-bottom: 20px;
      font-size: 26px;
      color: #fff;
    }

    .input-group {
      margin-bottom: 15px;
      text-align: left;
    }

    .input-group input {
      width: 100%;
      padding: 12px 15px;
      margin: 5px;
      border-radius: 8px;
      border: none;
      outline: none;
      background: rgba(255,255,255,0.1);
      color: white;
      font-size: 14px;
    }

    .input-group input::placeholder {
      color: #ddd;
    }

    .options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 13px;
      margin: 10px 0;
    }

    .options label {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .options a {
      color: #ccc;
      text-decoration: none;
    }

    .options a:hover {
      color: #ff471a;
    }

    .btn {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 25px;
      margin-top: 8px;
      background: linear-gradient(45deg, #ff00cc, #3333ff);
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn:hover {
      opacity: 0.85;
    }

    .signup-link {
      margin-top: 15px;
      font-size: 14px;
    }

    .signup-link a {
      color: #ffffffff;
      text-decoration: none;
      font-weight: bold;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }

</style>
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
    <h2>Sign Up</h2>
    <form action="insert_user.php" method="post">
      <div class="input-group">
        <input type="text" placeholder="First Name" name="fname" required><br>
        <input type="text" placeholder="Last Name" name="lname" required><br>
        <input type="text" placeholder="Username" name="username" required><br>
        <input type="email" placeholder="Email" name="email" required><br>
        <input type="number" placeholder="NID" name="NID"><br>
        <input type="date" placeholder="Date of Birth" name="Birthdate" required><br>
        <input type="password" placeholder="Password" name="pass" required><br>
        <input type="password" placeholder="Confirm Password" name="confirm_pass" required><br>
        <button type="submit" class="btn">Sign Up</button>
      </div>
    </form>
</div>

</body>
</html>

   



