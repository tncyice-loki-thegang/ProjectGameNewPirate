<?php
	showCustomSendAllServerOption();
	showButton();
	if($_POST){
		if (isset($_POST['sendToAccount'])) {
			if (isLogin(true)) {
				if ($GLOBALS['accID']!=""){
					customSendServer();
				}
			}
		} else if(isset($_POST['sendToAllServer'])) {
			$GLOBALS['accID'] = "";
			customSendServer();
		}
	}
	function customSendServer() {
		$customSendAllServerSelected = getDataFromPostKey("customSendAllServerSelected");
		$customValue = getDataFromPostKey('customValue');
		switch ($customSendAllServerSelected) {
			case "sendGold":
				if ($customValue == "") {
					alert(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_GOLD_ERROR_NOT_INPUT'));
				} else {
					sendGold();
				}
				break;
			case "sendHonourPoint":
				if ($customValue == "") {
					alert(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_HONOUR_POINT_ERROR_NOT_INPUT'));
				} else {
					sendHonourPoint();
				}
				break;
			case "resetSmelting":
				resetSmelting();
				break;
			case "resetTreasure":
				resetTreasure();
				break;
			case "resetElement":
				resetElement();
				break;
			case "resetGetHaki":
				resetGetHaki();
				break;
				
		}
	}
	function sendGold() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$customValue = getDataFromPostKey('customValue');
		if ($GLOBALS['accID']!=""){
			$sql = "UPDATE t_user SET gold_num = gold_num + :customValue WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->bindParam(':customValue', $customValue, PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(sprintf(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_GOLD_TO_ACCOUNT_SUCCESS'), number_format($customValue), $GLOBALS['accName']));
		} else {
			$sql = "UPDATE t_user SET gold_num = gold_num + :customValue";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':customValue', $customValue, PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(sprintf(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_GOLD_TO_SERVER_SUCCESS'), number_format($customValue)));
		}
	}
	function sendHonourPoint() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$customValue = getDataFromPostKey('customValue');
		if ($GLOBALS['accID']!=""){
			$sql = "UPDATE t_honourshop SET honour_point = honour_point + :customValue WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->bindParam(':customValue', $customValue, PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(sprintf(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_HONOUR_POINT_TO_ACCOUNT_SUCCESS'), number_format($customValue), $GLOBALS['accName']));
		} else {
			$sql = "UPDATE t_honourshop SET honour_point = honour_point + :customValue";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':customValue', $customValue, PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(sprintf(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_HONOUR_POINT_TO_SERVER_SUCCESS'), number_format($customValue)));
		}
	}
    function resetSmelting() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		
		if ($GLOBALS['accID']!=""){
			$sql = "UPDATE t_smelting SET smelt_times = 0 WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(sprintf(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_RESET_SMELTING_TO_ACCOUNT_SUCCESS'), $GLOBALS['accName']));
		} else {
			$sql = "UPDATE t_smelting SET smelt_times = 0";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			updateSuccess(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_RESET_SMELTING_TO_SERVER_SUCCESS'));
		}
    }
    function resetTreasure() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('resetTreasure');
		$hunt_aviable_num = $itemMenu['value'];
		
		if ($GLOBALS['accID']!=""){
			$sql = "UPDATE t_treasure SET hunt_aviable_num = :hunt_aviable_num, gold_refresh_num = 0 WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':hunt_aviable_num', $hunt_aviable_num, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(sprintf(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_RESET_TREASURE_TO_ACCOUNT_SUCCESS'), $GLOBALS['accName']));
		} else {
			$sql = "UPDATE t_treasure SET hunt_aviable_num = :hunt_aviable_num, gold_refresh_num = 0";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':hunt_aviable_num', $hunt_aviable_num, PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_RESET_TREASURE_TO_SERVER_SUCCESS'));
		}
    }
    function resetElement() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		
		$playtimes = 3;
		$movetimes = 15;
		$refreshtimes = 5;
		if ($GLOBALS['accID']!=""){
			$sql = "UPDATE t_element_sys SET playtimes = :playtimes, movetimes = :movetimes, refreshtimes = :refreshtimes WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':playtimes', $playtimes, PDO::PARAM_INT);
			$stmt->bindParam(':movetimes', $movetimes, PDO::PARAM_INT);
			$stmt->bindParam(':refreshtimes', $refreshtimes, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(sprintf(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_RESET_ELEMENT_TO_ACCOUNT_SUCCESS'), $GLOBALS['accName']));
		} else {
			$sql = "UPDATE t_element_sys SET playtimes = :playtimes, movetimes = :movetimes, refreshtimes = :refreshtimes";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':playtimes', $playtimes, PDO::PARAM_INT);
			$stmt->bindParam(':movetimes', $movetimes, PDO::PARAM_INT);
			$stmt->bindParam(':refreshtimes', $refreshtimes, PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_RESET_ELEMENT_TO_SERVER_SUCCESS'));
		}
    }
    function resetGetHaki() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		
		$bellyTimes = 255;
		$goldTimes = 0;
		if ($GLOBALS['accID']!=""){
			$sql = "UPDATE t_haki SET bellyTimes = :bellyTimes, goldTimes = :goldTimes WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':bellyTimes', $bellyTimes, PDO::PARAM_INT);
			$stmt->bindParam(':goldTimes', $goldTimes, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(sprintf(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_RESET_GET_HAKI_TO_ACCOUNT_SUCCESS'), $GLOBALS['accName']));
		} else {
			$sql = "UPDATE t_haki SET bellyTimes = :bellyTimes, goldTimes = :goldTimes";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':bellyTimes', $bellyTimes, PDO::PARAM_INT);
			$stmt->bindParam(':goldTimes', $goldTimes, PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(getLanguageByKey('CUSTOM_SEND_ALL_SERVER_RESET_GET_HAKI_TO_SERVER_SUCCESS'));
		}
    }
	function showCustomSendAllServerOption() {
		$customSendAllServerFile = file_get_contents("./config/language-vi/customSendAllServer.json");
		$convert = json_decode($customSendAllServerFile, true);
		$customSendAllServer = $convert['customSendAllServer'];
		
		$chooseFunctionTitle = getLanguageByKey('CUSTOM_SEND_ALL_SERVER_CHOOSE_FUNCTION');
		
		$customSendAllServerSelected = getDataFromPostKey("customSendAllServerSelected");
		if ($customSendAllServerSelected == "") {
			$customSendAllServerSelected = $customSendAllServer[0]['id'];
		}
		echo "<div class='choseCustomFunction'>";
			echo "<div style='width:71%'>";
				echo "<div class='title'>";
					echo "<p>{$chooseFunctionTitle}</p>";
				echo "</div>";
				echo "<div class='choseCustomFunctionBottom'>";
					echo "<div class='scroll' style='width: 95%; height: 95%'>";
						echo "<div style='width: 90%; height: 100%'>";
							for ($index = 0; $index < count($customSendAllServer); $index++) {
								$data = $customSendAllServer[$index];
								if ($data['id'] == $customSendAllServerSelected) {
									$checked = "checked='checked'";
								} else {
									$checked = "";
								}
								echo "<label class='container' style='width:65%'>{$data['title']}";
									echo "<input type='radio' {$checked} style='width:1%' name='customSendAllServerSelected' value='{$data['id']}'>";
									echo "<span class='checkmark'></span>";
								echo "</label>";
							}
						echo "</div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}
	function showButton() {
		$customValuePlaceHolder = getLanguageByKey('CUSTOM_SEND_ALL_SERVER_CUSTOM_VALUE_PLACE_HOLDER');
		$sendToAccountButton = getLanguageByKey('CUSTOM_SEND_ALL_SERVER_TO_ACCOUNT_BUTTON');
		$sendAllServerButton = getLanguageByKey('CUSTOM_SEND_ALL_SERVER_TO_ALL_SERVER_BUTTON');
		echo "<div class='login'>";
			echo "<div style='width:71%'>";
				echo "<div class='lineTop'>";
				echo "</div>";
				echo "<div class='loginBottom'>";
					echo "<div class='bgInput'>";
						$customValue = getDataFromPostKey('customValue');
						echo "<input type='number' name='customValue' id='customValue' value='{$customValue}' valign='center' placeholder='{$customValuePlaceHolder}'>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
		echo "<br/>";
		echo "<div class='btn-group' style='width:95%'>";
			echo "<button style='width:50%' type='submit' class='brk-btn' name='sendToAccount' value='sendToAccount'>{$sendToAccountButton}</button>";
			echo "<button style='width:50%' type='submit' class='brk-btn' name='sendToAllServer' value='sendToAllServer'>{$sendAllServerButton}</button>";
		echo "</div>";
	}
?>