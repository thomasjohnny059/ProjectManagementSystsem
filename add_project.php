<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

include 'connectDatabase.php';

$title = $start_date = $end_date = $phase = $description = "";
$title_err = $start_date_err = $end_date_err = $phase_err = $description_err = "";

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        exit("Invalid CSRF token");
    }

    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    if (empty(trim($_POST["start_date"]))) {
        $start_date_err = "Please enter a start date.";
    } else {
        $start_date = trim($_POST["start_date"]);
    }

    if (empty(trim($_POST["end_date"]))) {
        $end_date_err = "Please enter an end date.";
    } else {
        $end_date = trim($_POST["end_date"]);
        if ($end_date < $start_date) {
            $end_date_err = "End date cannot be before the start date.";
        }
    }

    if (empty(trim($_POST["phase"]))) {
        $phase_err = "Please select a phase.";
    } else {
        $phase = trim($_POST["phase"]);
    }

    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }

    if (empty($title_err) && empty($start_date_err) && empty($end_date_err) && empty($phase_err) && empty($description_err)) {
        $sql = "INSERT INTO projects (title, start_date, end_date, phase, description, uid) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", $param_title, $param_start_date, $param_end_date, $param_phase, $param_description, $param_uid);

            $param_title = $title;
            $param_start_date = $start_date;
            $param_end_date = $end_date;
            $param_phase = $phase;
            $param_description = $description;
            $param_uid = $_SESSION["uid"];

            if ($stmt->execute()) {
                header("location: welcome.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Project</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('3d-hyperspace-background-with-warp-tunnel-effect.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #fff;
        }

        form div {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #555;
            box-sizing: border-box;
            background-color: #444;
            color: #fff;
        }

        textarea {
            height: 100px;
        }

        span {
            color: red;
        }

        input[type="submit"],
        input[type="reset"] {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            background-color: #0056b3;
        }

        input[type="reset"] {
            margin-left: 10px;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #555;
            box-sizing: border-box;
            background-color: #444;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Add New Project</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo $title; ?>">
                <span><?php echo $title_err; ?></span>
            </div>
            <div>
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                <span><?php echo $start_date_err; ?></span>
            </div>
            <div>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                <span><?php echo $end_date_err; ?></span>
            </div>
            <div>
                <label for="phase">Phase:</label>
                <select id="phase" name="phase" class="input-field">
                    <option value="">Select Phase</option>
                    <option value="design" <?php if ($phase === 'design') echo 'selected'; ?>>Design</option>
                    <option value="development" <?php if ($phase === 'development') echo 'selected'; ?>>Development</option>
                    <option value="testing" <?php if ($phase === 'testing') echo 'selected'; ?>>Testing</option>
                    <option value="deployment" <?php if ($phase === 'deployment') echo 'selected'; ?>>Deployment</option>
                    <option value="complete" <?php if ($phase === 'complete') echo 'selected'; ?>>Complete</option>
                </select>
                <span><?php echo $phase_err; ?></span>
            </div>
            <div>
                <label for="description">Description:</label>
                <textarea id="description" name="description"><?php echo $description; ?></textarea>
                <span><?php echo $description_err; ?></span>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div>
                <input type="submit" value="Add Project">
                <input type="reset" value="Reset">
            </div>
        </form>
    </div>
</body>

</html>
