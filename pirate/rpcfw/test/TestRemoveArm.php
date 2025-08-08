<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TestRemoveArm.php 29560 2012-10-16 06:29:40Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestRemoveArm.php $
 * @author $Author: HaidongJia $(lanhongyu@babeltime.com)
 * @date $Date: 2012-10-16 14:29:40 +0800 (二, 2012-10-16) $
 * @version $Revision: 29560 $
 * @brief
 *
 **/

class TestRemoveArm extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$uid = intval($arrOption[0]);
		if ( $uid == 0 )
		{
			echo "invalid uid!\n";
			return;
		}

		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);

		$arrArm = array();
		$arrGem = array();
		$itemIds = BagDAO::selectBag(array(BagDef::BAG_ITEM_ID, BagDef::BAG_GID),
			 array(BagDef::BAG_UID, '=', $uid));


		foreach ($itemIds as $item)
		{
			if ( $item[BagDef::BAG_ITEM_ID] == BagDef::ITEM_ID_NO_ITEM )
			{
				continue;
			}

			$item_obj = ItemManager::getInstance()->getItem($item[BagDef::BAG_ITEM_ID]);
			if (in_array($item[BagDef::BAG_ITEM_ID], $arrArm) || $item_obj === NULL)
			{
				$values = array(
					BagDef::BAG_ITEM_ID => BagDef::ITEM_ID_NO_ITEM,
					BagDef::BAG_GID => $item[BagDef::BAG_GID],
					BagDef::BAG_UID => $uid
				);
				Logger::fatal('del item id %d in gid %d',
					 $item[BagDef::BAG_ITEM_ID], $item[BagDef::BAG_GID]);
				//BagDAO::insertOrupdateBag($values);
			}
			else
			{
				$arrArm[] = $item[BagDef::BAG_ITEM_ID];
			}
		}

		$arrRet = HeroDao::getHeroesByUid($uid, array('uid', 'htid', 'hid', 'va_hero'));


		foreach ($arrRet as $htid=>$hero)
		{
			foreach ($hero['va_hero']['arming'] as $position => $itemId)
			{
				if ( $itemId == ItemDef::ITEM_ID_NO_ITEM )
				{
					continue;
				}

				$item_obj = ItemManager::getInstance()->getItem($itemId);
				if (in_array($itemId, $arrArm) || $item_obj === NULL)
				{
					$va_hero = $hero['va_hero'];
					$va_hero['arming'][$position] = 0;
					Logger::fatal('del item id %d in position %d , hero hid %d',
						$itemId, $position, $hero['hid']);

					//HeroDao::update($hero['hid'], array('va_hero'=>$va_hero));
				}
				else
				{
					$arrArm[] = $itemId;
				}
			}
		}

		$data = new CData();
		$arrRet = $data->select(array('uid', 'exploreId', 'va_explore'))
			->from('t_explore')->where('uid', '=', $uid)->query();
		foreach ($arrRet as $ret)
		{
			$is_modify = FALSE;
			$va_explore = $ret['va_explore'];
			$explore_id = $ret['exploreId'];
			$arrItem = $va_explore['items'];
			foreach ($arrItem as $key => $item_id)
			{
				$item = ItemManager::getInstance()->getItem($item_id);
				if ( $item == NULL )
				{
					Logger::FATAL('invalid item_id:%d in explore id:%d!', $item_id, $explore_id);
					unset($va_explore['items'][$key]);
					$is_modify = TRUE;
				}
			}

			if ($is_modify)
			{
				$va_explore['items'] = array_merge($va_explore['items']);
				//ExploreDao::update($ret['uid'], $ret['exploreId'], array('va_explore' => $va_explore));
			}
		}

		$im_update = FALSE;
		foreach ($arrArm as $item_id)
		{
			if ( $item_id != ItemDef::ITEM_ID_NO_ITEM )
			{
				$item_obj = ItemManager::getInstance()->getItem($item_id);
				if ( $item_obj->getItemType() == ItemDef::ITEM_ARM )
				{
					$item_text = $item_obj->getItemText();

					if ( isset($item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) )
					{
						$modify = FALSE;
						foreach ( $item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE] as $hole_id => $gem_item_id )
						{
							if ( !empty($gem_item_id) )
							{
								$gem_item = ItemManager::getInstance()->getItem($gem_item_id);
								if ( $gem_item === NULL )
								{
									Logger::FATAL('del gem_item:%d in arm item:%d!',
										 $gem_item_id, $item_id);
									unset($item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE][$hole_id]);
									$modify = TRUE;
								}
							}
							else
							{
								$arrGem[] = $gem_item_id;
							}
						}
						if ( $modify == TRUE )
						{
							$im_update = TRUE;
							$item_obj->setItemText($item_text);
							var_dump($item_obj->itemInfo());
						}
					}
				}
			}
		}

		if ( $im_update == TRUE )
		{
			ItemManager::getInstance()->update();
		}

		var_dump(join(',',$arrArm));
		var_dump(join(',',$arrGem));
		echo "done\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
