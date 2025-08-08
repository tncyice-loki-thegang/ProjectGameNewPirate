<?php
// ไฟล์สำหรับเพิ่มฮีโร่ให้ผู้เล่น
include_once "../../../www/core/config.php";

// เชื่อมต่อฐานข้อมูล
$conn = @mysql_connect("$db_host","$db_username","$db_password") or die ("การเชื่อมต่อฐานข้อมูลไม่สำเร็จ กรุณาตรวจสอบ IP");
@mysql_select_db("$database",$conn) or die ("ฐานข้อมูลไม่มีอยู่หรือไม่สามารถเชื่อมต่อได้ กรุณาติดต่อผู้ดูแลระบบ");
mysql_query("set names utf8"); //ใช้การเข้ารหัสไฟล์เพื่อป้องกันข้อผิดพลาด

// ฟังก์ชันเพิ่มฮีโร่
function addHero($uid, $htid, $level = 1) {
    global $conn;
    
    // ตรวจสอบว่าผู้เล่นมีฮีโร่นี้อยู่แล้วหรือไม่
    $sql = "SELECT * FROM t_hero WHERE uid = '$uid' AND htid = '$htid'";
    $result = mysql_query($sql);
    
    if(mysql_num_rows($result) > 0) {
        return false; // มีฮีโร่นี้อยู่แล้ว
    }
    
    // เพิ่มฮีโร่ใหม่
    $sql = "INSERT INTO t_hero (uid, htid, level, all_exp, create_time) VALUES ('$uid', '$htid', '$level', 0, NOW())";
    $result = mysql_query($sql);
    
    if($result) {
        return true; // เพิ่มสำเร็จ
    } else {
        return false; // เพิ่มไม่สำเร็จ
    }
}

// ตัวอย่างการใช้งาน
if(isset($_GET['uid']) && isset($_GET['htid'])) {
    $uid = $_GET['uid'];
    $htid = $_GET['htid'];
    $level = isset($_GET['level']) ? $_GET['level'] : 1;
    
    if(addHero($uid, $htid, $level)) {
        echo "เพิ่มฮีโร่สำเร็จ!";
    } else {
        echo "เพิ่มฮีโร่ไม่สำเร็จ!";
    }
}
?>