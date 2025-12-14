<?php

session_start();


require_once 'Database_connect.php';

$error_msg = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_msg = "Please enter both email and password.";
    } else {
       
        $sql = "SELECT user_id, name, email, password FROM users WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
             
                if ($password === $row['password']) {
                    
                   
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['user_name'] = $row['name'];

                   
                    header("Location: home_page.php"); 
                    exit;
                    
                } else {
                    $error_msg = "Incorrect password.";
                }
            } else {
                $error_msg = "Email not found.";
            }
            $stmt->close();
        } else {
            $error_msg = "Database error.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
  <style>
      .error-box { color: red; background: #ffe6e6; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; }
  </style>
</head>
<body>
  <section class="login-container">
    <h1>Login</h1>

    <?php if (!empty($error_msg)): ?>
        <div class="error-box"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="login.php" method="post" autocomplete="off">
      <fieldset>
        <legend>Login to your account</legend>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email address" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>

        <input type="submit" value="Login">
      </fieldset>
    </form>

    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
  </section>
</body>
</html>