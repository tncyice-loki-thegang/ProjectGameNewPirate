<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FromArena.php 26423 2012-08-30 09:44:47Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FromArena.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-30 17:44:47 +0800 (四, 2012-08-30) $
 * @version $Revision: 26423 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class FromArena extends BaseScript
{
	protected function getReplay($uid)
	{
		
		$data = new CData();
		$arrField = array('id', 'attack_uid', 'defend_uid', 'attack_time', 'attack_replay');
		$retAtk = $data->noCache()->select($arrField)->from('t_arena_msg')->where('attack_uid', '=', $uid)
			->orderBy('attack_time', false)->limit(0, 1)->query();
		$retDef = $data->noCache()->select($arrField)->from('t_arena_msg')->where('defend_uid', '=', $uid)
			->orderBy('attack_time', false)->limit(0, 1)->query();
		$ret = $retAtk;
		if (empty($ret))
		{
			if (empty($retDef))
			{
				return 0;
			}
			else
			{
				return $retDef[0]['attack_replay'];
			}
		}
		else
		{
			if (empty($retDef))
			{
				return $ret[0]['attack_replay'];
			}
			
			if ($retDef[0]['attack_time'] > $ret[0]['attack_time'])
			{
				$ret = $retDef;
			}
			
			return $ret[0]['attack_replay'];
		}
		
	}
	
	public static function insertFromTemplate ($uid, $htid, $arrField, $vip)
	{
		if ($arrField == null)
		{
			$arrField = array();
		}
		$arrField['htid'] = $htid;
		$arrField['uid'] = $uid;
		
		if (!isset($arrField['curHp']))
		{
			$arrField['curHp'] = 0;
		}

		if (!isset($arrField['level']))
		{
			$arrField['level'] = 1;
		}

		if (!isset($arrField['rebirthNum']))
		{
			$arrField['rebirthNum'] = 0;
		}

		if (!isset($arrField['exp']))
		{
			$arrField['exp'] = 0;
		}

		if (!isset($arrField['stateId']))
		{
			$arrField['stateId'] = 0;
		}
		
		if (!isset($arrField['upgrade_time']))
		{
			$arrField['upgrade_time'] = Util::getTime();
		}

		if (!isset($arrField['va_hero']))
		{
			$arrField['va_hero'] = array();
			$arrField['va_hero']['daimonApple'] = array();
		}
		else
		{
			if (!isset($arrField['va_hero']['daimonApple']))
			{
				$arrField['va_hero']['daimonApple'] = array();
			}
		}

		//主角英雄特有的字段
		if (HeroUtil::isMasterHero($htid))
		{
			$arrField['va_hero']['master'] = array('transfer_num'=> self::getTransferNum($arrField['htid'], $arrField['level']),
												   'learned_normal_skills'=>array(),
												   'using_skill_time'=>0,
												   'using_skill_num'=>0,);
			$transNum = $arrField['va_hero']['master']['transfer_num'];
			$arrDefaultRageSkill = btstore_get()->MASTER_HEROES_TRANSFER[$htid][$transNum]['transfer_rageSkills']->toArray();
			$rageSkill = intval(key($arrDefaultRageSkill));
			$arrField['va_hero']['master']['learned_rage_skills'] = array($rageSkill);
			$arrField['va_hero']['master']['using_skill'] = $rageSkill;
			
			//主角的好感度等级 = int( (主角等级/2.5-16)*(0.6+VIP等级*0.04)
			$gwLevel = intval (($arrField['level'] /2.5 -16) * (0.6 + $vip * 0.04));
			if ($gwLevel < 0)
			{
				$gwLevel = 0;
			}
			$arrField['va_hero']['goodwill'] = array('exp'=>0, 'level'=> $gwLevel, 'upgrade_time'=>0);
			
		}
		else
		{
			//rebirth
			$arrField['rebirthNum'] = self::getRebirthNum($arrField['level']);
			
			//如果转生次数为0,则好感度等级=int( (min(英雄等级,50)/2.5-16)*(0.6+VIP等级*0.04)
			//如果转生次数不为0,则好感度等级=int( (((转生次数-1)*5+50)/2.5-16)*(0.6+VIP等级*0.04) )
			$gwLevel = 0;
			if ($arrField['rebirthNum'] == 0)
			{
				$gwLevel = intval((min($arrField['level'], 50) / 2.5 - 16) * (0.6 + $vip * 0.04));
			}
			else
			{
				$gwLevel = intval(((($arrField['rebirthNum'] - 1) * 5 + 50) / 2.5 - 16) * (0.6 + $vip * 0.04));
			}
			if ($gwLevel < 0)
			{
				$gwLevel = 0;
			}
			$arrField['va_hero']['goodwill'] = array('exp' => 0, 'level' => $gwLevel, 'upgrade_time' => 0);
		}
		
		$itemMgr = null;
		//0转开启位置		
		foreach (btstore_get()->CREATURES[$htid][CreatureInfoKey::devilFruitSkill] as $pos=>$rebirthDmTid)
		{
			list($rebirthNum, $DmTid) = $rebirthDmTid;
			if ($rebirthNum==0)
			{
				$itemId = 0;
				//有默认的恶魔果实
				if ($DmTid!=0)
				{
					$itemMgr = ItemManager::getInstance();
					$itemId = $itemMgr->addItem($DmTid);
					$itemId = $itemId[0];
				}
				$arrField['va_hero']['daimonApple'][$pos] = $itemId;
			}
		}
		if ($itemMgr!=null)
		{
			$itemMgr->update();
		}
		
		$arrField['va_hero']['arming'] = ArmingDef::$ARMING_NO_ARMING;
		
				
		
		
		
		return self::save($arrField);
	}
	
	public static function save($arrField)
	{
		$data = new CData();	
		if (!isset($arrField['hid']))
		{
			$data->uniqueKey('hid');
		}	
		
		Logger::warning('insert:%s', $arrField);
		$arrRet = $data->insertInto('t_hero')->values($arrField)->query();
	}
	
	public static function getRebirthNum($level)
	{
		$cfg = btstore_get()->HERO_REBIRTH;
		$num = 0;
		foreach ($cfg as $tmp)
		{
			if ($tmp['need_level'] > $level)
			{
				break;
			}
			$num = $tmp['rebirth_num'];
		}
		return $num;
	}
	
	public static function getTransferNum($htid, $level)
	{
		$cfg = btstore_get()->MASTER_HEROES_TRANSFER[$htid];
		$num = 0;
		foreach ($cfg as $tmp)
		{
			if ($tmp['need_lv'] > $level)
			{
				break;
			}
			$num = $tmp['transfer_num'];
		}
		return $num;
	}
	
	
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		//user
		$arrField = array('uid', 'vip', 'utid', 'uname', 'master_hid', 'va_user');
		$offsetUid = 10000;
		$limit = 100;
		
		$handle = fopen('/tmp/uid_bridurl', 'a');
		
		while(true)
		{
			$arrUser = UserDao::getArrUserByOffsetUid($offsetUid, $limit, $arrField);
			if (empty($arrUser))
			{
				break;
			}
			
			foreach ($arrUser as $user)
			{
				//check master_hid
				if ($user['master_hid'] ==0)
				{
					Logger::fatal('master_hid = 0 uid:%d', $user['uid']);
					continue;
				} 
				
				$hero = HeroDao::getByHid($user['master_hid'], array('hid'));
				//主角英雄在
				if (!empty($hero))
				{
					Logger::trace('get hero by master hid suc uid:%d', $user['uid']);
					continue;
				}
				
				$arrHid = $user['va_user']['recruit_hero_order'];
				$level = 20;
				
				//战报恢复
				$arrBridHtid = array();
				$brid = self::getReplay($user['uid']);
				if ($brid!=0)
				{
					$bt = BattleDao::getRecord($brid);
					$bt = Util::amfDecode($bt, true);
					
					Logger::debug('bt:%s', $bt);
					
					$arrHero = $bt['team1']['arrHero'];
					if ($bt['team2']['uid'] == $user['uid'])
					{
						$arrHero = $bt['team2']['arrHero'];
					}
					
					foreach ($arrHero as $hero)
					{
						$heroField = array();
						$heroField['hid'] = $hero['hid'];
						$pos = array_search($hero['hid'], $arrHid);
						if ($pos!==false)
						{
							unset($arrHid[$pos]);
						}
						
						$heroField['htid'] = $hero['htid'];
						$heroField['level'] = $hero['level'];
						$heroField['status'] = HeroDef::STATUS_RECRUIT;
						
						if (HeroUtil::isMasterHero($hero['htid']))
						{
							$level = $hero['level'];
						}
						
						
						//gw						
						self::insertFromTemplate($user['uid'], $heroField['htid'], $heroField, $user['vip']);

						$arrBridHtid[] = $heroField['htid'];
					}
					
					
					fwrite($handle, $user['uid'] . " " . $user['uname'] . " " . $bt['url_brid'] . "\n");
					
				}
				
				$mhid = $user['master_hid'];
				$pos = array_search($mhid, $arrHid);
				if ($pos!==false)
				{
					//先处理主角hero
					$heroField = array();
					$heroField['level'] = $level;
					$htid = UserConf::$USER_INFO[$user['utid']][1];
					$heroField['hid'] = $mhid;		
					$heroField['status'] = HeroDef::STATUS_RECRUIT;	
					self::insertFromTemplate($user['uid'], $htid, $heroField, $user['vip']);
					unset($arrHid[$pos]);					
				}
		
				foreach ($user['va_user']['heroes'] as $htid)
				{
					if (in_array($htid, $arrBridHtid))
					{
						continue;
					}
					
					if (HeroUtil::isMasterHero($htid))
					{
						continue;
					}
					
					$heroField = array();
					$heroField['level'] = $level;
					$heroField['status'] = HeroDef::STATUS_PUB;
					
					if (!empty($arrHid))
					{
						$heroField['hid'] = current($arrHid);
						array_shift($arrHid);
						$heroField['status'] = HeroDef::STATUS_RECRUIT;
					}
					
					self::insertFromTemplate($user['uid'], $htid, $heroField, $user['vip']);					
				}			
				
				
			}
			
			$endUser = end($arrUser);
			$offsetUid = $endUser['uid'] + 1;
		}
		
		
		fclose($handle);
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */