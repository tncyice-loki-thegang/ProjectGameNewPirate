
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
  include_once "core/config.php";
if(!$_SESSION['accountName']) exit("<script> alert('กรุณาเข้าสู่ระบบเพื่อใช้งาน!');location.href='index.php';</script>");
$sql2="select * from account where name='".$_SESSION['accountName']."'";
$result2=mysql_query($sql2); 
$row2=mysql_fetch_array($result2);
$SCDJ=$row2['dj'];
$webkey			="99p4GHAJLgdq3giFPuN3BYizx5XHIhpz";
$unam=$_SESSION['accountName'];
$regkk = "6k3XO7CvwaeIi0eTunotecbUhGVCpVso";
$regk ="AIbYhyYdaj697TT2RoEgozCGsgyYpCnb";
$userid= $_SESSION['accountName'];; // 16位MD5加密 
$Loguser      	= $userid;				//平台账号
$Loguser      	= $userid;				//平台账号
$fuckU		  	= "FUCKU";
$time        	= time();				//时间戳
$loginKey		= substr(md5($Loguser.$webkey.$time.$fuckU), 8, 16);
?>
	<title><?php echo $WebTitle?></title>
	<meta name="description" content="<?php echo $WebTitle?>" />
	<meta name="keywords" content="<?php echo $WebTitle?>, phiên bản Offline" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" href="/WebRes/Css/pintuer.css">
<link rel="stylesheet" href="/WebRes/Css/my.css">
<script src="/WebRes/Js/jquery.js"></script>
<script src="/WebRes/Js/pintuer.js"></script>
<!--[if lt IE 9]><script src="/WebRes/Js/html5.js"></script><![endif]-->
<!--[if IE 6]><script src="/WebRes/Js/DD_belatedPNG.js"></script><![endif]-->
<script src="/WebRes/Js/jquery.SuperSlide.2.1.1.js"></script>
<script src="/WebRes/Js/layer.min.js"></script>
<script src="/WebRes/Js/core.js"></script>
<script src="/WebRes/js/cookie.js"></script>
<script src="/WebRes/Js/respond.js"></script></head>
<body class="body_2" >
	<div class="main_bj">
		<div class="Cx_Nav">
	<div class="DH_Panel float-left">
		<?php 
	require 'top.php';//ใช้เทมเพลต 
	?> 
	</div>
	<div class="User_Panel bg-main float-right">		
		<img src="/WebRes/img/logo1.png" class="margin" width="80" height="80" />
		<div class="margin float-right User_Xx">	
						<strong class="text-white">สวัสดี <strong class="text-dot"><?php echo $_SESSION['accountName']?></strong></strong>
						<strong class="float-right margin-big-right"> 
									<a class="button button-little bg-yellow" href="/WebRes/tg/manage/user.php"  target="_black">โฆษณา</a>
					<!--<button class="button button-little bg-dot" onclick="Ajax_Action('','lqsc')">领取首充</button>-->
					<button class="button button-little bg-sub" onclick="OpenApp('%7B%22Url%22%3A%22%5C%2Fapp%5C%2Fsys%5C%2Feditpass.php%22%2C%22title%22%3A%22%5Cu4fee%5Cu6539%5Cu5bc6%5Cu7801%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22480%22%2C%22W_h%22%3A%22350%22%2C%22fn%22%3A%22%22%7D'); ">เปลี่ยนรหัสผ่าน</button>
					<button class="button button-little bg-dot" onclick="Ajax_Action('','SignOut')">ออกจากระบบ</button>
							</strong>
			<hr />
			<div class="media-inline">
				<div class="media text-center User_Login">
										<div class="txt bg-main" title="เติมเงินจำนวนหนึ่งเพื่ออัปเกรดระดับของคุณ"><strong>สมาชิก</strong><br>ระดับ</div>
					<div class="txt bg-main" title="จำนวนเงินที่เติมปัจจุบันของผู้ใช้"><strong><?php echo $SCDJ?></strong><br>ยอดเงิน</div>
					<div class="txt bg-main" title="คะแนนจะได้รับคืนตามจำนวนเงินที่เติมในอัตราส่วน 1:1 และคะแนนจะใช้สำหรับการซื้อในศูนย์การค้าออนไลน์"><strong >ยังไม่เปิดใช้งาน</strong><!--strong >0</strong--><br>คะแนนสะสม</div>
									</div>
			</div>
		</div>
	</div>
</div>		<div id="Main_Content" class="bg border border-main">
			<div class="margin">
				<div class="container-layout">
	<br>
		<div class="tab" data-toggle="hover">
			<div class="tab-head">
				<ul class="tab-nav ">
					<li class="active"><a href="#tab-0">เกม</a></li>
					<!--<li class="margin-left"><a href="#tab-1">แอปพลิเคชันพักผ่อน</a></li><li class="margin-left"><a href="#tab-2">แอปพลิเคชันสิทธิพิเศษ</a></li>-->
					</ul>
			</div>
			<div class="tab-body">
				<div class="tab-panel active" id="tab-0">
										<div class="line-small ">
										<div class="x4">
										<div class="pro radius-big  border border-dashed border-small " >
										<div class="wait"><img src="/WebRes/img/gcds-dragon-512.png" /></a>
										<div class="margin-big-top">
										<a onclick="OpenApp('%7B%22Url%22%3A%22%5C%2Fapp%5C%2FGame%5C%2Findex.php%22%2C%22title%22%3A%22%5Cu5f00%5Cu59cb%5Cu6e38%5Cu620f%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22800%22%2C%22W_h%22%3A%22480%22%2C%22fn%22%3A%22%22%7D');" class="button button-block  button-big bg-main");" class="button button-block  button-big bg-main">เริ่มเกม</a>
										</div>
										</div>
										</div>
										</div>
										<div class="x4">
										<div class="pro radius-big  border border-dashed border-small " >
										<div class="wait"><img src="/WebRes/img/gift_256x256.png" />
										<div class="margin-big-top">
										<a onclick="OpenApp('%7B%22Url%22%3A%22%5C%2Fapp%5C%2Fdh%5C%2Findex.php%22%2C%22title%22%3A%22%5Cu5145%5Cu503c%5Cu5151%5Cu6362%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22600%22%2C%22W_h%22%3A%22600%22%2C%22fn%22%3A%22%22%7D');" class="button button-block  button-big bg-main">เติมเงินและแลกเปลี่ยน</a>
										</div>
										</div>
										</div>
										</div>
										<div class="x4">
										<div class="pro radius-big  border border-dashed border-small " >
										<div class="wait"><img src="/WebRes/img/shop256.png" />
										<div class="margin-big-top">
										<a onclick="OpenApp('%7B%22%55%72%6c%22%3A%22%5C%2F%57%65%62%52%65%73%5C%2F%64%68%5C%2F%69%6e%64%65%78%2E%70%68%70%22%2C%22%74%69%74%6c%65%22%3A%22%5C%75%35%31%34%35%5C%75%35%30%33%63%5C%75%35%31%35%31%5C%75%36%33%36%32%22%2C%22%49%73%5F%4d%61%78%22%3A%22%30%22%2C%22%57%5F%77%22%3A%22%36%30%30%22%2C%22%57%5F%68%22%3A%22%36%30%30%22%2C%22%66%6e%22%3A%22%22%7D');" class="button button-block  button-big bg-main">ร้านค้าเว็บ</a>
										</div>
										</div>
										</div>
										</div>
										<div class="x4">
										<div class="pro radius-big  border border-dashed border-small " >
										<div class="wait"><img src="/WebRes/img/disney_dax_pink_19.png" />
										<div class="margin-big-top">
										<a onclick="OpenApp('%7B%22%55%72%6c%22%3A%22%5C%2F%45%4e%54%5C%2F%7a%70%5C%2F%69%6e%64%65%78%2E%70%68%70%22%2C%22%74%69%74%6c%65%22%3A%22%5C%75%36%45%33%38%5C%75%34%45%35%30%5C%75%35%37%33%41%22%2C%22%49%73%5F%4d%61%78%22%3A%22%30%22%2C%22%57%5F%77%22%3A%22%35%35%30%22%2C%22%57%5F%68%22%3A%22%36%30%30%22%2C%22%66%6e%22%3A%22%22%7D');" class="button button-block  button-big bg-main">สนามเด็กเล่น</a>
										</div>
										</div>
										</div>
										</div>
										<div class="x4">
										<div class="pro radius-big  border border-dashed border-small " >
										<div class="wait"><img src="/WebRes/img/receptionist.png" />
										<div class="margin-big-top">
										<a onclick="OpenApp('%7B%22Url%22%3A%22http%3A%5C/%5C/wpa.qq.com/msgrd%3Fv%3D3%26uin%3D2795412786%26site%3Dqq%26menu%3Dyes%22%2C%22title%22%3A%22%5Cu5ba2%5Cu670dQQ%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22500%22%2C%22W_h%22%3A%22650%22%2C%22fn%22%3A%22%22%7D');" class="button button-block  button-big bg-main">บริการลูกค้า</a></div></div></div></div>
										<div class="x4" style="display:none"><div class="pro radius-big  border border-dashed border-small " ><div class="wait"><img src="/WebRes/img/gcds-firecracker-512.png" /><div class="margin-big-top"><a href="http://shang.qq.com/wpa/qunwpa?idkey=550d81b45568475cea711295824a0a89d48091b6777c55419c6e68a1edbda314" target="_blank" class="button button-block button-big bg-main">Blog</a></div></div></div></div></div></div><div class="tab-panel" id="tab-1"><div class="line-small ">
										<div class="x4"><div class="pro radius-big  border border-dashed border-small " ><div class="wait"><img src="/WebRes/img/music.png" /><div class="margin-big-top"><a onclick="OpenApp('%7B%22Url%22%3A%22http%3A%5C/%5C/douban.fm%5C/partner%5C/baidu%5C/doubanradio%22%2C%22title%22%3A%22%5Cu5728%5Cu7ebf%5Cu7535%5Cu53f0%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22480%22%2C%22W_h%22%3A%22260%22%2C%22fn%22%3A%22%22%7D');" class="button button-block  button-big bg-main">สถานีวิทยุออนไลน์</a></div></div></div></div><div class="x4"><div class="pro radius-big  border border-dashed border-small " ><div class="wait"><img src="/WebRes/img/cinema.png" /><div class="margin-big-top"><a onclick="OpenApp('%7B%22Url%22%3A%22http%3A%5C%2F%5C%2Fwww.qiyi.com%5C%2Fmini%5C%2Fqplus.html%22%2C%22title%22%3A%22%5Cu5728%5Cu7ebf%5Cu89c6%5Cu9891%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22980%22%2C%22W_h%22%3A%22680%22%2C%22fn%22%3A%22%22%7D');" class="button button-block  button-big bg-main">วิดีโอออนไลน์</a></div></div></div></div><div class="x4"><div class="pro radius-big  border border-dashed border-small " ><div class="wait"><img src="/WebRes/img/tv.png" /><div class="margin-big-top"><a onclick="OpenApp('%7B%22Url%22%3A%22http%3A%5C/%5C/app.aplus.pptv.com%5C/tgapp%5C/baidu%5C/live%5C/main%22%2C%22title%22%3A%22%5Cu5728%5Cu7ebfTV%5Cu76f4%5Cu64ad%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22802%22%2C%22W_h%22%3A%22575%22%2C%22fn%22%3A%22%22%7D');" class="button button-block  button-big bg-main">ทีวีออนไลน์</a></div></div></div></div><div class="x4"><div class="pro radius-big  border border-dashed border-small " ><div class="wait"><img src="/WebRes/img/mv.png" /><div class="margin-big-top"><a onclick="OpenApp('%7B%22Url%22%3A%22http%3A%5C/%5C/www.yinyuetai.com%5C/baidu%5C/hao123%22%2C%22title%22%3A%22%5Cu5728%5Cu7ebf%5Cu97f3%5Cu60a6%5Cu53f0%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22747%22%2C%22W_h%22%3A%22550%22%2C%22fn%22%3A%22%22%7D');" class="button button-block  button-big bg-main">สถานีเพลงออนไลน์</a></div></div></div></div><div class="x4"><div class="pro radius-big  border border-dashed border-small " ><div class="wait"><img src="/WebRes/img/rili.png" /><div class="margin-big-top"><a onclick="OpenApp('%7B%22Url%22%3A%22http%3A%5C/%5C/www.365rili.com%5C/360wnl%5C/wnl.html%22%2C%22title%22%3A%22%5Cu5728%5Cu7ebf%5Cu65e5%5Cu5386%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22752%22%2C%22W_h%22%3A%22630%22%2C%22fn%22%3A%22%22%7D');" class="button button-block  button-big bg-main">ปฏิทินออนไลน์</a></div></div></div></div></div></div><div class="tab-panel" id="tab-2"><div class="line-small "></div></div>			</div>
		</div>
</div>			</div>	
		</div>
	</div>

<div class="bg-inverse" id="footer">
		<div class="navbar">
			<div class="navbar-body nav-navicon" id="navbar-footer">
				<div class="navbar-text"><?php echo $WebTitle?>&nbsp;&nbsp;<script src="http://s4.cnzz.com/stat.php?id=1254799960&web_id=1254799960" language="JavaScript"></script></div>
			</div>
		</div>
</div>

<!--http://rm.sina.com.cn/wm/VZ2010050511043310440VK/music/MUSIC1005051622027270.mp3--> 
</body>
</html>
