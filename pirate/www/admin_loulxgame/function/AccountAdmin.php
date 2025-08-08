<?php
	$GLOBALS['adminAccountUserName'] = "loulxgame";
	$GLOBALS['adminAccountPassword'] = "123456";
	$GLOBALS['adminUserName'] = getDataFromPostKey('adminUserName');
	$GLOBALS['adminPassword'] = getDataFromPostKey('adminPassword');
	showAccountAdmin();

	function showAccountAdmin() {
		$accountAdminTitle = getLanguageByKey('ACCOUNT_ADMIN_TITLE');
		$accountAdminUserName = getLanguageByKey('ACCOUNT_ADMIN_USER_NAME');
		$accountAdminPassword = getLanguageByKey('ACCOUNT_ADMIN_PASSWORD');
		echo "<div class='accountAdmin'>";
			echo "<div style='width:71%'>";
				echo "<div class='title'>";
					echo "<p>{$accountAdminTitle}</p>";
				echo "</div>";
				echo "<div class='adminLoginBottom'>";
					echo "<div class='bgInput'>";
						echo "<input type='text' name='adminUserName' id='adminUserName' value='{$GLOBALS['adminUserName']}' valign='center' placeholder='{$accountAdminUserName}'>";
					echo "</div>";
					echo "<div class='bgInput'>";
						echo "<input type='password' name='adminPassword' id='adminPassword' value='{$GLOBALS['adminPassword']}' valign='center' placeholder='{$accountAdminPassword}'>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}
	function isLoginAdmin() {
		if ($GLOBALS['adminUserName'] == $GLOBALS['adminAccountUserName'] && $GLOBALS['adminPassword'] == $GLOBALS['adminAccountPassword']) {
			return true;
		}
		$accountAdminError = getLanguageByKey('ACCOUNT_ADMIN_ERROR');
		return alert("{$accountAdminError}");
	}
?>