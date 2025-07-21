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
$required_fields = ['username', 'email', 'phone', 'password'];
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
$password = $_POST['password'];
$role     = 'admin';

// ตั้งค่าข้อมูลว่างสำหรับ admin ที่ไม่ได้กรอกข้อมูลตำแหน่งที่อยู่
$village     = '';
$subdistrict = '';
$district    = '';
$province    = '';

// เข้ารหัสรหัสผ่าน
$encrypted_pwd = password_hash($password, PASSWORD_DEFAULT);

// ตรวจสอบว่า email ซ้ำหรือไม่
$check_query = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($con, $check_query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
    mysqli_stmt_close($stmt);
    exit();
}
mysqli_stmt_close($stmt);

// เตรียมคำสั่ง insert แบบปลอดภัย
$insert_sql = "INSERT INTO users (
    username, email, phone, village, subdistrict,
    district, province, password, role
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$insert_stmt = mysqli_prepare($con, $insert_sql);
mysqli_stmt_bind_param($insert_stmt, "sssssssss",
    $username, $email, $phone, $village, $subdistrict,
    $district, $province, $encrypted_pwd, $role
);

if (mysqli_stmt_execute($insert_stmt)) {
    echo json_encode(['status' => 'success', 'message' => 'Admin registered successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
}

mysqli_stmt_close($insert_stmt);
mysqli_close($con);
?>
