<?php
  include_once "./core/config.php";


?>
<li><a class="button bg-main" href="/"><span class="icon-home"></span><br>หน้าแรก</a></li> 
		<li><a class="button bg-main" href="/admin_loulxgame/index.php" target="_black" ><span class="icon-gamepad"></span><br>แอดมิน</a></li> 
		<?php
		if(!$_SESSION['accountName'] || $_SESSION['accountName']==''){
		?>
					<li><a class="button bg-main" onclick="OpenApp('%7B%22Url%22%3A%22%5C%2Fapp%5C%2Fsys%5C%2Flogin.php%22%2C%22title%22%3A%22%5Cu4f1a%5Cu5458%5Cu767b%5Cu9646%22%2C%22Is_Max%22%3A%220%22%2C%22W_w%22%3A%22480%22%2C%22W_h%22%3A%22350%22%2C%22fn%22%3A%22%22%7D'); "><span class="icon-user"></span><br>สมาชิก</a></li> 
		  <?php
		  }else{
		  ?>
		  <li><a class="button bg-main" href="/member.php"><span class="icon-user"></span><br>สมาชิก</a></li> 
		  <?php }?>
		<li><a class="button bg-main"  href="https://loulxgame.com"  target="_black"><span class="icon-money"></span><br>เติมเงิน</a></li> 