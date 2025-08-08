<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserAttack.class.php 40236 2013-03-07 08:01:32Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/UserAttack.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2013-03-07 16:01:32 +0800 (四, 2013-03-07) $
 * @version $Revision: 40236 $
 * @brief
 *
 **/








class UserAttack
{
	var $prestige = 0;

	public function attack($des_uid)
	{
		$uid = RPCContext::getInstance()->getSession('global.uid');

		$return = array ( 'error_code' => 10100 );

		if ( EnSwitch::isOpen(SwitchDef::PORT_ATTACK) == FALSE )
		{
			Logger::DEBUG('user attack is not open!');
			return $return;
		}

		if (EnSwitch::isOpen(SwitchDef::PORT_ATTACK, $des_uid)== FALSE)
		{
			Logger::debug('user %d is not open for attack', $des_uid);
			$return['error_code'] = 10004;
			return $return;
		}

		$des_user = EnUser::getUserObj($des_uid);

		$user = EnUser::getUserObj();


		//如果在保护时间内,则不能攻打
		if ( $des_user->getProtectCDTime() > Util::getTime() )
		{
			$return['error_code'] = 10001;
			$return['protect_cdtime'] = $des_user->getProtectCDTime();
			return $return;
		}

		// 检查是否有行动力
		if ($user->subAttackExecution(UserAttackConfig::ATTAK_EXECUTION) == FALSE)
		{
			$attack_execution = $user->getAttackExecution();
			if ( !empty($attack_execution) )
			{
				$user->subAttackExecution($attack_execution);
			}
			if ($user->subExecution(UserAttackConfig::ATTAK_EXECUTION - $attack_execution) == FALSE)
			{
				Logger::DEBUG('Execution not enough,need %d.', PortConfig::PORT_CONSUME_EXECUTION);
				return $return;
			}
		}

		//检查战斗CD是否在冷却
		if ($user->addFightCDTime(UserAttackConfig::ATTACK_FIGHT_CDTIME) == FALSE)
		{
			Logger::DEBUG('in fight cd!');
			$return['error_code'] = 10002;
			return $return;
		}

		// 检查阵营是否相同，相同阵营不能攻击
		if ( $user->getGroupId() == $des_user->getGroupId() )
		{
			Logger::WARNING('same group id!don’t attack!');
			return $return;
		}

		//检测是否在港口,或者是否是否在相同的城镇
		//策划2012-3-5日修改
		$user_portberth = new PortBerth($uid);
		$user_port_id = $user_portberth->getPort();
		$des_user_portberth = new PortBerth($des_uid);
		$des_user_port_id = $des_user_portberth->getPort();
		if ( empty($user_port_id) || empty($des_user_port_id) )
		{
			Logger::WARNING('user:%d or des_user:%d not in a port!', $uid, $des_uid);
			return $return;
		}
		if ( Port::getTownByPort($user_port_id) != Port::getTownByPort($des_user_port_id) )
		{
			Logger::WARNING('user and des user not in same town!');
			return $return;
		}
		
		$battleInfo = $user->getBattleInfo();
		if ($battleInfo['ret']!='ok')
		{
			Logger::DEBUG('no enough hp or no hero!');
			$return['error_code'] = 10003;
			return $return;
		} 
		$atkedBattleInfo = $des_user->getBattleInfo();

		$battleUser = array (
						'uid' => $uid,
						'name' => $user->getUname(),
			            'level' => $user->getLevel(),
						'isPlayer' => true,
			            'flag' => 0,
			            'formation' => $user->getCurFormation(),
			            'arrHero' => $battleInfo['info']
					);

		$battleDesUser = array (
						'uid' => $des_uid,
						'name' => $des_user->getUname(),
                        'level' => $des_user->getLevel(),
						'isPlayer' => true,
                        'flag' => 0,
                        'formation' => $des_user->getCurFormation(),
                        'arrHero' => $atkedBattleInfo['info']
					);

		$bt = new Battle();
		$atkRet = $bt->doHero($battleUser, $battleDesUser, 0,
				array($this, 'attackCallback'),
				NULL,
				array (
					'bgid' 		=>		UserAttackConfig::USER_ATTACK_BATTLE_BG_ID,
					'musicId'	=>		UserAttackConfig::USER_ATTACK_MUSIC_ID,
					'type' 		=> 		BattleType::HARBOR,
					)
				);

		$des_user = EnUser::getUserObj($des_uid);

		// 减血User
		$heroArr = EnFormation::subUserHeroHp($atkRet['server']['team1'], 0, $battleInfo['info']);

		// 减血des User
		EnFormation::subUserHeroHp($atkRet['server']['team2'], $des_uid, $atkedBattleInfo['info']);

		//是否胜利
		$isSuccess = BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'];

		//发送成就相关处理
		EnAchievements::notify($uid, AchievementsDef::ATK_OTHERS_TIMES, 1);
		// 被攻击不要求判断是不是紫名了
		EnAchievements::notify($des_uid, AchievementsDef::RS_ATKED_TIMES, 1);
		if ( $des_user->getAtkValue() >= UserAttackConfig::ATTACK_VALUE_COLOR_PURPLEP_MIN )
		{
			EnAchievements::notify($uid, AchievementsDef::ATK_RS_TIMES, 1);
		}

		// 胜利需要处理
		if ( $isSuccess == TRUE )
		{
			//将自己的保护时间置为0
			$user->resetProctectCDTime();

			//增加自己的攻击值
			$user->addAtkValue(1);

			//减少对方的攻击值，并且增加保护时间
			//每次被攻击保护时间= （1-被攻击者攻击值保护时间系数）*保护时间系数
			$modulus = 0;
			$atkValue = $des_user->getAtkValue();
			foreach ( UserAttackConfig::$PROTECT_TIME_MODULUS as $key => $value )
			{
				if ( $key > $atkValue )
				{
					break;
				}
				$modulus = $value;
			}

			$protect_time = intval( ( 1 - $modulus / UserAttackConfig::MODULUS ) *
				UserAttackConfig::PROTECT_TIME_BASIC );

			$des_user->addProtectCDTime($protect_time);
			$des_user->subAtkValue(1);

		}

		//sendmail
		$mail_des_user_info = array (
			'uid' 		=> $des_uid,
			'uname' 	=> $des_user->getUname(),
			'utid'		=> $des_user->getUtid(),
		);
		MailTemplate::sendAttackUser($uid, $mail_des_user_info, $this->prestige, $atkRet['server']['brid'], $isSuccess);
		$mail_user_info = array (
			'uid' 		=> $uid,
			'uname' 	=> $user->getUname(),
			'utid'		=> $user->getUtid(),
		);
		MailTemplate::sendDefendUser($des_uid, $mail_user_info, $this->prestige, $atkRet['server']['brid'], !$isSuccess);

		//update user
		$user->update();
		$des_user->update();

		$userInfo = $user->getUserInfo();

		//调用每日任务
		EnDaytask::portAttack();

		//调用任务系统
		TaskNotify::operate(TaskOperateType::PORT_ATTACK);

		EnActive::addPortAtkTimes();

		return array(
			'error_code' => 10000,
			'fight_ret' => $atkRet['client'],
			'blood_package' => $userInfo['blood_package'],
			'prestige' => $userInfo['prestige_num'],
			'atk_value' => $userInfo['atk_value'],
			'protect_cdtime' => $userInfo['protect_cdtime'],
			'fight_cdtime' => $userInfo['fight_cdtime'],
		    'cur_hp' => $heroArr,
			'appraisal' => $atkRet['server']['appraisal'],
		);
	}

	public function attackCallback($server)
	{
		//是否胜利
		$isSuccess = BattleDef::$APPRAISAL[$server['appraisal']] <= BattleDef::$APPRAISAL['D'];

		$des_user = EnUser::getUserObj($server['uid2']);
		$user = EnUser::getUserObj($server['uid1']);

		Logger::DEBUG('attack user:%d, des user:%d', $server['uid1'], $server['uid2']);
		//增加自己的声望
		//声望获得基础值=int(min(防守方级别-10, (10+int(防守方攻击值/5)+防守方级别-攻击方级别))*(1-输赢系数) )

		$prestige = min($des_user->getLevel()-UserAttackConfig::PRESTIGE_BASIC, ( UserAttackConfig::PRESTIGE_BASIC
			+ intval($des_user->getAtkValue() / UserAttackConfig::PRESTIGE_ATTACK_DIVIDE  )
			+ $des_user->getLevel() - $user->getLevel() ) )
			* ( 1 - UserAttackConfig::$PERSTIGE_SUCCESS_MODULUS[$isSuccess] / UserAttackConfig::MODULUS);
		$prestige = intval($prestige);

		if ( $prestige <= 0 )
		{
			$prestige = 0;
		}

		Logger::DEBUG('user:%d get presitge:%d', $user->getUid(), $prestige);

		$user->addPrestige($prestige);

		$this->prestige = $prestige;

		//不减少对方的声望

		return array (
			'prestige' => $prestige,
		);

	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */