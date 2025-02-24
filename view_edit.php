<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'db/db.php';

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get attendance data for the selected date
$stmt = $conn->prepare("SELECT stu_name.stu_number, stu_name.stu_id, stu_name.stu_name, stu_name.stu_last_name, attendance.status FROM attendance JOIN stu_name ON attendance.stu_id = stu_name.stu_id WHERE attendance.date = ?");
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();
$attendance_data = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Sort students by number
usort($attendance_data, function ($a, $b) {
    return $a['stu_number'] - $b['stu_number'];
});
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูและแก้ไขข้อมูลการเข้าแถว</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <nav class="navbar">
        <div class="navbar-container">
            <span class="navbar-brand">ดูและแก้ไขข้อมูลการเข้าแถว</span>
            <a href="welcome.php" class="navbar-logout">ย้อนกลับ</a>
        </div>
    </nav>
    <div class="container">
        <h1>ข้อมูลการเข้าแถววันที่ <?php echo htmlspecialchars($date); ?></h1>

        <form action="view_edit.php" method="get">
            <label for="date">เลือกวันที่:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
            <button type="submit">ดูข้อมูล</button>
        </form>

        <form action="update_attendance.php" method="post">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
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
                        <?php foreach ($attendance_data as $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['stu_number']); ?></td>
                                <td><?php echo htmlspecialchars($data['stu_name']); ?></td>
                                <td><?php echo htmlspecialchars($data['stu_last_name']); ?></td>
                                <td>
                                    <label>
                                        <input type="radio" name="attendance[<?php echo htmlspecialchars($data['stu_id']); ?>]" value="present" <?php echo $data['status'] == 'present' ? 'checked' : ''; ?>> มา
                                    </label>
                                    <label>
                                        <input type="radio" name="attendance[<?php echo htmlspecialchars($data['stu_id']); ?>]" value="absent" <?php echo $data['status'] == 'absent' ? 'checked' : ''; ?>> ขาด
                                    </label>
                                    <label>
                                        <input type="radio" name="attendance[<?php echo htmlspecialchars($data['stu_id']); ?>]" value="late" <?php echo $data['status'] == 'late' ? 'checked' : ''; ?>> สาย
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit">บันทึกการแก้ไข</button>
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