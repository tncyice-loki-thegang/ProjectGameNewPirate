<?php
	if($_POST){
		if (isset($_POST['showDetail'])) {
			if (isUpdate('showDetailAccount')) {
				if (isLogin()) {
					if ($GLOBALS['accID']!=""){
						$GLOBALS['detailAccount'] = array(array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_NAME'), $GLOBALS['accName']));
						getDetailAccount();
					}
				}
			}
		}
	}
	function getDetailAccount(){
		try {
			getTUser();
			getTSoul();
			getTHonourShop();
			getTElementSys();
			getTCruise();
			getTSeaSoul();
			getTJewelry();
			getTAppleFactory();
			getHakiInfo();
			getTAstrolabeStone();
			getNumberStarTreasure();
			getNumberStarSmelting();
			
			showDetailAccount();
		} catch (\PDOException $e) {
			echo $e->getMessage();
		}
	}
	function getTUser(){
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$query = sprintf("SELECT * FROM t_user WHERE uid=%s", $GLOBALS['accID']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$account = $stmt->fetch();
		
		$levelMainHero = getMainHero('level');
		$idMainHero = getMainHero('htid');
		$mainHeroName = "";
		switch ($idMainHero) {
			case 11001:
				$mainHeroName = "Triệu Hồi Sư";
				break;
			case 11002:
				$mainHeroName = "Chiến Sĩ";
				break;
			case 11003:
				$mainHeroName = "Nhạc Sĩ";
				break;
			case 11004:
				$mainHeroName = "Kiếm Sĩ";
				break;
			case 11005:
				$mainHeroName = "Xạ Thủ";
				break;
			case 11006:
				$mainHeroName = "Hoa Tiêu";
				break;
		}
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_MAIN_HERO_NAME'), $mainHeroName));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_LEVEL'), $levelMainHero));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_BERI'), number_format($account['belly_num'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_GOLD'), number_format($account['gold_num'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_EXPERIENCE'), number_format($account['experience_num'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_PRESTIGE'), number_format($account['prestige_num'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_VIP'), $account['vip']));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_GEM_EXP'), $account['gem_exp']));
	}
	function getTSoul(){
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$query = sprintf("SELECT * FROM t_soul WHERE uid=%s", $GLOBALS['accID']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$soul = $stmt->fetch();
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_SOUL_BLUE'), number_format($soul['blue'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_SOUL_PURPLE'), number_format($soul['purple'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_SOUL_GREEN'), number_format($soul['green'])));
	}
	function getTHonourShop(){
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$query = sprintf("SELECT * FROM t_honourshop WHERE uid=%s", $GLOBALS['accID']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$honourShop = $stmt->fetch();
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_HONOUR_POINT'), number_format($honourShop['honour_point'])));
	}
	function getTElementSys(){
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$query = sprintf("SELECT * FROM t_element_sys WHERE uid=%s", $GLOBALS['accID']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$elementSys = $stmt->fetch();
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_ELEMENT_SCORE'), number_format($elementSys['element_score'])));
	}
	function getTCruise(){
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$query = sprintf("SELECT * FROM t_cruise WHERE uid=%s", $GLOBALS['accID']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$cruise = $stmt->fetch();
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_CARVED_STONE'), number_format($cruise['carved_stone'])));
	}
	function getTSeaSoul(){
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$query = sprintf("SELECT * FROM t_seasoul WHERE uid=%s", $GLOBALS['accID']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$seaSoul = $stmt->fetch();
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_SOUL_NUM'), number_format($seaSoul['seasoul_num'])));
	}
	function getTJewelry(){
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$query = sprintf("SELECT * FROM t_jewelry WHERE uid=%s", $GLOBALS['accID']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$jewelry = $stmt->fetch();
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_JEWELRY_ELEMENT'), number_format($jewelry['element'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_JEWELRY_ENERGY'), number_format($jewelry['energy'])));
	}
	function getTAppleFactory(){
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$query = sprintf("SELECT * FROM t_apple_factory WHERE uid=%s", $GLOBALS['accID']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$appleFactory = $stmt->fetch();
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_APPLE_FACTORY_DEMON_KERNEL'), number_format($appleFactory['demon_kernel'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_APPLE_FACTORY_APPLE_EXPERIENCE'), number_format($appleFactory['apple_experience'])));
	}
	function getHakiInfo(){
		$allInfo = HakiDao::get($GLOBALS['accID'], array('va_hakiInfo'));
		$info = $allInfo['va_hakiInfo'];
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_HAKI_ATTACK'), number_format($info['attack'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_HAKI_DEFENSE'), number_format($info['defense'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_HAKI_HP'), number_format($info['hp'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_HAKI_MASTER'), number_format($info['master'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_HAKI_XIULUO'), number_format($info['xiuluo'])));
	}
	function getTAstrolabeStone(){
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$query = sprintf("SELECT * FROM t_astrolabe_stone WHERE uid=%s", $GLOBALS['accID']);
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$astrolabeStone = $stmt->fetch();
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_USER_NUMBER_ASTROLABE_STONE'), number_format($astrolabeStone['stone_num'])));
	}
	function getNumberStarTreasure(){
		$treasureDao = TreasureDao::getByUid($GLOBALS['accID'], array('va_treasure'));
		$vaTreasure = $treasureDao['va_treasure'];
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_TREASURE_RED_STAR'), number_format($vaTreasure['red_score'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_TREASURE_PURPLE_STAR'), number_format($vaTreasure['purple_score'])));
	}
	function getNumberStarSmelting(){
		$smeltingDao = SmeltingDao::getSmeltingInfo($GLOBALS['accID']);
		$vaSmeltInfo = $smeltingDao['va_smelt_info'];
		$integralVaSmeltInfo = $vaSmeltInfo['integral'];
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_SMELTING_RED_STAR'), number_format($integralVaSmeltInfo['red'])));
		array_push($GLOBALS['detailAccount'], array(getLanguageByKey('SHOW_DETAIL_ACCOUNT_SMELTING_PURPLE_STAR'), number_format($integralVaSmeltInfo['purple'])));
	}
	function showDetailAccount(){
		echo "<div class='limiter'>";
			echo "<div class='scroll'>";
				echo "<div class='container-table100'>";
					echo "<div class='wrap-table100'>";
						echo "<div class='table'>";
							foreach ($GLOBALS['detailAccount'] as $detail){
								showRow($detail[0], $detail[1]);
							}
						echo "</div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}
	function showRow($name, $value){
		echo "<div class='row'>";
			echo "<div class='cell' data-title='Name'>";
				echo $name;
			echo "</div>";
			echo "<div class='cell' data-title='Value'>";
				echo $value;
			echo "</div>";
		echo "</div>";
	}
?>