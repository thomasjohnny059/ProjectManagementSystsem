<?php
session_start();

include 'connectDatabase.php';

$username = $email = "";
$username_err = $email_err = $reset_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty($username_err) && empty($email_err)) {
        $sql = "SELECT uid FROM users WHERE username = ? AND email = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $param_username, $param_email);
            $param_username = $username;
            $param_email = $email;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    header("location: reset_password.php?username=" . $username . "&email=" . $email);
                    exit();
                } else {
                    $reset_err = "No account found with the provided username and email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
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
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #111;
            color: #fff;
        }
        
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        
        .container {
            width: 400px;
            padding: 40px;
            background-color: #222;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            transition: transform 0.3s ease;
            position: relative;
        }

        .container:hover {
            transform: translateY(-10px);
        }
        
        h2 {
            margin-bottom: 20px;
            color: #007bff;
            font-weight: 600;
            font-size: 24px;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 20px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background-color: rgba(255, 255, 255, 0.9);
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #007bff;
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
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        p {
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        
        a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 700;
        }
        
        a:hover {
            color: #ffc107;
        }

        .home-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 700;
        }

        .home-link:hover {
            color: #ffc107;
        }

    </style>
</head>
<body>
    <a href="indexPublic.php" class="home-link">Home</a>
    <div class="overlay"></div>
    <div class="container">
        <h2>Forgot Password</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="error"><?php echo $reset_err; ?></div>
            <input type="text" name="username" placeholder="Username">
            <div class="error"><?php echo $username_err; ?></div>
            
            <input type="email" name="email" placeholder="Email">
            <div class="error"><?php echo $email_err; ?></div>
            
            <button type="submit" class="btn">Reset Password</button>
        </form>
        <p>Remember your password? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
