<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: title.script.php 35422 2013-01-11 04:50:44Z lijinfeng $
 * 
 **********************************************************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/scripts/title.script.php $
 * @author $Author: lijinfeng $(liuyang@babeltime.com)
 * @date $Date: 2013-01-11 12:50:44 +0800 (五, 2013-01-11) $
 * @version $Revision: 35422 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!title.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 称号ID
'name' => ++$ZERO,								// 称号名称
'detail' => ++$ZERO,							// 称号描述
'path' => ++$ZERO,								// 称号特效路径
'last_time' => ++$ZERO,							// 称号时效
'type' => ++$ZERO,								// 称号类型
'color' => ++$ZERO,								// 称号颜色
'msg' => ++$ZERO,								// 称号消息
'ids' => ++$ZERO,								// 称号属性ID组
'attrs' => ++$ZERO,								// 称号属性组
'nohiden' => ++$ZERO							// 不可隐藏
);

$item = array();
$file = fopen($argv[1].'/title.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$achieve = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if($key == 'ids' || $key == 'attrs')
		{
			if(!empty($data[$v]))
			{
				$array[$key] = array_map('intval',explode('|',$data[$v]));
			}
			else
			{
				$array[$key] = array();	
			}
			
		}else
		{
			$array[$key] = intval($data[$v]);
		}
	}
	
	if(!(empty($array['ids']) && empty($array['attrs'])))
	{
		do 
		{
			if(!empty($array['ids']) && !empty($array['attrs']))
			{
				if(count($array['ids']) == count($array['attrs']));
					break;
			}
		
			echo "fields count not match:!title.csv output\n";
			exit;
		
		}while(0);
	}
	

	$achieve[$array['id']] = $array;
}
fclose($file); //var_dump($achieve);

// 输出两个文件
$file = fopen($argv[2].'/TITLE', 'w');
fwrite($file, serialize($achieve));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */