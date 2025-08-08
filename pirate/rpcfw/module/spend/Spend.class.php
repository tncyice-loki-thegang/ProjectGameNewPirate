<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Spend.class.php 40526 2013-03-11 09:03:06Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/spend/Spend.class.php $
 * @author $Author: HaidongJia $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-11 17:03:06 +0800 (一, 2013-03-11) $
 * @version $Revision: 40526 $
 * @brief
 *
 **/

class Spend implements ISpend
{
	private function getSpendGold($beginTime)
	{
		$beginDate = strftime("%Y%m%d", $beginTime);
		//strtotime($cfg0['begin_time']));

		$user = EnUser::getUserObj();
		$arrDateAccum = $user->getAccumSpendGold();
		$goldAccum = 0;
		foreach ($arrDateAccum as $date => $gold)
		{
			if ($date >= $beginDate)
			{
				$goldAccum += $gold;
			}
		}
		return $goldAccum;
	}

	private function getConfig()
	{
		$arrRet = array();
		$arrCfg = btstore_get()->SPEND_ACCUM;
		$curTime = Util::getTime();
		foreach ($arrCfg as $id=>$cfg)
		{
			if ($curTime >= strtotime($cfg['begin_time'])
				&& $curTime <= strtotime($cfg['end_time'])
				&& GameConf::SERVER_OPEN_YMD <= $cfg['needOpentime'])
			{
				$arrRet[$id] = $cfg;
			}
		}

		return $arrRet;
	}

	private function getSpendReward($arrConfig)
	{
		$arrRet = array();
		$user = EnUser::getUserObj();
		$reward = $user->getSpendReward();
		foreach ($arrConfig as $id=>$t)
		{
			if (isset($reward[$id]) && $reward[$id]=='1')
			{
				$arrRet[] = $id;
			}
		}
		return $arrRet;
	}

	private function setRewardId($id)
	{
		$user = EnUser::getUserObj();
		$reward = $user->getSpendReward();
		$len = strlen($reward);
		if ($id >= $len)
		{
			$reward .= str_repeat('0', $id - $len + 1);
		}
		$reward[$id] = '1';
		$user->setSpendReward($reward);
	}

	/* (non-PHPdoc)
	 * @see ISpend::getInfo()
	 */
	public function getInfo ()
	{
		$arrConfig = $this->getConfig();

		$arrRet = array('ret'=>'ok', 'res'=>array());
		if (empty($arrConfig))
		{
			$arrRet['ret'] = 'over';
			return $arrRet;
		}

		$cfg0 = current($arrConfig);
		$beginTime = strtotime($cfg0['begin_time']);
		$arrRet['res']['gold_accum'] = $this->getSpendGold($beginTime);

		$arrRet['res']['reward'] = array();
		$user = EnUser::getUserObj();
		$reward = $user->getSpendReward();
		$arrRet['res']['reward'] = $this->getSpendReward($arrConfig);

		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see ISpend::getReward()
	 */
	public function getReward ($id)
	{
		$arrRet = array('ret'=>'ok', 'res'=>array());

		$arrConfig = $this->getConfig();
		if (empty($arrConfig))
		{
			$arrRet['ret'] = 'over';
			return $arrRet;
		}

		//检查id
		if (!isset($arrConfig[$id]))
		{
			Logger::warning('fail get spend reward, the id is not exist');
			throw new Exception('fake');
		}

		//id是否已经领取
		$user = EnUser::getUserObj();
		$reward = $this->getSpendReward($arrConfig);
		if (in_array($id, $reward))
		{
			Logger::warning('fail get spend reward, the id %d is rewarded ', $id);
			throw new Exception('fake');
		}
		//保存id
		$this->setRewardId($id);

		$cfg = $arrConfig[$id];
		$beginTime = strtotime($cfg['begin_time']);
		$spendGold = $this->getSpendGold($beginTime);
		if ($spendGold < $cfg['cost'])
		{
			Logger::warning('fail to get spend reward, spend gold is not enough');
			throw new Exception('fake');
		}

		//belly
		$user->addBelly($cfg['reward']['belly']);
		$arrRet['res']['belly'] = $cfg['reward']['belly'];

		//行动力
		$user->addExecution($cfg['reward']['execution']);
		$arrRet['res']['execution'] = $cfg['reward']['execution'];

		//阅历
		$user->addExperience($cfg['reward']['experiece']);
		$arrRet['res']['experiece'] = $cfg['reward']['experiece'];

		//物品
		$bag = null;
		$arrItem = $cfg['reward']['item']->toArray();
		if (!empty($arrItem))
		{
			$arrItemId = ItemManager::getInstance()->addItems($arrItem);
			$tmpItem = ChatTemplate::prepareItem($arrItemId);
			$bag = BagManager::getInstance()->getBag();
			if (!$bag->addItems($arrItemId))
			{
				$arrRet['ret'] = 'full';
				return $arrRet;
			}
		}

		//寻宝积分
		EnTreasure::addScore($cfg['treasure']['red_score'], $cfg['treasure']['purple_score']);
		$arrRet['treasure'] = $cfg['treasure']->toArray();

		//装备制作积分
		EnSmelting::addIntegral($cfg['arming_produce']['red_score'], $cfg['arming_produce']['purple_score']);
		$arrRet['arming_produce'] = $cfg['arming_produce']->toArray();

		if ($bag!=null)
		{
			$arrRet['res']['grid'] = $bag->update();
			ChatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $tmpItem);
		}
		else
		{
			$arrRet['res']['grid'] = array();
		}
		$user->update();

		$arrRet['res']['energy'] = $cfg['reward']['energy'];
		$arrRet['res']['element'] = $cfg['reward']['element'];
		Jewelry::addEnergyElement($user->getUid(),$cfg['reward']['energy'],$cfg['reward']['element']);
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */