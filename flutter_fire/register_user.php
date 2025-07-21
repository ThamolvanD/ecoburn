<?php 
require "connect.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// ตรวจสอบการเชื่อมต่อ
if (!$con) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed']);
    exit();
}

// ตรวจสอบว่าค่าทุกฟิลด์ถูกส่งมาหรือไม่
$required_fields = ['username', 'email', 'phone', 'village', 'subdistrict', 'district', 'province', 'password'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Missing field: $field"]);
        exit();
    }
}

// รับค่าจาก POST
$username    = $_POST['username'];
$email       = $_POST['email'];
$phone       = $_POST['phone'];
$village     = $_POST['village'];
$subdistrict = $_POST['subdistrict'];
$district    = $_POST['district'];
$province    = $_POST['province'];
$password    = $_POST['password'];
$role        = 'user'; // ค่า default

// กรณีผู้ใช้ทั่วไป ไม่มี agency
$agency = null;  // กำหนดค่า NULL เพื่อใส่ในฐานข้อมูล

// เข้ารหัสรหัสผ่านอย่างปลอดภัย
$encrypted_pwd = password_hash($password, PASSWORD_DEFAULT);

// ตรวจสอบว่า email ซ้ำหรือไม่
$check_query = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($con, $check_query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
    exit();
}
mysqli_stmt_close($stmt);

// เตรียมคำสั่ง insert แบบปลอดภัย (Prepared Statement)
$insert_sql = "INSERT INTO users (
    username, email, phone, village, subdistrict,
    district, province, agency, password, role
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$insert_stmt = mysqli_prepare($con, $insert_sql);

// แก้ไข bind param เพิ่ม agency และ role
mysqli_stmt_bind_param($insert_stmt, "ssssssssss",
    $username, $email, $phone, $village, $subdistrict,
    $district, $province, $agency, $encrypted_pwd, $role
);

if (mysqli_stmt_execute($insert_stmt)) {
    echo json_encode(['status' => 'success', 'message' => 'User registered successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . mysqli_error($con)]);
}

mysqli_stmt_close($insert_stmt);
mysqli_close($con);
?>
