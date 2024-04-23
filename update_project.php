<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

include 'connectDatabase.php';

$title = $start_date = $end_date = $phase = $description = "";
$title_err = $start_date_err = $end_date_err = $phase_err = $description_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST["title"]);
    if (empty(trim($title))) {
        $title_err = "Please enter a title.";
    }

    $start_date = htmlspecialchars($_POST["start_date"]);
    if (empty(trim($start_date))) {
        $start_date_err = "Please enter a start date.";
    }

    $end_date = htmlspecialchars($_POST["end_date"]);
    if (empty(trim($end_date))) {
        $end_date_err = "Please enter an end date.";
    }

    $phase = htmlspecialchars($_POST["phase"]);
    if (empty(trim($phase))) {
        $phase_err = "Please enter a phase.";
    }

    $description = htmlspecialchars($_POST["description"]);
    if (empty(trim($description))) {
        $description_err = "Please enter a description.";
    }

    if (empty($title_err) && empty($start_date_err) && empty($end_date_err) && empty($phase_err) && empty($description_err)) {
        $sql = "UPDATE projects SET title=?, start_date=?, end_date=?, phase=?, description=? WHERE pid=?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", $param_title, $param_start_date, $param_end_date, $param_phase, $param_description, $param_pid);

            $param_title = $title;
            $param_start_date = $start_date;
            $param_end_date = $end_date;
            $param_phase = $phase;
            $param_description = $description;
            $param_pid = $_GET["id"];

            if ($stmt->execute()) {
                header("location: welcome.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }
}

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $sql = "SELECT title, start_date, end_date, phase, description FROM projects WHERE pid = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $param_pid);
        $param_pid = $_GET["id"];

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($title, $start_date, $end_date, $phase, $description);
                $stmt->fetch();

                $title = htmlspecialchars($title);
                $start_date = htmlspecialchars($start_date);
                $end_date = htmlspecialchars($end_date);
                $phase = htmlspecialchars($phase);
                $description = htmlspecialchars($description);
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Project</title>
    <style>
        body {
            background-image: url('beautiful-milky-way-night-sky.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        div {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #111;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        form div {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="date"],
        textarea {
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
        a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        input[type="submit"]:hover,
        a:hover {
            background-color: #0056b3;
        }

        a {
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
<div>
        <h2>Update Project</h2>
        <p>Please fill in the details to update the project.</p>
        <form id="updateForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $_GET["id"]; ?>" method="post" onsubmit="return validateForm()">
            <div>
                <label>Title</label>
                <input type="text" id="title" name="title" value="<?php echo $title; ?>">
                <span id="titleErr"><?php echo $title_err; ?></span>
            </div>
            <div>
                <label>Start Date</label>
                <input type="date" id="startDate" name="start_date" value="<?php echo $start_date; ?>">
                <span id="startDateErr"><?php echo $start_date_err; ?></span>
            </div>
            <div>
                <label>End Date</label>
                <input type="date" id="endDate" name="end_date" value="<?php echo $end_date; ?>">
                <span id="endDateErr"><?php echo $end_date_err; ?></span>
            </div>
            <div>
                <label>Phase</label>
                <select id="phase" name="phase" class="input-field">
                    <option value="design" <?php if($phase === 'design') echo 'selected'; ?>>Design</option>
                    <option value="development" <?php if($phase === 'development') echo 'selected'; ?>>Development</option>
                    <option value="testing" <?php if($phase === 'testing') echo 'selected'; ?>>Testing</option>
                    <option value="deployment" <?php if($phase === 'deployment') echo 'selected'; ?>>Deployment</option>
                    <option value="complete" <?php if($phase === 'complete') echo 'selected'; ?>>Complete</option>
                </select>
                <span id="phaseErr"><?php echo $phase_err; ?></span>
            </div>
            <div>
                <label>Description</label>
                <textarea id="description" name="description"><?php echo $description; ?></textarea>
                <span id="descriptionErr"><?php echo $description_err; ?></span>
            </div>
            <div>
                <input type="submit" value="Update">
                <a href="welcome.php">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        function validateForm() {
            var title = document.getElementById("title").value;
            var startDate = document.getElementById("startDate").value;
            var endDate = document.getElementById("endDate").value;
            var phase = document.getElementById("phase").value;
            var description = document.getElementById("description").value;

            var titleErr = document.getElementById("titleErr");
            var startDateErr = document.getElementById("startDateErr");
            var endDateErr = document.getElementById("endDateErr");
            var phaseErr = document.getElementById("phaseErr");
            var descriptionErr = document.getElementById("descriptionErr");

            titleErr.innerHTML = startDateErr.innerHTML = endDateErr.innerHTML = phaseErr.innerHTML = descriptionErr.innerHTML = "";

            if (title === "") {
                titleErr.innerHTML = "Please enter a title.";
                return false;
            }

            if (startDate === "") {
                startDateErr.innerHTML = "Please enter a start date.";
                return false;
            }

            if (endDate === "") {
                endDateErr.innerHTML = "Please enter an end date.";
                return false;
            }

            if (phase === "") {
                phaseErr.innerHTML = "Please select a phase.";
                return false;
            }

            if (description === "") {
                descriptionErr.innerHTML = "Please enter a description.";
                return false;
            }

            var startDateObj = new Date(startDate);
            var endDateObj = new Date(endDate);

            if (endDateObj < startDateObj) {
                endDateErr.innerHTML = "End date cannot be before the start date.";
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
