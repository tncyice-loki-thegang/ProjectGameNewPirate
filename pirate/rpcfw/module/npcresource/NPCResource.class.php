<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: NPCResource.class.php 36941 2013-01-24 07:58:44Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/branches/pirate/rpcfw/newworldres/module/npcresource/NPCResource.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-24 15:58:44 +0800 (星期四, 24 一月 2013) $
 * @version $Revision: 36941 $
 * @brief 
 *  
 **/

class NpcResource implements INpcResource
{
	/*
	 * 初始化玩家uid
	*/
	private $m_uid;
	public function __construct()
	{
		$this->m_uid = RPCContext::getInstance()->getUid();
		//这里不能判断m_uid是否有效，因为下面的有几个接口并不能知道uid，比如
		//npc进攻玩家时，npc没有uid
	}

	/*
	 * 玩家占领资源
	 */
	public function attackResourceByUser($page_id, $resource_id)
	{
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('NpcResource invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		$obj=new MyNpcResource($this->m_uid,$page_id, $resource_id);
		return $obj->attackResourceByUser();
	}
	
	/*
	 * npc进攻领资源,由timer来执行
	*/
	public function attackResourceByNpc($uid,$page_id, $resource_id)
	{
		$obj=new MyNpcResource($uid,$page_id, $resource_id);
		$obj->attackResourceByNpc();
	}
	/*
	 * 玩家手动点击按钮，立即执行npc进攻
	 */
	public function doNpcAttackNow($page_id, $resource_id)
	{
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('NpcResource invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		$obj=new MyNpcResource($this->m_uid,$page_id, $resource_id);
		return $obj->doNpcAttackNow();
	}
	
	/*
	 * 资源矿到期，有timer来执行
	 */
	public function dueNpcResource($uid,$page_id, $resource_id,$npccount)
	{
		//如果是npc占领，则继续由npc占领
		if ($uid == NPCResourceDef::NPC_RESOURCE_NPC_ATTACK_UID)
		{
			NpcReourceLogic::resetResourceToNpcAttack($page_id,$resource_id);
		}
		else //如果是玩家占领
		{
			$obj=new MyNpcResource($uid,$page_id, $resource_id,$npccount);
			$obj->dueNpcResource();
		}
	}
	
	/*
	 * 获取指定页的资源信息
	 */
	public function resourceInfo($page_id)
	{
		//检测uid
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('NpcResource.resourceInfo invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		//参数是否合法
		$allpagecount=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_RES_PAGE_COUNT];
		if ($page_id <=0 || $page_id > $allpagecount )
		{
			Logger::fatal('NpcResource.resourceInfo page_id err! page_id:%d allpagecount:%d',$page_id,$allpagecount);
			throw new Exception('fake');
		}
		
		//从数据库里拉数据
		$ret=NpcReourceLogic::getCurPageResInfo($page_id);
		
		//如果为空（db的表还没初始化）或者和btstore里的个数不一致（说明策划更新了csv），则需要初始化
		$newinfo=array();
		$resids=btstore_get()->NPC_RES[NPCResourceDef::NPC_RESOURCE_CSV_RES_ID_ATTR_ARY][$page_id];
		if (empty($ret)||(count($resids) > count($ret)&&count($ret) > 0))
		{
			$newinfo=NpcReourceLogic::initResourceToNpcAttack($page_id,$ret);
		}
		//有没有异常情况（如某个玩家占矿后timer挂掉，无法继续下去），在这里进行修正
		if(!empty($ret))
		{
			NpcReourceLogic::fixUnExceptionResInfo($page_id);
		}
		//合并信息
		foreach ($newinfo as $val)
		{
			$ret[]=$val;
		}
		//生成前端信息并返回给前端
		return NpcReourceLogic::buildRetPageResInfo($ret);
	}
	
	/*
	 * 放弃资源
	 */
	public function givenUpNpcResource($page_id, $resource_id)
	{
		//检测uid
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('NpcResource invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		//放弃资源矿，并结算收益
		$obj=new MyNpcResource($this->m_uid,$page_id,$resource_id);
		return $obj->givenupResource();
	}

	/*
	 * 掠夺资源
	 */
	public function plunderResource($page_id, $resource_id)
	{
		//检测uid
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('NpcResource invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		$obj=new MyNpcResource($this->m_uid,$page_id, $resource_id);
		return $obj->plunderResource();
	}
	
	/*
	 * 进入npc资源矿
	 */
	public function enterNpcResource() 
	{
		//检测uid
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('NpcResource invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		
		RPCContext::getInstance()->setSession('global.arenaId', NPCResourceDef::NPC_RESOURCE_OFF_SET);
	
		//返回玩家自己的信息，比如占了几个矿、可占领次数、可掠夺次数等
		$obj=new MyNpcResource($this->m_uid);
		return $obj->selfResourceInfo();
	}
	
	/*
	 * 离开npc资源矿
	*/
	public function leaveNpcResource()
	{
		RPCContext::getInstance()->unsetSession('global.arenaId');
	}
	
	
	/*
	 * 供控制台使用，增加可占领次数和可掠夺次数
	 */
	public function addOccupyCount($count)
	{
		//检测uid
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('NpcResource invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		if ($count <=0)
		{
			return ;
		}
		$obj=new MyNpcResource($this->m_uid);
		return $obj->addOccupyCount($count);
	}
	public function addPlunderCount($count)
	{
		//检测uid
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('NpcResource invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		if ($count <=0)
		{
			return ;
		}
		$obj=new MyNpcResource($this->m_uid);
		return $obj->addPlunderCount($count);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */