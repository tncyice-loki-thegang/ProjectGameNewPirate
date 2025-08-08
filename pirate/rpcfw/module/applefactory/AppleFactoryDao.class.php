<?php

class AppleFactoryDao
{
	const tblName = 't_apple_factory';

	public static function get($uid, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('uid', '=', $uid)->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;
	}

	public static function insert($uid, $arrField)
	{
		$arrField['uid'] = $uid;
		$data = new CData();
		$data->insertInto(self::tblName)->values($arrField)->query();
	}

	public static function update($uid, $values)
	{
		$data = new CData();
		$data->update(self::tblName)->set($values)->where('uid', '=', $uid)->query();
	}
}