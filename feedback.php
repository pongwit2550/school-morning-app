<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'db/db.php';
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประเมินความพึงพอใจ</title>
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
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            padding: 20px;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            font-size: 2em;
            color: #ccc;
            cursor: pointer;
        }

        .star-rating input[type="radio"]:checked~label {
            color: #f5b301;
        }

        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #f5b301;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group textarea {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        button {
            padding: 10px 20px;
            background-color: #FF4136;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            display: block;
            width: 100%;
            font-size: 1.2em;
        }

        button:hover {
            background-color: #E0362C;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-container">
            <span class="navbar-brand">ประเมินความพึงพอใจ</span>
            <a href="welcome.php" class="navbar-logout">ย้อนกลับ</a>
        </div>
    </nav>
    <div class="container">
        <h1>ประเมินความพึงพอใจ</h1>
        <form action="submit_feedback.php" method="post">
            <div class="star-rating">
                <input type="radio" id="star5" name="rating" value="5">
                <label for="star5" title="5 stars">&#9733;</label>
                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4" title="4 stars">&#9733;</label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3" title="3 stars">&#9733;</label>
                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2" title="2 stars">&#9733;</label>
                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1" title="1 star">&#9733;</label>
            </div>
            <div class="form-group">
                <label for="comment">ความคิดเห็นเพิ่มเติม:</label>
                <textarea id="comment" name="comment" rows="4" placeholder="กรุณาแสดงความคิดเห็นเพิ่มเติม..."></textarea>
            </div>
            <button type="submit">ส่ง</button>
        </form>
    </div>
</body>

</html>