<?php
$servername = "ns3.hostify.in.th"; // ที่อยู่ของเซิร์ฟเวอร์ฐานข้อมูล
$username = "spminth_utdata";
$password = "jpuH2wJvYCcm4vojA6w9";
$dbname = "spminth_utdata";

// Create connection
//$conn = new mysqli(hostname: $servername, username: $username, password: $password, database: $dbname);
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>

