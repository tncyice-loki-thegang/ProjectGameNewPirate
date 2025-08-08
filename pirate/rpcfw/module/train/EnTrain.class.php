<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnTrain
 * Description : 训练内部接口类
 * Inherit     : 
 **********************************************************************************************************************/
class EnTrain
{
	/**
	 * 添加一条新的训练室记录
	 * 
	 * @param int $uid							用户ID
	 */
	public static function addNewTrainInfoForUser($uid)
	{
		// 插入一个空白用户信息到数据库中
		return TrainDao::addNewTrainInfo($uid);
	}

	/**
	 * 判断英雄是否正在训练
	 * 
	 * @param int $hid							英雄ID
	 */
	public static function isTraining($hid)
	{
		// 获取人物训练信息
		$userTrainInfo = MyTrain::getInstance()->getUserTrainInfo();
		// 检查该英雄是否正在训练
		if (!isset($userTrainInfo['va_train_info'][$hid]))
		{
			return false;
		}
		return true;
	}

	/**
	 * 调整训练中的英雄经验
	 */
	public static function checkHeroTrainExp()
	{
		// 登陆时候没有uid，直接返回
		if (RPCContext::getInstance()->getUid() == 0)
		{
			return ;
		}
		// 如果已经开启了训练模块
		if (EnSwitch::isOpen(SwitchDef::TRAIN))
		{
			// 即时调整英雄训练经验
			TrainLogic::adjustTrainTime();
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */