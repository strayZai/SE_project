<?php


session_start();

require_once 'Database_connect.php'; 


$message = '';
$message_type = ''; // 'success' or 'error'
$fullname = '';
$email = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm'] ?? '';


    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = 'Please fill all required fields.';
        $message_type = 'error';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
        $message_type = 'error';
    } else {
        
       
        $check_sql = "SELECT email FROM users WHERE email = ?";
        
        if ($check_stmt = $conn->prepare($check_sql)) {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->store_result(); 

            if ($check_stmt->num_rows > 0) {
                $message = 'This email is already registered. Please login.';
                $message_type = 'error';
            } else {
                
                
                $plaintext_password = $password; 

                
                $insert_sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
                
                if ($insert_stmt = $conn->prepare($insert_sql)) {
                   
                    $insert_stmt->bind_param("sss", $fullname, $email, $plaintext_password);

                    if ($insert_stmt->execute()) {
                       
                        $message = 'Account created successfully! Redirecting to login page.';
                        $message_type = 'success';
                        
                        
                        header('Refresh: 3; URL=login.php'); 
                    } else {
                    
                        $message = 'Error creating account: ' . $conn->error;
                        $message_type = 'error';
                    }
                    $insert_stmt->close();
                } else {
                     $message = 'Error preparing the insert statement.';
                     $message_type = 'error';
                }
            }
            $check_stmt->close();
        } else {
            $message = 'Error preparing the email check statement.';
            $message_type = 'error';
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* System message styling */
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <section class="signup-container">
        <h1>Sign Up</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="signup.php" method="post" autocomplete="off">
            <fieldset>
                <legend>Create your account</legend>

                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required aria-required="true" value="<?= htmlspecialchars($fullname) ?>">

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required aria-required="true" value="<?= htmlspecialchars($email) ?>">

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter a secure password" required aria-required="true">

                <label for="confirm">Confirm Password:</label>
                <input type="password" id="confirm" name="confirm" placeholder="Re-enter your password" required aria-required="true">

                <input type="submit" value="Create Account">
            </fieldset>
        </form>

        <p>Already have an account? <a href="login.php">Login</a></p>
    </section>
</body>
</html>