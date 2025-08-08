<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: index.php 30734 2012-10-31 13:04:46Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/mergeServer/index.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-31 21:04:46 +0800 (三, 2012-10-31) $
 * @version $Revision: 30734 $
 * @brief
 *
 **/

require_once dirname ( __FILE__ ) . '/conf/SQLTable.conf.php';
require_once dirname ( __FILE__ ) . '/def/Common.def.php';
require_once dirname ( __FILE__ ) . '/def/Data.def.php';
require_once dirname ( __FILE__ ) . '/lib/Mysql.class.php';
require_once dirname ( __FILE__ ) . '/lib/MysqlManager.class.php';
require_once dirname ( __FILE__ ) . '/lib/partitionTable.class.php';
require_once dirname ( __FILE__ ) . '/lib/Util.class.php';
require_once dirname ( __FILE__ ) . '/module/MergeServer/MergeServer.class.php';
require_once dirname ( __FILE__ ) . '/module/MergeServer/Items.class.php';
require_once dirname ( __FILE__ ) . '/module/MergeServer/UserDao.class.php';
require_once dirname ( __FILE__ ) . '/module/MergeServer/GuildDao.class.php';
require_once dirname ( __FILE__ ) . '/module/MergeServer/CheckPresident.php';
require_once dirname ( __FILE__ ) . '/module/SQLModify/SQLModify.class.php';
require_once dirname ( __FILE__ ) . '/module/SQLModify/SQLModifyDAO.class.php';
require_once dirname ( __FILE__ ) . '/module/SQLModify/VACallback.class.php';

function print_usage()
{
	echo	"Usage:php MergeSever.php [options]:
				-mf		first merge server game id, eg:30001;
				-md		second merge server game id, eg:30002;
				-tg		target merge server game id, eg:30003;
				-td		target merge server dataproxy host, eg:192.168.3.26;
				-mp		multi process num: eg:1/2/4, default 1;
				-h		help list;
				-?		help list!\n";
}

function main()
{
	global $argc, $argv;

	$result = getopt('h:?', array('mf:', 'md:', 'tg:', 'td:', 'mp:'));

	$merge_game_ids = '';
	$merge_game_dbs = '';
	$target_merge_game_id = 0;
	$target_merge_db_host = '';
	$multi_proccess_num = 1;

	foreach ( $result as $key => $value )
	{
		switch ( $key )
		{
			case 'mf':
				$merge_game_ids = strval($value);
				break;
			case 'md':
				$merge_game_dbs = strval($value);
				break;
			case 'tg':
				$target_merge_game_id = strval($value);
				break;
			case 'td':
				$target_merge_db_host = strval($value);
				break;
			case 'mp':
				$multi_proccess_num = intval($value);
				break;
			case 'h':
			case '?':
			default:
				print_usage();
				exit;
		}
	}

	//检测是否存在合并服务器的ids
	if  ( empty($merge_game_ids) || !file_exists($merge_game_ids) )
	{
		fwrite(STDERR,"-mf should be set!\n");
		print_usage();
		exit;
	}

	//检测是否存在合并服务器的dbs
	if  ( empty($merge_game_dbs) || !file_exists($merge_game_dbs))
	{
		fwrite(STDERR,"-md should be set!\n");
		print_usage();
		exit;
	}

	//检测是否存在目标服务器
	if ( empty($target_merge_game_id) )
	{
		fwrite(STDERR,"-tg should be set!\n");
		print_usage();
		exit;
	}

	//检测是否设置了目标合并服务器的DB
	if ( empty($target_merge_db_host) )
	{
		fwrite(STDERR, "-fd should be set!");
		print_usage();
		exit;
	}

	if ( $multi_proccess_num <= 0 )
	{
		$multi_proccess_num = 1;
	}

	$merge_game_id_arr = file_get_contents($merge_game_ids);
	$merge_game_id_arr = explode("\n", trim($merge_game_id_arr));

	$merge_game_db_arr = file_get_contents($merge_game_dbs);
	$merge_game_db_arr = explode("\n", trim($merge_game_db_arr));

	for ($i = 0; $i < count($merge_game_id_arr); $i++)
	{
		//设置game_id和db_host的对应关系
		MysqlManager::setDBHost($merge_game_id_arr[$i], $merge_game_db_arr[$i]);
	}

	MysqlManager::setDBHost($target_merge_game_id, $target_merge_db_host);

	$has_mistake = FALSE;
	for ( $i=0; $i < count($merge_game_id_arr);)
	{
		for ( $j=0; $j < $multi_proccess_num; $j++, $i++ )
		{
			if ( !isset($merge_game_id_arr[$i]) )
			{
				break;
			}
			$pid = pcntl_fork();
			if ( $pid == 0 )
			{
				MergeServer::merge($merge_game_id_arr, $merge_game_id_arr[$i], $target_merge_game_id);
				return;
			}
			else
			{
				$pidlist[] = $pid;
			}
		}
		for ( $j = 0; $j < count($pidlist); $j++ )
		{
			$status = 0;
			pcntl_waitpid($pidlist[$j], $status);
			if ( $status != 0 )
			{
				$has_mistake = TRUE;
			}
		}
		$pidlist = array();
	}

	if ( $has_mistake == TRUE )
	{
		exit(1);
	}
	else
	{
		echo "MERGE ALL DONE!\n";
	}
}

main ($argc, $argv);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */