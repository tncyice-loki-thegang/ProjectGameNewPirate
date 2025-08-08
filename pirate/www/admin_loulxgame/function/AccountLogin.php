<?php
	$GLOBALS['username'] = getDataFromPostKey('username');
	showAccountLogin();

	function showAccountLogin() {
		$accountLoginUserName = getLanguageByKey('ACCOUNT_LOGIN_USER_NAME');
		echo "<div class='login'>";
			echo "<div style='width:71%'>";
				echo "<div class='lineTop'>";
				echo "</div>";
				echo "<div class='loginBottom'>";
					echo "<div class='bgInput'>";
						echo "<input type='text' name='username' id='username' value='{$GLOBALS['username']}' valign='center' placeholder='{$accountLoginUserName}'>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}
	function isLogin() {
		if ($GLOBALS['username'] == "") {
			alert(getLanguageByKey('ACCOUNT_LOGIN_USER_NAME_ERROR'));
			return false;
		} else {
			if (isExistT9('account', sprintf("name='%s'", $GLOBALS['username'])) > 0) {
				getT9AccID();
				if (isExist('t_user', sprintf("pid='%s'", $GLOBALS['accT9ID'])) > 0) {
					getAccID();
					return true;
				} else {
					alert(getLanguageByKey('ACCOUNT_LOGIN_USER_NAME_EXIST'));
					return false;
				}
			} else {
				alert(getLanguageByKey('ACCOUNT_LOGIN_USER_NAME_NOT_FOUND'));
				return false;
			}
		};
	}
	function getT9AccID() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getT9Connect();
		$query = sprintf("SELECT id FROM account WHERE name='%s'", $GLOBALS['username']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$account = $stmt->fetch();
		$GLOBALS['accT9ID'] = $account['id'];
	}
	function getAccID() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$query = sprintf("SELECT uid, uname FROM t_user WHERE pid='%s'", $GLOBALS['accT9ID']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$account = $stmt->fetch();
		$GLOBALS['accID'] = $account['uid'];
		$GLOBALS['accName'] = $account['uname'];
	}
?>