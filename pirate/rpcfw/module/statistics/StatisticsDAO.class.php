<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: StatisticsDAO.class.php 25992 2012-08-21 01:58:00Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/statistics/StatisticsDAO.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-08-21 09:58:00 +0800 (二, 2012-08-21) $
 * @version $Revision: 25992 $
 * @brief
 *
 **/

class StatisticsDAO
{
	public static function insertOnline($values)
	{
		try {
			$data = new CData();
			$data->setServiceName(StatisticsDef::ST_STATISTICS_SERVICE_NAME);
			$data->useDb(StatisticsConfig::DB_NAME);
			$return = $data->insertInto(StatisticsDef::ST_TABLE_ONLINE_TIME)
				->values($values)->query(TRUE);
			if ( $return[DataDef::AFFECTED_ROWS] != 1 )
			{
				Logger::WARNING('insert on line statistics table failed!affect rows:%d',
					$return[DataDef::AFFECTED_ROWS]);
			}
		}
		catch(Exception $e)
		{
			Logger::WARNING('exception in statistics!:%s', $e->getMessage());
		}
	}

	public static function insertGold($values)
	{
		try {
			$data = new CData();
			$data->setServiceName(StatisticsDef::ST_STATISTICS_SERVICE_NAME);
			$data->useDb(StatisticsConfig::DB_NAME);
			$return = $data->insertInto(StatisticsDef::ST_TABLE_GOLD)
				->values($values)->query(TRUE);
			if ( $return[DataDef::AFFECTED_ROWS] != 1 )
			{
				Logger::WARNING('insert gold statistics table failed!affect rows:%d',
					$return[DataDef::AFFECTED_ROWS]);
			}
		}
		catch(Exception $e)
		{
			Logger::WARNING('exception in statistics!:%s', $e->getMessage());
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */