<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
        input[type="password"] {
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
            border-radius: 5px;
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
            color: black;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
            font-size: 18px;
            padding: 10px;
        }

        .home-link:hover {
            color: #ffc107;
        }
    </style>
</head>
<body>
<div class="overlay"></div>
    <div class="container">
        <a href="indexPublic.php" class="home-link">Home</a>
        <h2>Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    include 'connectDatabase.php';

                    $username = $password = "";
                    $username_err = $password_err = "";

                    if (empty(trim($_POST["username"]))) {
                        $username_err = "Please enter your username.";
                    } else {
                        $username = trim($_POST["username"]);
                    }

                    if (empty(trim($_POST["password"]))) {
                        $password_err = "Please enter your password.";
                    } else {
                        $password = trim($_POST["password"]);
                    }

                    if (empty($username_err) && empty($password_err)) {
                        $sql = "SELECT uid, username, password FROM users WHERE username = ?";

                        if ($stmt = $conn->prepare($sql)) {
                            $stmt->bind_param("s", $param_username);
                            $param_username = $username;

                            if ($stmt->execute()) {
                                $stmt->store_result();

                                if ($stmt->num_rows == 1) {
                                    $stmt->bind_result($uid, $username, $hashed_password);
                                    if ($stmt->fetch()) {
                                        if (password_verify($password, $hashed_password)) {
                                            session_start();
                                            $_SESSION["loggedin"] = true;
                                            $_SESSION["uid"] = $uid;
                                            $_SESSION["username"] = $username;
                                            header("location: welcomeUser.php");
                                        } else {
                                            $password_err = "The password you entered is not valid.";
                                        }
                                    }
                                } else {
                                    $username_err = "No account found with that username.";
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
            <div class="error"><?php echo (!empty($username_err)) ? $username_err : ''; ?></div>
            <input type="text" id="username" name="username" placeholder="Username">
            
            <div class="error"><?php echo (!empty($password_err)) ? $password_err : ''; ?></div>
            <input type="password" id="password" name="password" placeholder="Password">
            
            <button type="submit" class="btn">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
    </div>
</body>
</html>
