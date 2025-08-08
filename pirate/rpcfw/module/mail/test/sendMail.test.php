<?php
if (! defined ( 'ROOT' ))
{
	define ( 'ROOT', dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) );
	define ( 'LIB_ROOT', ROOT . '/lib' );
	define ( 'EXLIB_ROOT', ROOT . '/exlib' );
	define ( 'DEF_ROOT', ROOT . '/def' );
	define ( 'CONF_ROOT', ROOT . '/conf' );
	define ( 'LOG_ROOT', ROOT . '/log' );
	define ( 'MOD_ROOT', ROOT . '/module' );
	define ( 'HOOK_ROOT', ROOT . '/hook' );
}


require_once (DEF_ROOT . '/Define.def.php');

if (file_exists ( DEF_ROOT . '/Classes.def.php' ))
{
	require_once (DEF_ROOT . '/Classes.def.php');

	function __autoload($className)
	{

		$className = strtolower ( $className );
		if (isset ( ClassDef::$ARR_CLASS [$className] ))
		{
			require_once (ROOT . '/' . ClassDef::$ARR_CLASS [$className]);
		}
		else
		{
			trigger_error ( "class $className not found", E_USER_ERROR );
		}
	}
}

$framework = new RPCFramework ();
RPCContext::getInstance ()->setFramework ( $framework );
$framework->initExtern("","","",ScriptConf::PRIVATE_DB, time());
 
$recieverUid = 49806;
$replayId = 1;


$user1 = array (
	'uid' => 10001,
	'uname' => 'qwerty',
	'utid' => 1,
);

$user2 = array (
	'uid' => 51708,
	'uname' => 'qwerty',
	'utid' => 3,
);

$guild = array (
	'guild_id' => 1,
	'guild_name' => 'guild1',
);

//$bossId = 15;
//MailTemplate::sendConquer($recieverUid, $user1, $replayId, false);
//MailTemplate::sendConquer($recieverUid, $user1, $replayId, true);

//MailTemplate::sendPillage($recieverUid, $user1, $user2, $replayId, false);
//MailTemplate::sendPillage($recieverUid, $user1, $user2, $replayId, true);

//MailTemplate::sendMovePort($recieverUid, array($user1, $user1, $user1, $user1, $user1), $user1);
//MailTemplate::sendMovePort($recieverUid, array($user1, $user1, $user1), $user1);

//MailTemplate::sendBeingConquer($recieverUid, $user1, $replayId, false);
//MailTemplate::sendBeingConquer($recieverUid, $user1, $replayId, true);

//MailTemplate::sendBeingPillage($recieverUid, $user1, $user2, $replayId, false);
//MailTemplate::sendBeingPillage($recieverUid, $user1, $user2, $replayId, true);

//MailTemplate::sendMasterMovePort($recieverUid, $user1);
//MailTemplate::sendSubordinateMovePort($recieverUid, $user2);
//
//MailTemplate::sendRevoltMaster($recieverUid, $user1, $replayId, false);
//MailTemplate::sendRevoltMaster($recieverUid, $user1, $replayId, true);
//
//MailTemplate::sendSubordinateRevolt($recieverUid, $user1, $replayId, false);
//MailTemplate::sendSubordinateRevolt($recieverUid, $user1, $replayId, true);
//
//MailTemplate::sendupSubordinateGivenup($recieverUid, $user1);
//
//MailTemplate::sendPortResourceAttackDefaultFailed($recieverUid, $replayId);
//
//
//MailTemplate::sendPortResourceAttack($recieverUid, $user1, $replayId, false);
//MailTemplate::sendPortResourceAttack($recieverUid, $user1, $replayId, true);
//
//MailTemplate::sendPortResourceDue($recieverUid, 1);
//
//MailTemplate::sendPortResourceDefend($recieverUid, $user1, $replayId, TRUE, 1, 1);
//MailTemplate::sendPortResourceDefend($recieverUid, $user1, $replayId, false);
//
//
//MailTemplate::sendApplyGuild($recieverUid, $guild, false);
//MailTemplate::sendApplyGuild($recieverUid, $guild, true);
//
//
//MailTemplate::sendKickoutGuild($recieverUid, $guild, $user1);
//
//
//MailTemplate::sendGuildBanquet($recieverUid, false, 1, 1);
//MailTemplate::sendGuildBanquet($recieverUid, true, 1, 1, 1);
//
//MailTemplate::sendWorldResourceAward($recieverUid, 1, 1);
//
//
//MailTemplate::sendWorldResourceAttack($recieverUid, 1, 1, 1, 1, false);
//MailTemplate::sendWorldResourceAttack($recieverUid, 1, 1, 1, 1, true);
//
//MailTemplate::sendAttackUser($recieverUid, $user1, 1, 1, false);
//MailTemplate::sendAttackUser($recieverUid, $user1, 1, 1, true);
//
//
//MailTemplate::sendDefendUser($recieverUid, $user1, 1, 1, false);
//MailTemplate::sendDefendUser($recieverUid, $user1, 1, 1, true);
//
//MailTemplate::sendAddFriend($recieverUid, $user1);
//
//MailTemplate::sendArenaLuckyAward($recieverUid, 1, 1, array(10001=>1) );
//
//MailTemplate::sendArenaAward($recieverUid, 1, 1, 1, 1, 1, 1);
//
//
//MailTemplate::sendAchievement($recieverUid, 101101, 1, 1, 1, 1, 1, array(1000101));
//
//MailTemplate::sendTreasureReward($recieverUid, 1, 1, array(1000101), FALSE );
//
//
//MailTemplate::sendTreasureAttack($recieverUid, 101, $user1, 1, 1, 1, true);
//MailTemplate::sendTreasureAttack($recieverUid, 101, $user1, 1, 1, 1, false);
//
//MailTemplate::sendTreasureDefend($recieverUid, $user1, 1, 1, 1, true);
//MailTemplate::sendTreasureDefend($recieverUid, $user1, 1, 1, 1, false);
//
//MailTemplate::sendBoatOrder($recieverUid, $user1, 1);
//
//
//$master = $user1;
//$subordinate = $user1;
//$reward = array(
//	'belly' => 1,
//	'experience' => 1,
//	'prestige' => 1,
//	'gold' => 1,
//	'items' => array(1000101),
//);
//
//$reward1 = array(
//	'belly' => 1,
//	'experience' => 1,
//	'prestige' => 1,
//	'gold' => 1,
//	'items' => array(),
//);
//$belly = 100;
//
//MailTemplate::sendBossKill($recieverUid, $bossId, $reward1);
//MailTemplate::sendBossAttackHpFirst($recieverUid, $bossId, $reward1);
//MailTemplate::sendBossAttackHpSecond($recieverUid, $bossId, $reward1);
//MailTemplate::sendBossAttackHpThird($recieverUid, $bossId, $reward1);
//MailTemplate::sendBossAttackHpOther($recieverUid, $bossId, 100, '1.11%', $reward1);
//
//
//MailTemplate::sendBossKill($recieverUid, $bossId, $reward);
//MailTemplate::sendBossAttackHpFirst($recieverUid, $bossId, $reward);
//MailTemplate::sendBossAttackHpSecond($recieverUid, $bossId, $reward);
//MailTemplate::sendBossAttackHpThird($recieverUid, $bossId, $reward);
//MailTemplate::sendBossAttackHpOther($recieverUid, $bossId, 100, '1.11%', $reward);
//MailTemplate::sendTrainBrushToilet($recieverUid, $subordinate, $belly);
//MailTemplate::sendTrainPacify($recieverUid, $subordinate, $belly);
//MailTemplate::sendTrainItch($recieverUid, $subordinate, $belly);
//MailTemplate::sendTrainPlayGame($recieverUid, $subordinate, $belly);
//MailTemplate::sendTrainBeat($recieverUid, $subordinate, $belly);
//MailTemplate::sendTrainPraise($recieverUid, $subordinate, $belly);
//MailTemplate::sendTrainRide($recieverUid, $subordinate, $belly);
//MailTemplate::sendTrainPlayBall($recieverUid, $subordinate, $belly);
//MailTemplate::sendTrainShowtime($recieverUid, $subordinate, $belly);
//MailTemplate::sendBeingTrainBrushToilet($recieverUid, $master, $belly);
//MailTemplate::sendBeingTrainPacify($recieverUid, $master, $belly);
//MailTemplate::sendBeingTrainItch($recieverUid, $master, $belly);
//MailTemplate::sendBeingTrainPlayGame($recieverUid, $master, $belly);
//MailTemplate::sendBeingTrainBeat($recieverUid, $master, $belly);
//MailTemplate::sendBeingTrainPraise($recieverUid, $master, $belly);
//MailTemplate::sendBeingTrainRide($recieverUid, $master, $belly);
//MailTemplate::sendBeingTrainPlayBall($recieverUid, $master, $belly);
//MailTemplate::sendBeingTrainShowtime($recieverUid, $master, $belly);

// 发送擂台赛奖励邮件
//MailTemplate::sendChanlledgeTop32(49806, 
//									3201, 
//									3202, 
//									3203, 
//									3204, 
//									3205, 
//									3206, 
//									array());
//MailTemplate::sendChanlledgeTop16(49806, 
//									1601, 
//									1602, 
//									1603, 
//									1604, 
//									1605, 
//									1606, 
//									array());
//MailTemplate::sendChanlledgeTop8(49806, 
//									801, 
//									802, 
//									803, 
//									804, 
//									805, 
//									806, 
//									array());
//MailTemplate::sendChanlledgeTop4(49806, 
//									401, 
//									402, 
//									403, 
//									404, 
//									405, 
//									406,  
//									array());
//MailTemplate::sendChanlledgeTop2(49806, 
//									201, 
//									202, 
//									203, 
//									204, 
//									205, 
//									206, 
//									array());
//MailTemplate::sendChanlledgeTop1(49806, 
//									101, 
//									102, 
//									103, 
//									104, 
//									105, 
//									106, 
//									array());
//
//// 发送擂台赛助威奖励邮件
//MailTemplate::sendChanlledgeCheerTop8(49806, 
//									801, 
//									802, 
//									803, 
//									804, 
//									805, 
//									806, 
//									array());
//MailTemplate::sendChanlledgeCheerTop4(49806, 
//									401, 
//									402, 
//									403, 
//									404, 
//									405, 
//									406, 
//									array());
//MailTemplate::sendChanlledgeCheerTop2(49806, 
//									201, 
//									202, 
//									203, 
//									204, 
//									205, 
//									206,
//									array());
//MailTemplate::sendChanlledgeCheerTop1(49806, 
//									101, 
//									102, 
//									103, 
//									104, 
//									105, 
//									106, 
//									array());	
//
//// 幸运抽奖被抽中
//MailTemplate::sendChanlledgeLuckyPrize(49806, 101, 102, 103, 104, 105, 106, array());
//
//// 超级幸运奖被抽中
//MailTemplate::sendChanlledgeSuperLuckyPrize(49806, 201, 202, 203, 204, 205, 206, array());
//
//// 奖池发奖
//MailTemplate::sendChanlledgePrizePool(49806, 10000, 110000);

// VIP等级升级
//MailTemplate::sendVipperUpMsg(49806, 10);
//$itemIds = Array(120008 => 1,120009 => 2,120010 => 1);
//MailTemplate::sendMergerServerReward(49806, $itemIds);

// 偷鱼,祝福鱼
//$recieverUid = 20101;
//$thiefAry = array('uid' => 20101,'utid' => 2,'uname' => '54m55Lym5pav6LWb5ouJ',);
//$itemIds = array('item_template_id' => 160001, 'item_num' => 10);
//MailTemplate::sendStealFishMsg($recieverUid, $thiefAry, $itemIds);
//
//$recieverUid = 20101;
//$thiefAry = array('uid' => 74116,'utid' => 2,'uname' => '54m55Lym5pav6LWb5ouJ',);
//MailTemplate::sendStolenFishMsg($recieverUid, $thiefAry, $itemIds);
//
//$wishAry = array('uid' => 20101,'utid' => 2,'uname' => '54m55Lym5pav6LWb5ouJ',);
//$time = 60;
//$itemTemplateId = 160001;
//MailTemplate::sendWishFishMsg($recieverUid, $wishAry, $itemTemplateId, $time);
//
//$recieverUid = 20101;
//$wishAry = array('uid' => 74116,'utid' => 2,'uname' => '54m55Lym5pav6LWb5ouJ',);
//$itemTemplateId = 160001;
//$time = 90;
//MailTemplate::sendWishedFishMsg($recieverUid, $wishAry, $itemTemplateId, $time);

$reward = array(
				'rank' => 1,
				'score' => 100,
				'honour' => 99,
				'belly' => 10000,
				'experience' => 100000,
				'prestige' => 1000,
				'items' => array()
);
MailTemplate::sendGroupWarReward(21300, $reward);
echo "send mail end\n";
