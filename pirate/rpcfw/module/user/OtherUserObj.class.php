<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: OtherUserObj.class.php 40225 2013-03-07 06:40:56Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/OtherUserObj.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-07 14:40:56 +0800 (四, 2013-03-07) $
 * @version $Revision: 40225 $
 * @brief
 *
 **/


/**
 * UserObj和OtherUserObj用来修改用户信息。
 * 不要直接使用构造函数，应该EnUser::getInstance。
 *
 * 修改的用户有操蛋的四种状态，分别为：
 * 1、修改自己的数据，uid等于global.uid
 *  这种需要修改db，session。
 * 2、修改其他用户的数据，uid不等于global.uid
 * 	这种情况，保存变化的值，update的时候给lcserver发一个消息。
 * 	如果这个用户在线，则是第三种情况，否则是第四种情况
 * 3. lcserver告诉我，其他用户修改了老子的数据。uid等于global.uid
 * 	这种情况修改db，session，再同步到前端。
 * 4、没有任何在线用户，用来处理lcserve的调用。global.uid==null.修改数据库。
 *
 *
 * 情况2、使用OtherUserObj来实现，
 *
 * 1、3、4可以看做相同的处理：
 * 1、3的处理中，某些属性的修改可能需要通知任务系统。同步到前端，在上层模块中做。
 * 4 可以设置一个global.uid ，修改session、数据库。
 * 使用user.status来判断是否在线，通知任务系统。
 * 使用UserObj来实现， UserObj继承自OtherUserObj，实现update方法。
 *
 * @author idyll
 *
 *
 * 时间相关的处理，比如行动力，
 * 在构造函数的时候进行一次计算
 */



class OtherUserObj
{
	protected $user = array();
	protected $userModify = array();
	
		//主星盘属性
	protected $mainAst = null;
	//天赋星盘属性
	protected $talentAst = null;

	/**
	 * 不直接使用这个变量，因为这东西不会在构造的时候就赋值。
	 * 这是为了防止OtherUserObj 对象不做hero相关的操作的时候，也载人hero信息。
	 * 所以有了函数getHeroManager。
	 * @var HeroManager
	 */
	protected $heroManager = null;

	/**
	 * 属要计算的数据保存到这个字段里面，转到UserObj进行计算
	 * cur_execution
	 * protect_cdtime
	 * @var array
	 */
	private $extData = array();

	/**
	 * 特殊数据，只给otherUser使用， UserObj重载为空
	 * Enter description here ...
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @throws Exception
	 */
	protected function addExtData($key, $value)
	{
		switch ($key)
		{
			case 'cur_execution' :
				if (isset($this->extData['cur_execution']))
				{
					$this->extData['cur_execution'] += $value;
				}
				else
				{
					$this->extData['cur_execution'] = $value;
				}
				break;
			case 'protect_cdtime':
				$this->extData['protect_cdtime'] = $value;
				break;
			default:
				Logger::fatal('unknow ext data %d', $key);
				throw new Exception('sys');
				break;
		}
	}

	public function __construct ($uid)
	{
        if ($uid==RPCContext::getInstance()->getSession('global.uid'))
        {
            $this->user = UserSession::getSession('user.user');
        }
        else
        {
            $this->user = UserLogic::getUser($uid);
            if (empty($this->user))
            {
            	Logger::warning("fail to get user by uid:%d", $uid);
            	throw new Exception("user (uid:$uid) not found.");
            }
        }
        $this->user['uid'] = intval($this->user['uid']);
        if ($this->user['uid']==0)
        {
        	Logger::fatal('uid equal 0.');
        	throw new Exception('sys');
        }
		$this->userModify = $this->user;
		$this->init();
		$this->extData = array();
	}

	protected function init()
	{
		//1.计算行动力. 先给挂机减行动力，再计算
        //list($this->userModify['execution_time'], $this->userModify['cur_execution']) =
        //    UserLogic::calcExecution($this->userModify['execution_time'], $this->userModify['cur_execution']);

        $curTime = Util::getTime();
        //2.计算fight_cdtime时间
        if ($this->userModify['fight_cdtime'] < $curTime)
        {
            $this->userModify['fight_cdtime'] = $curTime;
            $this->user['fight_cdtime'] = $curTime;
        }

        //3 计算protect_cdtime
        if (!Util::isSameDay($this->userModify['protect_cdtime']))
        {
            $this->userModify['protect_cdtime_base'] = 0;
        }
        if ($this->userModify['protect_cdtime'] < $curTime)
        {
            $this->userModify['protect_cdtime'] = $curTime;
        }

        // 购买行动力
        if (!Util::isSameDay($this->userModify['last_buy_execution_time']))
        {
        	$this->userModify['last_date_buy_execution_num'] = 0;
        }
        
        //好感度相关
        if (!isset($this->user['va_user']['goodwill']))
        {
        	//两个都赋值，避免OtherUserObj更新va
			$this->user['va_user']['goodwill'] = UserLogic::getInitGoodwill();
			$this->userModify['va_user']['goodwill'] = $this->user['va_user']['goodwill'];
        }
        else if (!isset($this->user['va_user']['goodwill']['heritage']))
        {
        	$this->userModify['va_user']['goodwill']['heritage'] 
        		= $this->user['va_user']['goodwill']['heritage']
        		= array('time'=>Util::getTime(), 'num'=>0);
        }        
        
        //消耗金币统计
	 	if (!isset($this->user['va_user']['spend_gold']))
        {
        	//两个都赋值，避免OtherUserObj更新va
			$this->user['va_user']['spend_gold'] = array();
			$this->userModify['va_user']['spend_gold'] = $this->user['va_user']['spend_gold'];
        }
        
        //英雄转换
        if (!isset($this->user['va_user']['convert_heroes']))
        {
        	$this->user['va_user']['convert_heroes'] = array();
        	$this->userModify['va_user']['convert_heroes'] = array();
        	
        }
        
        //transfer group
        if (!isset($this->user['va_user']['group_info']))
        {
        	$this->user['va_user']['group_info'] = array('free_transfer'=>0, 'gold_transfer'=>0);
        	$this->userModify['va_user']['group_info'] = $this->user['va_user']['group_info'];
        }
	}

	public function calcExecution()
	{
		list($this->userModify['execution_time'], $this->userModify['cur_execution']) =
            UserLogic::calcExecution($this->userModify['execution_time'], $this->userModify['cur_execution']);
	}

	/**
	 * 得到招募英雄的顺序表
	 * Enter description here ...
	 */
	public function getRctHeroOrder ()
	{
		return $this->userModify['va_user']['recruit_hero_order'];
	}

	/**
	 * @return
	 * <code>
	 * 'uid'=>uid
	 * 'utid'=>utid
	 * 'uname'=>uname
	 * </code>
	 * Enter description here ...
	 */
	public function getTemplateUserInfo ()
	{
		return array('uid'=>$this->getUid(),
			'utid'=>$this->getUtid(),
			'uname'=>$this->getUname());
	}

	public function getStatus()
	{
		return $this->userModify['status'];
	}

	/**
	 * 是否禁言
	 * Enter description here ...
	 * @return bool
	 */
	public function isBanChat()
	{
		return $this->userModify['ban_chat_time'] > Util::getTime();
	}

	/**
	 * 禁言
	 * Enter description here ...
	 * @param unknown_type $endTime 结束时间
	 */
	public function banChat($endTime)
	{
		$this->userModify['ban_chat_time'] = $endTime;
	}

	/**
	 * 能招募英雄的数量
	 */
	public function getCanRecruitHeroNum ()
	{
		$num =  UserConf::CAN_RECRUIT_NUM;
		if (isset($this->userModify['va_user']['ext_hero_pos']))
		{
			//位置从0开始
			$num += ($this->userModify['va_user']['ext_hero_pos'] + 1);
		}
		return $num;
	}

	public function getPid()
	{
		return $this->userModify['pid'];
	}


	/**
	 * 返回所有英雄的htid
	 * Enter description here ...
	 */
	public function getAllHero ()
	{
		return $this->userModify['va_user']['heroes'];
	}

    public function printall()
    {
        var_dump($this->userModify['va_user']['heroes']);
    }

	public function addExecution ($num)
	{
		Logger::debug('addExecution:%d, cur:%d, execution_time:%s',
			$num, $this->userModify['cur_execution'], strftime("%Y%m%d %H:%M:%S", Util::getTime()));

		$num = intval($num);
        //这里必须先计算时间累计恢复的值
        list($this->userModify['execution_time'], $this->userModify['cur_execution'])
            = UserLogic::calcExecution($this->userModify['execution_time'], $this->userModify['cur_execution']);

        //这里可能是奖励，直接加吧，不检查最大值了
		$this->userModify['cur_execution'] += $num;
		if ($this->userModify['cur_execution'] < 0)
		{
            $this->userModify['cur_execution'] = 0;
			return false;
		}

		Logger::debug('addExecution res: cur:%d, execution_time:%s',
			$this->userModify['cur_execution'], strftime("%Y%m%d %H:%M:%S", Util::getTime()));

		$this->addExtData('cur_execution', $num);
		return true;
	}

	public function subExecution ($num)
	{
		Logger::debug('subExecution %d', $num);
		$num = intval($num);
		return $this->addExecution(-$num);
	}

	/**
	 * @deprecated
	 * 以key=>value返回用户信息
	 * Enter description here ...
	 */
	public function getUserInfo ()
	{
        //1.计算行动力
        list($this->userModify['execution_time'], $this->userModify['cur_execution'])
            = UserLogic::calcExecution($this->userModify['execution_time'], $this->userModify['cur_execution']);
        $arr = $this->userModify;
        $arr['level'] = $this->getMasterHeroLevel();
		return $arr;
	}

    /**
     * 返回留言
     */
	public function getMsg()
	{
		return $this->userModify['msg'];
	}

	public function getCurExecution()
	{
		//1.计算行动力
        list($this->userModify['execution_time'], $this->userModify['cur_execution'])
            = UserLogic::calcExecution($this->userModify['execution_time'], $this->userModify['cur_execution']);
		return $this->userModify['cur_execution'];
	}

	public function getCurFormation()
	{
		return $this->userModify['cur_formation'];
	}

	public function getExperience()
	{
		return $this->userModify['experience_num'];
	}

	public function getBelly()
	{
		return $this->userModify['belly_num'];
	}

	public function getGold()
	{
		return $this->userModify['gold_num'];
	}

	public function getGroupId()
	{
		return $this->userModify['group_id'];
	}

	public function getPrestige()
	{
		return $this->userModify['prestige_num'];
	}

	public function getAtkValue()
	{
		return $this->userModify['atk_value'];
	}

  	public function subAtkValue($atkValue)
	{
		return $this->addAtkValue(-$atkValue);
	}

	public function addAtkValue($atkValue)
	{
		$atkValue = intval($atkValue);
		$this->userModify['atk_value'] += $atkValue;
		if ($this->userModify['atk_value']<0)
		{
			$this->userModify['atk_value'] = 0;
			return false;
		}
		return true;
	}

	public function getUtid()
	{
		return $this->userModify['utid'];
	}

	public function getUid()
	{
		return $this->userModify['uid'];
	}

	public function getUname()
	{
		return $this->userModify['uname'];
	}

	/**
	 * @deprecated
	 * Enter description here ...
	 */
	public function getLevel()
	{
		return $this->getMasterHeroLevel();
	}

	public function getVip()
	{
		return $this->userModify['vip'];
	}

	public function getBloodPackage()
	{
		return $this->userModify['blood_package'];
	}

    public function getFightCDTime()
    {
        return $this->userModify['fight_cdtime'];
    }

	//跟保护时间不一样， 不计算累计时间
	public function addFightCDTime ($fightCDTime)
	{
		$curTime = Util::getTime();
		//cd没到不能加
		if ($this->userModify['fight_cdtime'] > $curTime )
		{
			return false;
		}
		$this->user['fight_cdtime'] = $curTime;
		$this->userModify['fight_cdtime'] = $curTime + $fightCDTime;
		return true;
	}

	public function getGuildId ()
	{
		return $this->userModify['guild_id'];
	}

	public function setGuildId ($guildId)
	{
		$this->userModify['guild_id'] = $guildId;
	}

    protected function addCDTime($cdTime, $baseTime, $addTime)
    {
        $curTime = Util::getTime();
        $baseTime += $addTime;
        $cdTime += $baseTime;

        //修改后，如果不是同一天，则把base重置为0
        if (!Util::isSameDay($cdTime))
        {
            $baseTime = 0;
        }
        return array($cdTime, $baseTime);
    }

    public function getProtectCDTime()
    {
        return $this->userModify['protect_cdtime'];
    }

    /**
     * 增加保护cdtime
     * isCheck 给otherUserObj转给userObj的时候使用，不检查是否能怎加cd
     * Enter description here ...
     * @param unknown_type $protectCDTime
     * @param unknown_type $isCheck
     */
	public function addProtectCDTime($protectCDTime, $isCheck=true)
	{
		//需要检查， cd时间没到，不能增加
		if ($isCheck && $this->userModify['protect_cdtime'] > Util::getTime())
		{
			return false;
		}

		if (!Util::isSameDay($this->userModify['protect_cdtime']))
		{
			$this->userModify['protect_cdtime_base'] =0;
		}

		if ($this->userModify['protect_cdtime'] < Util::getTime())
		{
			$this->userModify['protect_cdtime'] = Util::getTime();
		}

         list($this->userModify['protect_cdtime'], $this->userModify['protect_cdtime_base'])
         	= $this->addCDTime($this->userModify['protect_cdtime'], $this->userModify['protect_cdtime_base'], $protectCDTime);
        $this->addExtData('protect_cdtime', $protectCDTime);
        return true;
	}


	public function getUpperLimitBelly ()
	{
		return 10000000000;
	}

	public function addBelly ($num)
	{
		$num = intval($num);
		$this->userModify['belly_num'] += $num;

		if ($this->userModify['belly_num'] < 0)
		{
            $this->userModify['belly_num'] = 0;
			return false;
		}

		if ( $this->userModify['belly_num'] > $this->getMaxBelly())
		{
			$this->userModify['belly_num'] = $this->getMaxBelly();
		}

		return true;
	}


	public function subBelly ($num)
	{
		return $this->addBelly(-$num);
	}

	public function addGold ($num)
	{
		$num = intval($num);
		$this->userModify['gold_num'] += $num;

		if ($this->userModify['gold_num'] < 0)
		{
            $this->userModify['gold_num'] = 0;
			return false;
		}
		return true;
	}

    public function isOnline()
    {
        return $this->userModify['status']==UserDef::STATUS_ONLINE;
    }

	public function addFood ($num)
	{
		$num = intval($num);
		$this->userModify['food_num'] += $num;

		if ($this->userModify['food_num'] < 0)
		{
            $this->userModify['food_num'] = 0;
			return false;
		}

		if ( $this->userModify['food_num'] > $this->getMaxFood() )
		{
			$this->userModify['food_num'] = $this->getMaxFood();
		}
		return true;
	}

	public function subBloodPackage ($num)
	{
		$num = intval($num);
		$this->userModify['blood_package'] -= $num;
		if ($this->userModify['blood_package'] < 0)
		{
            $this->userModify['blood_package'] = 0;
			return false;
		}
		return true;
	}

	//如果当前的血量小于$num, 则只减去当前量
	//返回实际量
	public function subFitBloodPackage ($num)
	{
		$num = intval($num);
		$this->userModify['blood_package'] -= $num;

		if ($this->userModify['blood_package'] < 0)
		{
			$num = $this->userModify['blood_package'] + $num;
			$this->userModify['blood_package'] = 0;
		}
		return $num;
	}

	public function subFood ($num)
	{
		return $this->addFood(-$num);
	}

	/**
	 * @deprecated
	 * Enter description here ...
	 * @param unknown_type $num
	 */
	public function addExp($num)
	{
		$this->getMasterHeroObj()->addExp($num);
		return true;
	}

	/**
	 * 威望最小值为0,
	 * @param unknown_type $num
	 * @return true;
	 */
	public function addPrestige ($num)
	{
		$num = intval($num);
		$this->userModify['prestige_num'] += $num;
		if ($this->userModify['prestige_num'] < 0)
		{
            $this->userModify['prestige_num'] = 0;
            return false;
		}
		$this->isPrestigeChange = true;
		return true;
	}

	public function subPrestige ($num)
	{
		return $this->addPrestige(-$num);
	}

	public function addExperience ($num)
	{
		$num = intval($num);
		$this->userModify['experience_num'] += $num;
		if ( $this->userModify['experience_num'] > $this->getMaxExperience() )
		{
			$this->userModify['experience_num'] = $this->getMaxExperience();
		}

		if ($this->userModify['experience_num'] < 0)
		{
			$this->userModify['experience_num'] = 0;
			return false;
		}

		return true;
	}

	public function subExperience ($num)
	{
		return $this->addExperience(-$num);
	}

	public function rollback()
	{
		if ($this->heroManager!=null)
		{
			$this->getHeroManager()->rollback();
		}
		$this->userModify = $this->user;
		$this->extData = array();
	}

	public function getMaxGoldmine()
	{
		$ret = 0;
		foreach (UserConf::$GOLD_MINE_MAX as $prestige => $max)
		{
			if ($this->userModify['prestige_num'] < $prestige)
			{
				break;
			}
			else
			{
				$ret = $max;
			}
		}

		return $ret;
	}

	//血池上限=(医疗室等级+1)*医疗室血池上限系数+(人物等级+4)*人物血池上限系数
	public function getMaxBloodPackage()
	{
		$boatInfo = EnSailboat::getUserBoat($this->userModify['uid']);
		// 医疗室等级
		$medicalRoomLv = 0;
		if (isset($boatInfo['va_boat_info']['cabin_id_lv'][SailboatDef::MEDICAL_ROOM_ID]['level']))
		{
			$medicalRoomLv = $boatInfo['va_boat_info']['cabin_id_lv'][SailboatDef::MEDICAL_ROOM_ID]['level'];
		}

		$max = ($medicalRoomLv+1)*btstore_get()->MEDICAL_ROOM['max_hp_package_percent']	+
			($this->getMasterHeroLevel()+4) * UserConf::BLOOD_PACKAGE_LEVEL;
		return ceil($max);
	}

	public function update ()
	{
		if ($this->heroManager!=null)
		{
			$this->getHeroManager()->update();
		}

		$arrField = array();
		foreach ($this->user as $key => $value)
		{
			if ($this->userModify[$key]!=$value)
			{
				if ($key == 'protect_cdtime_base'
					|| $key == 'protect_cdtime'
					|| $key == 'cur_execution'
					|| $key == 'execution_time'
					|| $key == 'last_date_buy_execution_num')
				{
					continue;
				}

                if ($key=='guild_id'
                	|| $key == 'ban_chat_time')
                {
                    $arrField[$key] = $this->userModify[$key];
                }
                else
                {
                    $arrField[$key] = $this->userModify[$key]-$value;
                }
			}

			//特殊数据
			foreach ($this->extData as $key => $value)
			{
				$arrField[$key] = $value;
			}
		}

        if (!empty($arrField))
        {
        	Logger::debug('old info:%s, new info:%s, modify other user field:%s',
        		$this->user, $this->userModify, $arrField);
            $this->user = $this->userModify;
            $this->extData = array();
            //给lcserver发消息
            RPCContext::getInstance()->executeTask($this->user['uid'],
                                                   'user.modifyUserByOther',
                                                   array($this->user['uid'], $arrField),
                                                   false);
        }
	}

    private function getMaxBelly()
    {
    	return UserConf::BELLY_MAX;
    }

    private function getMaxFood()
    {
        return UserConf::FOOD_MAX;
    }

	private function getMaxExperience()
    {
    	return UserConf::EXPERIENCE_MAX;
    }

    /***************************************
     * 以下是英雄相关接口
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
			$this->heroManager = new HeroManager($this->userModify['uid'], $this->getAttrRctHeroes());
		}
		return $this->heroManager;
	}

	protected function getAttrRctHeroes($noCache=false)
	{
		$arrHid = $this->userModify['va_user']['recruit_hero_order'];
		return HeroLogic::getArrHero($arrHid, HeroDef::$HERO_FIELDS, $noCache);
	}

	//返回已招募的英雄
    public function getHeroObj($hid)
    {
    	return $this->getHeroManager()->getRctHeroObj($hid);
    }

    public function getMasterHeroLevel()
    {
    	return $this->getHeroManager()->getMasterHeroLevel();
    }

	/**
	 * 得到主角英雄
	 * @return MasterHeroObj
	 */
	public function getMasterHeroObj()
	{
		return $this->getHeroManager()->getMasterHeroObj();
	}

	public function hasHero($htid)
	{
		if (in_array($htid, $this->getAllHero()))
		{
			return true;
		}
		else
		{
			//已经转过档的也算
			return $this->isHeroConvert($htid);
		}
	}

	public function getRecruitHeroes ()
	{
		return $this->getHeroManager()->getRecruitHeroes();
	}

	public function getRecruitHeroesNum ()
	{
		return $this->getHeroManager()->getRecruitHeroesNum();
	}

	public function getLastTownId()
	{
		return $this->userModify['last_town_id'];
	}

	//返回威望排名
	public function getOrderPresitge()
	{
		return UserDao::getOrderPrestige($this->userModify['prestige_num'], $this->userModify['uid']);
	}

	public function setVip4Test($vip)
	{
		$this->userModify['vip'] = $vip;
	}

	public function getLastLoginTime()
	{
		return $this->userModify['last_login_time'];
	}
	
	/**
	 * 读取一组英雄的装备、恶魔果实，以及转被上的宝石到缓存(ItemManager管理)
	 * Enter description here ...
	 * @param unknown_type $arrHid 不超过9个
	 * @throws Exception
	 */
	public function prepareItem($arrHid)
	{
		if (count($arrHid) > 9)
		{
			Logger::fatal('must less than 10');
			throw new Exception('sys');
		}
		
		$arrArmingId = array();
		$arrGenId = array();
		
		//得到这些英雄所有的装备、恶魔果实id
		foreach ($arrHid as $hid)
		{
			$hero = $this->getHeroObj($hid);
			$arrArmingId = array_merge($arrArmingId, $hero->getArmingDmItemId());
		}
		$arrArmingItem = ItemManager::getInstance()->getItems($arrArmingId);
		
		foreach ($arrArmingItem as $item_id => $item)
		{		
			
			if ($item==null)
			{
				Logger::fatal('fixing item! uid: %d item_id: %d', $this->getUid(), $item_id);
				continue;
			}
			
			if ($item->getItemType() == ItemDef::ITEM_ARM)
			{	
				$arrGenId = array_merge($arrGenId, array_values($item->getGemItems()));
			}
		}
				
		ItemManager::getInstance()->getItems($arrGenId);		
	}
	
	/**
	 * @see prepareItem
	 * Enter description here ...
	 * @param unknown_type $arrHid 不超过9个
	 * @throws Exception
	 */
	public function prepareItem4CurFormation()
	{
		$arrHid = EnFormation::getFormationHids($this->getUid());
		$this->prepareItem($arrHid);	
	}
	
	/**
	 * 返回消耗金币信息
	 * Enter description here ...
	 * @return array(
	 * date1 => spendGoldNum,
	 * date2 => spendGoldNum,
	 * )
	 */
	public function getAccumSpendGold()
	{
		return $this->userModify['va_user']['spend_gold'];
	}
	
	/**
	 * 封号信息
	 * Enter description here ...
	 */
	public function getBanInfo()
	{
		if (!isset($this->userModify['va_user']['ban']))
		{
			return array('time'=>0, 'msg'=>'');
		}
		
		return $this->userModify['va_user']['ban'];
	}
	
	public function getConvertHeroes()
	{
		return $this->userModify['va_user']['convert_heroes'];
	}
	
	public function isHeroConvert($htid)
	{
		return in_array($htid, $this->userModify['va_user']['convert_heroes']);
	}
	
	public function getFightForce()
	{
		$this->prepareItem4CurFormation();
		$arrHids = EnFormation::getFormationHids($this->getUid());
		
		$fightForce = 0;
		foreach ($arrHids as $hid)
		{
			$hero = $this->getHeroObj($hid);			
			$fightForce += $hero->getFightForce();
		}
		return $fightForce;
	}	
	
	public function getMainAst()
	{
		if ($this->mainAst===null)
		{
			$this->mainAst = Astrolabe::getCurMainAstAttr($this->userModify['uid']);
		}
		return $this->mainAst;
	}
	
	public function getTalentAst()
	{
		if ($this->talentAst===null)
		{
			$this->talentAst = Astrolabe::getCurTalentAstAttr($this->userModify['uid']);
		}
		return $this->talentAst;
	}
	
	public function isShowDress()
	{
		return $this->userModify['show_dress'];
	}
	
	public function getMasterHeroDressTemplate()
	{
		return $this->getMasterHeroObj()->getDressTemplate();
	}
	
	public function getLoginDate()
	{
		return $this->userModify['va_user']['login_date'];
	}
	
	protected function writeBattleHitLog($str)
	{
		Logger::info('battle info cached %s', $str);
	}
	
	/**
	 * 返回给战斗使用的hero数组
	 * @return  array
	 * <code>
	 * 'ret'=>'ok'  or  'not_enough_hp'
	 * 'info'=> array();
	 * </code>
	 */
	public function getBattleInfo($needFreeMaxHp=false /*$needFightForce=true*/)
	{
		$needFightForce = true;
		$arrRet = array('ret'=>'ok', 'info'=>array());		
		$key = EnUser::getBattleInfoKey($this->userModify['uid']);
		$battleInfo = McClient::get($key);
		if ($battleInfo===null)
		{
			$arrRet = $this->getBattleInfo_($needFreeMaxHp);					
			$battleInfo = array('update_uid'=>RPCContext::getInstance()->getUid(), 'info'=>$arrRet['info']);
			$this->setBattleInfo($key, $battleInfo);
			
			$this->writeBattleHitLog('unhit');
		}
		else 
		{
			//当前用户
			$curUid = RPCContext::getInstance()->getUid();
			//非当前用户更新的数据， 重新计算一次
			if ($curUid == $this->userModify['uid'] && $curUid != $battleInfo['update_uid']) 
			{
				$arrRet = $this->getBattleInfo_($needFreeMaxHp);
				$battleInfo = array('update_uid'=>RPCContext::getInstance()->getUid(), 'info'=>$arrRet['info']);
				$this->setBattleInfo($key, $battleInfo);
				
				$this->writeBattleHitLog('update');
			}
			else
			{
				$this->writeBattleHitLog('hit');
			}
			
			$arrRet['info'] = $battleInfo['info'];
			
			//cache保存都是满血，用当前血库判断是否为满血，
			//不准确的情况，英雄是满血的, 血库刚好为空
			if ($this->getBloodPackage()<=0)
			{
				$arrRet['ret'] = 'not_enough_hp';
			}
		}
		
		return $arrRet;			
	}
	
	protected function setBattleInfo($key, $info)
	{
		if (UserConf::USE_BATTLE_CACHED)
		{
			Logger::debug('set battle info %s', $key);
			McClient::set($key, $info, UserConf::BATTLE_EXPIRED_TIME);
		}	
	}
	
	protected function getBattleInfo_($needFreeMaxHp=false)
	{
		$needFightForce = true;
		$arrRet = array('ret'=>'ok', 'info'=>array());
		$this->prepareItem4CurFormation();
		$formation = EnFormation::getFormationInfo($this->userModify['uid']);
		$arrRet['ret'] = EnFormation::checkUserFormation($this->userModify['uid'], $formation);
		$arrRet['info'] = EnFormation::changeForObjToInfo($formation, $needFreeMaxHp, $needFightForce);
		return $arrRet;		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */