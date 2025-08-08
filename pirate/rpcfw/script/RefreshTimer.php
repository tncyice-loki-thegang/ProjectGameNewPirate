<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/
if (! defined ( 'ROOT' ))
{
	define ( 'ROOT', dirname ( dirname ( __FILE__ ) ) );
	define ( 'LIB_ROOT', ROOT . '/lib' );
	define ( 'EXLIB_ROOT', ROOT . '/exlib' );
	define ( 'DEF_ROOT', ROOT . '/def' );
	define ( 'CONF_ROOT', ROOT . '/conf' );
	define ( 'LOG_ROOT', ROOT . '/log' );
	define ( 'MOD_ROOT', ROOT . '/module' );
	define ( 'HOOK_ROOT', ROOT . '/hook' );
	define ( 'COV_ROOT', ROOT . '/cov' );
}

require_once (MOD_ROOT . '/copy/Activity.class.php');
require_once (MOD_ROOT . '/timer/index.php');
require_once (LIB_ROOT . '/Logger.class.php');
require_once (LIB_ROOT . '/data/index.php');
require_once (DEF_ROOT . '/Framework.def.php');
require_once (CONF_ROOT . '/Framework.cfg.php');
require_once (CONF_ROOT . '/Script.cfg.php');
require_once (LIB_ROOT . '/TimerTask.class.php');


/**
 * 刷新时刻
 */
function refresh()
{
	// 取出所有活动信息
	$actList = btstore_get()->COPY_ACT;
	$timer = new Timer();
	// 听说得告诉前端？
	foreach ($actList as $key => $act)
	{
		// 获取活动开始的时刻
		$nexTime = Activity::getNextStartTime($key);
		if (empty($nexTime))
		{
			continue;
		}
		// 获取刷新点ID
		$rpList = $act['rp_array'];
		foreach ($rpList as $rpID)
		{
			// 添加个任务
			$tid = $timer->addTask(0, $nexTime, 'CopyLogic.getCopyInfo', array(btstore_get()->REFRESH_POINT[$rpID]['copy_id'], false));
			// 留个证据，我真发给何老师了
			Logger::trace('addTimer after %d excute. tid is %d, copyID is %d', $nexTime, $tid, btstore_get()->REFRESH_POINT[$rpID]['copy_id']);
		}
	}

	// 清空服务器的记录
	EnCopy::clearServerFight(0);
}

// 告诉服务器IP地址
RPCContext::getInstance()->getFramework()->initExtern(ScriptConf::PRIVATE_GROUP, '0');


//refresh();




/**********************************************************************************************************************/
// 以下为测试代码
/*

$afterDays = 5;

// 测试加若干天后的日期
echo '== getPlusDays_0 start ======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getPlusDays(time(), $afterDays);

echo 'Today is '.date("Ymd", time()).chr(13).chr(10);
echo 'After '.$afterDays.' days is : ';
var_dump($day);

echo '== getPlusDays_0 end ========================  '.chr(13).chr(10).chr(13).chr(10);

// 测试没有参数时，是否返回自己本身
echo '== getNextDay_0 start =======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getNextDay(time(), array(), array());
echo 'Next day is : ';
var_dump($day);

echo '== getNextDay_0 end =========================  '.chr(13).chr(10).chr(13).chr(10);

// 测试设定日期在今天之后时，是否返回当天
echo '== getNextDay_1 start =======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getNextDay(time(), array(0 => 20, 1 => 25), array());
echo 'Next day is : ';
var_dump($day);

echo '== getNextDay_1 end =========================  '.chr(13).chr(10).chr(13).chr(10);

// 测试设定日期在今天之前时，是否返回当天
echo '== getNextDay_2 start =======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getNextDay(time(), array(0 => 2, 1 => 5), array());
echo 'Next day is : ';
var_dump($day);

echo '== getNextDay_2 end =========================  '.chr(13).chr(10).chr(13).chr(10);

// 测试设定日期在混杂时，是否返回当天
echo '== getNextDay_3 start =======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getNextDay(time(), array(0 => 3, 1 => 5, 2 => 15, 3 => 20, 4 => 25), array());
echo 'Next day is : ';
var_dump($day);

echo '== getNextDay_3 end =========================  '.chr(13).chr(10).chr(13).chr(10);

// 测试设定星期在今天之后时，是否返回当天
echo '== getNextDay_4 start =======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getNextDay(time(), array(), array(0 => 5, 1 => 6));
echo 'Next day is : ';
var_dump($day);

echo '== getNextDay_4 end =========================  '.chr(13).chr(10).chr(13).chr(10);

// 测试设定星期在今天之后时，是否返回当天
echo '== getNextDay_5 start =======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getNextDay(time(), array(), array(0 => 0, 1 => 2));
echo 'Next day is : ';
var_dump($day);

echo '== getNextDay_5 end =========================  '.chr(13).chr(10).chr(13).chr(10);

// 测试设定星期在今天时，是否返回当天
echo '== getNextDay_6 start =======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getNextDay(time(), array(), array(0 => 0, 1 => 2, 2 => 3));
echo 'Next day is : ';
var_dump($day);

echo '== getNextDay_6 end =========================  '.chr(13).chr(10).chr(13).chr(10);

// 测试设定星期在混杂时，是否返回当天
echo '== getNextDay_7 start =======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getNextDay(time(), array(), array(0 => 0, 1 => 2, 2 => 6));
echo 'Next day is : ';
var_dump($day);

echo '== getNextDay_7 end =========================  '.chr(13).chr(10).chr(13).chr(10);

// 测试设定星期，日期时，是否准确
echo '== getNextDay_8 start =======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getNextDay(time(), array(0 => 3, 1 => 5, 2 => 15, 3 => 20, 4 => 25), array(0 => 0, 1 => 2, 2 => 6));
echo 'Next day is : ';
var_dump($day);

echo '== getNextDay_8 end =========================  '.chr(13).chr(10).chr(13).chr(10);

// 测试设定星期，日期时，是否准确
echo '== getNextDay_9 start =======================  '.chr(13).chr(10).chr(13).chr(10);
$day = getNextDay(1316102400, array(0 => 5, 1 => 25), array(0 => 1, 1 => 3, 2 => 4, 3 => 6));
echo 'Next day is : ';
var_dump($day);

echo '== getNextDay_9 end =========================  '.chr(13).chr(10).chr(13).chr(10);

// 获取下一天的开始时刻
echo '== getNextDayStart_0 start ==================  '.chr(13).chr(10).chr(13).chr(10);
$ret = getNextDayStart(time());
echo 'Next day is : ';
var_dump(date("Ymd-His-w", $ret));
echo chr(13).chr(10);

echo '== getNextDayStart_0 end ====================  '.chr(13).chr(10).chr(13).chr(10);

// 测试
echo '== getNextStartTime_0 start =================  '.chr(13).chr(10).chr(13).chr(10);

$actID = 2;
$ret = getNextStartTime($actID);

var_dump(btstore_get()->COPY_ACT[$actID]->toArray());
if (empty($ret))
	echo 'GAME OVER!'.chr(13).chr(10);
else
	var_dump(date("Ymd-His-w", $ret));

echo '== getNextStartTime_0 end ===================  '.chr(13).chr(10).chr(13).chr(10);
*/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */