<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ClearServerZkInfo.php 39785 2013-03-04 03:07:13Z HaopingBai $
 * 
 **********************************************************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/ClearServerZkInfo.php $
 * @author $Author: HaopingBai $(baihaoping@babeltime.com)
 * @date $Date: 2013-03-04 11:07:13 +0800 (一, 2013-03-04) $
 * @version $Revision: 39785 $
 * @brief 
 * 
 **/
require_once ('/home/pirate/rpcfw/conf/Script.cfg.php');

function main()
{

	global $argc, $argv;
	if ($argc != 2)
	{
		echo "Usage: php $argv[0] target_ip\n";
		exit ( 0 );
	}
	
	$targetHost = $argv [1];
	
	$lcserverRoot = '/pirate/lcserver';
	$zk = new Zookeeper ( ScriptConf::ZK_HOSTS );
	$arrGame = $zk->getChildren ( $lcserverRoot );
	foreach ( $arrGame as $game )
	{
		$path = $lcserverRoot . '/' . $game;
		$data = $zk->get ( $path );
		$arrData = amf_decode ( chr ( 0x11 ) . $data, 7 );
		
		if (empty ( $arrData ['host'] ))
		{
			continue;
		}
		$host = trim ( $arrData ['host'] );
		if ($host != $targetHost)
		{
			continue;
		}
		
		echo "delete $path\n";
	}
}

main ();
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */