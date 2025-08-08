<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PetEvolution.script.php 37099 2013-01-25 09:09:25Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/pet/scripts/PetEvolution.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-25 17:09:25 +0800 (五, 2013-01-25) $
 * @version $Revision: 37099 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!PET_EVOLUTION.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => 0,										// 进化表ID
't_name' => ++$ZERO,							// 模板名称
'grow_up_need_exp' => ++$ZERO,					// 进化级别所需成长值
'grow_up_need_lv' => ++$ZERO,					// 进化级别所需宠物级别
'after_evolution_id' => ++$ZERO,				// 进化后对应宠物ID
'total_evolution_exp' => ++$ZERO,				// 进化到该等级所需的总成长值
);


$item = array();
$file = fopen($argv[1].'/pet_growup.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$petLv = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = $data[$v];
	}

	$petLv[$array['id']] = $array;
}
fclose($file);


$file = fopen($argv[2].'/PET_EVOLUTION', 'w');
fwrite($file, serialize($petLv));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */