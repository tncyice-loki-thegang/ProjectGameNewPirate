<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyNpcResource.class.php 36995 2013-01-25 02:31:17Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/branches/pirate/rpcfw/newworldres/module/npcresource/MyNpcResource.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-25 10:31:17 +0800 (星期五, 25 一月 2013) $
 * @version $Revision: 36995 $
 * @brief 
 *  
 **/


/*
 * 这个类负责与某个具体的资源矿相关联的操作，这样的矿是与玩家相关联的,比如占领矿、放弃矿、掠夺矿
 */
class MyNpcResource
{
	private $m_uid;							//玩家uid 							
	private $page_id;						//页id
	private $resource_id;      	 			//资源ID
	private $resourceInfo;					//资源矿的数据
	private $resourceInfo_modify;			//修改后的资源矿的数据
	private $userinfo;						//玩家数据
	private $userinfo_modify;				//修改后的玩家数据
	
	public function MyNpcResource($uid=NULL,$page_id=NULL, $resource_id=NULL)
	{
		$this->m_uid 		= $uid;
		$this->page_id 		= $page_id;
		$this->resource_id 	= $resource_id;
		
		$this->resourceInfo=array();
		$this->userinfo=array();
		
		$this->resourceInfo_modify =array();
		$this->userinfo_modify =array();
		
		//获取玩家的信息，资源矿的信息在后面操作里获得锁之后再获取
		if ($this->m_uid!=NULL)
		{
			$this->userinfo= $this->getUserInfo($uid);
		}
	}
	/*
	 * 拉取后端的资源矿数据
	 */
	public function getResouceInfo($page_id, $resource_id)
	{
		$wheres  =array(array (	NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID, '=', $page_id),
				        array (	NPCResourceDef::NPC_RESOURCE_SQL_RES_ID, '=',  $resource_id));
		$selectfield = array  ( NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID,
								NPCResourceDef::NPC_RESOURCE_SQL_RES_ID,
								NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID,
								NPCResourceDef::NPC_RESOURCE_SQL_UID,
								NPCResourceDef::NPC_RESOURCE_SQL_ARMY_ID,
								NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME,
								NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER,
								NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER,
								NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT,
								NPCResourceDef::NPC_RESOURCE_SQL_PLUNDER_COUNT,);
		$ret= NpcResourceDao::getFromNpcResInfoTbl($selectfield, $wheres);
		if (empty($ret))
		{
			Logger::warning('getResouceInfo res info emtpy!');
			throw new Exception('fake');
		}
		
		return $ret[0];
	}
	
	/*
	 * 拉取这个玩家的数据
	 */
	public function getUserInfo($uid)
	{
		$return=array();
		$wheres  =array(array (	NPCResourceDef::NPC_RESOURCE_SQL_UID, '=', $uid));
		$selectfield = array  ( NPCResourceDef::NPC_RESOURCE_SQL_UID,
								NPCResourceDef::NPC_RESOURCE_SQL_CAN_OCCUPY_COUNT,
								NPCResourceDef::NPC_RESOURCE_SQL_LAST_OCCUPY_TIME,
								NPCResourceDef::NPC_RESOURCE_SQL_CAN_PLUNDER_COUNT,
								NPCResourceDef::NPC_RESOURCE_SQL_LAST_PLUNDER_TIME,
								NPCResourceDef::NPC_RESOURCE_SQL_PLUNDER_COOL_TIME,
								NPCResourceDef::NPC_RESOURCE_SQL_MANUAL_COOL_TIME,
								NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME,
								NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO,);
		$ret=  NpcResourceDao::getFromNpcResUserTbl($selectfield, $wheres);
		
		//如果为空，则初始化玩家数据
		if (empty($ret))
		{
			$occupycount=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_OCCUPY_COUNT];
			$plundercount=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_PLUNDER_COUNT];
			$ary= array(
					NPCResourceDef::NPC_RESOURCE_SQL_UID=>$uid,
					NPCResourceDef::NPC_RESOURCE_SQL_CAN_OCCUPY_COUNT=>$occupycount,
					NPCResourceDef::NPC_RESOURCE_SQL_LAST_OCCUPY_TIME =>Util::getTime(),//需要加上时间，不然以后策划说要累计次数的时候就没法弄
					NPCResourceDef::NPC_RESOURCE_SQL_CAN_PLUNDER_COUNT =>$plundercount,
					NPCResourceDef::NPC_RESOURCE_SQL_LAST_PLUNDER_TIME =>Util::getTime(),//需要加上时间，不然以后策划说要累计次数的时候就没法弄
					NPCResourceDef::NPC_RESOURCE_SQL_PLUNDER_COOL_TIME =>0,
					NPCResourceDef::NPC_RESOURCE_SQL_MANUAL_COOL_TIME=>0,
					NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME=>0,
					NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO=>array());
			if(!NpcResourceDao::insertIntoNpcResUserTbl($ary))
			{
				Logger::warning('getResouceInfo user info emtpy! insert fail!');
				throw new Exception('fake');
			}
			$return= $ary;
		}
		else 
		{
			$return=$ret[0];
		}
		
		//每天凌晨四点，清理战报
		$set=array();
		$time=$return[NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME];
		if (!Util::isSameDay($time))
		{
			$set[NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME]=Util::getTime();
			$set[NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO]=array();
			
			$return[NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME]=Util::getTime();
			$return[NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO]=array();
		}
		//每天凌晨四点，重置可占领次数
		$occupytime=$return[NPCResourceDef::NPC_RESOURCE_SQL_LAST_OCCUPY_TIME];
		if (!Util::isSameDay($occupytime))
		{
			$occupycount=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_OCCUPY_COUNT];
			$set[NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME]=Util::getTime();
			$set[NPCResourceDef::NPC_RESOURCE_SQL_CAN_OCCUPY_COUNT]=$occupycount;
			
			$return[NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME]=Util::getTime();
			$return[NPCResourceDef::NPC_RESOURCE_SQL_CAN_OCCUPY_COUNT]=$occupycount;
		}
		//每天凌晨四点，重置可掠夺次数
		$plundertime=$return[NPCResourceDef::NPC_RESOURCE_SQL_LAST_PLUNDER_TIME];
		if (!Util::isSameDay($plundertime))
		{
			$plundercount=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_PLUNDER_COUNT];
			$set[NPCResourceDef::NPC_RESOURCE_SQL_LAST_PLUNDER_TIME]=Util::getTime();
			$set[NPCResourceDef::NPC_RESOURCE_SQL_CAN_PLUNDER_COUNT]=$plundercount;
			
			$return[NPCResourceDef::NPC_RESOURCE_SQL_LAST_PLUNDER_TIME]=Util::getTime();
			$return[NPCResourceDef::NPC_RESOURCE_SQL_CAN_PLUNDER_COUNT]=$plundercount;
		}
	    if (!empty($set))
	    {
	    	$wheres  =array(array (NPCResourceDef::NPC_RESOURCE_SQL_UID, '=', $uid),);
	    	NpcResourceDao::updateNpcResUserTbl($set, $wheres);
	    }
		
		return $return;
	}
	
	/*
	 * 玩家占领资源矿，供前端调用
	 */
	public function attackResourceByUser()
	{
		//玩家uid，对应资源矿的页id，资源id
		$uid=$this->m_uid ;
		$page_id=$this->page_id;
		$resource_id=$this->resource_id;
		
		//默认返回值
		$return =array(NPCResourceDef::NPC_RESOURCE_ERROR_NAME=>
				       NPCResourceDef::NPC_RESOURCE_ERROR_UNKNOWN);
		
		//是不是在矿区开启时间内
		if (!NpcReourceLogic::checkOpenTime())
		{
			Logger::fatal('attackResourceByUser err open time!');
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_OPEN_TIME;
			return $return;
		}
		
		//获得锁
		$locker = new Locker();
		$locker->lock(NpcReourceLogic::getResLockName($page_id, $resource_id));
		
		//拉取数据库里该矿的信息
		$this->resourceInfo= $this->getResouceInfo($this->page_id, $this->resource_id);
		
		//是不是在保护时间
		$curtime=Util::getTime();
		$time=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_OCCUPY_PROTECT_TIME];
		$protecttime=$time+$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME];
		$occupyuid=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_UID];
		if ($occupyuid >NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID && $curtime < $protecttime)
		{
			Logger::fatal('attackResourceByUser!in protect time!');
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_PROTECT_TIME;
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			return $return;
		}
		
		 //目前只能允许玩家同时占一个矿
		$valinfo=$this->_getUserOccupyRes();
		if (count($valinfo) > NPCResourceDef::NPC_RESOURCE_ALLOW_OCCUPY)
		{
			Logger::fatal('attackResourceByNpc err!already occupied one res');
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_OVER_OCCUPY_COUNT;
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			return $return;
		}
		
		//占领次数够不够
		$occupycount=$this->userinfo[NPCResourceDef::NPC_RESOURCE_SQL_CAN_OCCUPY_COUNT];
		if ($occupycount <=0)
		{
			Logger::fatal('attackResourceByUser err occupycount:%d',$occupycount);
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_OCCUPY_COUNT;
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			return $return;
		}
		
		//玩家等级够不够
		$user = EnUser::getUserObj($uid);
		$userlevel=$user->getMasterHeroLevel();
		$limitminlevel=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_ENTER_MIN_LEVEL];
		if ($userlevel < $limitminlevel)
		{
			Logger::fatal('attackResourceByUser userlevel err level:%d needlevel:%d',$userlevel,$limitminlevel);
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_USER_LERVEL;
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			return $return;
		}
		
		//自己已经占了该矿且该矿没到期,就不要再占了
		$occupytime=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME];
		if ($occupyuid==$this->m_uid  )
		{
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_SAME_USER_OCCUPY;
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			return $return;
		}
		
		//检查战斗CD是否在冷却
		if ($user->addFightCDTime(PortConfig::PORT_RESOURCE_FIGHT_CDTIME) == FALSE)
		{
			Logger::fatal('attackResourceByUser !in fight cd!');
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_BATTLE_CD;
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			return $return;
		}
	
		//调用战斗
		$ret=$this->_doAttackResBattle();
		$atkRet=$ret['atk'];
		//占领矿的时候每个玩家要随机一个部队id，且后面npc进攻也都是用这个部队id，所以需要将这个值返回并保存
		$newarmyid = $ret['armyid'];
		
		//战斗是否成功
		$isSuccess = BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'];
		
		//无论成功失败都需要更新玩家数据（至少要更新战报）
		$this->userinfo_modify=$this->userinfo;
		
		//成功与否都要存战报，战报信息，页id 资源id 海贼团id 战报类型 目标uid 成功与否 战报id 时间  贝里收益，总共防守了多少波进攻
		$belly=0;$npccount=0;//刚占领成功，收益得等下一波进攻有了结果才能知道
		$brid=$atkRet['server']['brid'];
		$occupy_uid=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_UID];
		$battleytpe= ($occupy_uid==NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)?  //战报类型，是占领npc还是占领玩家
		NPCResourceDef::NPC_RESOURCE_BATTLE_TYPE_ATTACKNPC:NPCResourceDef::NPC_RESOURCE_BATTLE_TYPE_ATTACKUSER;
		$pirateid =$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
		$battleinfo=array('page_id'=>$page_id,'res_id'=>$resource_id,'pirate_id'=>$pirateid,
				          'type'=>$battleytpe,'target_uid'=>$occupy_uid,'isSuccess'=>$isSuccess,
				          'brid'=>$brid,'time'=>intval(Util::getTime()),'belly'=>$belly,'npccount'=>$npccount);
		//插到战报队列的头
		array_unshift($this->userinfo_modify[ NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO],$battleinfo);
		$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME]=Util::getTime();
		
		//战报是不是超过100条了，最多保留100条
		$curbattlinfo=$this->userinfo_modify[ NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO];
		$this->_processOverFlowBattleInfo($curbattlinfo);
		
		//如果成功
		if ($isSuccess)
		{
			//修改玩家数据，占领次数减1
			$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_CAN_OCCUPY_COUNT] -=1;
			$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_LAST_OCCUPY_TIME] = Util::getTime();
			
			//需要更新资源矿的数据
			$this->resourceInfo_modify=$this->resourceInfo;
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_UID]=$this->m_uid;
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME]=Util::getTime();
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_ARMY_ID]=$newarmyid;
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT]=0;
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_PLUNDER_COUNT]=0;
			
			//下一轮npc进攻的timer
			$npctime=Util::getTime()+btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_ATTACK_INTERVAL];
			$npctimer=TimerTask::addTask($this->m_uid, $npctime, 'npcresource.attackResourceByNpc',array($this->m_uid, $page_id, $resource_id));
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER]=$npctimer;
			//资源矿到期的timer
			$duetime=Util::getTime()+btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MAX_OCCUPY_TIME];
			$duetimer=TimerTask::addTask($this->m_uid, $duetime, 'npcresource.dueNpcResource',array($this->m_uid, $page_id, $resource_id));
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER]=$duetimer;
				
			//更新资源矿信息
			$this->updatResInfoToDb();
			
			//释放锁
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			
			//如果原占领者是玩家，则给该玩家结算收益，并发送邮件
			if ($occupy_uid > NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)
			{
				//给原玩家奖励
				$occupy_user = EnUser::getUserObj($occupy_uid);
				$uerlevel= $occupy_user->getMasterHeroLevel();
				$npccount=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT];
				$belly=NpcReourceLogic::getDefendIncome($page_id, $resource_id, $npccount, $uerlevel);
				$occupy_user->addBelly($belly);
				$occupy_user->update();
			
				//给原玩家发邮件
				$pirateId=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
				$param=array($occupy_user->getTemplateUserInfo(),Util::getTime(),$belly,$brid);
				MailTemplate::sendNewWorldResourceRobSuccess($occupy_uid, $param);
			}
			
			//把下一波信息推送给前端
			$this->_sendNpcAttackInfo($npctime,$pirateid,$npccount);
			
			//广播资源更新
			$this->_broadcastResInfo();
		}
		else 
		{
			//释放锁
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
		}
		
		//更新占领者的玩家数据（上面的战报信息、占领次数等）
		$this->updatUserInfoToDb();
		
		//给占领者和被占领者(如果被占领者是玩家)推送战报信息
		$newbattleinfo=$this->_buildBattleInfo(array($battleinfo));
		$this->_sendBattleInfo($this->m_uid, $occupy_uid, $newbattleinfo[0]);
		
		//给前端返回信息，包括战斗结果
		$duetime=Util::getTime()+btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MAX_OCCUPY_TIME];
		$protecttime=Util::getTime()+btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_OCCUPY_PROTECT_TIME];
		return array(
				'ret_code'=>NPCResourceDef::NPC_RESOURCE_ERROR_OK,
				'brid'=>$atkRet['server']['brid'],
				'attack_success'=>$isSuccess,
				'fight_ret' => $atkRet['client'],
				'fight_cdtime' => $user->getFightCDTime(),
				'appraisal' => $atkRet['server']['appraisal'],
				NPCResourceDef::NPC_RESOURCE_RET_DUE_TIME=>$duetime,
				NPCResourceDef::NPC_RESOURCE_RET_PROTECT_TIME=>$protecttime,
		);
	}
	
	/*
	 * npc进攻资源矿，由timer执行,由timer执行时，参数$cancelTimer应该为false
	*/
	public function attackResourceByNpc($cancelTimer=false)
	{
		//Logger::fatal('do attackResourceByNpc start');
		//玩家uid，对应资源矿的页id，资源id
		$uid=$this->m_uid ;
		$page_id=$this->page_id;
		$resource_id=$this->resource_id;
		
		//默认返回值
		$return =array(NPCResourceDef::NPC_RESOURCE_ERROR_NAME=>
				NPCResourceDef::NPC_RESOURCE_ERROR_UNKNOWN);
		
		//获得锁
		$locker = new Locker();
		$locker->lock(NpcReourceLogic::getResLockName($page_id, $resource_id));
		
		//拉取数据库里该矿的信息
		$this->resourceInfo= $this->getResouceInfo($this->page_id, $this->resource_id);
		
		//这个矿是不是被这个玩家占着
		$curuid=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_UID];
		if($curuid!=$uid )
		{
			Logger::fatal('attackResourceByNpc err!curuid:%d uid:%d',$curuid,$uid);
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			return $return;
		}
		
		//是不是已经过期了,如果过期了不处理，统一在NPCResourceLogic::fixUnExceptionResInfo里处理
		$occupyuid=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_UID];
		$occupytime=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME];
		$duetime=$occupytime+btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MAX_OCCUPY_TIME];
		if (Util::getTime()> $duetime )
		{
			Logger::fatal('attackResourceByNpc res timeout!uid:%d pageid:%d resid:%d',$uid,$page_id,$resource_id);
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			return $return;
		}
		
		//是不是需要去掉timer,玩家手动执行的时候就需要取消掉timer
		$timer=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER];
		if($cancelTimer==TRUE && $timer > 0)
		{
			TimerTask::cancelTask($timer);
		}

		//该玩家和npc战斗
		$atkRet=$this->_doNpcAttackBattle();
		$isSuccess = BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'];
		//npc是进攻方，我是防守方，对方输了才能算我赢.
		$isSuccess=(!$isSuccess);
		$brid=$atkRet['server']['brid'];
		
		//先计算下当前的收益，存战报用
		$npccount=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT];
		$belly=NpcReourceLogic::getFinalDefendMonsterLevel($npccount);
		
		//战报信息
		$this->userinfo_modify=$this->userinfo;
		$battletype= $isSuccess?NPCResourceDef::NPC_RESOURCE_BATTLE_TYPE_DEFENDNPC_OK: //战报类型，防守成功还是失败
								NPCResourceDef::NPC_RESOURCE_BATTLE_TYPE_DEFENDNPC_FAIL;
		//战报信息，页id 资源id 海贼团id 战报类型 目标uid 成功与否 战报id 时间,beilly收益，总共防守了多少波进攻
		$pirateid =$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
		$battleinfo=array('page_id'=>$page_id,'res_id'=>$resource_id,'pirate_id'=>$pirateid,
				'type'=>$battletype,'target_uid'=>NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID,
				'isSuccess'=>$isSuccess,'brid'=>$brid,'time'=>intval(Util::getTime()),'belly'=>$belly,'npccount'=>$npccount);
		//插入战报队列
		array_unshift($this->userinfo_modify[ NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO],$battleinfo);
		//战报是不是超过100条了，最多保留100条
		$curbattlinfo=$this->userinfo_modify[ NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO];
		$this->_processOverFlowBattleInfo($curbattlinfo);
		$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME]=Util::getTime();
		
		//如果成功则防守次数加1，且要设置下一波npc进攻的timer
		if ($isSuccess)
		{
			$this->resourceInfo_modify=$this->resourceInfo;
			
			//防守次数加1
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT]+=1;
			
			//下一轮npc进攻的timer
			$npctime=Util::getTime()+btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_ATTACK_INTERVAL];
			$npctimer=TimerTask::addTask(0, $npctime, 'npcresource.attackResourceByNpc',array($uid, $page_id, $resource_id));
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER]=$npctimer;
			//更新资源矿数据，新的npc进攻timer
			$this->updatResInfoToDb();
			
			//释放锁
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
					
			//把下一波信息推送给前端
			$this->_sendNpcAttackInfo($npctime,$pirateid,$npccount+1);
			Logger::debug('attackResourceByNpc success!next! npccount:%d',$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT]);
		}
		else
		{
			
			//如果失败,给玩家收益
			$user = EnUser::getUserObj($uid);
			$user->addBelly($belly);
			$user->update();
			
			//发邮件通知玩家
			$pirateId=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
			$param=array($pirateId,Util::getTime(),$belly,$brid);
			MailTemplate::sendNewWorldResourceDefFail($uid, $param);
			
			//重置为npc占领
			NpcReourceLogic::resetResourceToNpcAttack($page_id,$resource_id);
			
			//释放锁
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			
			$this->resourceInfo_modify =$this->resourceInfo;
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_UID]=NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID;
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME]=Util::getTime();
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT]=0;
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER]=0;
			$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER]=0;
			//广播资源更新
			$this->_broadcastResInfo();
			
			Logger::debug('attackResourceByNpc fail!npcount:%d',$npccount);
		}
		
		//更新玩家数据（战报）
		$this->updatUserInfoToDb();
		
		//推送战报信息给前端
		$newbattleinfo=$this->_buildBattleInfo(array($battleinfo));
		$this->_sendBattleInfo(NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID, $this->m_uid,$newbattleinfo[0]);
		
		$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_OK;
		return $return;
	}
	/*
	 * 玩家手动点击按钮，立即执行npc进攻
	 */
	public function doNpcAttackNow()
	{
		//玩家uid，对应资源矿的页id，资源id
		$uid=$this->m_uid ;
		$page_id=$this->page_id;
		$resource_id=$this->resource_id;
		
		//默认返回值
		$return =array(NPCResourceDef::NPC_RESOURCE_ERROR_NAME=>
				NPCResourceDef::NPC_RESOURCE_ERROR_UNKNOWN);
		
		//是否在开启时间内
		if (!NpcReourceLogic::checkOpenTime())
		{
			Logger::fatal('doNpcAttackNow err open time!');
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_OPEN_TIME;
			return ;
		}
		
		//是不是在冷却cd
		$curtime=Util::getTime();
		$cdtime=$this->userinfo[NPCResourceDef::NPC_RESOURCE_SQL_MANUAL_COOL_TIME];
		if($curtime <= $cdtime)
		{
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_MANUAL_CD;
			return;
		}
		
		$user = EnUser::getUserObj($uid);
		//金币够不够
		$costgold=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MANUAL_COST]['gold'];
		if (!$user->subGold($costgold))
		{
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_GOLD;
			return;
		}
		
		//贝里够不够
		$level= $user->getMasterHeroLevel();
		$costbelly=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MANUAL_COST]['belly'];
		if (!$user->subBelly($costbelly*$level))
		{
			$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_BELLY;
			return;
		}
		
		//立即调用npc进攻,并将原来的timer取消掉
		$ret=$this->attackResourceByNpc(TRUE);
		
		//若调用成功，则更新冷却cd
		$newcdtime=$this->userinfo[NPCResourceDef::NPC_RESOURCE_SQL_MANUAL_COOL_TIME];
		$isok=$ret[NPCResourceDef::NPC_RESOURCE_ERROR_NAME];
		if ($isok==NPCResourceDef::NPC_RESOURCE_ERROR_OK) 
		{
			//扣除金币或贝里
            $user->update();
            
            //如果扣了金币，则加统计日志
            if ($costgold > 0)
            {
            	Statistics::gold(StatisticsDef::ST_FUNCKEY_NPC_RESOURCE_NPC_ATTACK, $costgold,  Util::getTime());
            }

            //cd更新
            $cd=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MANUAL_CD];
            $newcdtime=Util::getTime()+$cd;
            $this->userinfo_modify=$this->userinfo;
            $this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_MANUAL_COOL_TIME]=$newcdtime;
            $this->updatUserInfoToDb();
		}
		
		$return['manual_cd']=$newcdtime;
		$return[NPCResourceDef::NPC_RESOURCE_ERROR_NAME]=NPCResourceDef::NPC_RESOURCE_ERROR_OK;
		if ($costgold > 0)
		{
			$return['cost']=$costgold;
		}
		elseif ($costbelly > 0)
		{
			$return['cost']=$costbelly*$level;
		}
		
		//返回给前端
		return $return;
	}
	/*
	 * 掠夺资源矿
	*/
	public  function plunderResource()
	{
		//玩家uid，对应资源矿的页id，资源id
		$uid=$this->m_uid ;
		$page_id=$this->page_id;
		$resource_id=$this->resource_id;
		
		$return =array('success'=>false,'belly'=>0,'brid'=>0);
	
		//是不是在矿区开启时间内
		if (!NpcReourceLogic::checkOpenTime())
		{
			return $return;
		}
		
		$curtime=Util::getTime();
		$retuser= $this->userinfo;
		if (empty($retuser))
		{
			Logger::warning('checkplunderCondition user info empty!');
			throw new Exception('fake');
		}
	
		//掠夺次数还够不够
		if ($retuser[NPCResourceDef::NPC_RESOURCE_SQL_CAN_PLUNDER_COUNT] <= 0)
		{
			Logger::DEBUG('plunderResource plunder count err!');
			return $return;
		}
	
		//玩家是不是还在掠夺失败cd
		$cdtime=$retuser[NPCResourceDef::NPC_RESOURCE_SQL_PLUNDER_COOL_TIME];
		if ($curtime < $cdtime)
		{
			Logger::DEBUG('plunderResource cd err!curtime:%d cd:%d',$curtime,$cdtime);
			return  $return;
		}
		
		$user = EnUser::getUserObj($uid);
		//检查战斗CD是否在冷却
		if ($user->addFightCDTime(PortConfig::PORT_RESOURCE_FIGHT_CDTIME) == FALSE)
		{
			Logger::DEBUG('plunderResource !in fight cd!');
			return $return;
		}

		//拉取数据库里该矿的信息
		$this->resourceInfo= $this->getResouceInfo($this->page_id, $this->resource_id);
		
		
		//该矿是不是被npc占领，策划不让掠夺玩家只能掠夺npc
		$retinfo=$this->resourceInfo;
		if (empty($retinfo))
		{
			Logger::fatal('plunderResource res info emtpy!');
			throw new Exception('fake');
		}
		if ($retinfo[NPCResourceDef::NPC_RESOURCE_SQL_UID] != NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)
		{
			Logger::DEBUG('plunderResource isnot npc attack!pageid:%d resid:%d',$page_id,$resource_id);
			return $return;
		}
	
		//当前的掠夺次数
		$curplundercount=$retuser[NPCResourceDef::NPC_RESOURCE_SQL_CAN_PLUNDER_COUNT];
	
		//调用战斗
		$atkRet=$this->_doPlunerBattle();
		// 是否成功
		$isSuccess = BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'];
	    
		//计算收益
		$uerlevel= $user->getMasterHeroLevel();
		$belly=NpcReourceLogic::getPlunderIncome($page_id, $resource_id,$uerlevel);
		$belly= ($isSuccess) ? intval($belly):intval($belly/2);//如果掠夺失败则收益减半
		
		//准备更新玩家信息
		$this->userinfo_modify=$this->userinfo;
		
		//战报信息，页id 资源id 海贼团id  战报类型 目标uid 成功与否 战报id 时间,beilly收益，总共防守了多少波进攻
		$brid=$atkRet['server']['brid'];
		$pirateid =$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
		$battletype=NPCResourceDef::NPC_RESOURCE_BATTLE_TYPE_PLUNDERNPC;
		$battleinfo=array('page_id'=>$page_id,'res_id'=>$resource_id,'pirate_id'=>$pirateid,
				'type'=>$battletype,'target_uid'=>NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID,
				'isSuccess'=>$isSuccess,'brid'=>$brid,'time'=>intval(Util::getTime()),'belly'=>$belly,'npccount'=>0);//只有玩家才有防守次数，npc是没有的，所以最后一个参数是0
		//插到战报队列的头
		array_unshift($this->userinfo_modify[ NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO],$battleinfo);

		//如果成功，则更新掠夺次数
		if ($isSuccess)
		{
			$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_CAN_PLUNDER_COUNT]=(--$curplundercount);
			$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_LAST_PLUNDER_TIME]=Util::getTime();
		}
		//如果失败则更新掠夺失败cd
		else
		{
			$cdtime=Util::getTime()+btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_PLUNDER_FAIL_CD];
			$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_PLUNDER_COOL_TIME]=$cdtime;
		}
		
		//战报是不是超过100条了
		$curbattlinfo=$this->userinfo_modify[ NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO];
		$this->_processOverFlowBattleInfo($curbattlinfo);
	
		//有没有更新成功,更新成功了才发奖励，不然玩家可能会刷的
		if(!$this->updatUserInfoToDb())
		{
			Logger::fatal('plunderResource update err! pageid:%d resid:%d',$page_id,$resource_id);
			return $return;
		}
		
		//给玩家奖励
		$user->addBelly($belly);
		$user->update();
		
		//给玩家发邮件
		$pirateId=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
		$param=array($pirateId,Util::getTime(),$belly,$brid);
		if ($isSuccess)
		{
			MailTemplate::sendNewWorldResourceAckNpcSuccess($this->m_uid,$param);
		}
		else
		{
			MailTemplate::sendNewWorldResourceAckNpcFail($this->m_uid,$param);
		}

		//返回给前端
		$return['success']=$isSuccess;
		$return['plunder_count']=$curplundercount;
		$return['belly']=$belly;
		$return['brid']=$brid;
		$return['client'] = $atkRet['client'];
		$return['appraisal'] = $atkRet['server']['appraisal'];
		$return['plunder_cool_time']=$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_PLUNDER_COOL_TIME];
		
	
		//推送战报信息
		$newbattleinfo=$this->_buildBattleInfo(array($battleinfo));
		$this->_sendBattleInfo( $this->m_uid,NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID,$newbattleinfo[0]);
		
		return $return;
	
	}
	/*
	 * 玩家数据被修改，在这里更新到数据库
	 */
	public  function updatUserInfoToDb()
	{
		if (empty($this->userinfo_modify))
		{
			return;
		}
		$set=$this->userinfo_modify;
		$wheres  =array(array (NPCResourceDef::NPC_RESOURCE_SQL_UID, '=', $this->m_uid),);
		return NpcResourceDao::updateNpcResUserTbl($set, $wheres);
	}
	
	/*
	 *资源矿数据被修改，在这里更新到数据库
	*/
	public  function updatResInfoToDb()
	{
		if (empty($this->resourceInfo_modify))
		{
			return;
		}
		$set=$this->resourceInfo_modify;
		$wheres  =array(array (	NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID, '=', $this->page_id),
				array (	NPCResourceDef::NPC_RESOURCE_SQL_RES_ID, '=',  $this->resource_id));
		return NpcResourceDao::updateNpcResInfoTbl($set, $wheres);
	}
	/*
	 * 放弃资源矿
	 */
	public  function givenupResource()
	{
		//玩家uid，对应资源矿的页id，资源id
		$uid=$this->m_uid ;
		$page_id=$this->page_id;
		$resource_id=$this->resource_id;
	
		$return=array('success'=>false,'belly'=>0);
		
		//获得锁
		$locker = new Locker();
		$locker->lock(NpcReourceLogic::getResLockName($page_id, $resource_id));
		
		//拉取数据库里该矿的信息
		$this->resourceInfo= $this->getResouceInfo($this->page_id, $this->resource_id);
		
		//我是不是占了这个矿
		$occupyuid=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_UID];
		if ($uid != $occupyuid)
		{
			Logger::fatal('givenupResource diff user! uid:%d occupyuid:%d',$uid,$occupyuid);
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			return $return;
		}
		
		//给玩家结算奖励
		$userObj = EnUser::getUserObj($uid);
		$npccount=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT];
		$belly=NpcReourceLogic::giveIncomeToUser($uid, $page_id, $resource_id, $npccount);
		
		//取消所有timer
		$duetimer	=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER];
		$nextimer	=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER];
		if ($duetimer > 0)
		{
			TimerTask::cancelTask($duetimer);
		}
		if ($nextimer > 0)
		{
			TimerTask::cancelTask($nextimer);
		}
		
		//重置该矿,改为npc占领
		NpcReourceLogic::resetResourceToNpcAttack($page_id,$resource_id);
		
		//释放锁
		$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
		
		$this->resourceInfo_modify =$this->resourceInfo;
		$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_UID]=NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID;
		$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME]=Util::getTime();
		$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT]=0;
		$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER]=0;
		$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER]=0;
		
		//广播资源更新
		$this->_broadcastResInfo();
		
		//给玩家发邮件
		MailTemplate::sendNewWorldResourceExpire($this->m_uid,$belly['belly']);
		
		$return['success']=true;
		$return['belly']=$belly['belly'];
		
		return $return;
	}
	
	/*
	 * 资源矿到期
	 */
	public function dueNpcResource()
	{
		//玩家uid，对应资源矿的页id，资源id
		$uid=$this->m_uid ;
		$page_id=$this->page_id;
		$resource_id=$this->resource_id;
		
		//获得锁
		$locker = new Locker();
		$locker->lock(NpcReourceLogic::getResLockName($page_id, $resource_id));
		
		//拉取数据库里该矿的信息
		$this->resourceInfo= $this->getResouceInfo($this->page_id, $this->resource_id);
	
		//我是不是占了这个矿
		$occupyuid=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_UID];
		if ($uid != $occupyuid)
		{
			Logger::fatal('dueNpcResource diff user! uid:%d occupyuid:%d',$uid,$occupyuid);
			$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
			return ;
		}
		
		$npccount=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT];
		
		//结算该玩家的收益,并发给玩家
		$belly=NpcReourceLogic::giveIncomeToUser($uid, $page_id, $resource_id, $npccount);
			
		//重置该矿,改为npc占领
		NpcReourceLogic::resetResourceToNpcAttack($page_id,$resource_id);
		
		//释放锁
		$locker->unlock(NpcReourceLogic::getResLockName($page_id, $resource_id));
		
		$this->resourceInfo_modify =$this->resourceInfo;
		$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_UID]=NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID;
		$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME]=Util::getTime();
		$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT]=0;
		$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_DUE_TEIMER]=0;
		$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_NEXT_NPC_TIMER]=0;
		//广播资源更新
		$this->_broadcastResInfo();
		
		//给玩家发邮件
		MailTemplate::sendNewWorldResourceExpire($uid,$belly);
	}
	
	/*
	 * 玩家自己的资源矿信息
	 */
	public function selfResourceInfo()
	{
		$uid=$this->m_uid;
		$userObj = EnUser::getUserObj($uid);
		//玩家占领的矿信息
		$wheres  =array(array (NPCResourceDef::NPC_RESOURCE_SQL_UID, '=', $uid));
		$selectfield = array(NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID,
				NPCResourceDef::NPC_RESOURCE_SQL_RES_ID,
				NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID,
				NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME,
				NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT,
		);
		$retres= NpcResourceDao::getFromNpcResInfoTbl($selectfield, $wheres);
		
		$resinfo=array();
		//过期时间和保护时间
		foreach ($retres as $val)
		{
			//过期时间
			$tmpary=$val;
			unset($tmpary[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME]);
			$time=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MAX_OCCUPY_TIME];
			$tmpary[NPCResourceDef::NPC_RESOURCE_RET_DUE_TIME]=intval($val[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME])+$time;
				
			//保护时间
			$protecttime=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_OCCUPY_PROTECT_TIME];
			$tmpary[NPCResourceDef::NPC_RESOURCE_RET_PROTECT_TIME]=intval($val[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME])+$protecttime;
			
			//下一波npc进攻时间
			$npcount=intval($val[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT]);
			$interval=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_ATTACK_INTERVAL];
			$tmpary['nextnpctime']=intval($val[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME])+($npcount+1)*$interval;
			$resinfo[]=$tmpary;
		}
		
		//获得玩家自己的可占领次数、可掠夺次数、掠夺失败cd、今天的战报
		$retuser=$this->userinfo;
		//$retuser['fight_cdtime'] =$userObj->getFightCDTime();
		
		unset($retuser[NPCResourceDef::NPC_RESOURCE_SQL_LAST_OCCUPY_TIME]);
		unset($retuser[NPCResourceDef::NPC_RESOURCE_SQL_LAST_PLUNDER_TIME]);
		unset($retuser[NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME]);
		
		//战报是不是超过100条了
		$curbattleinfo=$retuser[NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO];
		if (count($curbattleinfo)> NPCResourceDef::NPC_RESOURCE_MAX_BATTLE_COUNT)
		{
			$curbattleinfo=$this->_processOverFlowBattleInfo();
			$this->updatUserInfoToDb();
		}
		
		//生成前端的战报
		$battleinfo=$this->_buildBattleInfo($curbattleinfo);
		$retuser[NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO]=$battleinfo;
		
		//前端需要一个港口id
		$portBerth = new PortBerth();
		$retuser['port_id']= $portBerth->getPort();
		
		//玩家手动点击npc战斗的冷却时间
		$retuser['manual_cd']=$this->userinfo[NPCResourceDef::NPC_RESOURCE_SQL_MANUAL_COOL_TIME];
		
		return array('resinfo'=>$resinfo,'selfinfo'=>$retuser);
	}
	
	/*
	 * 占领资源矿时的战斗
	 */
	private function  _doAttackResBattle()
	{
		$uid=$this->m_uid;
		$page_id=$this->page_id;
		$resource_id=$this->resource_id;
		
		// 将阵型ID设置为用户当前默认阵型
		$user = EnUser::getUserObj($uid);
		$formationID = $user->getCurFormation();
		$user->prepareItem4CurFormation();
		$userFormation = EnFormation::getFormationInfo($uid);
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation,true);
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
		
		//服务器部队等级
		$armylevel=NpcReourceLogic::getServerArmyLevel();
		
		//当前的这个框是被NPC占领这还是被玩家占领着
		$occupy_uid=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_UID];
		if ($occupy_uid <= 0)
		{
			Logger::warning('attackResourceByUser uid ==0! pageid:%d resid:%d',$page_id,$resource_id);
			throw new Exception('fake');
		}
		//随机部队id
		$pirateid =$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
		$armyids=btstore_get()->NPC_PIRATE[$pirateid][NPCResourceDef::NPC_RESOURCE_CSV_ARMY_IDS];
		$armyID=$armyids[ rand(0, count($armyids)-1) ];
		$teamID = btstore_get()->ARMY[$armyID]['monster_list_id'];
		
		//如果是npc占领
		if($occupy_uid <= NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)
		{
			// 敌人信息
			$level_array = array_fill(0, count(FormationDef::$HERO_FORMATION_KEYS), $armylevel);
			$enemyFormation = EnFormation::getBossFormationInfo($teamID,$level_array);
		
			// 将对象转化为数组
			$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation,true);
			Logger::DEBUG('The boss list is %s', $enemyFormationArr);
		
			// 调用战斗模块
			$bt = new Battle();
			$atkRet = $bt->doHero(
					$battle_user,
					array(
							'uid' => $armyID,
							'name' => btstore_get()->ARMY[$armyID]['name'],
							'level' =>$armylevel ,//服务器的部队等级
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
			$occupyUserFormationArr = EnFormation::changeForObjToInfo($occupyUserFormation,true);
		
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
			Logger::debug('Ret from battle is %s.', $atkRet);
		}
		return array('atk'=>$atkRet,'armyid'=>$armyID);
	}
	
	/*
	 * 掠夺资源矿时的战斗
	 */
	private function _doPlunerBattle()
	{
		$uid=$this->m_uid;
		$page_id=$this->page_id;
		$resource_id=$this->resource_id;
		
		//获得当前的海贼团id
		$pirateid=$this->resourceInfo[ NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
		
		// 将阵型ID设置为用户当前默认阵型
		$user = EnUser::getUserObj($uid);
		$userFormation = EnFormation::getFormationInfo($uid);
		$formationID = $user->getCurFormation();
		$user->prepareItem4CurFormation();
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation,true);
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
		
		//随机部队id
		$armyids=btstore_get()->NPC_PIRATE[$pirateid][NPCResourceDef::NPC_RESOURCE_CSV_ARMY_IDS];
		$armyID=$armyids[ rand(0, count($armyids)-1) ];
		$teamID = btstore_get()->ARMY[$armyID]['monster_list_id'];
		
		//服务器部队等级
		$armylevel=NpcReourceLogic::getServerArmyLevel();
		
		// 敌人信息
		//$enemyFormation = EnFormation::getBossFormationInfo($teamID);
		$level_array = array_fill(0, count(FormationDef::$HERO_FORMATION_KEYS), $armylevel);
		$enemyFormation = EnFormation::getBossFormationInfo($teamID,$level_array);
		
		
		// 将对象转化为数组
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation,true);
		Logger::DEBUG('doNpcBattle The boss list is %s', $enemyFormationArr);
		
		// 调用战斗模块
		$bt = new Battle();
		$atkRet = $bt->doHero($battle_user,
				array(
						'uid' => $armyID,
						'name' => btstore_get()->ARMY[$armyID]['name'],
						'level' =>$armylevel,//注意，这里换成服务器部队等级
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
						'type' => BattleType::RESOURCE_PLUNDER,
				)
		);
		return $atkRet;
	}
	/*
	 * npc进攻时的战斗
	 */
	private function _doNpcAttackBattle()
	{
		$uid=$this->m_uid;
		$page_id=$this->page_id;
		$resource_id=$this->resource_id;
		
		//计算部队等级
		$npccount=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_NPC_COUNT];
		$armylevel=NpcReourceLogic::getServerArmyLevel()+$npccount+1;
		
	
		// 将阵型ID设置为用户当前默认阵型
		$user = EnUser::getUserObj($uid);
		$userFormation = EnFormation::getFormationInfo($uid);
		$formationID = $user->getCurFormation();
		$user->prepareItem4CurFormation();
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation,true);
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
		
		//策划原来是说每次战斗都固定用这个$armyID，但现在要改成随机，好吧
		//$armyID=$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_ARMY_ID];
		//随机部队id
		$pirateid =$this->resourceInfo[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
		$armyids=btstore_get()->NPC_PIRATE[$pirateid][NPCResourceDef::NPC_RESOURCE_CSV_ARMY_IDS];
		$armyID=$armyids[ rand(0, count($armyids)-1) ];

		$teamID = btstore_get()->ARMY[$armyID]['monster_list_id'];
		
		// 敌人信息
		//$enemyFormation = EnFormation::getBossFormationInfo($teamID);
		$level_array = array_fill(0, count(FormationDef::$HERO_FORMATION_KEYS), $armylevel);
		$enemyFormation = EnFormation::getBossFormationInfo($teamID,$level_array);
		
		// 将对象转化为数组
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation,true);
		Logger::DEBUG('doNpcBattle The boss list is %s', $enemyFormationArr);
		
		// 调用战斗模块
		$bt = new Battle();
		$atkRet = $bt->doHero(
				array(
						'uid' => $armyID,
						'name' => btstore_get()->ARMY[$armyID]['name'],
						'level' =>$armylevel,//注意，这里换成服务器部队等级
						'flag' => 0,
						'formation' => btstore_get()->TEAM[$teamID]['fid'],
						'isPlayer' => 0,
						'arrHero' => $enemyFormationArr),
				$battle_user,
				0,
				NULL,
				NULL,
				array (
						'bgid' => btstore_get()->ARMY[$armyID]['background_id'],
						'musicId' => btstore_get()->ARMY[$armyID]['music_path'],
						'type' => BattleType::RESOURCE,
				)
		);
		return $atkRet;
	}
	
	/*
	 * 获得玩家当前占的资源矿信息
	 */
	private function _getUserOccupyRes() 
	{
		$uid=$this->m_uid;
		$wheres  =array(array (	NPCResourceDef::NPC_RESOURCE_SQL_UID, '=',  $uid));
		$selectfield = array  ( NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID,
								NPCResourceDef::NPC_RESOURCE_SQL_RES_ID,
								NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME,
				);
		return NpcResourceDao::getFromNpcResInfoTbl($selectfield, $wheres);
	}
	/*
	 * 生成战报信息，给前端需要的格式
	 */
	private function _buildBattleInfo($battleary)
	{
		$battleinfo=array();
		//战报信息，页id 资源id 海贼团id 战报类型 目标uid 成功与否 战报id 时间，收益，总共防守了多少波进攻
		foreach ($battleary as $val)
		{
			$battletype=$val['type'];
			$attackerid=0;$attackername='';
			$defenderid=0;$defendername='';
			$success=$val['isSuccess'];
			
			if ($battletype==NPCResourceDef::NPC_RESOURCE_BATTLE_TYPE_ATTACKNPC)
			{
				$attackerid=$this->m_uid;
				$user = EnUser::getUserObj($attackerid);
				$attackername=$user->getUname();
				$defenderid= NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID;
				$defendername='';
			}
			elseif ($battletype==NPCResourceDef::NPC_RESOURCE_BATTLE_TYPE_ATTACKUSER)
			{
				$attackerid=$this->m_uid;
				$user = EnUser::getUserObj($attackerid);
				$attackername=$user->getUname();
				$defenderid= $val[4];
				$defenduser = EnUser::getUserObj($defenderid);
				$defendername=$defenduser->getUname();
			}
			elseif ($battletype==NPCResourceDef::NPC_RESOURCE_BATTLE_TYPE_DEFENDNPC_OK)
			{
				$attackerid=NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID;
				$attackername='';
				$defenderid=$this->m_uid;
				$defenduser = EnUser::getUserObj($defenderid);
				$defendername=$defenduser->getUname();
				//和前端约定，这个值始终代表攻击方是否胜利
				$success=false;
			}
			elseif ($battletype==NPCResourceDef::NPC_RESOURCE_BATTLE_TYPE_PLUNDERNPC)
			{
				$attackerid=$this->m_uid;
				$user = EnUser::getUserObj($attackerid);
				$attackername=$user->getUname();
				$defenderid= NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID;
				$defendername='';
			}
			elseif ($battletype==NPCResourceDef::NPC_RESOURCE_BATTLE_TYPE_DEFENDNPC_FAIL)
			{
				$attackerid=NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID;
				$attackername='';
				$defenderid=$this->m_uid;
				$defenduser = EnUser::getUserObj($defenderid);
				$defendername=$defenduser->getUname();
				//和前端约定，这个值始终代表攻击方是否胜利
				$success=true;
			}
				
			$battleinfo[]=array(
					 'pirate_id'=>$val['pirate_id'],
					 'type'=>$val['type'],
					 'result'=>$success,
					 'brid'=>$val['brid'],
					 'time'=>intval($val['time']),
					 'belly'=>$val['belly'],
					 'npc_count'=>$val['npccount'],
					 'attacker'=>array('id'=>$attackerid,'name'=>$attackername),
					 'defender'=>array('id'=>$defenderid,'name'=>$defendername),);
		}
		return $battleinfo;
	}
	/*
	 * 当战报数量大于100条时，删除多余的
	 */
	private function _processOverFlowBattleInfo($battleary)
	{
		if (empty($battleary))
			return $battleary;
		$curcont=count($battleary);
		if ($curcont <= NPCResourceDef::NPC_RESOURCE_MAX_BATTLE_COUNT)
			return $battleary;
		
		//只保留一百个（因为插入时是插入到数组的头的，所以只保留前100即可）
		$return= array_slice($battleary,0,NPCResourceDef::NPC_RESOURCE_MAX_BATTLE_COUNT-1);
		$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_VA_BATTLE_INFO]=$return;
		$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_LAST_BATTLE_TIME]=Util::getTime();
		return $return;
	}
	
	/*
	 * 向前端推广播消息
	 */
	private function _broadcastResInfo() 
	{
		$val['page_id']=$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_PAGE_ID];
		$val['resource_id']=$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_RES_ID];
		$val['pirate_id']=$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_PIRATE_ID];
		
		$occupytime=$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_OCCUPY_TIME];
		$val[NPCResourceDef::NPC_RESOURCE_RET_DUE_TIME]=$occupytime+btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_MAX_OCCUPY_TIME];
		
		//如果是npc占领，则保护时间为0
		$uid=$this->resourceInfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_UID];
		$val[NPCResourceDef::NPC_RESOURCE_RET_PROTECT_TIME]=0;
		if ($uid > NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)
		{
			$val[NPCResourceDef::NPC_RESOURCE_RET_PROTECT_TIME]=$occupytime+btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_OCCUPY_PROTECT_TIME];;
		}
		
		$val['uid']=$uid;
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
		
		//向前端广播数据
		RPCContext::getInstance()->sendFilterMessage('arena', NPCResourceDef::NPC_RESOURCE_OFF_SET,
		'sc.npcresource.resourceInfo',$val);
	}
	
	/*
	 * 推送战报信息
	*/
	private  function _sendBattleInfo($attackerid,$defenderid,$battleinfo)
	{
	  if ($attackerid > NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)
	  {
	  	 RPCContext::getInstance()->sendMsg(array($attackerid), 'sc.npcresource.report', $battleinfo);
	  }
	  if ($defenderid > NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)
	  {
	  	RPCContext::getInstance()->sendMsg(array($defenderid), 'sc.npcresource.report', $battleinfo);
	  }
	}
	
	/*
	 * 推送下一波NPC进攻时间
	 */
	private  function _sendNpcAttackInfo($nexttime,$pirate_id,$npccount)
	{
		$info=array('pirate_id'=>$pirate_id,'attackTime'=>$nexttime,'npc_count'=>$npccount);
		RPCContext::getInstance()->sendMsg(array($this->m_uid), 'sc.npcresource.npcAttack', $info);
	}
	
	/*
	 * 供控制台使用，增加可占领次数
	 */
	public function addOccupyCount($count)
	{
		$curcount=$this->userinfo[NPCResourceDef::NPC_RESOURCE_SQL_CAN_OCCUPY_COUNT];
		$this->userinfo_modify=$this->userinfo;
		$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_CAN_OCCUPY_COUNT]+=$count;
		$this->updatUserInfoToDb();
	}
	/*
	 * 供控制台使用，增加可掠夺次数
	*/
	public function addPlunderCount($count)
	{
		$curcount=$this->userinfo[NPCResourceDef::NPC_RESOURCE_SQL_CAN_PLUNDER_COUNT];
		$this->userinfo_modify=$this->userinfo;
		$this->userinfo_modify[NPCResourceDef::NPC_RESOURCE_SQL_CAN_PLUNDER_COUNT]+=$count;
		$this->updatUserInfoToDb();
	}
	
}

	
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */