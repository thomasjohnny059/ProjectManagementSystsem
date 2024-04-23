<?php
session_start();

$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connectDatabase.php';

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT uid FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = trim($_POST["username"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $sql = "SELECT uid FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST["email"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $email_err = "This email is already registered.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $param_username, $param_email, $param_password);
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            if ($stmt->execute()) {
                header("location: login.php");
                exit();
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
    <title>Register</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: black;
            position: relative;
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
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
            position: relative;
        }

        .container:hover {
            transform: translateY(-10px);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
            font-size: 24px;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background-color: rgba(255, 255, 255, 0.9);
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
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
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn[type="submit"] {
            background-color: #007bff;
        }

        .btn[type="submit"]:hover {
            background-color: #0056b3;
        }

        .btn[type="reset"] {
            background-color: #f44336;
            margin-top: 10px;
        }

        .btn[type="reset"]:hover {
            background-color: #d32f2f;
        }

        p {
            margin-top: 20px;
            color: #666;
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

        .home-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
            font-size: 18px;
            padding: 10px;
        }

        .home-link:hover {
            color: #ffc107;
        }

        .terms-checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            text-align: left;
        }

        .terms-checkbox {
            margin-right: 10px;
        }

    </style>
</head>
<body>
    <div class="overlay"></div>
    <a href="indexPublic.php" class="home-link">Home</a>
    <div class="container">
        <h2>Register</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>">
                <span class="error"><?php echo $username_err; ?></span>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $email; ?>">
                <span class="error"><?php echo $email_err; ?></span>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" value="<?php echo $password; ?>">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <div>
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
                <span class="error"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="terms-checkbox-container">
                <input type="checkbox" name="agree_terms" class="terms-checkbox" required>
                <label for="agree_terms">I agree to the <a href="termsAndConditionsReg.php" target="_blank">Terms and Conditions</a></label>
            </div>
            <div>
                <input type="submit" value="Register" class="btn">
                <input type="reset" value="Reset" class="btn">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
