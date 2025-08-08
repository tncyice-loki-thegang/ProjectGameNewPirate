<?php
include_once "config.php";
if($_POST['action']=='Logout')
{
	session_destroy();
	$array = array("info"=> "ออกจากระบบสำเร็จ!","status" => "y" ,"url" => "/index.php");
	echo json_encode($array);
	exit;
}
if($_POST['action']=='Login')
{
	$Uname = $_POST['Uname'];
	$Upass = $_POST['Upass'];
	$Scode = $_POST['Scode'];
	
	$sql = "SELECT * FROM t_user WHERE uname='$Uname' AND upass='$Upass' AND scode='$Scode'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	
	if(!$row)
	{
		$array = array("info"=> "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง","status" => "n" ,"url" => "index.php");
		echo json_encode($array);
		exit;
	}
	
	$_SESSION['accountName'] = $row['uname'];
	$_SESSION['accountId'] = $row['uid'];
	
	$array = array("info"=> "เข้าสู่ระบบสำเร็จ กด OK เพื่อไปยังหน้าสมาชิก!!","status" => "y" ,"url" => "/member.php");
	echo json_encode($array);
	exit;
}
if($_POST['action']=='ChangePass')
{
	$Uname = $_SESSION['accountName'];
	$Upass = $_POST['Upass'];
	$NewPass = $_POST['NewPass'];
	$Scode = $_POST['Scode'];
	
	$sql = "UPDATE t_user SET upass='$NewPass' WHERE uname='$Uname' AND upass='$Upass' AND scode='$Scode'";
	$result = mysql_query($sql);
	
	if(mysql_affected_rows() > 0)
	{
		$array = array("info"=> "เปลี่ยนรหัสผ่านสำเร็จ กรุณาเข้าสู่ระบบใหม่","status" => "y");
		echo json_encode($array);
		exit;
	}
	else
	{
		$array = array("info"=> "ข้อมูลไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง","status" => "n");
		echo json_encode($array);
		exit;
	}
}
if($_POST['action']=='ChangeScode')
{
	$Uname = $_SESSION['accountName'];
	$Upass = $_POST['Upass'];
	$Scode = $_POST['Scode'];
	$NewScode = $_POST['NewScode'];
	
	$sql = "UPDATE t_user SET scode='$NewScode' WHERE uname='$Uname' AND upass='$Upass' AND scode='$Scode'";
	$result = mysql_query($sql);
	
	if(mysql_affected_rows() > 0)
	{
		$array = array("info"=> "เปลี่ยนรหัสยืนยันสำเร็จ กรุณาเข้าสู่ระบบใหม่","status" => "y");
		echo json_encode($array);
		exit;
	}
	else
	{
		$array = array("info"=> "ข้อมูลไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง","status" => "n");
		echo json_encode($array);
		exit;
	}
}
if($_POST['action']=='Register')
{
	$Uname = $_POST['Uname'];
	$Upass = $_POST['Upass'];
	$Scode = $_POST['Scode'];
	
	$sql = "SELECT * FROM t_user WHERE uname='$Uname'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	
	if($row)
	{
		$array = array("info"=> "ชื่อผู้ใช้นี้มีอยู่แล้ว กรุณาเลือกชื่อผู้ใช้อื่น","status" => "n" );
		echo json_encode($array);
		exit;
	}
	
	$sql = "INSERT INTO t_user (uname, upass, scode, create_time) VALUES ('$Uname', '$Upass', '$Scode', NOW())";
	$result = mysql_query($sql);
	
	if($result)
	{
		$array = array("info"=> "สมัครสมาชิกสำเร็จ กด OK เพื่อไปยังหน้าสมาชิก!!","status" => "y" ,"url" => "/member.php");
		echo json_encode($array);
		exit;
	}
	else
	{
		$array = array("info"=> "เครือข่ายไม่ว่าง กรุณาลองใหม่อีกครั้ง!!","status" => "n" );
		echo json_encode($array);
		exit;
	}
}
?> 