<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestRepairBag.php 22315 2012-06-13 05:50:08Z HaidongJia $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestRepairBag.php $
 * @author $Author: HaidongJia $(hoping@babeltime.com)
 * @date $Date: 2012-06-13 13:50:08 +0800 (三, 2012-06-13) $
 * @version $Revision: 22315 $
 * @brief
 *
 **/
class TestRepairBag extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		if ( count($arrOption) != 3 )
		{
			echo "need args:: uid, from_gid, to_gid\n";
			return;
		}

		$uid = intval($arrOption[0]);
		$from_gid = intval($arrOption[1]);
		$to_gid = intval($arrOption[2]);
		if ( $uid == 0 )
		{
			echo "invalid uid!\n";
			return;
		}
		if ( $from_gid == 0 )
		{
			echo "invalid from_gid!\n";
			return;
		}
		if ( $to_gid == 0 )
		{
			echo "invalid to_gid!\n";
			return;
		}

		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);

		for ( $gid = $from_gid; $gid <= $to_gid; $gid++ )
		{
			$data = new CData ();
			$arrRet = $data->select(array('item_id'))->from( 't_bag' )->where ( 'gid', '=',
					$gid )->where ( 'uid', '=', $uid )->query ();
			if ( count($arrRet) != 1 )
			{
				echo "invalid gid!\n";
				return;
			}

			$item_id = $arrRet[0]['item_id'];
			Logger::FATAL('Remove Bag item:%d in gid:%d, user:%d', $item_id, $gid, $uid);

			$data = new CData ();
			$arrRet = $data->update ( 't_bag' )->set ( array ('item_id' => 0 ) )->where ( 'gid', '=',
					$gid )->where ( 'uid', '=', $uid )->query ();
			var_dump ( $arrRet );
		}
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */