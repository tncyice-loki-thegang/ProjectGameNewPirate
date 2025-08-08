<?php
session_start();
error_reporting(E_ALL^E_NOTICE);
//การเชื่อมต่อฐานข้อมูล
$db_type='mysql';
$db_charset='utf8';
$db_host='192.168.1.234';
$db_username='root';
$db_password='';
$database='t9-rxhzw-web';
//ข้อมูลการตั้งค่าเว็บไซต์
$WebTitle='VUA HẢI TẶC';
$qq='LOULX GAME';
$fuli=1000000000;    //โบนัสเพชรที่ให้เมื่อผู้เล่นสมัครสมาชิก
$bili=10000;
$tgurl="";
if(empty($tgurl)) $url_this = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
else $url_this =$tgurl;
$conn = @mysql_connect("$db_host","$db_username","$db_password") or die ("การเชื่อมต่อฐานข้อมูลไม่สำเร็จ กรุณาตรวจสอบ IP");
@mysql_select_db("$database",$conn) or die ("ฐานข้อมูลไม่มีอยู่หรือไม่สามารถเชื่อมต่อได้ กรุณาติดต่อผู้ดูแลระบบ");
mysql_query("set names UTF8"); //ใช้การเข้ารหัสไฟล์เพื่อป้องกันข้อผิดพลาด
function getIP()
{
if(!empty($_SERVER["HTTP_CLIENT_IP"]))
   $ip = $_SERVER["HTTP_CLIENT_IP"];
else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
   $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
else if(!empty($_SERVER["REMOTE_ADDR"]))
   $ip = $_SERVER["REMOTE_ADDR"];
else
   $ip = "ไม่สามารถรับข้อมูลได้!";
return $ip;
}
?>
