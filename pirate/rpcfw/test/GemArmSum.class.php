<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GemArmSum.class.php 27550 2012-09-20 07:39:36Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/GemArmSum.class.php $
 * @author $Author: HaidongJia $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-20 15:39:36 +0800 (四, 2012-09-20) $
 * @version $Revision: 27550 $
 * @brief
 *
 **/

class GemArmSum extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if ( count($arrOption) != 1 )
		{
			echo "invalid args!\n";
			return;
		}

		$filename = $arrOption[0];

		if ( file_exists($filename) == FALSE )
		{
			echo "invalid file name!\n";
			return;
		}

		$file = fopen($filename, 'r');

		if ( $file == FALSE )
		{
			echo "open file failed!\n";
			return;
		}

		for ( $i = 0; $i < 65535; $i++)
		{
			$line = fgets($file);
			if ( $line === FALSE )
			{
				break;
			}
			$uid = intval($line);
			if ( $uid == 0 )
			{
				break;
			}


			$gemArray = array();
			$itemIds = BagDAO::selectBag(array(BagDef::BAG_ITEM_ID, BagDef::BAG_GID),
				 array(BagDef::BAG_UID, '=', $uid));


			foreach ($itemIds as $item)
			{
				if ( $item[BagDef::BAG_ITEM_ID] == BagDef::ITEM_ID_NO_ITEM )
				{
					continue;
				}

				$item_obj = ItemManager::getInstance()->getItem($item[BagDef::BAG_ITEM_ID]);
				if ( $item_obj->getItemType() == ItemDef::ITEM_ARM )
				{
					$item_text = $item_obj->getItemText();
					if ( isset($item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) )
					{
						foreach ( $item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE] as $hole_id => $gem_item_id )
						{
							if ( !empty($gem_item_id) )
							{
								$gem_item = ItemManager::getInstance()->getItem($gem_item_id);
								if ( $gem_item !== NULL )
								{
									$gem_item_template_id = $gem_item->getItemTemplateID();
									if ( !isset($gemArray[$gem_item_template_id]) )
									{
										$gemArray[$gem_item_template_id] = array(
											'quality' => $gem_item->getItemQuality(),
											'num' => 1,
										);
									}
									else
									{
										$gemArray[$gem_item_template_id]['num']++;
									}
								}
							}
						}
					}
				}
				else if ( $item_obj->getItemType() == ItemDef::ITEM_GEM )
				{
					$gem_item = ItemManager::getInstance()->getItem($item[BagDef::BAG_ITEM_ID]);
					if ( $gem_item !== NULL )
					{
						$gem_item_template_id = $gem_item->getItemTemplateID();
						if ( !isset($gemArray[$gem_item_template_id]) )
						{
							$gemArray[$gem_item_template_id] = array(
								'quality' => $gem_item->getItemQuality(),
								'num' => 1,
							);
						}
						else
						{
							$gemArray[$gem_item_template_id]['num']++;
						}
					}
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
					$item_text = $item_obj->getItemText();
					if ( isset($item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE]) )
					{
						foreach ( $item_text[ItemDef::ITEM_ATTR_NAME_ARM_ENCHANSE] as $hole_id => $gem_item_id )
						{
							if ( !empty($gem_item_id) )
							{
								$gem_item = ItemManager::getInstance()->getItem($gem_item_id);
								if ( $gem_item !== NULL )
								{
									$gem_item_template_id = $gem_item->getItemTemplateID();
									if ( !isset($gemArray[$gem_item_template_id]) )
									{
										$gemArray[$gem_item_template_id] = array(
											'quality' => $gem_item->getItemQuality(),
											'num' => 1,
										);
									}
									else
									{
										$gemArray[$gem_item_template_id]['num']++;
									}
								}
							}
						}
					}
				}
			}

			echo $uid . "\n";
			echo json_encode($gemArray) . "\n";
		}
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */