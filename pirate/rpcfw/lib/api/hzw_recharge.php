<?php
	$db_host='192.168.1.234';
$db_username='root';
$db_password='';

	$pid = $_GET['uid'];
	$group = 'game20002';
	$gold = $_GET['gold'];
	

$database='pirate20002';   //账号库
$conn = @mysql_connect("$db_host","$db_username","$db_password") or die ("服务器维护中~详情联系 ".$qq."。");
 @mysql_select_db("$database",$conn) or die ("数据库表不存在或者未连接。请联系管理员 。");
 mysql_query("set names utf8"); //使用文件编码，防止出错
    
	$sql="SELECT * FROM t_user WHERE pid='".$pid."'";
	$result=mysql_query($sql); 
	$row=mysql_fetch_array($result);
	$uid=$row['uid'];
	
	$order_id = time() . $uid . mt_rand(10, 99);

	$cmd = "cd /home/pirate/bin && ./btscript {$group} /home/pirate/rpcfw/test/AddOrder.php {$uid} {$order_id} {$gold} 0";
	passthru($cmd);

?>
