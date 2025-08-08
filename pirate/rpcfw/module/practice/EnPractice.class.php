<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnPractice.class.php 18817 2012-04-18 07:05:16Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/practice/EnPractice.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-04-18 15:05:16 +0800 (三, 2012-04-18) $
 * @version $Revision: 18817 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnPractice
 * Description : 挂机内部接口类
 * Inherit     : 
 **********************************************************************************************************************/
class EnPractice
{
	/**
	 * 人物升级，需要改变挂机效率
	 * 
	 * @param int $lv							用户等级
	 */
	public static function changePracticeEfficiency($lv)
	{
		// 只有开启了此功能才做这件事
		if (EnSwitch::isOpen(SwitchDef::PRACTISE))
		{
			// 调用方法，修改经验
			MyPractice::getInstance()->changeLv($lv);
			MyPractice::getInstance()->save();
		}
	}

	/**
	 * 获取人物挂机剩余时刻
	 */
	public static function getPracticeLastSec()
	{
		// 初始化剩余时刻，如果没开启，则返回-1
		$ret = -1;
		// 如果开启了功能，则查看剩余时刻
		if (EnSwitch::isOpen(SwitchDef::PRACTISE))
		{
			// 查询是否正在挂机
			$ret = MyPractice::getInstance()->isPracticing();
			// 已经超出挂机时刻了，返回0
			if ($ret === false)
			{
				$ret = 0;
			}
		}
		// 返回剩余时刻
		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */