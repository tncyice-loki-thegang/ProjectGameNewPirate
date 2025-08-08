<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyPet
 * Description : 宠物数据持有类
 * Inherit     : 
 **********************************************************************************************************************/
class MyPet
{

	private $m_pet;								// 宠物数据
	private $uid;								// 用户ID
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyPet
	 */
	public static function getInstance()
	{
  		if (!self::$_instance instanceof self)
  		{
     		self::$_instance = new self();
  		}
  		return self::$_instance;
	}

	/**
	 * 毁掉单例，单元测试对应
	 */
	public static function release() 
	{
		if (self::$_instance != null) 
		{
			self::$_instance = null;
		}
	}

	/**
	 * 构造函数，获取 session 信息
	 * 
	 * @param $uid								// 用户ID
	 */
	private function __construct() 
	{
		// 从 session 中取得宠物信息
		$petInfo = RPCContext::getInstance()->getSession('sailboat.pet');
		// 获取用户ID，使用用户ID获取宠物信息
		$this->uid = RPCContext::getInstance()->getSession('global.uid');
		// 如果没顺利取得宠物信息
		if (empty($petInfo)) 
		{
			if (empty($this->uid)) 
			{
				Logger::fatal('Can not get pet info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户宠物信息
			$petInfo = PetDao::getPetInfo($this->uid);
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			PetDao::setBufferWithoutSelect($this->uid, $petInfo);
		}
		// 赋值给自己  —— 有可能是 false
		$this->m_pet = EnPet::fixUserPetInfo($petInfo);
		// 调整CD时间并将宠物信息设置进session
		self::getCdEndTime();
	}

	/**
	 * 初始化新宠物信息
	 */
	public function addNewPetInfo()
	{
		// 初始化人物训练信息
		$this->m_pet = PetDao::addNewPetInfo($this->uid);
		// 删掉前端不用的项目
		unset($this->m_pet['status']);
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
		// 返回最新数据
		return $this->m_pet;
	}

	/**
	 * 获取用户宠物信息
	 */
	public function getUserPetInfo()
	{
		// 更新CD时间
		self::getCdEndTime();
		// 更新宠物突飞次数 
		self::getTodayRapidTimes();
		// 获取持有化的数据
		return $this->m_pet;
	}

	/**
	 * 给用户添加一只宠物
	 */
	public function addNewPet($petTID)
	{
		// 获取宠物的id值
		if (!empty($this->m_pet['va_pet_info']))
		{
			$id = max(array_keys($this->m_pet['va_pet_info'])) + 1;
		}
		else 
		{
			$id = 1;
		}
		// 新生产一个白板宠物
		return self::clearPetInfo($id, $petTID);
	}

	/**
	 * 返回CD的截止时间
	 */
	public function getCdEndTime()
	{
		// 空值判断
		if ($this->m_pet === false)
		{
			return ;
		}
		// 记录下当前时间
		$curTime = Util::getTime();
		// 如果时间已经小于当前时刻
		if ($this->m_pet['cd_time'] <= $curTime) 
		{
			// 可以设置为空闲了
			$this->m_pet['cd_status'] = PetConf::RAPID_FREE;
			// 设置为当前时间
			$this->m_pet['cd_time'] = Util::getTime();
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
		// 返回CD时间
		return array('cd_time' => $this->m_pet['cd_time'], 'cd_status' => $this->m_pet['cd_status']);
	}

	/**
	 * 计算CD时间
	 * @param int $time							突飞耗费时刻
	 */
	public function addCdTime($addTime)
	{
		// 记录下当前时间
		$curTime = Util::getTime();
		// 现在时间开始，推算冻结时间
		$freezeTime = $curTime + PetConf::RAPID_MAX_TIME;
		// 先调整一下当前时刻
		self::getCdEndTime();
		// 不管三七二十一，加上时间，判断在上层处理
		$this->m_pet['cd_time'] += $addTime;
		// 看CD的状态是否需要改变
		if ($this->m_pet['cd_time'] >= $freezeTime) 
		{
			// 如果时间超过了约定时间, 那么就设置为 忙碌
			$this->m_pet['cd_status'] = PetConf::RAPID_BUSY;
		}
		Logger::debug("The pet CD status %s， endTime is %s", 
					  $this->m_pet['cd_status'], $this->m_pet['cd_time']);
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 重置CD时刻
	 */
	public function resetCdTime()
	{
		// 可以设置为空闲了
		$this->m_pet['cd_status'] = PetConf::RAPID_FREE;
		// 设置为当前时间
		$this->m_pet['cd_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 重置宠物所有属性 (两种情况会调用，新生宠物和重生的时候)
	 * 
	 * @param int $petID						宠物ID
	 * @param int $petTID						宠物模板ID
	 */
	public function clearPetInfo($petID, $petTID)
	{
		// 设置宠物的技能信息
		$skillInfo = array();
		// 设置普通技能信息, 天赋技能可以读表，而且不会发生改变，所以不记录了
		for ($index = 0; $index < count(btstore_get()->PET[$petTID]['init_skill_ids']); ++$index)
		{
			// 如果这个技能不为0
			if (intval(btstore_get()->PET[$petTID]['init_skill_ids'][$index]) != 0)
			{
				$skillInfo[btstore_get()->PET[$petTID]['init_skill_ids'][$index]] = 
			    	                      array('id' => intval(btstore_get()->PET[$petTID]['init_skill_ids'][$index]),
		            	                        'lv' => intval(btstore_get()->PET[$petTID]['init_skill_lvs'][$index]),
		                	                    'lock' => PetDef::UNLOCK);
			}
		}
		// 查看需要开启的技能栏位个数，除了开启的技能之外，还剩下多少个
		if (empty(btstore_get()->PET[$petTID]['init_skill_ids'][0]))
		{
 			// 如果策划们配置的是空，那么成全他们，用他们做的配置的技能槽个数
			$skillSlotNum = btstore_get()->PET[$petTID]['init_skill_num'];
		}
		else 
		{
			// 否则，如果策划们配置了技能，那么就用策划们配置的技能槽个数减去配置的技能个数(因为技能占据了技能槽)
			$skillSlotNum = btstore_get()->PET[$petTID]['init_skill_num'] - count(btstore_get()->PET[$petTID]['init_skill_ids']);
		}
		// 如果还有剩余的话 , 需要产生新的技能槽
		$skillInfo = self::generateSkillSlot($skillSlotNum, $skillInfo);

		// 设置新宠物的信息
		$petInfo = array('id' => $petID, 
		                 'tid' => $petTID, 
		                 'lv' => 1, 
		                 'exp' => 0, 
		                 'train_start_time' => 0, 
		                 'know_points' => btstore_get()->PET[$petTID]['understand_init'],
		                 'skill_info' => $skillInfo,
						 'in_warehouse' => PetDef::OUT_WARE_HOUSE,
						 // 这里有几个项目是重生时候不变化的，但是新创建的时候又没有，所以这里需要进行一些特殊操作
	 					 'grow_up_exp' => isset($this->m_pet['va_pet_info'][$petID]['grow_up_exp']) ? 
												$this->m_pet['va_pet_info'][$petID]['grow_up_exp'] : 0,
	 					 'grow_up_lv' => isset($this->m_pet['va_pet_info'][$petID]['grow_up_lv']) ? 
											   $this->m_pet['va_pet_info'][$petID]['grow_up_lv'] : 0,
						 'qualifications' => isset($this->m_pet['va_pet_info'][$petID]['qualifications']['pow']) ?
											       $this->m_pet['va_pet_info'][$petID]['qualifications'] : 
											       array('pow' => array('cur' => 0, 'add' => 0), 
											       		 'sen' => array('cur' => 0, 'add' => 0), 
											       		 'int' => array('cur' => 0, 'add' => 0), 
											       		 'phy' => array('cur' => 0, 'add' => 0)),
						 'talent_lv' => 1,
						 'talent_lose_times' =>	0
						);

		// 增加的宠物栏位置也作为宠物ID
		$this->m_pet['va_pet_info'][$petID] = $petInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
		// 返回宠物信息
		return $petInfo;
	}

	/**
	 * 调整宠物的等级和经验值
	 * 
	 * @param int $petID						宠物ID
	 * @param int $exp							训练增加的经验值
	 * @param int $time							训练重置的时刻
	 */
	public function resetExpLv($petID, $exp, $time)
	{
		// 获取宠物当前的模板ID
		$tid = $this->m_pet['va_pet_info'][$petID]['tid'];
		// 获取升级经验表ID
		$lvUpExpID = intval(btstore_get()->PET[$tid]['lv_up_exp_id']);
		// 获取领悟点等级间隔
		$perLv = intval(btstore_get()->PET_ROOM['kown_point_per_lv']);
		// 获取当前宠物等级
		$curLv = $this->m_pet['va_pet_info'][$petID]['lv'];
		// 获取人物等级
		$userLv = EnUser::getUserObj()->getLevel();
		// 获取升下一级所需要的经验
		$needExp = intval(btstore_get()->EXP_TBL[$lvUpExpID][++$curLv]);
		Logger::debug('Pet now exp is %d, need exp is %d.', $exp, $needExp);
		// 如果经验还够升一级的话
		while ($exp >= $needExp)
		{
			// 如果等级已经大于用户等级了
			if ($curLv > $userLv)
			{
				// 清空经验，跳出循环
				$exp = 0;
				break;
			}
			// 如果正好到达了等级间隔
			if ($curLv % $perLv == 0)
			{
				// 加一个领悟点
				++$this->m_pet['va_pet_info'][$petID]['know_points'];
			} 
			// 提升等级
			$this->m_pet['va_pet_info'][$petID]['lv'] = $curLv;
			// 扣除升级消耗的经验值
			$exp -= $needExp;
			// 读取升下一级所需要的经验
			$needExp = intval(btstore_get()->EXP_TBL[$lvUpExpID][++$curLv]);

			// 通知人物模块，重置战斗信息
			self::modifyBattleInfo($petID);
		}
		// 剩余的经验，留着吧
		$this->m_pet['va_pet_info'][$petID]['exp'] = $exp;
		// 调整下时刻
		$this->m_pet['va_pet_info'][$petID]['train_start_time'] = $time;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 内部函数，用来产生技能槽
	 *
	 * @param int $num							需要产生技能槽的个数
	 * @param array $skllInfo					当前技能栏位的信息
	 */
	private function generateSkillSlot($num, $skllInfo)
	{		
		// 如果小于策划们配置的初始技能槽个数，那么表示需要加上那么多个技能槽
		while ($num-- > 0)
		{
			// 获取一个随机数，当做暂时的技能ID
			do
			{
				$tmpID = rand(10000, 99999);
			}
			// 一直随机，直到不存在这个技能ID
			while (isset($skllInfo[$tmpID]));
			// 填写技能槽占位符
			$skllInfo[$tmpID]['id'] = $tmpID;
			// 增加技能等级，技能槽为0
			$skllInfo[$tmpID]['lv'] = 0;
			// 锁定状态
			$skllInfo[$tmpID]['lock'] = PetDef::UNLOCK;
		}
		// 返回产生后的技能状态
		return $skllInfo;
	}

	/**
	 * 开启新的技能槽
	 * @param int $petID						宠物ID
	 */
	public function openNewSkillSlot($petID)
	{
		$this->m_pet['va_pet_info'][$petID]['skill_info'] = 
		 			self::generateSkillSlot(1, $this->m_pet['va_pet_info'][$petID]['skill_info']);
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 重置宠物所有技能
	 * @param int $allPoints					该宠物获得的，全部技能点
	 * @param int $petID						宠物ID
	 * @param int $petTID						宠物模板ID
	 */
	public function resetSkill($allPoints, $petID, $tid)
	{
		$lockPoints = 0;
		foreach ($this->m_pet['va_pet_info'][$petID]['skill_info'] as $key => $skill)
		{
			// 如果某技能被锁定，那么需要返回领悟点
			if ($skill['lock'] == PetDef::LOCK)
			{
				// 获取此技能的初始等级 —— 如果有的话
				$initLv = empty(btstore_get()->PET[$tid]['init_skill'][$skill['id']]) ? 
				                0 : intval(btstore_get()->PET[$tid]['init_skill'][$skill['id']]);
				// 领悟点个数就是现在等级和初始等级的差
				$lockPoints += ($skill['lv'] - $initLv);
			}
			// 没有被锁定，那么就直接清空 —— 被遗弃的技能啊,真可怜
			else
			{
				// 如果这个技能是初始技能的话
				if (!empty(btstore_get()->PET[$tid]['init_skill'][$skill['id']]))
				{
					// 把等级设置为初始等级
					$this->m_pet['va_pet_info'][$petID]['skill_info'][$key]['lv'] = 
					                           intval(btstore_get()->PET[$tid]['init_skill'][$skill['id']]);
				}
				// 如果这个技能是其他技能, 并非与生俱来的
				else 
				{
					// 全部删除掉
					unset($this->m_pet['va_pet_info'][$petID]['skill_info'][$key]);
				}
			}
		}
		// 需要额外处理技能槽
		$skillSlotNum = btstore_get()->PET[$tid]['init_skill_num'] - count($this->m_pet['va_pet_info'][$petID]['skill_info']);
		// 如果小于策划们配置的初始技能槽个数，那么表示需要加上那么多个技能槽
		$this->m_pet['va_pet_info'][$petID]['skill_info'] = self::generateSkillSlot($skillSlotNum, 
		                                                                            $this->m_pet['va_pet_info'][$petID]['skill_info']);

		// 重置领悟点
		$this->m_pet['va_pet_info'][$petID]['know_points'] = $allPoints - $lockPoints;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);

		// 通知人物模块，重置战斗信息
		self::modifyBattleInfo($petID);
		// 返回最新领悟点个数
		return $this->m_pet['va_pet_info'][$petID]['know_points'];
	}

	/**
	 * 增加宠物经验
	 * @param int $petID						宠物ID
	 * @param int $exp							需要增加的经验
	 */
	public function addPetExp($petID, $exp)
	{
		// 增加宠物经验
		$this->m_pet['va_pet_info'][$petID]['exp'] += $exp;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 减去领悟点
	 * 
	 * @param int $petID						宠物ID
	 */
	public function subKnowPoint($petID)
	{
		// 减少领悟点
		--$this->m_pet['va_pet_info'][$petID]['know_points'];
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 加上领悟点
	 * 
	 * @param int $petID						宠物ID
	 * @param int $num							增加的个数
	 */
	public function addKnowPoint($petID, $num)
	{
		// 增加领悟点
		$this->m_pet['va_pet_info'][$petID]['know_points'] += $num;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 开启新技能
	 * @param int $petID						宠物ID
	 * @param int $skillID						宠物技能ID
	 */
	public function openSkill($petID, $skillID)
	{
		// 填写技能等级
		$this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['id'] = $skillID;
		// 增加技能等级
		$this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['lv'] = 1;
		// 锁定状态
		$this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['lock'] = PetDef::UNLOCK;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);

		// 通知人物模块，重置战斗信息
		self::modifyBattleInfo($petID);
	}

	/**
	 * 检查宠物是否技能等级已经满了
	 * 
	 * @param int $petID						宠物ID
	 */
	public function isSkillLevelFull($petID)
	{
		// 获取宠物模板ID
		$petTID = $this->m_pet['va_pet_info'][$petID]['tid'];
		// 如果连技能槽都没有，那么也不能返回全满, 或者 技能栏位没满
		if (empty($this->m_pet['va_pet_info'][$petID]['skill_info']) || 
		    count($this->m_pet['va_pet_info'][$petID]['skill_info']) < btstore_get()->PET[$petTID]['skill_limit'])
		{
			return false;
		}
		// 循环检查所有宠物技能
		foreach ($this->m_pet['va_pet_info'][$petID]['skill_info'] as $skill)
		{
			Logger::debug('Skill level is %d, max level is %d.',
			               $skill['lv'], btstore_get()->PET[$this->m_pet['va_pet_info'][$petID]['tid']]['limit_lv']);
			// 如果还有等级没满，那么返回没有等级全满
			if ($skill['lv'] < btstore_get()->PET[$petTID]['limit_lv'])
			{
				return false;
			}
		}
		// 否则返回全满的信息
		return true;
	}

	/**
	 * 宠物技能升级
	 * 
	 * @param int $petID						宠物ID
	 * @param int $skillID						宠物技能ID
	 * @param int $oldID						旧的技能ID，如果传入这个值，则需要清除掉旧技能
	 */
	public function levelUpSkill($petID, $skillID, $oldID = 0)
	{
		// 需要干掉旧技能，其实就是新技能开启，把占位的技能给干掉
		if (!empty($oldID))
		{
			// 删除旧技能
			unset($this->m_pet['va_pet_info'][$petID]['skill_info'][$oldID]);
			// 填写技能等级
			$this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['id'] = $skillID;
			// 增加技能等级
			$this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['lv'] = 1;
			// 锁定状态
			$this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['lock'] = PetDef::UNLOCK;
		}
		// 正常升级
		else
		{
			// 检查技能是否达到上限
			if (btstore_get()->PET[$this->m_pet['va_pet_info'][$petID]['tid']]['limit_lv'] <=
			    $this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['lv'])
		    {
		    	// 等级已满，升级失败
		    	return false;
		    }
			// 增加技能等级
			++$this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['lv'];
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
		
		// 通知人物模块，重置战斗信息
		self::modifyBattleInfo($petID);
		// 返回成功
		return true;
	}

	/**
	 * 将对应的低等级技能更换为高等级技能
	 * 
	 * @param int $petID						宠物ID
	 * @param int $oldTid						旧的模板ID
	 * @param int $newTid						新的模板ID
	 */
	public function changeSkillToHighLv($petID, $oldTid, $newTid)
	{
		// 获取旧宠物的列表
		$oldSkillArr = btstore_get()->PET[$oldTid]['can_acquire_skills'];
		// 获取新宠物的列表
		$newSkillArr = btstore_get()->PET[$newTid]['can_acquire_skills'];
		// 查看现有技能
		foreach ($this->m_pet['va_pet_info'][$petID]['skill_info'] as $skillID => $skill)
		{
			// 查看现有技能在可以领会技能中的位置，然后更换为相同位置的新技能 (如果新技能表里面有的话)
			for ($i = 0; $i < count($oldSkillArr); ++$i)
			{
				// 如果查询到了, 进行替换
				// 只有宠物现在学会的技能，在旧的表里有，而且新的表里相对应的位置也有。并且新旧不相同 的时候才进行替换
				if ($oldSkillArr[$i] == $skillID && !empty($newSkillArr[$i]) && $skillID != $newSkillArr[$i])
				{
					// 填写技能ID
					$this->m_pet['va_pet_info'][$petID]['skill_info'][$newSkillArr[$i]]['id'] = $newSkillArr[$i];
					// 增加技能等级
					$this->m_pet['va_pet_info'][$petID]['skill_info'][$newSkillArr[$i]]['lv'] = 
					$this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['lv'];
					// 锁定状态
					$this->m_pet['va_pet_info'][$petID]['skill_info'][$newSkillArr[$i]]['lock'] = 
					$this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['lock'];
					// 删除旧技能
					unset($this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]);
					Logger::debug("Pet %d skill changed, old is %d, new is %d.", $petID, $skillID, $newSkillArr[$i]);
				}
			}
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 删除一个宠物
	 */
	public function delPet($petID)
	{
		// 告诉前端，要卸载宠物，别在界面上显示了
		RPCContext::getInstance()->delPet($petID);
		// 删除掉一个宠物
		unset($this->m_pet['va_pet_info'][$petID]);
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
		
		// 通知人物模块，重置战斗信息
		self::modifyBattleInfo($petID);
	}

	/**
	 * 开启新的宠物栏位
	 */
	public function openSlot()
	{
		// 添加一个宠物栏位置
		++$this->m_pet['pet_slots'];
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 开启新的宠物训练栏位
	 */
	public function openTrainSlot()
	{
		// 添加一个宠物栏位置
		++$this->m_pet['train_slots'];
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 更换当前装备宠物
	 * @param int $petID						宠物ID
	 */
	public function changeCurPet($petID)
	{
		// 更换当前装备宠物
		$this->m_pet['cur_pet'] = $petID;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);

		// 通知人物模块，重置战斗信息
		self::modifyBattleInfo($petID);
	}

	/**
	 * 检查是否存在此宠物
	 * @param int $petID						宠物ID
	 */
	public function checkPetExist($petID)
	{
		if (!isset($this->m_pet['va_pet_info'][$petID]) || empty($this->m_pet['va_pet_info'][$petID]))
		{
			return false;
		}
		return true;
	}

	/**
	 * 该宠物是否已经在仓库里面了
	 * 
	 * @param int $petID						宠物ID
	 */
	public function inWareHouse($petID)
	{
		// 查看某个宠物是否已经在仓库里面了
		return $this->m_pet['va_pet_info'][$petID]['in_warehouse'] == PetDef::IN_WARE_HOUSE;
	}

	/**
	 * 增加一个仓库铺位
	 */
	public function openWareHouseSlot()
	{
		// 增加一个栏位
		++$this->m_pet['warehouse_slots'];
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
		// 返回增加后的个数
		return $this->m_pet['warehouse_slots'];
	}

	/**
	 * 数一下谁在仓库睡觉呢，一共有多少宠物
	 */
	public function countInWareHouse()
	{
		// 返回计数
		return self::__countPetsStatus(PetDef::IN_WARE_HOUSE);
	}

	/**
	 * 看一下身上带着多少宠物
	 */
	public function countOutWareHouse()
	{
		// 返回计数
		return self::__countPetsStatus(PetDef::OUT_WARE_HOUSE);
	}

	/**
	 * 数一下宠物的状态
	 * 
	 * @param int $status						是否在仓库里面
	 */
	private function __countPetsStatus($status)
	{
		// 计数器指零
		$num = 0;
		// 然后你懂的，循环嘛，我也没什么高招
		foreach ($this->m_pet['va_pet_info'] as $petID => $pet)
		{
			// 如果在仓库里睡觉，则进行统计操作
			if ($this->m_pet['va_pet_info'][$petID]['in_warehouse'] == $status)
			{
				++$num;
			}
		}
		// 返回计数
		return $num;
	}

	/**
	 * 把宠物放到仓库里
	 * 
	 * @param int $petID						宠物ID
	 */
	public function putInWarehouse($petID)
	{
		// 扔到仓库里面
		$this->m_pet['va_pet_info'][$petID]['in_warehouse'] = PetDef::IN_WARE_HOUSE;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 从仓库里面运出宠物
	 * 
	 * @param int $petID						宠物ID
	 */
	public function getOutWarehouse($petID)
	{
		// 从仓库里面拖出来
		$this->m_pet['va_pet_info'][$petID]['in_warehouse'] = PetDef::OUT_WARE_HOUSE;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 设置洗练结果
	 * 
	 * @param int $petID						宠物ID
	 * @param string $attr						何种属性
	 * @param int $num							具体需要加多少
	 * @param int $top							最大资质是多少
	 */
	public function addQualifications($petID, $attr, $num, $top)
	{
		// 获取当前值
		$cur = $this->m_pet['va_pet_info'][$petID]['qualifications'][$attr]['cur'];
		// 判断是否可以加超过, 如果超过了，就加到最大即可
		$add = ($cur + $num) > $top ? ($top - $cur) : $num;
		// 增加
		$this->m_pet['va_pet_info'][$petID]['qualifications'][$attr]['add'] = $add;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 判断宠物某项资质是否已经到达上线
	 * 
	 * @param string $attr						何种属性
	 * @param int $petID						宠物ID
	 */
	public function isTopAlready($attr, $petID)
	{
		// 获取宠物的模板ID
		$tid = $this->m_pet['va_pet_info'][$petID]['tid'];
		// 获取宠物成长等级
		$lv = $this->m_pet['va_pet_info'][$petID]['grow_up_lv'];
		// 宠物XX资质上限= (宠物资质基础值+宠物资质成长值*进化等级)*宠物XX资质系数
		$top = (btstore_get()->PET[$tid]['qualifications_base'] + btstore_get()->PET[$tid]['qualifications_up'] * $lv) * 
				btstore_get()->PET[$tid][$attr.'_attr'] / PetDef::LITTLE_WHITE_PERCENT;
		// 判断是否已经到达上线
		return $this->m_pet['va_pet_info'][$petID]['qualifications'][$attr]['cur'] >= $top;
	}

	/**
	 * 检查传承次数和被传承次数
	 * 
	 * @param int $curPet						想要废弃的宠物
	 * @param int $objPet						传承对象
	 */
	public function checkTransferTimes($curPet, $objPet)
	{
		// 查看传承次数
		if (isset($this->m_pet['va_pet_info'][$curPet]['transfer_times']) && 
			$this->m_pet['va_pet_info'][$curPet]['transfer_times'] > btstore_get()->PET_ROOM['max_transfer_times'])
		{
			return false;
		}
		// 查看被传承次数
		if (isset($this->m_pet['va_pet_info'][$objPet]['be_transfer_times']) && 
			$this->m_pet['va_pet_info'][$objPet]['be_transfer_times'] > btstore_get()->PET_ROOM['max_be_transfer_times'])
		{
			return false;
		}
		return true;
	}

	/**
	 * 增加传承和被传承次数
	 * 
	 * @param int $curPet						想要废弃的宠物
	 * @param int $objPet						传承对象
	 */
	public function addTransferTimes($curPet, $objPet)
	{
		// 查看传承次数
		if (isset($this->m_pet['va_pet_info'][$curPet]['transfer_times']))
		{
			++$this->m_pet['va_pet_info'][$curPet]['transfer_times'];
		}
		else
		{
			$this->m_pet['va_pet_info'][$curPet]['transfer_times'] = 1;
		}
		// 查看被传承次数
		if (isset($this->m_pet['va_pet_info'][$objPet]['be_transfer_times']))
		{
			++$this->m_pet['va_pet_info'][$objPet]['be_transfer_times'];
		}
		else 
		{
			$this->m_pet['va_pet_info'][$objPet]['be_transfer_times'] = 1;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 传承宠物经验
	 * 
	 * @param int $curPet						想要废弃的宠物
	 * @param int $objPet						传承对象
	 */
	public function transferExp($curPet, $objPet)
	{
		// 获取宠物A的总经验和总成长值
		$curExp = self::__calculateAllExp($curPet);
		// 获取宠物B的成长值，你想不想把策划们都弄死？—— 我也想~~
		$objExp = self::__calculateAllExp($objPet);
		Logger::debug("__calculateAllExp curExp is %s, objExp is %s.", $curExp, $objExp);

		/**************************************************************************************************************
		 * 首选传承宠物的经验,将A宠物的X1%的经验传给B,宠物B的(原有经验+传承经验)计算出来的等级和经验就是传承后的等级
 		 **************************************************************************************************************/
		$this->m_pet['va_pet_info'][$objPet]['exp'] += 
				floor($curExp['normal'] * btstore_get()->PET_ROOM['exp_transfer_persent'] / PetDef::LITTLE_WHITE_PERCENT);

		/**************************************************************************************************************
		 * 传承宠物的进化经验,将A宠物的X2%的进化值传给B,
		 * 宠物B的(原有进化值+传承进化值)计算出来的等级和经验就是传承后的进化等级,该进化等级不超过宠物等级能达到的最大进化等级
 		 **************************************************************************************************************/
		// 只有传承者超过被传承者才可以进行传承
		if ($curExp['grow'] > $objExp['grow'])
		{
			// 增加进化经验
			$addExp = floor($curExp['grow'] * btstore_get()->PET_ROOM['gu_exp_transfer_persent'] / PetDef::LITTLE_WHITE_PERCENT);
			// 如果加超过了，就不行
			if ($objExp['grow'] + $addExp > $curExp['grow'])
			{
				$addExp = $curExp['grow'] - $objExp['grow'];
			}
			// 增加进化经验
			$this->m_pet['va_pet_info'][$objPet]['grow_up_exp'] += $addExp;
		}

		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 获取传承百分比信息
	 * 
	 * @param int $curPet						传承者的宠物ID
	 */
	public function getTransferPersent($curPet)
	{
		if (!empty($this->m_pet['va_pet_info'][$curPet]['transfer_times']) && 
			$this->m_pet['va_pet_info'][$curPet]['transfer_times'] > 1)
		{
			return btstore_get()->PET_ROOM['qua_transfer_persent_up'];
		}
		return btstore_get()->PET_ROOM['qua_transfer_persent'];
	}

	/**
	 * 获取传承所需金币信息
	 * 
	 * @param int $curPet						传承者的宠物ID
	 */
	public function getTransferGold($curPet)
	{
		if (!empty($this->m_pet['va_pet_info'][$curPet]['transfer_times']) && 
			$this->m_pet['va_pet_info'][$curPet]['transfer_times'] > 0)
		{
			return btstore_get()->PET_ROOM['transfer_gold_up'];
		}
		return btstore_get()->PET_ROOM['transfer_gold'];
	}

	/**
	 * 获取传承所需道具信息
	 * 
	 * @param int $curPet						传承者的宠物ID
	 */
	public function getTransferItems($curPet)
	{
		if (!empty($this->m_pet['va_pet_info'][$curPet]['transfer_times']) && 
			$this->m_pet['va_pet_info'][$curPet]['transfer_times'] > 0)
		{
			return array('id' => btstore_get()->PET_ROOM['transfer_items_up']['id'], 
						 'num' => btstore_get()->PET_ROOM['transfer_items_up']['num']);
		}
		return array('id' => btstore_get()->PET_ROOM['transfer_items']['id'], 
					 'num' => btstore_get()->PET_ROOM['transfer_items']['num']);
	}

	/**
	 * 传承宠物资质
	 * 
	 * @param int $curPet						想要废弃的宠物
	 * @param int $objPet						传承对象
	 */
	public function transferQualifications($curPet, $objPet)
	{
		/**************************************************************************************************************
		 * 获取所需的基本信息
 		 **************************************************************************************************************/
		// 获取两个宠物股的模板ID，用来查表
		$curPTID = $this->m_pet['va_pet_info'][$curPet]['tid'];
		$objPTID = $this->m_pet['va_pet_info'][$objPet]['tid'];

		/**************************************************************************************************************
		 * 将宠物A的每一项资质按照算法Y进行计算,得出传承后的该项资质的值
		 *  宠物B的每一项资质都用该方法进行计算，各项资质不会超过宠物该项资质在该进化等级下的资质上限
		 * 
		 *	算法Y
		 * 		设宠物A的一项资质为Y, 宠物B的该项对应资质是Z，先根据Y和Z分别计算出Y1,Z1，再根据Y1,Z1算出Y3，Y3即是该项资质的传承值
 		 **************************************************************************************************************/
		// 对所有资质进行操作
		foreach (PetDef::$ATTR_INDEX as $attr)
		{
			// 获取当前宠物资质
			$Y = $this->m_pet['va_pet_info'][$curPet]['qualifications'][$attr]['cur'];
			// 获取目标宠物当前资质
			$Z = $this->m_pet['va_pet_info'][$objPet]['qualifications'][$attr]['cur'];
			// 如果传承者的资质比被传承者小，则不进行传承
			if ($Y < $Z)
			{
				continue;
			}

			// 获取宠物B的资质成长
			$objQuaUp = intval(btstore_get()->PET[$objPTID]['qualifications_up']);
			/**********************************************************************************************************
			 * Y1 = if(Y <= 宠物B的资质成长*5, Y*1, 
			 *         if(Y < 宠物B的资质成长*10, 宠物B的资质成长*5+（Y-宠物B的资质成长*5)*2,
			 *            if(Y < 宠物B的资质成长*15, 宠物B的资质成长*5+宠物B的资质成长*10+(Y-宠物B的资质成长*10)*3,
			 *		         if(Y < 宠物B的资质成长*20, 宠物B的资质成长*5+宠物B的资质成长*10+宠物B的资质成长*15+(Y-宠物B的资质成长*15)*4,
			 *					 宠物B的资质成长*5+宠物B的资质成长*10+宠物B的资质成长*15+宠物B的资质成长*20+(Y-宠物B的资质成长*20)*5))))  * X3
	 		 **********************************************************************************************************/
			$Y1 = self::__giveMeX1($Y, $objQuaUp);
			$Y1 *= self::getTransferPersent($curPet) / PetDef::LITTLE_WHITE_PERCENT;

			/**********************************************************************************************************
			 * Z1 = if(Z <= 宠物B的资质成长*5,Z*1,
	         *         if(Z < 宠物B的资质成长*10, 宠物B的资质成长*5+（Z-宠物B的资质成长*5)*2,
		     *            if(Z < 宠物B的资质成长*15, 宠物B的资质成长*5+宠物B的资质成长*10+(Z-宠物B的资质成长*10)*3,
			 *              if(Z < 宠物B的资质成长*20, 宠物B的资质成长*5+宠物B的资质成长*10+宠物B的资质成长*15+(Z-宠物B的资质成长*15)*4,
	 		 *					宠物B的资质成长*5+宠物B的资质成长*10+宠物B的资质成长*15+宠物B的资质成长*20+(Z-宠物B的资质成长*20)*5)))) 
	 		 **********************************************************************************************************/
			$Z1 = self::__giveMeX1($Z, $objQuaUp);

			/**********************************************************************************************************
			 * Y2=Y1+Z1
			 * Y3 = if (Y2 <= 宠物B的资质成长*5,Y2*1,
	 		 *			if(Y2 < 宠物B的资质成长*5*3, 宠物B的资质成长*5+(Y2-宠物B的资质成长*5)/2,
			 *				if(Y2 < 宠物B的资质成长*5*6,宠物B的资质成长*10+(Y2-宠物B的资质成长*5*3)/3,
			 *					if(Y2 < 宠物B的资质成长*5*10, 宠物B的资质成长*15+(Y2-宠物B的资质成长*5*6)/4,
	 		 *						宠物B的资质成长*20+(Y2-宠物B的资质成长*5*10)/5))))
	 		 **********************************************************************************************************/
			$Y2 = $Y1 + $Z1;
			if ($Y2 < $objQuaUp * 5)
			{
				$Y3 = $Y2;
			}
			else 
			{
				if ($Y2 < $objQuaUp * 15)
				{
					$Y3 = $objQuaUp * 5 + ($Y2 - $objQuaUp * 5) / 2;
				}
				else 
				{
					if ($Y2 < $objQuaUp * 30)
					{
						$Y3 = $objQuaUp * 10 + ($Y2 - $objQuaUp * 15) / 3;
					}
					else 
					{
						if ($Y2 < $objQuaUp * 50)
						{
							$Y3 = $objQuaUp * 15 + ($Y2 - $objQuaUp * 30) / 4;
						}
						else 
						{
							$Y3 = $objQuaUp * 20 + ($Y2 - $objQuaUp * 50) / 5;
						}
					}
				}
			}
			$Y3 = floor($Y3);
			Logger::debug("Y3 is %d.", $Y3);
			// 检查是否超过上限
			$top = self::__getPetQualificationTop($attr, 
												  $this->m_pet['va_pet_info'][$objPet]['grow_up_lv'],
												  $this->m_pet['va_pet_info'][$objPet]['tid']);
			// 判断是否可以加超过, 如果超过了，就加到最大即可
			$tmp = $Y3 > $top ? $top : $Y3;
			// 不仅不能超过最大，也不能超过头一个
			$this->m_pet['va_pet_info'][$objPet]['qualifications'][$attr]['cur'] = $tmp > $Y ? $Y : $tmp;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);

		// 通知人物模块，重置战斗信息
		self::modifyBattleInfo($objPet);
	}

	public function transferTalentLv($curPet, $objPet)
	{
		// 获取宠物A的总经验和总成长值
		$this->m_pet['va_pet_info'][$objPet]['talent_lv'] = $this->m_pet['va_pet_info'][$curPet]['talent_lv'];

		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 返回该宠物的总经验和总成长值
	 * 
	 * @param int $petID						需要计算的宠物
	 */
	private function __calculateAllExp($petID)
	{
		// 获取宠物当前的模板ID
		$tid = $this->m_pet['va_pet_info'][$petID]['tid'];
		// 获取升级经验表ID
		$lvUpExpID = intval(btstore_get()->PET[$tid]['lv_up_exp_id']);
		// 获取此宠物的当前等级
		$curLv = $this->m_pet['va_pet_info'][$petID]['lv'];
		// 记录经验值
		$totalExp = $this->m_pet['va_pet_info'][$petID]['exp'];
		// 遍历表
		foreach (btstore_get()->EXP_TBL[$lvUpExpID] as $lv => $exp)
		{
			// 遍历到头了，就跳出去。服了服了服了，到现在让我这么做了！我当初要记总经验不让我记，妈的策划就知道一天一个样，根本不体谅程序好实现不好实现！
			if ($lv > $curLv)
			{
				break;
			}
			// 加算总经验
			$totalExp += $exp;
		}

		// 记录经验值
		$totalGrowExp = $this->m_pet['va_pet_info'][$petID]['grow_up_exp'];
		// 记录进化表ID
		$evolutionID = btstore_get()->PET[$tid]['evolution_id'];
		// 获取进化成这个毛样子需要了多少经验
		$totalGrowExp += btstore_get()->PET_EVOLUTION[$evolutionID]['total_evolution_exp'];

		// 返回,我就是怨念着呢，肿么了？
		return array('normal' => $totalExp, 'grow' => $totalGrowExp);
	}
	
	/**
	 * 算X1
	 * 
	 * @param int $x
	 * @param int $quaUp
	 */
	private function __giveMeX1($x, $quaUp)
	{
		if ($x <= $quaUp * 5)
		{
			return $x;
		}
		else 
		{
			if ($x < $quaUp * 10)
			{
				return $x * 2 - $quaUp * 5;
			}
			else 
			{
				if ($x < $quaUp * 15)
				{
					return $x * 3 - $quaUp * 15;
				}
				else 
				{
					if ($x < $quaUp * 20)
					{
						return $x * 4 - $quaUp * 30;
					}
					else 
					{
						return $x * 5 - $quaUp * 50;
					}
				}
			}
		}
	}

	/**
	 * 进化
	 * 
	 * @param int $petID						宠物ID
	 */
	public function evolution($petID)
	{
		// 获取宠物当前的模板ID
		$tid = $this->m_pet['va_pet_info'][$petID]['tid'];
		// 记录进化表ID
		$evolutionID = btstore_get()->PET[$tid]['evolution_id'];
		// 检查成长值和成长等级
		if (btstore_get()->PET_EVOLUTION[$evolutionID]['grow_up_need_exp'] > $this->m_pet['va_pet_info'][$petID]['grow_up_exp'] || 
			btstore_get()->PET_EVOLUTION[$evolutionID]['grow_up_need_lv'] > $this->m_pet['va_pet_info'][$petID]['lv'])
		{
			return false;
		}

		// 修改宠物模板ID
		MyPet::getInstance()->changePetTid($petID, btstore_get()->PET_EVOLUTION[$evolutionID]['after_evolution_id']);
		// 修改宠物技能模板ID
		MyPet::getInstance()->changeSkillToHighLv($petID, $tid, btstore_get()->PET_EVOLUTION[$evolutionID]['after_evolution_id']);
		// 修改领悟点个数 —— 宠物进化前的技能等级保持不变，宠物的剩余领悟点根据2个宠物的初始领悟点的差值进行增加.
		$num = btstore_get()->PET[btstore_get()->PET_EVOLUTION[$evolutionID]['after_evolution_id']]['understand_init'] - 
			   btstore_get()->PET[$tid]['understand_init'];
		// 增加领悟点个数
		MyPet::getInstance()->addKnowPoint($petID, $num);
		// 修改成长值和成长等级
		MyPet::getInstance()->addGrowUpLv($petID, $evolutionID);

		// 通知人物模块，重置战斗信息
		self::modifyBattleInfo($petID);
		// 返回进化完毕 —— 每天都在杀与不杀之间徘徊啊！你懂的！
		return true;
	}

	/**
	 * 对所有资质进行求和
	 * 
	 * @param int $petID						宠物ID
	 */
	public function sumAllQualifications($petID)
	{
		// 初始清零
		$sum = 0;
		// 对所有资质进行求和
		foreach ($this->m_pet['va_pet_info'][$petID]['qualifications'] as $qualification)
		{
			$sum += $qualification['cur'];
		}
		return $sum;
	}

	/**
	 * 提交所有洗练的属性
	 * 
	 * @param int $petID						宠物ID
	 */
	public function commitQualifications($petID)
	{
		// 对所有资质进行提交
		foreach ($this->m_pet['va_pet_info'][$petID]['qualifications'] as $attr => $qualification)
		{
			// 增加各种属性
			$this->m_pet['va_pet_info'][$petID]['qualifications'][$attr]['cur'] += $qualification['add'];
			// 想坑爹，也不能坑爹成负数
			if ($this->m_pet['va_pet_info'][$petID]['qualifications'][$attr]['cur'] < 0)
			{
				$this->m_pet['va_pet_info'][$petID]['qualifications'][$attr]['cur'] = 0;
			}
			$this->m_pet['va_pet_info'][$petID]['qualifications'][$attr]['add'] = 0;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);

		// 通知人物模块，重置战斗信息
		self::modifyBattleInfo($petID);
	}

	/**
	 * 回滚所有洗练的属性
	 * 
	 * @param int $petID						宠物ID
	 */
	public function rollbackQualifications($petID)
	{
		// 对所有资质进行回滚
		foreach ($this->m_pet['va_pet_info'][$petID]['qualifications'] as $attr => $qualification)
		{
			// 清空各种属性
			$this->m_pet['va_pet_info'][$petID]['qualifications'][$attr]['add'] = 0;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 增加某宠物的成长值
	 * 
	 * @param int $petID						宠物ID
	 */
	public function addGrowUpExp($petID, $exp)
	{
		// 增加成长值
		$this->m_pet['va_pet_info'][$petID]['grow_up_exp'] += $exp;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 增加某宠物的进化等级
	 * 
	 * @param int $petID						宠物ID
	 * @param int $evolutionID					进化表ID
	 */
	public function addGrowUpLv($petID, $evolutionID)
	{
		// 获取所需成长值
		$exp = btstore_get()->PET_EVOLUTION[$evolutionID]['grow_up_need_exp'];
		// 去掉升级所需经验
		$this->m_pet['va_pet_info'][$petID]['grow_up_exp'] -= $exp;
		// 增加成长等级
		++$this->m_pet['va_pet_info'][$petID]['grow_up_lv'];
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);

		// 通知人物模块，重置战斗信息
		self::modifyBattleInfo($petID);
	}

	/**
	 * 更换宠物模板ID
	 * 
	 * @param int $petID						宠物ID
	 * @param int $tid							宠物模板ID
	 */
	public function changePetTid($petID, $tid)
	{
		// 修改模板ID
		$this->m_pet['va_pet_info'][$petID]['tid'] = $tid;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 修改技能锁定状态
	 * @param int $petID						宠物ID
	 * @param int $skillID						宠物技能ID
	 * @param int $state						技能锁定状态
	 */
	public function setLockState($petID, $skillID, $state)
	{
		// 设置锁定状态
		$this->m_pet['va_pet_info'][$petID]['skill_info'][$skillID]['lock'] = $state;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 设置训练开始时间, 停止训练时设置为0
	 */
	public function setTrainTime($petID, $time)
	{
		// 设置训练开始时间
		$this->m_pet['va_pet_info'][$petID]['train_start_time'] = $time;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 增加突飞次数
	 */
	public function addRapidTimes()
	{
		// 设置时间和次数
		$this->m_pet['rapid_date'] = Util::getTime();
		$this->m_pet['rapid_times'] = self::getTodayRapidTimes() + 1;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}

	/**
	 * 获取今天的突飞次数
	 */
	public function getTodayRapidTimes()
	{
		// 空值判断
		if ($this->m_pet === false)
		{
			return ;
		}
		// 如果上次突飞的时间是今天之前
		if (!Util::isSameDay($this->m_pet['rapid_date'], PetConf::REFRESH_TIME))
		{
			$this->m_pet['rapid_date'] = Util::getTime();
			$this->m_pet['rapid_times'] = 0;
			// 设置进session
			RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
			// 更新数据库吧，亲，我差点都把你忘了啊，看来宠物的确没啥关注度啊
			self::save();
		}
		// 返回次数
		return $this->m_pet['rapid_times'];
	}

	/**
	 * 计算所有的属性
	 * 
	 * @param int $tid							宠物模板ID
	 * @param arr $attr							宠物所有技能
	 */
	static public function calculateAllAttr($tid, $attr)
	{
		// 增加的属性值
		$buf = array();
		// 获取天赋技能和等级
		$innateSkillID = intval(btstore_get()->PET[$tid]['innate_skill_id']);
		$innateSkillLv = intval(btstore_get()->PET[$tid]['innate_skill_lv']);
		// 如果天赋技能不为空
		if (!empty($innateSkillID))
		{
			// 多个属性值了       2012/06/30
			foreach (btstore_get()->PET_SKILL[$innateSkillID]['attrID'] as $index => $attrID)
			{
				// 计算天赋技能属性加成
				$buf[$attrID] = $innateSkillLv * btstore_get()->PET_SKILL[$innateSkillID]['attrLv'][$index];
			}
		}
		// 遍历该宠物所有技能
		foreach ($attr as $skill)
		{
			// 如果没有任何提升，那么直接看下一个技能
			if (empty(btstore_get()->PET_SKILL[$skill['id']]))
			{
				continue;
			}

			// 多个属性值了       2012/06/30
			foreach (btstore_get()->PET_SKILL[$skill['id']]['attrID'] as $index => $attrID)
			{
				// 如果该属性已经被计算过，那么需要加算
				if (isset($buf[$attrID]))
				{
					// 加算
					$buf[$attrID] += intval($skill['lv']) * 
					                 btstore_get()->PET_SKILL[$skill['id']]['attrLv'][$index];
				}
				// 如果尚未被计算过，赋值即可
				else 
				{
					$buf[$attrID] = intval($skill['lv']) * 
					                btstore_get()->PET_SKILL[$skill['id']]['attrLv'][$index];
				}
			}
		}
		Logger::debug("Pet buffer is %s.", $buf);
		return $buf;
	}
	
	/**
	 * 获取宠物资质最大值
	 * 
	 * @param int $attr							何种资质
	 * @param int $lv							进化等级
	 * @param int $tid							模板ID
	 */
	public static function __getPetQualificationTop($attr, $lv, $tid)
	{
		// 宠物XX资质上限= (宠物资质基础值+宠物资质成长值*进化等级)*宠物XX资质系数
		return (btstore_get()->PET[$tid]['qualifications_base'] + btstore_get()->PET[$tid]['qualifications_up'] * $lv) * 
				btstore_get()->PET[$tid][$attr.'_attr'] / PetDef::LITTLE_WHITE_PERCENT;
	}

	/**
	 * 通知人物模块修改战斗信息
	 */
	private function modifyBattleInfo($petID)
	{
		// 只有当前装备的宠物改变的时候，才进行通知
		if ($petID == $this->m_pet['cur_pet'])
		{
			// 通知人物模块，重置战斗信息
			EnUser::modifyBattleInfo();
		}
	}

	/**
	 * 将数据保存至数据库
	 */
	public function save()
	{
		// 更新到数据库
		PetDao::updPetInfo($this->uid, $this->m_pet);
		// 返回更新信息
		return $this->m_pet;
	}
	
	public function addTalentLv($petID)
	{
		++$this->m_pet['va_pet_info'][$petID]['talent_lv'];

		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);


		// 通知人物模块，重置战斗信息
		self::modifyBattleInfo($petID);
	}
	
	public function setTalentLostTime($petID, $num)
	{
		$this->m_pet['va_pet_info'][$petID]['talent_lose_times'] = $num;

		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.pet', $this->m_pet);
	}


}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */