<?php
require_once "connectDatabase.php";

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["username"])) {
    header("location: login.php");
    exit;
}

if (!isset($_SESSION["email"]) || !isset($_SESSION["uid"])) {
    $stmt = $conn->prepare("SELECT email, uid FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION["username"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        $_SESSION["email"] = $row["email"];
        $_SESSION["uid"] = $row["uid"];
    } else {
        header("location: set_email.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0d0d0d;
            color: #fff;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        header {
            background: radial-gradient(circle, #101010, #000000);
            color: #fff;
            padding: 20px 0;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1a1a1a;
            position: relative;
        }

        header h1 {
            font-size: 3em;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 0;
            font-weight: 700;
            color: white;
        }

        .nav-links {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
            margin-left: 20px;
        }

        .nav-links a:hover {
            color: #ffc107;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #1a1a1a;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .container h2 {
            margin-bottom: 20px;
            color: #fff;
        }

        .container p {
            margin-bottom: 10px;
        }

        .container a {
            color: #ffc107;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .container a:hover {
            color: #ffea00;
        }

        .container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            outline: none;
        }

        .container button:hover {
            background-color: #0056b3;
        }

        .password-form {
            margin-top: 20px;
        }

        .password-form label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
        }

        .password-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #333;
            border-radius: 5px;
            background-color: #1a1a1a;
            color: #fff;
            outline: none;
        }
    </style>
</head>
<body>
<header>
    <h1>User Settings</h1>
    <div class="nav-links">
        <a href="welcomeUser.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</header>

<div class="container">
    <h2>Personal Information</h2>
    <?php
    echo "<p>Username: " . $_SESSION["username"] . "</p>";
    echo "<p>Email: " . $_SESSION["email"] . "</p>";
    echo "<p>User ID: " . $_SESSION["uid"] . "</p>";
    ?>

    <h2>Change Password</h2>
    <form class="password-form" action="changePassword.php" method="post">
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Change Password</button>
    </form>

    <h2>Terms and Conditions</h2>
    <a href="termsAndConditions.php">Read Terms and Conditions</a>

    <h2>Delete Account</h2>
    <form action="deleteAccount.php" method="post" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
        <button type="submit">Delete Account</button>
    </form>
</div>
</body>
</html>
