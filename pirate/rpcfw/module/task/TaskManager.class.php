<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TaskManager.class.php 26618 2012-09-04 02:36:25Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/task/TaskManager.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-04 10:36:25 +0800 (二, 2012-09-04) $
 * @version $Revision: 26618 $
 * @brief 
 * 
 **/













class TaskManager
{
	/**
	 * 检查己经接受的任务，是否能提交
	 * @var bool $checkAccept
	 */
	private $checkAccept = false;
	
	private $bag = null;
	
	/**
	 * 检查能接受的任务，是否仍然能接受
	 * 检查不能接受的任务，是否能接受 
	 * @var bool $checkCanAccept
	 */
	private $checkCanAccept = false;
	
	/**
	 * @var TaskReward
	 */
	private $taskReward = null;
	
	public function setCheckAccept ()
	{
		$this->checkAccept = true;
	}
	
	public function setCheckCanAccept ()
	{
		$this->checkCanAccept = true;
	}
	
	public function needCheck ()
	{
		return $this->checkAccept || $this->checkCanAccept;
	}
	
	/**
	 * 缓存数据
	 * TaskDataType::ARMY => array(id=>num)
	 * TaskDataType::OPERATE => array(id=>num)
	 * TaskDataType::BEAT_ARMY_LEVEL => array(array(id,level)) //这里是评价等级
	 * @var unknown_type
	 */
	private $dataBuf = array();
	
	public function getDataBuf ()
	{
		return $this->dataBuf;
	}
	
	private function clearDataBuf()
	{
		$this->dataBuf = array();
	}
	
	private function addDataBuf ($type, $data)
	{
		if ($type==TaskDataType::BEAT_ARMY_LEVEL)
		{
			$this->dataBuf[$type][] = $data;
			return; 
		}
		
		if (!isset($this->dataBuf[$type]))
		{
			$this->dataBuf[$type] = $data;
			return;
		}
		else
		{
			$sameKey = array_intersect_key($data, $this->dataBuf[$type]);
			foreach ($sameKey as $key=>$t)
			{				
				$this->dataBuf[$type][$key] += $data[$key];
			}
			$this->dataBuf[$type] += $data;
		}
	}
	
	/**
	 * 添加操作
	 * @param array $arrOperate
	 * <code>
	 * 操作id => 数量
	 * </code>
	 */
	public function addOperate ($arrOperate)
	{
		$this->addDataBuf(TaskDataType::OPERATE, $arrOperate);
	}
	
	/**
	 * 添加打败的部队,评价
	 * @param array $arrArmy
	 * <code>
	 * 部队id => 评价
	 * </code>
	 */
	public function addBeatArmyLevel ($arrArmy)
	{
		$this->addDataBuf(TaskDataType::BEAT_ARMY_LEVEL, $arrArmy);
	}
	
	/**
	 * 添加打败的部队
	 * @param array $arrArmy
	 * <code>
	 * id => 数量
	 * </code>
	 */
	public function addBeatArmy ($arrArmy)
	{
		$this->addDataBuf(TaskDataType::ARMY, $arrArmy);
	}
	
	
	/**
	 * 已接任务
	 * array(taskId => array('taskId'=>taskId, 'completeNum' => num, 'va_task'=>va_task, 'kid'=>kid)) 
	 */
	private $acceptTask = array();
	
	//可接任务，包括没有完成最多重复次数的任务
	//taskId => array('taskId'=>taskId, 'completeNum' => num)
	private $canAcceptTask = array();
	
	//可交任务
	//这个不保存，初始化时计算保存在缓存里面。已经可交，则发送数据告诉客户端
	

	//未达到接受条件的任务
	//taskId => array('taskId'=>taskId, 'completeNum' => num)
	private $unacceptTask = array();
	
	//已经完成的任务,完成了最大次数，不可再做
	//taskId => array('taskId'=>taskId, 'completeNum' => num)
	private $completeTask = array();
	
	//已经完成的主线任务,完成了最大次数，不可再做
	//taskId => array('taskId'=>taskId, 'completeNum' => num)
	private $mainCompleteTask = array();
	
	//每日/周/月任务htid
	private $dateTask = array();
	
	//奖励任务
	//taskId => array('taskId'=>taskId, 'completeNum'=>num)
	//private $REWARD_TASK = array();
	
	//奖励任务id
	//private $rewardTaskId = array();
	
	private $uid = null;
	
	private $TASKS = null;
	
	private $user = null;
	
	/**
	 * @var AcceptCondition
	 */
	private $acceptCondition = null;
	
	/**
	 * @var CompleteCondition
	 */
	private $completeCondition = null;
	
	private static $taskManager = null;
	
	public static function isExist($taskId)
	{
		return isset(btstore_get()->TASKS[$taskId]);
	}
	
	/**
	 * @return TaskManager
	 */
	public static function getInstance ()
	{
		if (self::$taskManager == null)
		{
			self::$taskManager = new TaskManager();
		}
		return self::$taskManager;
	}
	
	public function getUid()
	{
		return $this->uid;
	}
	
	public static function release ()
	{
		if (self::$taskManager != null)
		{
			self::$taskManager = null;
		}
	}
	
	public static function resetSession()
	{
		RPCContext::getInstance()->unsetSession('task.all');
	}
	
	/**
	 * 得到接受任务的npcId
	 * @param uint $taskId
	 */
	public function getAcceptNpcId ($taskId)
	{
		if (isset($this->TASKS[$taskId]))
		{
			return $this->TASKS[$taskId]['acceptNpcId'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * 任务是否接受
	 * Enter description here ...
	 * @param unknown_type $taskId
	 */
	public function isAccept ($taskId)
	{
		if (isset($this->acceptTask[$taskId]))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 在已接任务中根据部队id查找掉落表
	 * Enter description here ...
	 * @param unknown_type $armyId
	 */
	public function getDptInAccept($armyId)
	{
		$arrRet = array();
		foreach ($this->acceptTask as $taskId => $task)
		{
			$tbl = $this->TASKS[$taskId]['dropTable'];
			if (isset($tbl[$armyId]))
			{
				$arrRet[] = $tbl[$armyId];
			}
		}
		return $arrRet;
	}
	
	
	/**
	 * 得到提交任务的npcId
	 * @param uint $taskId
	 */
	public function getCompleteNpcId ($taskId)
	{
		if (isset($this->TASKS[$taskId]))
		{
			return $this->TASKS[$taskId]['comNpcId'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * 检查每日/周/月以及活动的任务
	 * 检查已接/能接/完成列表
	 * 每日/周/月任务检查是否完成，重置完成次数
	 */
	public function checkDateTask()
	{	
		$newAccept = array();
		$newCanAccept = array();
		$newUnaccept = array();
		
		foreach ($this->dateTask as $taskId=>$tmp)
		{
			$mainType = $tmp['mainType'];
			if (TaskMainType::DAY==$mainType || TaskMainType::WEEK==$mainType || TaskMainType::MONTH==$mainType)
			{
				if (array_key_exists($taskId, $this->acceptTask))
				{
					$delNum = $this->delDateTaskById($taskId, $mainType);
					if ($delNum!=0)
					{
						$this->acceptTask[$taskId]['completeNum'] -= $delNum;
						$newAccept[$taskId] = $this->acceptTask[$taskId];
					}
				}
				else if(array_key_exists($taskId, $this->canAcceptTask))
				{
					$delNum = $this->delDateTaskById($taskId, $mainType);
					if ($delNum!=0)
					{
						$this->canAcceptTask[$taskId]['completeNum'] -= $delNum;
						$newCanAccept[$taskId] = $this->canAcceptTask[$taskId];
					}					
				}
				else if(array_key_exists($taskId, $this->completeTask))
				{
					$delNum = $this->delDateTaskById($taskId, $mainType);
					if ($delNum!=0)
					{
						$this->completeTask[$taskId]['completeNum'] -= $delNum;
						//检查是否能接受
						if ($this->isCanAccept($taskId))
						{
							$this->canAcceptTask[$taskId] = $this->completeTask[$taskId];
							$newCanAccept[$taskId] = $this->completeTask[$taskId];
							unset($this->completeTask[$taskId]);
						}
						else
						{
							$this->unacceptTask[$taskId] = $this->completeTask[$taskId];
							unset($this->completeTask[$taskId]);
						}
					}
				}

			}			
			
		}	
		$this->saveSession();	
		$res = array('accept' => array_values($newAccept), 
				'canAccept' => array_values($newCanAccept), 
				'unaccept' => array_values($newUnaccept));
		Logger::debug('checkDateTask return %s', $res);
		return $res;
	}
	
	/**
	 * 从数据库中删除已经完成的过期（昨天/上周/上月）日周月任务
	 * @param unknown_type $taskId
	 * @param unknown_type $mainType
	 */
	private function delDateTaskById($taskId, $mainType)
	{
		$delNum = 0;
		//array('kid', 'taskId', 'complete_time', 'status');
		$dateTask = TaskDao::getDateTaskByTaskId($this->uid, $taskId);
		foreach ($dateTask as $task)
		{
			if ($task['status']==TaskStatus::COMPLETE)
			{
				switch ($mainType)
				{
					//每日任务
					case TaskMainType::DAY :
						if (!Util::isSameDay($task['complete_time']))
						{
							TaskDao::delete($task['kid'], $this->uid);
							$delNum++;
						}
						break;
					case TaskMainType::WEEK :
						if (!Util::isSameWeek($task['complete_time']))
						{
							TaskDao::delete($task['kid'], $this->uid);
							$delNum++;
						}
						break;
					
					case TaskMainType::MONTH :
						if (!Util::isSameMonth($task['complete_time']))
						{
							TaskDao::delete($task['kid'], $this->uid);
							$delNum++;
						}
						break;
					default:
						break;
				}
			}
		}
		return $delNum;		
	}
	
	public function accept ($taskId)
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());
		
		//达到最大能接的个数
		if (count($this->acceptTask) >= TaskConf::MAX_ACCEPTED_NUM)
		{
			Logger::warning('fail to accept task, accept num is max');
			throw new Exception('fake');
		}
		
		//是否在canAccept里面
		if (!array_key_exists($taskId, $this->canAcceptTask))
		{
			//TODO
			//Logger::warning("%d is not in canAccept %s.", $taskId, $this->canAcceptTask);
			//throw new Exception('fake');
			$arrRet['ret'] = 'not_exist';
			return $arrRet;
		}
		
		$task = $this->canAcceptTask[$taskId];
		
		//save to db
		$kid = TaskDao::insert($taskId, $this->uid, TaskStatus::ACCEPT);
		
		///array(taskId => array(status,完成次数,va_task,kid))
		$task['status'] = TaskStatus::ACCEPT;
		$task['kid'] = $kid;
		$task['va_task'] = array();
		
		unset($this->canAcceptTask[$taskId]);
		//检查完成条件，是否能完成
		$type = $this->TASKS[$taskId]['taskType'];
		$comCondition = $this->TASKS[$taskId]['complete'];
		$ret = $this->completeCondition->checkAndUpdate($kid, $type, $comCondition, $task['va_task']);
		if ($ret[0])
		{
			$task['status'] = TaskStatus::CAN_SUBMIT;
		}
		
		$this->acceptTask[$taskId] = $task;
		
		//save session
		$this->saveSession();
		$arrRet['res'] = $task;		
		return $arrRet;
	}
	
	/**
	 * 接受奖励任务， 不检查接受条件
	 * 直接添加的已经接受的任务里面
	 * @param uint $arrTaskId 奖励任务id数组
	 */
	public function acceptRewardTask ($arrTaskId)
	{
		$newAccept = array();
		//接受
		foreach ($arrTaskId as $taskId)
		{
			//检查是否为奖励任务
			if (!$this->isRewardTask($taskId))
			{
				Logger::fatal('%d is not reward task', $taskId);
				throw new Exception('sys');
			}
			
			//如果已经接受，不再接受
			if (array_key_exists($taskId, $this->acceptTask))
			{
				//奖励任务已经接受了，不处理这个任务
				Logger::debug('skip reward task %d, because the task is accepted', $taskId);
				continue;
			}
			
			
			//奖励任务应该都放在不能接受的里面
			if (!array_key_exists($taskId, $this->unacceptTask))
			{
				if (array_key_exists($taskId, $this->mainCompleteTask)
					|| array_key_exists($taskId, $this->completeTask))
				{
					Logger::fatal("%d task has completed max repeatNum, cannot been rewarded.", $taskId);
					throw new Exception("sys");
				}
			}
			
			$task = $this->unacceptTask[$taskId];  
			//save to db
			$kid = TaskDao::insert($taskId, $this->uid, TaskStatus::ACCEPT);
			///array(taskId => array(status,完成次数,va_task,kid))
			$task['status'] = TaskStatus::ACCEPT;
			$task['kid'] = $kid;
			$task['va_task'] = array();

			//检查完成条件，是否能提交
			$type = $this->TASKS[$taskId]['taskType'];
			$comCondition = $this->TASKS[$taskId]['complete'];
			$ret = $this->completeCondition->checkAndUpdate($kid, $type, $comCondition, $task['va_task']);
			if ($ret[0])
			{
				$task['status'] = TaskStatus::CAN_SUBMIT;
			}
		
			$this->acceptTask[$taskId] = $task;
			$newAccept[] = $task;
			unset($this->unacceptTask[$taskId]);
		}
		return $newAccept;
	}
    
	public function complete ($taskId)
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());
		
		$bag = BagManager::getInstance()->getBag();
		//是否在accept里面 并且是否能提交
		if (!array_key_exists($taskId, $this->acceptTask) || $this->acceptTask[$taskId]['status'] != TaskStatus::CAN_SUBMIT)
		{
			//TODO
			//Logger::warning('the task taskId %d isnot in accept array, or its status isnot submit', $taskId);
			//throw new Exception('fake');
			$arrRet['ret'] = 'not_exist';
			return $arrRet;
		}
		
		//是否为上交物品的任务
		if ($this->TASKS[$taskId]['taskType'] == TaskCompleteType::ITEM || $this->TASKS[$taskId]['taskType'] == TaskCompleteType::BEAT_ARMY_ITEM)
		{
			$comCondition = $this->TASKS[$taskId]['complete']->toArray();
			Logger::debug("del item :%s", $comCondition);
			$delRet = $bag->deleteItemsbyTemplateID($comCondition);
			if (!$delRet)
			{
				Logger::fatal("fail to delete item for complete task. item:%s", $comCondition);
				throw new Exception("fail to delete item");
			}			
		}
		
		//update db, 改为完成状态, 
		// 记录一个提交时间， 给日月周任务使用
		TaskDao::update($this->acceptTask[$taskId]['kid'],
			$this->uid, 
			array('complete_time'=>Util::getTime(), 'status' => TaskStatus::COMPLETE));
		
		$task = $this->acceptTask[$taskId];
		unset($this->acceptTask[$taskId]);
		$task['completeNum'] += 1;
		$task['status'] = TaskStatus::COMPLETE;

		//完成此任务后，新的可接任务，包括当前任务(未完成最大重复次数），后置任务，
		$newCanAccept = array();
		$newAccept = array();
		
		if ($this->TASKS[$taskId]['repeatNum'] <= $task['completeNum'])
		{
			if ($this->isMainTask($taskId))
			{
				$this->mainCompleteTask[$taskId] = $task;
			}
			else
			{
				$this->completeTask[$taskId] = $task;
			}
		}
		//没有完成最大重复次数
		else
		{			
			//check 是否能再次接受
			if ($this->isCanAccept($taskId))
			{
				$this->canAcceptTask[$taskId] = $task;
				$newCanAccept[] = $task;
			}
			else 
			{
				$this->unacceptTask[$taskId] = $task;
			}
		}
		
		//后置任务是否能接受
		$nextTaskId = $this->TASKS[$taskId]['nextTaskId'];	
		if ($nextTaskId!=0 && array_key_exists($nextTaskId, $this->unacceptTask))
		{
			//检查是否能接受
			if ($this->isCanAccept($nextTaskId))
			{			
				$this->canAcceptTask[$nextTaskId] = $this->unacceptTask[$nextTaskId];
				$newCanAccept[] = $this->canAcceptTask[$nextTaskId];
				unset($this->unacceptTask[$nextTaskId]);
			} 	
		}

		//检查不能接受的任务， 这里可能有支线任务变成可接受了
		$newCanAccept = array_merge($newCanAccept, $this->checkUnaccept());
		
		//奖励
		$reward = $this->taskReward->getReward($taskId);
		EnUser::getUserObj()->update();
		
		$arrRewardTaskId = $reward['taskId'];
		$arrRet['res'] = $reward;
		unset($arrRet['res']['taskId']);
		
		//奖励任务添加到已接受列表中
		$newAccept = $this->acceptRewardTask($arrRewardTaskId);		
		$arrRet['res']['task'] = array('canAccept' => $newCanAccept, 
				'accept' => $newAccept,
				'complete' => array($task));
				
		//处理背包信息
		//删除物品跟奖品物品的格子合并，返回哪些格子的物品修改了
		$arrRet['res']['item'] = $bag->update();
		
		//save session
		$this->saveSession();

		//广播
		if ($this->TASKS[$taskId]['comBroadcastContent'] !=0)
		{
			ChatTemplate::sendTaskEnd($this->TASKS[$taskId]['comBroadcastContent'], 
				array(EnUser::getUserObj()->getTemplateUserInfo(), $taskId));
		}
		
		
		return $arrRet;
	}
	
	private function isCanAccept ($taskId)
	{
		//关闭的任务不能接受
		if ($this->TASKS[$taskId]['isClose'])
		{
			return false;
		}
		
		$arrCondidtion = $this->TASKS[$taskId]['condition'];
		//不能接受了
		if ($this->acceptCondition->check($arrCondidtion))
		{
			return true;
		}
		return false;
	}
	
	public function abandon ($taskId)
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());
		
		//是否在accept里面
		if (!array_key_exists($taskId, $this->acceptTask))
		{
			Logger::warning("%d is not in accept task.", $taskId);
			$arrRet['ret'] = 'not_exist';
			return $arrRet;
		}
		
		//是否能放弃
		if ($this->TASKS[$taskId]['abandon'] != 1)
		{
			Logger::warning("%d is cannot abandon.", $taskId);
			throw new Exception('fake');
		}
		
		$item = array();
		$bag = BagManager::getInstance()->getBag();
		//是否为上交物品的任务
		if ($this->TASKS[$taskId]['taskType'] == TaskCompleteType::ITEM 
			|| $this->TASKS[$taskId]['taskType'] == TaskCompleteType::BEAT_ARMY_ITEM)
		{
			$comCondition = $this->TASKS['complete'];
			$item_manager = ItemManager::getInstance();
			foreach ($comCondition as $id=>$num)
			{
				//删除任务物品， 普通物品不删除
				if ($item_manager->isMissionItem($id))
				{
					for ($i=0; $i<$num; $i++)
					{
						$delRet = $bag->deleteItembyTemplateID($id, 1);
						if (!$delRet)
						{
							break;
						} 
					}
				}
			}
			//更新背包
			$item = $bag->update();
		}
		
		//del from db
		TaskDao::update($this->acceptTask[$taskId]['kid'],
			$this->uid, 
			array('status' => TaskStatus::DELETE));

		//放弃的任务可能是可接受的，保存下来返回给前端
		$newCanAccept = array();
		if ($this->isCanAccept($taskId))
		{
			$this->canAcceptTask[$taskId] = $this->acceptTask[$taskId];
			$this->canAcceptTask[$taskId]['status'] = TaskStatus::CAN_ACCEPT;
			$newCanAccept[] = $this->canAcceptTask[$taskId];
		}
		else 
		{
			$this->unacceptTask[$taskId] = array('taskId'=>$taskId, 
				'completeNum'=>$this->acceptTask[$taskId]['completeNum']);
		}
		//del from session
		unset($this->acceptTask[$taskId]);
		
		//保存session
		$this->saveSession();		
				
		$arrRet['res'] =  array("item" => $item, 'task'=>array('canAccept' => $newCanAccept));
		return $arrRet;
	}
	
	public function getAllTask ()
	{
		return array('accept' => array_values($this->acceptTask), 'canAccept' => array_values($this->canAcceptTask), //这里返回的是完成的主线任务 
		'complete' => array_values($this->mainCompleteTask));
	}
	
	/**
	 * 检查任务，是否有任务状态/数据改变
	 * @return array(已接受的任务, 新的能接的任务，新的不能接的任务) 
	 */
	public function checkModify ()
	{
		//已接任务新状态
		$newAccept = array();
		if ($this->checkAccept)
		{
			$newAccept = $this->checkAccept();
		}
		
		$newCanAccept = array();
		$newUnaccept = array();
		if ($this->checkCanAccept)
		{
			$newCanAccept = $this->checkUnaccept();
			$newUnaccept = $this->checkCanAccept();
		}
		
		$this->saveSession();
		$res = array('accept' => array_values($newAccept), 
				'canAccept' => array_values($newCanAccept), 
				'unaccept' => array_values($newUnaccept));
		Logger::debug('checkModify return %s', $res);
		$this->clearDataBuf();
		return $res;
	}
	
	/**
	 * 遍历所有已接任务，补充其它模块数据，检查是否可提交，并修改状态。 
	 * @return array 状态或者数据变化的任务 
	 */
	private function checkAccept ()
	{
		//记录修改的数据，返回给前端
		$newModify = array();
		//遍历已接任务
		foreach ($this->acceptTask as $taskId=>&$task)
		{
			$type = $this->TASKS[$taskId]['taskType'];
			$comCondition = $this->TASKS[$taskId]['complete'];
			$ret = $this->completeCondition->checkAndUpdate($task['kid'], $type, $comCondition, $task['va_task']);
			
			if ($ret[2])
			{
				//abandon
				$this->abandon($taskId); 	
			}
			else if ($ret[0] && $task['status'] != TaskStatus::CAN_SUBMIT) 
			{
				$task['status'] = TaskStatus::CAN_SUBMIT;
				$newModify[$taskId] = $task;
				//变成能接，检查开启功能	
				EnSwitch::canSubmit($taskId);				
			}
			else if(!$ret[0] && $task['status'] == TaskStatus::CAN_SUBMIT)
			{
				$task['status'] = TaskStatus::ACCEPT;
				$newModify[$taskId] = $task;
			}
			else if ($ret[1])
			{
				$newModify[$taskId] = $task;
			}
		}
		unset($task);
		return $newModify;
	}
	
	/**
	 * 检查能接受的，返回不能接受的任务
	 * @return array 不能接受的任务
	 */
	private function checkCanAccept ()
	{
		$newUnaccept = array();
		//检查能接受的，是否改为不能接受的任务
		foreach ($this->canAcceptTask as $taskId=>$task)
		{
			//不能接受了
            if (!$this->isCanAccept($taskId))
			{
				$this->unacceptTask[$taskId] = $task;
				$newUnaccept[$taskId] = $task;
				unset($this->canAcceptTask[$taskId]);
			}
		}
		return $newUnaccept;
	}
	
	/**
	 * 检查不能接受的任务，返回能接受的任务
	 * @return array 能接受的任务
	 */
	private function checkUnaccept ()
	{
		$newCanAccept = array();
		//检查不能接受的，是否改为能接受的
		foreach ($this->unacceptTask as $taskId=>$task)
		{
			//能接受了
            if ($this->isCanAccept($taskId))
			{
				$this->canAcceptTask[$taskId] = $task;
				$newCanAccept[$taskId] = $task;
				unset($this->unacceptTask[$taskId]);
			}
		}
		return $newCanAccept;
	}
	
	private function __construct ()
	{
		Logger::debug("TaskManager::_construct");
		$this->uid = RPCContext::getInstance()->getSession('global.uid');
		if ($this->uid==null)
		{
			return;
		}
		$this->TASKS = btstore_get()->TASKS;
		
		$this->acceptCondition = new AcceptCondition($this->uid, $this);
		$this->completeCondition = new CompleteCondition($this);
		$this->taskReward = new TaskReward();
		
		$this->acceptTask = array();
		$this->canAcceptTask = array();
		$this->unacceptTask = array();
		$this->completeTask = array();
		$this->mainCompleteTask = array();
		
		$this->init();
	}
	
	public function getTaskAll()
	{
		return array('accept'=>$this->acceptTask,		
			'canAccept'=>$this->canAcceptTask,
		'unaccept'=>$this->unacceptTask,	
		'complete'=>$this->completeTask,		
		'mainComplete'=>$this->mainCompleteTask,
		'reward'=>$this->taskReward);		
	}
	
	public function init ()
	{
		Logger::debug("TaskManager.init");
		$taskSession = RPCContext::getInstance()->getSession('task.all');
		if ($taskSession == null)
		{
			$this->loadTask();
			$this->saveSession();
			$this->checkDateTask();
		}
		else
		{
			$this->acceptTask = $taskSession['accept'];
			$this->canAcceptTask = $taskSession['canAccept'];
			$this->unacceptTask = $taskSession['unaccept'];
			$this->completeTask = $taskSession['complete'];
			$this->mainCompleteTask = $taskSession['mainComplete'];
		}
	}
	
	/**
	 * 输入参数的任务数组是否都已经完成（至少完成了一次）
	 * @param array $arrTaskId
	 */
	public function isComplete ($arrTaskId)
	{
		foreach ($arrTaskId as $taskId)
		{
			//这里需要在所有的列表中检查
			//不能接受的列表也要检查，
			//任务可能没完成最大的次数，所以没保存到已经完成列表里面
			//但是此任务当前已经不符合接受条件，所以保存在不能接受的表里
			$task = null;
			if (isset($this->acceptTask[$taskId]))
			{
				$task = $this->acceptTask[$taskId];
			}
			else if (isset($this->canAcceptTask[$taskId]))
			{
				$task = $this->canAcceptTask[$taskId];
			}
			else if (isset($this->unacceptTask[$taskId]))
			{
				$task = $this->unacceptTask[$taskId];
			}
			else if (isset($this->completeTask[$taskId]))
			{
				$task = $this->completeTask[$taskId];
			}
			else if (isset($this->mainCompleteTask[$taskId]))
			{
				$task = $this->mainCompleteTask[$taskId];
			}
			
			if ($task == null)
			{
				return false;
			}
			else if ($task['completeNum'] == 0)
			{
				return false;
			}
		
		}
		return true;
	}
	
	private function saveSession ()
	{
		$taskSession = array('accept' => $this->acceptTask, 
				'canAccept' => $this->canAcceptTask, 
				'unaccept' => $this->unacceptTask, 
				'complete' => $this->completeTask,
				'mainComplete'=>$this->mainCompleteTask);
		RPCContext::getInstance()->setSession('task.all', $taskSession);
	}
	
	//数据库中重复任务保存在不同的记录中，先把相同的任务放到一个数组中，然后根据完成状态保存到对应的数组中	
	//遍历所有任务模板, 初始化未接任务或者能接任务
	//遍历所有已接任务，检查是否可提交，并修改状态。
	private function loadTask ()
	{
		$this->loadFromDB();
		$this->initUnacceptOrCanAccept();
		//检查已经接受的任务
		$this->checkAccept();
	}
	
	/**
	 * 是否为奖励任务
	 * @param uint $taskId
	 */
	private function isRewardTask($taskId)
	{
		$condition = $this->TASKS[$taskId]['condition'];
		return $condition[TaskAcceptType::IS_REWARD];
	}
	
	/**
	 * 是否为每日任务 
	 * @param unknown_type $taskId
	 */
	private function isDayTask($taskId)
	{
		$mainType = $this->TASKS[$taskId]['mainType'];
		if (TaskMainType::DAY==$mainType)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 是否为每周任务 
	 * @param unknown_type $taskId
	 */
	private function isWeekTask($taskId)
	{
		$mainType = $this->TASKS[$taskId]['mainType'];
		if (TaskMainType::WEEK==$mainType)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 是否为每月任务 
	 * @param unknown_type $taskId
	 */
	private function isMonthTask($taskId)
	{
		$mainType = $this->TASKS[$taskId]['mainType'];
		if (TaskMainType::MONTH==$mainType)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 是否为主线任务
	 * @param uint $taskId
	 */
	private function isMainTask($taskId)
	{
		$mainType = $this->TASKS[$taskId]['mainType'];
		if (TaskMainType::MAIN==$mainType)
		{
			return true;
		}
		return false;
	}
	
	private function loadFromDB ()
	{
		//load from db
		$allAccepted = TaskDao::getByUid($this->uid);
		
		//构造array(taskId=> array('taskId'=> 'status'=>, kid=> va_task=>),)
		$allAcceptedTask = array();
		foreach ($allAccepted as $task)
		{
			$allAcceptedTask[$task['taskId']][] = $task;
		}
				
		foreach ($allAcceptedTask as $taskId=>$arrTask)
		{
			$task = $this->getState($arrTask);
			$task['taskId'] = $taskId;
			if (TaskStatus::ACCEPT == $task['status'])
			{
				$this->acceptTask[$taskId] = $task;
			}
			else if (TaskStatus::COMPLETE == $task['status'])
			{
				//判断是否完成了最多重复次数
				$maxNum = $this->TASKS[$taskId]['repeatNum'];
				if ($task['completeNum'] >= $maxNum)
				{
					//放到已完成列表里面
					//如果是主线任务，放主线完成任务表里
					if ($this->TASKS[$taskId]['mainType'] == TaskMainType::MAIN)
					{
						$this->mainCompleteTask[$taskId] = array('taskId' => $taskId, 'completeNum' => $task['completeNum']);
					}
					else
					{
						$this->completeTask[$taskId] = array('taskId' => $taskId, 'completeNum' => $task['completeNum']);
					}
				}
				else
				{
					//判断是否可接受，
                    if ($this->isCanAccept($taskId))
					{
						//放到可接列表里面
						$this->canAcceptTask[$taskId] = array('taskId' => $taskId, 'completeNum' => $task['completeNum']);
					}
					else
					{
						//不可接受
						$this->unacceptTask[$taskId] = array('taskId' => $taskId, 'completeNum' => $task['completeNum']);
					}
				}
			}
		
		}
	}
	
	//遍历所有任务, 初始化未接任务或者能接任务
	private function initUnacceptOrCanAccept ()
	{
		foreach ($this->TASKS as $taskId=>$task)
		{
			//另外保存所有的日月周任务
			if ($this->isDayTask($taskId) || $this->isWeekTask($taskId) || $this->isMonthTask($taskId))
			{
				$this->dateTask[] = $taskId;
			}
			
			//去掉数据库中已有任务
			if (isset($this->mainCompleteTask[$taskId]) 
				|| isset($this->completeTask[$taskId]) 
				|| isset($this->acceptTask[$taskId]) 
				|| isset($this->unacceptTask[$taskId]))
			{
				continue;
			}
			
			$task = array('taskId'=>$taskId, 'completeNum'=>0);
			if ($this->isCanAccept($taskId))
			{
				$this->canAcceptTask[$taskId] = $task;
			}
			else
			{
				$this->unacceptTask[$taskId] = $task;
			}
		}
	}
	
	/**
	 * 根据一组taskId的情况得到任务的状态
	 * @param array $arrTask
	 * <code>
	 * {
	 * taskId=>
	 * array
	 * {
	 * 'kid' => kid,
	 * 'status' => status,
	 * 'va_task' => va_task	 
	 * }
	 * }
	 * </code>
	 * 
	 * @return array
	 * <code>
	 * status
	 * completeNum
	 * va_task
	 * kid
	 * </code>
	 */
	private function getState ($arrTask)
	{
		$status = TaskStatus::UNSUPPORTED;
		$completeNum = 0;
		$kid = 0;
		$va_task = array();
		
		foreach ($arrTask as $task)
		{
			switch ($task['status'])
			{
				case TaskStatus::COMPLETE :
					++$completeNum;
					break;
				case TaskStatus::CAN_SUBMIT :
				case TaskStatus::ACCEPT :
					$status = TaskStatus::ACCEPT;
					$kid = $task['kid'];
					$va_task = $task['va_task'];
					break;
				default :
					Logger::fatal("unknow task type:%d", $task['status']);
					break;
			}
		}
		
		if ($completeNum != 0 && $status == TaskStatus::UNSUPPORTED)
		{
			$status = TaskStatus::COMPLETE;
		}
		$ret = array('status' => $status, 'completeNum' => $completeNum, 'va_task' => $va_task, 'kid' => $kid);
		return $ret;
	}
	
	/**
	 * 给控制台指令用
	 * Enter description here ...
	 * @param unknown_type $taskId
	 */
	public function canSubmit4Test($taskId)
	{
		$acceptTask = array();
		if ($taskId!=0)
		{
			if (!isset($this->acceptTask[$taskId]))
			{
				return -1;
			}
			$acceptTask = array($this->acceptTask[$taskId]);
		}
		else
		{
			$acceptTask = $this->acceptTask;
		}
		
		foreach ($acceptTask as $task)
		{
			$id = $task['taskId'];			
			foreach ($task['va_task'] as &$va)
			{
				$key = $va['key'];
				$max = btstore_get()->TASKS[$id]['complete'][$key];
				$va['value'] = $max;
			}
			unset($va);
			TaskDao::update($task['kid'], $this->uid, array('va_task'=>$task['va_task']));
		}		
		return 0;	
	}
	
	public function getAcceptTask()
	{
		return $this->acceptTask;
	}
	
	public function fixCompleteTask()
	{
		$this->fixTaskCompleteTask_($this->mainCompleteTask);
		$this->fixTaskCompleteTask_($this->completeTask);
	}
	
	private function fixTaskCompleteTask_($arrTask)
	{
		$modifyUser = false;
		$fixRewardTask = false;
		
		$abandonTaskId = TaskDao::getDelTaskId($this->uid);
		
		$userObj = EnUser::getUserObj($this->uid);
		//遍历完成的主线任务
		foreach ($arrTask as $taskId=>$taskInfo)
		{
			$rewardType = $this->TASKS[$taskId]['reward']['type'];
			//奖励英雄
			if (!empty($rewardType[TaskRewardType::HERO]))
			{
				foreach ($rewardType[TaskRewardType::HERO] as $htid)
				{
					if (!$userObj->hasHero($htid))
					{
						$modifyUser = true;
						Logger::fatal('fix task. add reward hero htid %d for task %d', $htid, $taskId);
						$userObj->addNewHeroToPub($htid);
					}
				}		
			}
			
			//奖励任务
			if (!empty($rewardType[TaskRewardType::TASK_ID]))
			{
				foreach ($rewardType[TaskRewardType::TASK_ID] as $rewardTaskId)
				{
					if (!$this->isAccept($rewardTaskId) && !$this->isComplete(array($rewardTaskId)))
					{
						//已经放弃，不处理
						if (in_array($rewardTaskId, $abandonTaskId))
						{
							Logger::debug('not fix abandon task %d', $rewardTaskId);
							continue;
						}
						
						$fixRewardTask = true;
						Logger::fatal('fix task. add reward task %d for task %d', $rewardTaskId, $taskId);
						$this->acceptRewardTask(array($rewardTaskId));
					}
				}
			}
			
			
		}
		
		if ($modifyUser)
		{
			$userObj->update();
			throw new Exception('close');
		}
		
		if ($fixRewardTask)
		{
			throw new Exception('close');
		}
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */