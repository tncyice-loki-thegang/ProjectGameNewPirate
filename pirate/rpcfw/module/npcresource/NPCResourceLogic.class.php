<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: NPCResourceLogic.class.php 36926 2013-01-24 07:07:34Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/branches/pirate/rpcfw/newworldres/module/npcresource/NPCResourceLogic.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-24 15:07:34 +0800 (星期四, 24 一月 2013) $
 * @version $Revision: 36926 $
 * @brief 
 *  
 **/


/*
 * 这里面处理与单个玩家无关的逻辑
 */

class  NpcReourceLogic
{
	private static $_serverLevel=NULL;			//服务器等级 

	/********************从这往下是为crontab提供的接口**************************************/
	/*
	 * 每天凌晨四点，crontab会调用该函数，重置服务器等级
	 * 服务器等级是服务器 max（等级排名前20的玩家的等级平均值，进入矿区最低等级）
	*/
	public static function   resetServerLevelEveryDay()
	{
		//获取前20的等级，然后平均
		$serverlevel=0;
		$arrRet = HeroLogic::getMasterTopLevel(0, NPCResourceDef::NPC_RESOURCE_SERVER_CAL_COUNT, array('level'));
		foreach ($arrRet as $val)
		{
			$serverlevel+=$val['level'];
		}
		$serverlevel=intval($serverlevel/NPCResourceDef::NPC_RESOURCE_SERVER_CAL_COUNT);
		
		//更新
		$limitminlevel=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_ENTER_MIN_LEVEL];
		$serverlevel= ($serverlevel > $limitminlevel) ?$serverlevel:$limitminlevel;
		self::updateServerLevel($serverlevel);
		
		//同时更新静态变量
		self::$_serverLevel=$serverlevel;
	}
	/*
	 * 每天凌晨四点，crontab会调用该函数，重置所有的资源矿的海贼团id，并将矿改为npc占领状态
	 * 服务器等级是服务器 max（等级排名前20的玩家的等级平均值，进入矿区最低等级）
	 * 郑琛原话：NPC再次占领的时候 不需要指定某个特定部队 只是给他表示这里是给 NPC海贼团占领了 只有在玩家去触发战斗的时候才需要随机决定该次战斗的部队是什么
	 * 所以每次改成npc占领时，不要指定到期timer
	 */
	public static function   resetResourceEveryDay()
	{
		$allres=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_RES_ID_ATTR_ARY];
		foreach ($allres as $pageid=>$resval)
		{
			//先拉数据
			$wheres  =array(array (NPCResourceDef::NPC_RESOURCE_SQL_RES_ID, '>', 0),
					array (NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID, '>', 0),
					array (NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID, '=', $pageid));
			$selectfield = array(  NPCResourceDef::NPC_RESOURCE_SQL_RES_ID,
					NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID,);
			$ret= NpcResourceDao::getFromNpcResInfoTbl($selectfield, $wheres);

			//如果为空或者和btstore里的个数不一致(比如策划更新了csv)，则需要初始化
			$excludepirateids=array();$newresinfo=array();
			$resids=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_RES_ID_ATTR_ARY][$pageid];
			if (empty($ret)|| (count($resids) > count($ret)&& count($ret) > 0) )
			{
				$newresinfo=NpcReourceLogic::initResourceToNpcAttack($pageid,$ret);
			}
			if (empty($ret))
			{
				continue;
			}
			foreach ($newresinfo as $val)
			{
				$excludepirateids[]=$val[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
			}
			//根据服务器等级获得海贼团id组
			$avilabids=array();
			$pirateidary=self::getPirateIdsByServerLevel();
			foreach ($pirateidary as $id)
			{
				if (!in_array($id, $excludepirateids)&&isset(btstore_get()->NPC_PIRATE[$id]))
				{
					$avilabids[intval($id)]=array('weight' =>btstore_get()->NPC_PIRATE[$id][NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_WEIGHTS]);
				}
			}
			//随机海贼团,并更新
			$index=0;
			$pirateids=Util::noBackSample($avilabids, count($ret));
			foreach ($ret as $resval)
			{
				$resid=$resval[NPCResourceDef::NPC_RESOURCE_SQL_RES_ID];
				//设置成npc占领
				self::resetResourceToNpcAttack($pageid,$resid,$pirateids[$index]);
				$index++;
			}
		}
	}
	/********************crontab提供的接口结束******************************************/
	
	/*
	 * 初始化npc资源矿信息，并将这些矿设置为npc占领
	 */
	public static function   initResourceToNpcAttack($page_id,$curval)
	{
		//获得锁
		$locker = new Locker();
		$locker->lock(NpcReourceLogic::getResLockName($page_id, -1));
		
		//找到该页内的所有资源id
		if (!isset(btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_RES_ID_ATTR_ARY][$page_id]))
		{
			Logger::fatal('initResourceToNpcAttack pagid err pagid:%d',$page_id);
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, -1));
			return;
		}
		$resids=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_RES_ID_ATTR_ARY][$page_id];
		
		//当前已经存在了的资源矿id
		$curresids=array();$curpirateids=array();
		foreach ($curval as $val)
		{
			$curresids[]=$val[NPCResourceDef::NPC_RESOURCE_SQL_RES_ID];
			$curpirateids[]=$val[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
		}
		
		//根据服务器等级，找到对应的海贼团id
		$pirateids=self::getPirateIdsByServerLevel();
		//获得对应的海贼团id数组,去掉已经用过的海贼团id
		$avilabpirateids=array();
		foreach ($pirateids as $id)
		{
			if ( !in_array($id, $curpirateids) && isset(btstore_get()->NPC_PIRATE[$id]))
			{
				$avilabpirateids[intval($id)]=array('weight' =>btstore_get()->NPC_PIRATE[$id][NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_WEIGHTS]);
			}
		}
		
		//需要更新的资源id
		$newresids=array();
		foreach ($resids as $resid=>$attr)
		{
			if (!in_array($resid, $curresids))
			$newresids[]=$resid;
		}
		
		//随机海贼团id
		if (count($avilabpirateids)< count($newresids) )
		{
			Logger::FATAL('initResourceToNpcAttack err!count1:%d count2:%d',count($avilabpirateids),count($resids) );
			throw new Exception('config');
		}
		$pirateids=Util::noBackSample($avilabpirateids, count($newresids));
		
		//更新,并设置成npc占领
		$index=0;$newinfo=array();	
		foreach ($newresids as $resid)
		{
			$ary= array(
					NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID=>$page_id,
					NPCResourceDef::NPC_RESOURCE_SQL_RES_ID => $resid,
					NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID =>$pirateids[$index],
					NPCResourceDef::NPC_RESOURCE_SQL_UID=>NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID,//npc占领
					NPCResourceDef::NPC_RESOURCE_SQL_ARMY_ID=>0,
					NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME =>Util::getTime(),
					NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER =>0,
					NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER =>0,
					NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT =>0,
					NPCResourceDef::NPC_RESOURCE_SQL_PLUNDER_COUNT=>0);
			NpcResourceDao::insertIntoNpcResInfoTbl($ary);
			$newinfo[]=$ary;
			$index++;
		}
		//释放锁
		$locker->unlock(NpcReourceLogic::getResLockName($page_id, -1));
		return $newinfo;
	}
	/*
	 * 重置资源，并改为npc占领，参数$pirateid表示是否需要更新该资源的海贼团id
	 * 海贼团的id只有每天凌晨四点才会更新，或者首次将矿的信息插入数据库时更新
	 */
	public static function   resetResourceToNpcAttack($page_id,$resource_id,$pirateid=NULL)
	{
		//设置该矿为npc占领
		$uid=NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID;//uid为1代表是npc占领
		
		//timer(其实对npc占领来说没必要设置timer，又不会给npc发奖励，对前端来说只是一个表现而已)
		//$time=Util::getTime()+btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MAX_OCCUPY_TIME];
		//$timer= TimerTask::addTask(0, $time, 'NpcResource.dueResource',array($uid, $page_id, $resid,0));
		$set=array(	NPCResourceDef::NPC_RESOURCE_SQL_UID =>$uid,
					NPCResourceDef::NPC_RESOURCE_SQL_ARMY_ID =>0,
					NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME =>Util::getTime(),//这个时间是唯一的，update的内容肯定和数据库内的不同
					NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER =>0,
					NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER =>0,
					NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT =>0,
					NPCResourceDef::NPC_RESOURCE_SQL_PLUNDER_COUNT =>0,);
		
		//是否需要更新海贼团id
		if ($pirateid != NULL)
		{
			$set[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID]=intval($pirateid);
		}
		$wheres  =array(array (NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID, '=',intval($page_id)),
				array (NPCResourceDef::NPC_RESOURCE_SQL_RES_ID, '=',intval($resource_id)));
		NpcResourceDao::updateNpcResInfoTbl($set, $wheres);
	}
	

	/*
	 * 玩家占矿后如果发生异常，则在该函数内修复
	 * 注意，对于npc占领，对客户端来说只是一个表现而已，到期就到期，不需要做处理的
	*/
	public static function   fixUnExceptionResInfo($page_id)
	{
		//异常是指玩家占了矿，但到了过期时间却没过期（如timer挂掉）
	
		//当前时间减去最大占领时间则为矿允许的占领时间，如果有比这个时间小的矿还被占着，那肯定就是异常了
		//（考虑到timer的异步执行，这里多减十分钟）,这样的话，即使出现了异常情况，也会被后面玩家在这个函数里修复掉
		$avilaboccupytime=Util::getTime()-btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MAX_OCCUPY_TIME]-60*10;
		$wheres  =array(array (NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME, 'BETWEEN',array(1, $avilaboccupytime)),
				array (NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID, '=', $page_id),
				array (NPCResourceDef::NPC_RESOURCE_SQL_UID, '>',NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID),//这个条件表明是玩家占领
				);
		$selectfield =  array(  NPCResourceDef::NPC_RESOURCE_SQL_RES_ID,
				NPCResourceDef::NPC_RESOURCE_SQL_UID,
				NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME,
				NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT,
				NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER,
				NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER);
		$ret= NpcResourceDao::getFromNpcResInfoTbl($selectfield, $wheres);
	
		foreach ($ret as $val)
		{
			$resid		=$val[NPCResourceDef::NPC_RESOURCE_SQL_RES_ID];
			$uid		=$val[NPCResourceDef::NPC_RESOURCE_SQL_UID];
			$occupytime	=$val[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME];
			$npccount   =$val[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT];
			$duetimer	=$val[NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER];
			$nextimer	=$val[NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER];
			
			//获得锁
			$locker = new Locker();
			$locker->lock(NpcReourceLogic::getResLockName($page_id, $resid));
				
			//将所有timer取消掉
			if ($duetimer > 0)
			{
				TimerTask::cancelTask($duetimer);
			}
			if ($nextimer > 0)
			{
				TimerTask::cancelTask($nextimer);
			}
				
			//更新资源矿,并将矿设置为npc占领
			self::resetResourceToNpcAttack($page_id, $resid,NULL);
			
			//给玩家发奖励
			if ($uid > NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)
			{
				$belly=self::giveIncomeToUser($uid, $page_id, $resid, $npccount);
				
				//给玩家发邮件
				MailTemplate::sendNewWorldResourceExpire($uid,$belly);
			}
			
			//释放锁
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resid));
			
			//记一个日志
			Logger::info('fixUnExceptionResInfo! uid:%d pageid:%d resid:%d occupytime:%d duetimer:%d nexttimer:%d npccount:%d',
			$uid,$page_id,$resid,$occupytime,$duetimer,$nextimer,$npccount);
		}
	}
	
	/*
	 * 拉取资源矿数据返回给前端
	 */
	public static function   getCurPageResInfo($page_id)
	{
		//返回页id 资源id 占领的uid（如果是-1则为npc占领）、占领时间 、到期时间、npc攻占次数
		$wheres  =array(array (NPCResourceDef::NPC_RESOURCE_SQL_RES_ID, '>', 0),
						array (NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID, '>', 0),
						array (NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID, '=', $page_id));
		$selectfield = array(  NPCResourceDef::NPC_RESOURCE_SQL_RES_ID,
							   NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID,				
							   NPCResourceDef::NPC_RESOURCE_SQL_UID,
							   NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID,
							   NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME,
							   NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT,);
		$ret= NpcResourceDao::getFromNpcResInfoTbl($selectfield, $wheres);
		$return=array();
		foreach ($ret as $val)
		{
			//过期时间为占领时间加最大时间
			$tmpary=$val;
			unset($tmpary[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME]);
			$time=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MAX_OCCUPY_TIME];
			$tmpary[NPCResourceDef::NPC_RESOURCE_RET_DUE_TIME]=intval($val[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME])+$time;
			
			//如果是玩家，则有保护时间，npc没有保护时间
			$tmpary[NPCResourceDef::NPC_RESOURCE_RET_PROTECT_TIME]=0;
			$uid=$val[NPCResourceDef::NPC_RESOURCE_SQL_UID];
			if ($uid > NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)
			{
				$protecttime=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_OCCUPY_PROTECT_TIME];
				$occupytime=intval($val[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME]);
			    $tmpary[NPCResourceDef::NPC_RESOURCE_RET_PROTECT_TIME]=$occupytime+$protecttime;
			}
			$return[]=$tmpary;
		}
		return $return;
	}
	
	/*
	 * 生成前端需要的页内资源信息
	 */
	public static function buildRetPageResInfo($newinfo)
	{
		$ret=array();$return=array();
		foreach ($newinfo as $val)
		{
			unset($val[NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID]);
			unset($val[NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER]);
			unset($val[NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER]);
			unset($val[NPCResourceDef::NPC_RESOURCE_SQL_PLUNDER_COUNT]);
			$ret[]=$val;
		}
		
		//名字、等级、公会等信息
		foreach ($ret as $val)
		{
			$uid=$val[NPCResourceDef::NPC_RESOURCE_SQL_UID];
			$val['level'] = 0;
			$val['serverarmylevel'] = NpcReourceLogic::getServerArmyLevel();
			$val['uname']	='';
			$val['group_id'] =0;
			$val['guild_id'] =0;
			$val['guild_emblem'] = 0;
			$val['guild_name'] = '';
				
			if ($uid > NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)
			{
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
				$val['level'] = intval($user->getLevel());
				$val['uname']	=$user->getUname();
				$val['group_id'] =$user->getGroupId();
				$val['guild_id'] =$guildId;
				$val['guild_emblem'] = $guildEmblem;
				$val['guild_name'] = $guildName;
			}
			$return[]=$val;
		}
		return $return;
	}
	
	
	/*
	 * 将收益发给玩家
	 */
	public static function giveIncomeToUser($uid,$page_id,$resid,$npccount)
	{
		$userObj = EnUser::getUserObj($uid);
		$belly=self::getDefendIncome($page_id, $resid, $npccount, $userObj->getLevel());
		$userObj->addBelly($belly);
		$userObj->update();
		return array('belly'=>$belly);
	}	
	
	/*
	 * 获得防守收益
	 * 防守最终收益=(25+（25+500*最终成功防守怪物等级）^0.5-2.5)*玩家等级*（100+资源矿收益系数）
	 * 其中500为收益系数1 ， 100为收益系数2
	 */
	public static function getDefendIncome($pageid,$resid,$npccount,$uesrlevel)
	{
		//收益系数
		$rate1=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_INCOME_RATE_1];
		$rate2=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_INCOME_RATE_2];
		
		$rate=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_RES_ID_ATTR_ARY][$pageid][$resid];
		$defendlevel=self::getFinalDefendMonsterLevel($npccount);
		$income=(25+ (sqrt(25+ $rate1* $defendlevel) -2.5))* $uesrlevel* ($rate2+$rate) ;
		return intval($income);
	}
	/*
	 * 获得掠夺收益
	 * 掠夺资源矿收益=(25+（25+500*服务器部队等级）^0.5-2.5)*玩家等级*（100+资源矿收益系数）
	 * 其中500为收益系数1 ， 100为收益系数2
	 */
	public static function getPlunderIncome($pageid,$resid,$uesrlevel)
	{
		$armylevel=self::getServerArmyLevel();
		
		//收益系数
		$rate1=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_INCOME_RATE_1];
		$rate2=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_INCOME_RATE_2];
		
		$rate=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_RES_ID_ATTR_ARY][$pageid][$resid];
		$income=(25+ (sqrt(25+ $rate1* $armylevel) -2.5))* $uesrlevel* ($rate2+$rate) ;
		return intval($income);
	}

	/*
	 * 获得最终防守的怪物等级
	 * 没防守成功一次，则等级加1，所以和防守次数有关系
	 */
	public static function getFinalDefendMonsterLevel($npccount)
	{
		$armylevel=self::getServerArmyLevel();
		$armylevel +=$npccount;
		return $armylevel;
	}
	
	/*
	 * 获得服务器部队等级
	 * 服务器部队等级=max（   int（服务器等级/4-10）  ，1）
	 */
	public static function getServerArmyLevel()
	{
		$serverlevel=self::getServerLevel();
		$serverlevel=intval($serverlevel/4 - 10);
		return $serverlevel > 1 ?$serverlevel:1;
	}
	
	/*
	 * 获得服务器等级，服务器等级是服务器 max（等级排名前20的玩家的等级平均值，进入矿区最低等级）
	 * 在上面的resetResourceEveryDay里计算
	*/
	public static function getServerLevel()
	{
		if (self::$_serverLevel == NULL)
		{	
			$wheres  =array(array (NPCResourceDef::NPC_RESOURCE_SQL_SEVERLEVEL_SQ_ID,
						 '=', NPCResourceDef::NPC_RESOURCE_CONST_SQ_ID));
			$selectfield = array(  NPCResourceDef::NPC_RESOURCE_SQL_SEVERLEVEL_VALUE_1);
			$ret= NpcResourceDao::getServerLevel($selectfield, $wheres);
			if (!empty($ret))
			{
				self::$_serverLevel=$ret[0][NPCResourceDef::NPC_RESOURCE_SQL_SEVERLEVEL_VALUE_1];
			}
			else
			{
				Logger::FATAL('NpcReourceLogic.getServerLevel failed!');
				throw new Exception('fake');
			}
		}
		//说明需要初始化（这种情况只能是线上服刚刚更新完，但crontab还未执行）
		if ( self::$_serverLevel == 0)
		{
			self::resetServerLevelEveryDay();
		}
		return self::$_serverLevel;
	}
	
	/*
	 * 更新服务器等级
	*/
	public static function updateServerLevel($serverlevel)
	{
		$set=array(NPCResourceDef::NPC_RESOURCE_SQL_SEVERLEVEL_VALUE_1 =>$serverlevel,
				   NPCResourceDef::NPC_RESOURCE_SQL_SEVERLEVEL_VALUE_2=>Util::getTime());
		$wheres  =array(array (NPCResourceDef::NPC_RESOURCE_SQL_SEVERLEVEL_SQ_ID,
						 '=', NPCResourceDef::NPC_RESOURCE_CONST_SQ_ID));
		NpcResourceDao::updateServerLevel($set, $wheres);
	}
	
	/*
	 * 是不是在开启时间段内
	 */
	public static function checkOpenTime()
	{
		if ( !isset(btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_START_TIME]) ||
			 !isset(btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_END_TIME])	)
		{
			Logger::FATAL('excavate start time is null!');
			throw new Exception('config');
		}
		$curtime=Util::getTime();
		$starttime=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_START_TIME];
		$endtime=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_END_TIME];
		
		$begin_time= strtotime(strval($starttime));
		$end_time= strtotime(strval($endtime));
		
		if ( $curtime < $begin_time || $curtime > $end_time )
		{
			Logger::DEBUG('checkOpenTime time err! curtime:%d ',$curtime);
			return false;
		}
		return true;
	}
	
	/*
	 * 根据服务器等级获得对应的海贼团id组
	*/
	private static function   getPirateIdsByServerLevel()
	{
		$index=0;
		$level=self::getServerLevel();
		$levlimit=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_LEV_LIMIT];
		foreach ($levlimit as $val)
		{
			$min=$val[NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_LEV_MIN];
			$max=$val[NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_LEV_MAX];
			if ($level >$min && $level<=$max )
				break;
			$index++;
		}
	
		//获得对应的海贼团id数组
		return btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_PIRATE_ID_ARY][$index];
	}
	
	/*
	 * 锁
	 */
	public  static function getResLockName($page_id,$resource_id)
	{
		return NPCResourceDef::NPC_RESOURCE_LOCKER_PRE . $page_id .
		NPCResourceDef::NPC_RESOURCE_LOCKER_CONJ . $resource_id;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */