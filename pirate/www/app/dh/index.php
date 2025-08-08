<?php
include_once "../../core/config.php";
if(!$_SESSION['accountName']) exit("<script> alert('กรุณาเข้าสู่ระบบเพื่อใช้งาน!');location.href='/index.php';</script>");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $WebTitle?> - แลกของรางวัล</title>
	<meta name="description" content="<?php echo $WebTitle?>" />
	<meta name="keywords" content="<?php echo $WebTitle?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" href="/WebRes/Css/pintuer.css">
<link rel="stylesheet" href="/WebRes/Css/my.css">
<script src="/WebRes/Js/jquery.js"></script>
<script src="/WebRes/Js/layer.min.js"></script>
<script src="/WebRes/Js/pintuer.js"></script>
<script src="/WebRes/Js/jquery.SuperSlide.2.1.1.js"></script>
<script src="/WebRes/Js/core.js"></script>
<script src="/WebRes/Js/cookie.js"></script>
<script src="/WebRes/Js/respond.js"></script>
</head>
<body class="body_2" >
	<div class="main_bj">
		<div class="Cx_Nav">
	<div class="DH_Panel float-left">
		<?php 
	require '../../top.php';//เรียกใช้เทมเพลต
	?> 
	</div>
	<div class="User_Panel bg-main float-right">		
		<img src="/WebRes/img/logo1.png" class="margin" width="80" height="80" />
		<div class="margin float-right User_Xx">	
						<strong class="text-white">ยินดีต้อนรับ <?php echo $_SESSION['accountName']; ?></strong>
						<strong class="float-right margin-big-right"> 
									<button class="button button-little bg-yellow" onclick="OpenApp('%7B%22Url%22%3A%22%5C%2Fapp%5C%2Fsys%5C%2Flogin.php%22%2C%22title%22%3A%22%5Cu4f1a%5Cu5458%5Cu767b%5Cu9646%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22480%22%2C%22W_h%22%3A%22350%22%2C%22fn%22%3A%22%22%7D'); ">เข้าสู่ระบบ</button>
					<button class="button button-little bg-dot" onclick="OpenApp('%7B%22Url%22%3A%22%5C%2Fapp%5C%2Fsys%5C%2Freg.php%22%2C%22title%22%3A%22%5Cu4f1a%5Cu5458%5Cu6ce8%5Cu518c%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22480%22%2C%22W_h%22%3A%22410%22%2C%22fn%22%3A%22%22%7D'); ">สมัครสมาชิก</button>
							</strong>
			<hr />
			<div class="media-inline">
				<div class="media text-center User_Login">
											<p class="text-white">《<?php echo $WebTitle?>》 เวอร์ชันออฟไลน์</p>
								</div>
			</div>
		</div>
	</div>
</div>		<div id="Main_Content" class="bg border border-main">
			<div class="margin">
			<form class="form-x" method="post" action="javascript:void(0);" id="form1">
			<div class="form-group">
				<div class="label"><label>เลือกเซิร์ฟเวอร์</label></div>
				<div class="field">
					<?php if($_SESSION['accountName']){?>
					<label class="radio"><input name="server" value="<?php echo $_SESSION['accountName']?>"  type="radio" data-validate="radio:กรุณาเลือกตัวละครในเกม"> <?php echo $_SESSION['accountName']?></label>
					<?php }else{?>
					<label class="radio"><input name="server" value="<?php echo $_SESSION['accountName']?>"  type="radio" data-validate="radio:กรุณาเลือกตัวละครในเกม"> <?php echo $_SESSION['accountName']?></label><?php }?>
				</div>
			</div>
			<div class="form-group">
				<div class="label"><label>เลือกของรางวัล</label></div>
				<div class="field">
					<select class="input" name="item">
						<option value="1">เงินทอง 100,000</option>
						<option value="2">ไอเทมพิเศษ</option>
						<option value="3">ประสบการณ์ 10,000</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="label"><label for="U_DH_Num">จำนวนที่แลก</label></div>
				<div class="field">
					<input type="text" class="input  text-center " id="U_DH_Num" name="U_DH_Num" size="20" data-validate="required:กรุณากรอกจำนวนที่แลก!,plusinteger:กรุณากรอกจำนวนเต็มบวก!,compare#>0:ค่าต้องมากกว่า 0,compare#<=10000000:ค่าต้องน้อยกว่าหรือเท่ากับยอดเงินในบัญชี"/>
					<div class="tips"></div>
				</div>
			</div>
			<div class="form-group">
				<div class="label"></div>
				<div class="field">
					<button class="button bg-main icon-check-square-o" type="button" onclick="Ajax_Action('Exchange')"  >แลกของรางวัล</button></div>
			</div>
			</form>
			</div>	
		</div>
	</div>
<div class="bg-inverse" id="footer">
		<div class="navbar">
			<div class="navbar-body nav-navicon" id="navbar-footer">
				<div class="navbar-text"><?php echo $WebTitle?>&nbsp;&nbsp;&nbsp;LOULX GAME</div>
			</div>
		</div>
</div></body>	
</html>