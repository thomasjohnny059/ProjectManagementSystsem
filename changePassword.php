<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["username"])) {
    header("location: login.php");
    exit;
}

include 'connectDatabase.php';

$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["current_password"]))) {
        $current_password_err = "Please enter your current password.";
    } else {
        $current_password = trim($_POST["current_password"]);
    }

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

    if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
        $sql = "SELECT password FROM users WHERE username = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $_SESSION["username"];
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($current_password, $hashed_password)) {
                            $sql = "UPDATE users SET password = ? WHERE username = ?";
                            if ($stmt = $conn->prepare($sql)) {
                                $stmt->bind_param("ss", $param_password, $param_username);
                                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
                                if ($stmt->execute()) {
                                    header("location: login.php");
                                    exit;
                                } else {
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                                $stmt->close();
                            }
                        } else {
                            $current_password_err = "The password you entered is not valid.";
                        }
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
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
