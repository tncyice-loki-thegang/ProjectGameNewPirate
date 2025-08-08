<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: PortResource.class.php 40064 2013-03-06 07:31:49Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/portResource/PortResource.class.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-03-06 15:31:49 +0800 (三, 2013-03-06) $
 * @version $Revision: 40064 $
 * @brief
 *
 **/

class PortResource
{
	/**
	 *
	 * 港口ID
	 * @var int
	 */
	private $port_id;

	/**
	 *
	 * 页码
	 * @var int
	 */
	private $page_id;

	/**
	 *
	 * 资源ID
	 * @var int
	 */
	private $resource_id;

	/**
	 *
	 * 资源信息
	 * @var array
	 */
	private $resource;

	/**
	 *
	 * 资源信息
	 * @var array
	 */
	private $resourceInfo;

	public function PortResource($port_id, $page_id, $resource_id)
	{
		//检测功能是否开启
		if ( EnSwitch::isOpen(SwitchDef::PORT_RESOURCE_AND_VASSAL) == FALSE )
		{
			Logger::warning('port resource is not open!');
			throw new Exception('fake');
		}

		$this->port_id = $port_id;
		$this->page_id = $page_id;
		$this->resource_id = $resource_id;

		$this->resourceInfo = self::getResouceInfo($this->port_id, $this->page_id, $this->resource_id);
	}

	private function getInfo()
	{
		$this->resource = PortResourceDAO::getPortResource($this->port_id, $this->page_id, $this->resource_id);
		//检测港口资源是否存在
		if ( empty($this->resource) )
		{
			Logger::FATAL('invalid resource_id:%d in page:%d port:%d',
				$this->resource_id, $this->page_id, $this->port_id);
			throw new Exception('fake');
		}
	}

	public function attackResource()
	{
		$return = array(PortDef::PORT_ERROR_CODE_NAME => PortDef::PORT_ERROR_CODE_INVAILD);

		$uid = RPCContext::getInstance()->getUid();

		$user = EnUser::getUserObj();

		//是否超过当前的占领上限
		if ( count(PortResourceDAO::getAllPortResource($uid)) >= $user->getMaxGoldmine() )
		{
			Logger::DEBUG('extend max resource!');
			$return[PortDef::PORT_ERROR_CODE_NAME] = PortDef::PORT_ERROR_CODE_MAX_GOLD_MINE;
			return $return;
		}

		// 检查是否有行动力
		if ($user->subResourceExecution(PortConfig::PORT_CONSUME_EXECUTION) == FALSE)
		{
			$resource_execution = $user->getResourceExecution();
			if ( !empty($resource_execution) )
			{
				$user->subResourceExecution($resource_execution);
			}
			if ($user->subExecution(PortConfig::PORT_CONSUME_EXECUTION- $resource_execution) == FALSE)
			{
				Logger::DEBUG('Execution not enough,need %d.', PortConfig::PORT_CONSUME_EXECUTION);
				return $return;
			}
		}

		//检查战斗CD是否在冷却
		if ($user->addFightCDTime(PortConfig::PORT_RESOURCE_FIGHT_CDTIME) == FALSE)
		{
			Logger::DEBUG('in fight cd!');
			$return[PortDef::PORT_ERROR_CODE_NAME] = PortDef::PORT_ERROR_CODE_IN_FIGHT_CD;
			return $return;
		}

		$userFormation = EnFormation::getFormationInfo($uid);
		// 检查英雄血量是否足够
		if ( EnFormation::checkUserFormation($uid, $userFormation) != 'ok' )
		{
			Logger::DEBUG('no enough hp or no hero!');
			$return[PortDef::PORT_ERROR_CODE_NAME] = PortDef::PORT_ERROR_CODE_NO_HP;
			return $return;
		}

		// 将阵型ID设置为用户当前默认阵型
		$formationID = $user->getCurFormation();
		$user->prepareItem4CurFormation();
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation);
		Logger::debug('The hero list is %s', $userFormationArr);
		$battle_user=array(
						'uid' => $uid,
						'name' => $user->getUname(),
			            'level' => $user->getLevel(),
			            'flag' => 0,
						'isPlayer' => 1,
			            'formation' => $formationID,
			            'arrHero' => $userFormationArr
					);

		// 战斗后英雄的血量
		$heroArr = array();

		//得到锁
		$locker = new Locker();
		$locker->lock($this->getPortResourceLockerName());

		//得到资源信息
		$this->getInfo();

		//检测是否在保护期内
		if ( Util::getTime() - $this->resource[PortDef::PORT_SQL_OCCUPY_TIME]
			< $this->resourceInfo[PortDef::PORT_RESOURCE_PROTECTED_TIME] )
		{
			Logger::DEBUG('in protected time!port_id:%d, page_id:%d resource_id:%d',
				$this->port_id, $this->page_id, $this->resource_id);
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			$return[PortDef::PORT_ERROR_CODE_NAME] = PortDef::PORT_ERROR_CODE_IN_PROTECTED_TIME;
			return $return;
		}

		$armyID = $this->resourceInfo[PortDef::PORT_RESOURCE_ARMY];
		if ( !isset(btstore_get()->ARMY[$armyID]) || !isset(btstore_get()->ARMY[$armyID]['monster_list_id']) )
		{
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			Logger::FATAL('invalid armyID:%d', $armyID);
			throw new Exception('fake');
		}

		//占领的用户UID
		$occupy_uid = $this->resource[PortDef::PORT_SQL_UID];

		if ( $occupy_uid == $uid )
		{
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			Logger::DEBUG('occpuy is you! can not attack!');
			throw new Exception('fake');
		}

		//当前是否有人占领
		if ( empty($occupy_uid) )
		{

			$teamID = btstore_get()->ARMY[$armyID]['monster_list_id'];

			// 敌人信息
			$enemyFormation = EnFormation::getBossFormationInfo($teamID);

			// 将对象转化为数组
			$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation);
			Logger::DEBUG('The boss list is %s', $enemyFormationArr);

			// 调用战斗模块
			$bt = new Battle();
			$atkRet = $bt->doHero($battle_user,
			                      array(
			                      		'uid' => $armyID,
			                      		'name' => btstore_get()->ARMY[$armyID]['name'],
			                            'level' => btstore_get()->ARMY[$armyID]['lv'],
			                            'flag' => 0,
			                            'formation' => btstore_get()->TEAM[$teamID]['fid'],
			                      		'isPlayer' => 0,
			                            'arrHero' => $enemyFormationArr),
			                      0,
			                      NULL,
			                      NULL,
			                      array (
		                      			'bgid' => btstore_get()->ARMY[$armyID]['background_id'],
		                      			'musicId' => btstore_get()->ARMY[$armyID]['music_path'],
			                      		'type' => BattleType::RESOURCE,
			                      	)
			                      );

			// 战斗系统返回值
			Logger::debug('Ret from battle is %s.', $atkRet);
		}
		else
		{
			//当前占领者的阵型信息
			$occupyUserFormation = EnFormation::getFormationInfo($occupy_uid);
			EnFormation::checkUserFormation($occupy_uid, $occupyUserFormation);
			//当前用户的信息
			$occupy_user = EnUser::getUserObj($occupy_uid);
			$occupy_user->prepareItem4CurFormation();
			$occupyUserFormationArr = EnFormation::changeForObjToInfo($occupyUserFormation);

			$battle_occupy_user = array (
										'uid' => $occupy_uid,
										'name' => $occupy_user->getUname(),
			                            'level' => $occupy_user->getLevel(),
			                            'flag' => 0,
			                            'formation' => $occupy_user->getCurFormation(),
			                            'isPlayer' => 1,
			                            'arrHero' => $occupyUserFormationArr
									);

			$bt = new Battle();
			$atkRet = $bt->doHero(
							$battle_user,
							$battle_occupy_user,
							0,
							NULL,
							NULL,
			                array (
		                    	'bgid' => btstore_get()->ARMY[$armyID]['background_id'],
		                    	'musicId' => btstore_get()->ARMY[$armyID]['music_path'],
			                	'type' => BattleType::RESOURCE,
			                )
						);

			// 减血Occupy User
			EnFormation::subUserHeroHp($atkRet['server']['team2'], $occupy_uid);

		}

		// 减血User
		$heroArr = EnFormation::subUserHeroHp($atkRet['server']['team1']);

		// 判断是否胜利
		$isSuccess = BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'];

		//send mail
		if ( !empty($occupy_uid) )
		{
			$mail_occupy_user = $occupy_user->getTemplateUserInfo();
			MailTemplate::sendPortResourceAttack($uid, $mail_occupy_user, $atkRet['server']['brid'], $isSuccess);
			$mail_user = $user->getTemplateUserInfo();
			$acquire = $this->acquireResource();
			MailTemplate::sendPortResourceDefend($occupy_uid, $mail_user,
			 $atkRet['server']['brid'], !$isSuccess, $acquire['time'], $acquire['belly']);
		}
		else
		{
			//如果攻击NPC失败,则发送邮件
			if ( $isSuccess == FALSE )
			{
				MailTemplate::sendPortResourceAttackDefaultFailed($uid, $atkRet['server']['brid']);
			}
		}

		if ( $isSuccess == TRUE )
		{
			//cancelTimer
			if ( !empty($this->resource[PortDef::PORT_SQL_DUE_TIMER]) )
			{
				TimerTask::cancelTask($this->resource[PortDef::PORT_SQL_DUE_TIMER]);
			}

			//如果该资源被他人占有，给他人发送所得
			if ( !empty($occupy_uid) )
			{
				$acquire = $this->acquireResource();
				$occupy_user->addBelly($acquire['belly']);
			}

			//addTimer
			$time = Util::getTime() + $this->resourceInfo[PortDef::PORT_RESOURCE_TIME];
			$timer_id = TimerTask::addTask(0, $time, 'port.dueResource',
				 array($uid, $this->port_id, $this->page_id, $this->resource_id,Util::getTime()));

			$this->resource[PortDef::PORT_SQL_UID] = $uid;
			$this->resource[PortDef::PORT_SQL_OCCUPY_TIME] = Util::getTime();
			$this->resource[PortDef::PORT_SQL_DUE_TIMER] = $timer_id;
			$this->resource[PortDef::PORT_SQL_IS_EXCAVATE] = 0;
			$this->resource[PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME] = 0;
			$this->resource[PortDef::PORT_SQL_PLUNDER_TIME] = 0;
			$this->resource[PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE] = 0;

			// 设置当前的数据
			PortResourceDAO::setPortResource($this->port_id, $this->page_id, $this->resource_id,
				 $uid, Util::getTime(), $timer_id, 0, 0, 0,0);

			//广播资源更新
			$this->sendMsg();
			if ( !empty($occupy_uid) )
			{
				$this->sendMsg4DueTime($occupy_uid, 0);
			}

			//调用节日系统
			EnFestival::addResourcePoint();

		}

		//更新数据
		$user->update();
		if ( !empty($occupy_uid) )
		{
			$occupy_user = EnUser::getUserObj($occupy_uid);
			$occupy_user->update();
		}

		//调用每日任务
		EnDaytask::occupyResourse();

		//调用活跃度系统
		EnActive::addResourceTimes();

		//释放锁
		$locker->unlock($this->getPortResourceLockerName());

		//通知任务系统
		TaskNotify::operate(TaskOperateType::OCCUPY_RESOURCE);

		// 将战斗结果返回给前端
		return array(
			PortDef::PORT_ERROR_CODE_NAME => PortDef::PORT_ERROR_CODE_OK,
			'fight_ret' => $atkRet['client'],
			'blood_package' => $user->getBloodPackage(),
			'fight_cdtime' => $user->getFightCDTime(),
		    'cur_hp' => $heroArr,
			'appraisal' => $atkRet['server']['appraisal']
		);
	}

	public function excavate()
	{
		$uid = RPCContext::getInstance()->getUid();

		$return = FALSE;

		//得到资源信息
		$this->getInfo();

		//资源不属于你,你不能淘金
		if ( $this->resource[PortDef::PORT_SQL_UID] != $uid )
		{
			Logger::DEBUG('the resource belong to uid:%d not to you:%d',
				$this->resource[PortDef::PORT_SQL_UID], $uid);
			return $return;
		}

		$time = Util::getTime();
		if ( $time < ExcavateUtil::getExcavateStartTime() ||
			$time > ExcavateUtil::getExcavateEndTime() )
		{
			Logger::DEBUG('not in excavate time!');
			return $return;
		}

		if ( $time < $this->resource[PortDef::PORT_SQL_OCCUPY_TIME] ||
			$time > $this->resource[PortDef::PORT_SQL_OCCUPY_TIME] + ExcavateUtil::getExcavateTime() )
		{
			Logger::DEBUG('not in excavate time!');
			return $return;
		}

		if ( $this->resource[PortDef::PORT_SQL_IS_EXCAVATE] == 1 )
		{
			Logger::DEBUG('the resource has excavate!');
			return $return;
		}

		$this->resource[PortDef::PORT_SQL_IS_EXCAVATE] = 1;
		PortResourceDAO::setPortResourceExcavate($this->port_id, $this->page_id, $this->resource_id, 1);

		//广播资源数据更新
		$this->sendMsg();
		return TRUE;
	}

	public function plunder()
	{
		$return = array();
		$uid = RPCContext::getInstance()->getUid();
		$port_berth = new PortBerth($uid);

		//是否有足够的次数
		if ( ! $port_berth->canPlunder() )
		{
			Logger::DEBUG('can not plunder! no time or not cool down!');
			return $return;
		}

		//得到锁
		$locker = new Locker();
		$locker->lock($this->getPortResourceLockerName());

		//得到资源信息
		$this->getInfo();

		$occupy_uid = $this->resource[PortDef::PORT_SQL_UID];
		//资源矿无人占领
		if ( empty($occupy_uid) )
		{
			Logger::DEBUG('port resource is no occupy user!');
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			return $return;
		}

		//资源矿必须开启淘金模式
		$is_excavate = $this->resource[PortDef::PORT_SQL_IS_EXCAVATE];
		if ( empty($is_excavate) )
		{
			Logger::DEBUG('port resource is no in excavate status!');
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			return $return;
		}

		if ( $occupy_uid == $uid )
		{
			Logger::DEBUG('occpuy is you! can not plunder!');
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			return $return;
		}

		$port_id = $port_berth->getPort();
		$other_port_berth = new PortBerth($occupy_uid);
		$other_port_id = $other_port_berth->getPort();
		//掠夺者和被掠夺者必须处于同一个城镇
		if ( Port::getTownByPort($port_id) != Port::getTownByPort($other_port_id) )
		{
			Logger::DEBUG('user:%d and user:%d not in same town', $uid, $occupy_uid);
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			return $return;
		}

		$time = Util::getTime();
		//当前资源矿必须处于非掠夺保护状态
		if ( $this->resource[PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME] >= $time )
		{
			Logger::DEBUG('in plunder protected time!');
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			return $return;
		}

		$user = EnUser::getUserObj($uid);
		$user_level = $user->getLevel();

		$occupy_user = EnUser::getUserObj($occupy_uid);
		$occupy_user_level = $occupy_user->getLevel();

		//计算是否触发战斗
		//max(战斗基础权重+min((被掠夺者等级-掠夺者等级)*掠夺战斗等级系数，掠夺战斗系数上限),掠夺战斗系数下限)
		$battle_probability = max(ExcavateUtil::getPlunderBattleBasicProBability() +
			min(($occupy_user_level - $user_level) * ExcavateUtil::getPlunderBattleModulus(),
			ExcavateUtil::getMaxPlunderBattleModulus()),
			ExcavateUtil::getMinPlunderBattleModulus() );

		$is_battle = rand(0, PortConfig::MODULUS) < $battle_probability;
		$is_success = FALSE;
		$replay_id = 0;

		if ( $is_battle == TRUE )
		{
			// 将阵型ID设置为用户当前默认阵型
			$userFormation = EnFormation::getFormationInfo($uid);
			$formationID = $user->getCurFormation();
			$user->prepareItem4CurFormation();
			$userFormationArr = EnFormation::changeForObjToInfo($userFormation, TRUE);
			Logger::debug('The hero list is %s', $userFormationArr);
			$battle_user=array(
							'uid' => $uid,
							'name' => $user->getUname(),
				            'level' => $user->getLevel(),
				            'flag' => 0,
							'isPlayer' => 1,
				            'formation' => $formationID,
				            'arrHero' => $userFormationArr
						);

			//当前占领者的阵型信息
			$occupyUserFormation = EnFormation::getFormationInfo($occupy_uid);
			EnFormation::checkUserFormation($occupy_uid, $occupyUserFormation);
			//当前用户的信息
			$occupy_user = EnUser::getUserObj($occupy_uid);
			$occupy_user->prepareItem4CurFormation();
			$occupyUserFormationArr = EnFormation::changeForObjToInfo($occupyUserFormation, TRUE);

			$battle_occupy_user = array (
										'uid' => $occupy_uid,
										'name' => $occupy_user->getUname(),
			                            'level' => $occupy_user->getLevel(),
			                            'flag' => 0,
			                            'formation' => $occupy_user->getCurFormation(),
			                            'isPlayer' => 1,
			                            'arrHero' => $occupyUserFormationArr
									);

			$armyID = $this->resourceInfo[PortDef::PORT_RESOURCE_ARMY];

			$bt = new Battle();
			$atkRet = $bt->doHero(
							$battle_user,
							$battle_occupy_user,
							0,
							NULL,
							NULL,
			                array (
		                    	'bgid' => btstore_get()->ARMY[$armyID]['background_id'],
		                    	'musicId' => btstore_get()->ARMY[$armyID]['music_path'],
			                	'type' => BattleType::RESOURCE_PLUNDER,
			                )
						);
			// 判断是否胜利
			$is_success = BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'];
			$replay_id = $atkRet['server']['brid'];
		}

		$plunder_success = FALSE;
		if ( $is_battle == TRUE && $is_success == FALSE )
		{
			//发送邮件
			MailTemplate::sendExploitResourceDefend($occupy_uid,
				$user->getTemplateUserInfo(), $replay_id, FALSE);
			MailTemplate::sendExploitResourceAttack($uid,
				$occupy_user->getTemplateUserInfo(), $replay_id, FALSE);
			$port_berth->addPlunderCd(ExcavateUtil::getPlunderFailedCdTime());
		}
		else
		{
			$plunder_success = TRUE;
			//得到收获的belly数量
			$guild_id = $occupy_user->getGuildId( );
			$level = $occupy_user->getLevel();
			if ( !empty($guild_id) )
			{
				$buffer = GuildLogic::getBufferByGuildId($guild_id);
				$resource_buffer = floatval($buffer['resourceAddition']) / PortConfig::MODULUS;
			}
			else
			{
				$resource_buffer = 0;
			}

			//按策划要求，将公式中的等级加上上下限
			if ( Port::getPortResourceUserLevelUp($this->port_id) !== NULL
					&& Port::getPortResourceUserLevelLow($this->port_id)!== NULL )
			{
				if ( $level > Port::getPortResourceUserLevelUp($this->port_id) )
				{
					$level = Port::getPortResourceUserLevelUp($this->port_id);
				}
				if ( $level < Port::getPortResourceUserLevelLow($this->port_id) )
				{
					$level = Port::getPortResourceUserLevelLow($this->port_id);
				}
			}

			//得到收获的belly数量
			$belly = intval( round( floatval($this->resourceInfo[PortDef::PORT_RESOURCE_OUTPUT])
				* ExcavateUtil::getPlunderOutputMulitiply() * $level * PortConfig::PORT_RESOURCE_MODULUS * (1 + $resource_buffer) ) );

			if ( $user->addBelly($belly) == FALSE )
			{
				Logger::FATAL('add belly failed!');
				//释放锁
				$locker->unlock($this->getPortResourceLockerName());
				return $return;
			}

			$this->resource[PortDef::PORT_SQL_PLUNDER_TIME] += 1;
			$this->resource[PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME] = Util::getTime() + ExcavateUtil::getPlunderProtectedTime();

			//cancelTimer
			if ( !empty($this->resource[PortDef::PORT_SQL_DUE_TIMER]) )
			{
				TimerTask::cancelTask($this->resource[PortDef::PORT_SQL_DUE_TIMER]);
			}

			$due_timer_id = NULL;

			//过期时间需要加上金币延长时间
			$extendtime=0;
			$grade_id=$this->resource[PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE];
			if ($grade_id>0 && isset(btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]))
			{
				$extendtime=btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]['time'];
			}

			$due_time = intval($this->resource[PortDef::PORT_SQL_OCCUPY_TIME]
					+ $this->resourceInfo[PortDef::PORT_RESOURCE_TIME]
					+ $extendtime//加上金币延长时间
					- $this->resource[PortDef::PORT_SQL_PLUNDER_TIME] *
					ExcavateUtil::getPlunderSubOccpuyTime());
			//如果掠夺后资源矿已经到期
			if ( $due_time <= Util::getTime() )
			{
				$this->__givenupResource();
				$this->sendMsg4DueTime($occupy_uid, 0);
			}
			else
			{
				$due_timer_id = TimerTask::addTask(0, $due_time, 'port.dueResource',
				 	array($occupy_uid, $this->port_id, $this->page_id, $this->resource_id,$this->resource[PortDef::PORT_SQL_OCCUPY_TIME]));
				$this->resource[PortDef::PORT_SQL_DUE_TIMER] = $due_timer_id;

				PortResourceDAO::setPortResource($this->port_id,
				$this->page_id, $this->resource_id, NULL, NULL, $due_timer_id, NULL,
				$this->resource[PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME],
				$this->resource[PortDef::PORT_SQL_PLUNDER_TIME],
				NULL);//这时不应该更改金币延长次数
				//广播资源更新
				$this->sendMsg();
				$this->sendMsg4DueTime($occupy_uid, $due_time);
			}

			$port_berth->addPlunderTime(1);

			//更新user
			$user->update();

			//发送邮件
			if ( $is_battle == FALSE )
			{
				MailTemplate::sendExploitResourceNoBattleRecord($user->getTemplateUserInfo(),
					$occupy_user->getTemplateUserInfo(), $belly);
			}
			else
			{
				MailTemplate::sendExploitResourceDefend($occupy_uid,
					$user->getTemplateUserInfo(), $replay_id, TRUE, $belly);
				MailTemplate::sendExploitResourceAttack($uid,
					$occupy_user->getTemplateUserInfo(), $replay_id, TRUE, $belly);
			}
		}

		$return = array();
		$return['plunder_success'] = $plunder_success;
		$return['is_battle'] = $is_battle;
		$return['belly'] = $user->getBelly();
		if ( $return['is_battle'] )
		{
			$return['client'] = $atkRet['client'];
			$return['appraisal'] = $atkRet['server']['appraisal'];
		}

		//释放锁
		$locker->unlock($this->getPortResourceLockerName());

		return $return;
	}

	public function givenupResource()
	{
		$uid = RPCContext::getInstance()->getUid();

		$return = array('giveup_success' => FALSE);

		//得到锁
		$locker = new Locker();
		$locker->lock($this->getPortResourceLockerName());

		//得到资源信息
		$this->getInfo();

		//资源不属于你,你不能放弃
		if ( $this->resource[PortDef::PORT_SQL_UID] != $uid )
		{
			Logger::DEBUG('the resource belong to uid:%d not to you:%d',
				$this->resource[PortDef::PORT_SQL_UID], $uid);
			return $return;
		}

		//得到资源的收益
		$acquire = $this->acquireResource();
		$belly = $acquire['belly'];

		//取消计时器
		if ( !empty($this->resource[PortDef::PORT_SQL_DUE_TIMER]) )
		{
			TimerTask::cancelTask($this->resource[PortDef::PORT_SQL_DUE_TIMER]);
		}

		//放弃资源
		$this->__givenupResource();

		//释放锁
		$locker->unlock($this->getPortResourceLockerName());

		//得到当前的belly值
		$user = EnUser::getUserObj();

		return array (
			'giveup_success' => TRUE,
			'belly' => $user->getBelly(),
		);
	}

	public function dueResource($uid,$occupy_time=0)
	{
		//得到锁
		$locker = new Locker();
		$locker->lock($this->getPortResourceLockerName());

		//得到资源信息
		$this->getInfo();
		if ( $uid != $this->resource[PortDef::PORT_SQL_UID] )
		{
			Logger::WARNING('the resource belong to user:%d not you:%d',
				 $this->resource[PortDef::PORT_SQL_UID], $uid);
			return;
		}
		if ($occupy_time >0 && $occupy_time!=$this->resource[PortDef::PORT_SQL_OCCUPY_TIME])
		{
			Logger::WARNING('invalid timer ');
			return;
		}
		//放弃资源
		$this->__givenupResource();

		//释放锁
		$locker->unlock($this->getPortResourceLockerName());

	}

	private function __givenupResource()
	{

		$acquire = $this->acquireResource();
		$belly = $acquire['belly'];

		$uid = $this->resource[PortDef::PORT_SQL_UID];

		$user = EnUser::getUserObj($uid);

		if ( $user->addBelly($belly) == FALSE )
		{
			Logger::FATAL('add belly failed!');
			throw new Exception('fake');
		}

		//将矿山置为无人占领
		PortResourceDAO::setPortResource($this->port_id, $this->page_id, $this->resource_id,
			0, 0, 0, 0, 0, 0,0);

		$this->resource = array (
				PortDef::PORT_SQL_UID => 0,
				PortDef::PORT_SQL_OCCUPY_TIME => 0,
				PortDef::PORT_SQL_DUE_TIMER => 0,
				PortDef::PORT_SQL_IS_EXCAVATE => 0,
				PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME => 0,
				PortDef::PORT_SQL_PLUNDER_TIME => 0,
				PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE => 0,
		);

		$user->update();

		//sendmail
		MailTemplate::sendPortResourceDue($uid, $belly);

		//广播资源更新
		$this->sendMsg();
	}

	public function resourceInfo() {

		//得到资源信息
		$this->getInfo();

		if ( $this->resource[PortDef::PORT_SQL_UID] == 0 )
		{
			return array();
		}
		else
		{
			$uid = $this->resource[PortDef::PORT_SQL_UID];
			$user = EnUser::getUserObj($uid);
			$guildId = intval($user->getGuildId());
			$guildEmblem = 0;
			$guildName = '';
			if ( !empty($guildId) )
			{
				$guildInfo = GuildLogic::getRawGuildInfoById($guildId);
				$guildEmblem = $guildInfo['current_emblem_id'];
				$guildName = $guildInfo['name'];
			}
			//过期时间需要加上金币延长时间
			$extendtime=0;
			$grade_id=$this->resource[PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE];
			if ($grade_id>0 && isset(btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]))
			{
				$extendtime=btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]['time'];
			}
			return array(
				'resource_id' => $this->resource_id,
				'uid' => intval($uid),
				'level' => intval($user->getLevel()),
				'uname'	=> $user->getUname(),
				'group_id' => $user->getGroupId(),
				'guild_id' => $guildId,
				'guild_emblem' => $guildEmblem,
				'guild_name' => $guildName,
				'due_time' => intval($this->resource[PortDef::PORT_SQL_OCCUPY_TIME]
					+ $this->resourceInfo[PortDef::PORT_RESOURCE_TIME])
					+$extendtime //加上金币延长时间
					- $this->resource[PortDef::PORT_SQL_PLUNDER_TIME] *
					ExcavateUtil::getPlunderSubOccpuyTime(),
				'protect_time' => intval($this->resource[PortDef::PORT_SQL_OCCUPY_TIME]
					+ $this->resourceInfo[PortDef::PORT_RESOURCE_PROTECTED_TIME]),
				'plunder_protect_time' => $this->resource[PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME],
				'is_excavate' => $this->resource[PortDef::PORT_SQL_IS_EXCAVATE],
				'plunder_time' => $this->resource[PortDef::PORT_SQL_PLUNDER_TIME],
				'occpuy_time' => $this->resource[PortDef::PORT_SQL_OCCUPY_TIME],
				'gold_extend_count' => ($this->resource[PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE]>0)?1:0,
			);
		}
	}

	private function acquireResource()
	{
		//这里需要判断一下，是不是需要加上金币延长的时间
		$extendtime=0;
		$grade_id=$this->resource[PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE];
		if ($grade_id>0 && isset(btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]) )
		{
			$extendtime=btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]['time'];
		}
		$time = Util::getTime() - $this->resource[PortDef::PORT_SQL_OCCUPY_TIME];
		//如果持续时间超过最大持续时间,则设置为最大持续时间
		if ( $time > ($this->resourceInfo[PortDef::PORT_RESOURCE_TIME] +$extendtime))
		{
			$time = $this->resourceInfo[PortDef::PORT_RESOURCE_TIME]+$extendtime;
		}
		$excavate_buffer = 0;
		if ( $this->resource[PortDef::PORT_SQL_IS_EXCAVATE] == PortDef::HAS_EXCAVATE )
		{
			if ( $time > ( $this->resourceInfo[PortDef::PORT_RESOURCE_TIME] +$extendtime - $this->resource[PortDef::PORT_SQL_PLUNDER_TIME]
				* ExcavateUtil::getPlunderSubOccpuyTime() ) )
			{
				$time = $this->resourceInfo[PortDef::PORT_RESOURCE_TIME]+$extendtime - $this->resource[PortDef::PORT_SQL_PLUNDER_TIME]
					* ExcavateUtil::getPlunderSubOccpuyTime();
			}
			$excavate_buffer = ExcavateUtil::getExcavateOutputMulitiply() / PortConfig::MODULUS;
		}

		$uid = $this->resource[PortDef::PORT_SQL_UID];
		//得到收获的belly数量
		$user = EnUser::getUserObj($uid);
		$guild_id = $user->getGuildId();
		$level = $user->getLevel();

		if ( Port::getPortResourceUserLevelUp($this->port_id) !== NULL
			&& Port::getPortResourceUserLevelLow($this->port_id)!== NULL )
		{
			if ( $level > Port::getPortResourceUserLevelUp($this->port_id) )
			{
				$level = Port::getPortResourceUserLevelUp($this->port_id);
			}
			if ( $level < Port::getPortResourceUserLevelLow($this->port_id) )
			{
				$level = Port::getPortResourceUserLevelLow($this->port_id);
			}
		}

		if ( !empty($guild_id) )
		{
			$buffer = GuildLogic::getBufferByGuildId($guild_id);
			$resource_buffer = floatval($buffer['resourceAddition']) / PortConfig::MODULUS;
		}
		else
		{
			$resource_buffer = 0;
		}

		//得到收获的belly数量
		$belly = intval( round( floatval($this->resourceInfo[PortDef::PORT_RESOURCE_OUTPUT])
			* $time * $level * PortConfig::PORT_RESOURCE_MODULUS * (1 + $resource_buffer) )
			* ( 1 + $excavate_buffer ) );

		//增加节日加成
		$belly = intval($belly * EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_RESOURCE));

		//最少获得1
		if ( $belly <= 0 )
		{
			$belly = 1;
		}

		return array(
				'time' => $time,
				'belly' => $belly,
		);
	}

	private function sendMsg() {

		//向前端广播数据
		RPCContext::getInstance()->sendFilterMessage('resource', $this->port_id,
			 	're.port.updateRes',
			 	array(
			 			'port_id' => $this->port_id,
			 			'page_id' => $this->page_id,
			 			'resource_info' => $this->resourceInfo(),
			 	)
		);
	}

	private function sendMsg4DueTime($occupy_uid, $time)
	{
		//向前端推送数据
		RPCContext::getInstance()->sendMsg(array($occupy_uid),
			 	're.resource.dueTime',
			 	array(
			 			'port_id' => $this->port_id,
			 			'page_id' => $this->page_id,
			 			'resource_id' => $this->resource_id,
			 			'due_time' => $time,
			 	)
		);
	}

	/**
	 *
	 * 得到港口资源信息
	 *
	 * @param int $port_id						港口ID
	 * @param int $page_id						港口资源页码
	 * @param int $resource_id					港口资源ID
	 *
	 * @throws Exception						如果数据在btstore中不存在,则throw错误
	 */
	public static function getResouceInfo($port_id, $page_id, $resource_id)
	{
		if ( !isset(btstore_get()->PORT[$port_id]) )
		{
			Logger::FATAL("invalid port_id:%d", $port_id);
			throw new Exception('config');
		}
		else
		{
			if ( !isset(btstore_get()->PORT[$port_id][PortDef::PORT_RESOURCE_GROUPS][$page_id]) )
			{
				Logger::FATAL("invalid page_id:%d in port:%d", $page_id, $port_id);
				throw new Exception('config');
			}
			else
			{
				$resouce_group_id = btstore_get()->PORT[$port_id][PortDef::PORT_RESOURCE_GROUPS][$page_id];
				if ( !isset(btstore_get()->PORTRESOURCE[$resouce_group_id]) )
				{
					Logger::FATAL("invalid port resource group id:%d", $resouce_group_id);
					throw new Exception('config');
				}
				else
				{
					if ( !isset(btstore_get()->PORTRESOURCE[$resouce_group_id][PortDef::PORT_RESOURCE_LIST][$resource_id]) )
					{
						Logger::FATAL("invalid port resource id:%d in group:%d",
							$resource_id, $resouce_group_id);
						throw new Exception('config');
					}
					else
					{
						return btstore_get()->PORTRESOURCE[$resouce_group_id][PortDef::PORT_RESOURCE_LIST][$resource_id];
					}
				}
			}
		}
	}

	private function getPortResourceLockerName()
	{
		return PortDef::PORT_RESOURCE_LOCKER_PRE . $this->port_id .
			PortDef::PORT_RESOURCE_LOCKER_CONJ . $this->page_id .
			PortDef::PORT_RESOURCE_LOCKER_CONJ . $this->resource_id;
	}

	/**
	 * 利用金币延长占领时间
	 * @param int $uid
	 * @param int $grade_id 档次id
	 * @return int
	 */
	public function extendTimeByGold($uid,$grade_id)
	{
		//返回值 1 ok
		/*0 未知错误
		 *-1 该资源不属于你
		 *-2  档次id不对
		 *-3  金币不够
		 *-4 行动力不够
		 *-5 不在可延长的时间段内
		 *-6 已经延长过了
		 */
		$return = 0;

		//先获得锁
		$locker = new Locker();
		$locker->lock($this->getPortResourceLockerName());

		//得到资源信息
		$this->getInfo();

		//资源不属于你,你不能延长
		if ( $this->resource[PortDef::PORT_SQL_UID] != $uid )
		{
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			Logger::DEBUG('extendTimeByGold：the resource belong to uid:%d not to you:%d',
			$this->resource[PortDef::PORT_SQL_UID], $uid);
			return -1;
		}

		//传过来的档次id对不对
		if (!isset(btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]))
		{
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			Logger::FATAL("extendTimeByGold err id uid:%s id:%s " ,$uid,$grade_id);
			return -2;
		}

		$userObj = EnUser::getUserObj($uid);
		//如果开启船精灵则不扣金币
		$gold=0;
		if (!EnElves::hasResourceElf($uid))
		{
			//金币够不够
			$gold = btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]['gold'];
			$curgold=$userObj->getGold();
			if ($curgold < $gold )
			{
				//释放锁
				$locker->unlock($this->getPortResourceLockerName());
				return -3;
			}
		}

		//行动力够不够
		$execution = btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]['execution'];
		$curexecution=$userObj->getCurExecution();
		if ($curexecution < $execution)
		{
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			return -4;
		}

		//是否达到对应的vip等级
		$vip = $userObj->getVip();
		$need_vip =btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]['viplevel'];
		if ($need_vip > 0 && $vip < $need_vip)
		{
			Logger::FATAL('extendTimeByGold invalid curvip:%d needvip:%d', $vip,$need_vip);
			return 0;
		}

		//在不在可延长的时间段内（策划说暂时先读淘金模式里配置的时间，这可是个坑哦，别以后忘记了）
		$time = Util::getTime();
		if ( $time < $this->resource[PortDef::PORT_SQL_OCCUPY_TIME] ||
			 $time > $this->resource[PortDef::PORT_SQL_OCCUPY_TIME] + ExcavateUtil::getExcavateTime() )
		{
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			Logger::DEBUG('extendTimeByGold：not in excavate time!');
			return -5;
		}

		//是不是已经延长过了
		if ( $this->resource[PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE] > 0 )
		{
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			Logger::DEBUG('the resource has extended time!');
			return -6;
		}

		//扣除金币
		if ($gold > 0 && (!EnElves::hasResourceElf($uid)))
		{
			$userObj->subGold($gold);
			Statistics::gold(StatisticsDef::ST_FUNCKEY_RESOURCE_EXTEND_TIME_BY_GOLD, $gold,  Util::getTime());
		}
		//扣除行动力
		if ($execution > 0 && !$userObj->subExecution($execution))
		{
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			Logger::FATAL("extendTimeByGold subExecution fail uid:%s curexecution:%s execution:%s" ,$uid,$curexecution,$execution);
			return 0;
		}
		$userObj->update();

		//计算新的timer所需时间
		$curTime= Util::getTime();
		$extendtime = btstore_get()->RESOURCE_GOLD_EXTEND_TIME[$grade_id]['time'];
		$newtime = intval($this->resource[PortDef::PORT_SQL_OCCUPY_TIME]
				+ $this->resourceInfo[PortDef::PORT_RESOURCE_TIME]
				+ $extendtime//加上金币延长时间
				- $this->resource[PortDef::PORT_SQL_PLUNDER_TIME] *
				ExcavateUtil::getPlunderSubOccpuyTime());
		if ($newtime <= $curTime)
		{
			//释放锁
			$locker->unlock($this->getPortResourceLockerName());
			Logger::FATAL("extendTimeByGold:time err time:%s resource_time:%s occupy_time:%s curtime:%s plunder_time:%s SubOccpuyTime:%s",
			$newtime,$this->resourceInfo[PortDef::PORT_RESOURCE_TIME],$this->resource[PortDef::PORT_SQL_OCCUPY_TIME],
			$curTime,$this->resource[PortDef::PORT_SQL_PLUNDER_TIME],ExcavateUtil::getPlunderSubOccpuyTime());
			return 0;
		}
		//先把原先的timer取消掉
		TimerTask::cancelTask($this->resource[PortDef::PORT_SQL_DUE_TIMER]);
		//再增加新的timer
		$timer_id = TimerTask::addTask(0, $newtime, 'port.dueResource',
				array($uid, $this->port_id, $this->page_id, $this->resource_id,$this->resource[PortDef::PORT_SQL_OCCUPY_TIME]));

		// 设置当前的数据
		PortResourceDAO::setPortResource($this->port_id, $this->page_id, $this->resource_id,
		NULL, NULL, $timer_id, NULL, NULL, NULL,$grade_id);//把档次id插入数据库

		//释放锁
		$locker->unlock($this->getPortResourceLockerName());

		//广播资源数据更新
		$this->sendMsg();
		return 1;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */