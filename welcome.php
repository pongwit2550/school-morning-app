<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'db/db.php';

// Get the current user's grade and room
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT class FROM teacher WHERE teacher_name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($grade_room);
$stmt->fetch();
$stmt->close();

list($grade, $room) = explode('/', $grade_room);

// Get students in the same grade and room
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

// Get attendance statistics for the current class
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$stmt = $conn->prepare("SELECT date, COUNT(*) as total, SUM(status = 'present') as present, SUM(status = 'absent') as absent, SUM(status = 'late') as late FROM attendance WHERE class = ? AND date LIKE ? GROUP BY date");
$month_param = "$month%";
$stmt->bind_param("ss", $grade_room, $month_param);
$stmt->execute();
$attendance_stats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Prepare data for charts
$dates = [];
$present_counts = [];
$absent_counts = [];
$late_counts = [];
foreach ($attendance_stats as $stat) {
    $dates[] = $stat['date'];
    $present_counts[] = $stat['present'];
    $absent_counts[] = $stat['absent'];
    $late_counts[] = $stat['late'];
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ด</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            margin-top: 80%;
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

        .navbar-buttons {
            display: flex;
            gap: 20px;
        }

        .navbar-button {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .navbar-button:hover {
            background-color: #E0A800;
            transform: scale(1.05);
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown:hover .navbar-button {
            background-color: #E0A800;
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

        h1,
        h2,
        h3 {
            text-align: center;
            color: #FF4136;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }

        canvas {
            margin: 20px 0;
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

        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        form div {
            margin-right: 10px;
        }

        button {
            padding: 10px;
            background-color: #FF4136;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #E0362C;
            transform: scale(1.05);
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .button {
            padding: 10px 20px;
            background-color: #FF4136;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .button:hover {
            background-color: #E0362C;
            transform: scale(1.05);
        }

        .error {
            color: red;
            text-align: center;
        }

        .message {
            color: green;
            text-align: center;
        }

        @media (max-width: 768px) {
            .navbar-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .dropdown-content {
                min-width: 100px;
            }

            .container {
                margin-top: 100px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <span class="navbar-brand">แดชบอร์ด</span>
        <div class="navbar-buttons">
            <div class="dropdown">
                <a href="#" class="navbar-button">เมนู</a>
                <div class="dropdown-content">
                    <a href="welcome.php">ย้อนกลับ</a>
                    <a href="feedback.php">ประเมินความพึงพอใจ</a>
                    <a href="logout.php">ออกจากระบบ</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <h1>ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <h2>ห้อง: <?php echo htmlspecialchars($grade_room); ?></h2>

        <div class="card">
            <h3>สถิติการเข้าแถวในเดือนนี้</h3>
            <canvas id="attendancePieChart"></canvas>
            <div id="noPieData" style="display: none;">ไม่มีข้อมูล</div>
        </div>

        <div class="card">
            <h3>สถิติการเข้าแถวในแต่ละวัน</h3>
            <canvas id="attendanceBarChart"></canvas>
            <div id="noBarData" style="display: none;">ไม่มีข้อมูล</div>
        </div>

        <h3>รายชื่อนักเรียน</h3>
        <div class="student-list">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">เลขที่</th>
                        <th scope="col">ชื่อ</th>
                        <th scope="col">นามสกุล</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Sort students by number
                    usort($students, function ($a, $b) {
                        return $a['stu_number'] - $b['stu_number'];
                    });
                    foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['stu_number']); ?></td>
                            <td><?php echo htmlspecialchars($student['stu_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['stu_last_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h3>ดูข้อมูลย้อนหลัง</h3>
        <form action="welcome.php" method="get">
            <label for="month">เดือน:</label>
            <input type="month" id="month" name="month" value="<?php echo htmlspecialchars($month); ?>" class="form-control">
            <button type="submit" class="btn btn-primary mt-2">ดูข้อมูล</button>
        </form>

        <div class="buttons">
            <a href="view_edit.php" class="button">ดูและแก้ไข</a>
            <a href="save_today.php" class="button">บันทึกวันนี้</a>
        </div>
    </div>

    <script>
        const dates = <?php echo json_encode($dates); ?>;
        const presentCounts = <?php echo json_encode($present_counts); ?>;
        const absentCounts = <?php echo json_encode($absent_counts); ?>;
        const lateCounts = <?php echo json_encode($late_counts); ?>;

        const pieCtx = document.getElementById('attendancePieChart').getContext('2d');
        const barCtx = document.getElementById('attendanceBarChart').getContext('2d');

        if (presentCounts.length === 0 && absentCounts.length === 0 && lateCounts.length === 0) {
            document.getElementById('noPieData').style.display = 'block';
            document.getElementById('attendancePieChart').style.display = 'none';
        } else {
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['มา', 'ขาด', 'สาย'],
                    datasets: [{
                        data: [
                            presentCounts.reduce((a, b) => a + b, 0),
                            absentCounts.reduce((a, b) => a + b, 0),
                            lateCounts.reduce((a, b) => a + b, 0)
                        ],
                        backgroundColor: ['#4CAF50', '#F44336', '#FFC107']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'สถิติการเข้าแถวในเดือนนี้'
                        }
                    }
                }
            });
        }

        if (dates.length === 0) {
            document.getElementById('noBarData').style.display = 'block';
            document.getElementById('attendanceBarChart').style.display = 'none';
        } else {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [{
                            label: 'มา',
                            data: presentCounts,
                            backgroundColor: '#4CAF50'
                        },
                        {
                            label: 'ขาด',
                            data: absentCounts,
                            backgroundColor: '#F44336'
                        },
                        {
                            label: 'สาย',
                            data: lateCounts,
                            backgroundColor: '#FFC107'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'สถิติการเข้าแถวในแต่ละวัน'
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'วันที่'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'จำนวน'
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>