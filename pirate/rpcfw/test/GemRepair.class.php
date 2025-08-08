<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GemRepair.class.php 25705 2012-08-15 08:37:09Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/GemRepair.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-08-15 16:37:09 +0800 (三, 2012-08-15) $
 * @version $Revision: 25705 $
 * @brief
 *
 **/

class GemRepair extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if ( count($arrOption) != 1 )
		{
			echo "invalid item_id!\n";
			return;
		}
		$item_id = intval($arrOption[0]);

		$im_update = FALSE;
		$item_obj = ItemManager::getInstance()->getItem($item_id);
		if ( $item_obj !== NULL && $item_obj->getItemType() == ItemDef::ITEM_ARM )
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
				}
				if ( $modify == TRUE )
				{
					$im_update = TRUE;
					$item_obj->setItemText($item_text);
					var_dump($item_obj->itemInfo());
				}
			}
		}
		else
		{
			echo "invalid item_id:$item_id or invalid item_type\n";
			return;
		}

		if ( $im_update == TRUE )
		{
			ItemManager::getInstance()->update();
		}

		echo "done\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */