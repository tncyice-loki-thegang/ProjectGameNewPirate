<?php
	$db_host='192.168.1.234';
$db_username='root';
$db_password='';

	$pid = $_GET['uid'];
	$group = 'game20002';
	$gold = $_GET['gold'];
	

$database='pirate20002';   //�˺ſ�
$conn = @mysql_connect("$db_host","$db_username","$db_password") or die ("������ά����~������ϵ ".$qq."��");
 @mysql_select_db("$database",$conn) or die ("���ݿ�����ڻ���δ���ӡ�����ϵ����Ա ��");
 mysql_query("set names utf8"); //ʹ���ļ����룬��ֹ����
    
	$sql="SELECT * FROM t_user WHERE pid='".$pid."'";
	$result=mysql_query($sql); 
	$row=mysql_fetch_array($result);
	$uid=$row['uid'];
	
	$order_id = time() . $uid . mt_rand(10, 99);

	$cmd = "cd /home/pirate/bin && ./btscript {$group} /home/pirate/rpcfw/test/AddOrder.php {$uid} {$order_id} {$gold} 0";
	passthru($cmd);

?>
