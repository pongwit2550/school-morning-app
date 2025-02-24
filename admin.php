<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['username'] != 'admin') {
    header('Location: index.php');
    exit;
}

include 'db/db.php';

// Get feedback data
$stmt = $conn->prepare("SELECT username, rating, comment, created_at FROM feedback ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$feedbacks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - ดู Feedback</title>
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

        .navbar {
            width: 100%;
            background-color: #FF4136;
            padding: 10px 20px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            color: #fff;
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-logout {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .navbar-logout:hover {
            background-color: #E0A800;
            transform: scale(1.05);
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            overflow-y: auto;
            margin-top: 80px;
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

        h1 {
            text-align: center;
            color: #FF4136;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        .version {
            margin-top: 20px;
            color: #888;
            font-size: 0.9em;
            text-align: center;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <span class="navbar-brand">Admin - ดู Feedback</span>
        <a href="logout.php" class="navbar-logout">ออกจากระบบ</a>
    </nav>
    <div class="container">
        <h1>Feedback</h1>
        <table>
            <thead>
                <tr>
                    <th>ชื่อผู้ใช้</th>
                    <th>คะแนน</th>
                    <th>ความคิดเห็น</th>
                    <th>วันที่</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($feedback['username']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['comment']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="version">(เว็ปนี้เป็น web Application version Alpha 0.0.8)</div>
    </div>
</body>

</html>