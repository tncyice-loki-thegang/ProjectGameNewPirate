<!DOCTYPE html>
<html lang="zh-cn">
<head>
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
<script src="/webres/js/cookie.js"></script>
<script src="/WebRes/Js/respond.js"></script></head>
<body>
<div class="container-layout margin-big-top">
    <div class="line  form-x">
        <form id="EditPass">
	        <div class="form-group">
	            <div class="label"><label for="Upass">新&nbsp;&nbsp;密&nbsp;码</label></div>
	            <div class="field field-icon-right">
	                <input type="password" class="input" ID="Upass" name="Upass" placeholder="登录密码" data-validate="required:请填写密码,length#>5:密码长度不低于6位" />
	                <span class="icon icon-key"></span>
	            </div>
	        </div>
	        <div class="form-group">
	            <div class="label"><label for="password">安&nbsp;&nbsp;全&nbsp;码</label></div>
	            <div class="field field-icon-right">
	                <input type="password" class="input" ID="Scode" name="Scode" placeholder="安全码" data-validate="required:请填写安全码,length#>5:安全码长度不低于6位" />
	                <span class="icon icon-key"></span>
	            </div>
	        </div>
	        <!--div class="form-group">
	            <div class="label"><label for="Ycode">验&nbsp;&nbsp;证&nbsp;码</label></div>
	            <div class="field">
	                <input type="text" class="input" ID="Ycode" name="Ycode" placeholder="填写右侧的验证码" data-validate="required:请填写安全码,plusinteger:验证码为数字,length#>3:请输入4位验证码,length#<5:请输入4位验证码" />
	                <img width="80" height="32" class="passcode" id="imgVcode" src="/core/VCode.php?v=1426338336"
	                alt="点击更换" onclick="changeVcode()" />
	        </div>
			</div-->
        </form>
    	<div class="margin-big-top text-center"><button class="button button-block bg-sub text-big" type="button" onclick="Ajax_Action('EditPass')"  >修改密码</button></div>
	
</div>
</body>
</html>