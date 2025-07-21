<?php
require "connect.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// ตรวจสอบการเชื่อมต่อ
if (!$con) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed']);
    exit();
}

// ตรวจสอบว่าฟิลด์ที่จำเป็นถูกส่งมาหรือไม่
$required_fields = ['username', 'email', 'phone', 'village', 'subdistrict', 'province', 'password'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Missing field: $field"]);
        exit();
    }
}

// รับข้อมูลจาก POST
$username    = $_POST['username'];
$email       = $_POST['email'];
$phone       = $_POST['phone'];
$village     = $_POST['village'];
$subdistrict = $_POST['subdistrict'];
$province    = $_POST['province'];
$password    = $_POST['password'];
$role        = 'village_head';

// ตั้งค่า default สำหรับฟิลด์ที่ยังไม่ได้ใช้งาน
$district = '';

// เข้ารหัสรหัสผ่าน
$encrypted_pwd = password_hash($password, PASSWORD_DEFAULT);

// ตรวจสอบว่า email ซ้ำหรือไม่
$check_sql = "SELECT id FROM users WHERE email = ?";
$check_stmt = mysqli_prepare($con, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $email);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
    mysqli_stmt_close($check_stmt);
    exit();
}
mysqli_stmt_close($check_stmt);

// เตรียมคำสั่ง INSERT แบบปลอดภัย
$insert_sql = "INSERT INTO users (
    username, email, phone, village, subdistrict,
    district, province, password, role
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$insert_stmt = mysqli_prepare($con, $insert_sql);
mysqli_stmt_bind_param($insert_stmt, "sssssssss",
    $username, $email, $phone, $village, $subdistrict,
    $district, $province, $encrypted_pwd, $role
);

// รันคำสั่ง
if (mysqli_stmt_execute($insert_stmt)) {
    echo json_encode(['status' => 'success', 'message' => 'Village Head registered successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
}

// ปิด statement และ connection
mysqli_stmt_close($insert_stmt);
mysqli_close($con);
?>
