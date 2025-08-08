<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaDao.class.php 28951 2012-10-12 03:22:25Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/arena/ArenaDao.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-10-12 11:22:25 +0800 (五, 2012-10-12) $
 * @version $Revision: 28951 $
 * @brief 
 *  
 **/




class ArenaDao
{
	const tblName = 't_arena';
	const tblNameMsg = 't_arena_msg';
	
	public static function getCount()
	{
		$data = new CData();
		$arrRet = $data->selectCount()->from(self::tblName)->where('uid','>', '0')->query();
		return $arrRet[0]['count'];
	}
	
	public static function getArrInfo($arrUid, $arrField)
	{
		if (!in_array('uid', $arrField))
		{
			$arrField[] = 'uid';
		}
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::tblName)->where('uid', 'IN', $arrUid)->query();
		return Util::arrayIndex($arrRet, 'uid');
	}
	
	public static function getMaxPostion()
	{
		$data = new CData();
		$arrRet = $data->select(array('max(position)'))->from(self::tblName)->where('uid', '>', 0)->query();
		return $arrRet[0]['max(position)'];
	}
	
	public static function insert($uid, $arrField)
	{
		$data = new CData();
		$arrField['uid'] = $uid;
		$arrRet = $data->insertInto(self::tblName)->values($arrField)->query();
	}
	
	public static function get($uid, $arrField)
	{
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::tblName)->where('uid', '=', $uid)->query();
		if (!empty($arrRet))
		{
			return $arrRet[0];
		}
		return $arrRet;
	}
	
	public static function getByPos($pos, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('position', '=', $pos)->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;
	}
	
	public static function getArrByPos($arrPos, $arrField)
	{
		if (empty($arrPos))
		{
			return array();
		}
		
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('position', 'in', $arrPos)->query();
		return $ret;
	}
	
	/**
	 * 得到名次区间, 用于发奖
	 * 奖励时间小于reward_time
	 * Enter description here ...
	 * @param unknown_type $pos1
	 * @param unknown_type $pos2
	 * @param unknown_type $arrField
	 */
	public static function getPosRange4Reward($pos1, $pos2, $rewardTime, $arrField)
	{
		$data = new CData();
		$arrPos = range($pos1, $pos2);
		$whereRewardTime = array('reward_time', '<', $rewardTime);
		$ret = $data->select($arrField)->from(self::tblName)->where('position', 'in', $arrPos)
			->where($whereRewardTime)			
            ->orderBy('position', false)->query();
		return $ret;
	}
	
	
	public static function update($uid, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField)->where('uid', '=', $uid)->query();
	}
	
	public static function updateReward($uid)
	{
		$data = new CData();
		$strRewardTime ="-1" . ArenaDateConf::LAST_DAYS . "day";
		$rewardTime = strtotime($strRewardTime, Util::getTime()); 
		$ret = $data->update(self::tblName)->set(array('va_reward'=>array()))
			->where('uid', '=', $uid)
			->where('reward_time', '>', $rewardTime) //需要判断reward_time大于 当前时间-ArenaDateConf::LAST_DAY
			->query();
		if ($ret['affected_rows']!=1)
		{
			return false;
		}	
		return true;
	}

    public static function updateByPosition($position, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField)->where('position', '=', $position)->query();
	}

	public static function updateChallenge($info, $atkedInfo, $oldPositoin, $oldAtkPosition)
	{	
		$roundPos = rand(100000000, 200000000);		
		if ($oldPositoin > $oldAtkPosition)
		{
			$min = $atkedInfo;
			$max = $info;
		}
		else
		{
			$min = $info;
			$max = $atkedInfo;
		}
		
		$batchData = new BatchData();
		//位置小的放到tmp里面
		$tmpData = $batchData->newData();
		$tmpPos = $min['position'];
		$min['position'] = $roundPos;
		$tmpData->update(self::tblName)->set($min)
		  ->where('uid', '=', $min['uid'])->query();
		
		//跟新大的
		$dataOther = $batchData->newData();
		$dataOther->update(self::tblName)->set($max)
			->where('uid', '=', $max['uid'])->query();

		//更新小的
		$min['position'] = $tmpPos;		
		$dataInfo = $batchData->newData();
		$dataInfo->update(self::tblName)->set($min)
			->where('uid', '=', $min['uid'])->query();
		
		$batchData->query();
	}
	
	public static function insertMsg($arrField)
	{
		$data = new CData();
		$data->insertInto(self::tblNameMsg)->values($arrField)->query();
	}
	
	public static function getMsg($uid, $arrField, $num)
	{
		if (!isset($arrField['id']))
		{
			$arrField[] = 'id';
		}
		
		//atk msg
		$data = new CData();
		$atkRet = $data->select($arrField)->from(self::tblNameMsg)->
			where('attack_uid', '=', $uid)->orderBy('attack_time', false)->
			limit(0, $num)->query();
		
		
		$defRet = $data->select($arrField)->from(self::tblNameMsg)->
			where('defend_uid', '=', $uid)->orderBy('attack_time', false)->
			limit(0, $num)->query();
		
		//reverse cmp
		$rcmp = function  ($msg1, $msg2)
		{
			if ($msg1['id'] < $msg2['id'])
			{
				return 1;
			}
			//主键没有相当的情况
			return -1;
		};
		
		$arrMsg = array_merge($atkRet, $defRet);
		usort($arrMsg, $rcmp);
		
		$arrRet = array();
		$i = 0;
		$curMsg = current($arrMsg);
		while ($i<$num && $curMsg)
		{		
			$arrRet[] = $curMsg;
			$i++;
			$curMsg = next($arrMsg);
		}
		return $arrRet;
	}

    public static function getPositionList($num, $arrField)
    {
        $data = new CData();
        $ret = $data->select($arrField)->from(self::tblName)
            ->where('position', '<=', $num)->query();
        $ret = Util::arrayIndex($ret, "uid");
        return $ret;
    }
    
    public static function getTop($offset, $limit, $arrField)
    {
    	$data = new CData();
    	$arrRet = $data->select($arrField)->from(self::tblName)
    		->where('uid', '>', '0')->orderBy('position', false)
    		->limit($offset, $limit)->query();
    	return $arrRet;
    }
    
	public static function getDefeatedNotice($uid, $time, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblNameMsg)
			->where('attack_time', '>', $time)
			->where('defend_uid', '=', $uid)
			->where('attack_res', '=', 1)	
			->orderBy('attack_time', false)
			->limit(0, CData::MAX_FETCH_SIZE)
			->query();
		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */