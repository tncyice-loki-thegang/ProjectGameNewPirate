<?php

class RideDao
{
	const tblName = 't_user_ride';
	
	public static function  insert($uid, $arrField)
	{
		$data = new CData();
		$arrField['uid'] = $uid;
		$data->insertInto(self::tblName)->values($arrField)->query();
	}
	
	public static function update($uid, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField)->where('uid', '=', $uid)->query();
	}
	
	public static function get($uid, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('uid', '=', $uid)->query();
		if (empty($ret))
		{
			return $ret;
		}
		return $ret[0];
	}
}
