<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Daytask.class.php 31137 2012-11-16 02:59:46Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/daytask/Daytask.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-11-16 10:59:46 +0800 (五, 2012-11-16) $
 * @version $Revision: 31137 $
 * @brief 
 *  
 **/





class Daytask implements IDaytask
{
	private $uid = 0;
	
	public function __construct()
	{
		if (!EnSwitch::isOpen(SwitchDef::DAYTASK))
		{
			Logger::warning('fail to daytask, switch return false');
		}
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	/* (non-PHPdoc)
	 * @see IDaytask::getInfo()
	 */
	public function getInfo ()
	{
		$arrRet = DaytaskLogic::getInfo();
		$arrRet['target_type'] = $arrRet['va_daytask']['target_type'];	

		$arrUncomplete = DaytaskLogic::getArrUncomplete($this->uid, $arrRet['refresh_time']);
		$arrCanAccept = array();
		
		$pos = 0;
		foreach ($arrRet['va_daytask']['canAccept'] as $taskId)
		{
			if (isset($arrUncomplete[$taskId]) && $pos==$arrUncomplete[$taskId]['pos'])
			{
				$arrCanAccept[] = array('taskId'=>$taskId, 'count'=>$arrUncomplete[$taskId]['count'], 'pos'=>$pos);		
			}
			else
			{
				$arrCanAccept[] = array('taskId'=>$taskId, 'count'=>0, 'pos'=>$pos);	
			}
			$pos++;
		}
		
		$arrRet['canAccept'] = $arrCanAccept;
		$arrRet['integral_reward'] = $arrRet['va_daytask']['integral_reward'];
		$arrRet['left_free_refresh_num'] = DaytaskConf::FREE_REFRESH_NUM - $arrRet['va_daytask']['free_refresh_num'];
		
		unset($arrRet['va_daytask']);
		unset($arrRet['refresh_time']);
		unset($arrRet['uid']);
		
		$arrAccept = RPCContext::getInstance()->getSession('daytask.accept');
		if (!empty($arrAccept))
		{
			$arrRet['accept'] = array_values($arrAccept);
			foreach($arrRet['accept'] as &$task)
			{
				unset($task['id']);
				unset($task['refresh_time']);
			}
		}
		else
		{
			$arrRet['accept'] = array();
		}
		
		return $arrRet;		
	}

	/* (non-PHPdoc)
	 * @see IDaytask::accept()
	 */
	public function accept ($taskId, $pos)
	{
		$arrRet = DaytaskLogic::accept($taskId, $pos);
		if ($arrRet['ret']=='ok')
		{
			unset($arrRet['res']['id']);
			unset($arrRet['res']['refresh_time']);
		}
		return $arrRet;		
	}

	/* (non-PHPdoc)
	 * @see IDaytask::complete()
	 */
	public function complete ($taskId, $goldComplete=false)
	{
		$arrRet = DaytaskLogic::complete($taskId, $goldComplete);
		if ($arrRet['ret'] == 'ok')
		{
			$arrCanAccept = array();
			$pos = 0;
			foreach ($arrRet['res']['canAccept'] as $taskId)
			{
				$arrCanAccept[] = array('taskId' => $taskId, 'pos' => $pos, 'count' => 0);
				$pos++;
			}
			$arrRet['res']['canAccept'] = $arrCanAccept;			
			TaskNotify::operate(TaskOperateType::DAYTASK_COMPLETE);
			
			EnActive::addDayTaskTimes();
			
			EnFestival::addDaytaskPoint();
		}
		
		return $arrRet;
	}
	
	/* (non-PHPdoc)
	 * @see IDaytask::complete()
	 */
	public function goldComplete ($taskId)
	{
		return $this->complete($taskId, true);
	}

	/* (non-PHPdoc)
	 * @see IDaytask::goldRefrshTask()
	 */
	public function goldRefreshTask ()
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());
		$arrCanAccept = DaytaskLogic::goldRefreshTask();
		$pos = 0;
		foreach ($arrCanAccept as $taskId)
		{
			$arrRet['res']['canAccept'][] = array('taskId'=>$taskId, 'pos'=>$pos, 'count'=>0);
			$pos++; 	
		}		
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IDaytask::itemRefreshTask()
	 */
	public function itemRefreshTask ()
	{
		$arrRet = array('ret' => 'ok', 'res' => array());
		return $arrRet;
	}
	
	/* (non-PHPdoc)
	 * @see IDaytask::getIntegralReward()
	 */
	public function getIntegralReward ()
	{
		return DaytaskLogic::getIntegralReward();
	}
	
	/* (non-PHPdoc)
	 * @see IDaytask::abandon()
	 */
	public function abandon ($taskId)
	{
		$ret = DaytaskLogic::abandon($taskId);
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IDaytask::upgrade()
	 */
	public function upgrade ()
	{
		DaytaskLogic::upgrade();
		return $this->getInfo();
	}

	/* (non-PHPdoc)
	 * @see IDaytask::freeRefreshTask()
	 */
	public function freeRefreshTask ()
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());
		$arrCanAccept = DaytaskLogic::freeRefreshTask();
		$pos = 0;
		foreach ($arrCanAccept as $taskId)
		{
			$arrRet['res']['canAccept'][] = array('taskId'=>$taskId, 'pos'=>$pos, 'count'=>0);
			$pos++; 	
		}		
		return $arrRet;		
	}
	
	public function getCompleteNumToday($uid)
	{
		$ret = DaytaskInfoDao::get($uid, array('complete_num', 'refresh_time'));
		if (empty($ret))
		{
			return 0;
		}
		
		if (Util::isSameDay($ret['refresh_time']))
		{
			return $ret['complete_num'];
		}
		return 0;
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */