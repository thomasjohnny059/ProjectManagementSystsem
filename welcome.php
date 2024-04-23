<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["username"])) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
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
        }

        .logout-link {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .logout-link:hover {
            color: #ffc107;
        }

        .user-settings-link {
            position: absolute;
            top: 20px;
            right: 90px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .user-settings-link:hover {
            color: #ffc107;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .search-bar input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #333;
            border-radius: 5px;
            font-size: 1.2em;
            outline: none;
            background-color: #1a1a1a;
            color: #fff;
        }

        .search-bar button, .search-bar .add-project-link {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            transition: background-color 0.3s ease;
            outline: none;
            margin-left: 10px;
        }

        .search-bar button:hover, .search-bar .add-project-link:hover {
            background-color: #0056b3;
        }

        .project-list {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .project-list li {
            background-color: #1a1a1a;
            border-radius: 10px;
            padding: 20px;
            transition: transform 0.3s ease;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            border: 1px solid #333;
        }

        .project-list li:hover {
            transform: translateY(-5px);
            background-color: #2a2a2a;
        }

        .project-list h2 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #fff;
            font-size: 1.8em;
            font-weight: 700;
        }

        .details-container {
            display: none;
            padding: 20px;
            background-color: #1a1a1a;
            border-radius: 10px;
            margin-top: 10px;
            border: 1px solid #333;
            color: #fff;
        }

        .active .details-container {
            display: block;
        }

        .details-container p {
            margin-bottom: 10px;
            font-size: 1.2em;
        }

        .no-projects {
            text-align: center;
            color: #777;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: url("https://www.transparenttextures.com/patterns/stardust.png");
            opacity: 0.8;
            animation: stars 300s linear infinite;
        }

        @keyframes stars {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-100%);
            }
        }

        .shooting-star {
            position: absolute;
            width: 2px;
            height: 2px;
            background-color: #fff;
            z-index: -1;
            animation: shooting-star 3s linear infinite;
        }

        @keyframes shooting-star {
            from {
                transform: translateX(-100px) translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateX(100vw) translateY(-100px);
                opacity: 1;
            }
        }

        .container::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(0, 0, 0, 0) 50%, rgba(0, 0, 0, 0.7) 80%);
            z-index: -1;
            pointer-events: none;
        }

        header::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(0, 0, 0, 0) 50%, rgba(0, 0, 0, 0.7) 80%);
            z-index: -1;
            pointer-events: none;
        }

        .home-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .home-link:hover {
            color: #ffc107;
        }
    </style>
</head>
<body>
<header>
    <?php
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["username"])) {
        echo "<h1>Hi, " . $_SESSION["username"] . "!</h1>";
        echo "<div class='auth-links'>";
        echo "<a href='welcomeUser.php' class='home-link'>Home</a>";
        echo "<a href='userSetting.php' class='user-settings-link'>User Settings</a>";
        echo "<a href='logout.php' class='logout-link'>Logout</a>";
        echo "</div>";
    } else {
        header("location: login.php");
        exit;
    }
    ?>
</header>

<div class="container">
    <div class="search-bar">
        <form action="welcome.php" method="GET">
            <input type="text" name="search" placeholder="Search by title or start date">
            <button type="submit">Search</button>
        </form>
        <a href="add_project.php" class="add-project-link">Add Project</a>
    </div>


    <ul class="project-list">
        <?php
        include 'connectDatabase.php';

        $sql = "SELECT projects.*, users.email FROM projects INNER JOIN users ON projects.uid = users.uid";

        include 'connectDatabase.php';

        $sql = "SELECT projects.*, users.email FROM projects INNER JOIN users ON projects.uid = users.uid";

        if(isset($_GET['search'])) {
            $search = $_GET['search'];
            if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $search)) {
                $sql .= " WHERE projects.start_date LIKE '%$search%'";
            } else {
                $search = '%' . mysqli_real_escape_string($conn, $search) . '%';
                $sql .= " WHERE projects.title LIKE '$search'";
            }
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li class='project-item'>";
                echo "<h2>" . $row['title'] . "</h2>";
                echo "<p><strong>Description:</strong> " . $row['description'] . "</p>";
                echo "<p class='start-date'><strong>Start Date:</strong> " . $row['start_date'] . "</p>";
                echo "<div class='details-container'>";
                echo "<p><strong>End Date:</strong> " . $row['end_date'] . "</p>";
                echo "<p><strong>Phase:</strong> " . $row['phase'] . "</p>";
                echo "<p><strong>Email:</strong> <a href='mailto:" . $row['email'] . "'>" . $row['email'] . "</a></p>";
                if ($_SESSION['uid'] == $row['uid']) {
                    echo "<p><a href='update_project.php?id=" . $row['pid'] . "'>Update</a></p>"; 
                }
                echo "</div>";
                echo "</li>";
            }
        } else {
            echo "<p>No projects found.</p>";
        }
        ?>
    </ul>
</div>

<script>
    document.querySelectorAll('.project-item').forEach(item => {
        item.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });
</script>
</body>
</html>
