<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IDigActivity.class.php 37665 2013-01-30 11:08:05Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/digactivity/IDigActivity.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-01-30 19:08:05 +0800 (三, 2013-01-30) $
 * @version $Revision: 37665 $
 * @brief 
 *  
 **/


interface IDigActivity
{
	/**
	 * 获取当前用户挖宝的相关信息
	 * @return array
	 * <code>
	 * 		accumSpendGold	//从活动开始后的累计消费
	 * 		freeNum			//剩余免费挖宝次数
	 * 		leftNum			//剩余挖宝次数
	 * </code>
	 */
	public function getInfo();

	/**
	 * 当前用户挖宝
	 * @param int $type  类型。 1：使用消费金币和重置金币增加次数； 2：金币
	 * @param int $batchNum 批量次数
	 * 
	 * @return array
	 * <code>
	 * 		'grid' => 更新的背包信息
	 * 		'needGold' =>花费的金币
	 * 		drop=> array
	 * 				template_id => num
	 * </code>
	*/
	public function dig($type, $batchNum = 1);
}


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */