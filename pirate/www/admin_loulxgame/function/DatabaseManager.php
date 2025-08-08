<?php
	if($_POST){
		if (isLogin()) {
			if ($GLOBALS['accID']!=""){
				allowUpdate();
			}
		}
	}
	
	function allowUpdate(){
		try {
			if (isset($_POST['addBeri'])) {
				if (isUpdate('addBeri')) {
					addBeri();
				}
			}
			if (isset($_POST['addGold'])) {
				if (isUpdate('addGold')) {
					addGold();
				}
			}
			if (isset($_POST['addExperience'])) {
				if (isUpdate('addExperience')) {
					addExperience();
				}
			}
			if (isset($_POST['addPrestige'])) {
				if (isUpdate('addPrestige')) {
					addPrestige();
				}
			}
			if (isset($_POST['addSoulBlue'])) {
				if (isUpdate('addSoulBlue')) {
					addSoulBlue();
				}
			}
			if (isset($_POST['addSoulPurple'])) {
				if (isUpdate('addSoulPurple')) {
					addSoulPurple();
				}
			}
			if (isset($_POST['addSoulGreen'])) {
				if (isUpdate('addSoulGreen')) {
					addSoulGreen();
				}
			}
			if (isset($_POST['addHonourPoint'])) {
				if (isUpdate('addHonourPoint')) {
					addHonourPoint();
				}
			}
			if (isset($_POST['addElementScore'])) {
				if (isUpdate('addElementScore')) {
					addElementScore();
				}
			}
			if (isset($_POST['addVIP'])) {
				if (isUpdate('addVIP')) {
					addVIP();
				}
			}
			if (isset($_POST['addCarvedStone'])) {
				if (isUpdate('addCarvedStone')) {
					addCarvedStone();
				}
			}
			if (isset($_POST['addSeaSoulNum'])) {
				if (isUpdate('addSeaSoulNum')) {
					addSeaSoulNum();
				}
			}
			if (isset($_POST['addJewelryElement'])) {
				if (isUpdate('addJewelryElement')) {
					addJewelryElement();
				}
			}
			if (isset($_POST['addJewelryEnergy'])) {
				if (isUpdate('addJewelryEnergy')) {
					addJewelryEnergy();
				}
			}
			if (isset($_POST['addAppleFactoryDemonKernel'])) {
				if (isUpdate('addAppleFactoryDemonKernel')) {
					addAppleFactoryDemonKernel();
				}
			}
			if (isset($_POST['addAppleFactoryAppleExperience'])) {
				if (isUpdate('addAppleFactoryAppleExperience')) {
					addAppleFactoryAppleExperience();
				}
			}
			if (isset($_POST['addItemNumberGudaijingshi'])) {
				if (isUpdate('addItemNumberGudaijingshi')) {
					addItemNumberGudaijingshi();
				}
			}
			if (isset($_POST['addItemNumberAllBlue'])) {
				if (isUpdate('addItemNumberAllBlue')) {
					addItemNumberAllBlue();
				}
			}
			if (isset($_POST['addItemGemLv8'])) {
				if (isUpdate('addItemGemLv8')) {
					addItemGemLv8();
				}
			}
			if (isset($_POST['addHakiNumber'])) {
				if (isUpdate('addHakiNumber')) {
					addHakiNumber();
				}
			}
			if (isset($_POST['addAstrolabeStoneNumber'])) {
				if (isUpdate('addAstrolabeStoneNumber')) {
					addAstrolabeStoneNumber();
				}
			}
			if (isset($_POST['addGemExp'])) {
				if (isUpdate('addGemExp')) {
					addGemExp();
				}
			}
			if (isset($_POST['addNumberStarTreasure'])) {
				if (isUpdate('addNumberStarTreasure')) {
					addNumberStarTreasure();
				}
			}
			if (isset($_POST['addNumberStarSmelting'])) {
				if (isUpdate('addNumberStarSmelting')) {
					addNumberStarSmelting();
				}
			}
			if (isset($_POST['updateLevelAstrolabe'])) {
				if (isUpdate('updateLevelAstrolabe')) {
					updateLevelAstrolabe();
				}
			}
			if (isset($_POST['updateLevelRoomShipTechnology'])) {
				if (isUpdate('updateLevelRoomShipTechnology')) {
					updateLevelRoomShipTechnology();
				}
			}
			if (isset($_POST['resetTreasure'])) {
				if (isUpdate('resetTreasure')) {
					resetTreasure();
				}
			}
			if (isset($_POST['resetElement'])) {
				if (isUpdate('resetElement')) {
					resetElement();
				}
			}
			if (isset($_POST['resetGetHaki'])) {
				if (isUpdate('resetGetHaki')) {
					resetGetHaki();
				}
			}
			if (isset($_POST['addMainHero1Level'])) {
				if (isUpdate('addMainHero1Level')) {
					addMainHero1Level();
				}
			}
			if (isset($_POST['addPartnerHeroLevel'])) {
				if (isUpdate('addPartnerHeroLevel')) {
					addPartnerHeroLevel();
				}
			}
		} catch (\PDOException $e) {
			echo $e->getMessage().' - '.$e->getLine();
		}
	}

    function addBeri() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addBeri');
		$belly_num = $itemMenu['value'];
		
		$sql = "UPDATE t_user SET belly_num = belly_num + :belly_num WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':belly_num', $belly_num, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_BERI'), number_format($itemMenu['value'])));
    }

    function addGold() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addGold');
		$gold_num = $itemMenu['value'];
		
		$sql = "UPDATE t_user SET gold_num = gold_num + :gold_num WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':gold_num', $gold_num, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_GOLD'), number_format($itemMenu['value'])));
    }

    function addExperience() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addExperience');
		$experience_num = $itemMenu['value'];
		
		$sql = "UPDATE t_user SET experience_num = experience_num + :experience_num WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':experience_num', $experience_num, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_EXPERIENE'), number_format($itemMenu['value'])));
    }

    function addPrestige() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addPrestige');
		$prestige_num = $itemMenu['value'];
		
		$sql = "UPDATE t_user SET prestige_num = prestige_num + :prestige_num WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':prestige_num', $prestige_num, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_PRESTIGE'), number_format($itemMenu['value'])));
    }

    function addSoulBlue() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addSoulBlue');
		$blue = $itemMenu['value'];
		
		$sql = "UPDATE t_soul SET blue = blue + :blue WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':blue', $blue, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_SOUL_BLUE'), number_format($itemMenu['value'])));
    }

    function addSoulPurple() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addSoulPurple');
		$purple = $itemMenu['value'];
		
		$sql = "UPDATE t_soul SET purple = purple + :purple WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':purple', $purple, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_SOUL_PURPLE'), number_format($itemMenu['value'])));
    }

    function addSoulGreen() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addSoulGreen');
		$green = $itemMenu['value'];
		
		$sql = "UPDATE t_soul SET green = green + :green WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':green', $green, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_SOUL_GREEN'), number_format($itemMenu['value'])));
    }

    function addHonourPoint() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addHonourPoint');
		$honour_point = $itemMenu['value'];
		
		if (isExist('t_honourshop', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_honourshop SET honour_point = honour_point + :honour_point WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':honour_point', $honour_point, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_HONOUR_POINT'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_HONOUR_POINT_ERROR'));
		}
    }

    function addElementScore() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addElementScore');
		$element_score = $itemMenu['value'];
		
		if (isExist('t_element_sys', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_element_sys SET element_score = element_score + :element_score WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':element_score', $element_score, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_ELEMENT_SCORE'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_RESET_ELEMENT_ERROR'));
		}
    }

    function addVIP() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addVIP');
		$vipLevel = $itemMenu['value'];
		
		$sql = "UPDATE t_user SET vip = :vip WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':vip', $vipLevel, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		updateSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_VIP'), number_format($itemMenu['value'])));
    }

    function addCarvedStone() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addCarvedStone');
		$carved_stone = $itemMenu['value'];
		
		if (isExist('t_cruise', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_cruise SET carved_stone = carved_stone + :carved_stone, free_dice_times = 5, gold_dice_times = 35 WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':carved_stone', $carved_stone, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_CARVED_STONE'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_CARVED_STONE_ERROR'));
		}
    }

    function addSeaSoulNum() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addSeaSoulNum');
		$seasoul_num = $itemMenu['value'];
		
		if (isExist('t_seasoul', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_seasoul SET seasoul_num = seasoul_num + :seasoul_num WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':seasoul_num', $seasoul_num, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_SEA_SOUL_NUM'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_SEA_SOUL_NUM_ERROR'));
		}
    }

    function addJewelryElement() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addJewelryElement');
		$element = $itemMenu['value'];
		
		if (isExist('t_jewelry', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_jewelry SET element = element + :element WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':element', $element, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_JEWELRY_ELEMENT'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_JEWELRY_ERROR'));
		}
    }

    function addJewelryEnergy() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addJewelryEnergy');
		$energy = $itemMenu['value'];
		
		if (isExist('t_jewelry', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_jewelry SET energy = energy + :energy WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':energy', $energy, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_JEWELRY_ENERGY'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_JEWELRY_ERROR'));
		}
    }

    function addAppleFactoryDemonKernel() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addAppleFactoryDemonKernel');
		$demon_kernel = $itemMenu['value'];
		
		$sql = "UPDATE t_apple_factory SET demon_kernel = demon_kernel + :demon_kernel WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':demon_kernel', $demon_kernel, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_APPLE_FACTORY_DEMON_KERNEL'), number_format($itemMenu['value'])));
    }

    function addAppleFactoryAppleExperience() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addAppleFactoryAppleExperience');
		$apple_experience = $itemMenu['value'];
		
		$sql = "UPDATE t_apple_factory SET apple_experience = apple_experience + :apple_experience WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':apple_experience', $apple_experience, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_APPLE_FACTORY_APPLE_EXPERIENCE_FULL'), number_format($itemMenu['value'])));
    }

    function addItemNumberGudaijingshi() {
		$itemID = 120015; // 120015: K.cương cổ đại
		$items = array($itemID);
		$stackable = ItemAttr::getItemAttr($itemID, ItemDef::ITEM_ATTR_NAME_STACKABLE);
		$arrItemTpl = array_combine($items, array_fill(0, count($items), $stackable * 5));
		MailLogic::sendSysItemMailByTemplate($GLOBALS['accID'], MailConf::DEFAULT_TEMPLATE_ID, MailTiTleMsg::MAIL_ITEM_SENDER, MailContentMsg::MAIL_ITEM_SENDER, $arrItemTpl);
		alert(getLanguageByKey('MAIN_PAGE_ITEM_NUMBER_GUDAIJINGSHI_SUCCESS'));
    }

    function addItemNumberAllBlue() {
		$listItemMailPhaLe = array(
							"120101", "120102", "120103", "120104", "120105"
						);
		$listItemMailHaiMa = array(
							"120201", "120202", "120203", "120204", "120205"
						);
		$listItemMailTranChau = array(
							"120301", "120302", "120303", "120304", "120305"
						);
		$listItemMailMaNao = array(
							"120401", "120402", "120403", "120404", "120405"
						);
		$listItemMailSanHo = array(
							"120501", "120502", "120503", "120504", "120505"
						);
		$listItemMailQuangDong = array(
							"121101", "121102", "121103", "121104", "121105"
						);
		$listItemMailQuangSat = array(
							"121201", "121202", "121203", "121204", "121205"
						);
		$listItemMailQuangBac = array(
							"121301", "121302", "121303", "121304", "121305"
						);
		$listItemMailQuangVang = array(
							"121401", "121402", "121403", "121404", "121405"
						);
		$listItemMailDaAnhKim = array(
							"121451", "121452"
						);
		$listItem = array($listItemMailPhaLe, $listItemMailHaiMa, $listItemMailTranChau, $listItemMailMaNao, $listItemMailSanHo, 
						$listItemMailQuangDong, $listItemMailQuangSat, $listItemMailQuangBac, $listItemMailQuangVang, $listItemMailDaAnhKim);
		foreach($listItem as $items) {
			$itemsSendMail = array();
			foreach ($items as $itemID){
				$stackable = ItemAttr::getItemAttr($itemID, ItemDef::ITEM_ATTR_NAME_STACKABLE);
				$itemsSendMail[$itemID] = $stackable;
			}
			MailLogic::sendSysItemMailByTemplate($GLOBALS['accID'], MailConf::DEFAULT_TEMPLATE_ID, MailTiTleMsg::MAIL_ITEM_SENDER, MailContentMsg::MAIL_ITEM_SENDER, $itemsSendMail);
		}
		
		alert(getLanguageByKey('MAIN_PAGE_ITEM_NUMBER_ALL_BLUE_SUCCESS'));
    }

    function addItemGemLv8() {
		$listItemMail1 = array(
							"40071", "40072", "40073", "40074", "40075"
						);
		$listItemMail2 = array(
							"40076", "40077"
						);
		$listItem = array($listItemMail1, $listItemMail2);
		foreach($listItem as $items) {
			$itemsSendMail = array();
			foreach ($items as $itemID){
				$stackable = ItemAttr::getItemAttr($itemID, ItemDef::ITEM_ATTR_NAME_STACKABLE);
				$itemsSendMail[$itemID] = $stackable;
			}
			MailLogic::sendSysItemMailByTemplate($GLOBALS['accID'], MailConf::DEFAULT_TEMPLATE_ID, MailTiTleMsg::MAIL_ITEM_SENDER, MailContentMsg::MAIL_ITEM_SENDER, $itemsSendMail);
		}
		
		alert(getLanguageByKey('MAIN_PAGE_ITEM_GEM_LV_8_SUCCESS'));
    }

    function addHakiNumber() {
		$itemMenu = getItemMenuIndex('addHakiNumber');
		$hakiNumber = $itemMenu['value'];
		
		if (isExist('t_haki', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$dateUpdate = array(0, $hakiNumber, $hakiNumber, $hakiNumber, $hakiNumber, $hakiNumber);
			HakiLogic::updateHakiInfo($GLOBALS['accID'],$dateUpdate);
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_ITEM_NUMBER_HAKI'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_ITEM_NUMBER_HAKI_ERROR'));
		}
    }

    function addAstrolabeStoneNumber() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addAstrolabeStoneNumber');
		$stone = $itemMenu['value'];
		
		if (isExist('t_astrolabe_stone', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_astrolabe_stone SET stone_num = stone_num + :stone_num WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':stone_num', $stone, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_ITEM_NUMBER_ASTROLABE_STONE'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_ITEM_NUMBER_ASTROLABE_STONE_ERROR'));
		}
    }

    function addGemExp() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('addGemExp');
		$gemExpNum = $itemMenu['value'];
		
		$sql = "UPDATE t_user SET gem_exp = gem_exp + :gem_exp WHERE uid = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':gem_exp', $gemExpNum, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_GEM_EXP'), number_format($itemMenu['value'])));
    }

    function addNumberStarTreasure() {
		$itemMenu = getItemMenuIndex('addNumberStarTreasure');
		$starNum = $itemMenu['value'];
		
		if (isExist('t_treasure', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$treasureDao = TreasureDao::getByUid($GLOBALS['accID'], array('va_treasure'));
			$vaTreasure = $treasureDao['va_treasure'];
			$vaTreasure['red_score'] += $starNum;
			$vaTreasure['purple_score'] += $starNum;
			
			$treasureDao['va_treasure'] = $vaTreasure;
			
			TreasureDao::update($GLOBALS['accID'], $treasureDao);
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_NUMBER_STAR_TREASURE'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_RESET_TREASURE_ERROR'));
		}
    }

    function addNumberStarSmelting() {
		$itemMenu = getItemMenuIndex('addNumberStarSmelting');
		$starNum = $itemMenu['value'];
		
		if (isExist('t_smelting', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$smeltingDao = SmeltingDao::getSmeltingInfo($GLOBALS['accID']);
			$vaSmeltInfo = $smeltingDao['va_smelt_info'];
			$integralVaSmeltInfo = $vaSmeltInfo['integral'];
			$integralVaSmeltInfo['red'] += $starNum;
			$integralVaSmeltInfo['purple'] += $starNum;
			
			$vaSmeltInfo['integral'] = $integralVaSmeltInfo;
			$smeltingDao['va_smelt_info'] = $vaSmeltInfo;
			SmeltingDao::updSmeltingInfo($GLOBALS['accID'], $smeltingDao);
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_NUMBER_STAR_SMELTING'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_RESET_SMELTING_ERROR'));
		}
    }

    function updateLevelAstrolabe() {
		$arytraymain=btstore_get()->ASTROLABE_TRAYMAIN;
		$trayids=$arytraymain[count($arytraymain)];
		$consid=intval($trayids[count($trayids) - 1]);
		$expid=intval(btstore_get()->ASTROLABE_STARS[$consid]['astExpId']);
		$allExp = 0;
		for ($index = 1; $index <= $expid; $index++) {
			$aryexp=btstore_get()->ASTROLABE_EXP[intval($index)];
			$ary=$aryexp[1];
			foreach ($ary as $exp => $needlevel){
				$allExp += intval($exp);
			}
		}
		
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		if (isExist('t_astrolabe_stone', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_astrolabe_info SET ast_lev = :ast_lev, all_levlup_exp = :all_levlup_exp WHERE uid = :id AND ast_id = 1";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':ast_lev', $expid, PDO::PARAM_INT);
			$stmt->bindParam(':all_levlup_exp', $allExp, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(getLanguageByKey('MAIN_PAGE_UPDATE_LEVEL_ASTROLABE'));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_ITEM_NUMBER_ASTROLABE_STONE_ERROR'));
		}
    }

    function updateLevelRoomShipTechnology() {
		$boatInfo = SailboatDao::getBoatInfoByUid($GLOBALS['accID'], array('va_boat_info'));
		$levelMainHero = getMainHero('level');
		
		$vaBoatInfo = $boatInfo['va_boat_info'];
		foreach ($vaBoatInfo['cabin_id_lv'] as $keyCabinIdLv => $cabinIdLv){
			$cabinIdLv['level'] = $levelMainHero;
			$vaBoatInfo['cabin_id_lv'][$keyCabinIdLv] = $cabinIdLv;
		}
		$boatInfo['va_boat_info'] = $vaBoatInfo;
		SailboatDao::updateBoatInfo($GLOBALS['accID'], $boatInfo);
		$sciTech = SciTechDao::getStInfo($GLOBALS['accID']);
		if(!empty($sciTech)){
			$sciTechInfo = $sciTech['va_st_info'];
			$sciTechInfoLevel = $sciTechInfo['st_id_lv'];
			foreach ($sciTechInfoLevel as $keySciTechInfoLevelDetail => $sciTechInfoLevelDetail){
				$sciTechInfoLevelDetail['lv'] = $levelMainHero;
				$sciTechInfoLevel[$keySciTechInfoLevelDetail] = $sciTechInfoLevelDetail;
			}
			$sciTechInfo['st_id_lv'] = $sciTechInfoLevel;
			$sciTech['va_st_info'] = $sciTechInfo;
			SciTechDao::updStInfo($GLOBALS['accID'], $sciTech);
		} else {
		}
		updateSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_UPDATE_ROOM_SHIP_TECHNOLOGY'), number_format($levelMainHero)));
    }

    function resetSmelting() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		
		if (isExist('t_smelting', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_smelting SET smelt_times = 0 WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(getLanguageByKey('MAIN_PAGE_RESET_SMELTING'));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_RESET_SMELTING_ERROR'));
		}
    }

    function resetTreasure() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		$itemMenu = getItemMenuIndex('resetTreasure');
		$hunt_aviable_num = $itemMenu['value'];
		
		if (isExist('t_treasure', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_treasure SET hunt_aviable_num = :hunt_aviable_num, gold_refresh_num = 0 WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':hunt_aviable_num', $hunt_aviable_num, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			insertSuccess(sprintf("%s %s.", getLanguageByKey('MAIN_PAGE_RESET_TREASURE'), number_format($itemMenu['value'])));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_RESET_TREASURE_ERROR'));
		}
    }

    function resetElement() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		
		$playtimes = 3;
		$movetimes = 15;
		$refreshtimes = 5;
		if (isExist('t_element_sys', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_element_sys SET playtimes = :playtimes, movetimes = :movetimes, refreshtimes = :refreshtimes WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':playtimes', $playtimes, PDO::PARAM_INT);
			$stmt->bindParam(':movetimes', $movetimes, PDO::PARAM_INT);
			$stmt->bindParam(':refreshtimes', $refreshtimes, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(getLanguageByKey('MAIN_PAGE_RESET_ELEMENT'));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_RESET_ELEMENT_ERROR'));
		}
    }

    function resetGetHaki() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		
		$bellyTimes = 255;
		$goldTimes = 0;
		if (isExist('t_haki', sprintf("uid='%s'", $GLOBALS['accID'])) > 0) {
			$sql = "UPDATE t_haki SET bellyTimes = :bellyTimes, goldTimes = :goldTimes WHERE uid = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':bellyTimes', $bellyTimes, PDO::PARAM_INT);
			$stmt->bindParam(':goldTimes', $goldTimes, PDO::PARAM_INT);
			$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
			$stmt->execute();
			updateSuccess(getLanguageByKey('MAIN_PAGE_RESET_GET_HAKI'));
		} else {
			alert(getLanguageByKey('MAIN_PAGE_RESET_GET_HAKI_ERROR'));
		}
    }

    function addMainHero1Level() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		
		$listLevelMain = getListLevelByID(2);
		$levelMainHero = getMainHero('level');
		$all_exp = 0;
		for ($index = 2; $index <= $levelMainHero + 5; $index++) {
			$key = "lv_".$index;
			$all_exp += $listLevelMain[$key];
		}
		
		$sql = "UPDATE t_hero SET all_exp = :all_exp WHERE uid = :id AND (htid = '11001' OR htid = '11002' OR htid = '11003' OR htid = '11004' OR htid = '11005' OR htid = '11006')";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':all_exp', $all_exp, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		alert(sprintf(getLanguageByKey('MAIN_PAGE_ADD_MAIN_HERO_1_LEVEL_ALERT'), $levelMainHero + 5));
    }

    function addPartnerHeroLevel() {
		$db = DatabaseConnection::getInstance();
		$conn = $db -> getConnect();
		
		$levelMainHero = getMainHero('level');
		$rebirthNum = floor(($levelMainHero - 45) / 5);
		
		$sql = "UPDATE t_hero SET level = :levelMainHero, rebirthNum = :rebirthNum WHERE uid = :id AND (htid NOT IN ('11001', '11002', '11003', '11004', '11005', '11006'))";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':levelMainHero', $levelMainHero, PDO::PARAM_INT);
		$stmt->bindParam(':rebirthNum', $rebirthNum, PDO::PARAM_INT);
		$stmt->bindParam(':id', $GLOBALS['accID'], PDO::PARAM_INT);
		$stmt->execute();
		alert(sprintf(getLanguageByKey('MAIN_PAGE_ADD_PARTNER_HERO_LEVEL_ALERT'), $levelMainHero));
    }
?>