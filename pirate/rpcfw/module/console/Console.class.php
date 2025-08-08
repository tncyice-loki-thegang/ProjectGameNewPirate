<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Console.class.php 40557 2013-03-11 12:55:48Z wuqilin $
 *
 **********************************************************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/console/Console.class.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-03-11 20:55:48 +0800 (一, 2013-03-11) $
 * @version $Revision: 40557 $
 * @brief
 *
 **/

class Console
{
	
	public function setBattleCache()
	{
		EnUser::getUserObj()->getBattleInfo();
		return 'ok';	
	}
	
	public function hasBattleCache()
	{
		$uid = RPCContext::getInstance()->getUid();
		$key = EnUser::getBattleInfoKey($uid);
		$battleInfo = McClient::get($key);
		if ($battleInfo!==null)
		{
			return 'yes';
		}
		return 'no';
	}
	
	public function elves($op, $num)
	{
		$uid = RPCContext::getInstance()->getUid();
		switch ($op)
		{
			case 'exp':
				ElvesDao::update($uid, array('exp'=>$num));
				break;
			case 'exp_time':
				$time = strtotime($num);
				ElvesDao::update($uid, array('exp_compute_time'=>$time));
				break;
			default:
				$op = intval($op);
				if (!is_int($op))
				{
					return "err, unknown $op\n";
				}
				
				if (!isset(btstore_get()->ELVES[$op]))
				{
					return "err, unknown $op\n";
				}
				
				$arr = ElvesDao::get($uid, ElvesObj::$field);
				$time = strtotime($num) + btstore_get()->ELVES[$op]['day'] * 86400;
				$arr['va_elves'][$op] = $time;
				ElvesDao::update($uid, $arr);
				return 'ok';				
		}
		
		
		return 'ok';
	}
	
	public function gemExp($exploreId, $num)
	{
		$user = EnUser::getUserObj();
		$user->addGemExp($num);
		$user->update();
		return 'ok';	
	}
	
	public function exploreIntegrate($exploreId, $num=0)
	{
		$uid = RPCContext::getInstance()->getUid();
		if ($num==0)
		{
			$ret = ExploreDao::getInfo($uid, $exploreId, array('integral'));
			if (!empty($ret))
			{
				return $ret['integral'];
			}			
			return 0;
		}
		else
		{
			ExploreDao::update($uid, $exploreId, array('integral'=>$num));
			return 'ok';
		}
	} 
	
	public function sign($step, $date)
	{
		if ($date>0)
		{
			exit("date > 0\n");
		}
		if ($step<1 or $step>7)
		{
			exit("step err\n");
		}		
		
		$uid = RPCContext::getInstance()->getUid();
		$time = strtotime("$date day"  ,Util::getTime());
		RewardSignDao::update($uid, array('sign_time'=>$time, 'step'=>$step));
		return 'ok';
	}
	
	public function signUpgradeTime($nday)
	{
		$time = -$nday*86400;
		$uid = RPCContext::getInstance()->getUid();
		$op = new DecOperator($time);
		RewardSignDao::update($uid, array('upgrade_time'=>$op));
		return 'ok';
	}

	public function execute($arg)
	{

		if (! FrameworkConfig::DEBUG)
		{
			Logger::fatal ( "non debug mode found console command" );
			throw new Exception ( 'close' );
		}

		$arg = trim ( $arg );
		$arg = preg_replace('/\s\s+/', ' ', $arg);
		$arrArg = explode ( ' ', $arg );
		$command = $arrArg [0];
		array_shift ( $arrArg );
		return call_user_func_array ( array ($this, $command ), $arrArg );
	}

	public function show()
	{
		$err = '哥，你是要show me the money吗？';
		$args = func_get_args();
		if (count($args)<3)
		{
			return 'aa';

			return $err;
		}

		if ($args[0]=='me' && $args[1]=='the' && 0!=preg_match('/^money/', $args[2]))
		{
			$user = EnUser::getUserObj();
			$user->addBelly(100*100*1000);
			$user->addPrestige(100*100*1000);
			$user->addPrestige(100*100*1000);
			$user->addGold(100*100*1000);
			$user->addExecution(100);
			$user->update();
			return '您太阴吧了';
		}
		return $err;

	}

	/**
	 *
	 * Enter description here ...
	 */
	public function code()
	{
		$args = func_get_args();
		$str = '';
		foreach ($args as $arg)
		{
			$str .= $arg . ' ';
		}
		$str .= ';';
		Logger::debug('strcode %s', $str);
		return eval($str);
	}

	/**
	 * user UID FUNCTION ARGV
	 * Enter description here ...
	 */
	public function user()
	{
		$args = func_get_args();
		if (count($args) < 2)
		{
			return 'err argv.';
		}

		$uid = $args[0];
		$fun = $args[1];
		$funArgs = $args;
		array_shift($funArgs);
		array_shift($funArgs);

		$user = EnUser::getUserObj($uid);
		$ret = call_user_func_array(array($user, $fun), $funArgs);
		if (!$ret)
		{
			return $fun . ' return:' . $ret;
		}
		$user->update();
		return $ret;
	}

	/**
	 * herof hid FUNCTION ARGV
	 * Enter description here ...
	 */
	public function  herof()
	{
		$args = func_get_args();
		if (count($args) < 2)
		{
			return 'err argv.';
		}

		$hid = $args[0];
		$fun = $args[1];
		$funArgs = $args;
		array_shift($funArgs);
		array_shift($funArgs);

		$hero = EnUser::getUserObj()->getHeroObj($hid);
		$ret = call_user_func_array(array($hero, $fun), $funArgs);
		EnUser::getUserObj()->update();
		return $ret;
	}
	
	public function addGoldOrder($addGold)
	{
		$addGold = intval($addGold);
		$uid = RPCContext::getInstance()->getUid();
		$orderId = 'AAAA_00_' . strftime("%Y%m%d%H%M%S") . rand(10000, 99999);
		$user = new User();
		$user->addGold4BBpay($uid, $orderId, $addGold);
		return 'ok';
	}


	/**
	 * echo pid
	 */
	public function pid()
	{
		return EnUser::getUserObj()->getPid();
	}

	/**
	 * echo uid
	 */
	public function uid()
	{
		return RPCContext::getInstance()->getUid();
	}

	public function group($id)
	{
		$uid  = RPCContext::getInstance()->getUid();
		UserDao::updateUser($uid, array('group_id'=>$id));
		return 'ok';
	}

	/**
	 * banChat uid N 禁言uid N分钟, uid=0, 禁言自己
	 * @param unknown_type $uid
	 */
	public function banChat($uid, $minutes)
	{
		$user = EnUser::getUserObj($uid);
		$endTime = Util::getTime() + $minutes * 60;
		$user->banChat($endTime);
		$user->update();
		return 'ok';
	}
	
	public function ban($uid, $sec, $msg='')
	{
		RPCContext::getInstance()->closeConnection($uid);
				
		$ret = UserDao::getUserFieldsByUid($uid, array('uid', 'va_user'));
		if (empty($ret))
		  {
		    return "not found $uid";
		  }

		$va_user = $ret['va_user'];
		$va_user['ban'] = array('time'=>Util::getTime() + $sec, 'msg'=>$msg);
		UserDao::updateUser($uid, array('va_user'=>$va_user, 'status'=>UserDef::STATUS_BAN));		
	}

	/**
	 * 所有英雄加到酒馆
	 * Enter description here ...
	 */
	public function allhero()
	{
		$all = btstore_get()->CREATURES;
		$user = EnUser::getUserObj();
		foreach ($all as $htid => $tmp)
		{
			if (HeroUtil::isHeroByHtid($htid))
			{
				if (!$user->hasHero($htid))
				{
					$user->addNewHeroToPub($htid);
				}
			}
		}
		$user->update();
		return 'ok';
	}
	
	

	public function allHeroLevel ($num)
	{
		if ($num > UserConf::MAX_LEVEL)
		{
			return "超过最大等级 " . UserConf::MAX_LEVEL;	
		}
		
		if ($num<=0)
		{
			return "必须大于等于0";
		}
		
		$user = EnUser::getUserObj();
		if ($user->getMasterHeroLevel() < $num)
		{
			return 'err:等级超过主角等级';
		}

		$arrHid = $user->getRctHeroOrder();
		foreach ($arrHid as $hid)
		{
			$hero = $user->getHeroObj($hid);
			if ($hero->isMasterHero())
			{
				continue;
			}
			HeroDao::update($hid, array('level'=>$num));
		}
		return 'ok';
	}
	
	public function allHeroRebirth ($num)
	{
		$user = EnUser::getUserObj();
		$level = $user->getMasterHeroLevel();
		
		if ($num > intval(($level - 45)/5) )
		{
			return "又调皮了，搞这么高的转生等级做什么";
		}
		
		$arrHid = $user->getRctHeroOrder();
		foreach ($arrHid as $hid)
		{
			$hero = $user->getHeroObj($hid);
			if ($hero->isMasterHero())
			{
				continue;
			}
			HeroDao::update($hid, array('rebirthNum'=>$num));
		}
		return 'ok';
	}

	/**
	 * blood N  设置血包数
	 * Enter description here ...
	 * @param unknown_type $num
	 */
	public function blood($num)
	{
		$user = EnUser::getUserObj();
		$cur = $user->getBloodPackage();
		$diff = $num - $cur;
		$ret = $user->addBloodPackage($diff);
		$user->update();
		return $ret;
	}

	public function transfer($num)
	{
		$num = intval($num);
		if ($num > 5 || $num < 0)
		{
			return '别瞎搞，转职目前只能为0到4';
		}
		$user = EnUser::getUserObj();
		$hero = $user->getMasterHeroObj();
		$attr = $hero->getAllAttr();
		$master = $attr['va_hero']['master'];
		$master['transfer_num'] = $num;
		$attr['va_hero']['master'] = $master;
		HeroDao::update($attr['hid'], array('va_hero'=>$attr['va_hero']));
		return true;
	}

	/**
	 * reset protect_cdtime
	 * Enter description here ...
	 */
	public function protect_cdtime()
	{
		$user = EnUser::getUserObj();
		$user->resetProctectCDTime();
		$user->update();
		return 'ok';
	}

	/**
	 * daytask complete N 设置已完成次数
	 * daytask integral N 设置积分
	 * daytask target N 刷新target为N个
	 * Enter description here ...
	 */
	public function daytask($op, $num=0)
	{
		$uid = RPCContext::getInstance()->getUid();
		$arrField = array();
		switch ($op)
		{
			case 'complete':
				$arrField = array('complete_num'=>$num);
				break;
			case 'integral':
				$arrField = array('integral'=>$num);
			case 'target':
				if ($num < 1 or $num > 3)
				{
					return 'err num';
				}
				$info = DaytaskLogic::getInfo();
				$info['va_daytask']['target_type'] = DaytaskLogic::refreshTargetType($info['level'], $num);
				DaytaskInfoDao::update($uid, $info);
				return $info['va_daytask']['target_type'];
			case 'info':
				$info = RPCContext::getInstance()->getSession('daytask.accept');
				return $info;
				break;
			default:
				return 'err. unknown ' . $op;
		}

		DaytaskInfoDao::update($uid, $arrField);
		return 'ok. ' . $op . ' ' . $num ;
	}

	/**
	 * 打开所有功能
	 * Enter description here ...
	 */
	public function open($type=0)
	{
		$uid = RPCContext::getInstance()->getUid();
		if ($type==0)
		{
			$data = ~0 & 0xfffffff;
			$arrField = array('data0'=>$data, 'data1'=>$data, 'data2'=>$data);
			SwitchDao::update($uid, $arrField);
		}
		else
		{
			SwitchLogic::setValue($type);
		}

		return 'ok';
	}

	public function close($type)
	{
		if ($type==0)
		{
			return 'err:close 0';
		}
		$uid = RPCContext::getInstance()->getUid();
		$key = 'data' . floor(($type-1) / 25);
		$pos = ($type-1) % 25;

		$value = 1;
		$value <<= $pos;
		$value = ~$value;

		$arr = SwitchDao::get($uid, array('data0', 'data1', 'data2'));
		$src = $arr[$key];
		$des = $src & $value;
		SwitchDao::update($uid, array($key=>$des));

	}

	public function isOpen($type)
	{
		$ret = EnSwitch::isOpen($type);
		return $ret;
	}

	/**
	 * 清空寻宝次数与打劫次数,打劫时间
	 */
	public function treasure()
	{
		$uid = RPCContext::getInstance ()->getUid ();
		$arrField = array ('hunt_num' => 0, 'rob_num' => 0,
			'rob_time' => 0, 'rob_cdtime' => 0, 'gold_refresh_num'=>0,
			'experience_refresh_num'=>0 );
		TreasureDao::update ( $uid, $arrField );
		return 'ok';
	}

	public function treasureInfo()
	{
		$treasure = new Treasure();
		return $treasure->getInfo();
	}
	
	public function treasureScore($type, $score)
	{
		if ($type!='red' && $type!='purple')
		{
			exit('type only red or purple');
		}
		
		$uid = RPCContext::getInstance()->getUid();
		$info = TreasureDao::getByUid($uid, array('va_treasure'));
		$info['va_treasure'][$type . "_score"] = $score;
		TreasureDao::update($uid, $info);
		return 'ok';
	}

	public function smeltingScore($score)
	{
		MySmelting::getInstance()->addIntegral(1, $score);
		MySmelting::getInstance()->addIntegral(2, $score);
		MySmelting::getInstance()->save();
		return 'ok';
	}

	/**
	 * execution clear 重置购买行动力限制
	 * execution num  设置行动力为num
	 * execution view 查看后端行动力
	 */
	public function execution($op)
	{

		$user = EnUser::getUserObj ();
		$uid = RPCContext::getInstance ()->getUid ();
		$arrField = array ();
		switch ($op)
		{
			case 'clear' :
				$arrField = array ('last_buy_execution_time' => 0 );
				break;
			case 'view':
				$cur = EnUser::getUserObj()->getCurExecution();
				return $cur;
				break;
			default :
				//if ($op > UserConf::MAX_EXECUTION)
				{
					//$op = UserConf::MAX_EXECUTION;
				}
				$arrField = array ('cur_execution' => $op, 'execution_time'=>Util::getTime() );
				break;
		}
		UserDao::updateUser ( $uid, $arrField );
		return 'ok';
	}

	/**
	 * arena succ num 连胜场次为num
	 * arena hist num 历史最大连胜为num
	 * arena upgrade num 连续上升名次
	 * @param unknown_type $op
	 * @param unknown_type $num
	 */
	public function arena($op, $num)
	{

		$num = intval ( $num );
		$uid = RPCContext::getInstance ()->getUid ();
		$arrField = array ();
		switch ($op)
		{
			case 'succ' :
				$arrField = array ('cur_suc' => $num );
				break;
			case 'hist' :
				$arrField = array ('history_max_suc' => $num );
				break;
			case 'upgrade' :
				$arrField = array ('upgrade_continue' => $num );
				break;
			case 'challenge' :
				$arrField = array('challenge_num' => $num);
				break;
			default :
				throw new Exception ( "unsupport command arena $op" );
				break;
		}
		ArenaDao::update ( $uid, $arrField );
		return 'ok';
	}

	public function belly($num)
	{
		if ($num=='view')
		{
			$user = EnUser::getUserObj ();
			return $user->getBelly ();
		}

		$num = intval ( $num );
		$user = EnUser::getUserObj ();
		$cur = $user->getBelly ();
		$user->addBelly ( $num - $cur );
		$user->update ();
		return $user->getBelly ();
	}

	public function vip($num)
	{
		$num = intval($num);
		$user = EnUser::getUserObj();
		$user->setVip($num);
		$user->update();
		return $user->getVip();
	}

	public function gold($num)
	{
		$num = intval ( $num );
		$user = EnUser::getUserObj ();
		$cur = $user->getGold ();
		$user->addGold ( $num - $cur );
		$user->update ();
		return $user->getGold ();
	}

	public function prestige($num)
	{

		$num = intval ( $num );
		$user = EnUser::getUserObj ();
		$cur = $user->getPrestige ();
		$user->addPrestige ( $num - $cur );
		$user->update ();
		return $user->getPrestige ();
	}

	public function experience($num)
	{

		$num = intval ( $num );
		$user = EnUser::getUserObj ();
		$cur = $user->getExperience ();
		$user->addExperience ( $num - $cur );
		$user->update ();
		return $user->getExperience ();
	}

	public function allExp($num)
	{
		$hero = EnUser::getUserObj()->getMasterHeroObj();
		$hid = $hero->getHid();
		$hero->setAllExp(0);
		$hero->addExp($num);
		if ($hero->getLevel() > UserConf::MAX_LEVEL)
		{
			return "超过最大等级";	
		}
		
		HeroDao::update($hid, array('all_exp' =>$num));
		
		return "ok";
	}
	
	public function level($num)
	{
		if ($num > UserConf::MAX_LEVEL)
		{
			return "超过最大等级";	
		}
		
		$num = intval ( $num );
		if ($num<=0)
		{
			return 'level must be >= 0';
		}
		$hero = EnUser::getUserObj ()->getMasterHeroObj();
		if ($num < $hero->getLevel())
		{
			return 'fail';
		}
		$hid = $hero->getHid();
		
		$expTblId = btstore_get()->CREATURES[$hero->getHtid()][CreatureInfoKey::expId];
		$allExp = 0;
		foreach (btstore_get()->EXP_TBL[$expTblId] as $cfgLevel => $cfgExp)
		{
			if ($num == $cfgLevel)
			{
				break;
			}	
			
			$allExp += $cfgExp;
		}
			
		//foreach (btstore_get()->EXP_TBL)
		HeroDao::update($hid, array('level'=>$num, 'all_exp' =>$allExp, 'exp'=>0));
		return 'ok';
	}

	public function exp($num)
	{
		$num = intval ( $num );
		$userObj = EnUser::getUserObj();
		$hero = $userObj->getMasterHeroObj();
		$cur = $hero->getExp();
		if ($num < $cur)
		{
			return "err. the $num is less than current value $cur";
		}
		$userObj->addExp ( $num - $cur );
		$userObj->update ();
		return array ('exp' => $hero->getExp (), 'level' => $hero->getLevel () );
	}

	public function atk_value($num)
	{

		$num = intval ( $num );
		$user = EnUser::getUserObj ();
		$cur = $user->getAtkValue ();
		$user->addAtkValue ( $num - $cur );
		$user->update ();
		return $user->getAtkValue ();
	}

	/**
	 * hero add htid
	 * hero info htid
	 * hero exp htid exp_num
	 * hero rebirth htid num
	 * hero level htid num
	 * hero recruit htid
	 * hero gl/goodwill_level htid level
	 * hero heritage htid 
	 * hero list
	 * @param unknown_type $op
	 */
	public function hero($op, $num=0, $num2 = 0)
	{
		//list
		if ($op=='list')
		{
			$arrRet = array();
			$hero = new Hero();
			$arrAttr = $hero->getRecruitHeroes();
			$arrHid = array();
			$user = EnUser::getUserObj();
			foreach ($arrAttr as $attr)
			{
				$arr = array('htid'=>$attr['htid'], 'hid'=>$attr['hid'],
					'curHp'=>$attr['curHp'], 'exp'=>$attr['exp'], 'all_exp'=>$attr['all_exp']);
				$hero = $user->getHeroObj($arr['hid']);
				$arr['maxHp'] = $hero->getMaxHp();
				$arr['rebirth'] = $hero->getRebirthNum();
				if ($hero->isMasterHero())
				{
					$arr['transfer_num'] = $hero->getTransferNum();
				}
				$arrRet[] = $arr;
			}

			return $arrRet;
		}



		if ($num[0]=='A' or $num[0]=='a')
		{
			$num = 10000 + intval(substr($num, 1));
		}
		else
		{
			$num = intval ( $num );
		}
		$num2 = intval ( $num2 );
		$user = EnUser::getUserObj ();
		switch ($op)
		{
			case 'add' :
				if (! $user->hasHero ( $num ))
				{
					$user->addNewHeroToPub ( $num );
					$user->update ();
				}
				return 'ok';
				break;
			case 'exp' :
				$hero = $user->getHeroObjByHtid ( $num );
				$hero->addExp ( $num2 - $hero->getExp () );
				$user->update ();
				return array ('exp' => $hero->getExp (), 'level' => $hero->getLevel () );
				break;
			case 'rebirth' :
				$hero = $user->getHeroObjByHtid ( $num );
				$hid = $hero->getHid ();
				if ($num2>20 || $num2<0)
				{
					return '别瞎jb搞，转生只能支持0-20';
				}
				$arrField = array ('rebirthNum' => $num2 );
				HeroDao::update ( $hid, $arrField );
				return 'ok';
				break;
			case 'level' :
				$hero = $user->getHeroObjByHtid ( $num );
				$hid = $hero->getHid ();
				if ($num<=0)
				{
					return 'err. level must be > 0';
				}
				$arrField = array ('level' => $num2 );
				HeroDao::update ( $hid, $arrField );
				return 'ok';
				break;
			case 'recruit':
				$htid = $num;
				if (!$user->hasHero($htid))
				{
					$user->addNewHeroToPub($htid);
					$user->update();
				}
				$hero = new Hero();
				return $hero->recruit($htid);
			case 'info':
				$htid = $num;
				$hero = $user->getHeroObjByHtid($htid);
				$ret = $hero->getInfo();
				return $ret;
			case 'gl':
			case 'goodwill_level':
				$htid = $num;
				$level = $num2;
				$uid = RPCContext::getInstance()->getSession('global.uid');
				$attr = HeroLogic::getHeroByUidHtid($uid, $htid, array('hid', 'va_hero'));
				$va_hero = $attr['va_hero'];
				$va_hero['goodwill']['level'] = $level;
				HeroDao::update($attr['hid'], array('va_hero'=>$va_hero));
				return 'ok';
				break;
			case 'gw':
			case 'goodwill':
				$htid = $num;
				$exp = $num2;
				$uid = RPCContext::getInstance()->getSession('global.uid');
				$attr = HeroLogic::getHeroByUidHtid($uid, $htid, array('hid', 'va_hero'));
				$va_hero = $attr['va_hero'];
				$va_hero['goodwill']['exp'] = $exp;
				HeroDao::update($attr['hid'], array('va_hero'=>$va_hero));
				return 'ok';
				break;			

			case 'heritage':
				$uid = RPCContext::getInstance()->getSession('global.uid');
				$attr = HeroLogic::getHeroByUidHtid($uid, $htid, array('hid', 'va_hero'));
				$va_hero = $attr['va_hero'];
				if (!isset($va_hero['goodwill']['heritage']))
				{
					$va_hero['goodwill']['heritage'] = 0;
				}
				
				if ($va_hero['goodwill']['heritage']==0)
				{
					$va_hero['goodwill']['heritage'] = 1;
				}
				else
				{
					$va_hero['goodwill']['heritage'] = 0;
				}
				 				
				HeroDao::update($attr['hid'], array('va_hero'=>$va_hero));
				return 'ok';
				break;

			default :
				return "error. unknown operate:$op";
				break;
		}
	}

	/**
	 * task accept taskId
	 * task finish taskId
	 * task reset taskId
	 * task list
	 * @param unknown_type $op
	 * @param unknown_type $taskId
	 * @return @see ITask::getAllTask
	 */
	public function task($op, $taskId='')
	{
		if ($op=='list')
		{
			if ($taskId=='accept')
			{
				return array_keys(TaskManager::getInstance()->getAcceptTask());
			}
			else
			{
				$task = new Task ();
				return $task->getAllTask ();
			}
		}

		$taskId = intval ( $taskId );


		$uid = RPCContext::getInstance ()->getUid ();
		//clear session
		TaskManager::resetSession ();
		TaskManager::release ();

		$status = TaskStatus::DELETE;
		$op = strtolower($op);
		switch ($op)
		{
			case 'accept' :
				$status = TaskStatus::ACCEPT;
				break;
			case 'finish' :
				$status = TaskStatus::COMPLETE;
				break;
			case 'reset' :
				$status = TaskStatus::DELETE;
				break;
			case 'canfinish':
			case 'can':
			case 'cansubmit':
				$mgr = TaskManager::getInstance();
				$ret = $mgr->canSubmit4Test($taskId);
				if($ret===-1)
				{
					return 'fail to find accept task:' . $taskId;
				}
				return 'ok. 注意：只对打怪任务和操作类型的任务有效';

				break;
			default :
				Logger::warning ( 'unknow operate %s for task', $op );
				return 'error. unknow operate ' . $op . 'for task';
				break;
		}

		if (! TaskManager::isExist ( $taskId ))
		{
			//Logger::warning ( 'the taskId %d is not exist', $taskId );
			return ( 'taskId ' . $taskId . ' no exist' );
		}

		if ($status==TaskStatus::DELETE)
		{
			TaskDao::delTaskForConsole($taskId, $uid);
		}
		else
		{
			TaskDao::insert ( $taskId, $uid, $status );
		}
		$task = new Task ();
		return $task->getAllTask ();
	}

	/**
	 *
	 * 增加物品
	 *
	 * @param int $item_template_id		物品模板id
	 * @param int $item_num				物品数量
	 * @param boolean 					是否添加到临时背包
	 *
	 * @return array
	 * <code>
	 * {
	 * 'add_success':boolean		是否成功
	 * 'bag_modify':array			背包修改
	 * [
	 * gid:itemInfo
	 * ]
	 * }
	 * </code>
	 */
	public function addItem($item_template_id, $item_num = 1, $in_tmp_bag = FALSE)
	{

		$item_template_id = intval ( $item_template_id );
		$item_num = intval ( $item_num );
		$bag = BagManager::getInstance ()->getBag ();
		if ($bag->addItemByTemplateID ( $item_template_id, $item_num, $in_tmp_bag ) == FALSE)
		{
			return array ('add_success' => FALSE );
		}
		else
		{
			$bag_modify = $bag->update ();
			return array ('add_success' => TRUE, 'bag_modify' => $bag_modify );
		}
	}

	/**
	 *
	 * 掉落物品
	 *
	 * @param int $drop_template_id
	 * @param int $number
	 * @param boolean $in_tmp_bag
	 *
	 * @return array
	 * <code>
	 * {
	 * 'add_success':boolean
	 * 'bag_modify':array			背包修改
	 * [
	 * gid:itemInfo
	 * ]
	 * }
	 * </code>
	 */
	public function dropItem($drop_template_id, $number = 1, $in_tmp_bag = FALSE)
	{

		$drop_template_id = intval ( $drop_template_id );
		$number = intval ( $number );
		if ($number <= 0)
		{
			return array ('drop_success' => FALSE );
		}
		$bag = BagManager::getInstance ()->getBag ();
		for($i = 0; $i < $number; $i ++)
		{
			if ($bag->dropItem ( $drop_template_id ) == FALSE)
			{
				return array ('add_success' => FALSE );
			}
		}

		$bag_modify = $bag->update ();
		return array ('add_success' => TRUE, 'bag_modify' => $bag_modify );

	}

	/**
	 *
	 * 设置背包的所有装备的强化等级
	 *
	 * @param int $reinforce_level
	 * @throws Exception
	 */
	public function reinforce($reinforce_level)
	{
		$reinforce_level = intval($reinforce_level);
		if ( $reinforce_level <=0 || $reinforce_level > ForgeConfig::ARM_MAX_REINFORCE_LEVEL )
		{
			$reinforce_level = ForgeConfig::ARM_MAX_REINFORCE_LEVEL;
		}
		$bag = BagManager::getInstance()->getBag();
		$item_ids = $bag->getItemIdsByItemType(ItemDef::ITEM_ARM);
		foreach ( $item_ids as $item_id )
		{
			$item = ItemManager::getInstance()->getItem($item_id);
			if ( $item->getItemType() != ItemDef::ITEM_ARM )
			{
				Logger::FATAL('invalid item_type!');
				throw new Exception('fake');
			}
			$item->setReinforceLevel($reinforce_level);
		}
		ItemManager::getInstance()->update();
		return TRUE;
	}

	/**
	 *
	 * 清空背包
	 *
	 * @return boolean				TRUE表示移除成功, FALSE表示移除失败
	 */
	public function clearBag()
	{

		$bag = BagManager::getInstance ()->getBag ();
		if ($bag->clearBag () == FALSE)
		{
			return FALSE;
		}
		else
		{
			$bag->update ();
			return TRUE;
		}
	}

	/**
	 *
	 * 背包格子信息
	 *
	 * @param int $gid
	 *
	 * @return array(itemInfo)
	 */
	public function gridInfo($gid)
	{
		$bag = BagManager::getInstance ()->getBag ();
		return $bag->gridInfo($gid);
	}

	/**
	 *
	 * 得到装备的计算信息
	 *
	 * @param int $item_id
	 *
	 * @return array
	 */
	public function armingInfo($item_id)
	{
		$item_id = intval($item_id);
		$item = ItemManager::getInstance()->getItem($item_id);
		$item_type = $item->getItemType();
		if ( $item_type != ItemDef::ITEM_ARM && $item_type != ItemDef::ITEM_DAIMONAPPLE )
		{
			return array();
		}
		else
		{
			return $item->info();
		}
	}

	/**
	 *
	 * 进入城镇
	 *
	 * @return boolean
	 *
	 * @todo 可能会导致其他部分数据错误,具体测试时在进行处理
	 */
	public function enterTown($town_id)
	{

		$town_id = intval ( $town_id );
		$city = new City ();
		$city->addEnterTownList ( $town_id );
		return TRUE;
	}

	/**
	 *
	 * 重置所有出售者卖出物品数量
	 *
	 * @return boolean
	 */
	public function clearSeller()
	{

		$values = array (
			SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM => 0
		);

		$wheres = array (
			array(SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM, '>', 0)
		);

		SellerDAO::updateSeller($values, $wheres);

		return TRUE;
	}

	/**
	 *
	 * 开启一个副本
	 *
	 * @param int $copyID						副本ID
	 *
	 * @return boolean
	 */
	public function openCopy($copyID)
	{

		$copyID = intval ( $copyID );
		$copyInst = new MyCopy ();
		$copyInst->addNewCopy ( $copyID );
		$copyInst->save ( $copyID );
		return TRUE;
	}

	/**
	 *
	 * 清空某部队杀敌次数
	 *
	 * @param int $enemyID						部队ID
	 *
	 * @return boolean
	 */
	public function reDefeatEnemy($enemyID)
	{
		$enemyID = intval ( $enemyID );
		// 获取副本ID
		$copyID = intval ( btstore_get ()->ARMY [$enemyID] ['copy_id'] );
		// 获取副本信息
		$copyInst = new MyCopy ();
		$copyInfo = $copyInst->getCopyInfo ( $copyID );
		// 如果玩家还没有这个副本信息
		if ($copyInfo === false)
		{
			// 给他加入一个副本
			$copyInst->addNewCopy ( $copyID );
			// 再获取一次
			$copyInfo = $copyInst->getCopyInfo ( $copyID );
		}
		// 获取用户杀怪信息
		$defeatList = $copyInfo ['va_copy_info'] ['defeat_id_times'];
		// 记录杀怪次数
		if (isset ( $defeatList [$enemyID] ))
		{
			unset ( $defeatList [$enemyID] );
		}
		// 保存次数
		$copyInst->updUserDefeatNum ( $copyID, $defeatList );

		// 保存到数据库
		$copyInst->save ( $copyID );
		return TRUE;
	}

	/**
	 *
	 * 增加杀敌次数和评价
	 *
	 * @param int $enemyID						部队ID
	 * @param int $times						打赢的次数
	 * @param int $appraisal					打的评价
	 *
	 * @return boolean
	 */
	public function defeatEnemy($enemyID, $times = 1, $appraisal = 3)
	{

		$enemyID = intval ( $enemyID );
		$times = intval ( $times );
		$appraisal = intval ( $appraisal );

		// TODO 暂时没有前置判断，等需要了再加上吧
		// 获取副本ID
		$copyID = intval ( btstore_get ()->ARMY [$enemyID] ['copy_id'] );
		// 获取副本信息
		$copyInst = new MyCopy ();
		$copyInfo = $copyInst->getCopyInfo ( $copyID );
		// 如果玩家还没有这个副本信息
		if ($copyInfo === false)
		{
			// 给他加入一个副本
			$copyInst->addNewCopy ( $copyID );
			// 再获取一次
			$copyInfo = $copyInst->getCopyInfo ( $copyID );
		}
		// 获取用户杀怪信息
		$defeatList = $copyInfo ['va_copy_info'] ['defeat_id_times'];
		// 记录杀怪次数
		if (isset ( $defeatList [$enemyID] ))
		{
			$defeatList [$enemyID] += $times;
		}
		else
		{
			$defeatList [$enemyID] = $times;
		}
		// 保存次数
		$copyInst->updUserDefeatNum ( $copyID, $defeatList );
		// 设置成绩
		if (! isset ( $copyInfo ['va_copy_info'] ['id_appraisal'] [$enemyID] ) ||
		 	$copyInfo ['va_copy_info'] ['id_appraisal'] [$enemyID] > $appraisal)
		{
			// 那么给戴个大红花
			$copyInst->setDefeatAppraisal ( $copyID, $enemyID, $appraisal );
		}
		// 通知任务系统
		TaskNotify::beatArmy( $enemyID, $appraisal );
		// 每日任务
		EnDaytask::beatSuccess();
		// 保存到数据库
		$copyInst->save ( $copyID );
		return TRUE;
	}

	/**
	 * 清空当日的熔炼次数
	 *
	 * @return boolean
	 */
	public function resetSmeltingTimes()
	{

		MySmelting::getInstance ()->resetSmeltingTimes ();
		MySmelting::getInstance ()->save ();
		return TRUE;
	}

	/**
	 * 加满所有科技
	 *
	 * @return boolean
	 */
	public function upSciTechLv($lv)
	{
		$lv = intval ( $lv );

		$tmp = new MySciTech();
		$tmp->upSciTechLv ($lv);
		$tmp->save ();

		return TRUE;
	}

	/**
	 * 清空当日的金币邀请工匠次数
	 *
	 * @return boolean
	 */
	public function resetArtificerTimes()
	{

		MySmelting::getInstance ()->resetArtificerTimes ();
		MySmelting::getInstance ()->save ();
		return TRUE;
	}

	/**
	 * 清空当日的会谈次数
	 *
	 * @return boolean
	 */
	public function resetTalksTimes()
	{

		TalksDao::updTalksInfo ( self::getUid (),
				array ('talk_date' => Util::getTime (), 'talk_times' => 0 ) );

		return TRUE;
	}

	/**
	 * 重置当日的会谈刷新次数
	 *
	 * @return boolean
	 */
	public function resetTalksRefreshTimes()
	{

		TalksDao::updTalksInfo ( self::getUid (),
				array ('refresh_date' => Util::getTime (), 'refresh_times' => 0 ) );

		return TRUE;
	}

	/**
	 * 清空出航次数
	 *
	 * @return boolean
	 */
	public function resetSailsTimes()
	{

		MyCaptain::getInstance ()->addSailTimesToMax ();
		MyCaptain::getInstance ()->save ();
		return TRUE;
	}

	/**
	 * 清空当日厨房制作次数
	 *
	 * @return boolean
	 */
	public function resetCooksTimes()
	{

		KitchenDao::updKitchenInfo ( self::getUid (),
				array ('cook_date' => Util::getTime (), 'cook_times' => 0 ) );

		return TRUE;
	}

	/**
	 * 清空当日厨房金币制作次数
	 *
	 * @return boolean
	 */
	public function resetGoldCooksTimes()
	{

		KitchenDao::updKitchenInfo ( self::getUid (),
				array ('gold_cook_date' => Util::getTime (), 'gold_cook_times' => 0 ) );

		return TRUE;
	}

	/**
	 * 清空当日厨房当日下单的次数
	 *
	 * @return boolean
	 */
	public function resetOrdersTimes()
	{

		KitchenDao::updKitchenInfo ( self::getUid (),
				array ('order_date' => Util::getTime (), 'order_times' => 0 ) );

		return TRUE;
	}

	/**
	 * 清除建筑队列CD
	 */
	public function resetBuildingListCD()
	{
		$listInfo = SailboatInfo::getInstance()->getBuildListInfo();

		foreach ($listInfo as $id => $v)
		{
			SailboatInfo::getInstance()->clearCDByGold($id);
		}
		SailboatInfo::getInstance()->save();

		return TRUE;
	}

	/**
	 * 清空当日厨房当日被下单的次数
	 *
	 * @return boolean
	 */
	public function resetBeOrdersTimes()
	{

		KitchenDao::updKitchenInfo ( self::getUid (),
				array ('order_date' => Util::getTime (), 'be_order_times' => 0 ) );

		return TRUE;
	}

	/**
	 * 清空当日军团战的次数
	 *
	 * @return boolean
	 */
	public function resetGroupBattleTimes()
	{
		$groupBattleInfo = CopyDao::getGroupBattleInfo(self::getUid ());
		$curTime = Util::getTime();
		$groupBattleInfo['normal_last_time'] = $curTime;
		$groupBattleInfo['normal_times'] = CopyConf::DAY_GROUP_TIMES;
		$groupBattleInfo['activity_last_time'] = $curTime;

		if (!empty($groupBattleInfo['va_copy_info']['copy_times']))
		{
			foreach ($groupBattleInfo['va_copy_info']['copy_times'] as $enemyID => $v)
			{
				$groupBattleInfo['va_copy_info']['copy_times'][$enemyID] = CopyConf::DAY_ACTIVITY_TIMES;
			}
		}
		// 更新数据库
		CopyDao::updateGroupBattle($groupBattleInfo['uid'], $groupBattleInfo);

		return TRUE;
	}

	/**
	 * 重置精英副本挑战次数
	 */
	public function resetEliteCopyTimes()
	{
		MyEliteCopy::getInstance()->resetChallengeTimes();
		return TRUE;
	}

	/**
	 * 增加新称号ID
	 *
	 * @return boolean
	 */
	public function addNewTitle($titleID)
	{
		EnAchievements::addNewTitle($titleID);
		return TRUE;
	}

	/**
	 * 删除已经获取到的成就
	 * 
	 * @param int $achieveID
	 */
	public function unsetAchievement($achieveID)
	{
		$achieve = MyAchievements::getInstance()->getAchieveByID($achieveID);
		if (empty($achieve))
		{
			return FALSE;
		}
		$achieve['is_show'] = 0;
		$achieve['is_get'] = 0;
		$achieve['get_time'] = 0;
		$achieve['va_a_info'] = array();

		AchievementsDao::updAchieveInfo(RPCContext::getInstance()->getUid(), 
		                                $achieveID, $achieve);
		return TRUE;
	}

	/**
	 * 通关精英副本
	 * 
	 * @param int $copyID
	 */
	public function passEliteCopy($copyID)
	{
		// 获取此人精英副本信息
		$eliteCopy = MyEliteCopy::getInstance()->getUserEliteInfo();
		// 是否可以进入这个副本
		// 先检查是否已经可以攻打
		if (!isset($eliteCopy['va_copy_info'][$copyID]))
		{
			return FALSE;
		}
		// 不是第一次进入的话
		if ($eliteCopy['progress'] != 0 && $eliteCopy['progress'] < $copyID)
		{
			return FALSE;
		}
		// 遍历之前的所有副本，如果没打到这里，也不能进入
		foreach ($eliteCopy['va_copy_info'] as $copyInfo)
		{
			// 尚有未通关副本，那么直接返回不能挑战
			if ($copyInfo['copy_id'] < $copyID && $copyInfo['is_end'] != 1)
			{
				return FALSE;
			}
		}
		// 只有首次通关才需要做这些事情
		if (MyEliteCopy::getInstance()->needPassCopy($copyID))
		{
			// 更新副本进度
			MyEliteCopy::getInstance()->upgradeProgress($copyID);
			MyEliteCopy::getInstance()->save();
		}
		return TRUE;
	}

	/**
	 * 增加一个新的英雄副本
	 * 
	 * @param int $copyID
	 */
	public function addNewHeroCopy($copyID)
	{
		MyHeroCopy::getInstance()->addNewCopy($copyID);
		MyHeroCopy::getInstance()->save($copyID);
	}

	/**
	 * 通关英雄副本
	 * 
	 * @param int $copyID
	 */
	public function passHeroCopy($copyID)
	{
		MyHeroCopy::getInstance()->setCopyOver($copyID);
		MyHeroCopy::getInstance()->save($copyID);
	}
	
	public function passLhy($copyID)
	{
		$this->addNewHeroCopy($copyID);
		$this->passHeroCopy($copyID);
	}

	/**
	 * 设置未通关英雄副本
	 * 
	 * @param int $copyID
	 */
	public function unPassHeroCopy($copyID)
	{
		MyHeroCopy::getInstance()->unsetCopyOver($copyID);
		MyHeroCopy::getInstance()->save($copyID);
	}

	/**
	 * 重置领奖次数
	 */
	public function resetImpelPrizeTimes()
	{
		MyImpelDown::getInstance()->resetPrizeTimes();

		return 'ok';
	}

	/**
	 * 清除隐藏关信息
	 */
	public function clearImpelHiddenFloor()
	{
		MyImpelDown::getInstance()->clearHiddenFloor();

		return 'ok';
	}

	/**
	 * 设置推进城进度
	 */
	public function setImpelProgress($lFloor, $sFloor)
	{
		MyImpelDown::getInstance()->setProgress($lFloor, $sFloor);

		return 'ok';
	}

	/**
	 * 击败某个小层
	 * 
	 * @param int $floorID						小层ID
	 */
	public function defeatSFloorInImpelDown($floorID)
	{
		$user = EnUser::getUserObj();
		// 通过小层ID获取大层ID
		$lFloorID = btstore_get()->FLOOR_S[$floorID]['l_id'];
		// 查看这一层有多少小层
		$allSFloorNum = count(btstore_get()->FLOOR_L[$lFloorID]['s_floor_list']);
		// 获取这一层最后的一个小层ID
		$lastSFloorID = btstore_get()->FLOOR_L[$lFloorID]['s_floor_list'][$allSFloorNum - 1];
		// 如果通关了，那么就做特殊处理，进行奖励
		if ($lastSFloorID == $floorID && btstore_get()->FLOOR_L[$lFloorID]['type'] != ImpelConf::HIDE_FLOOR)
		{
			// 只有首次通关才需要做这些事情
			if (MyImpelDown::getInstance()->needPassCopy($lFloorID) && 
				$user->getLevel() > btstore_get()->FLOOR_L[btstore_get()->FLOOR_L[$lFloorID]['after_id']]['open_lv'])
			{
				// 更新副本进度
				MyImpelDown::getInstance()->upgradeCopyProgress(btstore_get()->FLOOR_L[$lFloorID]['after_id']);
			}
			// 更新排行 
			MyImpelDown::getInstance()->upgradeRank($floorID);
			// 将通关标识一下，方便前端使用
			MyImpelDown::getInstance()->upgradeArmyProgress($lFloorID, 0);
			// 设置通关
			MyImpelDown::getInstance()->setEnd($lFloorID);
		}
		// 隐藏关的话，通关之后需要删除数据
		else if ($lastSFloorID == $floorID)
		{
			MyImpelDown::getInstance()->clearHideCopyProgress($lFloorID);
		}
		// 没有通关，则需要更新进度
		else 
		{
			// 获取下一层的ID
			$nextFloorID = btstore_get()->FLOOR_S[$floorID]['next_id'];
			// 更新进度
			MyImpelDown::getInstance()->upgradeArmyProgress($lFloorID, $nextFloorID);
			// 更新排行 
			MyImpelDown::getInstance()->upgradeRank($floorID);
		}
		MyImpelDown::getInstance()->save();
	}

	// 成就暂空
	// 增加悬赏值
	/**
	 * 获取用户ID
	 */
	private static function getUid()
	{

		// 获取用户ID
		$uid = RPCContext::getInstance ()->getSession ( 'global.uid' );
		// 如果没有获取到
		if (empty ( $uid ))
		{
			Logger::fatal ( 'Can not get Captain info from session!' );
			throw new Exception ( 'fake' );
		}
		return $uid;
	}
	
	public function soul($type, $num=0)
	{
		$arrType = array('purple', 'blue', 'belly', 'gold', 'vip');
		if (!in_array($type, $arrType))
		{
			return "type err, $type\n";
		}
		
		$soul = SoulObj::getInstance();
		$soul->get();
		
		$uid = RPCContext::getInstance()->getUid();
		$data = new CData();
		$arrUpdate = array();
		if ($type == 'purple' || $type=='blue')
		{
			$arrUpdate[$type] = $num;
		}
		else
		{
			if ($type=='belly')
			{
				$arrUpdate['belly_num'] = $num;
			}
			else if ($type =='gold')
			{
				$arrUpdate['gold_num'] = $num;
			}
			else if ($type=='vip')
			{
				$arrUpdate['vip_gold_num'] = $num;
			}
			else
			{
				exit("err type:$type");
			}			
		}
		
		$data->update('t_soul')->set($arrUpdate)->where('uid', '=', $uid)->query();
		return 'ok';		
	}

	public function initallblue()
	{
		$uid = RPCContext::getInstance()->getUid();
		$data = new CData();
		$arrUpdate['va_belly_times'] = AllBlueDao::initBellyCount();
		$data->update('t_allblue')->set($arrUpdate)->where('uid', '=', $uid)->query();
		return 'ok';		
	}

	public function initFishTimes()
	{
		$uid = RPCContext::getInstance()->getUid();
		$data = new CData();
		$arrUpdate = array();
		$arrUpdate['farmfish_times'] = 10;
		$arrUpdate['farmfish_tftimes'] = 0;
		$arrUpdate['farmfish_wftimes'] = 0;
		$arrUpdate['farmfish_wdftimes'] = 0;
		$data->update('t_allblue')->set($arrUpdate)->where('uid', '=', $uid)->query();
		return 'ok';	
	}
	
	public function friendList()
	{
		$uid = RPCContext::getInstance()->getUid();
		return FriendLogic::getFriendList($uid);
	}

	public function upFFishInfo($uid, $queueId, $status)
	{
		// 取得用户uid
		if(EMPTY($uid))
		{
			return;
		}
	    $allBlueInfo = AllBlueDao::getAllBlueInfo($uid);
		if (empty($allBlueInfo))
		{
			$allBlueInfo = array('uid' => $uid, 
					'va_belly_times' => AllBlueDao::initBellyCount(), 
					'gold_times' => 0, 
					'collect_time' => 0, 
					'monster_id' => 0,
					'atkmonster_fail_times' => 0,
					'status' => DataDef::NORMAL,
					'farmfish_times' => 0,
					'farmfish_tftimes' => 0,
					'farmfish_wftimes' => 0,
					'va_farmfish_queueInfo' => AllBlueDao::initFishQueue(),
					'farmfish_time' => 0);
			$data = new CData();
			$data->insertInto('t_allblue')->values($allBlueInfo)->query();
		}
		// 随即1条鱼出来
		// 配置表信息
    	$mAllBlue = btstore_get()->ALLBLUE->toArray();
    	$mFish = btstore_get()->FISH->toArray();
    	
    	// 可以随即的鱼
    	$fishArray = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_GROUPSEAFISH];
    	
    	// 做成鱼的随即数组(鱼ID和权重)
    	$randFishArray = array();
    	for ($i = 0; $i < count($fishArray); $i++)
    	{
    		if(!isset($mFish[$fishArray[$i]]))
    		{
    			continue;
    		}
    		$randFishArray[$mFish[$fishArray[$i]][AllBlueDef::ALLBLUE_FARMFISH_ID]] = 
    					array(AllBlueDef::ALLBLUE_FARMFISH_REFISHINGWEIGHT =>
    						  $mFish[$fishArray[$i]][AllBlueDef::ALLBLUE_FARMFISH_REFISHINGWEIGHT]);
    	}
    	
    	$krillId = array();
    	if(!EMPTY($randFishArray))
    	{
			$krillId = Util::noBackSample($randFishArray,
									1, 
									AllBlueDef::ALLBLUE_FARMFISH_REFISHINGWEIGHT);
    	}

		// (0:空闲 1:养殖中 2:成熟)
		if($status == 0)
		{
			$queue = AllBlueDao::initFishQueue();
			$allBlueInfo['va_farmfish_queueInfo'][$queueId] = $queue[$queueId];
		}
		else if ($status == 1)
		{
			$btime = intval(Util::getTime());
			$etime = $btime + 300;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['qstatus'] = 1;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['fstatus'] = 1;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['btime'] = $btime;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['etime'] = $etime;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['fishid'] = $krillId[0];
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['isboot'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['tfcount'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['wfcount'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['krillid'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['krillinfo'] = array();
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['thief'] = array();
		}
		else if ($status == 2)
		{
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['qstatus'] = 1;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['fstatus'] = 2;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['btime'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['etime'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['fishid'] = $krillId[0];
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['isboot'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['tfcount'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['wfcount'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['krillid'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['krillinfo'] = array();
			$allBlueInfo['va_farmfish_queueInfo'][$queueId]['thief'] = array();
		}
		// 更新
		$data = new CData();
		$arrUpdate['va_farmfish_queueInfo'] = $allBlueInfo['va_farmfish_queueInfo'];
		$data->update('t_allblue')->set($arrUpdate)->where('uid', '=', $uid)->query();
		return 'ok';	
	}
	
	public function matureFish($fish0, $fish1, $fish2)
	{
		$uid = RPCContext::getInstance()->getUid();
		$fishAry = array($fish0, $fish1, $fish2);
    	for ($i = 0; $i < 3; $i++)
    	{
			$allBlueInfo['va_farmfish_queueInfo'][$i]['qstatus'] = 1;
			$allBlueInfo['va_farmfish_queueInfo'][$i]['fstatus'] = 2;
			$allBlueInfo['va_farmfish_queueInfo'][$i]['btime'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$i]['etime'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$i]['fishid'] = $fishAry[$i];
			$allBlueInfo['va_farmfish_queueInfo'][$i]['isboot'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$i]['tfcount'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$i]['wfcount'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$i]['krillid'] = 0;
			$allBlueInfo['va_farmfish_queueInfo'][$i]['krillinfo'] = array();
			$allBlueInfo['va_farmfish_queueInfo'][$i]['thief'] = array();
    	}
		// 更新
		$data = new CData();
		$arrUpdate['va_farmfish_queueInfo'] = $allBlueInfo['va_farmfish_queueInfo'];
		$data->update('t_allblue')->set($arrUpdate)->where('uid', '=', $uid)->query();
		return 'ok';	
	}
	
	public function addHonourPoint($point)
	{
		$uid = RPCContext::getInstance()->getUid();
		$data = new CData();
		$arrUpdate = array();
		$arrUpdate['honour_point'] = $point;
		$data->update('t_honourshop')->set($arrUpdate)->where('uid', '=', $uid)->query();
		return 'ok';
	}
	
	public function fightForce($uid=0)
	{
		$user = EnUser::getUserObj($uid);
		$fightForce = $user->getFightForce();
		return $fightForce;
	}
	
	public function heritage($num)
	{
		$user = EnUser::getUserObj();
		$cur = $user->getTodayHeritageGoodwillNum();
		$dis = $num - $cur;
		$user->addHeritageGoodwillNum($dis);
		$user->update();
	}
	
	
	
	public function resetTNcnt()
	{
		$uid = RPCContext::getInstance()->getUid();
		EnTreasure::decreaseNpcBoatCnt($uid,1);
		return 'ok';
	}
	
	
	public function rollTN($tn_lvl)
	{
		$bt_ids = btstore_get()->NPC_TREASURE[0]['npc_boat_ids'][$tn_lvl];
		
		$cur_time = Util::getTime();
		$actTime = array($cur_time,$cur_time+5);
		TreasureNpcDao::activateTreasureNpc($bt_ids,$actTime);
		return 'ok';
		
	}
	
	
	public function setRobLeft($bt_id,$cnt)
	{
		$arrField = array(TreasureNpcDef::TREASURE_NPC_ROB_LEFT_CNT => $cnt);
		TreasureNpcDao::updateNpcBoatInfo($bt_id,$arrField);
		return 'ok';
	}
	
	
	public function setSprFestDay($day)
	{
		$uid = RPCContext::getInstance()->getUid();
		$arrField = array(
			'day' => $day
 		);
 		
 		SpringFestivalWelfareDao::updateWelfare($uid,$arrField);
 		return 'ok';
	}
	
	public function addNpcResOccupyCount($count)
	{
		$obj= new NpcResource();
		$obj->addOccupyCount($count);
		return 'ok';
	}
	
	public function addNpcResPlunderCount($count)
	{
		$obj= new NpcResource();
		$obj->addPlunderCount($count);
		return 'ok';
	}
	public function dig($op, $args)
	{
		$uid = RPCContext::getInstance()->getUid();
		switch($op)
		{
			case 'free':
				$freeNum = intval($args);
				$values = array('free_num' => $freeNum);
				DigActivityDAO::update($uid, $values);
				return 'ok';
			case 'time':
				$lastTime = intval($args);
				$values = array('last_dig_time' => time()-86400*$lastTime);
				DigActivityDAO::update($uid, $values);
				return 'ok';
	
		}
	
		return "not support:$op, $args";
	}
	
	public function  addjewelryenery($val)
	{
		$uid = RPCContext::getInstance()->getUid();
		Jewelry::addEnergyElement($uid,$val,0);
		return 'ok';
	}
	
	public function  addjewelryelement($val)
	{
		$uid = RPCContext::getInstance()->getUid();
		Jewelry::addEnergyElement($uid,0,$val);
		return 'ok';
	}
	
	public function setPirateBattleInfo($ach_type,$value)
	{
		$uid = RPCContext::getInstance()->getUid();
		
	/*	$arrField = array();
		$arrField[0] = $value;

		
		// 设置属性
		$arr = array('uid' => $uid,
					 'achieve_id' => $ach_id,
					 'is_show' => 0,
					 'is_get' => 0,
					 'get_time' => 0,
					 'va_a_info' => $arrField,
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblUserAchieve)
		               ->values($arr)->query();*/
		
		EnAchievements::notify($uid,$ach_type,$value);
	}
	
	public function abyss($op, $args)
	{
		$uid = RPCContext::getInstance()->getUid();
		switch($op)
		{
			case 'clgnum':
				$values = array(
						'left_clg_num' => intval($args)
						);
				AbyssCopyDAO::update($uid, $values);
				return 'ok';
			case 'exenum':
				$values = array(
						'left_exe_num' => intval($args)
						);
				AbyssCopyDAO::update($uid, $values);
				return 'ok';
			case 'buynum':
				$values = array(
				'week_buy_num' => intval($args)
				);
				AbyssCopyDAO::update($uid, $values);
				return 'ok';
			case 'time':
				$values = array(
				'last_enter_time' => time()-86400*intval($args)
				);
				AbyssCopyDAO::update($uid, $values);
				return 'ok';
				
			case 'fightNum':
				$uid = RPCContext::getInstance()->getUid();
				$copyUUID = RPCContext::getInstance()->getSession(AbyssCopyDef::SESSION_COPY_UUID);				
				if(empty($copyUUID))
				{
					return 'not in copy';
				}
				$fightNum = intval($args);
				$memInst = AbyssCopyMem::getInstance();
				$memInst->modifyUser(array($uid), NULL, array('fightNum' =>$fightNum ));
				$memInst->saveCopyData();
				return 'ok';
		}
	
		return "not support:$op, $args";
	}
	

	public function __call($method, $arrArg)
	{


		return call_user_func_array ( array ('GuildConsole', $method ), $arrArg );
	}

	public function help()
	{

		return '显示帮助信息：help
		user UID FUNCTION ARGV 后端自测用的指令
		banChat uid N 禁言uid N分钟, uid=0, 禁言自己
		查看uid： uid
		查看pid: pid
		设置group id：group id
		设置血包数 blood N
		清楚保护时间: protect_cdtime
		设置公会等级： setGuildLevel 新的公会等级
		设置公会等级经验: setGuildData 公会等级当前经验
		设置公会经验等级: setGuildExpLevel 新的公会经验等级
		设置公会经验经验: setGuildExpData 公会经验值当前经验
		设置公会阅历等级: setGuildExperienceLevel 新的公会阅历等级
		设置公会阅历经验: setGuildExperienceData 公会阅历值当前经验
		设置公会资源等级: setGuildResourceLevel 新的公会资源等级
		设置公会资源经验: setGuildResourceData 公会资源值当前经验
		设置公会宴会等级: setGuildBanquetLevel 设置公会宴会科技等级
		设置公会积分: setGuildRewardPoint 设置公会积分
		设置公会周贡献: setGuildWeekContribute 设置公会周贡献
		设置公会成员贡献值: setGuildContributeData 设置公会成员贡献
		重置公会宴会举办时间: resetGuildBanquet
		设置公会成员日贡献值: setGuildDayBelly 设置公会成员日贡献
		重置公会成员日belly：resetGuildDayBelly
		重置公会成员金币贡献时间：resetGuildDayGold
		清空寻宝次数与打劫次数，打劫时间, 刷新次数: treasure
		重置购买行动力限制: execution clear
		设置行动力: execution 新的行动力
		查看后端行动力 execution view
		设置vip： vip 新的vip等级
		设置belly： belly N
		设置金币: gold N
		设置威望： prestige N
		设置阅历： experience N
		设置等级： level N
		设置经验: exp N
		设置攻击值： atk_value N
		竞技场：
		arena succ N  设置连胜场次为N
		arena hist N  设置历史最大连胜为num
		arena upgrade N 连续上升名次为N
		arena challenge N 设置挑战次数
		英雄：
		hero recruit HTID 招募英雄
		hero info HTID 英雄计算出来的属性
		hero add HTID 添加模板id为HTID的英雄到酒馆
		hero exp HTID EXP  设置模板id为HTID的hero经验为EXP
		hero rebirth HTID NUM 设置模板id为HTID的hero转生次数为NUM
		hero level HTID NUM 设置模板id为HTID的hero等级为NUM
		hero list 查看英雄 当前生命， 最大生命， 转生次数 hid， htid
		allHeroLevel NUM 把所有已招募的英雄（除主角英雄）设置为等级NUM，
		transfer NUM 转职次数
		任务：
		task accept taskId 接受任务taskId
		task finish taskId 完成任务taskId
		task reset taskId 重置任务taskId
		task list 列出所有任务的详细信息
		task list accept 列出已接任务的id
		task canfinish 修改所有已接任务为可完成。只对打怪任务和操作类型的任务有效
		task can 同上
		task can taskId 修改已接任务taskId为可提交。只对打怪任务和操作类型的任务有效
		物品:
		addItem $item_template_id $item_num=1 $in_tmp_bag=false 增加物品
		dropItem $drop_template_id $number=1 $in_tmp_bag=false 调用掉落表
		clearBag	清空背包
		gridInfo $gid 得到某个格子的物品信息
		reinforce $reinforce_level 设置背包的所有装备的强化等级
		armingInfo $item_id 装备信息
		商店：
		clearSeller 重置所有出售者卖出物品数量
		城镇:
		enterTown $town_id  进入城镇
		主船：
		清空建筑队列CD:resetBuildingListCD
		清空当日厨房当日被下单的次数: resetBeOrdersTimes
		清空当日厨房当日下单的次数: resetOrdersTimes
		清空当日厨房制作次数: resetCooksTimes
		清空当日厨房金币制作次数: resetGoldCooksTimes
		清空出航次数: resetSailsTimes
		清空当日的会谈次数: resetTalksTimes
		重置当日的会谈刷新次数: resetTalksRefreshTimes
		清空当日的金币邀请工匠次数: resetArtificerTimes
		清空当日的熔炼次数: resetSmeltingTimes
		升满所有科技： upSciTechLv 指定等级
		副本：
		清空某部队杀敌次数： reDefeatEnemy 部队ID
		增加杀敌次数和评价: defeatEnemy 部队ID 打赢的次数 打的评价
		开启一个副本: openCopy 副本ID
		清空军团战次数: resetGroupBattleTimes
		重置精英副本挑战次数: resetEliteCopyTimes
		通关精英副本 passEliteCopy 精英副本ID
		通关英雄副本 passHeroCopy 英雄副本ID
		设置英雄副本为未通关  unPassHeroCopy 英雄副本ID
		击败推进城的某个小层 defeatSFloorInImpelDown 小层ID
		清空推进城的领奖次数 resetImpelPrizeTimes
		清除推进城隐藏关信息 clearImpelHiddenFloor
		设置推进城进度 setImpelProgress 大层ID 小层ID
		每日任务：
		daytask complete N 设置已完成次数
		daytask intgral N 设置积分
		daytask target N 刷新target为N个 N 为1-3
		open 打开所有功能
		isOpen N 功能是否打开
		allhero 把所有英雄添加到酒馆
		belly view 查看当前用户多少belly
		addNewTitle 称号ID  增加一个称号
		unsetAchievement 成就ID 删除一个成就
		addGoldOrder 金币数量  添加充值的金币
		hero gl/goodwill_level HTID 好感度等级  设置英雄好感度等级
		hero gw/goodwill HTID 好感度等级经验  设置英雄好感度经验 
		treasureScore red/purple N 设置寻宝红色/紫色积分
		smeltingScore N 增加装备制作积分
		sign step N 每日签到， step 为1-7, N必须<=0, N等于-1,表示昨天签的第step步 	
		signUpgradeTime -N 每日签到时间往前减N天 
		soul purple/blue N 设置紫魂/蓝魂数量	
		soul belly/gold/vip N 设置belly 金币 vip免费造魂次数
		fightforce 战斗力值
		hero heritage HTID  设置英雄已经传承/未传承
		ban UID SECOND  封号  uid:被封uid， SECOND: 从现在开始，封多少秒
		heritage N  设置好感传承次数
		exploreIntegrate EXPLORE_ID NUM 探索积分。   EXPLORE_ID 为探索表id，如1004; NUM为空或者为0,显示积分， 不为空并且不等于0设置积分
		initallblue 初始化采集次数 
		initFishTimes 初始化自己偷鱼/祝福鱼/养鱼次数 
		friendList 获得好友列表
		upFFishInfo FUID QUEUEID STATUS 更新好友加的养鱼队列  FUID:好友的UID QUEUEID:队列ID(0,1,2) STATUS:养殖状态(0:空闲 1:养殖中 2:成熟)
		matureFish 鱼苗id1 鱼苗id2 鱼苗id3 fishid让所有队列的鱼成熟 
		addHonourPoint N    N是增加荣誉的积分数
		gemExp exploreId exp 增加宝石经验， 如：gemExp 1004 10000
		resetTNcnt 重置寻宝npc次数
		rollTN [0,1,2]	刷寻宝NPC
		setRobLeft boatID cnt boatID npc船ID，cnt 当前次数
		elves exp N 设置经验值为N				
		elves exp_time 20130120 设置上次经验计算时间为2013年1月20日
		elves 1 20130120  在20130120开启id 1
		setSprFestDay day 指定天数
		addNpcResOccupyCount count 增加npc资源矿的可占领次数
		addNpcResPlunderCount count 增加npc资源矿的可掠夺次数
		setPirateBattleInfo ach_type 成就小类,value 具体值
		dig free N 将挖宝免费次数改成N
		dig time N 将上一次挖宝时间改成N天前
	    addjewelryenery N 增加宝物系统里的能量石
		addjewelryelement N 增加宝物系统里的元素石
		setBattleCache 设置battle缓存
		hasBattleCache 当前是否有battle信息缓存
		abyss clgnum N 将剩余挑战次数改成N
		abyss exenum N 将剩余练习次数改成N
		abyss buynum N 将本周购买次数改成N
		abyss time 	N  将上一次挑战时间改成N天前
		';
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */