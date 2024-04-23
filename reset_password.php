<?php
session_start();

include 'connectDatabase.php';

$username = $email = $new_password = $confirm_password = "";
$username_err = $email_err = $new_password_err = $confirm_password_err = $reset_err = "";

if (isset($_GET['username']) && isset($_GET['email'])) {
    $username = $_GET['username'];
    $email = $_GET['email'];
} else {
    header("location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter your new password.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your new password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    if (empty($new_password_err) && empty($confirm_password_err)) {
        $sql = "UPDATE users SET password = ? WHERE username = ? AND email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $param_password, $param_username, $param_email);
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_username = $username;
            $param_email = $email;
            if ($stmt->execute()) {
                header("location: login.php");
                exit();
            } else {
                $reset_err = "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #111;
            color: #fff;
        }
        
        .container {
            width: 400px;
            padding: 40px;
            background-color: #222;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h2 {
            margin-bottom: 20px;
            color: #007bff;
            font-weight: 600;
            font-size: 28px;
            text-transform: uppercase;
        }
        
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 12px 20px;
            margin-bottom: 20px;
            border: none;
            border-radius: 25px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: #333;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        
        input[type="password"]:focus {
            outline: none;
            background-color: #444;
        }
        
        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
            text-align: left;
        }
        
        .btn {
            width: 100%;
            padding: 15px 0;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 25px;
            cursor: pointer;
            font-size: 18px;
            text-transform: uppercase;
            transition: background-color 0.3s ease;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        p {
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }
        
        a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?username=<?php echo $username; ?>&email=<?php echo $email; ?>" method="post">
            <div>
                <input type="password" name="new_password" placeholder="New Password">
                <span class="error"><?php echo $new_password_err; ?></span>
            </div>
            <div>
                <input type="password" name="confirm_password" placeholder="Confirm New Password">
                <span class="error"><?php echo $confirm_password_err; ?></span>
            </div>
            <div>
                <button type="submit" class="btn">Reset Password</button>
            </div>
            <div>
                <p><?php echo $reset_err; ?></p>
            </div>
        </form>
        <p>Remember your password? <a href="login.php" style="color: #007bff;">Log in here</a>.</p>
    </div>
</body>
</html>
