<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GenQueryCacheConfig.php 38744 2013-02-20 06:37:45Z HaopingBai $
 * 
 **********************************************************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/GenQueryCacheConfig.php $
 * @author $Author: HaopingBai $(baihaoping@babeltime.com)
 * @date $Date: 2013-02-20 14:37:45 +0800 (三, 2013-02-20) $
 * @version $Revision: 38744 $
 * @brief 
 * 
 **/
global $argv;
$data = file_get_contents ( $argv [1] );
if (empty ( $data ))
{
	echo "no data found\n";
	exit ( 0 );
}

$arrConfig = array ();
$xml = new SimpleXMLElement ( $data );
foreach ( $xml->table as $table )
{
	if (empty ( $table->primary_keys ))
	{
		echo "no primary_keys for table $table->name\n";
		continue;
	}
	
	$arrPrimaryKey = array ();
	foreach ( $table->primary_keys[0] as $key )
	{
		$arrPrimaryKey [] = strval($key);
	}
	
	$arrConfig [strval($table->name[0])] = $arrPrimaryKey;
}

var_export ( $arrConfig );

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
