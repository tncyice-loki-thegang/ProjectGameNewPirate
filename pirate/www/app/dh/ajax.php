<?php
include_once "../../core/config.php";
if(!$_SESSION['accountName']) exit("<script> alert('กรุณาเข้าสู่ระบบเพื่อใช้งาน!');location.href='/index.php';</script>");

if($_POST['action']=='Exchange')
{
	$server = $_POST['server'];
	$item = $_POST['item'];
	$amount = $_POST['U_DH_Num'];
	
	// ตรวจสอบยอดเงินในบัญชี
	$sql = "SELECT * FROM t_user WHERE uname='$server'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	
	if(!$row)
	{
		$array = array("info"=> "ไม่พบข้อมูลผู้ใช้","status" => "n");
		echo json_encode($array);
		exit;
	}
	
	// ตรวจสอบยอดเงิน
	if($amount > 10000000)
	{
		$array = array("info"=> "การแลกไม่สำเร็จเนื่องจากเงินทองไม่เพียงพอ!!","status" => "n" ,"url" => "member.php");
		echo json_encode($array);
		exit;
	}
	
	// ทำการแลก
	$sql = "UPDATE t_user SET gold = gold - $amount WHERE uname='$server'";
	$result = mysql_query($sql);
	
	if($result)
	{
		$array = array("info"=> "แลกของรางวัลสำเร็จ!","status" => "y" ,"url" => "/member.php");
		echo json_encode($array);
		exit;
	}
	else
	{
		$array = array("info"=> "เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง","status" => "n");
		echo json_encode($array);
		exit;
	}
}

if($_POST['action']=='Get_GameUser')
{
	$server = $_POST['server'];
	
	$sql = "SELECT * FROM t_user WHERE uname='$server'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	
	if($row)
	{
		$html = "<label class='radio'><input name='character' value='".$row['uname']."'  type='radio' data-validate='radio:กรุณาเลือกตัวละครในเกม'> ".$row['uname']."</label>";
		$array = array("html"=> $html, "status" => "y");
		echo json_encode($array);
		exit;
	}
	else
	{
		$html = "<p class='text-red'>ไม่พบตัวละครในเซิร์ฟเวอร์นี้</p>";
		$array = array("html"=> $html, "status" => "n");
		echo json_encode($array);
		exit;
	}
}
?>