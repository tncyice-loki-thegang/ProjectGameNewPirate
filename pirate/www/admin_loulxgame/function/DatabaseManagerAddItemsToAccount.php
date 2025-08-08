<?php
	$listItems = getJsonFromFileXML("./config/itemconfig.xml");
	if($_POST){
		if (isset($_POST['getAllItems'])) {
			getAllItems($listItems['items']);
		} else if (isset($_POST['getAllItemsFruit'])){
			getAllItemsFruit($listItems['items']);
		} else if (isset($_POST['getAllItemsPetEgg'])){
			getAllItemsPetEgg($listItems['items']);
		} else if (isset($_POST['getAllItemsDress'])){
			getAllItemsDress($listItems['items']);
		} else if (isset($_POST['getAllItemsMounts'])){
			getAllItemsMounts($listItems['items']);
		} else if (isset($_POST['addItem'])) {
			if (isUpdate('sendItemWithMail')) {
				$itemID = trim($_POST['itemID']);
				if ($_POST['itemID']==""){
					alert(getLanguageByKey('ADD_ITEM_TO_ACCOUNT_ERROR_ITEM_ID'));
				} else if (!is_numeric($itemID)){
					alert(getLanguageByKey('ADD_ITEM_TO_ACCOUNT_ERROR_ITEM_ID_NUMBER'));
				} else if (isLogin(true)) {
					if ($GLOBALS['accID']!=""){
						$items = array($itemID); // 70014: 2000 Vang, 
						$stackable = ItemAttr::getItemAttr($itemID, ItemDef::ITEM_ATTR_NAME_STACKABLE);
						$arrItemTpl = array_combine($items, array_fill(0, count($items), $stackable * 5));
						$return = "";
						try {
							$return = MailLogic::sendSysItemMailByTemplate($GLOBALS['accID'], MailConf::DEFAULT_TEMPLATE_ID, MailTiTleMsg::MAIL_ITEM_SENDER, MailContentMsg::MAIL_ITEM_SENDER, $arrItemTpl);
						} catch (Exception $ex){
							echo $ex->getMessge();
						}
						if ($return == ""){
							alert(getLanguageByKey('ADD_ITEM_TO_ACCOUNT_FAIL'));
						} else {
							alert(getLanguageByKey('ADD_ITEM_TO_ACCOUNT_SUCCESS'));
						}
					}
				}
			}
		}	
	}
	function getAllItems($listItems){
		showUpdateInformation($listItems['item'], getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_SHOW_ALL_ITEMS'), false, 0);
	}
	function getAllItemsFruit($listItems){
		showUpdateInformation($listItems['item'], getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_SHOW_ALL_ITEMS_FRUIT'), true, 8);
	}
	function getAllItemsPetEgg($listItems){
		showUpdateInformation($listItems['item'], getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_SHOW_ALL_ITEMS_PET_EGG'), true, 9);
	}
	function getAllItemsDress($listItems){
		showUpdateInformation($listItems['item'], getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_SHOW_ALL_ITEMS_DRESS'), true, 14);
	}
	function getAllItemsMounts($listItems){
		showUpdateInformation($listItems['item'], getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_SHOW_ALL_ITEMS_MOUNTS'), true, 17);
	}
	function showUpdateInformation($listItems, $title, $isFilter, $numberFilter) {
		
		$tableImageTitle = getLanguageByKey('ADD_ITEM_TO_ACCOUNT_TABLE_TITLE_IMAGE');
		$tableIdTitle = getLanguageByKey('ADD_ITEM_TO_ACCOUNT_TABLE_TITLE_ID');
		$tableNameTitle = getLanguageByKey('ADD_ITEM_TO_ACCOUNT_TABLE_TITLE_NAME');
		$tableDesTitle = getLanguageByKey('ADD_ITEM_TO_ACCOUNT_TABLE_TITLE_DES');
		
		echo "<div class='table-users' style='width:95%''>";
			echo "<div class='header'>";
				echo $title;
			echo "</div>";
			echo "<div class='scroll'>";
				echo "<table cellspacing='0'>";
					echo "<tr>";
						echo "<th>{$tableImageTitle}</th>";
						echo "<th>{$tableIdTitle}</th>";
						echo "<th>{$tableNameTitle}</th>";
						echo "<th width='400'>{$tableDesTitle}</th>";
					echo "</tr>";
					foreach ($listItems as $detail){
						$isAdd = true;
						$data = $detail['@attributes'];
						if ($isFilter){
							if ($data['itemClass'] != $numberFilter){
								$isAdd = false;
							} 
						}
						if ($isAdd){
							$resourceConfig = $GLOBALS['config']['resource'];
							$imageResourceLink = $resourceConfig['item'].$data['itemBig'];
							echo "<tr>";
								echo "<td><img src='{$imageResourceLink}' alt='' /></td>";
								echo "<td><b>{$data['id']}</b></td>";
								echo "<td><b>{$data['name']}</b></td>";
								echo "<td>";
									echo "{$data['info']}";
								echo "</td>";
							echo "</tr>";
						}
					}
				echo "</table>";
			echo "</div>";
		echo "</div>";
	}
?>