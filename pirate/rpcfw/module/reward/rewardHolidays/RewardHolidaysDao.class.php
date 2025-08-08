<?php

class RewardHolidaysDao
{
	const tblName = 't_reward_summerholidays';
	
	public static function get($uid, $id, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('uid', '=', $uid)
			->where('id', '=', $id)->query();
		if (empty($ret))
		{
			return array();
		}
		return $ret[0];
	}
	
	public static function insert($uid, $id, $arrField)
	{
		$arrField['id'] = $id;
		$arrField['uid'] = $uid;
		$data = new CData();
		$data->insertInto(self::tblName)->values($arrField)->query();
	}
	
	public static function update($uid, $id, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField)->
			where('uid', '=', $uid)->where('id', '=', $id)->query();
	}
}