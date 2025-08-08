<?php
include_once "../../WebRes/config.php";
//
//Rid:4506898162254646   获取的角色ID
$U_DH_Num=$_POST['U_DH_Num'];
if($U_DH_Num<0){
$array = array("info"=>"元宝不能小于0！","status"=>"n");    
echo json_encode($array);
exit;
}
$Action=$_GET['Action'];
$serverid=$_POST['FQ'];
$sql1="SELECT * FROM $database.server WHERE fqid='".$serverid."'";
$result=mysql_query($sql1); 
$row2=mysql_fetch_array($result);
$dbid=$row2['dbname'];
$sid = $row2['fcm'];
if($Action=='Get_GameUser'){
//<label class="button margin-top"><input name="Rid" value="4506898162254646"  type="radio" data-validate="radio:请选择游戏角色"> 燕唯松</label>
$array = array("status"=>"y","html"=>"<label class=\"button margin-top\"><input name=\"Rid\" value=\"4506898162254646\"  type=\"radio\" data-validate=\"radio:请选择游戏角色\"> s".$sid.".".$_SESSION['accountName']."</label>");   
echo json_encode($array);
exit;
}


if($Action=='DHYB'){
$sql="SELECT * FROM $database.account WHERE name='".$_SESSION['accountName']."'";
$result=mysql_query($sql); 
$row=mysql_fetch_array($result);
$pid=$row['id'];



	$url = "http://192.168.1.234:10001/hzw_mail.php?group=".$serverid."&uid=".$pid."&itemid=".$U_DH_Num."&dname=".$dbid."&do=ok";
	$recharge_result = trim(file_get_contents($url));
	if ('ok' == $recharge_result) {  
	$array = array("info"=>"兑换元宝成功，请进入游戏查收","status"=>"y");    
	echo json_encode($array);
	exit;
	}else
	{
	$array = array("info"=>"充值失败请联系管理员:$qq","status"=>"y");    
	echo json_encode($array);
	exit;
	}


}

?>