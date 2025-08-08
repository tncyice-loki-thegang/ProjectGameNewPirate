<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: drop.validate.script.php 21135 2012-05-23 11:48:16Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/drop.validate.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-23 19:48:16 +0800 (三, 2012-05-23) $
 * @version $Revision: 21135 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Drop.def.php";

$drops = btstore_get()->DROPITEM->toArray();

foreach ( $drops as $drop_id => $drop_info )
{
	$drop_list_num = $drop_info[DropDef::DROP_LIST_NUM];
	for ( $i = 0; $i < $drop_list_num; $i++ )
	{
		if ( !isset(btstore_get()->ITEMS[$drop_info[DropDef::DROP_LIST][$i][DropDef::DROP_ITEM_TEMPLATE_ID]]) )
		{
			echo "DROP $drop_id list $i invalid item_id" .
				$drop_info[DropDef::DROP_LIST][$i][DropDef::DROP_ITEM_TEMPLATE_ID] . "\n";
		}

		if ( empty($drop_info[DropDef::DROP_LIST][$i][DropDef::DROP_ITEM_NUM]) )
		{
			echo "DROP $drop_id list $i invalid item_num" .
				$drop_info[DropDef::DROP_LIST][$i][DropDef::DROP_ITEM_NUM] .
				"with item_id:" . $drop_info[DropDef::DROP_LIST][$i][DropDef::DROP_ITEM_TEMPLATE_ID] . "\n";
		}

		if ( empty($drop_info[DropDef::DROP_LIST][$i][DropDef::DROP_WEIGHT]) )
		{
			echo "DROP $drop_id list $i invalid weight" .
				$drop_info[DropDef::DROP_LIST][$i][DropDef::DROP_WEIGHT] .
				"with item_id:" . $drop_info[DropDef::DROP_LIST][$i][DropDef::DROP_ITEM_TEMPLATE_ID] . "\n";
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */