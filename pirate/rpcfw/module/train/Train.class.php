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
 * Class       : Train
 * Description : 训练对外接口实现类
 * Inherit     : ITrain
 **********************************************************************************************************************/
class Train implements ITrain
{

	/* (non-PHPdoc)
	 * @see ITrain::getUserTrainInfo()
	 */
	public function getUserTrainInfo()
	{
		Logger::debug('Train::getUserTrainInfo Start.');
		// 获取用户英雄训练信息
		$ret = TrainLogic::getUserTrainInfo();
		Logger::debug('Train::getUserTrainInfo End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITrain::openTrainSlot()
	 */
	public function openTrainSlot() 
	{
		Logger::debug('Train::openTrainSlot Start.');
		// 开启新的训练位
		$ret = TrainLogic::openTrainSlot();
		Logger::debug('Train::openTrainSlot End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITrain::rapid()
	 */
	public function rapid($heroID) 
	{
		// 检查参数
		if ($heroID <= 0)
		{
			Logger::fatal('Err para, %d!', $heroID);
			throw new Exception('fake');
		}
		Logger::debug('Train::rapid Start.');
		// 突飞
		$ret = TrainLogic::rapid($heroID, 1);
		Logger::debug('Train::rapid End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITrain::rapidByTimes()
	 */
	public function rapidByTimes($heroID, $times) 
	{
		// 检查参数
		if ($heroID <= 0 || $times <= 0)
		{
			Logger::fatal('Err para, %d!, %d.', $heroID, $times);
			throw new Exception('fake');
		}
		Logger::debug('Train::rapid Start.');
		// 突飞
		$ret = TrainLogic::rapid($heroID, $times);
		Logger::debug('Train::rapid End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITrain::rapidByGold()
	 */
	public function rapidByGold($heroID) 
	{
		// 检查参数
		if ($heroID <= 0)
		{
			Logger::fatal('Err para, %d!', $heroID);
			throw new Exception('fake');
		}
		Logger::debug('Train::rapidByGold Start.');
		// 金币突飞
		$ret = TrainLogic::rapidByGold($heroID);
		Logger::debug('Train::rapidByGold End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITrain::startTrain()
	 */
	public function startTrain($heroID, $mode, $time) 
	{
		// 检查参数
		if ($heroID <= 0)
		{
			Logger::fatal('Err para, %d!', $heroID);
			throw new Exception('fake');
		}
		Logger::debug('Train::startTrain Start.');
		// 训练
		$ret = TrainLogic::train($heroID, $mode, $time);
		Logger::debug('Train::startTrain End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITrain::stopTrain()
	 */
	public function stopTrain($heroID) 
	{
		// 检查参数
		if ($heroID <= 0)
		{
			Logger::fatal('Err para, %d!', $heroID);
			throw new Exception('fake');
		}
		Logger::debug('Train::stopTrain Start.');
		// 停止训练
		$ret = TrainLogic::stopTrain($heroID);
		Logger::debug('Train::stopTrain End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITrain::getCDTime()
	 */
	public function getCDTime() 
	{
		Logger::debug('Train::getCDTime Start.');
		// 获取CD时间
		$ret = TrainLogic::getCDTime();
		Logger::debug('Train::getCDTime End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITrain::clearCDByGold()
	 */
	public function clearCDByGold() 
	{
		Logger::debug('Train::clearCDByGold Start.');
		// 清除CD时间
		$ret = TrainLogic::clearCDByGold();
		Logger::debug('Train::clearCDByGold End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITrain::changeTrainMode()
	 */
	public function changeTrainMode($heroID, $mode, $time) 
	{
		// 检查参数
		if ($heroID <= 0)
		{
			Logger::fatal('Err para, %d!', $heroID);
			throw new Exception('fake');
		}
		Logger::debug('Train::changeTrainMode Start.');
		// 更换训练模式
		$ret = TrainLogic::changeTrainMode($heroID, $mode, $time);
		Logger::debug('Train::changeTrainMode End.');

		return $ret;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */