<?php
include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $grade = $_POST['grade'];
    $room = $_POST['room'];
    $subjects = $_POST['subjects'];

    // Format the grade and room information
    if (strpos($grade, 'ม.') !== false) {
        $grade_room = $grade . '/' . str_replace('ม.', '', $room);
    } else {
        $grade_room = $grade . '/' . str_replace('ปวช.', '', $room);
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT teacher_id FROM teacher WHERE teacher_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = 'ชื่อผู้ใช้นี้มีอยู่แล้ว';
        header('Location: register.php');
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO teacher (teacher_name, password, class, subject) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $grade_room, $subjects);
        if ($stmt->execute()) {
            $_SESSION['message'] = 'สมัครสมาชิกสำเร็จ';
            header('Location: index.php');
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาด: ' . $stmt->error;
            header('Location: register.php');
        }
    }

    $stmt->close();
    $conn->close();
}
