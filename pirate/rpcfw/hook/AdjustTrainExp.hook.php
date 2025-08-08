<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AdjustTrainExp.hook.php 16415 2012-03-14 02:48:44Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/hook/AdjustTrainExp.hook.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:48:44 +0800 (三, 2012-03-14) $
 * @version $Revision: 16415 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : AdjustTrainExp
 * Description : 伙伴训练Hook实现类
 * Inherit     :
 **********************************************************************************************************************/
class AdjustTrainExp
{
	/**
	 * 执行hook， 调整正在训练的伙伴等级
	 * 
	 * @param array $arrRet
	 */
	function execute($arrRet)
	{
		Logger::debug("Train Hook begin");

		// 检查并调整伙伴训练经验
		EnTrain::checkHeroTrainExp();

		Logger::debug("Train Hook end");
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */