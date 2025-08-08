<?php
	if($_POST){
		if (isset($_POST['goBackBtn'])){
			header("Location: /admin_loulxgame/index.php");
			exit;
		} else if (isset($_POST['sendHeroWithMail'])){
			header("Location: /admin_loulxgame/AddHeroToAccount.php");
			exit;
		} else if (isset($_POST['sendItemWithMail'])){
			header("Location: /admin_loulxgame/AddItemToAccount.php");
			exit;
		} else if (isset($_POST['toolCompareFileXMLS'])){
			header("Location: /admin_loulxgame/ToolCompareFileXMLS.php");
			exit;
		} else if (isset($_POST['toolCompareFileMainSWF'])){
			header("Location: /admin_loulxgame/ToolCompareFileMain.php");
			exit;
		} else if (isset($_POST['serverController'])){
			header("Location: /admin_loulxgame/ServerController.php");
			exit;
		} else if (isset($_POST['showDetailAccount'])){
			header("Location: /admin_loulxgame/ShowDetailAccount.php");
			exit;
		} else if (isset($_POST['gotoQueryDatabase'])){
			header("Location: /admin_loulxgame/QueryDatabase.php");
			exit;
		} else if (isset($_POST['gotoCustomSendAllServer'])){
			header("Location: /admin_loulxgame/CustomSendAllServer.php");
			exit;
		}
	}
?>