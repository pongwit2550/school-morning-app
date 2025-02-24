<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
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
            max-width: 500px;
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
        input[type="password"],
        select {
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

        .back-button {
            margin-top: 20px;
            display: block;
            text-align: center;
        }

        .back-button a {
            color: #FF4136;
            text-decoration: none;
            font-size: 1em;
        }

        .back-button a:hover {
            text-decoration: underline;
        }

        .version {
            margin-top: 20px;
            color: #888;
            font-size: 0.9em;
        }
    </style>
    <script>
        function updateRooms() {
            var grade = document.getElementById("grade").value;
            var roomSelect = document.getElementById("room");
            roomSelect.innerHTML = "";

            if (grade.startsWith("ม.")) {
                for (var i = 1; i <= 14; i++) {
                    var option = document.createElement("option");
                    option.value = "ม." + i;
                    option.text = "ห้อง. " + i;
                    roomSelect.appendChild(option);
                }
            } else if (grade.startsWith("ปวช.")) {
                for (var i = 1; i <= 3; i++) {
                    var option = document.createElement("option");
                    option.value = "ปวช." + i;
                    option.text = "ห้อง. " + i;
                    roomSelect.appendChild(option);
                }
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>สมัครสมาชิก</h2>
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
        <form action="register_process.php" method="post">
            <div>
                <label for="username">ชื่อผู้ใช้:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">รหัสผ่าน:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="grade">ระดับชั้น:</label>
                <select id="grade" name="grade" required onchange="updateRooms()">
                    <option value="">เลือกระดับชั้น</option>
                    <?php
                    for ($i = 1; $i <= 6; $i++) {
                        echo "<option value='ม.$i'>ม.$i</option>";
                    }
                    for ($i = 1; $i <= 3; $i++) {
                        echo "<option value='ปวช.$i'>ปวช.$i</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="room">ห้อง:</label>
                <select id="room" name="room" required>
                    <option value="">เลือกห้อง</option>
                </select>
            </div>
            <div>
                <label for="subjects">วิชาที่คุณสอน:</label>
                <select id="subjects" name="subjects">
                    <option value="คณิตศาสตร์">คณิตศาสตร์</option>
                    <option value="วิทยาศาสตร์">วิทยาศาสตร์</option>
                    <option value="ภาษาไทย">ภาษาไทย</option>
                    <option value="สังคมศึกษา">สังคมศึกษา</option>
                    <option value="ภาษาอังกฤษ">ภาษาอังกฤษ</option>
                    <option value="พลศึกษา">พลศึกษา</option>
                    <option value="ศิลปะ">ศิลปะ</option>
                    <option value="การงานอาชีพ">การงานอาชีพ</option>
                    <option value="คอมพิวเตอร์">คอมพิวเตอร์</option>
                    <option value="บัญชี">บัญชี</option>
                    <option value="การตลาด">การตลาด</option>
                    <option value="ภาษาจีน">ภาษาจีน</option>
                    <option value="ภาษาญี่ปุ่น">ภาษาญี่ปุ่น</option>
                    <option value="ภาษาเกาหลี">ภาษาเกาหลี</option>
                </select>
            </div>
            <div>
                <button type="submit">สมัครสมาชิก</button>
            </div>
        </form>
        <div class="back-button">
            <a href="index.php">ย้อนกลับ</a>
        </div>
        <div class="version">(เว็ปนี้เป็น web Application version Alpha 0.0.8)</div>
    </div>
</body>

</html>