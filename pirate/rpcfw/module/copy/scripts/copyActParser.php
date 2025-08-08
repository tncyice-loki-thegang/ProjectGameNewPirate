<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: copyActParser.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/scripts/copyActParser.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

class copyActParser extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$csvFile = $arrOption [0];
		$btstoreFile = $arrOption [1];
		$parser = new ActivityParser ();
		$arrData = $parser->parse ( $csvFile . '/copy_act.csv' );
		$data = serialize ( $arrData );
		file_put_contents ( $btstoreFile . '/COPY_ACT', $data );
	}

}

require_once (LIB_ROOT . '/AbstractParser.class.php');

class ActColumn
{
	/**
	 * 活动id
	 * @var int
	 */
	const ID = 0;

	/**
	 * 活动名称
	 * @var string
	 */
	const NAME = 1;

	/**
	 * 活动开始时刻
	 * @var string
	 */
	const YEAR_START = 2;

	/**
	 * 活动结束时刻
	 * @var string
	 */
	const YEAR_END = 3;

	/**
	 * 活动开始日期
	 * @var array
	 */
	const DAY_LIST = 4;

	/**
	 * 活动开始星期
	 * @var array
	 */
	const WEEK_LIST = 5;

	/**
	 * 每天活动结束时刻
	 * @var string
	 */
	const DAY_START = 6;

	/**
	 * 每天活动结束时刻
	 * @var string
	 */
	const DAY_END = 7;

	/**
	 * 刷新点个数权重数组
	 * @var array
	 */
	const RP_NUM = 8;

	/**
	 * 刷新点数组
	 * @var array
	 */
	const RP_ARRAY = 9;

	/**
	 * 刷新间隔
	 * @var int
	 */
	const INTERVAL = 10;

	/**
	 * 广播频道
	 * @var int
	 */
	const BROADCAST_CHANNEL = 11;

	/**
	 * 广播内容
	 * @var string
	 */
	const BROADCAST_DETAIL = 12;

	/**
	 * 随机部队数组
	 * @var array
	 */
	const ARMY_ARRAY = 13;
	
	/**
	 * 最后一项 + 1
	 * @var int
	 */
	const ALL_COL = 14;
}

class ActivityParser extends AbstractParser
{
	protected function getColumnCount()
	{
		// 返回一共有多少项目
		return ActColumn::ALL_COL;
	}

	/* (non-PHPdoc)
	 * @see AbstractParser::parseRow()
	 */
	protected function parseRow($arrRow, $rowCounter)
	{
		$arrRet = array ();
		$arrRet['id'] = intval( $arrRow[ActColumn::ID] );
		$arrRet['name'] = $arrRow[ActColumn::NAME];
		// 开始时刻
		if (!empty($arrRow[ActColumn::YEAR_START]))
		{
			$yStart = array();
			$yStart['year'] = substr($arrRow[ActColumn::YEAR_START], 0, 4);
			$yStart['mon'] = substr($arrRow[ActColumn::YEAR_START], 4, 2);
			$yStart['day'] = substr($arrRow[ActColumn::YEAR_START], 6, 2);
			$yStart['hour'] = substr($arrRow[ActColumn::YEAR_START], 8, 2);
			$yStart['min'] = substr($arrRow[ActColumn::YEAR_START], 10, 2);
			$yStart['sec'] = substr($arrRow[ActColumn::YEAR_START], 12, 2);
			$arrRet['year_start'] = $yStart;
		}
		else 
		{
			$arrRet['year_start'] = false;
		}
		// 结束时刻
		if (!empty($arrRow[ActColumn::YEAR_END]))
		{
			$yEnd = array();
			$yEnd['year'] = substr($arrRow[ActColumn::YEAR_END], 0, 4);
			$yEnd['mon'] = substr($arrRow[ActColumn::YEAR_END], 4, 2);
			$yEnd['day'] = substr($arrRow[ActColumn::YEAR_END], 6, 2);
			$yEnd['hour'] = substr($arrRow[ActColumn::YEAR_END], 8, 2);
			$yEnd['min'] = substr($arrRow[ActColumn::YEAR_END], 10, 2);
			$yEnd['sec'] = substr($arrRow[ActColumn::YEAR_END], 12, 2);
			$arrRet['year_end'] = $yEnd;
		}
		else 
		{
			$arrRet['year_end'] = false;
		}

		if (!empty($arrRow[ActColumn::DAY_LIST]))
		{
			$arrRet['day_list'] = explode(',', $arrRow[ActColumn::DAY_LIST]);
		}
		else 
		{
			$arrRet['day_list'] = false;
		}
		if (!empty($arrRow[ActColumn::WEEK_LIST]))
		{
			$arrRet['week_list'] = explode(',', $arrRow[ActColumn::WEEK_LIST]);
		}
		else 
		{
			$arrRet['week_list'] = false;
		}
		// 每天开始时刻
		$dStart = array();
		$dStart['hour'] = substr($arrRow[ActColumn::DAY_START], 0, 2);
		$dStart['min'] = substr($arrRow[ActColumn::DAY_START], 2, 2);
		$dStart['sec'] = substr($arrRow[ActColumn::DAY_START], 4, 2);
		$arrRet['day_start'] = $dStart;
		// 每天结束时刻
		$dEnd = array();
		$dEnd['hour'] = substr($arrRow[ActColumn::DAY_END], 0, 2);
		$dEnd['min'] = substr($arrRow[ActColumn::DAY_END], 2, 2);
		$dEnd['sec'] = substr($arrRow[ActColumn::DAY_END], 4, 2);
		$arrRet['day_end'] = $dEnd;
		// 获取个数的权重
		$rpNumArr = explode(',', $arrRow[ActColumn::RP_NUM]);
		$rpNumTmp = array();
		foreach ($rpNumArr as $rpNum)
		{
			$pair = explode('|', $rpNum);
			$rpNumTmp[] = $pair[1];
		}
		$arrRet['rp_num'] = $rpNumTmp;
		// 刷新点权重
		$rpArr = explode(',', $arrRow[ActColumn::RP_ARRAY]);
		$rpArrTmp = array();
		$rpArrWeightTmp = array();
		foreach ($rpArr as $rp)
		{
			$pair = explode('|', $rp);
			$rpArrTmp[] = $pair[0];
			$rpArrWeightTmp[] = $pair[1];
		}
		$arrRet['rp_array'] = $rpArrTmp;
		$arrRet['rp_array_weight'] = $rpArrWeightTmp;
		// 刷新点间隔
		$arrRet['interval'] = intval( $arrRow[ActColumn::INTERVAL] );
		// 刷新点部队权重
		$armyArr = explode(',', $arrRow[ActColumn::ARMY_ARRAY]);
		$armyArrTmp = array();
		$armyArrWeightTmp = array();
		foreach ($armyArr as $army)
		{
			$pair = explode('|', $army);
			$armyArrTmp[] = $pair[0];
			$armyArrWeightTmp[] = $pair[1];
		}
		$arrRet['army_array'] = $armyArrTmp;
		$arrRet['army_array_weight'] = $armyArrWeightTmp;
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see AbstractParser::validate()
	 */
	protected function validate($arrConfig)
	{
		// 用ID作为KEY，重新排列数组
		$arrConfig = Util::arrayIndex($arrConfig, 'id');
		// 计算每个副本中的活动
		$copyAct = array();
		// 循环所有活动
		foreach ($arrConfig as $act)
		{
			// 此活动都在那些副本里
			$copyAct = self::sortCopyAct($act['id'], $act['rp_array'], $copyAct);
		}
		// 记录到总数据里
		$arrConfig['copy_act'] = $copyAct;
		return $arrConfig;
	}

	// 此活动都在那些副本里
	private function sortCopyAct($actID, $rpArr, $copyActList)
	{
		foreach ($rpArr as $rpID)
		{
			// 通过刷新点ID获取副本ID
			$copyID = btstore_get()->REFRESH_POINT[$rpID]['copy_id'];
			// 如果此活动尚未存在数组里
			if (!isset($copyActList[$copyID]) || !in_array($actID, $copyActList[$copyID]))
			{
				// 记录下
				$copyActList[$copyID][] = $actID;
			}
		}
		return $copyActList;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */