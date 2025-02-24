<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("INSERT INTO feedback (username, rating, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $username, $rating, $comment);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = 'ขอบคุณสำหรับการประเมินความพึงพอใจ';
    header('Location: welcome.php');
    exit;
} else {
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการส่งข้อมูล';
    header('Location: welcome.php');
    exit;
}
