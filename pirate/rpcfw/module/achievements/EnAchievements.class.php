<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnAchievements.class.php 36883 2013-01-24 03:42:55Z lijinfeng $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/EnAchievements.class.php $
 * @author $Author: lijinfeng $(liuyang@babeltime.com)
 * @date $Date: 2013-01-24 11:42:55 +0800 (四, 2013-01-24) $
 * @version $Revision: 36883 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnAchievements
 * Description : 成就内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnAchievements
{
	/**
	 * 获取其他用户正在展示的成就ID
	 * 
	 * @param int $uid							用户ID
	 */
	static public function getOtherShowAchieveList($uid)
	{
		return AchievementsDao::getShowAchieveIDs($uid);
	}

	/**
	 * 返回用户成就排名
	 */
	static public function getUserAchieveRank()
	{
		// 获取当前用户的成就点数
		$achievePoint = MyAchievements::getInstance()->getAchievePoint();
		// 获取本人名次
		return AchievementsDao::getUserAchieveRank(RPCContext::getInstance()->getUid(), $achievePoint);
	}
	
	static public function getCurrentTitleAttr($uid)
	{
		$achieve = new Achievements;
		
		return $achieve->getCurrentTitleAttrs($uid);
	}

	/**
	 * 获取成就列表 
	 */
	static public function getAchieveList($min, $max)
	{
		// 获取服务器成就排行
		$list = AchievementsDao::getServerAchieveList($min, $max);
		// 对空加判断
		if (!empty($list))
		{
			// 获取所有公会ID
			$guildIDs = Util::arrayExtract($list, 'guild_id');
			// 通过公会ID获取公会名称
	    	$guildNames = GuildLogic::getMultiGuild($guildIDs, array('name'));
	    	// 获取主角hid
	    	$hids = Util::arrayExtract($list, 'master_hid');
	    	// 根据hid获取用户等级
	    	$HidsLv = HeroLogic::getArrHero($hids, array('level'));
	    	// 将公会名称和等级插入数组
	    	foreach ($list as $key => $user)
	    	{
	    		// 合并公会名称
	    		$list[$key]['guild_name'] = isset($guildNames[$user['guild_id']]['name']) ? 
	    		                                  $guildNames[$user['guild_id']]['name'] : '';
	    		// 合并用户等级
	    		$list[$key]['level'] = $HidsLv[$user['master_hid']]['level'];
	    		// 删掉没有用的主角hid
	    		unset($list[$key]['master_hid']);
	    	}
		}
    	// 返回给前端
    	return $list;
	}

	/**
	 * 返回改用户当前装备的称号
	 * 
	 * @param int $uid							用户ID
	 */
	static public function getUserTitle($uid)
	{
		// 如果是当前用户，那么一切就变的简单了
		if ($uid == RPCContext::getInstance()->getUid())
		{
			// 获取用户展示的称号
			$titles = AchievementsLogic::getShowName();
		}
		// 其他人的称号，就要麻烦数据库了
		else 
		{
			// 从数据库中获取展示的名称
			$titles = AchievementsDao::getShowTitles($uid);
			// 剔除掉不新鲜的部位
			$titles = AchievementsLogic::checkOverdueByOther($uid,$titles);
		}
		// 删掉不用的数据
		foreach ($titles as $key => $title)
		{
			unset($titles[$key]['is_show']);
			unset($titles[$key]['status']);
		}
		// 返回
		return isset($titles[0]['title_id']) ? $titles[0]['title_id'] : 0;
	}

	/**
	 * 获取用户成就点数
	 */
	static public function getUserAchievePoint($uid)
	{
		// 如果是当前用户
		if ($uid == RPCContext::getInstance()->getUid())
		{
			// 获取当前用户的成就点数
			$achievePoint = MyAchievements::getInstance()->getAchievePoint();
		}
		else 
		{
			// 获取指定用户的所有成就
			$achieveList = AchievementsDao::getAchieveInfo($uid);
			// 计算这些成就的成就点数
			$achievePoint = MyAchievements::calculateAchievePoint($achieveList);
		}
		// 返回成就点数
		return $achievePoint;
	}

	/**
	 * 获取用户悬赏值和悬赏等级
	 */
	static public function getUserBounty($uid)
	{
		// 如果是当前用户
		if ($uid == RPCContext::getInstance()->getUid())
		{
			// 获取当前用户的成就点数
			$achievePoint = MyAchievements::getInstance()->getAchievePoint();
		}
		else 
		{
			// 获取指定用户的所有成就
			$achieveList = AchievementsDao::getAchieveInfo($uid);
			// 计算这些成就的成就点数
			$achievePoint = MyAchievements::calculateAchievePoint($achieveList);
		}
		// 获取本人的悬赏值
		$bountyPoint = $achievePoint * AchievementsDef::BOUNTY_PER;
		// 悬赏等级初始化
		$lv = 0;
		// 查询悬赏等级
		foreach (btstore_get()->SALARY as $salary)
		{
			// 记录悬赏等级
			$lv = $salary['lv'];
			// 找到档位了，退出
			if ($salary['next_exp'] > $bountyPoint)
			{
				break;
			}
		}
		Logger::debug('User bounty point is %d, lv is %d.', $bountyPoint, $lv);
		return array('point' => $bountyPoint, 'lv' => $lv);
	}

	/**
	 * 增加一个新称号
	 * 
	 * @param int $titleID						称号ID
	 */
	static public function addNewTitle($titleID)
	{
		// 发送炫耀信息
		if (!empty(btstore_get()->TITLE[$titleID]['msg']))
		{
			ChatTemplate::sendTitleGet(EnUser::getUserObj()->getTemplateUserInfo(), $titleID);
		}
		// 增加一个新称号
		return AchievementsDao::addNewTitle(RPCContext::getInstance()->getUid(), $titleID);
	}

	/**
	 * 公会成就通知方法
	 * 
	 * @param int $guildID						公会ID
	 * @param int $type							成就小分类
	 * @param int $value_1						这次改变的数值1
	 */
	static public function guildNotify($guildID, $type, $value_1)
	{
		// 通知当前用户的成就系统
		self::__notify($type, $value_1, $guildID);
	}

	/**
	 * 成就通知方法
	 * 
	 * @param int $uid							用户ID
	 * @param int $type							成就小分类
	 * @param int $value_1						这次改变的数值1
	 * @param int $value_2						这次改变的数值2
	 */
	static public function notify($uid, $type, $value_1, $value_2 = 1)
	{
		// 如果是当前用户，那么进行简单的处理方式
		if (RPCContext::getInstance()->getUid() == $uid)
		{
			// 通知当前用户的成就系统
			return self::__notify($type, $value_1, $value_2);
		}
		// 非当前用户
		else 
		{
			// 需要经过 lcserver 转发, 如果转发过去之后，没有 global.uid， 那么可以自行设置
			RPCContext::getInstance()->executeTask($uid, 
			                                       'achievements.excuteNotify',
			                                       array($uid, $type, $value_1, $value_2));
		}
	}

	/**
	 * 通知成就系统, 查看是否获取到成就
	 * 
	 * @param int $type							成就小分类
	 * @param int $value_1						这次改变的数值1
	 * @param int $value_2						这次改变的数值2
	 */
	static public function __notify($type, $value_1, $value_2 = 1)
	{
		Logger::debug('EnAchievements notify called, type is %d, v1 is %d, v2 is %d.', 
		              $type, $value_1, $value_2);
		// 成就列表
		$achieveArr = array();
		// 检查成就小分类
		switch ($type)
		{
			// 需要记录中间结果类型
			case AchievementsDef::PRACTICE_TOTAL_EXP:			// 通过历练领取到的所有经验总量
			case AchievementsDef::SMELTING_TIMES:				// 制作装备达到一定次数
			case AchievementsDef::AUTO_ATK_TIMES:				// 完成连续攻击的所有次数
			case AchievementsDef::TREASURE_TIMES:				// 寻宝达到一定次数
			case AchievementsDef::DAY_TASK_COUNT_HIGH:			// 完成过的所有高品质每日任务数量
			case AchievementsDef::DAY_TASK_COUNT_ALL:			// 完成过的每日任务数量
			case AchievementsDef::RS_ATKED_TIMES: 				// 记录被紫名攻击次数
			case AchievementsDef::ATK_OTHERS_TIMES: 			// 记录攻击其他阵营玩家次数
			case AchievementsDef::ATK_RS_TIMES: 				// 记录攻击紫名次数
		    case AchievementsDef::ATTACK_WORLD_BOSS_TIMES:		// 攻击世界BOSS次数
			case AchievementsDef::INSPIRE_TIMES:				// 鼓舞次数
			case AchievementsDef::ACT_GROUP_ATK_TIMES:			// 一共进行过多少次活动副本战，只获胜
			case AchievementsDef::TEAM_BATTLE_TIMES:			// 进行过多少次战役部队的战斗，只获胜
			case AchievementsDef::ROB_TIMES:					// 开启寻宝以后所有打劫的次数
			case AchievementsDef::LOSE_ENEMY_TIMES:				// 副本失败次数
			case AchievementsDef::REFORCE_OK_TIMES:				// 强化成功次数
		    case AchievementsDef::LEARN_GOOD_WILL_SKILL_NUM:	// 好感度技能个数
		    case AchievementsDef::OLYMPIC_SIGN_TIMES:			// 人物在擂台赛中报名或者挑战次数超过某指定值
		    case AchievementsDef::OLYMPIC_CHEER_TIMES:			// 人物在擂台赛中助威次数超过某指定值
		    case AchievementsDef::PIRATE_BATTLE_ENEMIES_KILL:	// 海贼战场中杀敌数目
		    case AchievementsDef::PIRATE_BATTLE_RESOURCE_CNT:	// 占领资源数
		    case AchievementsDef::PIRATE_BATTLE_ROB_JIFEN_CNT:	// 掠夺积分数
		    case AchievementsDef::PIRATE_BATTLE_JOIN_CNT:		// 海贼战场参与数据
		    case AchievementsDef::PIRATE_BATTLE_LOSE_CNT:		// 海贼战场失败次数
		    	// 检查并记录成就
				$achieveArr = self::checkRecordAchieve($type, $value_1);
				break;
			// 需要记录中间结果，且有俩参数
		    case AchievementsDef::DEFEAT_ARMY_LOSE:				// 攻击某部队的失败次数
		    case AchievementsDef::DEFEATE_ENEMY_S_TIMES:		// 记录已经攻打的次数
		    case AchievementsDef::OLYMPIC_NO_TIMES:				// 擂台赛中获得某排名次数超过某指定值
		    	$achieveArr = self::checkRecordTimesAchieve($type, $value_1, $value_2);
				break;
			// 两个参数类型
		    case AchievementsDef::DEFEATE_ENEMY_TIMES:
		    case AchievementsDef::REFORCE_ARM_COLOR_TIMES:
		    case AchievementsDef::HERO_LEVEL:
		    case AchievementsDef::HERO_GOOD_WILL_LV:
		    	// 检查次数看是否可以获取成就
		    	$achieveArr = self::checkTimesAchieve($type, $value_1, $value_2);
				break;
			// 一个参数类型
		    case AchievementsDef::SAIL_BELLY:
		    case AchievementsDef::OFFER_BELLY:
		    case AchievementsDef::MAX_BELLY:
		    case AchievementsDef::MAX_PRESTIGE:
		    case AchievementsDef::MAX_EXPERIENCE:
		    case AchievementsDef::MAX_HEROS:
		    case AchievementsDef::LEVEL:
			case AchievementsDef::HERO_REBIRTH:
			case AchievementsDef::COOK_LEVEL:
			case AchievementsDef::BAG_GRID_NUM:
			case AchievementsDef::ITEM_REFORCE_LEVEL:
			case AchievementsDef::FORMATION_LEVEL:
		    case AchievementsDef::ARTIFICER_NUM:
		    case AchievementsDef::SMELTING_QUALITY:
		    case AchievementsDef::SELL_DISH:
		    case AchievementsDef::PET_SKILL_TYPE_NUM:
		    case AchievementsDef::VIP_LEVEL:
			case AchievementsDef::ARENA_NO_1:
		    case AchievementsDef::TREASURE_QUALITY:
		    case AchievementsDef::DAY_TASK_POINTS:
		    case AchievementsDef::WORLD_BOSS_NO1:
			case AchievementsDef::MAX_FRIENDS:
			case AchievementsDef::ITEM_COLOR:
			case AchievementsDef::ARENA_KEEP_WIN_NUM:
		    case AchievementsDef::KILL_WORLD_BOSS:
		    case AchievementsDef::GOOD_WILL_LEVEL:
		    case AchievementsDef::PIRATE_BATTLE_JIFEN_FIRST:		// 海贼战场积分第一
		    case AchievementsDef::PIRATE_BATTLE_CONTIOUS_WIN:		// 海贼战场连胜数
		    	
		    	// 直接比较，看是否可以获取成就 (大于等于)
		    	$achieveArr = self::checkSimpleAchieve($type, $value_1, false);
				break;
			case AchievementsDef::PASS_COPY: 
		    case AchievementsDef::GET_ALL_COPY_PRIZE: 
		    case AchievementsDef::DEFEATE_ENEMY_SSS:
			case AchievementsDef::ARRIVE_TOWN:
		    case AchievementsDef::DEFEAT_ARMY_NO_1:
		    case AchievementsDef::OWN_PET:
		    case AchievementsDef::OWN_HERO:
		    	// 直接比较，看是否可以获取成就 (严格相等)
		    	$achieveArr = self::checkSimpleAchieve($type, $value_1, true);
				break;
			// 需要查英雄身上的装备都是怎样的并计算
		    case AchievementsDef::HERO_ITEM_COLOR:
		    	$achieveArr = SpAchievements::checkHeroEquipColor($value_1);
				break;
			// 需要查询所有舱室等级并计算
			case AchievementsDef::CABIN_LEVEL:
				$achieveArr = SpAchievements::checkCabinsLv($value_1);
				break;
			// 同时拥有的宠物种类数
			case AchievementsDef::PET_TYPE_NUM:
				$achieveArr = SpAchievements::checkPetTypeNum();
				break;
			// 需要查询前N个伙伴的等级
			case AchievementsDef::HEROS_LEVEL:
				$achieveArr = SpAchievements::checkHeroesLv($value_1);
				break;
			// 需要记录中间结果并计算
			case AchievementsDef::ARENA_POSITION_UP: 			// 记录开始，结束名次，和日期
				$achieveArr = SpAchievements::checkArenaRankUp($value_1, $value_2);
				break;
			case AchievementsDef::TOTAL_ONLINE_TIME: 			// 记录上线时间
				$achieveArr = SpAchievements::checkTotalOnlineTime();
				// 判断，如果返回的是时间，表明还需要这么长时间才能得到成就
				if (!is_array($achieveArr))
				{
					return $achieveArr;
				}
				// 否则正常获得了成就
				break;
			case AchievementsDef::KEEP_ONLINE_TIME:
				$achieveArr = SpAchievements::checkKeepOnlineTime();
				// 判断，如果返回的是时间，表明还需要这么长时间才能得到成就
				if (!is_array($achieveArr))
				{
					return $achieveArr;
				}
				// 否则正常获得了成就
				break;
			// 公会成就
		    case AchievementsDef::GUILD_ST_LEVEL:
		    case AchievementsDef::GUILD_MEMBER_NUM:
		    case AchievementsDef::GET_WORLD_RES:
		    	// 公会类型比较特殊，单独使用其他函数进行
				return self::guildNotiy($type, $value_1, $value_2);
			default:
				Logger::fatal('Error achievement type: %d!', $type);
				throw new Exception('fake');
		}

		Logger::debug('All achieve list is %s.', $achieveArr);

		// 成功获取成就
		foreach ($achieveArr as $achieveID)
		{
			// 如果是奖励使用的特殊成就，那么就不需要进行其他操作
			if (btstore_get()->ACHIEVE[$achieveID]['hide'] == 1)
			{
				continue;
			}
			// 通知前端，获取了成就
			RPCContext::getInstance()->sendMsg(array(RPCContext::getInstance()->getUid()), 
			                                  'achievements.getNewAchievement',
			                                   array($achieveID));
			// 发送炫耀信息
			if (!empty(btstore_get()->ACHIEVE[$achieveID]['msg_id']))
			{
				ChatTemplate::sendAchievementEnd(EnUser::getUserObj()->getTemplateUserInfo(), $achieveID);
			}
			// 查看是否有物品
			$item = array();
			// 如果有物品，那么才传过去，不然就是空数组
			if (!empty(btstore_get()->ACHIEVE[$achieveID]['item_id']))
			{
				// 生成物品
				$item = ItemManager::getInstance()->addItem(btstore_get()->ACHIEVE[$achieveID]['item_id']);
				ItemManager::getInstance()->update();
			}
			// 发送奖励邮件 ,有数值才发邮件
			if (!empty(btstore_get()->ACHIEVE[$achieveID]['title']) ||
			    !empty(btstore_get()->ACHIEVE[$achieveID]['belly']) ||
			    !empty(btstore_get()->ACHIEVE[$achieveID]['gold']) ||
			    !empty(btstore_get()->ACHIEVE[$achieveID]['experience']) ||
			    !empty(btstore_get()->ACHIEVE[$achieveID]['prestige']) ||
			    !empty($item))
		    {
				MailTemplate::sendAchievement(RPCContext::getInstance()->getUid(), 
			    	                          $achieveID,
			        	                      btstore_get()->ACHIEVE[$achieveID]['title'],
			            	                  btstore_get()->ACHIEVE[$achieveID]['belly'],
			                	              btstore_get()->ACHIEVE[$achieveID]['gold'],
			                    	          btstore_get()->ACHIEVE[$achieveID]['experience'],
			                        	      btstore_get()->ACHIEVE[$achieveID]['prestige'],
			                            	  $item,
			                            	  TRUE);
		    }
		}

		// 返回一个没用的返回值，前端可能会用到
		return $achieveArr;
	}

	/**
	 * 处理公会成就
	 * 
	 * @param int $type							成就小分类
	 * @param int $value						这次改变的数值
	 * @param int $guildID						公会ID
	 */
	static private function guildNotiy($type, $value, $guildID)
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[$type]))
		{
			return ;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[$type];
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 检查是否已经获得了这个成就
			if (!AchievementsDao::checkGuildAchieveAlreadyGet($guildID, $achieveID))
			{
				// 如果尚未获取这个成就的话, 检查是否可以获取这个成就
				$con = btstore_get()->ACHIEVE[$achieveID]['condition'][0];
				// 如果达成了这个成就的话，记录下成就ID
				if ($value >= $con)
				{
					// 记录成就ID
					$achieveArr[] = $achieveID;
				}
			}
		}
		// 成功获取成就
		foreach ($achieveArr as $achieveID)
		{
			// 将新成就加入到数据库
			$arrRet = AchievementsDao::addGuildAchieveInfo($guildID, $achieveID);
			// 如果成功更新了
			if ($arrRet['affected_rows'] != 0)
			{
				// 通知所有在线的黑龙会的人，你们的老大挂了……啊，不，你们获取了一个什么成就……
				RPCContext::getInstance()->sendFilterMessage('guild', 
				                                             $guildID, 
				                                            'achievements.getNewAchievement', 
				                                             array($achieveID));
			}
		}
		return ;
	}

	/**
	 * 检查次数类成就是否达成
	 * 
	 * @param int $type							小类型
	 * @param int $value						某项数值
	 * @param int $times						已经达成的次数
	 */
	static private function checkTimesAchieve($type, $value, $times)
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[$type]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[$type];
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 检查是否已经获得了这个成就
			if (!MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				// 如果尚未获取这个成就的话, 检查是否可以获取这个成就
				$con_1 = intval(btstore_get()->ACHIEVE[$achieveID]['condition'][0]);
				$con_2 = intval(btstore_get()->ACHIEVE[$achieveID]['condition'][1]);
				// 如果达成了这个成就的话，记录下成就ID (举例：部队ID为 XX 的时候，攻击次数大于要求次数，那么获得成就)
				if ($value == $con_1 && $times >= $con_2)
				{
					// 将新成就加入到数据库
					$achievePoint = MyAchievements::getInstance()->addAchieve($achieveID);
					// 加到差不多了就试试得没得到新成就
					EnAchievements::__notify(AchievementsDef::OFFER_BELLY, $achievePoint);
					// 记录成就ID
					$achieveArr[] = $achieveID;
				}
			}
		}
		// 返回所获取的成就
		return $achieveArr;
	}

	/**
	 * 检查需要记录的次数类成就是否达成 (此类成就都是唯一的)
	 * 
	 * @param int $type							小类型
	 * @param int $value						某项数值
	 * @param int $times						已经达成的次数
	 */
	static private function checkRecordTimesAchieve($type, $value, $times)
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[$type]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[$type];
		// 该部队ID对应的成就ID
		$achieveID = 0;
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieve)
		{
			// 查看成就条件
			$con_1 = intval(btstore_get()->ACHIEVE[$achieve]['condition'][0]);
			// 恰好等于部队ID时
			if ($value == $con_1)
			{
				// 记录成就ID并退出
				$achieveID = $achieve;
			}
			else 
			{
				continue;
			}
			// 如果策划没有配置这个部队相关的成就，那么直接返回
			if ($achieveID == 0 || MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				continue;
			}
			// 检查是否已经获得了这个成就
			if (!MyAchievements::getInstance()->checkAlreadyHave($achieveID))
			{
				// 如果尚未保存这个成就的话，那么先保存下这个成就
				MyAchievements::getInstance()->addRecordAchieve($achieveID);
			}
			// 加算存储的值
			$recordValue = MyAchievements::getInstance()->addRecordValue($achieveID, $times);
			// 查看成就条件
			$con_2 = intval(btstore_get()->ACHIEVE[$achieveID]['condition'][1]);
			// 如果达成了这个成就的话，记录下成就ID (举例：部队ID为 XX 的时候，攻击次数大于要求次数，那么获得成就)
			if ($recordValue >= $con_2)
			{
				// 记录成就ID
				$achieveArr[] = $achieveID;
				// 修改成就状态为：已经到手
				MyAchievements::getInstance()->changeAchieveStat($achieveID);
			}
			// 将修改的值更新到数据库
			MyAchievements::getInstance()->save($achieveID);
		}
		// 返回所获取的成就
		return $achieveArr;
	}

	/**
	 * 检查需要记录值的成就是否达成 (此类小项目都是唯一的)
	 * 
	 * @param int $type							小类型
	 * @param int $value						某项数值
	 */
	static private function checkRecordAchieve($type, $value)
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[$type]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[$type];
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 都已然到手了，那么就不再计算了
			if (MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				continue;
			}
			// 检查是否已经获得了这个成就
			else if (!MyAchievements::getInstance()->checkAlreadyHave($achieveID))
			{
				// 如果尚未保存这个成就的话，那么先保存下这个成就
				MyAchievements::getInstance()->addRecordAchieve($achieveID);
			}
			// 加算存储的值
			$recordValue = MyAchievements::getInstance()->addRecordValue($achieveID, $value);
			// 加算完毕，查看是否可以获取到这个成就了
			$con = btstore_get()->ACHIEVE[$achieveID]['condition'][0];
			// 如果达成了这个成就的话，记录下成就ID
			if ($recordValue >= $con)
			{
				// 记录成就ID
				$achieveArr[] = $achieveID;
				// 修改成就状态为：已经到手, 哼唧
				MyAchievements::getInstance()->changeAchieveStat($achieveID);
			}
			// 将修改的值更新到数据库
			MyAchievements::getInstance()->save($achieveID);
		}
		// 返回所获取的成就
		return $achieveArr;
	}

	/**
	 * 检查成就是否达成
	 * 
	 * @param int $type							小类型
	 * @param int $value						某项数值
	 * @param bool $needEqual					是否需要相等
	 */
	static private function checkSimpleAchieve($type, $value, $needEqual)
	{
		// 初始化返回值
		$achieveArr = array();
		// 如果没设置，则直接返回
		if (!isset(btstore_get()->ACHIEVE_MINOR[$type]))
		{
			return $achieveArr;
		}
		// 获取小项的成就列表
		$achieveList = btstore_get()->ACHIEVE_MINOR[$type];
		// 遍历所有的成就， 进行检查操作
		foreach ($achieveList as $key => $achieveID)
		{
			// 检查是否已经获得了这个成就
			if (!MyAchievements::getInstance()->checkAlreadyGet($achieveID))
			{
				// 如果尚未获取这个成就的话, 检查是否可以获取这个成就
				$con = btstore_get()->ACHIEVE[$achieveID]['condition'][0];
				// 如果达成了这个成就的话，记录下成就ID
				if (!$needEqual && $value >= $con)
				{
					// 将新成就加入到数据库
					$achievePoint = MyAchievements::getInstance()->addAchieve($achieveID);
					// 加到差不多了就试试得没得到新成就
					EnAchievements::__notify(AchievementsDef::OFFER_BELLY, $achievePoint);
					// 记录成就ID
					$achieveArr[] = $achieveID;
				}
				// 这种情况必须相等
				else if ($needEqual && $value == $con)
				{
					// 将新成就加入到数据库
					$achievePoint = MyAchievements::getInstance()->addAchieve($achieveID);
					// 加到差不多了就试试得没得到新成就
					EnAchievements::__notify(AchievementsDef::OFFER_BELLY, $achievePoint);
					// 记录成就ID
					$achieveArr[] = $achieveID;
				}
			}
		}
		// 返回所获取的成就
		return $achieveArr;
	}
	
	/**
	 * 指定用户当前的称号是否可隐藏
	 * @param $uid	用户ID
	 * @return 
	 * 		true 可隐藏
	 * 		false 不可隐藏
	 */
	static function isCurrentTitleCanHide($uid)
	{
		return AchievementsLogic::isCurrentTitleCanHide($uid);
	}
	
	
	/**
	 * 指定称号是否必须显示
	 * @param $title_id 称号ID
	 * @return unknown_type
	 */
	static function isTitleMustShow($title_id)
	{
		if(!isset(btstore_get()->TITLE[$title_id]))
		{
			return false;
		}
		
		return  btstore_get()->TITLE[$title_id]['nohiden'];
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */