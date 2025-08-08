<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readTask.php 15007 2012-02-28 02:24:25Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/task/script/readTask.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-02-28 10:24:25 +0800 (二, 2012-02-28) $
 * @version $Revision: 15007 $
 * @brief 
 * 
 **/

require_once ('/home/pirate/rpcfw/def/Task.def.php');

if ($argc < 2)
{
	exit("argv error");
}

//输出错误用
$line = 0;
function exit_msg($msg)
{
	global $line;
	exit("csv file line:$line.\nerror msg:$msg\n");	
}

//处理完成条件key，
function getCompleteKey ($kv)
{
	if (empty($kv['key']))
	{
		return array();
	}
	
	$arr = explode('|', $kv['key']);
//	foreach ($arr as &$v)
//	{
//		if (strlen($v) > 0)
//		{
//			if ($v[0] == 'A')
//			{
//				$v = 10000 + intval(substr($v, 1, strlen($v) - 1));
//			}
//			else if ($v[0] == 'B')
//			{
//				$v = 100000 + intval(substr($v, 1, strlen($v) - 1));
//			}
//		}
//	}
//	unset($v);
	return $arr;
}

//从 | 分割的字符解析为数组
function getArrInt ($key, $kv)
{
	if (!isset($kv[$key]))
	{
		exit_msg("getArrInt error:$key");
	}
	
	if (empty($kv[$key]))
	{
		return array();
	}
	$arr = explode('|', $kv[$key]);
	return array_map('intval', $arr);
}

function getArrStr($key, $kv)
{
	if (!isset($kv[$key]))
	{
		exit_msg("getArrStr error:$key");
	}
	
	if (empty($kv[$key]))
	{
		return array();
	}
	$arr = explode('|', $kv[$key]);
	return $arr;
}

//完成条件处理
//返回array(完成条件,掉落表)
function parseComplete ($type, $kv)
{
	$comKey = getCompleteKey($kv);
	
	$count = getArrInt('count', $kv);
	$value1 = getArrInt('value1', $kv);
	$value2 = getArrInt('value2', $kv);
	$value3 = getArrInt('value3', $kv);
	$dropTableId = getArrInt('dropTableId', $kv);
	
	//key=>数量
	$completeNum = array();
	
	//armyId => droptableId
	$arrDropTable = array();

	switch ($type)
	{
		//打败部队
		//上交物品
		//进行某次操作
    case TaskCompleteType::BEAT_ARMY :
    case TaskCompleteType::ITEM :
    case TaskCompleteType::OPERATE :
        for($i = 0; $i < count($comKey); ++$i)
        {
            //xxx => 数量
            $completeNum[$comKey[$i]] = $count[$i];
        }
        break;
		//击败部队取物品
    case TaskCompleteType::BEAT_ARMY_ITEM :
        for($i = 0; $i < count($comKey); ++$i)
        {
            //上交物品 => 数量
            $completeNum[$value1[$i]] = $count[$i];            
            if (empty($dropTableId))
            {
                exit_msg("droptableId empty\n");
            }
            
            //armyId => dropTableId
            $arrDropTable[$comKey[$i]] = $dropTableId[$i];
        }
        break;
		//寻人
    case TaskCompleteType::FIND_NPC :
        break;
		
		//击败部队			
    case Taskcompletetype::BEAT_ARMY_LEVEL :
        //id => array(count, 评价）
        $value1 = getArrStr('value1', $kv);
        if (empty($value1))
        {
            exit("value1 empty\n");
        }

        for($i = 0; $i < count($comKey); ++$i)
        {
            //次数 评价 
            if (empty($count))
            {
                exit_msg("count empty\n");
            }
            $completeNum[$comKey[$i]] =  array($count[$i], $value1[$i]);
            
            if (!empty($dropTableId))
            {
            	//armyId => dropTableId
               $arrDropTable[$comKey[$i]] = $dropTableId[$i];
            }
        }
        break;
		
		//建筑升级
		//人物属性
    case TaskCompleteType::BUILDING_UPGRADE :
    case TaskCompleteType::USER_PROPERTY :
        for($i = 0; $i < count($comKey); ++$i)
        {
            //xx => 数值
			if (!isset($value1))
			{
				$value1[$i];
				exit_msg('valu1 跟 key 的个数不一致。');
			}
            $completeNum[$comKey[$i]] = $value1[$i];
        }
        break;
		
		//hero升级
    case TaskCompleteType::HERO_UPGRADE :
        for($i = 0; $i < count($comKey); ++$i)
        {
            //htid => 等级
            $completeNum[$comKey[$i]] = $value1[$i];
        }
        break;
		
		//连续登录
    case TaskCompleteType::LOGIN :
    	$totalLogin = 0;
    	$continousLogin = 0;
    	if (isset($value1[0]))
    	{
    		$totalLogin = $value1[0]; 
    	}
		if (isset($value2[0]))
    	{
    		$continousLogin = $value2[0]; 
    	}
        if ($totalLogin xor $continousLogin)
        {
            exit_msg("value1 or value2 error");
        }
        $completeNum = array($totalLogin, $continousLogin);
        break;
    default :
        var_dump($kv);
        exit_msg("unsupport taskType:$type");
	}
	return array($completeNum, $arrDropTable);
}

//解析接受条件
function parseAcceptCondition ($mainType, $kv)
{
	$condition = array();
	
	$conLevel = getArrInt('conLevel', $kv);
	$conGender = getArrInt('conGender', $kv);
	$conPrestige = getArrInt('conPrestige', $kv);
	$conSuccess = getArrInt('conSuccess', $kv);
	$conCopy = getArrInt('conCopy', $kv);
	$conBeginPeriod = $kv['conBeginPeriod'];
	$conEndPeriod = $kv['conEndPeriod'];
	$preTaskId = getArrInt('preTaskId', $kv);
	
	if (!empty($conLevel))
	{
		$condition[TaskAcceptType::LEVEL] = $conLevel;
	}
	
	if (!empty($conGender))
	{
		$condition[TaskAcceptType::GENDER] = $conLevel;
	}
	
	if (!empty($conPrestige))
	{
		$condition[TaskAcceptType::PRESTIGE] = $conPrestige;
	}
	
	if (!empty($conSuccess))
	{
		$condition[TaskAcceptType::SUCCESS] = $conSuccess;
	}
	
	if (!empty($conBeginPeriod))
	{
		$condition[TaskAcceptType::PERIOD] = array(strtotime($conBeginPeriod), strtotime($conEndPeriod));
	}
	
	if (!empty($preTaskId))
	{
		$condition[TaskAcceptType::PRE_TASK_ID] = $preTaskId;
	}
	
	$condition[TaskAcceptType::IS_REWARD] = false;
	
	return $condition;
}

// A1|A2
function getHeroArr($key, $kv)
{
	throw new Exception('del');
//	$ret = array();
//	$retstr = getArrStr($key, $kv);
//	foreach ($retstr as $value)
//	{
//		if (strlen($value)<2 || $value[0]!='A')
//		{
//			exit_msg('hero id error');
//		}
//		//$htid = 10000 + intval(substr($value, 1, strlen($value) - 1));
//		$ret[] = $htid;
//	}
//	return $ret;
}

//解析奖励
function parseReward ($kv)
{
	$arrRewardType = array();
	$rewardKey = array(TaskRewardType::BELLY => 'reward_beili', 
                       TaskRewardType::EXPERIENCE => 'reward_yueli',
                       TaskRewardType::PRESTIGE => 'reward_weiwang',
                       TaskRewardType::EXP => 'reward_jingyan',
                       TaskRewardType::FOOD => 'reward_shiwu',
                       TaskRewardType::TITLE => 'reward_chengwei',
                       TaskRewardType::DROPTABLE_ID => 'rewardDroptableId',
                       TaskRewardType::TASK_ID => 'rewardTaskId',
                       TaskRewardType::HERO => 'reward_yingxiong');
	
	foreach ($rewardKey as $type=>$key)
	{
		//taskId title hero可能为数组		
		if ($type == TaskRewardType::TASK_ID)
		{
			$ret = getArrInt($key, $kv);
			$arrRewardType[$type] = $ret;
		}
		else if ($type==TaskRewardType::HERO)
		{
			$ret = getArrInt($key, $kv);
			$arrRewardType[$type] = $ret;
		}
		else if ($type == TaskRewardType::TITLE)
		{
			$value = $kv['reward_chengwei'];
			if (empty($value))
			{
				$arrRewardType[$type] = array();
			}
			else
			{
				$arrRewardType[$type] = explode('|', $value);
			}
		}
		else
		{
			$v = intval($kv[$key]);
			$arrRewardType[$type] = $v;
		}
	}
	
	if ($kv['reward_fangshi'] == 1)
	{
		$arrRewardCount = TaskCountReward::REWARD_FIXED;
	}
	else if ($kv['reward_fangshi'] == 2)
	{
		$arrRewardCount = TaskCountReward::REWARD_LEVEL;
	}
	//			else
	//	{
	//		$arrRewardType = array();
	//		$arrRewardCount[0] = TaskCountReward::REWARD_LEVEL;
	//	}
	else
	{
		exit("error reward_fangshi");
	}
	
	return array('count' => $arrRewardCount, 'type' => $arrRewardType);
}

$fileName = $argv[1] . '/task.csv';
$handle = fopen($fileName, "r") or exit("fail to $fileName\n");

//忽略第一行
$data = fgetcsv($handle);

//第二行为key值
$arrKey = fgetcsv($handle);
$arrKey = array_map("trim", $arrKey);

//简单值类型的key,都为整数 
$arrNeedKeyInt = array('taskId', 'acceptNpcId', 'comNpcId', 'mainType', 'taskType', 'repeatNum', 'abandon', 'nextTaskId');

//解析
$allTask = array();
$rewardTaskId = array();
while ( ($data = fgetcsv($handle)) != false )
{
	$line++;
	
	$data = array_map('trim', $data);
	$kv = array_combine($arrKey, $data);
	
	if ($kv['taskId']==0)
	{
		exit_msg('taskId equal 0');
	}
	
	if (isset($allTask[$kv['taskId']]))
	{
		exit_msg($kv['taskId'] . " duplicate");
	}		
	
	//把固定值放到数组中
	foreach ($arrNeedKeyInt as $key)
	{
		if (!array_key_exists($key, $kv))
		{
			var_dump($key);
			var_dump($arrKey);
			var_dump($kv);
			exit_msg("error\n");
		}
		$task[$key] = intval($kv[$key]);
		if ($task[$key] == 0)
		{
			//exit("error $key:" . $kv[$key]);
		}
	}
	
	//处理接受条件
	$task['condition'] = parseAcceptCondition($task['mainType'], $kv);
	
	//处理完成条件
	list($task['complete'], $task['dropTable']) = parseComplete($task['taskType'], $kv);
	
	if ($task['taskType'] == TaskCompleteType::LOGIN)
	{
		if (!isset($task['condition'][TaskAcceptType::PERIOD]))
		{
			exit_msg("login task error");
		}
		$beginPeriod = intval(strftime("%Y%m%d", $task['condition'][TaskAcceptType::PERIOD][0]));
		$endPeriod = intval(strftime("%Y%m%d", $task['condition'][TaskAcceptType::PERIOD][1]));
		$task['complete'][] = array($beginPeriod, $endPeriod);
	}
	
	
	//广播
	$task['comBroadcast'] = intval($kv['comBroadcast']);
	$task['comBroadcastContent'] = $kv['comBroadcastContent'];
	
	//处理奖励
	$task['reward'] = parseReward($kv);
	
	//是否关闭，如果已经关闭，忽略
	if ($kv['isClose']==0)
	{
		$task['isClose'] = false;
	}
	else
	{
		$task['isClose'] = true;
	}
	
	//$task['reward']['isReward'] = 
	//var_dump($task['reward']);
	$rewardTaskId = array_merge($rewardTaskId, $task['reward']['type'][TaskRewardType::TASK_ID]);		
	
	$allTask[$task['taskId']] = $task;
}
fclose($handle);

//把奖励任务的接受条件设置为true，表示不可接受
$rewardTask = array();
$rewardTaskId = array_unique($rewardTaskId);
foreach ($rewardTaskId as $taskId)
{
	if ($taskId!=0)
	{
		if (!isset($allTask[$taskId]))
		{
			exit('taskId:' .$taskId. ' 不存在或者或者已经关闭');
		}
		$allTask[$taskId]['condition'][TaskAcceptType::IS_REWARD] = true;
	}	
}

//var_dump($allTask);

$outputPath =  $argv[2];

{
	$ret = serialize($allTask);
	//echo $ret;
	$h = fopen($outputPath . "/TASKS", "w");
	fwrite($h, $ret);
	fclose($h);
}

//else if($argv[2]=="accept")
{
	foreach ($allTask as $taskId=> $task)
	{
		$acceptNpc[$task["acceptNpcId"]][] = $task['taskId'];
	}
	//var_dump($acceptNpc);
	$ret = serialize($acceptNpc);
	$h = fopen($outputPath . "/ACCEPT_NPC", "w");
	fwrite($h, $ret);
	fclose($h);
}

//else if($argv[2]=="com")
{
	foreach ($allTask as $task)
	{
		$comNpc[$task["comNpcId"]][] = $task['taskId'];
	}
	//var_dump($acceptNpc);
	$ret = serialize($comNpc);
	$h = fopen($outputPath . "/COMPLETE_NPC", "w");
	fwrite($h, $ret);
	fclose($h);
}

echo "all taskId:";

var_dump(array_keys($allTask));

var_dump("<br>success.");


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */