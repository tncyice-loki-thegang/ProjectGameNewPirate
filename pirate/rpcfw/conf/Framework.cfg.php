<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Framework.cfg.php 40678 2013-03-13 03:56:56Z HaidongJia $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Framework.cfg.php $
 * @author $Author: HaidongJia $(hoping@babeltime.com)
 * @date $Date: 2013-03-13 11:56:56 +0800 (三, 2013-03-13) $
 * @version $Revision: 40678 $
 * @brief
 *
 **/

class FrameworkConfig
{

	const MAX_RECURS_LEVEL = 10;

	/**
	 * PHPProxy的最大重试次数
	 * @var int
	 */
	const MAX_RETRY_NUM = 3;

	/**
	 * 是否对响应进行编码
	 * @var bool
	 */
	const ENCODE_RESPONSE = true;

	/**
	 * 最小用户uid
	 * @var int
	 */
	const MIN_UID = 20000;

	/**
	 * 同一时间所允许的误差
	 * @var int
	 */
	const SAME_TIME_OFFSET = 2;

	/**
	 * 用于生成摘要的扰码
	 * @var string
	 */
	const MESS_CODE = 'BabelTime';

	/**
	 * 系统所使用的编码方式
	 * @var string
	 */
	const ENCODING = "utf8";

	/**
	 * 异步方法
	 * @var int
	 */
	const ASYNC_CMD_TPL = '/home/pirate/bin/php /home/pirate/rpcfw/lib/ScriptRunner.php -f /home/pirate/rpcfw/lib/AsyncExecutor.php %s >>/home/pirate/rpcfw/log/popen.log.wf 2>&1 &';

	/**
	 * 本地用于连接phpproxy的地址
	 * @var unknown_type
	 */
	const PHPPROXY_PATH = '/home/pirate/phpproxy/var/phpproxy.sock';

	/**
	 * PPCProxy/PHPProxy的读超时时间
	 * @var int
	 */
	const PROXY_READ_TIMEOUT = 5;

	/**
	 * RPCPRoxy/PHPProxy的写超时时间
	 * @var int
	 */
	const PROXY_WIRTE_TIMEOUT = 1;

	/**
	 * RPCProxy/PHPProxy/HTTPClient的连接超时时间
	 * @var int
	 */
	const PROXY_CONNECT_TIMEOUT = 1;

	/**
	 * HTTPClient业务执行超时时间
	 * @var int
	 */
	const PROXY_EXECUTE_TIMEOUT = 3;

	/**
	 * 压缩上限
	 * @var int
	 */
	const PROXY_COMPRESS_THRESHOLD = 102400;

	/**
	 * 编码参数
	 * @var int
	 */
	const AMF_ENCODE_FLAGS = AMF_ENCODE_FLAGS;

	/**
	 * 解码参数
	 * @var int
	 */
	const AMF_DECODE_FLAGS = AMF_DECODE_FLAGS;

	/**
	 * 最大执行时间
	 * @var int
	 */
	const MAX_EXECUTE_TIME = 2550;

	/**
	 * 最大响应大小
	 * @var int
	 */
	const MAX_RESPONSE_SIZE = 1024000;

	/**
	 * 最大请求大小
	 * @var int
	 */
	const MAX_REQUEST_SIZE = 1024000;

	/**
	 * 四个小时的秒数
	 * @var int
	 */
	const FOUR_HOURS_SECOND = 14400;

	/**
	 * 周一四点的秒数, 类似FOUR_HOURS_SECOND，
	 * @var int
	 */
	const WEEK_SECOND = 14400;

	/**
	 * 每月的1号4点的秒数
	 * @var int
	 */
	const MONTH_SECOND = 14400;

	/**
	 * 日志文件名
	 * @var int
	 */
	const LOG_NAME = 'rpc.log';

	/**
	 * 日志级别
	 * @var int
	 */
	const LOG_LEVEL = Logger::L_INFO;

	/**
	 * 是否开启debug,为调试方便会将某些检查禁掉，线上一定要为false
	 * @var int
	 */
	const DEBUG = true;

	/**
	 * 是否开启覆盖率检查，线上一定为false
	 * @var int
	 */
	const COVERAGE = false;

	/**
	 * 是否将异步执行任务转移到main机器上运行
	 */
	const ASYNC_TASK_ON_MAIN = true;

	/**
	 * 覆盖率报告生成地址
	 * @var string
	 */
	const COVERAGE_ROOT = '/home/pirate/static/coverage';

	/**
	 * 请求前的hook
	 * @var array
	 */
	static $ARR_BEFORE_HOOK = array ('AdjustTrainExp', 'AutoAttackFilter' );

	/**
	 * 请求后的hook
	 * @var array
	 *
	 * @import 请确保将SessionEncode放置在array的最后.
	 */
	static $ARR_AFTER_HOOK = array ('TaskUpdate', 'UnsetBattleInfo', /*请确保将SessionEncode放置在array的最后*/'SessionEncode' );

	/**
	 * 可以排除的不需要用户登陆的命令
	 * @var array
	 */
	static $ARR_EXCLUDE_LOGIN_METHOD = array (
			'user' => array ('userLogin' => true, 'getUsers' => true, 'createUser' => true,
					'cancelDel' => true, 'getRandomName' => true ) );

	/**
	 * 可以排除的不需要连接的命令
	 * @var array
	 */
	static $ARR_EXCLUDE_CONNECT_METHOD = array ('user' => array ('login' => true ),
			'battle' => array ('test' => true, 'getRecord' => true , 'getRecordForWeb' => true),
			'gm' => array ('reportClientError' => true, 'getTime' => true ) );

	/**
	 * 允许不登录的私有方法
	 * @var unknown_type
	 */
	static $ARR_PRIVATE_METHOD = array (
			'timer' => array ('execute' => true ),
			'forge' => array ('refreshReinforceProbability' => true ),
			'user' => array ('clearUser' => true, 'modifyUserByOther' => true,
					'addItemsOtherUser' => true, 'addGold4BBpay' => true, 'extraExecution' => true,
					'setVip4BBpay' => true, 'getArrUserByPid' => true, 'getOrder' => true,
					'getArrOrder' => true, 'getByUname' => true, 'getMultiInfoByPid' => true,
					'getTopEn' => true, 'getByPid' => true, 'ban' => true, 'getBanInfo' => true ),
			'achievements' => array ('excuteNotify' => true ),
			'copy' => array ('groupAttack' => true, 'addGroupEnemyDefeatInfo' => true ),
			'olympic' => array ('__executeFight' => true, '__executeAward' => true,
					'__executeSaveReplay' => true, '__updOtherUserIntegral' => true,
					'__saveSignUpReplay' => true, 'startFinals' => true ),
			'smelting' => array ('refreshArtificer' => true ),
			'guild' => array ('finalReward' => true, 'notifyBanquet' => true,
					'startBanquest' => true, 'update' => true, 'battle' => true, 'doBattle' => true,
					'chanllenge' => true, 'battleReward' => true, 'getTopGuild' => true,
					'doFinalReward' => true ), 'port' => array ('dueResource' => true ),
			'worldResource' => array ('signupEnd' => true, 'battleEnd' => true, 'attackEnd' => true,
					'createBattle' => true, 'chatSignupStart' => true, 'chatBattleStart' => true ),
			'boss' => array ('bossComing' => true, 'bossStart' => true, 'bossEnd' => true,
					'rewardForTimer' => true, 'reward' => true, 'rewardForBotList' => true,
					'rewardForBot' => true ),
			'vassal' => array ('relieveByMstMovePort' => true, 'getTrainBelly' => true ),
			'arena' => array ('arenaDataRefresh' => true ),
			'hero' => array ('modifyHeroByOther' => true ),
			'treasure' => array ('huntReturnTimeout' => true ),
			'gm' => array ('silentUser' => true, 'newResponse' => true, 'newBroadCast' => true,
					'newBroadCastTest' => true, 'sendRankingActivityLevelReward' => true,
					'sendRankingActivityArenaReward' => true,
					'sendRankingActivityPrestigeReward' => true,
					'sendRankingActivityOfferReward' => true,
					'sendRankingActivityCopyReward' => true,
					'sendRankingActivityGuildReward' => true ),
			'reward' => array ('getLastSignTime' => true, 'isSignToday' => true ),
			'daytask' => array ('getCompleteNumToday' => true ),
			'payback' => array ('addPayBackInfo' => true, 'getPayBackInfoByTime' => true,
					'modifyPayBackInfo' => true, 'isPayBackInfoOpen' => true,
					'openPayBackInfo' => true, 'closePayBackInfo' => true ),
			'proxy' => array ('syncExecute' => true, 'asyncExecute' => true,
					'getTotalUserCount' => true, 'closeUser' => true ),
			'allblue' => array ('modifyStealFishInfoByOther' => true,
					'modifyWishFishInfoByOther' => true, 'modifyStealFishTimes' => true,
					'modifyBeWishFishTimes' => true ),
			'honourshop' => array('modifyHonourPoint' => true),
			'groupwar' => array ('fightWin' => true, 'fightLose' => true, 'touchDown' => true,
					'battleEnd' => true, 'rewardOnEnd' => true, 'doRewardOnEnd' => true, 'systemMsg'=>true, 'createBattle'=>true ),
			'treasurenpc' => array( 'trigerNpcBoatInfo' => true , 'broadcastTreasureNpc' => true),
			'worldwar' => array ('__saveBattlePara' => true, '__saveBattleReplay' => true,
								 '__saveLoseTimes' => true, '__executeAward' => true,
								 '__sendWorldWarMsg' => true, '__saveBattleReward' => true,
								 '__sendAuditiondOverMsg' => true),
			'jewelry'=>array('updateEnergyElement'=>true),
			'abysscopy'=>array('dealCard' => true, 'dealDirectlyPassCard' => true, 'directPassFlopCard' => true, 'enterRoom' => true, 'flopCard' => true, 'getAllUser' => true, 'leave' => true, 'onArmy' => true, 'onQuestion' => true, 'onTrigger' => true, 'passCopyReward' => true, 'reDealCard' => true, 'reDealDirectlyPassCard' => true, 'rewardCard' => true, 'start'=>true),
			);

	/**
	 * 对外暴露的接口
	 * @var array
	 */
	static $ARR_PUBLIC_METHOD = array (
			'abysscopy' => array ('buyChallengeNum' => true, 'chat' => true, 'create' => true, 'directlyPassCopy' => true, 'getDirectlyPassInfo' => true, 'getUserInfo' => true, 'join' => true, 'leaveRoom' => true),
			'achievements' => array ('delShowAchievements' => true, 'delShowName' => true, 'fetchPrize' => true, 'fetchSalary' => true, 'getAchievementPoints' => true, 'getAchievementsByIDs' => true, 'getAchievementsPointsByType' => true, 'getKeepOnlineAchievements' => true, 'getLastOnlineAchievements' => true, 'getLatestAchievements' => true, 'getNameList' => true, 'getNewAchievement' => true, 'getPrizeStatus' => true, 'getShowAchievements' => true, 'setShowAchievements' => true, 'setShowName' => true, 'setUserShowTitleID' => true),
			'active' => array ('fetchPrize' => true, 'getActiveInfo' => true),
			'allblue' => array ('atkSeaMonster' => true, 'buyDonateItemTimes' => true, 'catchFish' => true, 'catchKrill' => true, 'catchKrills' => true, 'collectAllBule' => true, 'donateByGold' => true, 'donateByItem' => true, 'farmFish' => true, 'farmFishInfo' => true, 'fishing' => true, 'getAllBlueInfo' => true, 'getAllBlueLevelInfo' => true, 'getFarmFishInfo' => true, 'getFriendList' => true, 'getSubordinateFishList' => true, 'getSubordinateList' => true, 'goFriendFishpond' => true, 'krillInfo' => true, 'openBoot' => true, 'openFishQueue' => true, 'refreshKrill' => true, 'thiefFish' => true, 'wishFish' => true),
			'answer' => array ('answerQuestion' => true, 'getInfo' => true),
			'applefactory' => array ('compose' => true, 'getInfo' => true),
			'arena' => array ('buyAddedChallenge' => true, 'challenge' => true, 'clearCdtime' => true, 'defeatedNotice' => true, 'enterArena' => true, 'getDefeatedNotice' => true, 'getInfo' => true, 'getPositionList' => true, 'getPositionReward' => true, 'getRewardLuckyList' => true, 'hasReward' => true, 'leaveArena' => true, 'refreshBroadcast' => true, 'refreshPlayerList' => true),
			'astrolabe' => array ('askBuyStone' => true, 'askEquipTalentAst' => true, 'askInitInfo' => true, 'askLevelUpCons' => true, 'askLevelUpMain' => true, 'askObtainTodayExp' => true, 'askSwitchTalentAst' => true, 'askUnEquipTalentAst' => true, 'resetTalentAst' => true),
			'ateam' => array ('adjust' => true, 'dismiss' => true, 'enter' => true, 'kick' => true, 'leave' => true, 'quit' => true, 'setAutoStart' => true, 'start' => true),
			'bag' => array ('arrange' => true, 'arrangeDepot' => true, 'bagInfo' => true, 'compositeItem' => true, 'destoryItem' => true, 'gridInfo' => true, 'moveItem' => true, 'openDepotGrid' => true, 'openDepotGridByItem' => true, 'openGrid' => true, 'openGridByItem' => true, 'receiveItem' => true, 'sortgridInfo' => true, 'swapBagDepot' => true, 'useItem' => true),
			'battle' => array ('getRecord' => true, 'getRecordUrl' => true),
			'blackmarket' => array ('buyBlackMarket' => true, 'buyGoods' => true, 'closeBlackMarket' => true, 'getBlackMarketInfo' => true, 'refreshBlackMarket' => true, 'setting' => true, 'triggerBlackMarket' => true),
			'blood' => array ('autoReceiveScore' => true, 'buyFailCount' => true, 'buyReceiveCount' => true, 'buyReceiveCount' => true, 'cancelReady' => true, 'change' => true, 'changeFormation' => true, 'create' => true, 'endBlood' => true, 'enhanceAttr' => true, 'enhanceAttrAll' => true, 'enter' => true, 'formation' => true, 'getEnterInfo' => true, 'getInfo' => true, 'join' => true, 'leave' => true, 'notify' => true, 'ready' => true, 'receiveScore' => true, 'reChallenge' => true, 'serverRank' => true, 'start' => true),
			'boatbattle' => array ('boatBattleUserInfo' => true, 'buyRecivePointTimes' => true, 'changeFormation' => true, 'chooseRound' => true, 'create' => true, 'formationInfo' => true, 'getEnterInfo' => true, 'join' => true, 'joinBattle' => true, 'jumpRound' => true, 'nextRound' => true, 'quit' => true, 'recivePoint' => true, 'recruitHero' => true, 'reload' => true, 'saveFormation' => true, 'secKill' => true, 'weekRank' => true),
			'bonusGrap' => array ('fetchRewards' => true, 'getInfo' => true, 'sendBonus' => true),
			'boss' => array ('attack' => true, 'canEnter' => true, 'enterBossCopy' => true, 'getBossBot' => true, 'getBossOffset' => true, 'inspire' => true, 'inspireByGold' => true, 'kill' => true, 'leaveBossCopy' => true, 'over' => true, 'revive' => true, 'setBossBot' => true, 'setFormationStatus' => true, 'subCdTime' => true, 'unsetBossBot' => true, 'update' => true, 'updateUserCount' => true, 'uploadFormation' => true),
			'bounty' => array ('exchange' => true, 'getbounty' => true, 'getintegrals' => true),
			'burningCrusade' => array ('addBuffers' => true, 'buyTreasure' => true, 'buyTreasureMulti' => true, 'changeFromation' => true, 'createNewGame' => true, 'doBattle' => true, 'getAutoFormationInfo' => true, 'getBattleInfo' => true, 'getBufferShop' => true, 'getFromation' => true, 'getInfo' => true, 'getPrize' => true, 'getSelfRank' => true, 'getTreasure' => true, 'goToNextMission' => true, 'ready' => true, 'refreshBuffer' => true, 'refreshRank' => true, 'uploadAutoFormation' => true),
			'captain' => array ('answer' => true, 'clearCDByGold' => true, 'getCDTime' => true, 'getUserCaptainInfo' => true, 'sail' => true, 'sailByGold' => true),
			'cardguess' => array ('bet' => true, 'getInitInfo' => true, 'getWinCards' => true, 'getWinUsers' => true, 'sendPrizeOver' => true),
			'change' => array ('changeMasterHeroHtid' => true, 'changeName' => true),
			'charity' => array ('fetchCharity' => true, 'fetchCharityAfterOpenServer' => true, 'fetchPresigeSalary' => true, 'fetchVipSalary' => true, 'getCharityInfo' => true, 'onClicktoFetchSalary' => true),
			'chat' => array ('sendBroadCast' => true, 'sendBroadCastInCardServer' => true, 'sendCopy' => true, 'sendGroup' => true, 'sendGuild' => true, 'sendHarbor' => true, 'sendPersonal' => true, 'sendResource' => true, 'sendTown' => true, 'sendWorld' => true),
			'city' => array ('checkEnter' => true, 'enterTown' => true, 'enterTownList' => true, 'leaveTown' => true, 'moveInTown' => true),
			'copet' => array ('addPetSkill' => true, 'advanceTransfer' => true, 'born' => true, 'bornTwins' => true, 'changeToEgg' => true, 'clearCDByGold' => true, 'commitRefresh' => true, 'equip' => true, 'getPrize' => true, 'getUserPetCollectionInfo' => true, 'getUserPetInfo' => true, 'lockSkill' => true, 'openSlot' => true, 'protect' => true, 'refreshQualifications' => true, 'reset' => true, 'rollbackRefresh' => true, 'sell' => true, 'setFollow' => true, 'setShowCoPetID' => true, 'swallow' => true, 'swallowAll' => true, 'transfer' => true, 'understand' => true, 'unequip' => true, 'unLockSkill' => true, 'unProtect' => true, 'upTalentSkill' => true),
			'copy' => array ('attack' => true, 'attackOnce' => true, 'attackOnceByGold' => true, 'autoAttackByGold' => true, 'cancelAutoAtk' => true, 'checkWhenLogin' => true, 'clearFightCdByGold' => true, 'copyTeamPrize' => true, 'createTeam' => true, 'endAttackByGold' => true, 'enterCopy' => true, 'getActivityGroupArmyDefeatNum' => true, 'getAllCopiesID' => true, 'getCommonGroupArmyDefeatNum' => true, 'getCommonGroupBattleInviteSetting' => true, 'getCopiesInfoByCopyChooseID' => true, 'getCopyInfo' => true, 'getEnemiesDefeatNum' => true, 'getEnemyDefeatNum' => true, 'getInviteSetting' => true, 'getPrize' => true, 'getReplayList' => true, 'getUserCopies' => true, 'getUserLatestCopyInfo' => true, 'joinTeam' => true, 'leaveCopy' => true, 'rpenemy' => true, 'saveCommonGroupBattleInviteSetting' => true, 'saveInviteSetting' => true, 'startAutoAtk' => true, 'startAutoAttack' => true, 'startCommonGroupBattleAutoAttack' => true),
			'cruise' => array ('answer' => true, 'arriveNode' => true, 'chooseNode' => true, 'cruiseInfo' => true, 'reCruise' => true, 'throwDice' => true),
			'crystal' => array ('getInfo' => true, 'getResource' => true, 'lvUp' => true, 'lvUpByGold' => true, 'onClickLvUp' => true, 'summon' => true),
			'dailyWorship' => array ('worship' => true, 'worshipInfo' => true),
			'daimonApple' => array ('composite' => true, 'getInfo' => true, 'split' => true, 'transfer' => true),
			'daytask' => array ('abandon' => true, 'accept' => true, 'complete' => true, 'freeRefreshTask' => true, 'getInfo' => true, 'getIntegralReward' => true, 'goldComplete' => true, 'goldRefreshTask' => true, 'itemRefreshTask' => true, 'updatetask' => true, 'upgrade' => true),
			'depositPlan' => array ('buyDepositPlan' => true, 'getDepositPlanInfo' => true, 'receivePrize' => true),
			'digactivity' => array ('dig' => true, 'getInfo' => true),
			'dress' => array ('changeFigure' => true, 'compose' => true, 'getDressRommInfo' => true, 'reinforce' => true, 'split' => true),
			'elementsys' => array ('clear' => true, 'getGameInfo' => true, 'moveStone' => true, 'refresh' => true),
			'elitecopy' => array ('attack' => true, 'byCoin' => true, 'enterEliteCopy' => true, 'getEliteCopyInfo' => true, 'getPassUsers' => true, 'leaveEliteCopy' => true, 'passByGold' => true, 'restartEliteCopy' => true),
			'elves' => array ('get' => true, 'icsAll' => true, 'icsTime' => true, 'setModelLevel' => true),
			'elvessystem' => array ('elvesRelease' => true, 'equip' => true, 'getCollectionInfo' => true, 'getCurElvesInfo' => true, 'getUserElvesInfo' => true, 'heritageExp' => true, 'openCaveSlots' => true, 'openForestSlots' => true, 'putElvesToCave' => true, 'putElvesToForest' => true, 'sendRewardToUser' => true, 'unequip' => true, 'useGoldToAddExp' => true, 'useItemToAddExp' => true, 'useNormalToAddExp' => true),
			'exactivity' => array ('exchange' => true, 'exchangeInfo' => true, 'exHistory' => true),
			'exchange' => array ('exchangeArmItem' => true, 'exchangeDaimonAppleItem' => true, 'exchangeDirectItem' => true, 'exchangeGemItem' => true, 'exchangeHorseDecorationItem' => true, 'exchangeItem' => true, 'exchangeJewelryItem' => true, 'exchangePartnerDressItem' => true, 'getExchangeInfo' => true, 'recieveItem' => true),
			'exchangeshop' => array ('addReward' => true, 'buyPoint' => true, 'exchangShopInfo' => true, 'exItem' => true, 'getIsReward' => true),
			'expdrugs' => array ('addExpbyDrugs' => true),
			'explore' => array ('clearCdtime' => true, 'exploreArm' => true, 'exploreArmor' => true, 'explorePos' => true, 'getBoxByGold' => true, 'getExplore' => true, 'getReward' => true, 'moveToBag' => true, 'quickExplore' => true, 'sell' => true),
			'festival' => array ('buyCard' => true, 'exchangeItem' => true, 'flopCards' => true, 'getAreadyBuyInfo' => true, 'getExchangePoint' => true, 'getFeedbackUserInfo' => true, 'getFestivalUserInfo' => true, 'sellCards' => true, 'takePlayingFeedback' => true, 'takeReturnFeedback' => true),
			'fish' => array ('add' => true, 'steal' => true, 'subordinateBreed' => true, 'will' => true, 'willFriend' => true),
			'forge' => array ('compose' => true, 'daimonAppleFuse' => true, 'daimonAppleFuseAll' => true, 'daimonAppleLevelUpByExp' => true, 'demonEvoPanel' => true, 'demonEvoUp' => true, 'elementFuse' => true, 'elementFuseAll' => true, 'elementFuseExp' => true, 'enchase' => true, 'fixedRefresh' => true, 'fixedRefreshAffirm' => true, 'fuse' => true, 'fuseAll' => true, 'gemLevelUpByExp' => true, 'getPotentialityTransfer' => true, 'getRefreshReq' => true, 'getReinforceCD' => true, 'getReinforceProbability' => true, 'getTransferInfo' => true, 'gild' => true, 'openMaxProbability' => true, 'partnerDressReinforce' => true, 'potentialityTransfer' => true, 'randRefresh' => true, 'randRefreshAffirm' => true, 'reinforce' => true, 'reinforceBoatArm' => true, 'resetReinforceTime' => true, 'split' => true, 'transfer' => true, 'unGild' => true, 'weakening' => true),
			'formation' => array ('changeCurFormation' => true, 'commitAttr' => true, 'evolution' => true, 'getAllFormation' => true, 'getCdEndTime' => true, 'getFormationBench' => true, 'plusFormationLv' => true, 'refreshAttr' => true, 'setCurFormation' => true),
			'friend' => array ('addBlackList' => true, 'addFriend' => true, 'addRecommendFriendList' => true, 'delFriend' => true, 'getBestFriend' => true, 'getFriendList' => true, 'notify' => true, 'recommendFriendList' => true),
			'gatherweal' => array ('exchange' => true),
			'gb' => array ('chanllenge' => true, 'vote' => true),
			'gemimprint' => array ('gemImprint' => true, 'gemRefresh' => true, 'materialsInfo' => true),
			'gemmatrix' => array ('explode' => true, 'getInfo' => true, 'getScore' => true, 'levelUp' => true),
			'gemrefresh' => array ('refresh' => true, 'refreshInfo' => true, 'replace' => true),
			'gm' => array ('getInfoBeforeExit' => true),
			'groupwar' => array ('enter' => true, 'getAutoJoin' => true, 'getEnterInfo' => true, 'groupBattleInfo' => true, 'inspire' => true, 'join' => true, 'leave' => true, 'removeJoinCd' => true, 'setAutoJoin' => true),
			'growupplan' => array ('activation' => true, 'fetchPrize' => true, 'getInfo' => true),
			'guild' => array ('agree' => true, 'apply' => true, 'buyEmblem' => true, 'buyMemberNum' => true, 'cancel' => true, 'contributeBelly' => true, 'contributeGold' => true, 'create' => true, 'dismiss' => true, 'endBanquet' => true, 'enterClub' => true, 'getGuildAndMemberInfo' => true, 'getGuildApplyList' => true, 'getGuildByName' => true, 'getGuildInfo' => true, 'getGuildInfoById' => true, 'getMemberArenaList' => true, 'getMemberInfo' => true, 'getMemberList' => true, 'getPersonalApplyList' => true, 'getRecordList' => true, 'getWorldList' => true, 'holdBanquet' => true, 'holdBanquetGuildBoss' => true, 'impeach' => true, 'inspire' => true, 'joinGuild' => true, 'kickMember' => true, 'leaveClub' => true, 'modifyPasswd' => true, 'openFlag' => true, 'quit' => true, 'refreshBanquet' => true, 'refuse' => true, 'setDefaultTech' => true, 'setEmblem' => true, 'setJob' => true, 'setVicePresident' => true, 'startBanquet' => true, 'transPresident' => true, 'unsetVicePresident' => true, 'updateGuildName' => true, 'updatePost' => true, 'updateSlogan' => true, 'upgradeBanquet' => true),
			'guildboss' => array ('attack' => true, 'autoAttend' => true, 'clearCd' => true, 'getInfo' => true, 'getRank' => true),
			'guildshop' => array ('buildByItems' => true, 'buildByMoney' => true, 'buyShopItems' => true, 'getMysticShopInfo' => true, 'getNormalShopInfo' => true, 'resetMysticShopInfo' => true),
			'guildskill' => array ('getAllGuildTechLv' => true, 'getBellyPurchaseTimes' => true, 'plusGuildTechLv' => true, 'PurchaseTechPoint' => true),
			'guildwar' => array ('buyMaxWinTimes' => true, 'changeExpendablies' => true, 'cheer' => true, 'clearUpdFmtCdByGold' => true, 'enterWorldWar' => true, 'getGuildExpendableList' => true, 'getGuildWarInfo' => true, 'getGuildWarInfoByID' => true, 'getHistoryCheerInfo' => true, 'getHistoryFightInfo' => true, 'getPrize' => true, 'getReplay' => true, 'getTempleInfo' => true, 'getUserFightForce' => true, 'getUserGuildWarInfo' => true, 'getWorshipUsers' => true, 'leaveMsg' => true, 'leaveWorldWar' => true, 'signUp' => true, 'updateFormation' => true, 'worship' => true),
			'haki' => array ('addProperty' => true, 'allGoldTrial' => true, 'allTrial' => true, 'convert' => true, 'gethakiInfo' => true, 'hakiInfo' => true, 'hakiReturn' => true, 'levelupHakiScene' => true, 'notifyOpenUi' => true, 'trial' => true),
			'hero' => array ('addArming' => true, 'addDaimonApple' => true, 'addDress' => true, 'addElementItem' => true, 'addGoodwillByGold' => true, 'addGoodwillByItem' => true, 'addJewelry' => true, 'addPartnerDressItem' => true, 'addPrestigeHero' => true, 'convert' => true, 'fire' => true, 'getConvertHeroes' => true, 'getHeroByHid' => true, 'getHeroes' => true, 'getPubHeroes' => true, 'getRecruitHeroes' => true, 'getTalnetSkillInfo' => true, 'heritageGoodwill' => true, 'masterLearnSkill' => true, 'masterLearnSkillFromOther' => true, 'masterTransfer' => true, 'masterUsingSkill' => true, 'moveAllArming' => true, 'moveAllArmingAndJewelry' => true, 'moveAllJewelry' => true, 'moveArming' => true, 'moveElementItem' => true, 'moveJewelry' => true, 'openDaimonAppleByItem' => true, 'rebirth' => true, 'recruit' => true, 'reFreshPartnerDress' => true, 'removeArming' => true, 'removeDaimonApple' => true, 'removeDress' => true, 'removeElementItem' => true, 'removeJewelry' => true, 'removePartnerDressItem' => true, 'sendAllGift' => true, 'talnetSkillLevelUp' => true),
			'herocopy' => array ('attack' => true, 'byCoin' => true, 'enterHeroCopy' => true, 'getHeroCopyInfo' => true, 'getHeroCopyInfoByID' => true, 'getAllCopiesID' => true, 'leaveHeroCopy' => true),
			'honourshop' => array ('exItemByHonour' => true, 'honourInfo' => true),
			'horsedecoration' => array ('getInfo' => true, 'refresh' => true, 'reinforce' => true, 'replace' => true, 'setSuit' => true, 'transfer' => true),
			'impeldown' => array ('buyChallengeTime' => true, 'getImpelDownInfo' => true, 'getPrize' => true, 'getTop' => true, 'refreshNpcList' => true, 'refreshNpcListByGold' => true, 'savingAce' => true),
			'itemInfo' => array ('getItemInfo' => true),
			'jewelry' => array ('getStrengthInfo' => true, 'reBrith' => true, 'refresh' => true, 'refreshInfo' => true, 'reinforce' => true, 'replace' => true, 'sealTransfer' => true, 'treasureStrengthMore' => true),
			'kitchen' => array ('clearCDByGold' => true, 'cook' => true, 'getBeorder' => true, 'getUserKitchenInfo' => true, 'getUserOrderInfo' => true, 'goldCook' => true, 'goldCookByTimes' => true, 'placeOrder' => true, 'sell' => true),
			'lottery' => array ('doLotteryAll' => true, 'doLotteryOnce' => true, 'doLotteryTimes' => true, 'getInfo' => true, 'reset' => true),
			'luckypointer' => array ('getInfo' => true, 'getRollLog' => true, 'roll' => true),
			'mail' => array ('deleteAllBattleMail' => true, 'deleteAllMailBoxMail' => true, 'deleteAllPlayerMail' => true, 'deleteAllSystemMail' => true, 'deleteMail' => true, 'fetchAllItems' => true, 'fetchItem' => true, 'getBattleMailList' => true, 'getMailBoxList' => true, 'getMailDetail' => true, 'getNoReadMailCount' => true, 'getPlayMailList' => true, 'getSysItemMailList' => true, 'getSysMailList' => true, 'sendMail' => true),
			'map' => array ('mapInfo' => true),
			'mergeserver' => array ('getCompensation' => true, 'getIsCompensation' => true, 'getMergerServerTimes' => true, 'getRewardLast' => true, 'Reward' => true),
			'npc' => array ('getInfo' => true),
			'NpcInfo' => array ('getTreasureNpc' => true),
			'npcresource' => array ('attackResourceByUser' => true, 'doNpcAttackNow' => true, 'enterNpcResource' => true, 'givenUpNpcResource' => true, 'leaveNpcResource' => true, 'plunderResource' => true, 'resourceInfo' => true),
			'olympic' => array ('challenge' => true, 'cheer' => true, 'clearCdByGold' => true, 'enterArena' => true, 'getAllCheerObj' => true, 'getFightInfo' => true, 'getJackPot' => true, 'getSelfOrder' => true, 'getTop' => true, 'getUserOlympicInfo' => true, 'levelArena' => true, 'signUp' => true),
			'outlet' => array ('buy' => true, 'enter' => true, 'getStock' => true, 'leave' => true),
			'pachinko' => array ('getUserPachinkoInfo' => true, 'play' => true, 'showHand' => true),
			'partnertrial' => array ('doBattle' => true, 'fetchDailyPrize' => true, 'fetchPassPrize' => true, 'getInfo' => true, 'rankInfo' => true, 'resetCheckPoint' => true),
			'payback' => array ('executeAllPayBack' => true, 'getCurAvailablePayBackIds' => true),
			'pet' => array ('addPetSkill' => true, 'advanceTransfer' => true, 'clearCDByGold' => true, 'commitRefresh' => true, 'degenerateToEgg' => true, 'equip' => true, 'evolution' => true, 'feedingAll' => true, 'feedingOnce' => true, 'getOutWarehouse' => true, 'getPrize' => true, 'getUserPetCollectionInfo' => true, 'getUserPetInfo' => true, 'lockSkill' => true, 'openSlot' => true, 'openWarehouseSlot' => true, 'putInWarehouse' => true, 'rapid' => true, 'rapidByGold' => true, 'reborn' => true, 'refreshQualifications' => true, 'resetByEgg' => true, 'resetByGold' => true, 'rollbackRefresh' => true, 'sell' => true, 'setFollow' => true, 'setShowPetID' => true, 'stopTrain' => true, 'train' => true, 'transfer' => true, 'understand' => true, 'unequip' => true, 'unLockSkill' => true, 'upTalentSkill' => true),
			'poker' => array ('abandon' => true, 'afterLogin' => true, 'battleFinish' => true, 'bet' => true, 'betOpen' => true, 'betOperation' => true, 'buyPoint' => true, 'cancelMatch' => true, 'checkLogin' => true, 'competeOk' => true, 'competePrice' => true, 'exceptionFinish' => true, 'formation' => true, 'freezeCdEnd' => true, 'getProcessInfo' => true, 'getTodayFreeReward' => true, 'login' => true, 'match' => true, 'matchSuccess' => true, 'processInfo' => true, 'ready' => true, 'readyok' => true, 'readysuccess' => true, 'readyTimeOut' => true, 'receiveCompete' => true, 'startFormation' => true, 'userLogoff' => true),
			'pokerbill' => array ('bill2game' => true, 'bill2poker' => true, 'checkBill2game' => true, 'checkBill2poker' => true),
			'port' => array ('attackResource' => true, 'enterPort' => true, 'enterPortResource' => true, 'excavateResource' => true, 'extendResourceTimeByGold' => true, 'getElves' => true, 'getPlunderInfo' => true, 'getPort' => true, 'givenupResource' => true, 'leavePort' => true, 'leavePortResource' => true, 'moveInPort' => true, 'occupy' => true, 'plunderResource' => true, 'portBerthInfo' => true, 'resetPlunderCdByGold' => true, 'resourceInfo' => true, 'selfBerthInfo' => true, 'selfResourceInfo' => true),
			'practice' => array ('accelerate' => true, 'accelerateByTimes' => true, 'fetchExp' => true, 'getUserPracticeInfo' => true, 'openVipFullDayMode' => true),
			'propertylock' => array ('getStatus' => true, 'initPassword' => true, 'questionReset' => true, 'reset' => true, 'setStatus' => true, 'unlock' => true),
			'purchaseExp' => array ('buyElementExp' => true, 'getInfo' => true),
			'pushmap' => array ('buyFailCount' => true, 'challenge' => true, 'getGuide' => true, 'getInitData' => true, 'getRank' => true, 'refreshHeros' => true),
			'randomboss' => array ('attackBoss' => true, 'clearCd' => true, 'getInitData' => true),
			'retrieve' => array ('getInfo' => true, 'retrieveScore' => true),
			'reward' => array ('allHolidaysReward' => true, 'dailyFillSign' => true, 'dailySign' => true, 'dailySignReward' => true, 'getDailySignInfo' => true, 'getGift' => true, 'getGiftByCode' => true, 'getGiftInfo' => true, 'getHolidaysInfo' => true, 'getRewardGold' => true, 'getRewardGoldInfo' => true, 'getSignInfo' => true, 'getSprFestWelfareInfo' => true, 'holidaysReward' => true, 'recieveSprFestWelfare' => true, 'sign' => true, 'signUpgrade' => true),
			'ride' => array ('disMount' => true, 'getCellectInfo' => true, 'getInfo' => true, 'mount' => true, 'receiveCellectReward' => true, 'setShowStatus' => true),
			'riding' => array ('getInfo' => true, 'train' => true),
			'rollprestige' => array ('batch' => true, 'getInitInfo' => true, 'recievePrestige' => true, 'start' => true),
			'rondombox' => array ('exchange' => true, 'open' => true),
			'roulette' => array ('batch' => true, 'getInitInfo' => true, 'recieveExp' => true, 'start' => true),
			'sailboat' => array ('addNewBuildList' => true, 'clearCDByGold' => true, 'equipItem' => true, 'getBoatInfo' => true, 'getBoatInfoByID' => true, 'getBuildListStatus' => true, 'openNewCabin' => true, 'refittingSailboat' => true, 'removeItem' => true, 'upgradeCabinLv' => true),
			'salesroom' => array ('addprice' => true, 'auction' => true, 'getLastSalesroomInfo' => true, 'getSalesroomInfo' => true),
			'sciTech' => array ('clearCdTimeByGold' => true, 'getAllSciTechLv' => true, 'getCdEndTime' => true, 'openCreditMode' => true, 'plusSciTechLv' => true),
			'scratchcard' => array ('getInfo' => true, 'roll' => true),
			'seasoul' => array ('composeSeasoul' => true, 'getInfo' => true, 'openMultiPalace' => true, 'openMultiStarfish' => true, 'openPalaceBig' => true),
			'smelting' => array ('clearCDByGold' => true, 'getSmeltingInfo' => true, 'getSmeltingItem' => true, 'integralExchange' => true, 'inviteArtificer' => true, 'refreshArtificer' => true, 'smeltingAll' => true, 'smeltingOnce' => true),
			'soul' => array ('automatic' => true, 'convert' => true, 'create' => true, 'exchangeItemByGreen' => true, 'get' => true, 'grow' => true, 'harvest' => true, 'levelUpSoul' => true),
			'spend' => array ('getInfo' => true, 'getReward' => true),
			'strongworldshop' => array ('buyShopItems' => true, 'getShopInfo' => true, 'refreshShopInfo' => true),
			'talks' => array ('getHeroList' => true, 'getUserTalksInfo' => true, 'openFreeMode' => true, 'refresh' => true, 'refreshAll' => true, 'startTalks' => true),
			'task' => array ('abandon' => true, 'accept' => true, 'complete' => true, 'getAllTask' => true, 'updateTask' => true),
			'taskfight' => array ('finishByGold' => true, 'getInfo' => true, 'reward' => true),
			'tehuishop' => array ('buy' => true, 'getInitInfo' => true),
			'town' => array ('addPlayer' => true, 'addTeleport' => true, 'delPlayer' => true, 'hide' => true, 'move' => true, 'transport' => true, 'update' => true),
			'trade' => array ('buy' => true, 'repurchase' => true, 'repurchaseInfo' => true, 'sell' => true, 'sellerInfo' => true),
			'train' => array ('changeTrainMode' => true, 'clearCDByGold' => true, 'getUserTrainInfo' => true, 'openTrainSlot' => true, 'rapid' => true, 'rapidByGold' => true, 'rapidByTimes' => true, 'startTrain' => true, 'stopTrain' => true, 'upgradeTrainCd' => true),
			'treasure' => array ('autoHunt' => true, 'clearRobCdtime' => true, 'enterReturnScene' => true, 'exchangeItemWithScore' => true, 'getInfo' => true, 'getTreasureAutoConf' => true, 'hunt' => true, 'huntReturnByGold' => true, 'leaveReturnScene' => true, 'openMapByGold' => true, 'refresh' => true, 'rob' => true, 'stopAutoHunt' => true),
			'treasurenpc' => array ('buySuccess' => true, 'getHasTreasureNpc' => true, 'getTreasureNpc' => true, 'huntTreasureNpc' => true),
			'turntable' => array ('getBellyPond' => true, 'getInfo' => true, 'refresh' => true, 'roll' => true),
			'twelvemountain' => array ('buyChallengeTimes' => true, 'buyNewSlot' => true, 'doBattle' => true, 'getCheckpointInfo' => true, 'getCommonBattleReward' => true, 'getFormationInfo' => true, 'getGoldBattleReward' => true, 'getGuildHeroInfo' => true, 'getHeroDetailInfo' => true, 'getHeroReward' => true, 'getStarReward' => true, 'getTwelveMountainInfo' => true, 'getUserUploadInfo' => true, 'rentHero' => true, 'replaceHero' => true, 'reset' => true, 'resetHeroTime' => true, 'setFormation' => true, 'setMessage' => true),
			'twentyOneScores' => array ('continueSendCards' => true, 'fetchRewards' => true, 'getUserInfo' => true, 'refreshRewards' => true, 'sendCards' => true, 'sendCardsAgain' => true),
			'user' => array ('attack' => true, 'buyBloodPackage' => true, 'buyExecution' => true, 'buyGemExp' => true, 'cancelDel' => true, 'createUser' => true, 'delUser' => true, 'getArrConfig' => true, 'getCanRecruitHeroNum' => true, 'getChunPuOPClientReward' => true, 'getOPClientReward' => true, 'getOtherUserRctHeroes' => true, 'getPayReward' => true, 'getRandomName' => true, 'getSecondPayInfo' => true, 'getSecondPayReward' => true, 'getSelfOrder' => true, 'getSettings' => true, 'getSimpleInfo' => true, 'getSwitch' => true, 'getSwitchRewardInfo' => true, 'getTop' => true, 'getTopUserInfo' => true, 'getUser' => true, 'getUserByID' => true, 'getUserInfoFromCache' => true, 'getUsers' => true, 'getVaConfig' => true, 'groupTransferByGold' => true, 'groupTransferByItem' => true, 'isGetPayReward' => true, 'isPay' => true, 'login' => true, 'openHeroPos' => true, 'setArrConfig' => true, 'setGroup' => true, 'setMute' => true, 'setVaConfig' => true, 'setVisibleCount' => true, 'showDress' => true, 'showVip' => true, 'switchReward' => true, 'unameToUid' => true, 'updateMsg' => true, 'userLogin' => true, 'userLogoff' => true, 'wallowKick' => true),
			'vassal' => array ('conquer' => true, 'getInfoByUid' => true, 'getVassalAll' => true, 'getVassalUserInfo' => true, 'relieve' => true, 'train' => true),
			'weekcard' => array ('buy' => true, 'getInfo' => true, 'reward' => true),
			'worldboat' => array ('afterLogin' => true, 'broadcastMsg' => true, 'buyFreeNepAttack' => true, 'checkLogin' => true, 'enter' => true, 'getBoatInfo' => true, 'getBridInfo' => true, 'getEnterInfo' => true, 'getMsgInfo' => true, 'getNepInfo' => true, 'getUserActiveInfo' => true, 'inGold' => true, 'inspire' => true, 'join' => true, 'leave' => true, 'login' => true, 'nepAttack' => true, 'outGold' => true, 'peacock' => true, 'removeJoinCd' => true, 'setBoatId' => true, 'setNepId' => true, 'sign' => true, 'strength' => true, 'strengthNep' => true),
			'worldbullfightarena' => array ('afterLogin' => true, 'attack' => true, 'canEnter' => true, 'changeFormation' => true, 'enter' => true, 'getInspireLevel' => true, 'getPlayers' => true, 'getRankList' => true, 'getUserActiveInfo' => true, 'inspire' => true, 'joinReviveCd' => true, 'leave' => true, 'login' => true, 'over' => true, 'sendBattleInfo' => true, 'sendJoinReviveCd' => true, 'sendReviveInfo' => true, 'sign' => true, 'updateFightInfo' => true),
			'worldchat' => array ('afterLogin' => true, 'checkBeforeChat' => true, 'checkLogin' => true, 'getPeakChatLogs' => true, 'login' => true, 'sendPeakChatMsg' => true, 'sendWorldChatMsg' => true),
			'worldpeak' => array ('cheer' => true, 'clearPromotionUpdateCd' => true, 'getEveryDayReceivePopularTop' => true, 'getEveryDaySendPopularTop' => true, 'getFameHallInfo' => true, 'getFameHallShowInfo' => true, 'getTotalReceivePopularTop' => true, 'getUserAuditionsInfo' => true, 'getUserFightRecord' => true, 'getUserHistoryInfo' => true, 'getUserPeakInfo' => true, 'getUserReceivePopularInfo' => true, 'getWorldPeakDivisionInfo' => true, 'getWorldPeakReplay' => true, 'sendPopularGift' => true, 'signupAdvance' => true, 'signupNormal' => true, 'updateSignUpInfo' => true),
			'worldResource' => array ('battleFieldUpdate' => true, 'enter' => true, 'giveup' => true, 'guildworldResourceInfos' => true, 'leave' => true, 'signup' => true, 'worldResourceAttackList' => true, 'worldResourceSignupList' => true),
			'worldteam' => array ('addMatchingCount' => true, 'addViewCount' => true, 'cancelReady' => true, 'change' => true, 'changeFormation' => true, 'changeOrder' => true, 'cheer' => true, 'clearPlayOffCd' => true, 'create' => true, 'dismissTeam' => true, 'enter' => true, 'formation' => true, 'getInit' => true, 'getMyCheerInfo' => true, 'getMyFormationInfo' => true, 'getPlayOffFightInfo' => true, 'getPlayOffInfo' => true, 'getRank' => true, 'getReady' => true, 'getScoreFightMsg' => true, 'getTeamPrize' => true, 'getTempleInfo' => true, 'getTeamWarInfo' => true, 'getView' => true, 'getViewDetail' => true, 'getWorshipUsers' => true, 'join' => true, 'leave' => true, 'leaveMsg' => true, 'matching' => true, 'pk' => true, 'updatePlayOffFightInfo' => true, 'updateUserFightInfo' => true, 'updateUserHero' => true, 'worship' => true),
			'worldtree' => array ('clearCdByGold' => true, 'contributeByItem' => true, 'contributeByMoney' => true, 'getContributeRankInfo' => true, 'getContributeUsers' => true, 'getWordTreeInfo' => true),
			'worldwar' => array ('challengeChampion' => true, 'cheer' => true, 'clearUpdFmtCdByGold' => true, 'enterWorldWar' => true, 'getFormationInfo' => true, 'getHistoryCheerInfo' => true, 'getHistoryFightInfo' => true, 'getPrize' => true, 'getTempleInfo' => true, 'getUserWorldWarInfo' => true, 'getWorldWarInfo' => true, 'getWorshipUsers' => true, 'leaveMsg' => true, 'leaveWorldWar' => true, 'sendChallegeSucRewards' => true, 'signUp' => true, 'updateFormation' => true, 'worship' => true),
			);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
