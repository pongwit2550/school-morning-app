<?php
session_start();
include 'db/db.php'; // เชื่อมต่อฐานข้อมูล (ไฟล์ที่มีการเชื่อมต่อฐานข้อมูล)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ตรวจสอบชื่อผู้ใช้และรหัสผ่านจากฐานข้อมูล
    $stmt = $pdo->prepare("SELECT * FROM teacher WHERE teacher_name = :username");
    $stmt->execute(['username' => $username]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teacher && password_verify($password, $teacher['password'])) {
        // ถ้าชื่อผู้ใช้และรหัสผ่านถูกต้อง
        $_SESSION['teacher_id'] = $teacher['teacher_id'];
        $_SESSION['teacher_name'] = $teacher['teacher_name'];
        $_SESSION['role'] = $teacher['role'];

        $_SESSION['message'] = "ยินดีต้อนรับ, " . $teacher['teacher_name'] . "!";
        header("Location: welcome.php"); // เปลี่ยนไปหน้า welcome.php
        exit();
    } else {
        // ถ้าชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
        $_SESSION['error'] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
        header("Location: index.php"); // กลับไปที่หน้า index.php
        exit();
    }
}
?>
