<?php
include_once "../../core/config.php";
//session_start();
if(!$_SESSION['accountName'] || !$_GET['FQ']) exit("<script> alert('กรุณาเข้าสู่ระบบเพื่อใช้งาน!');location.href='/index.php';</script>");

$newtime=time();
$server=$_GET['FQ'];
$sql="select * from server where fcm='".$server."'";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$server_nm = $row['name'];
if($row['opentime']>$newtime){
	$tim3=date('y-m-d h:i:s',$row['opentime']);
	$array = array("info"=> "","status" => "y" ,"url" => "/member.php");
	echo "<script>alert('【".$row['name']."】เวลาเปิดเซิร์ฟเวอร์คือ【".$tim3."】');location.href='../../member.php'</script>";
	exit;
}
$sql="select * from account where name ='".$_SESSION['accountName']."'";
$result=mysql_query($sql);
$row2=mysql_fetch_array($result);
$url = "http://192.168.1.234/T9NuoYa/T9NuoYa.html?sid=".$row2['id']."";


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>ราชาโจรสลัด 2023 LouLx Game</title>
<style type="text/css">

body, html {width:100%;height:100%;margin:0;padding:0;}
  </style>
</head>
<body scroll="no" style="background:#000;">
<iframe src="<?php echo $url;?>" id='mainFrame' name='mainFrame' scrolling='no' width='100%' height='100%' frameborder='0'></iframe>
</body>
</html>