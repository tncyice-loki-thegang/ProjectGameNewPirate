<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Condition.class.php 5027 2011-09-20 02:22:43Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/condition/Condition.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2011-09-20 10:22:43 +0800 (二, 2011-09-20) $
 * @version $Revision: 5027 $
 * @brief
 *
 **/

class Condition
{
	/**
	 * 判断条件是否满足
	 *
	 * @param int $uid
	 * @param int $item
	 * @param int $condition_id
	 *
	 * @return boolean		TRUE表示条件满足, FALSE表示条件不满足
	 */
	public static function itemCondition($uid, $item, $condition_id)
	{
		$condition = btstore_get()->CONDITION[$condition_id];
		if ( empty($condition) )
		{
			Logger::FATAL("invalid condition ID:%d", $condition_id);
		}
		foreach ( $condition as $key => $value )
		{
			switch ( $key )
			{
				case ConditionDef::CONDITION_DELAY_TIME:
					$time = Util::getTime();
					if ( $time < $item->getItemTime() + $value )
						return FALSE;
					break;
				case ConditionDef::CONDITION_USER_LEVEL:
					//TODO need user module
					break;
				default:
					break;
			}
		}
		return TRUE;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */