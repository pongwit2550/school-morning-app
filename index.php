<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: slideIn 0.5s ease-in-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        h2 {
            color: #FF4136;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            margin-bottom: 20px;
        }

        .message {
            color: green;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 20px;
            background-color: #FF4136;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            width: 100%;
            font-size: 1.2em;
        }

        button:hover {
            background-color: #E0362C;
            transform: scale(1.05);
        }

        .register-link {
            margin-top: 20px;
        }

        .version {
            margin-top: 20px;
            color: #888;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>เข้าสู่ระบบ</h2>
        <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo "<p class='error'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['message'])) {
            echo "<p class='message'>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['message']);
        }
        ?>
        <form action="login.php" method="post">
            <div>
                <label for="username">ชื่อผู้ใช้:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">รหัสผ่าน:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <button type="submit">เข้าสู่ระบบ</button>
            </div>
        </form>

        <div class="register-link">
            <a href="register.php">สมัครสมาชิก?</a>
        </div>
        <div class="version">(เว็ปนี้เป็น web Application version Alpha 0.0.8)</div>
    </div>
</body>

</html>