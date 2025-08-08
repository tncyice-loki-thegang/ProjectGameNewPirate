<?php
	$listHeroes = getJsonFromFileXML("./config/heroes.xml");
	if($_POST){
		if (isset($_POST['getAllHeroes'])) {
			getAllHeroes($listHeroes['heroes']);
		} else if (isset($_POST['getAllHeroesHaki'])){
			getAllHeroesHaki($listHeroes['heroes']);
		} else if (isset($_POST['addHero'])) {
			if (isUpdate('sendHeroWithMail')) {
				$heroId = trim($_POST['heroesID']);
				if ($_POST['heroesID']==""){
					alert(getLanguageByKey('ADD_HEROES_TO_ACCOUNT_ERROR_HEROES_ID'));
				} else if (!is_numeric($heroId)){
					alert(getLanguageByKey('ADD_HEROES_TO_ACCOUNT_ERROR_HEROES_ID_NUMBER'));
				} else if (isLogin(true)) {
					if ($GLOBALS['accID']!=""){
						allowUpdateHero($heroId);
					}
				}
			}
		}	
	}
	function getAllHeroes($listHeroes){
		showUpdateInformation($listHeroes, getLanguageByKey('ADD_HEROES_TO_ACCOUNT_BUTTON_SHOW_ALL_HEROES'), false);
	}
	function getAllHeroesHaki($listHeroes){
		showUpdateInformation($listHeroes, getLanguageByKey('ADD_HEROES_TO_ACCOUNT_BUTTON_SHOW_ALL_HEROES_HAKI'), true);
	}
	function allowUpdateHero($heroId){
		/*echo "get ServerProxy"."<br/>";
		$proxy = new ServerProxy();
		$proxy->closeUser($GLOBALS['accID']);
		echo "get closeUser"."<br/>";
		sleep(1);*/
		$user = UserDao::getUserFieldsByUid($GLOBALS['accID'], array('va_user'));
		if (empty($user)){
			alert(getLanguageByKey('ADD_HEROES_TO_ACCOUNT_GET_USER_FAIL'));
		} else {
			$va_user = $user['va_user'];
			
			if (in_array($heroId, $va_user['heroes'])){
				alert(getLanguageByKey('ADD_HEROES_TO_ACCOUNT_EXIST'));
			} else {
				$va_user['heroes'][] = $heroId;
				try {
					UserDao::updateUser($GLOBALS['accID'], array('va_user'=>$va_user));
					alert(getLanguageByKey('ADD_HEROES_TO_ACCOUNT_SUCCESS'));
				} catch (Exception $ex){
					alert(getLanguageByKey('ADD_HEROES_TO_ACCOUNT_FAIL'));
				}
			}
		}
	}
	function showUpdateInformation($listHeroes, $title, $isHaki) {
		$tableImageTitle = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_IMAGE');
		$tableIdTitle = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_ID');
		$tableNameTitle = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_NAME');
		$tableInfoTitle = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO');
		$tableSkillTitle = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_SKILL');
	
		$tableInfoStarLevel = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO_STAR_LEVEL');
		$tableInfoPhyDefend = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO_PHY_DEFEND');
		$tableInfoKillDefend = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO_KILL_DEFEND');
		$tableInfoMgcDefend = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO_MGC_DEFEND');
		$tableInfoPhyFDmgRatio = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO_PHY_F_DMG_RATIO');
		$tableInfoKillFDmgRatio = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO_KILL_F_DMG_RATIO');
		$tableInfoMgcFDmgRatio = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO_MGC_F_DMG_RATIO');
		$tableInfoPhyFEptRatio = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO_PHY_F_EPT_RATIO');
		$tableInfoKillFEptRatio = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO_KILL_F_EPT_RATIO');
		$tableInfoMgcFEptRatio = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_INFO_MGC_F_EPT_RATIO');
		
		$tableSkillNormalAtk = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_SKILL_NORMAL_ATK');
		$tableSkillRageAtkSkill = getLanguageByKey('ADD_HEROES_TO_ACCOUNT_TABLE_TITLE_SKILL_RAGE_ATK_SKILL');
		
		$listSkillFile = getJsonFromFileXML("./config/skill.xml");
		$listSkill = $listSkillFile['skill'];
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
						echo "<th>{$tableInfoTitle}</th>";
						echo "<th width='300'>{$tableSkillTitle}</th>";
					echo "</tr>";
					foreach ($listHeroes as $detail){
						$isAdd = true;
						$data = $detail['@attributes'];
						if ($isHaki){
							if ($data['nameColor'] < 5){
								$isAdd = false;
							} 
						} else {
							if ($data['htid'] != $data['modelId']){
								$isAdd = false;
							}
						}
						if ($isAdd){
							$resourceConfig = $GLOBALS['config']['resource'];
							$imageResourceLink = $resourceConfig['hero'].$data['bigHeadImg'];
							$startLevel = sprintf($tableInfoStarLevel, $data['starLevel']);
							
							$stgRebirthNum = $data['stgRebirth'] / 100;
							$aglRebirthNum = $data['aglRebirth'] / 100;
							$itgRebirthNum = $data['itgRebirth'] / 100;
							$phyFDmgRatio = $data['phyFDmgRatio'] / 10000;
							$killFDmgRatio = $data['killFDmgRatio'] / 10000;
							$mgcFDmgRatio = $data['mgcFDmgRatio'] / 10000;
							$phyFEptRatio = $data['phyFEptRatio'] / 10000;
							$killFEptRatio = $data['killFEptRatio'] / 10000;
							$mgcFEptRatio = $data['mgcFEptRatio'] / 10000;
							
							$skillNormal = findObjectFromArrayWhere($listSkill, 'id', $data['normalAtk']);
							$skillRage = findObjectFromArrayWhere($listSkill, 'id', $data['rageAtkSkill']);
							echo "<tr>";
								echo "<td><img src='{$imageResourceLink}' alt='' /></td>";
								echo "<td><b>{$data['htid']}</b></td>";
								echo "<td><b>{$data['name']}</b></td>";
								echo "<td>";
									echo $startLevel;
									echo "<br/>";
									echo sprintf($tableInfoPhyDefend, $data['phyDefend'], $stgRebirthNum);
									echo "<br/>";
									echo sprintf($tableInfoKillDefend, $data['killDefend'], $aglRebirthNum);
									echo "<br/>";
									echo sprintf($tableInfoMgcDefend, $data['mgcDefend'], $itgRebirthNum);
									echo "<br/>";
									echo $tableInfoPhyFDmgRatio.$phyFDmgRatio;
									echo "<br/>";
									echo $tableInfoKillFDmgRatio.$killFDmgRatio;
									echo "<br/>";
									echo $tableInfoMgcFDmgRatio.$mgcFDmgRatio;
									echo "<br/>";
									echo $tableInfoPhyFEptRatio.$phyFEptRatio;
									echo "<br/>";
									echo $tableInfoKillFEptRatio.$killFEptRatio;
									echo "<br/>";
									echo $tableInfoMgcFEptRatio.$mgcFEptRatio;
								echo "</td>";
								echo "<td>";
									echo "<b>".$tableSkillNormalAtk."</b>";
									echo "<br/>";
									echo $skillNormal['des'];
									echo "<br/>";
									echo "<b>".$tableSkillRageAtkSkill."</b>";
									echo "<br/>";
									echo $skillRage['des'];
								echo "</td>";
							echo "</tr>";
						}
					}
				echo "</table>";
			echo "</div>";
		echo "</div>";
	}
?>