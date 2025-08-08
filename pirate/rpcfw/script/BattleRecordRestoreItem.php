<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: BattleRecordRestoreItem.php 31288 2012-11-20 03:14:27Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/BattleRecordRestoreItem.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-20 11:14:27 +0800 (二, 2012-11-20) $
 * @version $Revision: 31288 $
 * @brief
 *
 **/

/**
 *
 * 通过战斗录像恢复物品
 *
 * @example btscript BattleRecordRestoreItem.php 51846 娜芙亚琪娜 152568c29fd17eb2
 *
 * @tutorial	通过战斗录像恢复,首先从战斗录像中将所有的ARM和GEM存入列表,然后依次检查
 * 				背包,英雄,商店中存在的物品,将这些物品冲丢失的ARM和GEM列表中移除,然后依
 * 				次从丢失的ARM列表中将所丢失的物品依次通过邮件系统发回给用户(如果丢失ARM
 * 				上的GEM在丢失的GEM列表中,则移除),发送完毕后依次从丢失的GEM列表中将丢失
 * 				的GEM恢复
 *
 * @author pkujhd
 *
 */
class BattleRecordRestoreItem extends BaseScript
{
	/**
	 *
	 * 装备列表
	 * @var array
	 */
	var $m_arm_items = array();

	/**
	 *
	 * 宝石列表
	 * @var array
	 */
	var $m_gem_items = array();

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		//判断参数是否合法
		if ( count($arrOption) != 3 )
		{
			echo "invalid args:uid, uname, brid!";
			return;
		}

		$uid = intval($arrOption[0]);
		$uname = strval($arrOption[1]);
		$brid = strval($arrOption[2]);

		//得到录像中的所有物品
		if ( self::getBattleRecordItem($uid, $uname, $brid) == FALSE )
		{
			echo "invalid args: uid not in battle replay!";
			return;
		}

		//设置session中的global.uid
		RPCContext::getInstance()->setSession('global.uid', $uid);

		//删除背包里存在的物品
		$this->subItemInBag();
		//删除英雄身上存在的物品
		$this->subItemInHero();
		//删除商店里可以回购的物品
		$this->subItemInRepurchase();

		Logger::INFO('restore arm items:%s', $this->m_arm_items);
		Logger::INFO('restore gem items:%s', $this->m_gem_items);

		//发送物品
		$this->sendItems($uid);

		Logger::INFO('restore user:%d item end!', $uid);
	}

	/**
	 *
	 * 得到战斗录像中的物品列表.
	 *
	 * @param int $uid					用户uid
	 * @param string $uname				用户名称
	 * @param string $brid				战斗录像ID
	 *
	 * @return boolean					TRUE表示成功
	 */
	private function getBattleRecordItem($uid, $uname, $brid)
	{
		//解码brid
		$brid = BabelCrypt::decryptNumber($brid);

		//得到战斗录像
		$replay = BattleDao::getRecord ( $brid );

		//解码战斗录像
		$replay = Util::amfDecode($replay, TRUE);

		$team1 = $replay['team1'];
		$team2 = $replay['team2'];

		//判断是否当前用户在此录像中
		if ( $team1['uid'] == $uid && $team1['name'] == $uname )
		{
			$team = $team1;
		}
		else if ( $team2['uid'] == $uid && $team2['name'] == $uname )
		{
			$team = $team2;
		}
		else
		{
			return FALSE;
		}

		//得到用户英雄身上的物品
		$arrHero = $team['arrHero'];
		foreach ( $arrHero as $value )
		{
			foreach ( $value['equipInfo'] as $item_value )
			{
				if ( isset($item_value['item_id']) )
				{
					$this->m_arm_items[$item_value['item_id']] = $item_value;
					if ( isset($item_value['va_item_text']['arm_enchanse']) )
					{
						foreach ( $item_value['va_item_text']['arm_enchanse'] as $hole_id => $gem_item )
						{
							$this->m_gem_items[$gem_item['item_id']] = $gem_item;
						}
					}
				}
			}
		}
		return TRUE;
	}

	/**
	 *
	 * 从列表中移除背包中已经存在的物品
	 *
	 * @param NULL
	 *
	 * @return NULL
	 */
	private function subItemInBag()
	{
		$bag = BagManager::getInstance()->getBag();
		$bagInfo = $bag->BagInfo();
		Logger::INFO('subItemInBag start!');
		$this->__subItems($bagInfo[BagDef::USER_BAG]);
		$this->__subItems($bagInfo[BagDef::TMP_BAG]);
		$this->__subItems($bagInfo[BagDef::MISSION_BAG]);
		$this->__subItems($bagInfo[BagDef::DEPOT_BAG]);
		Logger::INFO('subItemInBag end!');
	}

	/**
	 *
	 * 从列表中移除英雄身上中已经存在的物品
	 *
	 * @param NULL
	 *
	 * @return NULL
	 */
	private function subItemInHero()
	{
		$heroObj = new Hero();
		$heroes = $heroObj->getRecruitHeroes();
		foreach ( $heroes as $hero )
		{
			Logger::INFO('subItemInHero hid:%d start!', $hero['hid']);
			$this->__subItems($hero['armingInfo']);
			Logger::INFO('subItemInHero hid:%d end!', $hero['hid']);
		}
	}

	/**
	 *
	 * 从列表中移除商店中可以回购的物品
	 *
	 * @param NULL
	 *
	 * @return NULL
	 */
	private function subItemInRepurchase()
	{
		$trade = new Trade();
		$repurchaseInfo = $trade->repurchaseInfo();
		$repurchaseInfo = Util::arrayExtract($repurchaseInfo, 'item_info');
		Logger::INFO('subItemInRepurchase start!');
		$this->__subItems($repurchaseInfo);
		Logger::INFO('subItemInRepurchase end!');
	}

	/**
	 *
	 * 从列表中移除已经存在的物品
	 *
	 * @param array $item_infos
	 *
	 * @return NULL
	 */
	private function __subItems($item_infos)
	{
		foreach ( $item_infos as $item_info )
		{
			//如果该位置上没有物品,则继续
			if ( empty($item_info) )
			{
				continue;
			}

			$item_id = $item_info['item_id'];

			//如果在丢失的ARM列表里,则移除
			if ( isset($this->m_arm_items[$item_id]) )
			{
				//由于装备的强化等级和潜能有可能修改,如果强化等级比原先的小,则采用原先的强化等级
				//如果潜能为空,则采用原先的潜能
				$this->updateArm($item_id, $this->m_arm_items[$item_id]['va_item_text']);
				unset($this->m_arm_items[$item_id]);
			}
			else
			{
				//如果在丢失的GEM列表中,则移除
				if ( isset($this->m_gem_items[$item_id]) )
				{
					//更新宝石的等级到原先的等级
					$this->updateGem($item_id, $this->m_gem_items[$item_id]['va_item_text']);
					unset($this->m_gem_items[$item_id]);
				}
			}
			//如果该ARM物品上有GEM,则将其从GEM列表中移除
			if ( isset($item_info['va_item_text']['arm_enchanse']) )
			{
				foreach ( $item_info['va_item_text']['arm_enchanse'] as $gem_item )
				{
					$gem_item_id = $gem_item['item_id'];
					if ( isset($this->m_gem_items[$gem_item_id]) )
					{
						//更新宝石的等级到原先的等级
						$this->updateGem($gem_item_id, $this->m_gem_items[$gem_item_id]['va_item_text']);
						unset($this->m_gem_items[$gem_item_id]);
					}
				}
			}
		}
	}

	/**
	 *
	 * 发送物品
	 *
	 * @param int $uid
	 *
	 * @return NULL
	 */
	private function sendItems($uid)
	{
		//在丢失中的ARM列表中循环
		foreach($this->m_arm_items as $item_id => $item_info)
		{
			//从系统中得到物品
			$item = ItemManager::getInstance()->getItem($item_id);
			//如果从系统中得不到物品,则物品已经被删除
			if ( $item === NULL )
			{
				//将物品从系统中恢复
				self::resetItem($item_id);
				$item = ItemManager::getInstance()->getItem($item_id);
				//如果无法恢复,则出错,并记录
				if ( $item  === NULL )
				{
					Logger::FATAL('item_id:%d is not in item system!', $item_id);
					unset($this->m_arm_items[$item_id]);
					continue;
				}
			}
			//如果该物品上的GEM存在于丢失列表中,则将GEM从丢失的GEM列表中移除
			if ( $item->noEnchase() == FALSE )
			{
				$gem_items = $item->getGemItems();
				foreach($gem_items as $hole_id => $gem_item_id)
				{
					//从系统中得到物品
					$gem_item_obj = ItemManager::getInstance()->getItem($gem_item_id);
					//如果从系统中得不到物品,则物品已经被删除
					if ( $gem_item_obj === NULL )
					{
						//将物品从系统中恢复
						self::resetItem($gem_item_id);
						$gem_item_obj = ItemManager::getInstance()->getItem($gem_item_id);
						//如果无法恢复,则出错,并记录
						if ( $gem_item_obj === NULL )
						{
							Logger::FATAL('item_id:%d is not in item system!', $gem_item_id);
							unset($this->m_gem_items[$gem_item_id]);
							continue;
						}
					}
					if ( isset($this->m_gem_items[$gem_item_id]) )
					{
						//更新宝石的等级到原先的等级
						$this->updateGem($gem_item_id, $this->m_gem_items[$gem_item_id]['va_item_text']);
						unset($this->m_gem_items[$gem_item_id]);
					}
				}
			}

			//由于装备的强化等级和潜能有可能修改,如果强化等级比原先的小,则采用原先的强化等级
			//如果潜能为空,则采用原先的潜能
			$this->updateArm($item_id, $this->m_arm_items[$item_id]['va_item_text']);
			//通过邮件将物品发回给用户
			self::sendMail($uid, array($item_id));
		}

		//如果所有的装备被恢复以后,仍然有宝石没有恢复,则通过邮件恢复
		foreach($this->m_gem_items as $item_id => $item_info)
		{
			//从系统中得到物品
			$item = ItemManager::getInstance()->getItem($item_id);
			//如果从系统中得不到物品,则物品已经被删除
			if ( $item === NULL )
			{
				//将物品从系统中恢复
				self::resetItem($item_id);
				$item = ItemManager::getInstance()->getItem($item_id);
				//如果无法恢复,则出错,并记录
				if ( $item === NULL )
				{
					Logger::FATAL('item_id:%d is not in item system!', $item_id);
					unset($this->m_gem_items[$item_id]);
					continue;
				}
			}

			//更新宝石的等级到原先的等级
			$this->updateGem($item_id, $this->m_gem_items[$item_id]['va_item_text']);
			//通过邮件将物品发回给用户
			self::sendMail($uid, array($item_id));
		}
	}

	/**
	 *
	 * 发送邮件
	 *
	 * @param int $uid						用户uid
	 * @param array(int) $item_ids			物品列表
	 *
	 * @return NULL
	 */
	private function sendMail($uid, $item_ids)
	{
		$subject = MailTiTleMsg::MAIL_ITEM_SENDER;
		$content = MailContentMsg::MAIL_ITEM_SENDER;

		Logger::INFO('MailItemDelivery::send mail to user:%d item ids:%s', $uid, $item_ids);
		MailLogic::sendSysItemMail($uid,
			MailConf::DEFAULT_TEMPLATE_ID, $subject, $content, $item_ids);
	}

	/**
	 *
	 * 更新ARM,如果物品的强化等级小于原先的强化等级,则设置为原先的强化等级
	 * 如果物品上没有潜能,则设置为原先的潜能
	 *
	 * @param int $item_id
	 * @param array $va_item_text
	 *
	 * @return NULL
	 */
	private function updateArm($item_id, $va_item_text)
	{
		//得到物品对象
		$item = ItemManager::getInstance()->getItem($item_id);

		//如果物品不是ARM,则直接退出
		if ( $item->getItemType() != ItemDef::ITEM_ARM )
		{
			return;
		}

		$item_text = $item->getItemText();
		//标记为未修改
		$modify = FALSE;

		//比较强化等级
		if ( $item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] <
			$va_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] )
		{
			$item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL] =
			$va_item_text[ItemDef::ITEM_ATTR_NAME_REINFORCE_LEVEL];
			$modify = TRUE;
		}
		//检查潜能是否为array()
		if ( empty($item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY]) )
		{
			foreach ( $va_item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY] as $attr_id => $attr_value )
			{
				$item_text[ItemDef::ITEM_ATTR_NAME_POTENTIALITY][$attr_id] = $attr_value *
				Potentiality::getPotentialityAttrValue($item->getRandPotentialityId(), $attr_id);
			}
			$modify = TRUE;
		}

		//如果发生修改,则写入
		if ( $modify == TRUE )
		{
			$item->setItemText($item_text);
			ItemManager::getInstance()->update();
		}
	}

	/**
	 *
	 * 更新GEM,将所有的宝石等级置为原先的等级
	 *
	 * @param int $item_id
	 * @param array $va_item_text
	 *
	 * @return NULL
	 */
	private function updateGem($item_id, $va_item_text)
	{
		//得到物品对象
		$item = ItemManager::getInstance()->getItem($item_id);

		//如果物品不是GEM,则直接退出
		if ( $item->getItemType() != ItemDef::ITEM_GEM )
		{
			return;
		}

		$item_text = $item->getItemText();
		//标记为未修改
		$modify = FALSE;

		//比较强化等级
		if ( $item_text[ItemDef::ITEM_ATTR_NAME_EXP] !=
			$va_item_text[ItemDef::ITEM_ATTR_NAME_EXP] )
		{
			$item_text[ItemDef::ITEM_ATTR_NAME_EXP] =
			$va_item_text[ItemDef::ITEM_ATTR_NAME_EXP];
			$modify = TRUE;
		}

		//如果发生修改,则写入
		if ( $modify == TRUE )
		{
			$item->setItemText($item_text);
			ItemManager::getInstance()->update();
		}
	}

	/**
	 *
	 * 重置物品
	 *
	 * @param int $item_id
	 *
	 * @return NULL
	 */
	private function resetItem($item_id)
	{
		$values = array (ItemDef::ITEM_SQL_ITEM_DELETED => 0);
		$where = array(ItemDef::ITEM_SQL_ITEM_ID, '=', $item_id);
		$return = ItemDAO::updateItem($where, $values);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */