<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = date('Y-m-d');
    $attendance_data = $_POST['attendance'];

    foreach ($attendance_data as $stu_id => $status) {
        // Check if attendance already exists for today
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
    header('Location: welcome.php');
    exit;
}

// Get students in the same grade and room
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT class FROM teacher WHERE teacher_name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($grade_room);
$stmt->fetch();
$stmt->close();

list($grade, $room) = explode('/', $grade_room);

$stmt = $conn->prepare("SELECT stu_number, stu_id, stu_name, stu_last_name FROM stu_name WHERE stu_class = ?");
$stmt->bind_param("s", $grade_room);
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Sort students by number
usort($students, function ($a, $b) {
    return $a['stu_number'] - $b['stu_number'];
});
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกข้อมูลการเข้าแถววันนี้</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <nav class="navbar">
        <div class="navbar-container">
            <span class="navbar-brand">บันทึกข้อมูลการเข้าแถววันนี้</span>
            <a href="welcome.php" class="navbar-logout">ย้อนกลับ</a>
        </div>
    </nav>
    <div class="container">
        <h1>บันทึกข้อมูลการเข้าแถววันนี้</h1>

        <form action="save_today.php" method="post">
            <div class="student-list">
                <table>
                    <thead>
                        <tr>
                            <th>เลขที่</th>
                            <th>ชื่อ</th>
                            <th>นามสกุล</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['stu_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['stu_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['stu_last_name']); ?></td>
                                <td>
                                    <select name="attendance[<?php echo htmlspecialchars($student['stu_id']); ?>]">
                                        <option value="present">มา</option>
                                        <option value="absent">ขาด</option>
                                        <option value="late">สาย</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit">บันทึก</button>
        </form>
    </div>
</body>

</html>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .navbar {
        width: 100%;
        background-color: #007BFF;
        padding: 10px 0;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
    }

    .navbar-container {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
    }

    .navbar-brand {
        color: #fff;
        font-size: 20px;
        font-weight: bold;
    }

    .navbar-logout {
        color: #fff;
        text-decoration: none;
        background-color: #FF4136;
        padding: 10px 20px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .navbar-logout:hover {
        background-color: #E0362C;
    }

    .container {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 800px;
        overflow-y: auto;
        margin-top: 60px;
        /* Adjust for navbar height */
    }

    h1 {
        text-align: center;
    }

    .student-list {
        max-height: 300px;
        overflow-y: auto;
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

    button {
        padding: 10px;
        background-color: #007BFF;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: block;
        margin: 0 auto;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>