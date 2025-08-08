<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroManager.class.php 26087 2012-08-22 08:39:14Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/HeroManager.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-22 16:39:14 +0800 (三, 2012-08-22) $
 * @version $Revision: 26087 $
 * @brief
 *
 **/



class HeroManager
{
	/**
	 * 威望英雄表
	 * @var array
	 * <code>
	 * htid=> array('prestige_num'=>int, 'group_id'=>int)
	 * </code>
	 */
	private $arrPtgHero = null;

	/**
	 * hero属性数组, htid => array()
	 * Enter description here ...
	 * @var array
	 */
	private $arrRctAttr = null;

    //hid => obj
    private $arrRctObj = array();
    
    //htid => obj都是没有招募过的英雄
    private $arrPubObj = array();

    private $uid=0;
    
    private $masterHid = 0;

	public function __construct ($uid, $arrRctAttr)
	{
		$this->uid = $uid;		
		$this->arrRctAttr = $arrRctAttr;
		$this->arrPtgHero = btstore_get()->PRESTIGE_HERO;
	}
    
	public function saveRctHero ($attr)
	{
		$this->arrRctAttr[$attr['hid']] = $attr;
	}
	
	private function createHero($heroAttr)
	{
		$guid = RPCContext::getInstance()->getUid();
		if ($guid == $heroAttr['uid'])
		{
			if (HeroUtil::isMasterHero($heroAttr['htid']))
			{
				return new MasterHeroObj($heroAttr);
			}
			else 
			{
				return new HeroObj($heroAttr);
			}
		}
		else
		{
			return new OtherHeroObj($heroAttr);
		}
	}
	
	public function getArrRctAttr()
    {
    	return $this->arrRctAttr;
    }
    
    /**
     * 得到master hero 对象
     * @return MasterHeroObj 
     */
    public function getMasterHeroObj()
    {
    	if ($this->masterHid==0)
    	{
    		foreach ($this->arrRctAttr as $attr)
    		{
    			if (HeroUtil::isMasterHero($attr['htid']))
    			{
    				$this->masterHid = $attr['hid'];
    				break;
    			}
    		}
    	}
    	return $this->getRctHeroObj($this->masterHid);
    }
	
	public function getMasterHeroLevel ()
	{
		$heroObj = $this->getMasterHeroObj();
		return $heroObj->getLevel();
	}
	
	/**
	 * 得到已招募HeroObj对象,
	 * 如果不是当前用户的已招募英雄则返回OtherHeroObj对象
	 * @return HeroObj
	 */
	public function getRctHeroObj ($hid)
	{
		if ($hid == 0)
		{
			Logger::warning('getHeroObj by hid 0');
			throw new Exception('fake');
		}
		
		if (!isset($this->arrRctObj[$hid]))
		{
			if (isset($this->arrRctAttr[$hid]))
			{
				$this->arrRctObj[$hid] = $this->createHero($this->arrRctAttr[$hid]);
			}			
			else
			{
				Logger::warning('fail to get hero by hid %d for user %d', $hid, $this->uid);
                throw new Exception('fake');
			}			
		}		
		return $this->arrRctObj[$hid];
	}
	
	private function getRctHidByHtid($htid)
	{
		foreach ($this->arrRctAttr as $hid=>$attr)
		{
			if ($htid==$attr['htid'])
			{
				return $hid;
			}
		}
		return 0;
	}
	
	public function getRctHeroObjByHtid($htid)
	{
		$hid = $this->getRctHidByHtid($htid);
		return $this->getRctHeroObj($hid);
	}
	
	public function getPubHeroObj ($htid)
	{
		if (!isset($this->arrPubObj[$htid]))
		{
			if (!EnUser::getUserObj($this->uid)->hasHero($htid))
			{
				Logger::warning('fail to get pub hero %d', $htid);
				throw new Exception('fake');				
			}
			
			$attr = $this->getPubHeroAttr($htid);
			$obj = $this->createHero($attr);
			if ($obj->isPub())
			{
				$this->arrPubObj[$htid] = $obj;
			}
			else
			{
				Logger::warning('fail to get pub hero %d', $htid);
				throw new Exception('fake');
			}					
		}
		return $this->arrPubObj[$htid];
	}

	/**
	 * 给所有已招募的英雄加经验
	 * Enter description here ...
	 * @param unknown_type $expNum
	 * @param unknown_type $countType
	 */
	public function addExpForRecruit ($expNum, $countType)
	{
		//先给主英雄加经验
		$mHero = $this->getMasterHeroObj();
		$rate = 1;
		if ($countType == TaskCountReward::REWARD_LEVEL)
		{
			$rate = $mHero->getLevel();
		}
		$mHero->addExp($expNum * $rate);
		
		foreach ($this->arrRctAttr as $hid=>$attr)
		{
			$hero = $this->getRctHeroObj($hid);
			if ($hero->isMasterHero())
			{
				continue;
			}

			if ($hero->isRecruit())
			{
				if ($countType == TaskCountReward::REWARD_LEVEL)
				{
					$rate = $hero->getLevel();
				}
				$hero->addExp($expNum * $rate);
			}
		}
	}

	/**
	 * 给所有已招募的英雄加hp
	 * Enter description here ...
	 * @param unknown_type $expNum
	 * @param unknown_type $countType
	 */
	public function addHpToMaxForRecruit ()
	{
		foreach ($this->arrRctAttr as $hid=>$attr)
		{
			$heroObj = $this->getRctHeroObj($hid);
			$heroObj->setToMaxHp();
		}
	}

	private function getPubHeroAttr($htid)
	{
		$uid = RPCContext::getInstance()->getSession('global.uid');
		$attr = HeroLogic::getHeroByUidHtid($uid, $htid);
		//从来没有招募过的英雄
		if (empty($attr))
		{
			//为了统一，这里把hid设置为0， 实际是无效的hid
			$attr = array("status"=>HeroDef::STATUS_PUB, "htid"=>$htid, 'level'=>1, 'hid'=>0, 'uid'=>$uid);
		}
		return $attr;
	}

	/**
	 * 得到招募英雄数组，
	 * 数组内容为英雄属性
	 * Enter description here ...
	 */
	public function getRecruitHeroes ()
	{
		$ret = array();
		foreach ($this->arrRctAttr as $hid => $attr)
		{
			$hero = $this->getRctHeroObj($hid);
			$ret[$hid] = $hero->getAllAttr();
		}
		return $ret;
	}
	
	/**
	 * 得到酒馆英雄
	 * Enter description here ...
	 * @return htid => array()
	 */
	public function getPubHeroes ()
	{
		$ret = array();
		$user = EnUser::getUserObj($this->uid);
		$arrAllHtid = $user->getAllHero();
		$arrRctHtid = Util::arrayExtract($this->arrRctAttr, 'htid');
		$arrPubHtid = array_diff($arrAllHtid, $arrRctHtid);
		
		//去掉已经转换的英雄
		$arrConvertHtid = $user->getConvertHeroes();
		if (!empty($arrConvertHtid))
		{
			$arrPubHtid = array_diff($arrPubHtid, $arrConvertHtid);
		}
		
		$arrRet = HeroLogic::getArrHeroByHtid($this->uid, $arrPubHtid);		
		foreach ($arrPubHtid as $htid)
		{
			if (!isset($arrRet[$htid]))
			{
				$arrRet[$htid] = array('htid'=>$htid);
			}
		}
		return $arrRet;
	}

	/**
	 * 得到已招募英雄的数量
	 */
	public function getRecruitHeroesNum()
	{
		return count($this->arrRctAttr);
	}
	
	/**
	 * 普通英雄的等级不能超过主角英雄的等级
	 * 必须先更新主角英雄，不然等级可能有问题
	 * Enter description here ...
	 */
	public function update()
	{
		//更新主角英雄
		$mh = $this->getMasterHeroObj();
		$mh->update();
		$this->saveRctHero($mh->getAllAttr());
		
		$arrNewPubObj = array();
		foreach ($this->arrRctObj as $obj)
		{
			if (!$obj->isMasterHero())
			{
				$obj->update();
				if ($obj->isRecruit())
				{
					$this->saveRctHero($obj->getAllAttr());
				}
				else
				{
					unset($this->arrRctAttr[$obj->getHid()]);
					$arrNewPubObj[$obj->getHtid()] = $obj;
				}
			}
		}
		
		foreach ($this->arrPubObj as $obj)
		{
			$obj->update();
			if ($obj->isRecruit())
			{
				$this->saveRctHero($obj->getAllAttr());
				$this->arrRctObj[$obj->getHid()] = $obj;
				unset($this->arrPubObj[$obj->getHtid()]);
			}
		}
		
		$this->arrPubObj += $arrNewPubObj;
	}
	
	public function rollback()
	{
		foreach ($this->arrRctObj as $obj)
		{
			$obj->rollback();
		}
		
		foreach ($this->arrPubObj as $obj)
		{
			$obj->rollback();
		}
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */