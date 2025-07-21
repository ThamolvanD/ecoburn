<?php
require "connect.php";


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// ตรวจสอบการเชื่อมต่อ
if (!$con) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed']);
    exit();
}

// ตรวจสอบว่าค่าทุกฟิลด์ที่จำเป็นถูกส่งมาหรือไม่
$required_fields = ['username', 'email', 'phone', 'province', 'agency', 'password'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Missing field: $field"]);
        exit();
    }
}

// รับค่าจาก POST
$username = $_POST['username'];
$email    = $_POST['email'];
$phone    = $_POST['phone'];
$province = $_POST['province'];
$agency   = $_POST['agency'];
$password = $_POST['password'];
$role     = 'officer';

// ค่า default สำหรับเขตพื้นที่ (ถ้ายังไม่กรอก)
$village     = '';
$subdistrict = '';
$district    = '';

// เข้ารหัสรหัสผ่าน
$hashed_pwd = password_hash($password, PASSWORD_DEFAULT);

// ตรวจสอบว่า email ซ้ำหรือไม่ (ใช้ Prepared Statement)
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
    district, province, agency, password, role
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$insert_stmt = mysqli_prepare($con, $insert_sql);
mysqli_stmt_bind_param($insert_stmt, "ssssssssss",
    $username, $email, $phone, $village, $subdistrict,
    $district, $province, $agency, $hashed_pwd, $role
);

// ประมวลผลคำสั่ง
if (mysqli_stmt_execute($insert_stmt)) {
    echo json_encode(['status' => 'success', 'message' => 'Officer registered successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
}

// ปิดการใช้งาน
mysqli_stmt_close($insert_stmt);
mysqli_close($con);
?>
