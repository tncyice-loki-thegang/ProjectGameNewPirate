<?php
// Kết nối MySQL
$serverName = '192.168.1.234';
$serverPort = '3306';
$userName = 'root';
$passCode = '';
$dbName = 'pirate20002';

$conn = new mysqli($serverName, $userName, $passCode, $dbName, $serverPort);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// Xử lý form
$uname_hero = '';
$uname_blood = '';
$toast_message = '';
$toast_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Xử lý form sửa t_hero
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'hero') {
        $uname_hero = trim($_POST['uname_hero']);
        $logout_confirm_hero = isset($_POST['logout_confirm_hero']) ? $_POST['logout_confirm_hero'] : '';

        if (empty($uname_hero)) {
            $toast_message = "กรุณาใส่ชื่อตัวละครสำหรับ Hero";
            $toast_type = 'error';
        } elseif ($logout_confirm_hero !== 'on') {
            $toast_message = "กรุณายืนยันว่าได้ออกจากเกมแล้วสำหรับ Hero";
            $toast_type = 'error';
        } else {
            $sql = "SELECT uid FROM t_user WHERE uname = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $uname_hero);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $uid = $row['uid'];

                $sql_hero = "UPDATE t_hero SET level = 10, exp = 0, all_exp = 0 WHERE uid = ? AND htid LIKE '11%'";
                $stmt_hero = $conn->prepare($sql_hero);
                $stmt_hero->bind_param("i", $uid);
                if ($stmt_hero->execute()) {
                    $toast_message = "อัปเดต Hero สำเร็จสำหรับ $uname_hero (UID: $uid)";
                    $toast_type = 'success';
                } else {
                    $toast_message = "ข้อผิดพลาดในการอัปเดต Hero: " . $conn->error;
                    $toast_type = 'error';
                }
                $stmt_hero->close();
            } else {
                $toast_message = "ไม่พบตัวละคร $uname_hero";
                $toast_type = 'error';
            }
            $stmt->close();
        }
    }

    // Xử lý form sửa blood_package
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'blood') {
        $uname_blood = trim($_POST['uname_blood']);
        $logout_confirm_blood = isset($_POST['logout_confirm_blood']) ? $_POST['logout_confirm_blood'] : '';

        if (empty($uname_blood)) {
            $toast_message = "กรุณาใส่ชื่อตัวละครสำหรับ Blood Package";
            $toast_type = 'error';
        } elseif ($logout_confirm_blood !== 'on') {
            $toast_message = "กรุณายืนยันว่าได้ออกจากเกมแล้วสำหรับ Blood Package";
            $toast_type = 'error';
        } else {
            $sql = "SELECT uid FROM t_user WHERE uname = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $uname_blood);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $uid = $row['uid'];

                $sql_blood = "UPDATE t_user SET blood_package = 0 WHERE uid = ?";
                $stmt_blood = $conn->prepare($sql_blood);
                $stmt_blood->bind_param("i", $uid);
                if ($stmt_blood->execute()) {
                    $toast_message = "อัปเดต Blood Package สำเร็จสำหรับ $uname_blood (UID: $uid)";
                    $toast_type = 'success';
                } else {
                    $toast_message = "ข้อผิดพลาดในการอัปเดต Blood Package: " . $conn->error;
                    $toast_type = 'error';
                }
                $stmt_blood->close();
            } else {
                $toast_message = "ไม่พบตัวละคร $uname_blood";
                $toast_type = 'error';
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขค่าบัญชี</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <style>
        body {
            background: linear-gradient(to bottom right, #1e3a8a, #3b82f6);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Inter', sans-serif;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 20rem;
        }
        @media (min-width: 1280px) {
            .container {
                max-width: 600px;
            }
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .btn {
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-lg font-bold text-center text-gray-800 mb-5">ราชาโจรสลัด - LouLx Game</h2>
        
        <!-- Form sửa Hero (t_hero) -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">แก้ไขข้อผิดพลาดระดับเกิน Lv.200</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="form_type" value="hero">
                <div class="mb-3">
                    <label for="uname_hero" class="block text-sm font-medium text-gray-700 mb-1">ชื่อตัวละคร</label>
                    <input type="text" id="uname_hero" name="uname_hero" value="<?php echo htmlspecialchars($uname_hero); ?>" 
                           class="input-field w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                </div>
                <div class="mb-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="logout_confirm_hero" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">ยืนยันว่าได้ออกจากเกมแล้ว</span>
                    </label>
                </div>
                <button type="submit" class="btn w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700">
                    อัปเดต
                </button>
            </form>
        </div>

        <!-- Form sửa Blood Package (t_user) -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">แก้ไขข้อผิดพลาดชีวิตเกินขีดจำกัดไม่สามารถเข้า PB ได้</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="form_type" value="blood">
                <div class="mb-3">
                    <label for="uname_blood" class="block text-sm font-medium text-gray-700 mb-1">ชื่อตัวละคร</label>
                    <input type="text" id="uname_blood" name="uname_blood" value="<?php echo htmlspecialchars($uname_blood); ?>" 
                           class="input-field w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
                </div>
                <div class="mb-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="logout_confirm_blood" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">ยืนยันว่าได้ออกจากเกมแล้ว</span>
                    </label>
                </div>
                <button type="submit" class="btn w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700">
                    อัปเดต
                </button>
            </form>
        </div>
    </div>

    <?php if ($toast_message): ?>
        <script>
            Toastify({
                text: "<?php echo $toast_message; ?>",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "<?php echo $toast_type === 'success' ? '#10b981' : '#ef4444'; ?>",
                stopOnFocus: true,
            }).showToast();
        </script>
    <?php endif; ?>
</body>
</html>