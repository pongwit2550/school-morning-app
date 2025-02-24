<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $attendance_data = $_POST['attendance'];

    foreach ($attendance_data as $stu_id => $status) {
        // Check if attendance already exists for the selected date
        $stmt = $conn->prepare("SELECT id FROM attendance WHERE stu_id = ? AND date = ?");
        $stmt->bind_param("is", $stu_id, $date);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update existing record
            $stmt->close();
            $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE stu_id = ? AND date = ?");
            $stmt->bind_param("sis", $status, $stu_id, $date);
        } else {
            // Insert new record
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO attendance (stu_id, date, status) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $stu_id, $date, $status);
        }

        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['message'] = 'บันทึกข้อมูลสำเร็จ';
    header('Location: view_edit.php?date=' . $date);
    exit;
} else {
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
    header('Location: view_edit.php');
    exit;
}
