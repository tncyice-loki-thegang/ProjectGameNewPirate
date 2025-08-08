<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */


class SpringFestivalWelfareDao
{
	static protected $FieldName = array('day','day_time','recieve');
	static protected $SprFestTableName = 't_reward_sprfestwelfare';
	
	
	/**
	 * 
	 * @param $fields	列名
	 * @return unknown_type
	 */
	static function getInfo($uid,$fields = 0)
	{
		$arr_fields = $fields;
		if(empty($arr_fields))
		{
			$arr_fields = self::$FieldName;
		}
		
		$data = new CData();
		$ret = $data->select($arr_fields)->from(self::$SprFestTableName)->where('uid','=',$uid)->query();
		if(isset($ret[0]))
		{
			$ret = $ret[0];
		}
		
		return $ret;
	}
	
	
	/**
	 * 更新福利数据
	 * @param $info
	 * @return unknown_type
	 */
	static function updateWelfare($uid,$info)
	{
		if(empty($info))
		{
			return false;
		}
		
		$data = new CData();	
		return $data->update(self::$SprFestTableName)->set($info)->where('uid','=',$uid)->query();	
	}
	
	
	/**
	 * 插入新年福利数据
	 * @param $info
	 * @return unknown_type
	 */
	static function insertWelfare($info)
	{
		if(empty($info))
		{
			return false;
		}
		
		$data = new CData();	
		return $data->insertIgnore(self::$SprFestTableName)->values($info)->query();
	}
	
}