
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
<script src="/WebRes/js/cookie.js"></script>
<script src="/WebRes/Js/respond.js"></script></head>
<body >
<div class="container margin-top">
	<form class="form" id="DHYB">
		<p class="text-yellow text-center"><strong>兑换奖励说明(基础比例:1:1000元宝)</strong></p>
		<p class="text-yellow text-left margin-big-left">普通会员额外奖励:<strong> 0 </strong>元宝  <strong class="text-main"> <=== 你当前的奖励额度</strong><br>青铜会员额外奖励:<strong> 0 </strong>元宝<br>白银会员额外奖励:<strong> 0 </strong>元宝<br>黄金会员额外奖励:<strong> 0 </strong>元宝<br>白金会员额外奖励:<strong> 0 </strong>元宝<br>钻石会员额外奖励:<strong> 0 </strong>元宝<br></p>        <div class="form-group">
            <div class="label text-main"><label for="FQ">选择游戏分区</label></div>
            <div class="field padding-left">
            	<div class="button-group border-main radio">
				<?php  
				include_once "../../WebRes/config.php";
				$sql="select * from $database.server order by fqid desc limit 0,1000";
				$result=mysql_query($sql); 
				while($row=mysql_fetch_array($result))
				{
				?><label class="button margin-top" ><input name="FQ" onclick="Ajax_Get_GameUser('<?php echo $row['fqid']?>');" value="<?php echo $row['fqid']?>"  type="radio" data-validate="radio:请选择游戏分区"> <?php echo $row['name']?></label><?php }?>	
					</div>
            </div>
        </div>
        <div class="form-group">
            <div class="label text-main"><label for="Rid">选择对应分区帐号</label></div>
            <div class="field padding-left">
            	<div class="button-group border-main radio" id="S_GameUser">
	            	
            	</div>
            </div>
        </div>
        <div class="form-group">
            <div class="label"><label for="U_DH_Num">本次兑换金额</label></div>
            <div class="field padding-left">
            	<input type="text" class="input  text-center " id="U_DH_Num" name="U_DH_Num" size="20" data-validate="required:请输入兑换数量!,plusinteger:请输入正整数!,compare#>0:数值须大于0,compare#<=10000000:数值须小于或等于账户余额"/>
            </div>
        </div>	               
        <button class="button bg-main margin-bottom button-block" disabled="disabled" type="button" ID="THTj" onclick="Ajax_DHYB('#DHYB')">确认兑换</button>
		</form>
</div>
<script type="text/javascript">
	function Ajax_DHYB(FormID) {//ajax 兑换元宝
		if (FormYz(FormID)){
			Tjdata=$(FormID).serialize();//序列化表单选项
			DoAjax("ajax.php?Action=DHYB",Tjdata,function (data) {
				if(data.status == 'y'){
					layer.alert(data.info, 1, function(){
					   window.location.reload();
					});	
				}else{
					layer.alert(data.info, 5, '温馨提示!');
				}
			});
		}
	}
	function Ajax_Get_GameUser(FQ) {//ajax 提取分区内角色
		Tjdata={FQ:FQ};//序列化表单选项
		var loadi = layer.load(0); 
		$("#S_GameUser").empty(); 
		DoAjax("ajax.php?Action=Get_GameUser",Tjdata,function (data) {
			
			if(data.status == 'y'){
				$("#S_GameUser").html(data.html);
				$("#THTj").attr("disabled",false); 
				$.getScript("/WebRes/js/pintuer.js");
			}else{
				$("#S_GameUser").html(data.html);
				$("#THTj").attr("disabled",true);  
			}
			layer.close(loadi);
		});
		
	}
</script>
</body>
</html>