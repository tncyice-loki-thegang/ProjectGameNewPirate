<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserObj.class.php 40527 2013-03-11 09:06:36Z HaidongJia $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/UserObj.class.php $
 * @author $Author: HaidongJia $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-11 17:06:36 +0800 (一, 2013-03-11) $
 * @version $Revision: 40527 $
 * @brief
 *
 **/




/**
 *
 * @see OtherUserObj
 * @author idyll
 *
 */
class UserObj extends OtherUserObj
{
	/**
	 * 英雄属性数组
	 * Enter description here ...
	 * @var array
	 */
	protected $arrHeroAttr = array();

	/**
	 * 威望英雄表
	 * Enter description here ...
	 * @var array
	 */
	private $arrPtgHero = null;

	public function __construct ($uid)
	{
		$this->arrPtgHero = btstore_get()->PRESTIGE_HERO;
        parent::__construct($uid);
	}

	protected function addExtData($key, $value)
	{
		//nothing, just for other
		return;
	}

    protected $attrFunc = array(
        'cur_execution'       =>   'addExecution',
        'belly_num'           =>   'addBelly'         ,
        'gold_num'            =>   'addGold'          ,
        'prestige_num'        =>   'addPrestige'      ,
        'experience_num'      =>   'addExperience'    ,
        'food_num'            =>   'addFood'          ,
        'blood_package'       =>   'addBloodPackage'  ,
    	'guild_id'            =>   'setGuildId',
    	'fight_cdtime'		  => 	'fromOtherAddFightCdtime',
    	'protect_cdtime'	  => 	'fromOtherAddProtectCdtime',
    	'atk_value'			  => 	'addAtkValue',
    	'ban_chat_time'	      => 	'setBanChatTime',
    	'vip' => 'fromOtherSetVip4Test'
        );

     //这里， 进攻冷却时间， 不检查是否大于当前时间
    protected function fromOtherAddFightCdtime($num)
    {
    	$num = intval($num);
    	$curTime = Util::getTime();
    	if ($this->userModify < $curTime)
    	{
    		$this->userModify['fight_cdtime'] = $curTime;
    	}
    	$this->userModify['fight_cdtime'] += $num;
    }

    public function fromOtherSetVip4Test($num)
    {
    	$this->userModify['vip'] += $num;
    	$this->userModify['va_user']['vip_ics_time'] = Util::getTime();
    }

    //这里，不检查是否大于当前时间, 因为用户可能 恰好 被多个用户攻击， 这个值能累计
    protected function fromOtherAddProtectCdtime($num)
    {
    	$this->addProtectCDTime($num, false);
    }

    public function getLastSalaryTime()
    {
    	return $this->userModify['last_salary_time'];
    }

    public function setLastSalaryTime($time)
    {
    	return $this->userModify['last_salary_time'] = $time;
    }

	/**
	 * 修改user字段的值，包括对象本身, 但是不修改数据库。
	 * 例如用于充值加金币，先使用batch update，然后调用此函数
	 * @param unknown_type $arrFields
	 */
	public function modifyFields($arrFields)
	{
		foreach ($arrFields as $key=>$value)
		{
			$this->user[$key] += $value;
			$this->userModify[$key] += $value;
		}
	}

    /**
	 * 修改用户信息.
     * 输入的属性数组为变化的值
	 * Enter description here ...
	 * @param unknown_type $arrField
	 */
	public function modifyUserByOther($arrField)
	{
		foreach ($arrField as $attrName=>$num)
		{
            if (isset($this->attrFunc[$attrName]))
            {
                call_user_func(array($this, $this->attrFunc[$attrName]), $num);
            }
            else
            {
                Logger::fatal('can not modify other user attribute %s', $attrName);
              	throw new Exception('fake');
            }
		}
	}

	public function setBanChatTime($time)
	{
		$this->userModify['ban_chat_time'] = $time;
	}

	public function getLastPlaceType()
	{
		return $this->userModify['last_place_type'];
	}

	public function getLastPlaceData()
	{
		return $this->userModify['last_place_data'];
	}

	public function setLastPlaceType($type)
	{
		$this->userModify['last_place_type'] = $type;
	}

	public function setLastPlaceData($data)
	{
		$this->userModify['last_place_data'] = $data;
	}

	public function subGold ($num)
	{
		if ($num<0)
		{
			Logger::fatal('fail to subGold, the num %d is not positive', $num);
			throw new Exception('sys');
		}
		$this->sumSpendGold($num);
		
		return $this->addGold(-$num);
	}

	public function addGold ($num)
	{
		$num = intval($num);
		if ($num<0)
		{
			$ret = parent::addGold($num);
			if ($ret)
			{
				EnDaytask::costGold();
			}
			return $ret;
		}
		else
		{
			return parent::addGold($num);
		}
	}

	public function setGroupId($groupId)
	{
		if ($this->userModify['group_id']!=0)
		{
			Logger::warning('fail to set group id, the group can be set only one time ');
			throw new Exception('fake');
		}

		if (!isset(GroupConf::$GROUP[$groupId]))
		{
			Logger::warning('the group is invalidate.');
			throw new Exception('fake');
		}
		$this->userModify['group_id'] = $groupId;
	}
	
	public function groupTransferByGold($groupId, $gold)
	{
		if ($gold==0)
		{
			$today = Util::todayDate();
			if ($this->userModify['va_user']['group_info']['free_transfer'] >= btstore_get()->GROUP_TRANSFER['free_num']
					|| defined('GameConf::MERGE_SERVER_OPEN_DATE')
					|| ($today - GameConf::SERVER_OPEN_YMD) > btstore_get()->GROUP_TRANSFER['free_day']) 
			{
				Logger::warning('free transfer group over max num or overdate');				
				throw new Exception('fake');
			}			
		}
		else
		{
			$needGold = btstore_get()->GROUP_TRANSFER['gold_base'] + 
				$this->userModify['va_user']['group_info']['gold_transfer'] * btstore_get()->GROUP_TRANSFER['gold_ics'];
			++$this->userModify['va_user']['group_info']['gold_transfer'];
			if ($needGold != $gold)
			{
				Logger::warning('gold not equal, argv %d,  config %d', $gold, $needGold);
				throw new Exception('fake');
			}
			
			if (!$this->subGold($needGold))
			{
				Logger::warning('lack gold for group transfer');
				throw new Exception('fake');
			}

			Statistics::gold(StatisticsDef::ST_FUNCKEY_GROUP_TRANSFER, $needGold, Util::getTime());
		}				
		$this->groupTransfer($groupId);
	}
	
	public function groupTransferByItems($groupId)
	{
		$items = btstore_get()->GROUP_TRANSFER['items'];
		$bag = BagManager::getInstance()->getBag();
		
		if (!$bag->deleteItemsByTemplateID($items))
		{
			Logger::warning('lack item for group transfer by items');
			throw new Exception('fake');
		}
		
		$ret = $bag->update();		
		$this->groupTransfer($groupId);
		return $ret;
	}
	
	public function groupTransferCheck($groupId)
	{
		if ($this->getGroupId()==0)
		{
			Logger::warning('fail to transfer group, select group first');
			throw new Exception('fake');
		}
		
		if ($this->getGroupId()==$groupId)
		{
			Logger::warning('fail to transfer group, dest group is same to current');
			throw new Exception('fake');
		}
		
		if (!isset(GroupConf::$GROUP[$groupId]))
		{
			Logger::warning('the group is invalidate.');
			throw new Exception('fake');
		}		
		
		//olympic time		
		if (EnOlympic::isOlympicTime(Util::getTime())) 
		{			
			return 'olympic';
		}
		
		//boss time 
		if (BossUtil::isInBossTime(Util::getTime()))
		{
			return 'boss';			
		}	
			
		return 'ok';
	}
	
	protected function groupTransfer($groupId)
	{
		$this->userModify['group_id'] = $groupId;
		
		$port = new Port();
		$portId = $port->getPort();
		$townId = Port::getTownByPort($portId);
		$newPortId = City::getEnterPort($townId, $groupId);
		if ($portId!=$newPortId)
		{
			$port->moveIntoPort($newPortId, Util::getTime());
		}
	}

    public function setMsg($msg)
	{
		if (mb_strlen($msg, 'utf-8')>UserConf::MSG_MAX_LEN)
		{
			Logger::warning('msg lenght is too long');
			throw new Exception('fake');
		}

		//过滤敏感词
		$msg = TrieFilter::mb_replace($msg);
		$this->userModify['msg'] = $msg;
		return true;
	}

	public function setVip($vip)
	{
		$this->userModify['vip'] = $vip;
	}

	public function resetFightCDTime()
	{
		$this->userModify['fight_cdtime'] = Util::getTime();
	}

    public function resetProctectCDTime()
    {
    	$this->userModify['protect_cdtime'] = Util::getTime();
    }

	public function addBloodPackage ($num)
	{
		$num = intval($num);
        //给已招募的英雄把血加满
        $addHpForHero = false;
        if ($this->userModify['blood_package']==0 && $num>0)
        {
        	$addHpForHero = true;
        }

		$maxNum = $this->getMaxBloodPackage();
    	if ($maxNum < $num + $this->userModify['blood_package'])
    	{
    		Logger::warning('fail to addBloodPackage, over max num %d. cur:%d, add:%d',
    			$maxNum, $this->userModify['blood_package'], $num);
    		throw new Exception('fake');
    	}

		$this->userModify['blood_package'] += $num;
		if ($this->userModify['blood_package'] < 0)
		{
            $this->userModify['blood_package'] = 0;
			return false;
		}

		if ($addHpForHero)
		{
			$this->getHeroManager()->addHpToMaxForRecruit();
		}
		return true;
	}

	public function fullBloodPackage ()
	{
		$diff = $this->getMaxBloodPackage()-$this->userModify['blood_package'];
		while ($diff !=0)
		{
			$this->addBloodPackage($diff);
			$diff = $this->getMaxBloodPackage()-$this->userModify['blood_package'];
		}
		return true;
	}

    /**
	 * 设置当前阵型
	 * Enter description here ...
	 * @param unknown_type $fid
	 */
	public function setCurFormation ($fid)
	{
		$this->userModify['cur_formation'] = $fid;
	}

    /**
	 * 保存招募英雄的顺序表
	 * Enter description here ...
	 * @param unknown_type $arrHtid
	 */
	public function saveRctHeroOrder ($arrHid)
	{
		$this->userModify['va_user']['recruit_hero_order'] = $arrHid;
	}

    /**
	 * 往顺序表中添加招募英雄
	 * @param unknown_type $htid
	 */
	public function addHeroToRctHeroOrder($hid)
	{
		$this->userModify['va_user']['recruit_hero_order'][] = $hid;
	}

	/**
	 * 往顺序表中删除招募英雄
	 * @param unknown_type $htid
	 * @throws Exception
	 */
	public function delHeroFromRctHeroOrder ($hid)
	{
		$arrHid = $this->userModify['va_user']['recruit_hero_order'];
		$pos = array_search($hid, $arrHid);
		if ($pos === false)
		{
			throw new Exception("fail to find hero $hid in recruit_hero_order");
		}
		unset($arrHid[$pos]);
		$arrHid = array_merge($arrHid);
		$this->userModify['va_user']['recruit_hero_order'] = $arrHid;
	}

    public function addLoginDate()
	{
		$today = Util::todayDate();
		$today = intval($today);
		
		//今天已经登录过，直接返回
		$lastLoginDate = end($this->userModify['va_user']['login_date']);
		if ($today==$lastLoginDate)
		{
			return;
		}

		$this->userModify['va_user']['login_date'][] = $today;
		if (count($this->userModify['va_user']['login_date']) > UserConf::LOGIN_DATE_NUM)
		{
			unset($this->userModify['va_user']['login_date'][0]);
		}
		$this->userModify['va_user']['login_date'] = array_merge($this->userModify['va_user']['login_date']);
	}

	public function setLoginTime()
	{
		if (!isset($this->userModify['va_user']['wallow']))
		{
			$this->userModify['va_user']['wallow'] = array('login'=>Util::getTime(), 'logoff'=>0, 'accum'=>0, 'kick'=>0);
		}
		else
		{
			RPCContext::getInstance()->setSession('global.last_logoff_time', $this->userModify['va_user']['wallow']['logoff']);
			
			$wallow = &$this->userModify['va_user']['wallow'];			
			//判断上次下线时间， 跟这次登录时间是否为同一天		
			if (!Util::isSameDay($wallow['logoff']))
			{
				$wallow['accum'] = 0;
				$wallow['logoff'] = 0;				
			}
			$wallow['login'] = Util::getTime();
		}
		
		$this->userModify['last_login_time'] = Util::getTime();
	}
	
	public function getVaUser()
	{
		return $this->userModify['va_user'];
	}
	

    public function setStatus($status)
    {
    	$this->userModify['status'] = $status;
    }

    public function getItemAccum()
    {
    	if (!isset($this->userModify['va_user']['item_accum']))
    	{
    		return null;
    	}
    	return $this->userModify['va_user']['item_accum'];
    }

    public function setItemAccum($itemAccum)
    {
    	$this->userModify['va_user']['item_accum'] = $itemAccum;
    }

    public function buyBloodPackage($num)
    {
    	
    	$boatInfo = EnSailboat::getUserBoat($this->userModify['uid']);
    	// 医疗室等级
    	$medicalRoomLv = 0;
    	if (isset($boatInfo['va_boat_info']['cabin_id_lv'][SailboatDef::MEDICAL_ROOM_ID]['level']))
    	{
    		$medicalRoomLv = $boatInfo['va_boat_info']['cabin_id_lv'][SailboatDef::MEDICAL_ROOM_ID]['level'];
    	}
    	$numPerBelly = max(array(intval($medicalRoomLv/3), UserConf::BLOOD_PER_BELLY));
    	
    	$needBelly = ceil($num / $numPerBelly);
    	if (false==$this->subBelly($needBelly))
    	{
    		Logger::warning('fail to buyBloodPackage, belly is not enough.');
    		throw new Exception('fake');
    	}

    	return $this->addBloodPackage($num);
    }

    public function buyExecution ($num)
    {
    	$num = intval($num);
    	$cur = $this->getCurExecution();
    	$max = UserConf::MAX_EXECUTION;
    	if ($cur+$num > $max )
    	{
    		Logger::warning('fail to buy execution, over max num.');
    		return 'overflow';
    	}

    	//检查是否能买
    	$vip = $this->getVip();
    	$numCanBuy = btstore_get()->VIP[$vip]['execution_gold']['num'];

    	if (!Util::isSameDay($this->userModify['last_buy_execution_time']))
    	{
    		$this->userModify['last_date_buy_execution_num'] = 0;
    	}

    	if ($num + $this->userModify['last_date_buy_execution_num'] > $numCanBuy)
    	{
    		Logger::warning('fail to buy execution, over num of can buy.');
    		return 'overflow';
    	}

    	//sub gold
    	$price = btstore_get()->VIP[$vip]['execution_gold']['gold'];
    	$costGold = $price * $num;
    	if ($this->subGold($costGold)==false)
    	{
    		Logger::warning('fail to buy execution, the gold is not enough.');
    		throw new Exception('fake');
    	}

    	Statistics::gold(StatisticsDef::ST_FUNCKEY_BUY_EXECUTION, $costGold, Util::getTime());

    	$this->userModify['last_date_buy_execution_num'] += $num;
    	$this->userModify['last_buy_execution_time'] = Util::getTime();
    	$this->userModify['cur_execution'] += $num;
    	return 'ok';
    }

	public function update ()
	{
		if ($this->heroManager!=null)
		{
			$this->getHeroManager()->update();
			RPCContext::getInstance()->setSession('hero.arrHeroAttr', $this->heroManager->getArrRctAttr());
		}

		$arrField = array();
		foreach ($this->user as $key => $value)
		{
			if ($this->userModify[$key]!=$value)
			{
				$arrField[$key] = $this->userModify[$key];
			}
		}

        //保存到数据库
        if (!empty($arrField))
        {
            UserDao::updateUser($this->user['uid'], $arrField);
        }

        //用户在线，通知任务系统
        if ($this->isOnline())
        {
            if ($this->userModify['prestige_num'] != $this->user['prestige_num'])
            {
                TaskNotify::userPrestigeChange();
            }
        }

        $this->user = $this->userModify;
        UserSession::saveSession('user.user', $this->user);

		//保存到数据库
        if (!empty($arrField))
        {
            //成就
            if (isset($arrField['vip']))
            {
            	EnAchievements::notify($this->getUid(), AchievementsDef::VIP_LEVEL, $this->getVip());
            }

            if (isset($arrField['belly_num']))
            {
            	// 通知成就系统
				EnAchievements::notify($this->getUid(), AchievementsDef::MAX_BELLY, $this->getBelly());
            }

            // 通知成就系统
            if (isset($arrField['prestige_num']))
            {
				EnAchievements::notify($this->getUid(), AchievementsDef::MAX_PRESTIGE, $this->getPrestige());
            }

            // 通知成就系统
            if (isset($arrField['experience_num']))
            {
				EnAchievements::notify($this->getUid(), AchievementsDef::MAX_EXPERIENCE, $this->getExperience());
            }

        }
	}


	/**************
	 * hero相关
	 */
	/**
	 * 当前类都使用此函数得到$this->heroManager
	 * @see $this->heroManager
	 * Enter description here ...
	 * @return HeroManager
	 */
	public function getHeroManager()
	{
		if ($this->heroManager==null)
		{
			$this->heroManager = new HeroManager($this->userModify['uid'], $this->getAttrRctHeroes(true));
		}
		return $this->heroManager;
	}
	
	protected function getAttrRctHeroes($noCache=false)
	{
		$arrHeroAttr = RPCContext::getInstance()->getSession('hero.arrHeroAttr');
		if ($arrHeroAttr == null)
		{
			$arrHeroAttr = parent::getAttrRctHeroes($noCache);
			RPCContext::getInstance()->setSession('hero.arrHeroAttr', $arrHeroAttr);
			Logger::debug('load hero no cache');
		}
		return RPCContext::getInstance()->getSession('hero.arrHeroAttr');
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $htid
	 * @return HeroObj
	 */
	public function getPubHeroObj($htid)
	{
		return $this->getHeroManager()->getPubHeroObj($htid);
	}

	//返回已招募的英雄
	public function getHeroObjByHtid($htid)
	{
		return $this->getHeroManager()->getRctHeroObjByHtid($htid);
	}

	public function addExpForRecruit ($expNum, $countType)
	{
		return $this->getHeroManager()->addExpForRecruit($expNum, $countType);
	}

	public function getPubHeroes()
	{
		return $this->getHeroManager()->getPubHeroes();
	}

	/**
	 * 添加威望英雄到酒馆
	 * @param $htid
	 * @throws Exception
	 */
	public function addPrestigeHero($htid)
	{
		//判断是否存在
		if (!isset($this->arrPtgHero[$htid]))
		{
			Logger::warning("htid %d is not exist in prestige hero table.", $htid);
			throw new Exception('fake');
		}

		//判断是否已经在酒馆或者已经招募
		//addNewHeroToPub检查

		//判断威望
		$ptg = $this->userModify['prestige_num'];
		if ($ptg < $this->arrPtgHero[$htid]['prestige_num'])
		{
			Logger::warning("prestige %d is not enough for add prestige hero htid %d.", $ptg, $htid);
			throw new Exception('fake');
		}

		//判断阵营
		//等于0, 所有阵营都可招募
		if ($this->arrPtgHero[$htid]['group_id'] != 0)
		{
			$groupId = $this->userModify['group_id'];
			if ($groupId != $this->arrPtgHero['htid']['group_id'])
			{
				Logger::warning("the htid %d is not for the group_id %d.", $htid, $groupId);
				throw new Exception('fake');
			}
		}
		$this->addNewHeroToPub($htid);
	}

	/**
	 * 添加一个新的可招募的英雄
	 * @param unknown_type $htid
	 */
	public function addNewHeroToPub($htid)
	{
		Logger::debug("add hero htid %d to pub.", $htid);
		if (in_array($htid, $this->getAllHero()))
		{
			Logger::fatal('the user has had the hero htid:%d', $htid);
			throw new Exception('fake');
		}

		if (!HeroUtil::checkHtid($htid))
		{
			Logger::fatal("invalid htid %d for addHero", $htid);
			throw new Exception('sys');
		}
		$this->userModify['va_user']['heroes'][] = $htid;

		// 获取现在拥有的全部英雄数
		$allHreoNum = count($this->userModify['va_user']['heroes']);
		// 通知成就系统
		EnAchievements::notify($this->getUid(), AchievementsDef::MAX_HEROS, $allHreoNum);
		EnAchievements::notify($this->getUid(), AchievementsDef::OWN_HERO, $htid);
	}

	/**
	 * 所有recruit hero的血加满
	 * Enter description here ...
	 */
	public function addHpToMaxForRecruit()
	{
		$this->getHeroManager()->addHpToMaxForRecruit();
	}

	/**
	 * 增加成就点数
	 */
	public function addAchievePoint($achievePoint)
	{
		$this->userModify['achieve_point'] += $achievePoint;
		$this->userModify['last_achieve_time'] = Util::getTime();
	}

	/**
	 * 出错的情况下，重置成就点数
	 */
    public function setAchievePoint($achievePoint)
    {
		$this->userModify['achieve_point'] = $achievePoint;
    }

    public function getAchievePoint()
    {
    	return $this->userModify['achieve_point'];
    }

    /**
     * 设置用户最新副本ID
     */
    public function setCopyID($copyID)
    {
    	$this->userModify['copy_id'] = $copyID;
		$this->userModify['last_copy_time'] = Util::getTime();
    }

    /**
     * 消耗副本令
     */
    public function subCopyExecution($num=1)
    {
    	return $this->subExtraExecution('copy_execution', $num);
    }

    /**
     * 获取副本令个数
     */
    public function getCopyExecution()
    {
		return $this->getExtraExecution('copy_execution');
    }

    public function getVassalExecution()
    {
    	return $this->getExtraExecution('vassal_execution');
    }

    public function subVassalExecution($num=1)
    {
    	return $this->subExtraExecution('vassal_execution', $num);
    }

    public function getResourceExecution()
    {
    	return $this->getExtraExecution('resource_execution');
    }

    public function subResourceExecution($num=1)
    {
    	return $this->subExtraExecution('resource_execution', $num);
    }

    public function getAttackExecution()
    {
    	return $this->getExtraExecution('attack_execution');
    }

    public function subAttackExecution($num=1)
    {
    	return $this->subExtraExecution('attack_execution', $num);
    }

    private function subExtraExecution($executionType, $num)
    {
    	$cur = $this->getExtraExecution($executionType);
    	if ($cur < $num)
    	{
    		return false;
    	}
    	$this->userModify[$executionType] -= $num;
    	return true;
    }

    private function getExtraExecution($executionType)
    {
		// 返回
		return $this->userModify[$executionType];
    }

    public function getUserType()
    {
    	return RPCContext::getInstance()->getSession('global.userType');
    }

    /**
     * 得到上次需要更新免费令的时间
     * Enter description here ...
     */
    private function getNeedUpdateExtraExecutionTime()
    {
    	$arrResetTime = ExtraExecutionConf::$EXECUTION_RESET_DEFAULT;
    	sort($arrResetTime);

		$p = current($arrResetTime);
		$curTime = Util::getTime();
		$last = 0;

		//当前时间小于第一个更新时间，则最后更新的时间是昨天的最后一个时间点
		if ($curTime < strtotime($p))
		{
			$last = strtotime('-1 day ' . end($arrResetTime), Util::getTime());
		}
		else
		{
			while ( $p != false )
			{
				$p = strtotime($p, Util::getTime());
				if ($p > $curTime)
				{
					break;
				}
				$last = $p;
				$p = next($arrResetTime);
			}
		}
		return $last;
    }

    /**
     * 计算免费令，并且把血库加满
     * Enter description here ...
     */
    public function extraExecution()
    {
    	$arrRet = array();
    	$curTime = Util::getTime();
    	$arrResetTime = ExtraExecutionConf::$EXECUTION_RESET_DEFAULT;
    	sort($arrResetTime);
    	//数据库上次更新时间
    	$lastUpdateTime = $this->userModify['copy_execution_time'];

    	//上次需要更新的时间
    	$lastNeedUpdateTime = $this->getNeedUpdateExtraExecutionTime();
    	if ($lastUpdateTime < $lastNeedUpdateTime)
    	{
    		//加血库
			$this->fullBloodPackage();

			foreach (ExtraExecutionConf::$EXECUTION_CFG as $executionType => $num)
			{
				$this->userModify[$executionType] = $num;
				$this->userModify[$executionType . '_time'] = $lastNeedUpdateTime;
			}
    	}

		$arrRet['copy_execution'] = $this->getCopyExecution();
		$arrRet['attack_execution'] = $this->getAttackExecution();
		$arrRet['resource_execution'] = $this->getResourceExecution();
		$arrRet['vassal_execution'] = $this->getVassalExecution();

		return $arrRet;
    }

    /**
     * 先减已有的行动力，再计算按时间恢复的行动力，
     * 登录时候，挂机用来减行动力
     * Enter description here ...
     * @param unknown_type $num
     */
	public function preSubExecution($num)
	{
		Logger::debug('preSubExecution %d', $num);
		$num = intval($num);
		if ($this->userModify['cur_execution'] < $num)
		{
			return false;
		}
		$this->userModify['cur_execution'] -= $num;
		return true;
	}

	public function getAllAttr()
	{
		return $this->userModify;
	}

	/**
	 * 在线累计时间
	 * Enter description here ...
	 */
	public function getOnlineAccumTime()
	{
		$loginTime = RPCContext::getInstance()->getSession('global.login_time');
		return Util::getTime() - $loginTime + $this->userModify['online_accum_time'];
	}

	public function getMute()
	{
		return $this->userModify['mute'];
	}

	public function setMute($isMute)
	{
		$this->userModify['mute'] = $isMute;
	}

	public function getVisibleType()
	{
		return $this->userModify['visible_type'];
	}

	public function setVisibleType($visibleType)
	{
		$this->userModify['visible_type'] = $visibleType;
	}

	/**
	 * 是否已经使用了新手卡
	 * Enter description here ...
	 */
	public function isUseBeginnerCard()
	{
		if (isset($this->userModify['va_user']['beginner_card']))
		{
			return true;
		}
		return false;
	}

	public function setBeginnerCard()
	{
		$this->userModify['va_user']['beginner_card'] = 1;
	}
	
	/**
	 * 开启附加的招募位置，从0开始，必须是连续的
	 * Enter description here ...
	 * @param unknown_type $pos
	 */
	public function openHeroRecruitPos($pos)
	{
		if (!isset($this->userModify['va_user']['ext_hero_pos']))
		{
			$this->userModify['va_user']['ext_hero_pos'] = -1;
		}
		
		if ($this->userModify['va_user']['ext_hero_pos']+1 != $pos)
		{
			Logger::warning('fail to open recruit pos. cur:%d, open:%d', 
				$this->userModify['va_user']['ext_hero_pos'], $pos);
			throw new Exception('fake');
		}
		
		$this->userModify['va_user']['ext_hero_pos'] = $pos;
		
		$costGold = ($pos + 1) * UserConf::OPEN_HERO_POS_COST_BASE;
		if (!$this->subGold($costGold))
		{
			Logger::warning('fail to open hero recruit pos, gold is not enough');
			throw new Exception('fake');
		}	

		//
		Statistics::gold(StatisticsDef::ST_FUNCKEY_HERO_POS, $costGold, Util::getTime());
	}
	
	public function setPayReward()
	{
		$this->userModify['va_user']['pay_reward'] = 1;
	}
	
	public function getPayReward()
	{
		if (empty($this->userModify['va_user']['pay_reward']))
		{
			return 0;
		}
		
		return $this->userModify['va_user']['pay_reward'];
	}
	
	protected function init()
	{
		parent::init();

		$this->resetGoodwill();		
		
		if (count($this->userModify['va_user']['spend_gold']) > UserConf::SPEND_GOLD_DATE_NUM)
		{
			$first = key($this->userModify['va_user']['spend_gold']);
			unset($this->userModify['va_user']['spend_gold'][$first]);
		}
	}
	
	protected function resetGoodwill()
	{
		if (!Util::isSameDay($this->userModify['va_user']['goodwill']['time']))
		{
			$this->userModify['va_user']['goodwill']['num_by_gold'] = 0;
			$this->userModify['va_user']['goodwill']['time'] = Util::getTime();
			$this->userModify['va_user']['goodwill']['num_free'] = 0;
		}
	}

	public function getGoodwillFreeNum()
	{
		return btstore_get()->VIP[$this->getVip()]['goodwill_vip_num'];
	}
	
	/**
	 * 增加使用金币加好感度的次数, 这里会消耗金币
	 * Enter description here ...
	 */
	public function addGoodwillNum()
	{		
		$vip = $this->getVip();		
		$maxNum = btstore_get()->VIP[$vip]['goodwill_vip_num'] + UserConf::GOODWILL_NUM_BY_GOLD;
		
		$gw = &$this->userModify['va_user']['goodwill'];
		if ($gw['num_by_gold'] >= $maxNum)
		{
			Logger::warning('fail to add goodwill by gold, num %d over or equal max %d', 
				$gw['num_by_gold'], $maxNum);
			throw new Exception('fake');
		}			
		
		if ($gw['num_free'] < $this->getGoodwillFreeNum())
		{
			++$gw['num_free'];
		}
		else
		{
			//计算金币值
			$needGold = (1 + $this->userModify['va_user']['goodwill']['num_by_gold']) 
				* UserConf::GOODWILL_BY_GOLD_BASE;  
		
			////减金币, 金币是否够
			$user = EnUser::getUserObj();
			if (!$user->subGold($needGold))
			{
				Logger::warning('fail to add goodwill num by gold, gold not enough for %d', $needGold);
				throw new Exception('fake');
			}
			++$gw['num_by_gold'];
			
			Statistics::gold(StatisticsDef::ST_FUNCKEY_GOODWILL_GOLD, $needGold, Util::getTime());
		}
	}
	
	protected function sumSpendGold($costGold)
	{
		$today = date("Ymd", Util::getTime());
		if (!isset($this->userModify['va_user']['spend_gold'][$today]))
		{
			$this->userModify['va_user']['spend_gold'][$today] = 0;
		}
		
		$this->userModify['va_user']['spend_gold'][$today] += $costGold;		
	}
	
	/**
	 * 使用va字段 保存消费累计，不再使用reward_point
	 * Enter description here ...
	 * @param unknown_type $reward
	 */
	public function setSpendReward($reward)
	{
		$this->userModify['va_user']['spend_reward'] = $reward;
	}
	
	public function getSpendReward()
	{
		if (isset($this->userModify['va_user']['spend_reward']))
		{
			return $this->userModify['va_user']['spend_reward'];
		}
		return '';
	}
	
	public function setRewardPoint()
	{
		throw new Exception('sys');
	}
	
	public function setBirthday()
	{
		throw new Exception('sys');
	}
	
	public function setQid($qid)
	{
		if ($this->userModify['birthday']==0)
		{
			$this->userModify['birthday'] = intval($qid);
		}
	}
	
	public function ban($time, $msg)
	{
		if (strlen($msg)>30)
		{
			$msg = substr($msg, 0, UserConf::BAN_MSG_MAX_LEN);
		}
		$this->userModify['va_user']['ban'] = array('time' => $time, 'msg'=>$msg);
	}
	
	public function isBan()
	{
		if (!isset($this->userModify['va_user']['ban']))
		{
			return false;
		}
		
		if ($this->userModify['va_user']['ban']['time'] > Util::getTime())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function covertHero($srcHtid, $desHtid)
	{
		//替换英雄
		$pos = array_search($srcHtid, $this->userModify['va_user']['heroes']);
		if ($pos!==false)
		{
			$this->userModify['va_user']['heroes'][$pos] = $desHtid;
		}
		else
		{
			Logger::warning('the htid %d is not in va_user.heroes', $srcHtid);
		}
		
		//添加到convert
		if (!in_array($srcHtid, $this->userModify['va_user']['convert_heroes']))
		{
			$this->userModify['va_user']['convert_heroes'][] = $srcHtid;
		}
		else
		{
			Logger::warning('the htid %d is in convert heroes', $srcHtid);
		}		
	}
	
	public function resetHeritageGoodwillNum()
	{
		if (!Util::isSameDay($this->userModify['va_user']['goodwill']['heritage']['time']))
        {
        	$this->userModify['va_user']['goodwill']['heritage']['time'] = Util::getTime();
        	$this->userModify['va_user']['goodwill']['heritage']['num'] = 0;
        }	
	}
	
	public function getTodayHeritageGoodwillNum()
	{	
		$this->resetHeritageGoodwillNum();	
        return $this->userModify['va_user']['goodwill']['heritage']['num'];
	}
	
	public function addHeritageGoodwillNum($num)
	{
		$this->resetHeritageGoodwillNum();
		$this->userModify['va_user']['goodwill']['heritage']['time'] = Util::getTime();
        $this->userModify['va_user']['goodwill']['heritage']['num'] += $num;
	}
	
	public function getLastLogoffTime()
	{
		$time = RPCContext::getInstance()->getSession('global.last_logoff_time');
		if ($time==null)
		{
			return 0;
		}
		return $time;
	}	
	
	public function getOPClientReward()
	{
		if (!isset($this->userModify['va_user']['opclient_reward']))
		{
			return 0;
		}
		return $this->userModify['va_user']['opclient_reward'];
	}
	
	public function setOPClientReward()
	{
		return $this->userModify['va_user']['opclient_reward'] = 1;
	}
	
	public function setVaConfig($vaConfig)
	{
		if (count($vaConfig) > UserConf::VA_CONFIG_SIZE)
		{			
			Logger::warning('va config %d is more than max size', count($vaConfig));
			throw new Exception('fake');
		}
		
		foreach ($vaConfig as $config)
		{
			if (is_array($config))
			{
				Logger::warning('va config type err');
				throw new Exception('fake');
			}
		}
		
		$this->userModify['va_user']['va_config'] = $vaConfig;
	}
	
	public function setArrConfig($key, $value)
	{
		if (!isset($this->userModify['va_user']['arr_config']))
		{
			$this->userModify['va_user']['arr_config'] = array();
		}
		
		if (is_array($value))
		{
			if (count($value) > UserConf::ARR_CONFIG_SIZE)
			{
				Logger::warning('arr config is more than max size');
				throw new Exception('fake');
			}
			
			foreach ($value as $tmp)
			{
				if (is_array($tmp))
				{
					Logger::warning('arr config type err');
					throw new Exception('fake');
				}
				else if (is_string($tmp))
				{
					if (strlen($tmp) > 100)
					{
						Logger::warning('string too long for arr config');
						throw new Exception('fake');
					}
				}
			}
		}
		else if(is_string($value))
		{
			if (strlen($tmp) > 100)
			{
				Logger::warning('string too long for arr config');
				throw new Exception('fake');
			}
		}
		
		$this->userModify['va_user']['arr_config'][$key] = $value;
		if (count($this->userModify['va_user']['arr_config']) > UserConf::ARR_CONFIG_SIZE)
		{
			Logger::warning('arr config too large');
			throw new Exception('fake');
		}
	}
	
	public function getVaConfig()
	{
		if (isset($this->userModify['va_user']['va_config']))
		{
			return $this->userModify['va_user']['va_config'];
		}
		else
		{
			return array();
		}		
	}
	
	public function getArrConfig()
	{
		if (isset($this->userModify['va_user']['arr_config']))
		{
			return $this->userModify['va_user']['arr_config'];
		}
		else
		{
			return array();
		}
	}
	
	public function getGemExp()
	{
		return $this->userModify['gem_exp'];		
	}
	
	public function subGemExp($exp)
	{
		$this->userModify['gem_exp'] -= $exp;
		if ($this->userModify['gem_exp'] < 0)
		{
			$this->userModify['gem_exp'] = 0;
			return false;
		}  
		return true;		
	}
	
	public function addGemExp($exp)
	{
		$this->userModify['gem_exp'] += $exp;
	}
	
	public function buyGemExp($id)
	{
		$cfg = btstore_get()->GEM_EXP_GOLD;
		if (!isset($cfg[$id]))
		{
			Logger::warning('fail to buy gem exp, id %d is not exist', $id);
			throw new Exception('fake');
		}
		
		$cfg = $cfg[$id];
		if (!$this->subGold($cfg['gold']))
		{
			Logger::warning('gold is not enough for buy gem exp');
			throw new Exception('fake');
		} 
		
		Statistics::gold(StatisticsDef::ST_FUNCKEY_EXPLORE_BUY_EXP, $cfg['gold'], Util::getTime());
		
		$this->addGemExp($cfg['gem_exp']);
	}
	
	public function showDress($isShow)
	{
		if ($isShow!=0 && $isShow!=1)
		{
			Logger::warning('show dress argv %s err', $isShow);
			throw new Exception('fake');
		}
		$this->userModify['show_dress'] = $isShow;
	}
	
	protected function transferBattleArrHero($arrInfo)
	{
		//nothing
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */