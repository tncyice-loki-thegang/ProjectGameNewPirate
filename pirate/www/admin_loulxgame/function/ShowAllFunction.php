<?php
	$menuIndexConfig = $GLOBALS['config']['menuIndex'];
	$GLOBALS['behindAdminText'] = $GLOBALS['config']['behindAdminText'];
	showAllFunction($menuIndexConfig);

	function showAllFunction($menuIndexConfig) {
		$basicsFunctionTitle = getLanguageByKey('MAIN_PAGE_BASICS_FUNTION_TITLE');
		$advancesFunctionTitle = getLanguageByKey('MAIN_PAGE_ADVANCES_FUNCTION_TITLE');
		$toolTranslate = getLanguageByKey('MAIN_PAGE_TOOL_TITLE');
		echo "<div class='titleMain'>";
			echo "<div style='width:71%'>";
				echo "<div class='title'>";
					echo "<p>{$basicsFunctionTitle}</p>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
		echo "<div class='btn-group'>";
			showItem("addBeri", $menuIndexConfig['addBeri'], 25);
			showItem("addGold", $menuIndexConfig['addGold'], 25);
			showItem("addExperience", $menuIndexConfig['addExperience'], 25);
			showItem("addPrestige", $menuIndexConfig['addPrestige'], 25);
		echo "</div>";
		echo "<div class='btn-group'>";
			showItem("addSoulBlue", $menuIndexConfig['addSoulBlue'], 25);
			showItem("addSoulPurple", $menuIndexConfig['addSoulPurple'], 25);
			showItem("addSoulGreen", $menuIndexConfig['addSoulGreen'], 25);
			showItem("addHonourPoint", $menuIndexConfig['addHonourPoint'], 25);
		echo "</div>";
		echo "<div class='btn-group'>";
			showItem("addVIP", $menuIndexConfig['addVIP'], 25);
			showItem("addElementScore", $menuIndexConfig['addElementScore'], 25);
			showItem("addCarvedStone", $menuIndexConfig['addCarvedStone'], 25);
			showItem("addSeaSoulNum", $menuIndexConfig['addSeaSoulNum'], 25);
		echo "</div>";
		echo "<div class='btn-group'>";
			showItem("addJewelryElement", $menuIndexConfig['addJewelryElement'], 25);
			showItem("addJewelryEnergy", $menuIndexConfig['addJewelryEnergy'], 25);
			showItem("addAppleFactoryDemonKernel", $menuIndexConfig['addAppleFactoryDemonKernel'], 25);
			showItem("addAppleFactoryAppleExperience", $menuIndexConfig['addAppleFactoryAppleExperience'], 25);
		echo "</div>";
		echo "<div class='btn-group'>";
			showItem("addItemNumberGudaijingshi", $menuIndexConfig['addItemNumberGudaijingshi'], 25);
			showItem("addItemNumberAllBlue", $menuIndexConfig['addItemNumberAllBlue'], 25);
			showItem("addItemGemLv8", $menuIndexConfig['addItemGemLv8'], 25);
			showItem("addNumberStarSmelting", $menuIndexConfig['addNumberStarSmelting'], 25);
		echo "</div>";
		echo "<div class='btn-group'>";
			showItem("addHakiNumber", $menuIndexConfig['addHakiNumber'], 25);
			showItem("addAstrolabeStoneNumber", $menuIndexConfig['addAstrolabeStoneNumber'], 25);
			showItem("addGemExp", $menuIndexConfig['addGemExp'], 25);
			showItem("addNumberStarTreasure", $menuIndexConfig['addNumberStarTreasure'], 25);
		echo "</div>";
		echo "<br/>";
		echo "<div class='btn-group'>";
			showItem("updateLevelAstrolabe", $menuIndexConfig['updateLevelAstrolabe'],50);
			showItem("updateLevelRoomShipTechnology", $menuIndexConfig['updateLevelRoomShipTechnology'],50);
		echo "</div>";
		echo "<br/>";
		echo "<div class='btn-group'>";
			showItem("resetSmelting", $menuIndexConfig['resetSmelting'], 25);
			showItem("resetTreasure", $menuIndexConfig['resetTreasure'], 25);
			showItem("resetElement", $menuIndexConfig['resetElement'], 25);
			showItem("resetGetHaki", $menuIndexConfig['resetGetHaki'], 25);
		echo "</div>";
		echo "<br/>";
		echo "<div class='btn-group'>";
			showItem("addMainHero1Level", $menuIndexConfig['addMainHero1Level'], 50);
			showItem("addPartnerHeroLevel", $menuIndexConfig['addPartnerHeroLevel'], 50);
		echo "</div>";
		echo "<br/>";
		echo "<div class='btn-group'>";
			showItem("sendHeroWithMail", $menuIndexConfig['sendHeroWithMail'], 50);
			showItem("sendItemWithMail", $menuIndexConfig['sendItemWithMail'], 50);
		echo "</div>";
		echo "<br/>";
		echo "<div class='btn-group'>";
			showItem("showDetailAccount", $menuIndexConfig['showDetailAccount'], 100);
		echo "</div>";
#		echo "<div class='titleMain'>";
#			echo "<div style='width:71%'>";
#				echo "<div class='title'>";
#					echo "<p>{$advancesFunctionTitle}</p>";
#				echo "</div>";
#			echo "</div>";
#		echo "</div>";
#		echo "<br/>";
#		echo "<div class='btn-group'>";
#			showItem("gotoCustomSendAllServer", $menuIndexConfig['gotoCustomSendAllServer'], 50);
#			showItem("gotoQueryDatabase", $menuIndexConfig['gotoQueryDatabase'], 50);
#		echo "</div>";
#		echo "<br/>";
#		echo "<div class='btn-group'>";
#			showItem("serverController", $menuIndexConfig['serverController'], 100);
#		echo "</div>";
#		echo "<div class='titleMain'>";
#			echo "<div style='width:71%'>";
#				echo "<div class='title'>";
#					echo "<p>{$toolTranslate}</p>";
#				echo "</div>";
#			echo "</div>";
#		echo "</div>";
#		echo "<br/>";
#		echo "<div class='btn-group'>";
#			showItem("toolCompareFileMainSWF", $menuIndexConfig['toolCompareFileMainSWF'], 50);
#			showItem("toolCompareFileXMLS", $menuIndexConfig['toolCompareFileXMLS'], 50);
#		echo "</div>";  -->
	}
	function showItem($id, $item, $width) {
		$style = "width:".$width."%; background-color: #1D2033";
		$behindAdminText = "";
		if ($item['enable']) {
			if ($item['isAdmin']) {
				$style = "width:".$width."%; background-color: #1D2033";
				$behindAdminText = $GLOBALS['behindAdminText'];
			} else {
				$style = "width:".$width."%";
			}
		} else {
			$style = "width:".$width."%; background-color:gray";
		}
		$titleItem = getLanguageByKey($item['title']);
		echo "<button style='{$style}' type='submit' class='brk-btn' name='{$id}' value='{$id}'>{$titleItem}{$behindAdminText}</button>";
	}
?>