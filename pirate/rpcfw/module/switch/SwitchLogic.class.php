<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: SwitchLogic.class.php 40052 2013-03-06 07:13:12Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/switch/SwitchLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-06 15:13:12 +0800 (三, 2013-03-06) $
 * @version $Revision: 40052 $
 * @brief 
 *  
 **/




/**
 * 其实data0, data1， 等还不如搞一个字符串，简单一点点，效率也不差。
 * 只多了几个字节
 * Enter description here ...
 * @author idyll
 *
 */
class SwitchLogic
{
	private static $arrField = array('data0', 'data1', 'data2');
	private static $arrRewardField = array('reward0', 'reward1', 'reward2');

	const WIDTH = 25;
	
	public static function get()
	{
		$uid = RPCContext::getInstance()->getUid();
		$arrRet = RPCContext::getInstance()->getSession('switch.info');
		if ($arrRet!==null)
		{
			return $arrRet;
		}
		
		$arrRet = SwitchDao::get($uid, self::$arrField);
		if (empty($arrRet))
		{
			self::insertDefault($uid);
			$arrRet = SwitchDao::get($uid, self::$arrField);
		}

		RPCContext::getInstance()->setSession('switch.info', $arrRet);
		return $arrRet;
	}
	
	public static function fixSwitch()
	{
		$info  = RPCContext::getInstance()->getSession('switch.info');
		if (empty($info))
		{
			$info = self::get();
		}
		
		$isModify = false;
		$taskMgr = TaskManager::getInstance();
		$cfg = btstore_get()->SWITCH;
		foreach ($cfg as $taskId => $arrSwitch)
		{
			foreach($arrSwitch as $switch)
			{
				$status = $switch['status'];
				$type = $switch['type'];
				
				//忽略已开启
				if (self::isOpen__($type, $info))
				{
					continue;
				}

				$complete = $taskMgr->isComplete(array($taskId));
				//已经完成， 都设置为已开启功能
				if ($complete)
				{
					//fix
					Logger::warning('fix switch %s', $type);
					self::setValue_($info, $type, 'data');
					$isModify = true;
				}
				else 
				{
					//接受任务就开启的功能的
					if ($status==TaskStatus::ACCEPT && $taskMgr->isAccept($taskId))
					{
						//fix
						Logger::warning('fix switch %s', $type);
						self::setValue_($info, $type, 'data');
						$isModify = true;
					}
				}		
			}
		}
		
		if ($isModify)
		{
			$uid = RPCContext::getInstance()->getUid();
			SwitchDao::update($uid, $info);
			RPCContext::getInstance()->setSession('switch.info', $info);
		}		
		return $isModify;		
	}
	
	public static function taskStausChange($taskId, $status)
	{
		Logger::debug('task %d change to status %d', $taskId, $status);
		
		$uid = RPCContext::getInstance()->getUid();
		$cfg = btstore_get()->SWITCH;

		if (!isset($cfg[$taskId]))
		{
			return;
		}
		
		$arrType = array();
		foreach ($cfg[$taskId] as $typeStatus)
		{
			if ($status!=$typeStatus['status'])
			{
				continue;
			}
			$arrType[] = $typeStatus['type'];
		}
		
		if (!empty($arrType))
		{
			Logger::debug('update switch, set type %s', $arrType);
			self::setArrValue($arrType);
		}

	}
	
	private static $arrOtherSwitch = array();

	public static function isOpen($type, $uid=0, $prekey='data')
	{
		if ($uid==0 || $uid==RPCContext::getInstance()->getUid())
		{
			$info = self::get();
		}
		else if (isset(self::$arrOtherSwitch[$uid])) 
		{
			$info = self::$arrOtherSwitch[$uid];	
		}
		else
		{			
			$info = SwitchDao::get($uid, self::$arrField);
			if (empty($info))
			{
				return false;
			}
			self::$arrOtherSwitch[$uid] = $info;
		}
		return self::isOpen__($type, $info, $prekey);
	}
	
	public static function reward($type)
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());
		if (!self::isOpen($type))
		{
			Logger::warning('fail to reward switch, %d is not open', $type);
			throw new Exception('fake');
		}
		
		//检查是否领取过
		$uid = RPCContext::getInstance()->getUid();	
		$arrReward = self::getRewardInfo($uid);
		if (self::isOpen__($type, $arrReward, 'reward'))
		{
			Logger::warning('fail to switchReward, the reward is took');
			throw new Exception('fake');
		}	

		if (!isset(btstore_get()->SWITCH_REWARD[$type]))
		{
			Logger::warning('fail to switchReward， the type %s isnot exist', $type);
			throw new Exception('close');
		}
		
		$cfg = btstore_get()->SWITCH_REWARD[$type];
		$user = EnUser::getUserObj();
		
		foreach ($cfg as $key => $value)
		{
			switch ($key)
			{
				case 'belly':
					$user->addBelly($value);
					$arrRet['res']['belly'] = $value; 
					break;
				case 'experience':
					$user->addExperience($value);
					$arrRet['res']['experience'] = $value; 
					break;
				case 'arrItem':
					if (!empty($value))
					{
						$bagMgr = BagManager::getInstance()->getBag();
						if (!$bagMgr->addItems($value, true))
						{
							return 'bag_full';
						}
						$arrRet['res']['grid'] = $bagMgr->update();
					}					
			}
		}
		$user->update();
		
		//保存到数据库	
		self::setValue_($arrReward, $type, 'reward');
		SwitchDao::update($uid, $arrReward);
		
		return $arrRet;
	}
	
	private static function isOpen__($type, $info, $prekey='data')
	{
		list($key, $pos) = self::getKeyAndPos($type, $prekey);
		$data = $info[$key];
		$value = $data & ( 1 << $pos);
		return $value != 0;
	}
	
	public static function getArr()
	{
		$info = self::get();
		$arr = array();
		$index = 0;
		foreach (self::$arrField as $field)
		{
			$data = $info[$field];
			for($pos=0; $pos<self::WIDTH; $pos++)
			{
				$bit = 1 << $pos;
				$value = $data & $bit;
				if ($value!=0)
				{
					$arr[] = $index * self::WIDTH + $pos +1; 	
				}
			}						
			$index++;
		}
		return $arr;		
	}
	
	public static function getArrReward($uid)
	{
		$info = self::getRewardInfo($uid);
		$arr = array();
		$index = 0;
		foreach (self::$arrRewardField as $field)
		{
			$data = $info[$field];
			for($pos=0; $pos<self::WIDTH; $pos++)
			{
				$bit = 1 << $pos;
				$value = $data & $bit;
				if ($value!=0)
				{
					$arr[] = $index * self::WIDTH + $pos +1; 	
				}
			}						
			$index++;
		}
		return $arr;		
	}

	private static function getKeyAndPos($type, $prekey)
	{
		$key = $prekey . floor(($type-1) / self::WIDTH);
		$pos = ($type-1) % self::WIDTH;
		return array($key, $pos);
	}

	public static function setArrValue($arrType)
	{
		$info = self::get();
		$isModify = false;
		foreach ($arrType as $type)
		{
			if (self::setValue_($info, $type, 'data'))
			{
				$isModify = true;
			}
		}
		if (!$isModify)
		{
			return;
		}
		RPCContext::getInstance()->setSession('switch.info', $info);
		$uid = RPCContext::getInstance()->getUid();
		SwitchDao::update($uid, $info);
	}
	
	//返回是否修改
	private static function setValue_(&$info, $type, $prekey)
	{
		list($key, $pos) = self::getKeyAndPos($type, $prekey);
		$data = $info[$key];	
		$value = 1;
		$value <<= $pos;

		//已经设置了值
		if (($value & $data)!=0)
		{
			return false;
		} 
		else if ($prekey=='data')
		{
			switch ($type)
			{
				case SwitchDef::TREASURE:
					EnTreasure::openTreasure();
					break;
				case SwitchDef::SOUL:
					EnSoul::openSoul();
					break;
				case SwitchDef::ALLBLUE:
					EnAllBlue::initAllBlueCollectTime();
					break;
			}
		}

		$data |= $value;
		$info[$key] = $data;
		return true;
	}
	
	public static function setValue($type)
	{
		$info = self::get();
		if (!self::setValue_($info, $type, 'data'))
		{
			return;
		}

		RPCContext::getInstance()->setSession('switch.info', $info);
		$uid = RPCContext::getInstance()->getUid();
		SwitchDao::update($uid, $info);
	}	

	private static function insertDefault($uid)
	{
		$arrInsert = array();
		foreach (self::$arrField as $field)
		{
			$arrInsert[$field] = 0;
		}
		foreach (self::$arrRewardField as $field)
		{
			$arrInsert[$field] = 0;
		}
		SwitchDao::insert($uid, $arrInsert);
	}
	
	private static function getRewardInfo($uid)
	{
		$arrRet = SwitchDao::get($uid, self::$arrRewardField);
		if (empty($arrRet))
		{
			self::insertDefault($uid);
			$arrRet = SwitchDao::get($uid, self::$arrRewardField);
		}

		return $arrRet;
	}

	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */