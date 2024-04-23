<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["username"])) {
    header("location: login.php");
    exit;
}

include 'connectDatabase.php';

$password = "";
$password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["password"])) {
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter your password.";
        } else {
            $password = trim($_POST["password"]);
        }
    }

    if (empty($password_err)) {
        $conn->begin_transaction();

        try {
            $sql_select_user = "SELECT uid, password FROM users WHERE username = ?";

            if ($stmt_select_user = $conn->prepare($sql_select_user)) {
                $stmt_select_user->bind_param("s", $param_username);
                $param_username = $_SESSION["username"];

                if ($stmt_select_user->execute()) {
                    $stmt_select_user->store_result();

                    if ($stmt_select_user->num_rows == 1) {
                        $stmt_select_user->bind_result($uid, $hashed_password);
                        if ($stmt_select_user->fetch()) {
                            if (password_verify($password, $hashed_password)) {
                                $sql_delete_projects = "DELETE FROM projects WHERE uid = ?";
                                if ($stmt_delete_projects = $conn->prepare($sql_delete_projects)) {
                                    $stmt_delete_projects->bind_param("i", $uid);
                                    $stmt_delete_projects->execute();
                                } else {
                                    throw new Exception("Failed to prepare statement for deleting projects.");
                                }

                                $sql_delete_user = "DELETE FROM users WHERE username = ?";
                                if ($stmt_delete_user = $conn->prepare($sql_delete_user)) {
                                    $stmt_delete_user->bind_param("s", $param_username);
                                    $stmt_delete_user->execute();
                                    $conn->commit();
                                    header("location: login.php");
                                    exit;
                                } else {
                                    throw new Exception("Failed to prepare statement for deleting user.");
                                }
                            } else {
                                $password_err = "The password you entered is not valid.";
                            }
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                $stmt_select_user->close();
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error: " . $e->getMessage();
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
    <title>Delete Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0d0d0d;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #1a1a1a;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #555;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
            outline: none;
        }

        .error-message {
            color: #ff8080;
        }

        .btn-delete {
            background-color: #ff3333;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-delete:hover {
            background-color: #cc0000;
        }

        .btn-cancel {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cancel:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Delete Account</h2>
    <p>Please enter your password to confirm deletion of your account.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label>Password</label>
            <input type="password" name="password">
            <span class="error-message"><?php echo $password_err; ?></span>
        </div>
        <div>
            <input type="submit" class="btn-delete" value="Delete Account">
            <a href="welcomeUser.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
