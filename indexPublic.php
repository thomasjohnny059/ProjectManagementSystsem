<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            overflow: hidden;
            background: url('3d-rendering-planet-earth.jpg') center/cover fixed;
            animation: move-background 30s linear infinite;
        }

        @keyframes move-background {
            0% {
                background-position: center top;
            }
            50% {
                background-position: center 5%;
            }
            100% {
                background-position: center top;
            }
        }

        .btn-projects {
            margin-top: 20px;
            padding: 12px 48px;
            color: #ffffff;
            background-color: #4d4d4d;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-projects:hover {
            background-color: #6b6b6b;
        }

        .welcome-animation {
            position: relative;
            padding: 30px 70px;
            color: #ffffff;
            background: linear-gradient(to right, #4d4d4d 0, white 10%, #4d4d4d 20%);
            background-position: calc(-200px);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s infinite linear;
            animation-fill-mode: forwards;
            -webkit-text-size-adjust: none;
            font-weight: 600;
            font-size: 70px;
            text-decoration: none;
            white-space: nowrap;
        }

        @keyframes shine {
            0% {
                background-position: calc(-100px);
            }
            100% {
                background-position: calc(100% + 900px);
            }
        }

        .auth-links {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
        }

        .auth-links a {
            color: #ffffff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .auth-links a:hover {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="auth-links">
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>

    <h1 class="welcome-animation">Project Management System</h1>

    <a href="index.php" class="btn-projects">Projects</a>
</body>
</html>
