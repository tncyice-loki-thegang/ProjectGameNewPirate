<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyAbyssCopy.class.php 40940 2013-03-19 07:04:44Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/MyAbyssCopy.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-19 15:04:44 +0800 (二, 2013-03-19) $
 * @version $Revision: 40940 $
 * @brief 
 *  
 **/


/**
 * 存储单个用户的相关数据
 *
 */
class MyAbyssCopy
{
	private $uid = 0;

	private $dbData;
	
	private $dbDataModify;
	
	public static $AllField = array(
			'uid',
			'week_buy_num',
			'left_clg_num',
			'left_exe_num',
			'last_enter_time',
			'cur_copy_uuid',
			'va_abyss' );

	
	function __construct($uid)
	{
		$this->uid = $uid;
		
		if(!empty($this->dbData))
		{
			return;
		}
		
		$this->dbData = AbyssCopyDAO::getByUid($this->uid, self::$AllField);
		
		if (empty($this->dbData))
		{
			$this->dbData = self::insertDefault($this->uid);
		}
		
		$this->dbDataModify = $this->dbData;
		
		$this->init();
	}
	
	public function init()
	{
		$offset = FrameworkConfig::WEEK_SECOND - 86400*2; 	//周六早上4点刷新 
		$weeks = self::getWeeksBetween($this->dbDataModify['last_enter_time'], $offset);
		if( $weeks > 0 )
		{			
			$userConf = AbyssCopy::getUserConf();

			$this->dbDataModify['week_buy_num'] = 0;
			$this->dbDataModify['left_exe_num'] = $userConf['baseExerciseNum'];
 
			$this->dbDataModify['left_clg_num'] += $userConf['baseChallengeNum'] * $weeks;
			if ($this->dbDataModify['left_clg_num'] > $userConf['maxChallengeNum'])
			{
				$this->dbDataModify['left_clg_num'] = $userConf['maxChallengeNum'];
			}			
		}
	}
	
	
	public function canEnterCopy($copyId)
	{
		//是否开启功能节点
		if (!EnSwitch::isOpen(SwitchDef::ABYSS_COPY))
		{
			Logger::warning('cant enter abyss, switch');
			throw new Exception('fake');
		}		
		
		$conf = AbyssCopy::getCopyConf($copyId);
		if( !isset($conf))
		{
			Logger::info('no copy:%d', $copyId);
			throw new Exception('fake');
		}

		//没有挑战次数的话，不让进
		if( $this->dbDataModify['left_clg_num'] == 0 &&  $this->dbDataModify['left_exe_num'] == 0 )
		{
			Logger::debug('uid:%d cant enter copy, no num', $this->uid);
			return false;
		}

		//没有达到开启条件的话，不让进
		if( $conf['preAbyssCopyId'] > 0 &&
				!in_array( $conf['preAbyssCopyId'], $this->dbDataModify['va_abyss']['passed'])  ) 
		{
			Logger::debug('uid:%d cant enter copy, preAbyssCopyId:%d', $this->uid, $conf['preAbyssCopyId']);
			return false;
		}
		
		if( $conf['preArmyId'] > 0 && 
				! CopyLogic::isEnemyDefeated($conf['preArmyId'])  ) 
		{
			Logger::debug('uid:%d cant enter copy, preArmyId:%d', $this->uid, $conf['preArmyId']);
			return false;
		}
	
		return true;
	}
	
	public function joinCopy($copyUUID)
	{
		$this->dbDataModify['cur_copy_uuid'] = $copyUUID;	
		$this->dbDataModify['last_enter_time'] = Util::getTime();
	
		if($this->uid == RPCContext::getInstance()->getUid())
		{
			RPCContext::getInstance()->setSession(AbyssCopyDef::SESSION_COPY_UUID, $copyUUID);
		}
	}
	
 	public function isExercise()
 	{
 		return $this->dbDataModify['left_clg_num'] <= 0;
 	}
 	
	public function leaveCopy()
	{				
		$this->dbDataModify['cur_copy_uuid'] = 0;
			
	}
	
	public function passCopy($copyId)
	{
		if($this->dbDataModify['left_clg_num'] > 0)
		{
			$this->dbDataModify['left_clg_num']--;
		}
		else if($this->dbDataModify['left_exe_num'] > 0)
		{
			$this->dbDataModify['left_exe_num']--;
		}
		else
		{
			Logger::fatal('no challenge or exercise num');
		}
		
		if( !in_array($copyId, $this->dbDataModify['va_abyss']['passed']) )
		{
			$this->dbDataModify['va_abyss']['passed'][] = $copyId;
			Logger::info('uid:%d pass copy:%d, all:%s', $this->uid, $copyId, $this->dbDataModify['va_abyss']['passed']);
		}
	}
	
	public function buyChallengeNum($num)
	{
		$this->dbDataModify['left_clg_num'] += $num;
		$this->dbDataModify['week_buy_num'] += $num;
		$this->dbDataModify['last_enter_time'] = Util::getTime();	//买过一次后需要把这个设置一下
		return $this->dbDataModify['left_clg_num'];
	}
	
	public function getInfo()
	{
		$info = array(
				'weekBuyNum' => $this->dbDataModify['week_buy_num'],
				'weekClgNum' => $this->dbDataModify['left_clg_num'],
				'weekExeNum' => $this->dbDataModify['left_exe_num'],
				'curCopyUUID' => $this->dbDataModify['cur_copy_uuid'],
				'passed' => $this->dbDataModify['va_abyss']['passed'],
		);		
		
		return $info;
	}	

	
	public function update()
	{
		$arrField = array();
		foreach ($this->dbData as $key => $value)
		{
			if ($this->dbDataModify[$key]!= $value)
			{
				$arrField[$key] = $this->dbDataModify[$key];
			}
		}
		if(! empty($arrField) )
		{
			AbyssCopyDAO::update($this->dbDataModify['uid'], $arrField);
		}
	}
	
	/**
	 * 从session中取当前用户所在深渊本UUID， 如果session里面没有，就从DB中取一下
	 * @throws Exception
	 * @return int $copyUUID
	 */
	public static function getCopyUUID()
	{
		$uid = RPCContext::getInstance()->getUid();
		$copyUUID = RPCContext::getInstance()->getSession(AbyssCopyDef::SESSION_COPY_UUID);
		
		if(empty($copyUUID))
		{			
			$obj = new MyAbyssCopy($uid);
			
			$info = $obj->getInfo();
			$copyUUID = $info['curCopyUUID'];
			
			if($copyUUID == 0)
			{
				Logger::info('uid:%d never join copy', $uid);
				throw new Exception('fake');
			}
			//此处不能把copyUUID设置到session中，在AbyssCopy中根据session中是否有此key来判断，需不需要addListener
		}
		
		return $copyUUID;		
	}

	
	public static function insertDefault($uid)
	{

		$values = array(
				'uid' => $uid,
				'week_buy_num' => 0 ,
				'left_clg_num' => 0,
				'left_exe_num' => 0,
				'last_enter_time' => Util::getTime()-86400*7,
				'cur_copy_uuid' => 0,
				'va_abyss' => array(
						'passed' => array()
						) 
				);
		
		AbyssCopyDAO::insert($uid, $values);
		return $values;
	}
	
	
	public static function getWeeksBetween($checkTime, $offset = FrameworkConfig::WEEK_SECOND)
	{
		$curTime = Util::getTime ();
		
		$SECONDS_OF_WEEK = 604800;
		
		//这个时间为周日的晚上
		//$s = "1970-2-28 23:59:59";
		//$BASE_TIME = strtotime($s);
		$BASE_TIME = 5155199;
		
		$checkTime -= ($BASE_TIME + $offset);
		$curTime -= ($BASE_TIME + $offset);
		
		$checkWeek = intval ( $checkTime / $SECONDS_OF_WEEK );
		$curWeek = intval ( $curTime / $SECONDS_OF_WEEK );
		
		return $curWeek - $checkWeek;
	}
}


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */