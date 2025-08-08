<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyCopy.class.php 29240 2012-10-13 06:02:09Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/MyCopy.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-13 14:02:09 +0800 (六, 2012-10-13) $
 * @version $Revision: 29240 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyCopy
 * Description : 副本数据持有类
 * Inherit     : 
 **********************************************************************************************************************/
class MyCopy
{
	private $m_copyList;						// 副本数据
	private $uid;								// 用户ID

	/**
	 * 构造函数，获取 session 信息
	 */
	function __construct() 
	{
		// 从 session 中取得副本信息
		$copyList = RPCContext::getInstance()->getSession('copy.copyList');
		// 获取用户ID，使用用户ID获取副本信息
		$this->uid = RPCContext::getInstance()->getSession('global.uid');
		// 如果没顺利取得副本信息
		if (empty($copyList)) 
		{
			if (empty($this->uid)) 
			{
				Logger::fatal('Can not get copy info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户副本信息
			$copyList = CopyDao::getUserCopies($this->uid);
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			CopyDao::setBufferWithoutSelect($this->uid, $copyList);
		}
		// 赋值给自己
		$this->m_copyList = $copyList;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.copyList', $this->m_copyList);
	}

	/**
	 * 查看当前用户是否击败过某个副本的某个部队
	 * 
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 */
	public function isCopyEnemyDefeated($copyID, $enemyID)
	{
		// 只有设置了某个副本的某个部队击败过，才算正确，其他的都不算打过
		if (isset($this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'][$enemyID]))
		{
			return true;
		}
		return false;
	}

	/**
	 * 获取某个部队被攻击的次数
	 * 
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 */
	public function getCopyEnemyDefeatNum($copyID, $enemyID)
	{
		// 返回次数
		if (isset($this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'][$enemyID]))
		{
			return $this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'][$enemyID];
		}
		return 0;
	}

	/**
	 * 查看此用户是否干掉过这个部队
	 * @param int $enemyID						部队ID
	 */
	public function isEnemyDefeated($enemyID)
	{
		// 查看所有副本数据
		foreach ($this->m_copyList as $copy)
		{
			// 如果搜到这个部队的信息了，那么就返回次数
			if (isset($copy['va_copy_info']['defeat_id_times'][$enemyID]))
			{
				return $copy['va_copy_info']['defeat_id_times'][$enemyID];
			}
		}
		// 没找到就返回0次
		return 0;
	}

	/**
	 * 查看是否需要开启新副本
	 */
	public function checkNeedOpenNewCopies()
	{
		// 获取所有需要开启的副本信息
		$tasks = btstore_get()->COPY['task'];
		// 循环遍历所有的任务开启副本信息
		foreach ($tasks as $taskID => $nextCopies)
		{
			// 检查是否接收到所需的任务了, 如果没有接收到任务，那么直接查看下一个
			if (!EnTask::isAccept($taskID))
			{
				continue;
			}
			// 循环开启此任务对应的所有副本
			foreach ($nextCopies as $copyID)
			{
				// 没有开启过这个副本的时候，开启副本
				if (!isset($this->m_copyList[$copyID]))
				{
					// 开启新副本，恩
					self::addNewCopy($copyID);
					// 直接更新到数据库
					self::save($copyID);
				}
			}
		}
	}

	/**
	 * 获取用户的所有副本
	 * @param int $uid							用户ID
	 */
	public function getUserCopies()
	{
		// 查看是否需要开启新的副本
		self::checkNeedOpenNewCopies();
		// 获取持有化的数据
		return $this->m_copyList;
	}

	/**
	 * 获取当前用户的某个副本信息
	 * @param int $copyID						副本ID
	 */
	public function getCopyInfo($copyID)
	{
		// 返回相应副本信息
		return isset($this->m_copyList[$copyID]) ? $this->m_copyList[$copyID] : false;
	}

	/**
	 * 存储用户进度
	 * @param int $copyID						副本ID
	 * @param array $progress					用户当前进度
	 */
	public function updUserProgress($copyID, $progress)
	{
		// 到这一步，默认认为副本是存在的
		$this->m_copyList[$copyID]['va_copy_info']['progress'] = $progress;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.copyList', $this->m_copyList);
	}

	/**
	 * 存储用户杀怪次数 (怪物小队)
	 * 
	 * @param int $copyID						副本ID
	 * @param array $defeatNum					用户杀怪次数
	 */
	public function updUserDefeatNum($copyID, $defeatNum)
	{
		// 到这一步，默认认为副本是存在的
		$this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'] = $defeatNum;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.copyList', $this->m_copyList);
	}

	/**
	 * 增加杀敌次数
	 * 
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 * @throws Exception
	 */
	public function addUserDefeatNum($copyID, $enemyID)
	{
		// 加算这个怪的杀敌信息，但是却没有这个副本
		if (empty($this->m_copyList[$copyID]))
		{
			Logger::fatal('Can not get copy info, uid is %d, enemy ID is %d', $this->uid, $enemyID);
			throw new Exception('fake');
		}

		// 加算杀怪信息
		if (isset($this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'][$enemyID]))
		{
			// 不为空的时候加算
			++$this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'][$enemyID];
		}
		else 
		{
			// 空的时候记录首杀
			$this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'][$enemyID] = 1;
		}
		// 记录日志
		Logger::debug('The total number defeat No.%d army is %d.', $enemyID, 
		              $this->m_copyList[$copyID]['va_copy_info']['defeat_id_times'][$enemyID]);
	}

	/**
	 * 增加副本完成次数
	 * 
	 * @param int $copyID						副本ID
	 */
	public function addCopyRaid($copyID)
	{
		++$this->m_copyList[$copyID]['raid_times'];
		// 设置进session
		RPCContext::getInstance()->setSession('copy.copyList', $this->m_copyList);
	}

	/**
	 * 增加副本得分
	 * 
	 * @param int $copyID						副本ID
	 * @param int $score						需要增加的分数
	 */
	public function addCopyScore($copyID, $score)
	{
		$this->m_copyList[$copyID]['score'] += $score;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.copyList', $this->m_copyList);
		// 判断是否已经获取到了所有的副本奖励
		if (empty(btstore_get()->COPY[$copyID]['prize_scores']) || 
		    $this->m_copyList[$copyID]['score'] < btstore_get()->COPY[$copyID]['prize_scores'][count(btstore_get()->COPY[$copyID]['prize_scores']) - 1])
		{
			// 如果没设置或者分数还不够
			return false;
		}
		return true;
	}

	/**
	 * 增加领取副本奖励次数
	 * 
	 * @param int $copyID						副本ID
	 * @param int $caseID						宝箱ID
	 */
	public function addPirzedTimes($copyID, $caseID)
	{
		$this->m_copyList[$copyID]['prized_num'] += CopyConf::$CASE_INDEX[$caseID];
		// 设置进session
		RPCContext::getInstance()->setSession('copy.copyList', $this->m_copyList);
	}

	/**
	 * 更新攻击某部队的最好成绩
	 * 
	 * @param int $copyID						副本ID
	 * @param int $enemyID						部队ID
	 * @param int $appraisal					评价
	 */
	public function setDefeatAppraisal($copyID, $enemyID, $appraisal)
	{
		$this->m_copyList[$copyID]['va_copy_info']['id_appraisal'][$enemyID] = $appraisal;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.copyList', $this->m_copyList);
	}

	/**
	 * 获得一个新的奖励
	 * 
	 * @param int $copyID						副本ID
	 * @param int $prizeID						奖励ID
	 */
	public function addPrize($copyID, $prizeID)
	{
		$this->m_copyList[$copyID]['va_copy_info']['prize_ids'][$prizeID] = true;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.copyList', $this->m_copyList);
	}

	/**
	 * 是否已经获取了某个奖励
	 *
	 * @param int $copyID						副本ID
	 * @param int $prizeID						奖励ID
	 */
	public function isPrized($copyID, $prizeID)
	{
		// 检查这个奖励是否已经实现过了
		if (isset($this->m_copyList[$copyID]['va_copy_info']['prize_ids'][$prizeID]) && 
		    $this->m_copyList[$copyID]['va_copy_info']['prize_ids'][$prizeID] === true)
		{
			return true;
		}
		return false;
	}

	/**
	 * 为用户增加一个副本
	 * 
	 * @param int $copyID						副本ID
	 */
	public function addNewCopy($copyID)
	{
		Logger::debug('add a new copy for %d, copy id is %d.', $this->uid, $copyID);

		// 看看是不是已经有了，如果没有再进行插入操作
		if (!empty($this->m_copyList[$copyID]))
		{
			return ;
		}

		// 判断是普通副本还是隐藏副本
		if (btstore_get()->COPY[$copyID]['copy_type'] == CopyConf::NORMAL_COPY)
		{
			// 普通副本的话，需要记录获取时刻和副本ID
			EnUser::getUserObj()->setCopyID($copyID);
			EnUser::getUserObj()->update();
		}

		// 设置VA字段信息
		$va_info = array('progress' => array(), 'defeat_id_times' => array(), 
		                 'id_appraisal' => array(), 'prize_ids' => array());
		// 设置插入数据
		$arr = array('uid' => $this->uid,
					 'copy_id' => $copyID,
					 'raid_times' => 0,
					 'score' => 0,
					 'prized_num' => 0,
					 'va_copy_info' => $va_info,
					 'status' => DataDef::NORMAL);
		// 增加一个副本信息
		$this->m_copyList[$copyID] = $arr;
		// 设置进session
		RPCContext::getInstance()->setSession('copy.copyList', $this->m_copyList);
	}

	/**
	 * 将副本信息写入数据库
	 * @throws Exception
	 */
	public function save($copyID)
	{
		// 将当前内容写入数据库
		if (empty($this->m_copyList))
		{
			$copyList = RPCContext::getInstance()->getSession('copy.copyList');
			if (empty($this->m_copyList)) 
			{
				// 获取用户ID，使用用户ID获取副本信息
				$uid = RPCContext::getInstance()->getSession('global.uid');
				if (empty($uid)) {
					Logger::fatal('Can not get copy info from session!');
					throw new Exception('fake');
				}
				// 通过用户ID获取用户副本信息
				$copyList = CopyDao::getUserCopies($uid);
			}
			// 赋值给自己
			$this->m_copyList = $copyList;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('copy.copyList', $this->m_copyList);
		// 操作数据库
		CopyDao::updateCopyInfo($this->m_copyList[$copyID]);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */