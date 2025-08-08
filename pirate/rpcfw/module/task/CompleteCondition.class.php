<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

/**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 * 
 **/




class CompleteCondition
{
	/**
	 * 
	 * Enter description here ...
	 * @var TaskManager
	 */
	private $taskMgr;
	private $uid;
	const DAY_OF_SECONDS = 86400;
	public function __construct ($taskMgr)
	{
		$this->taskMgr = $taskMgr;
		$this->uid = $this->taskMgr->getUid();
	}
	
	/**
	 * 检查数据，并且把保存在其它模块的数据拉过来，修改curData
	 * Enter description here ...
	 * @param 任务唯一标识符 $kid
	 * @param 任务完成类型 $type
	 * @param 完成条件 $condition
	 * @param 但前数据 $curData
	 * @return array
	 * 1: bool 能提交
	 * 2：bool 数据有更新
	 * 3 : bool 是否必须放弃 （如连续登录任务过期）
	 */
	public function checkAndUpdate ($kid, $type, $comCondition, &$curData)
	{
		$RetCanSubmit = true;
		$RetModify = false;
		$RetAbandon = false;
		
		$condition = $comCondition;
		$dataBuf = $this->taskMgr->getDataBuf();
		$dataByType = array();
		switch ($type)
		{
			//打败部队
			case TaskCompleteType::BEAT_ARMY :
				if (isset($dataBuf[TaskDataType::ARMY]))
				{
					$dataByType = $dataBuf[TaskDataType::ARMY];
				}
				list($modify, $newData) = $this->CheckAndGetNewData($kid, $dataByType, TaskDataType::ARMY, $curData, $condition);
				if ($modify)
				{
					$RetModify = true;
					$curData = $newData;
					TaskDao::update($kid, $this->uid, array('va_task' => $curData));
				}
				$canSubmit = $this->check(TaskDataType::ARMY, $condition, $curData);
				if (!$canSubmit)
				{
					$RetCanSubmit = false;
				}
				break;
			//操作
			case TaskCompleteType::OPERATE :
				if (isset($dataBuf[TaskDataType::OPERATE]))
				{
					$dataByType = $dataBuf[TaskDataType::OPERATE];
				}
				
				list($modify, $newData) = $this->CheckAndGetNewData($kid, $dataByType, TaskDataType::OPERATE, $curData, $condition);
				if ($modify)
				{
					$RetModify = true;
					$curData = $newData;
					TaskDao::update($kid, $this->uid, array('va_task' => $curData));
				}
				$canSubmit = $this->check(TaskDataType::OPERATE, $condition, $curData);
				if (!$canSubmit)
				{
					$RetCanSubmit = false;
				}
				
				break;
			//打败某部队
			case TaskCompleteType::BEAT_ARMY_LEVEL :
				$modify = false;
				$canSubmit = true;
				if (isset($dataBuf[TaskDataType::BEAT_ARMY_LEVEL]))
				{
					$dataByType = $dataBuf[TaskDataType::BEAT_ARMY_LEVEL];
				}
				
				//用于此任务的数据
				$dataTheTask = array();
				//打败部队的时候看评价是否大于要求的等级
				foreach ($dataByType as $arrBeatArmy)
				{
					//arrBeatArmy : id,resLevel
					list($armyId, $resLevel) = $arrBeatArmy;
					//战斗没在此任务中
					if (!isset($condition[$armyId]))
					{
						continue;
					}
					list($needNum, $needResLevel) = $condition[$armyId];
					//评价不够
					if ( BattleDef::$APPRAISAL[$resLevel] > BattleDef::$APPRAISAL[$needResLevel])
					{
						continue;
					}
					
					if (isset($dataTheTask[$armyId]))
					{
						$dataTheTask[$armyId] += 1;
					}
					else
					{
						$dataTheTask[$armyId] = 1;
					}			
				}
				
				//id=>次数
				$beatArmyCondition = array();
				foreach ($condition as $armyId => $arrCountLevel)
				{
					$beatArmyCondition[$armyId] = $arrCountLevel[0];
				}
				
				list($modify, $newData) = $this->CheckAndGetNewData($kid, $dataTheTask, TaskDataType::BEAT_ARMY_LEVEL, $curData, $beatArmyCondition);
				if ($modify)
				{
					$RetModify = true;
					$curData = $newData;
					TaskDao::update($kid, $this->uid, array('va_task' => $curData));
				}
				$canSubmit = $this->check(TaskDataType::BEAT_ARMY_LEVEL, $beatArmyCondition, $curData);
				
				if (!$canSubmit)
				{
					$RetCanSubmit = false;
				}
				break;
			
			//击败部队取物
			case TaskCompleteType::BEAT_ARMY_ITEM :
			//上交物品
			case TaskCompleteType::ITEM :
				$bag = BagManager::getInstance()->getBag();
				$curItem = array();
				foreach ($condition as $key=>$num)
				{
					$itemNum = $bag->getItemNumByTemplateID($key);
					$curItem[$key] = $itemNum;
				}
				Logger::debug('get item info from bag:%s', $curItem);
				list($modify, $newData) = $this->CheckAndGetNewData($kid, $curItem, TaskDataType::ITEM, $curData, $condition, false);
				if ($modify)
				{
					$RetModify = true;
					$curData = $newData;
				}
				$canSubmit = $this->check(TaskDataType::ITEM, $condition, $curData);
				if (!$canSubmit)
				{
					$RetCanSubmit = false;
				}
				break;
			
			//找人
			case TaskCompleteType::FIND_NPC :
				//找人直接可提交
				break;
			
			//建筑升级
			case TaskCompleteType::BUILDING_UPGRADE :
				$curLevel = array();
				foreach ($condition as $key=>$num)
				{
					$curLevel[$key] = Sailboat::getCabinLv($key);
				}
				list($modify, $newData) = $this->CheckAndGetNewData($kid, $curLevel, TaskDataType::BUILDING_UPGRADE, $curData, $condition, false);
				
				if ($modify)
				{
					$RetModify = true;
					$curData = $newData;
				}
				$canSubmit = $this->check(TaskDataType::BUILDING_UPGRADE, $condition, $curData);
				if (!$canSubmit)
				{
					$RetCanSubmit = false;
				}
				break;
			
			//人物属性
			case TaskCompleteType::USER_PROPERTY :
				$curProperty = array();
				$user = EnUser::getUser();
				foreach ($condition as $key=>$num)
				{
					//level
					if ($key == 1)
					{
						$curProperty[$key] = $user['level'];
					}
					//prestige_num
					else if ($key == 2)
					{
						$curProperty[$key] = $user['prestige_num'];
					}
					else
					{
						Logger::fatal('unknow user property %d for task', $key);
					}
				}
				list($modify, $newData) = $this->CheckAndGetNewData($kid, $curProperty, TaskDataType::USER_PROPERTY, $curData, $condition, false);
				
				if ($modify)
				{
					$RetModify = true;
					$curData = $newData;
				}
				$canSubmit = $this->check(TaskDataType::USER_PROPERTY, $condition, $curData);
				if (!$canSubmit)
				{
					$RetCanSubmit = false;
				}
				
				break;
			
			//英雄升级
			case TaskCompleteType::HERO_UPGRADE :
				$curLevel = array();
				foreach ($condition as $htid=>$level)
				{
					try
					{
						//返回已招募的英雄
                    	$heroObj = EnUser::getUserObj()->getHeroObjByHtid($htid);
					}
					catch (Exception $e)
					{
						try
						{
							//酒馆英雄等级到了也行， 蛋痛
							//策划靠不住， 所以catch一把
							$heroObj = EnUser::getUserObj()->getPubHeroObj($htid);
						}
						catch(Exception $e)
						{
							Logger::fatal('config err. the user has no hero %d.', $htid);
							$curLevel[$htid] = 0;
							continue;
						}
					}
					$curLevel[$htid] = $heroObj->getLevel();
				}
				list($modify, $newData) = $this->CheckAndGetNewData($kid, $curLevel, TaskDataType::HERO_UPGRADE, $curData, $condition, false);
				
				if ($modify)
				{
					$RetModify = true;
					$curData = $newData;
				}
				$canSubmit = $this->check(TaskDataType::HERO_UPGRADE, $condition, $curData);
				if (!$canSubmit)
				{
					$RetCanSubmit = false;
				}
				break;
			
			//连续登录
			case TaskCompleteType::LOGIN :
				list($needTotalLogin, $needContinousLogin, list($begPeriod, $endPeriod)) = $condition;
				
//				$userInfo = EnUser::getUser();				
				$curTime = Util::getTime();
				$curDate = Util::todayDate();
				if (end($curData)!=$curDate)
				{
					$curData[] = $curDate;	
					$RetModify = true;
				}
				$loginDate = $curData;
				
				$totalLogin = 0;				
				//总计登录多少天
				if ($needTotalLogin!=0)
				{
					$begPeriod = strtotime($begPeriod);
					$endPeriod = strtotime($endPeriod);
					for($i=$begPeriod; $i<=$endPeriod;)
					{
						$tmp = intval(strftime("%Y%m%d", $i));
						if (in_array($tmp, $loginDate))
						{
							$totalLogin++;
						}
						$i += self::DAY_OF_SECONDS;
					}
					
					$curData[0] = array(TaskDataType::LOGIN, 0, $totalLogin);
					
					if ($totalLogin < $needTotalLogin)
					{
						$RetCanSubmit = false;
						//检查是否过期						
						if ($curDate > $endPeriod)
						{
							$RetAbandon = true;
						}
					}
				}
				//连续登录多少天
				else
				{
					//最长的连续登录天数
					$max = 0;
					$len = count($loginDate);
					for($i=0; $i<$len; /**/)
					{
						$j=$i+1;
						while($j<$len && 
							strtotime($loginDate[$j])==(strtotime($loginDate[$j-1])+self::DAY_OF_SECONDS))
						{
							$j++;
						}
						$max = max($max, $j-$i);
						$i = $j;
					}
					
					$curData[0] = array(TaskDataType::LOGIN, 0, $max);
					
					if ($max < $needContinousLogin)
					{
						$RetCanSubmit = false;
						//检查是否过期
						if ($curDate > $endPeriod)
						{
							$RetAbandon = true;
						}
					}
				}
				break;
			
			default :
				Logger::fatal("unknow task complete type:%d", $type);
				return false;
		}
		
		$res = array($RetCanSubmit, $RetModify, $RetAbandon);
		Logger::debug("checkAndUpdate return %s", $res);
		return $res;
	}
	
	//是否能提交
	private function check ($dataType, $condition, $curData)
	{
		foreach ($condition as $key=>$num)
		{
			$found = false;
			foreach ($curData as $value)
			{
				if ($value['type'] == $dataType && $value['key'] == $key)
				{
					$found = true;
					if ($value['value'] < $num)
					{
						return false;
					}
				}
			}
			if (!$found)
			{
				return false;
			}
		
		}
		return true;

	}
	
	/**
	 * 修改数据，并且检查是任务是否能提交
	 * @param array $newData id=>num 新数据
	 * @param uint $dataType TaskDataType 
	 * @param array $curData array(TaskDataType=> , 'key'=> , 'value'=> ) 
	 * @param varry $condition id=>num
	 * @param bool $isAdditon 
	 * true:表示数据为追加，原来的数据加上新数据
	 * false:表示用新数据覆盖现有的数据
	 * @return 是否修改了数据
	 */
	
	private function CheckAndGetNewData ($kid, $newData, $dataType, $curData, $condition, $isAdditon = true)
	{
		$modify = false;
		$canSubmit = true;
		$found = false;
		
		$indexCurData = Util::arrayIndex($curData, 'key');
		foreach ($condition as $id=>$num)
		{
			if (!array_key_exists($id, $indexCurData))
			{
				$indexCurData[$id] = array('type' => $dataType, 'key' => $id, 'value' => 0);
				$modify = true;
			}						
		}
		
		foreach ($newData as $id=>$num)
		{
			if (!array_key_exists($id, $indexCurData))
			{
				//不是当前的任务需要的数据
				continue;
			}
			
			if ($num >= 0)
			{
				if ($isAdditon)
				{
					$modify = true;
					$indexCurData[$id]['value'] += $num;
				}
				else if ($indexCurData[$id]['value'] != $num)
				{
					$modify = true;
					$indexCurData[$id]['value'] = $num;
				}
			}
		}
		
		//判断是否做特殊处理	
		if (isset(self::$specialFunc[$dataType]))
		{
			foreach ($indexCurData as &$tmpData)
			{						
				$typeSpecialFunc = self::$specialFunc[$dataType];
				if (isset($typeSpecialFunc[$tmpData['key']]))
				{
					$func = $typeSpecialFunc[$tmpData['key']];
					$isModify = call_user_func_array($func, array(&$tmpData));
					if ($isModify)
					{
						$modify = true;
					}
				}
			}
			unset($tmpData);
		}		
		$ret = array($modify, array_values($indexCurData));
		Logger::debug('CheckAndGetNewData ret:%s', $ret);
		return $ret;
	}
	
	/*
	 * 	const CAPTAIN_ROOM_OPEN = 29; // 船长室开启
	const SAILOR_ROOM_OPEN = 30; // 水手室开启
	const MEDICAL_ROOM_OPEN = 31; // 医疗室开启
	 * 	const ST_ROOM_OPEN = 32; // 研究院开启
	const PET_ROOM_OPEN = 33; // 宠物室开启
	const KITCHEN_ROOM_OPEN = 34; // 厨房开启
	const CASH_ROOM_OPEN = 35; // 藏金室开启
	const TRAIN_ROOM_OPEN = 36; // 训练室开启
	const TRADE_ROOM_OPEN = 37; // 贸易室开启
	 */
	
	//这里某些类型做蛋痛的特殊处理, 
	// 特殊处理函数输入参数为引用，返回bool 判断是否修改了数据
	private static $specialFunc = array(
		TaskDataType::OPERATE => array( 
			TaskOperateType::FORMATION => array('CompleteCondition', 'checkFormationHero'),
			TaskOperateType::JOIN_OR_CREATE_GUILD => array('CompleteCondition', 'checkGuild'),
			
			TaskOperateType::ST_ROOM_OPEN => array('CompleteCondition', 'checkSailboatSTRoom'),
			TaskOperateType::PET_ROOM_OPEN => array('CompleteCondition', 'checkSailboatPetRoom'),
			TaskOperateType::KITCHEN_ROOM_OPEN => array('CompleteCondition', 'checkSailboatKitchenRoom'),
			TaskOperateType::CASH_ROOM_OPEN => array('CompleteCondition', 'checkSailboatCashRoom'),
			TaskOperateType::TRAIN_ROOM_OPEN => array('CompleteCondition', 'checkSailboatTrainRoom'),
			TaskOperateType::TRADE_ROOM_OPEN => array('CompleteCondition', 'checkSailboatTradeRoom'),	
			TaskOperateType::CAPTAIN_ROOM_OPEN => array('CompleteCondition', 'checkSailboatCaptainRoom'),
			TaskOperateType::SAILOR_ROOM_OPEN => array('CompleteCondition', 'checkSailboatSailorRoom'),
			TaskOperateType::MEDICAL_ROOM_OPEN => array('CompleteCondition', 'checkSailboatMedicalRoom'),		
			),
	);
	
	////阵上有两个英雄的时候，数据改为=1
	private static function checkFormationHero(&$data)
	{
		$arrHid = EnFormation::getFormationHids();
		$num = count($arrHid);		
		if ($num-1 > 0)
		{
			if ($data['value']==0)
			{
				$data['value'] = 1;
				return true;
			}
		}
		return false;		
	}
	
	private static function checkGuild(&$data)
	{
		$uid = RPCContext::getInstance()->getUid();
		$ret = GuildLogic::getMemberInfo($uid);
		if (empty($ret))
		{
			return false;
		}
		
		if ($data['value']==0)
		{
			$data['value'] = 1;
			return true;
		}
		return false;
	}	
	
	private static function checkSailboatSTRoom(&$data)
	{
		if (EnSailboat::isCabinOpen(SailboatDef::SCI_TECH_ID))
		{
			return self::modifySailboatData($data);
		}
		return false;
	}
	
	private static function checkSailboatPetRoom(&$data)
	{
		if (EnSailboat::isCabinOpen(SailboatDef::PET_ID))
		{
			return self::modifySailboatData($data);
		}
		return false;
	}
	
	private static function checkSailboatKitchenRoom(&$data)
	{
		if (EnSailboat::isCabinOpen(SailboatDef::KITCHEN_ID))
		{
			return self::modifySailboatData($data);
		}
		return false;
	}
	
	private static function checkSailboatCashRoom(&$data)
	{
		if (EnSailboat::isCabinOpen(SailboatDef::CASH_ROOM_ID))
		{
			return self::modifySailboatData($data);
		}
		return false;
	}
	
	private static function checkSailboatTrainRoom(&$data)
	{
		if (EnSailboat::isCabinOpen(SailboatDef::TRAIN_ROOM_ID))
		{
			return self::modifySailboatData($data);
		}
		return false;
	}
	
	private static function checkSailboatTradeRoom(&$data)
	{
		if (EnSailboat::isCabinOpen(SailboatDef::TRADE_ROOM_ID))
		{
			return self::modifySailboatData($data);
		}
		return false;
	}
	
	private static function checkSailboatCaptainRoom(&$data)
	{
		if (EnSailboat::isCabinOpen(SailboatDef::CAPTAIN_ROOM_ID))
		{
			return self::modifySailboatData($data);
		}
		return false;
	}
	
	private static function checkSailboatSailorRoom(&$data)
	{
		if (EnSailboat::isCabinOpen(SailboatDef::SAILOR_01_ID))
		{
			return self::modifySailboatData($data);
		}
		return false;
	}
	
	private static function checkSailboatMedicalRoom(&$data)
	{
		if (EnSailboat::isCabinOpen(SailboatDef::MEDICAL_ROOM_ID))
		{
			return self::modifySailboatData($data);
		}
		return false;
	}
	
	private static function modifySailboatData(&$data)
	{
		if ($data['value']==0)
		{
			$data['value'] = 1;
			return true;
		}
		return false;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */