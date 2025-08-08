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

// Bảng level_up_exp
$level_up_exp = [
    2 => 1050, 3 => 2800, 4 => 5500, 5 => 9050, 6 => 13350, 7 => 18250, 8 => 23600, 9 => 29400, 10 => 35650,
    11 => 42150, 12 => 49000, 13 => 56000, 14 => 63200, 15 => 70600, 16 => 77950, 17 => 85350, 18 => 92850, 19 => 100200, 20 => 107550,
    21 => 114650, 22 => 121700, 23 => 128500, 24 => 135100, 25 => 141450, 26 => 147550, 27 => 153400, 28 => 158900, 29 => 164150, 30 => 169000,
    31 => 199700, 32 => 233950, 33 => 272000, 34 => 314050, 35 => 360150, 36 => 368100, 37 => 375450, 38 => 382150, 39 => 380250, 40 => 377300,
    41 => 373450, 42 => 368750, 43 => 371650, 44 => 374050, 45 => 393650, 46 => 413700, 47 => 434100, 48 => 454750, 49 => 475700, 50 => 496950,
    51 => 518350, 52 => 539950, 53 => 573150, 54 => 607800, 55 => 643900, 56 => 681600, 57 => 720900, 58 => 761800, 59 => 788500, 60 => 815700,
    61 => 843350, 62 => 871450, 63 => 900000, 64 => 929050, 65 => 958550, 66 => 988500, 67 => 1018900, 68 => 1049800, 69 => 1081100, 70 => 1112900,
    71 => 1145150, 72 => 1177900, 73 => 1211050, 74 => 1244700, 75 => 1278800, 76 => 1313350, 77 => 1348400, 78 => 1383850, 79 => 1419800, 80 => 1456200,
    81 => 2239550, 82 => 2417950, 83 => 2548200, 84 => 2698700, 85 => 2878900, 86 => 2947450, 87 => 3016800, 88 => 3086950, 89 => 3157900, 90 => 3229700,
    91 => 3302250, 92 => 3375650, 93 => 3449850, 94 => 3524800, 95 => 3600650, 96 => 3677250, 97 => 3754650, 98 => 3832850, 99 => 3911900, 100 => 3991750,
    101 => 4072350, 102 => 4153800, 103 => 4236050, 104 => 4319150, 105 => 4403000, 106 => 4487650, 107 => 4573150, 108 => 4659450, 109 => 4746500, 110 => 4834400,
    111 => 4923100, 112 => 5012650, 113 => 5102950, 114 => 5194050, 115 => 5286000, 116 => 5378750, 117 => 5472300, 118 => 5566650, 119 => 5661800, 120 => 5757750,
    121 => 5854500, 122 => 5952100, 123 => 6050450, 124 => 6149650, 125 => 6249650, 126 => 6350450, 127 => 6452050, 128 => 6554450, 129 => 6657700, 130 => 6761700,
    131 => 6866550, 132 => 6972200, 133 => 7078650, 134 => 7185900, 135 => 7293950, 136 => 7402800, 137 => 7512450, 138 => 7622950, 139 => 7734250, 140 => 7846300,
    141 => 7959200, 142 => 8072900, 143 => 8187450, 144 => 8302750, 145 => 8418850, 146 => 8535800, 147 => 8653550, 148 => 8772050, 149 => 8891400,
    150 => 9011550, 151 => 9132550, 152 => 9254300, 153 => 9376850, 154 => 9500250, 155 => 9624450, 156 => 9749450, 157 => 9875200, 158 => 10001850, 159 => 10129250,
    160 => 10257450, 161 => 10386500, 162 => 10516300, 163 => 10646950, 164 => 10778400, 165 => 10910650, 166 => 11043700, 167 => 11177550, 168 => 11312250, 169 => 11447700,
    170 => 11584000, 171 => 11721050, 172 => 11858950, 173 => 11997650, 174 => 12137200, 175 => 12277500, 176 => 12418600, 177 => 12560550, 178 => 12703250, 179 => 12846800,
    180 => 12991150, 181 => 13136300, 182 => 13282250, 183 => 13429050, 184 => 13576600, 185 => 13725000, 186 => 13874150, 187 => 14024150, 188 => 14174950, 189 => 14326550,
    190 => 14478950, 191 => 14632200, 192 => 14786200, 193 => 14941050, 194 => 15096650, 195 => 15253100, 196 => 15410350, 197 => 15568400, 198 => 15727250, 199 => 15886950,
    200 => 16047400
];

// Hàm tính all_exp dựa trên level mong muốn
function calculateAllExp($target_level, $level_up_exp) {
    if ($target_level < 1 || $target_level > 200) {
        return false; // Level ไม่ถูกต้อง
    }
    $total_exp = 0;
    // Tính tổng exp từ cấp 2 đến cấp (target_level - 1)
    for ($lvl = 2; $lvl < $target_level; $lvl++) {
        if (isset($level_up_exp[$lvl])) {
            $total_exp += $level_up_exp[$lvl];
        } else {
            return false; // Level ไม่ถูกต้อง
        }
    }
    return $total_exp;
}

// Hàm kiểm tra level thực tế dựa trên all_exp
function calculateActualLevel($all_exp, $level_up_exp) {
    $total_exp = 0;
    $level = 1;
    foreach ($level_up_exp as $lvl => $exp) {
        $total_exp += $exp;
        if ($all_exp >= $total_exp) {
            $level = $lvl;
        } else {
            break;
        }
    }
    return $level;
}

// Xử lý form khi submit
$uname = '';
$level = '';
$toast_message = '';
$toast_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = trim($_POST['uname']);
    $level = trim($_POST['level']);
    $logout_confirmed = isset($_POST['logout_confirm']) ? $_POST['logout_confirm'] : '';

    // Kiểm tra đầu vào
    if (empty($uname)) {
        $toast_message = "กรุณาใส่ชื่อตัวละคร";
        $toast_type = 'error';
    } elseif (empty($level) || !is_numeric($level) || $level < 1 || $level > 200) {
        $toast_message = "กรุณาใส่ระดับที่ถูกต้อง (1-200)";
        $toast_type = 'error';
    } elseif ($logout_confirmed !== 'on') {
        $toast_message = "กรุณายืนยันว่าได้ออกจากเกมแล้ว";
        $toast_type = 'error';
    } else {
        // Tính all_exp từ level mong muốn
        $all_exp = calculateAllExp($level, $level_up_exp);
        if ($all_exp === false) {
            $toast_message = "ระดับ $level ไม่ถูกต้อง";
            $toast_type = 'error';
        } else {
            // Truy vấn lấy uid từ t_user
            $sql = "SELECT uid FROM t_user WHERE uname = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $uname);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $uid = $row['uid'];

                // Cập nhật level và all_exp trong t_hero cho nhân vật chính (htid bắt đầu bằng 11xxx)
                $sql_update = "UPDATE t_hero SET level = ?, all_exp = ? WHERE uid = ? AND htid LIKE '11%'";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("iii", $level, $all_exp, $uid);

                if ($stmt_update->execute()) {
                    // Kiểm tra level thực tế dựa trên all_exp
                    $actual_level = calculateActualLevel($all_exp, $level_up_exp);
                    if ($actual_level != $level) {
                        $toast_message = "คำเตือน: all_exp = $all_exp สำหรับระดับ $level แต่เกมอาจแสดงระดับ $actual_level กรุณาตรวจสอบตาราง exp";
                        $toast_type = 'warning';
                    } else {
                        $toast_message = "อัปเดตสำเร็จสำหรับตัวละคร $uname (UID: $uid, Level: $level, all_exp: $all_exp)";
                        $toast_type = 'success';
                    }
                } else {
                    $toast_message = "ข้อผิดพลาดในการอัปเดต: " . $conn->error;
                    $toast_type = 'error';
                }
                $stmt_update->close();
            } else {
                $toast_message = "ไม่พบตัวละคร $uname";
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
    <title>อัปเดตระดับตัวละคร</title>
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
        .toast-warning {
            background-color: #f59e0b !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-lg font-bold text-center text-gray-800 mb-5">อัปเดตระดับตัวละคร</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="updateLevelForm">
            <div class="mb-3">
                <label for="uname" class="block text-sm font-medium text-gray-700 mb-1">ชื่อตัวละคร</label>
                <input type="text" id="uname" name="uname" value="<?php echo htmlspecialchars($uname); ?>" 
                       class="input-field w-full p-2 border border-gray-300 rounded-lg focus:outline-none">
            </div>
            <div class="mb-3">
                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">ระดับที่ต้องการ</label>
                <input type="number" id="level" name="level" value="<?php echo htmlspecialchars($level); ?>" 
                       class="input-field w-full p-2 border border-gray-300 rounded-lg focus:outline-none" min="1" max="200">
            </div>
            <div class="mb-3">
                <label class="flex items-center">
                    <input type="checkbox" name="logout_confirm" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">ยืนยันว่าได้ออกจากเกมแล้ว</span>
                </label>
            </div>
            <button type="submit" class="btn w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700">
                อัปเดต
            </button>
        </form>
    </div>

    <?php if ($toast_message): ?>
        <script>
            Toastify({
                text: "<?php echo $toast_message; ?>",
                duration: 5000,
                gravity: "top",
                position: "right",
                backgroundColor: "<?php echo $toast_type === 'success' ? '#10b981' : ($toast_type === 'warning' ? '#f59e0b' : '#ef4444'); ?>",
                stopOnFocus: true,
                className: "<?php echo $toast_type === 'warning' ? 'toast-warning' : ''; ?>"
            }).showToast();
        </script>
    <?php endif; ?>
</body>
</html>