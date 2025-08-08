<?php
	if($_POST){
		if (isUpdate('toolCompareFileXMLS')) {
			if (isset($_POST['runToolXMLS'])){
				runningToolXMLS();
				//fileItemConfig();
			}
		}
	}
    function runningToolXMLS() {
		fileAbyssArmy();
		fileAbyssCopy();
		fileAbyssEnemyanchor();
		fileAbyssQuestion();
		fileAbyssRoom();
		fileAbyssTrigger();
		fileAchieve();
		fileAchievementParentType();
		fileAchievementSubType();
		fileActiveStatistics();
		fileActiveDegree();
		fileActivity();
		fileAffix();
		fileAllblueDarkgoldequip();
		fileArenaContent();
		fileArmy();
		fileArtificer();
		fileAutoAtk();
		fileBattleTalk();
		fileBlackpearlReward();
		fileBlackMarket();
		fileBlood();
		fileBloodPonits();
		fileBoat();
		fileBoatBattle();
		fileBoatBattleArmy();
		fileBoatBattleCustoms();
		fileBattleHelp();
		fileBoatBattleHero();
		fileBoatBattleRecruit();
		fileBounty();
		fileBountyOperation();
		fileBuffer();
		fileCabinUpgradeCost();
		fileCamp();
		fileCaptainRoom();
		fileCardContent();
		fileCardguessReward();
		fileCardguessSeries();
		fileCardHero();
		fileCardName();
		fileCardSet();
		fileChapter();
		fileChrismasWelfare();
		fileCollectionWords();
		fileConquestContent();
		fileCopy();
		fileCopyTeam();
		fileCruiseAnswer();
		fileCruiseContent();
		fileCruiseMap();
		fileCrystalContent();
		fileCrystalReward();
		fileCyberPort();
		fileCyberRes();
		fileDaimonappleFuse();
		fileDayTask();
		fileDigActive();
		fileDish();
		fileDomineer();
		fileDomineerScene();
		fileElementContent();
		fileElitecopy();
		fileElvesFunction();
		fileEmblem();
		fileErnieReward();
		fileExit();
		fileExploreAccum();
		fileExploreIntro();
		fileFormation();
		fileFoundation();
		fileGemupContent();
		fileGenius();
		fileGeniusChatTips();
		fileGeniusSkill();
		fileGoodWillTypeItem();
		fileGroupWorship();
		fileGuaguale();
		fileGuide();
		fileGuildbossContent();
		fileGuildBossSkill();
		fileGuildBossTeam();
		fileGuildconquestContent();
		fileGuildScience();
		fileGuildShop();
		fileHaoganExp();
		fileHefuActivity();
		fileHeroCopy();
		fileHeroes();
		fileHerosTalent();
		fileHorseContent();
		fileHorseDecorationItem();
		fileHorseDecorationSuit();
		fileHuodongJifen();
		fileItemConfig();
		fileJieriHuodong();
		fileJiuGuan();
		fileJuhun();
		fileKingPeakContent();
		fileKitchen();
		fileLevelUpExp();
		fileLuckyPointer();
		fileLuckyPointerReward();
		fileMail();
		fileMonsterTemlCopyNeedNot();
		fileMountainBattleCondition();
		fileMountsRiding();
		fileMountsReward();
		fileMountsRiding2();
		fileNpc();
		fileNpcBoat();
		fileNpcPirate();
		fileNpcRes();
		fileObtActivity();
		fileOnlineRewardLib();
		fileOpenPrize();
		filePalace();
		filePalaceBig();
		filePayAgainReward();
		filepayAgainRewardPlus();
		filePet();
		filePetAttachedIllustrations();
		filePetAttachedIllustrationsContent();
		filePetChatTips();
		filePetProperty();
		filePetIllustrations();
		filePetIllustrationsContent();
		filePetRoom();
		filePetSkill();
		filePirateFlag();
		filePointBack();
		filePort();
		filePrisonBig();
		filePrisonSmall();
		filePrize();
		fileQuestion();
		fileQuestions();
		fileRandomBossTeam();
		fileRedlineBattleCondition();
		fileRes();
		fileRole();
		fileRoleModelConfig();
		fileRoomConfig();
		fileScienceCost();
		fileSciTech();
		fileScratch();
		fileSeaSoulHelp();
		fileSellers();
		fileSkill();
		fileStarattri();
		fileStarExp();
		fileStarfish();
		fileStars();
		fileStrongWorld();
		fileStrongWorldShop();
		fileStLvCost();
		fileSummerOnlinePrize();
		fileTalksEvent();
		fileTanSuo();
		fileTask();
		fileTaskActivity();
		fileTeamConquestContent();
		fileTehuishop();
		fileTeleport();
		fileTempMsgContent();
		fileThorBattleCondition();
		fileTiaojiaoShijian();
		fileTishishezhi();
		fileTitle();
		fileTopLimit();
		fileTown();
		fileTrainVassalProject();
		fileTrayDower();
		fileTreasure();
		fileTreasureSuit();
		fileTreechatTips();
		fileTreeContent();
		fileTuituArmy();
		fileTuituBig();
		fileTuituHero();
		fileValuableBook();
		fileVipchongzhi();
		fileVipDesc();
		fileWarFlag();
		fileWinStreak();
		fileWinStreakArena();
		fileWorldBoat();
		fileWorldBoatWarContent();
		fileWorldBoatWinStreak();
		fileWorldBoss();
		fileWorldNeptune();
		fileWorldres();
		fileWorldResConfig();
		fileWorship();
		fileXiaofeiLeiji();
		fileZhuanzhibiao();
	}
	function fileAbyssArmy(){
    	$fileName = "abyss_army.xml";
    	$dataResult = compareFileTool($fileName, "abyss_army", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileAbyssCopy(){
    	$fileName = "abyss_copy.xml";
    	$dataResult = compareFileTool($fileName, "abyss_copy", "id", array("name", "bossName", "bossInfo"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileAbyssEnemyanchor(){
    	$fileName = "abyss_enemyanchor.xml";
    	$dataResult = compareFileTool($fileName, "abyss_enemyanchor", "id", array("armyName", "armyTalk1", "armyTalk2", "armyTalk3", "victoryInfo", ), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileAbyssQuestion(){
    	$fileName = "abyss_question.xml";
    	$dataResult = compareFileTool($fileName, "abyss_question", "id", array("name", "content", "question1Content", "question2Content", "question3Content"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileAbyssRoom(){
    	$fileName = "abyss_room.xml";
    	$dataResult = compareFileTool($fileName, "abyss_room", "id", array("roomName", "winInfo"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileAbyssTrigger(){
    	$fileName = "abyss_trigger.xml";
    	$dataResult = compareFileTool($fileName, "abyss_trigger", "id", array("name", "talk1", "talk2", "talk3"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileAchieve(){
    	$fileName = "achieve.xml";
    	$dataResult = compareFileTool($fileName, "achieve", "id", array("name", "des"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileAchievementParentType(){
    	$fileName = "achievementParentType.xml";
    	$dataResult = compareFileTool($fileName, "achievementParentType", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileAchievementSubType(){
    	$fileName = "achievementSubType.xml";
    	$dataResult = compareFileTool($fileName, "achievementSubType", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileActiveStatistics(){
    	$fileName = "ActiveStatistics.xml";
    	$dataResult = compareFileTool($fileName, "ActiveStatistics", "id", array("TitleText", "buttonText"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileActiveDegree(){
    	$fileName = "active_degree.xml";
    	$dataResult = compareFileTool($fileName, "active_degree", "ID", array("TitleText", "buttonText"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileActivity(){
    	$fileName = "activity.xml";
    	$dataResult = compareFileActivityTool($fileName, "activity", "id", array("name", "timeInfo", "description"), 2);
    	if ($dataResult != "") {
    		writeFileXMLCustomName($fileName, $dataResult, "activities");
			showStatus($fileName, true);
    	}
	}
	function fileAffix(){
    	$fileName = "affix.xml";
    	$dataResult = compareFileTool($fileName, "affix", "id", array("print", "propertyName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileAllblueDarkgoldequip(){
    	$fileName = "allblue_darkgoldequip.xml";
    	$dataResult = compareFileTool($fileName, "allblue_darkgoldequip", "id", array("name", "tips"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileArenaContent(){
    	$fileName = "arena_content.xml";
		copyAllFileVI($fileName);
	}
	function fileArmy(){
    	$fileName = "army.xml";
    	$dataResult = compareFileTool($fileName, "army", "id", array("name", "info", "vitorCondition", "itemDrop"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileArtificer(){
    	$fileName = "artificer.xml";
    	$dataResult = compareFileTool($fileName, "artificer", "artisonId", array("artisonName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileAutoAtk(){
    	$fileName = "auto_atk.xml";
    	$dataResult = compareFileTool($fileName, "auto_atk", "armyId", array("armyName", "itemDesc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBattleTalk(){
    	$fileName = "battleTalk.xml";
		$dataResult = compareFileBattleTalkTool($fileName);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBlackpearlReward(){
    	$fileName = "blackpearl_reward.xml";
    	$dataResult = compareFileTool($fileName, "blackpearl_reward", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBlackMarket(){
    	$fileName = "black_market.xml";
    	$dataResult = compareFileTool($fileName, "black_market", "id", array("description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBlood(){
    	$fileName = "blood.xml";
    	$dataResult = compareFileTool($fileName, "blood", "teamId", array("info", "name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBloodPonits(){
    	$fileName = "blood_ponits.xml";
    	$dataResult = compareFileTool($fileName, "blood_ponits", "id", array("prewords_monsterA", "prewords_monsterB", "prewords_monsterC", "failwords_monsterA", "failwords_monsterB", "failwords_monsterC", "successwords_monsterA", "successwords_monsterB", "successwords_monsterC", "prewords_captain"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBoat(){
    	$fileName = "boat.xml";
    	$dataResult = compareFileTool($fileName, "boat", "id", array("boatName", "boatDesc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBoatBattle(){
    	$fileName = "boatBattle.xml";
		unachievableFile($fileName);
	}
	function fileBoatBattleArmy(){
    	$fileName = "boatBattle_army.xml";
    	$dataResult = compareFileTool($fileName, "boatBattle_army", "id", array("modelName", "name", "info", "speak1", "speak2", "speak3"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBoatBattleCustoms(){
    	$fileName = "boatBattle_customs.xml";
    	$dataResult = compareFileTool($fileName, "boatBattle_customs", "id", array("passInfo", "eliteInfo", "ourspeak1", "ourspeak2", "ourspeak3", "ourspeak4", "ourspeak5", "name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBattleHelp(){
    	$fileName = "boatBattle_help.xml";
		copyAllFileVI($fileName);
	}
	function fileBoatBattleHero(){
    	$fileName = "boatBattle_hero.xml";
    	$dataResult = compareFileTool($fileName, "boatBattle_hero", "htid", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBoatBattleRecruit(){
    	$fileName = "boatBattle_recruit.xml";
    	$dataResult = compareFileTool($fileName, "boatBattle_recruit", "htid", array("dialog"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBounty(){
    	$fileName = "bounty.xml";
    	$dataResult = compareFileTool($fileName, "bounty", "id", array("timeinfo"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBountyOperation(){
    	$fileName = "bounty_operation.xml";
    	$dataResult = compareFileTool($fileName, "bounty_operation", "id", array("operation"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileBuffer(){
    	$fileName = "buffer.xml";
    	$dataResult = compareFileTool($fileName, "buffer", "id", array("name", "des"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCabinUpgradeCost(){
    	$fileName = "cabinUpgradeCost.xml";
    	$dataResult = compareFileTool($fileName, "cabinUpgradeCost", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCamp(){
    	$fileName = "camp.xml";
    	$dataResult = compareFileTool($fileName, "camp", "id", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCaptainRoom(){
    	$fileName = "captain_room.xml";
    	$dataResult = compareFileTool($fileName, "captain_room", "id", array("name", "info"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCardContent(){
    	$fileName = "cardContent.xml";
		copyAllFileVI($fileName);
	}
	function fileCardguessReward(){
    	$fileName = "cardguess_reward.xml";
    	$dataResult = compareFileTool($fileName, "cardguess_reward", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCardguessSeries(){
    	$fileName = "cardguess_series.xml";
    	$dataResult = compareFileTool($fileName, "cardguess_series", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCardHero(){
    	$fileName = "card_hero.xml";
    	$dataResult = compareFileTool($fileName, "card_hero", "htid", array("tname", "name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCardName(){
    	$fileName = "card_name.xml";
    	$dataResult = compareFileTool($fileName, "card_name", "id", array("maleName", "femaleName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCardSet(){
    	$fileName = "card_set.xml";
    	$dataResult = compareFileTool($fileName, "card_set", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileChapter(){
    	$fileName = "chapter.xml";
    	$dataResult = compareFileTool($fileName, "item", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileChrismasWelfare(){
    	$fileName = "chrismasWelfare.xml";
    	$dataResult = compareFileTool($fileName, "chrismasWelfare", "type", array("name", "description", "timeinfo"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCollectionWords(){
    	$fileName = "collection_words.xml";
    	$dataResult = compareFileTool($fileName, "collection_words", "id", array("Reward_tab", "descTitle", "descBody", "descFooter"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileConquestContent(){
    	$fileName = "conquestContent.xml";
		copyAllFileVI($fileName);
	}
	function fileCopy(){
    	$fileName = "copy.xml";
    	$dataResult = compareFileTool($fileName, "copy", "id", array("name", "bossName", "bossInfo"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCopyTeam(){
    	$fileName = "copy_team.xml";
    	$dataResult = compareFileTool($fileName, "copy_team", "id", array("tName", "name", "des", "victoryConditionsDes"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCruiseAnswer(){
    	$fileName = "cruiseAnswer.xml";
    	$dataResult = compareFileTool($fileName, "cruiseAnswer", "id", array("name", "content", "question1Content", "question2Content", "question3Content"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCruiseContent(){
    	$fileName = "cruiseContent.xml";
		copyAllFileVI($fileName);
	}
	function fileCruiseMap(){
    	$fileName = "cruiseMap.xml";
    	$dataResult = compareFileTool($fileName, "cruiseMap", "id", array("name", "info"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCrystalContent(){
    	$fileName = "crystalContent.xml";
		copyAllFileVI($fileName);
	}
	function fileCrystalReward(){
    	$fileName = "crystal_reward.xml";
    	$dataResult = compareFileTool($fileName, "crystal_reward", "id", array("crystalName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCyberPort(){
    	$fileName = "cyberPort.xml";
    	$dataResult = compareFileTool($fileName, "port", "portId", array("portName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileCyberRes(){
    	$fileName = "cyberRes.xml";
    	$dataResult = compareFileTool($fileName, "res", "id", array("name", "resName1", "resName2", "resName3", "resName4", "resName5", "resName6", "resName7", "resName8", "resName9", "resName10"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileDaimonappleFuse(){
    	$fileName = "daimonapple_fuse.xml";
    	$dataResult = compareFileTool($fileName, "daimonapple_fuse", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileDayTask(){
    	$fileName = "daytask.xml";
    	$dataResult = compareFileTool($fileName, "daytask", "taskid", array("taskname", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileDigActive(){
    	$fileName = "dig_active.xml";
    	$dataResult = compareFileTool($fileName, "dig_active", "id", array("info1", "info2", "iconname"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileDish(){
    	$fileName = "dish.xml";
    	$dataResult = compareFileTool($fileName, "dish", "id", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileDomineer(){
    	$fileName = "domineer.xml";
		unachievableFile($fileName);
	}
	function fileDomineerScene(){
    	$fileName = "domineer_scene.xml";
    	$dataResult = compareFileTool($fileName, "domineer_scene", "id", array("name", "description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileElementContent(){
    	$fileName = "elementContent.xml";
		unachievableFile($fileName);
	}
	function fileElitecopy(){
    	$fileName = "elitecopy.xml";
    	$dataResult = compareFileTool($fileName, "elitecopy", "id", array("name", "info"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileElvesFunction(){
    	$fileName = "elves_function.xml";
    	$dataResult = compareFileTool($fileName, "elves_function", "id", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileEmblem(){
    	$fileName = "emblem.xml";
    	$dataResult = compareFileTool($fileName, "emblem", "id", array("emname", "ename", "description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileErnieReward(){
    	$fileName = "ernie_reward.xml";
    	$dataResult = compareFileTool($fileName, "ernie_reward", "id", array("info"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileExit(){
    	$fileName = "exit.xml";
    	$dataResult = compareFileTool($fileName, "exit", "id", array("tips"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileExploreAccum(){
    	$fileName = "exploreAccum.xml";
    	$dataResult = compareFileTool($fileName, "accumItem", "id", array("accumItemName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileExploreIntro(){
    	$fileName = "exploreIntro.xml";
		unachievableFile($fileName);
	}
	function fileFormation(){
    	$fileName = "formation.xml";
    	$dataResult = compareFileTool($fileName, "formation", "id", array("name", "description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileFoundation(){
    	$fileName = "foundation.xml";
    	$dataResult = compareFileTool($fileName, "foundation", "id", array("info", "name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileGemupContent(){
    	$fileName = "gemupContent.xml";
		copyAllFileVI($fileName);
	}
	function fileGenius(){
    	$fileName = "genius.xml";
    	$dataResult = compareFileTool($fileName, "genius", "id", array("name", "sort", "description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileGeniusChatTips(){
    	$fileName = "genius_chatTips.xml";
		copyAllFileVI($fileName);
	}
	function fileGeniusSkill(){
    	$fileName = "genius_skill.xml";
    	$dataResult = compareFileTool($fileName, "genius_skill", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileGoodWillTypeItem(){
    	$fileName = "goodwilltypeitem.xml";
    	$dataResult = compareFileTool($fileName, "goodwilltypeitem", "id", array("name", "des"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileGroupWorship(){
    	$fileName = "groupWorship.xml";
    	$dataResult = compareFileTool($fileName, "groupWorship", "id", array("defaultComment", "name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileGuaguale(){
    	$fileName = "guaguale.xml";
    	$dataResult = compareFileTool($fileName, "guaguale", "id", array("help", "A_rule", "B_rule"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileGuide(){
    	$fileName = "guide.xml";
		unachievableFile($fileName);
	}
	function fileGuildbossContent(){
    	$fileName = "guildbossContent.xml";
		copyAllFileVI($fileName);
	}
	function fileGuildBossSkill(){
    	$fileName = "guildboss_skill.xml";
    	$dataResult = compareFileTool($fileName, "guildboss_skill", "id", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileGuildBossTeam(){
    	$fileName = "guildboss_team.xml";
    	$dataResult = compareFileTool($fileName, "guildboss_team", "monstersquadID", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileGuildconquestContent(){
    	$fileName = "guildconquestContent.xml";
		copyAllFileVI($fileName);
	}
	function fileGuildScience(){
    	$fileName = "guildScience.xml";
    	$dataResult = compareFileTool($fileName, "guildScience", "id", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileGuildShop(){
    	$fileName = "guild_shop.xml";
		unachievableFile($fileName);
	}
	function fileHaoganExp(){
    	$fileName = "haogan_exp.xml";
    	$dataResult = compareFileTool($fileName, "haogan_exp", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileHefuActivity(){
    	$fileName = "hefu_activity.xml";
    	$dataResult = compareFileTool($fileName, "hefu_activity", "ID", array("name", "info1", "info2", "rewardInfo"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileHeroCopy(){
    	$fileName = "herocopy.xml";
    	$dataResult = compareFileTool($fileName, "herocopy", "id", array("hcopyName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileHeroes(){
    	$fileName = "heroes.xml";
    	$dataResult = compareFileTool($fileName, "heroes", "htid", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileHerosTalent(){
    	$fileName = "heros_talent.xml";
    	$dataResult = compareFileTool($fileName, "heros_talent", "id", array("name", "skillDepict", "promotion1Dis", "promotion2Dis", "promotionSkill1Dis", "preview"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileHorseContent(){
    	$fileName = "HorseContent.xml";
		copyAllFileVI($fileName);
	}
	function fileHorseDecorationItem(){
    	$fileName = "HorseDecorationItem.xml";
    	$dataResult = compareFileTool($fileName, "HorseDecorationItem", "id", array("name", "type", "description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileHorseDecorationSuit(){
    	$fileName = "HorseDecorationSuit.xml";
    	$dataResult = compareFileTool($fileName, "HorseDecorationSuit", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileHuodongJifen(){
    	$fileName = "huodong_jifen.xml";
    	$dataResult = compareFileTool($fileName, "huodong_jifen", "id", array("opration"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileItemConfig(){
    	$fileName = "itemconfig.xml";
    	$dataResult = compareFileItemConfigTool($fileName);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileJieriHuodong(){
    	$fileName = "jierihuodong.xml";
		unachievableFile($fileName);
	}
	function fileJiuGuan(){
    	$fileName = "jiu_guan.xml";
    	$dataResult = compareFileTool($fileName, "jiu_guan", "htid", array("dialog"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileJuhun(){
    	$fileName = "juhun.xml";
    	$dataResult = compareFileTool($fileName, "juhun", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileKingPeakContent(){
    	$fileName = "kingPeak_content.xml";
		copyAllFileVI($fileName);
	}
	function fileKitchen(){
    	$fileName = "kitchen.xml";
    	$dataResult = compareFileTool($fileName, "kitchen", "id", array("name", "info"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileLevelUpExp(){
    	$fileName = "level_up_exp.xml";
    	$dataResult = compareFileTool($fileName, "level_up_exp", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileLuckyPointer(){
    	$fileName = "LuckyPointer.xml";
		unachievableFile($fileName);
	}
	function fileLuckyPointerReward(){
    	$fileName = "LuckyPointer_reward.xml";
    	$dataResult = compareFileTool($fileName, "LuckyPointer_reward", "id", array("prizeshowtext"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileMail(){
    	$fileName = "mail.xml";
    	$dataResult = compareFileTool($fileName, "mail", "id", array("title"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileMonsterTemlCopyNeedNot(){
    	$fileName = "monster_teml_copyNeedNot.xml";
    	$dataResult = compareFileTool($fileName, "monsters_tmpl", "htid", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileMountainBattleCondition(){
    	$fileName = "mountain_battle_condition.xml";
    	$dataResult = compareFileTool($fileName, "mountain_battle_condition", "id", array("describe"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileMountsRiding(){
    	$fileName = "mountsRiding.xml";
    	$dataResult = compareFileTool($fileName, "mountsRiding", "id", array("description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileMountsReward(){
    	$fileName = "mounts_Reward.xml";
    	$dataResult = compareFileTool($fileName, "mounts_Reward", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileMountsRiding2(){
    	$fileName = "mounts_riding.xml";
    	$dataResult = compareFileTool($fileName, "mounts_riding", "level", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileNpc(){
    	$fileName = "npc.xml";
    	$dataResult = compareFileTool($fileName, "npc", "id", array("npcTemplate", "npcName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileNpcBoat(){
    	$fileName = "npcboat.xml";
    	$dataResult = compareFileTool($fileName, "npcboat", "id", array("npc_name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileNpcPirate(){
    	$fileName = "npc_pirate.xml";
    	$dataResult = compareFileTool($fileName, "npc_pirate", "npcID", array("npcName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileNpcRes(){
    	$fileName = "npc_res.xml";
		unachievableFile($fileName);
	}
	function fileObtActivity(){
    	$fileName = "obtActivity.xml";
    	$dataResult = compareFileTool($fileName, "obtActivity", "id", array("name", "timeinfo", "limitinfo", "ruleinfo"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileOnlineRewardLib(){
    	$fileName = "online_reward_lib.xml";
    	$dataResult = compareFileTool($fileName, "online_reward_lib", "id", array("desc", "prizeInfo_1", "prizeInfo_2", "prizeInfo_3", "prizeInfo_4", "prizeInfo_5", "prizeInfo_6", "prizeInfo_7"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileOpenPrize(){
    	$fileName = "open_prize.xml";
    	$dataResult = compareFileTool($fileName, "open_prize", "id", array("name", "description", "content", "require"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePalace(){
    	$fileName = "palace.xml";
    	$dataResult = compareFileTool($fileName, "palace", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePalaceBig(){
    	$fileName = "palaceBig.xml";
    	$dataResult = compareFileTool($fileName, "palaceBig", "id", array("palaceName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePayAgainReward(){
    	$fileName = "payAgainreward.xml";
		unachievableFile($fileName);
	}
	function filepayAgainRewardPlus(){
    	$fileName = "payAgainrewardPlus.xml";
		unachievableFile($fileName);
	}
	function filePet(){
    	$fileName = "pet.xml";
    	$dataResult = compareFileTool($fileName, "pet", "id", array("name", "description", "petHome"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePetAttachedIllustrations(){
    	$fileName = "petattached_illustrations.xml";
		unachievableFile($fileName);
	}
	function filePetAttachedIllustrationsContent(){
    	$fileName = "petattached_illustrations_Content.xml";
		unachievableFile($fileName);
	}
	function filePetChatTips(){
    	$fileName = "petchatTips.xml";
		copyAllFileVI($fileName);
	}
	function filePetProperty(){
    	$fileName = "petProperty.xml";
    	$dataResult = compareFileTool($fileName, "pet", "tid", array("name", "description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePetIllustrations(){
    	$fileName = "pet_illustrations.xml";
		unachievableFile($fileName);
	}
	function filePetIllustrationsContent(){
    	$fileName = "pet_illustrations_Content.xml";
		unachievableFile($fileName);
	}
	function filePetRoom(){
    	$fileName = "pet_room.xml";
    	$dataResult = compareFileTool($fileName, "pet_room", "id", array("name", "info"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePetSkill(){
    	$fileName = "pet_skill.xml";
    	$dataResult = compareFileTool($fileName, "pet_skill", "id", array("name", "description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePirateFlag(){
    	$fileName = "pirate_flag.xml";
    	$dataResult = compareFileTool($fileName, "pirate_flag", "id", array("name", "description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePointBack(){
    	$fileName = "point_back.xml";
    	$dataResult = compareFileTool($fileName, "point_back", "id", array("activity", "help"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePort(){
    	$fileName = "port.xml";
    	$dataResult = compareFileTool($fileName, "port", "portId", array("portName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePrisonBig(){
    	$fileName = "prison_big.xml";
    	$dataResult = compareFileTool($fileName, "prison_big", "id", array("name", "des"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePrisonSmall(){
    	$fileName = "prison_small.xml";
    	$dataResult = compareFileTool($fileName, "prison_small", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function filePrize(){
    	$fileName = "prize.xml";
    	$dataResult = compareFileTool($fileName, "prize", "prizeId", array("prizeTempName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileQuestion(){
    	$fileName = "question.xml";
    	$dataResult = compareFileTool($fileName, "question", "id", array("name", "info", "answer1", "answer2", "answer3", "answer4"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileQuestions(){
    	$fileName = "questions.xml";
    	$dataResult = compareFileTool($fileName, "questions", "id", array("question", "answerA", "answerB", "answerC"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileRandomBossTeam(){
    	$fileName = "randombossteam.xml";
    	$dataResult = compareFileTool($fileName, "randombossteam", "monstersquadID", array("name", "info"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileRedlineBattleCondition(){
    	$fileName = "redline_battle_condition.xml";
    	$dataResult = compareFileTool($fileName, "redline_battle_condition", "id", array("describe"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileRes(){
    	$fileName = "res.xml";
    	$dataResult = compareFileTool($fileName, "res", "id", array("name", "resName1", "resName2", "resName3", "resName4", "resName5", "resName6", "resName7", "resName8", "resName9", "resName10", "resName11", "resName12", "resName13", "resName14", "resName15", "resName16"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileRole(){
    	$fileName = "role.xml";
    	$dataResult = compareFileTool($fileName, "role", "id", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXMLCustomName($fileName, $dataResult, "roles");
			showStatus($fileName, true);
    	}
	}
	function fileRoleModelConfig(){
    	$fileName = "roleModelConfig.xml";
    	$dataResult = compareFileTool($fileName, "roleModelConfig", "id", array("roleName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileRoomConfig(){
    	$fileName = "roomconfig.xml";
		unachievableFile($fileName);
	}
	function fileScienceCost(){
    	$fileName = "scienceCost.xml";
    	$dataResult = compareFileTool($fileName, "item", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileSciTech(){
    	$fileName = "sci_tech.xml";
    	$dataResult = compareFileTool($fileName, "sci_tech", "id", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileScratch(){
    	$fileName = "Scratch.xml";
    	$dataResult = compareFileTool($fileName, "Scratch", "id", array("description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileSeaSoulHelp(){
    	$fileName = "seaSoulhelp.xml";
		copyAllFileVI($fileName);
	}
	function fileSellers(){
    	$fileName = "sellers.xml";
    	$dataResult = compareFileTool($fileName, "sellers", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileSkill(){
    	$fileName = "skill.xml";
    	$dataResult = compareFileTool($fileName, "skill", "id", array("name", "des", "class", "skillTip"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileStarattri(){
    	$fileName = "starattri.xml";
    	$dataResult = compareFileTool($fileName, "starattri", "ID", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileStarExp(){
    	$fileName = "starexp.xml";
    	$dataResult = compareFileTool($fileName, "starexp", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileStarfish(){
    	$fileName = "starfish.xml";
    	$dataResult = compareFileTool($fileName, "starfish", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileStars(){
    	$fileName = "stars.xml";
    	$dataResult = compareFileTool($fileName, "stars", "astId", array("tempName", "astName", "astInfo"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileStrongWorld(){
    	$fileName = "strongWorld.xml";
		unachievableFile($fileName);
	}
	function fileStrongWorldShop(){
    	$fileName = "strongWorld_shop.xml";
		unachievableFile($fileName);
	}
	function fileStLvCost(){
    	$fileName = "st_lv_cost.xml";
    	$dataResult = compareFileTool($fileName, "st_lv_cost", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileSummerOnlinePrize(){
    	$fileName = "summer_online_prize.xml";
		unachievableFile($fileName);
	}
	function fileTalksEvent(){
    	$fileName = "talks_event.xml";
    	$dataResult = compareFileTool($fileName, "talks_event", "id", array("t_name", "detail"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTanSuo(){
    	$fileName = "tan_suo.xml";
    	$dataResult = compareFileTool($fileName, "tan_suo", "id", array("exploreName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTask(){
    	$fileName = "task.xml";
    	$dataResult = compareFileTool($fileName, "task", "taskId", array("taskName", "fullDesc", "tips", "track_NoAccept", "track_accept", "track_execute", "track_finish"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTaskActivity(){
    	$fileName = "task_activity.xml";
    	$dataResult = compareFileTool($fileName, "task_activity", "id", array("instructions"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTeamConquestContent(){
    	$fileName = "teamConquestContent.xml";
		copyAllFileVI($fileName);
	}
	function fileTehuishop(){
    	$fileName = "tehuishop.xml";
		unachievableFile($fileName);
	}
	function fileTeleport(){
    	$fileName = "teleport.xml";
    	$dataResult = compareFileTool($fileName, "item", "id", array("name", "data"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTempMsgContent(){
    	$fileName = "temp_msg_content.xml";
    	$dataResult = compareFileTool($fileName, "temp_msg_content", "id", array("temp_content"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileThorBattleCondition(){
    	$fileName = "thor_battle_condition.xml";
    	$dataResult = compareFileTool($fileName, "thor_battle_condition", "id", array("describe"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTiaojiaoShijian(){
    	$fileName = "tiaojiao_shijian.xml";
    	$dataResult = compareFileTool($fileName, "tiaojiao_shijian", "projectID", array("projectName", "projectInfo"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTishishezhi(){
    	$fileName = "tishishezhi.xml";
    	$dataResult = compareFileTool($fileName, "tishishezhi", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTitle(){
    	$fileName = "title.xml";
    	$dataResult = compareFileTool($fileName, "title", "titleID", array("titleName", "titleDes", "unget_desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTopLimit(){
    	$fileName = "top_limit.xml";
    	$dataResult = compareFileTool($fileName, "top_limit", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTown(){
    	$fileName = "town.xml";
    	$dataResult = compareFileTool($fileName, "town", "townId", array("townName", "conditionDesc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTrainVassalProject(){
    	$fileName = "trainVassalProject.xml";
    	$dataResult = compareFileTool($fileName, "item", "projectID", array("projectName", "projectInfo"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTrayDower(){
    	$fileName = "trayDower.xml";
    	$dataResult = compareFileTool($fileName, "trayDower", "giftastID", array("name", "info", "buttontips"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTreasure(){
    	$fileName = "treasure.xml";
    	$dataResult = compareFileTool($fileName, "treasure", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTreasureSuit(){
    	$fileName = "treasureSuit.xml";
    	$dataResult = compareFileTool($fileName, "treasureSuit", "id", array("name", "description"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTreechatTips(){
    	$fileName = "treechatTips.xml";
		copyAllFileVI($fileName);
	}
	function fileTreeContent(){
    	$fileName = "treeContent.xml";
		copyAllFileVI($fileName);
	}
	function fileTuituArmy(){
    	$fileName = "tuitu_army.xml";
    	$dataResult = compareFileTool($fileName, "tuitu_army", "armyId", array("name", "info"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTuituBig(){
    	$fileName = "tuitu_big.xml";
    	$dataResult = compareFileTool($fileName, "tuitu_big", "index", array("name", "des"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileTuituHero(){
    	$fileName = "tuitu_hero.xml";
    	$dataResult = compareFileTool($fileName, "tuitu_hero", "htid", array("name", "desc"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileValuableBook(){
    	$fileName = "valuablebook.xml";
    	$dataResult = compareFileTool($fileName, "valuablebook", "nodeId", array("openTips", "descText"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileVipchongzhi(){
    	$fileName = "vipchongzhi.xml";
		unachievableFile($fileName);
	}
	function fileVipDesc(){
    	$fileName = "vipDesc.xml";
    	$dataResult = compareFileTool($fileName, "vip", "level", array("desc"), 2);
    	if ($dataResult != "") {
    		writeFileXMLCustomName($fileName, $dataResult, "vipDesc");
			showStatus($fileName, true);
    	}
	}
	function fileWarFlag(){
    	$fileName = "warFlag.xml";
		unachievableFile($fileName);
	}
	function fileWinStreak(){
    	$fileName = "win_streak.xml";
    	$dataResult = compareFileTool($fileName, "win_streak", "id", array("winStreak", "winStreakEnd"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileWinStreakArena(){
    	$fileName = "win_streak_arena.xml";
    	$dataResult = compareFileTool($fileName, "win_streak", "id", array("winStreak", "winStreakEnd"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileWorldBoat(){
    	$fileName = "worldBoat.xml";
    	$dataResult = compareFileTool($fileName, "worldBoat", "boatLevel", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileWorldBoatWarContent(){
    	$fileName = "worldBoatWar_Content.xml";
		unachievableFile($fileName);
	}
	function fileWorldBoatWinStreak(){
    	$fileName = "worldBoat_win_streak.xml";
    	$dataResult = compareFileTool($fileName, "win_streak", "id", array("winStreak", "winStreakEnd"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileWorldBoss(){
    	$fileName = "worldboss.xml";
    	$dataResult = compareFileTool($fileName, "worldboss", "id", array("name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileWorldNeptune(){
    	$fileName = "worldNeptune.xml";
    	$dataResult = compareFileTool($fileName, "worldNeptune", "ID", array("name", "effectDesc", "neppositionY", "popotxt"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileWorldres(){
    	$fileName = "worldres.xml";
    	$dataResult = compareFileTool($fileName, "worldres", "resId", array("resName", "desc", "campName"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileWorldResConfig(){
    	$fileName = "worldResConfig.xml";
		unachievableFile($fileName);
	}
	function fileWorship(){
    	$fileName = "worship.xml";
    	$dataResult = compareFileTool($fileName, "worship", "id", array("defaultComment", "name"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function fileXiaofeiLeiji(){
    	$fileName = "xiaofei_leiji.xml";
		unachievableFile($fileName);
	}
	function fileZhuanzhibiao(){
    	$fileName = "zhuanzhibiao.xml";
    	$dataResult = compareFileZhuanzhibiaoTool($fileName, "zhuanzhibiao", array("transfer_title"), 2);
    	if ($dataResult != "") {
    		writeFileXML($fileName, $dataResult);
			showStatus($fileName, true);
    	}
	}
	function compareFileTool($fileName, $object, $keyCheck, $listTextReplace, $numberTab) {
		$filePathCN = "./config/toolCompareXmls/XMLS-CN/".$fileName;
		$filePathVI = "./config/toolCompareXmls/XMLS-VI/".$fileName;
		if (!file_exists($filePathCN) || !file_exists($filePathVI)){
			return "";
		}
		$listObjectXmlCN = getJsonFromFileXML($filePathCN);
		$listObjectXmlCN = $listObjectXmlCN[$object];

		$listObjectXmlVI = getJsonFromFileXML($filePathVI);
		$listObjectXmlVI = $listObjectXmlVI[$object];

		$listObjectXmlVIArray = array();
		foreach($listObjectXmlVI as $dataVI) {
			if (array_key_exists('@attributes', $dataVI)) {
				$detailVI = $dataVI['@attributes'];
			} else {
				$detailVI = $dataVI;
			}
			$keyVI = $detailVI[$keyCheck];
			$listObjectXmlVIArray[$keyVI] = $detailVI;
		}

		$dataResult = "";
		foreach($listObjectXmlCN as $dataCN) {
			if (array_key_exists('@attributes', $dataCN)) {
				$detailCN = $dataCN['@attributes'];
			} else {
				$detailCN = $dataCN;
			}
			$keyCN = $detailCN[$keyCheck];
			if (array_key_exists($keyCN, $listObjectXmlVIArray)) {
				$detailVI = $listObjectXmlVIArray[$keyCN];
				foreach ($listTextReplace as $keyReplace) {
					$detailCN[$keyReplace] = $detailVI[$keyReplace];
				}
			}
			$detailString = convertArrayToString($detailCN);
			$tab = "";
			for ($indexTab = 0; $indexTab < $numberTab; $indexTab++) { 
				$tab .= " ";
			}
			$detailString = $tab."<".$object." ".$detailString."/>"."\n";
			$dataResult .= $detailString;
		}
		return $dataResult;
	}
	function compareFileActivityTool($fileName, $object, $keyCheck, $listTextReplace, $numberTab) {
		$filePathCN = "./config/toolCompareXmls/XMLS-CN/".$fileName;
		$filePathVI = "./config/toolCompareXmls/XMLS-VI/".$fileName;
		if (!file_exists($filePathCN) || !file_exists($filePathVI)){
			return "";
		}
		$listObjectXmlCN = getJsonFromFileXML($filePathCN);
		$listObjectXmlCN = $listObjectXmlCN[$object];

		$listObjectXmlVI = getJsonFromFileXML($filePathVI);
		$listObjectXmlVI = $listObjectXmlVI[$object];

		$listObjectXmlVIArray = array();
		foreach($listObjectXmlVI as $detailActivityVI) {
			$detailActivityConfigVI = $detailActivityVI['config'];
			$detailActivityTimeIntervalVI = $detailActivityVI['timeInterval'];
			$detailVI = array();
			
			if (array_key_exists('@attributes', $detailActivityConfigVI)) {
				$detailVI['config'] = $detailActivityConfigVI['@attributes'];
			} else {
				$detailVI['config'] = $detailActivityConfigVI;
			}
			if (array_key_exists('@attributes', $detailActivityTimeIntervalVI)) {
				$detailVI['timeInterval'] = $detailActivityTimeIntervalVI['@attributes'];
			} else {
				$detailVI['timeInterval'] = $detailActivityTimeIntervalVI;
			}
			$detailVIConfig = $detailVI['config'];
			$keyVI = $detailVIConfig[$keyCheck];
			$listObjectXmlVIArray[$keyVI] = $detailVI;
		}

		$dataResult = "";
		foreach($listObjectXmlCN as $detailActivityCN) {
			$detailActivityConfigCN = $detailActivityCN['config'];
			$detailActivityTimeIntervalCN = $detailActivityCN['timeInterval'];
			$detailCN = array();
			
			if (array_key_exists('@attributes', $detailActivityConfigCN)) {
				$detailCN['config'] = $detailActivityConfigCN['@attributes'];
			} else {
				$detailCN['config'] = $detailActivityConfigCN;
			}
			if (array_key_exists('@attributes', $detailActivityTimeIntervalCN)) {
				$detailCN['timeInterval'] = $detailActivityTimeIntervalCN['@attributes'];
			} else {
				$detailCN['timeInterval'] = $detailActivityTimeIntervalCN;
			}
			
			$detailCNConfig = $detailCN['config'];
			$detailCNTimeInterval = $detailCN['timeInterval'];
			$keyCN = $detailCNConfig[$keyCheck];
			
			if (array_key_exists($keyCN, $listObjectXmlVIArray)) {
				$detailVI = $listObjectXmlVIArray[$keyCN];
				$detailVIConfig = $detailVI['config'];
				$detailVITimeInterval = $detailVI['timeInterval'];
				foreach ($listTextReplace as $keyReplace) {
					$detailCNConfig[$keyReplace] = $detailVIConfig[$keyReplace];
				}
				$detailCNTimeInterval['time'] = $detailVITimeInterval['time'];
			}
			$detailConfigString = convertArrayToString($detailCNConfig);
			$detailTimeIntervalString = convertArrayToString($detailCNTimeInterval);
			$tab = "";
			$halfTab = "";
			for ($indexTab = 0; $indexTab < $numberTab; $indexTab++) { 
				$tab .= " ";
			}
			for ($indexTab = 0; $indexTab < $numberTab; $indexTab+=2) { 
				$halfTab .= " ";
			}
			$detailConfigString = $tab."<config ".$detailConfigString."/>"."\n";
			$detailTimeIntervalString = $tab."<timeInterval ".$detailTimeIntervalString."/>"."\n";
			
			$dataResult .= $halfTab."<".$object.">"."\n";
			$dataResult .= $detailConfigString.$detailTimeIntervalString;
			$dataResult .= $halfTab."</".$object.">"."\n";
		}
		return $dataResult;
	}
	function compareFileZhuanzhibiaoTool($fileName, $object, $listTextReplace, $numberTab) {
		$filePathCN = "./config/toolCompareXmls/XMLS-CN/".$fileName;
		$filePathVI = "./config/toolCompareXmls/XMLS-VI/".$fileName;
		if (!file_exists($filePathCN) || !file_exists($filePathVI)){
			return "";
		}
		$listObjectXmlCN = getJsonFromFileXML($filePathCN);
		$listObjectXmlCN = $listObjectXmlCN[$object];

		$listObjectXmlVI = getJsonFromFileXML($filePathVI);
		$listObjectXmlVI = $listObjectXmlVI[$object];

		$dataResult = "";
		foreach($listObjectXmlCN as $dataCN) {
			if (array_key_exists('@attributes', $dataCN)) {
				$detailCN = $dataCN['@attributes'];
			} else {
				$detailCN = $dataCN;
			}
			$detailVI = findRowInList($listObjectXmlVI, array('htid' => $detailCN['htid'], 'transfer_num' => $detailCN['transfer_num']));
			if ($detailVI != null) {
				foreach ($listTextReplace as $keyReplace) {
					$detailCN[$keyReplace] = $detailVI[$keyReplace];
				}
			}
			$detailString = convertArrayToString($detailCN);
			$tab = "";
			for ($indexTab = 0; $indexTab < $numberTab; $indexTab++) { 
				$tab .= " ";
			}
			$detailString = $tab."<".$object." ".$detailString."/>"."\n";
			$dataResult .= $detailString;
		}
		return $dataResult;
	}
	function compareFileBattleTalkTool($fileName) {
		$tab = "    ";
		$halfTab = "  ";
		$filePathCN = "./config/toolCompareXmls/XMLS-CN/".$fileName;
		$filePathVI = "./config/toolCompareXmls/XMLS-VI/".$fileName;
		if (!file_exists($filePathCN) || !file_exists($filePathVI)){
			return "";
		}
		$listObjectXmlCN = getJsonFromFileXML($filePathCN);

		$listObjectXmlVI = getJsonFromFileXML($filePathVI);

		$dataResult = "";
		foreach($listObjectXmlCN as $keyDataCN => $dataCN) {
			if (array_key_exists($keyDataCN, $listObjectXmlVI)) {
				$dataCN = $listObjectXmlVI[$keyDataCN];
			}
			if (array_key_exists('@attributes', $dataCN)) {
				$attributesCN = $dataCN['@attributes'];
			} else {
				$attributesCN = $dataCN;
			}
			$attributesString = convertArrayToString($attributesCN);
			$attributesString = $halfTab."<".$keyDataCN." ".$attributesString.">"."\n";
			
			if (array_key_exists('word', $dataCN)) {
				$wordCN = $dataCN['word'];
			} else {
				$wordCN = $dataCN;
			}
			$wordString = "";
			if (is_array($wordCN)) {
				foreach($wordCN as $childWordCN) {
					$wordString .= $tab."<word>".$childWordCN."</word>"."\n";
				}
			} else {
				$wordString .= $tab."<word>".$wordCN."</word>"."\n";
			}
			$attributesString .= $wordString;
			$attributesString .= $halfTab."</".$keyDataCN.">"."\n";
			$dataResult .= $attributesString;
		}
		return $dataResult;
	}
	function compareFileItemConfigTool($fileName) {
		$tab = "    ";
		$halfTab = "  ";
		$listTextReplace = array("name", "info", "info_1", "info_2", "info_3", "info_4", "info_5", "info_6", "info_7", "info_8", "info_9", "info_10");
		$keyCheck = "id";
		$filePathCN = "./config/toolCompareXmls/XMLS-CN/".$fileName;
		$filePathVI = "./config/toolCompareXmls/XMLS-VI/".$fileName;
		if (!file_exists($filePathCN) || !file_exists($filePathVI)){
			return "";
		}
		$listObjectXmlCN = getJsonFromFileXML($filePathCN);

		$listObjectXmlVI = getJsonFromFileXML($filePathVI);

		$dataResult = "";
		foreach($listObjectXmlCN as $keyDataCN => $dataCN) {
			if ($keyDataCN == "comment") {
			} else if ($keyDataCN == "items") {
				$attributesString = $halfTab."<".$keyDataCN.">"."\n";
				$detailDataString = "";
				
				if (array_key_exists("items", $listObjectXmlVI)) {
					$dataItemVI = $listObjectXmlVI[$keyDataCN];
				}
				$listObjectXmlVIArray = array();
				foreach($dataItemVI as $childDataVI) {
					foreach($childDataVI as $childDetailVI) {
						if (array_key_exists('@attributes', $childDetailVI)) {
							$detailChildDataVI = $childDetailVI['@attributes'];
						} else {
							$detailChildDataVI = $childDetailVI;
						}
						$keyDetailDataVI = $detailChildDataVI[$keyCheck];
						$listObjectXmlVIArray[$keyDetailDataVI] = $detailChildDataVI;
					}
				}
				foreach($dataCN as $keyChildDataCN => $childDataCN) {
					foreach($childDataCN as $childDetailCN) {
						if (array_key_exists('@attributes', $childDetailCN)) {
							$detailChildDataCN = $childDetailCN['@attributes'];
						} else {
							$detailChildDataCN = $childDetailCN;
						}
						$keyDetailDataCN = $detailChildDataCN[$keyCheck];
						if (array_key_exists($keyDetailDataCN, $listObjectXmlVIArray)) {
							$detailChildDataVI = $listObjectXmlVIArray[$keyDetailDataCN];
							foreach ($listTextReplace as $keyReplace) {
								$detailChildDataCN[$keyReplace] = $detailChildDataVI[$keyReplace];
							}
						}
						$detailChildDataString = convertArrayToString($detailChildDataCN);
						$detailDataString .= $tab."<".$keyChildDataCN." ".$detailChildDataString."/>"."\n";
					}
				}
				$attributesString .= $detailDataString;
				$attributesString .= $halfTab."</".$keyDataCN.">"."\n";
				$dataResult .= $attributesString;
			} else {
				$attributesString = $halfTab."<".$keyDataCN.">"."\n";
				$detailDataString = "";
				foreach($dataCN as $keyChildDataCN => $childDataCN) {
					foreach($childDataCN as $childDetailCN) {
						if (array_key_exists('@attributes', $childDetailCN)) {
							$detailChildDataCN = $childDetailCN['@attributes'];
						} else {
							$detailChildDataCN = $childDetailCN;
						}
						$detailChildDataString = convertArrayToString($detailChildDataCN);
						$detailDataString .= $tab."<".$keyChildDataCN." ".$detailChildDataString."/>"."\n";
					}
				}
				$attributesString .= $detailDataString;
				$attributesString .= $halfTab."</".$keyDataCN.">"."\n";
				$dataResult .= $attributesString;
			}
		}
		return $dataResult;
	}
	function findRowInList($listData, $listWhere) {
		foreach($listData as $data) {
			$isOK = true;
			if (array_key_exists('@attributes', $data)) {
				$detail = $data['@attributes'];
			} else {
				$detail = $data;
			}
			foreach($listWhere as $key => $where) {
				if (array_key_exists($key, $detail)) {
					if ($detail[$key] != $where) {
						$isOK = false;
					}
				} else {
					$isOK = false;
				}
			}
			if($isOK) {
				return $detail;
			}
		}
		return null;
	}
	function copyAllFileVI($fileName) {
		$filePathVI = "./config/toolCompareXmls/XMLS-VI/".$fileName;
		$filePathResult = "./config/toolCompareXmls/XMLS-Result/".$fileName;
		showStatusCopyFile($fileName, copy($filePathVI, $filePathResult));
	}
	function unachievableFile($fileName) {
		showStatusInWeb($fileName, false, "", getLanguageByKey('TOOL_COMPARE_FILE_XMLS_STATUS_UNACHIEVABLE'));
	}
	function convertArrayToString($listData) {
		$dataString = "";
		foreach ($listData as $key => $value) {
			$dataString .= $key."=\"".htmlspecialchars($value, ENT_QUOTES)."\" ";
		}
		if ($dataString != "") {
			return substr($dataString, 0, -1);
		}
		return "";
	}
	function writeFileXML($fileName, $data){
		writeFileXMLCustomName($fileName, $data, "root");
	}
	function writeFileXMLCustomName($fileName, $data, $nameRoot){
		$filePathResult = "./config/toolCompareXmls/XMLS-Result/".$fileName;
		$header = '<?xml version="1.0" encoding="UTF-8"?>';
		$dataResult = $header."\n"."<".$nameRoot.">"."\n";
		$dataResult .= $data;
		$dataResult .= "</".$nameRoot.">";

		//file_put_contents($filePathResult, $dataResult);

		$root = simplexml_load_string($dataResult);
		$root->asXml($filePathResult);
	}
	function showStatus($fileName, $isOK) {
		$statusSuccess = getLanguageByKey('TOOL_COMPARE_FILE_XMLS_STATUS_SUCCESS');
		$statusFail = getLanguageByKey('TOOL_COMPARE_FILE_XMLS_STATUS_FAIL');
		showStatusInWeb($fileName, $isOK, $statusSuccess, $statusFail);
	}
	function showStatusCopyFile($fileName, $isOK) {
		$statusSuccess = getLanguageByKey('TOOL_COMPARE_FILE_XMLS_COPY_STATUS_SUCCESS');
		$statusFail = getLanguageByKey('TOOL_COMPARE_FILE_XMLS_COPY_STATUS_FAIL');
		showStatusInWeb($fileName, $isOK, $statusSuccess, $statusFail);
	}
	function showStatusInWeb($fileName, $isOK, $statusSuccess, $statusFail) {
		if($isOK) {
			$color = 'green';
			$status = $statusSuccess;
		} else {
			$color = 'orange';
			$status = $statusFail;
		}
		echo "<span style='color:{$color}'>";
			echo "<font size='3'><b>{$fileName} -----------------> {$status}</b></font>";
		echo "</span>";
		echo "<br/>";
	}
?>