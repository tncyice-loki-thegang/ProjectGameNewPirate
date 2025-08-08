<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: npctreasure.scripts.php 35833 2013-01-14 11:46:46Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasurenpc/scripts/npctreasure.scripts.php $
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date: 2013-01-14 19:46:46 +0800 (一, 2013-01-14) $
 * @version $Revision: 35833 $
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */

$fileName = $argv[1] . '/npctreasure.csv';

$handle  = fopen($fileName, 'r') or exit('fail to open ' . $fileName);

fgetcsv($handle);
fgetcsv($handle);

$ZERO = 0;

$KEY = array(
	'npc_boat_refresh_times' => $ZERO,
	'npc_boat_refresh_lvls' => ++$ZERO,
	'npc_boat_ids' => ++$ZERO,
	'npc_boat_rand_cnt' => ++$ZERO,
	'npc_boat_rob_cnt_max' => ++$ZERO,
);


$data = array();
while(($line=fgetcsv($handle))!=null)
{
	$item = array();
	$boat_max = 0;
	foreach($KEY as $key => $idx)
	{
		// 处理刷新时间段
		if($key == 'npc_boat_refresh_times')
		{
			$times_sec = explode(',',$line[$idx]);
			$item[$key]['begin_time'] = array();
			$item[$key]['end_time'] = array();
			$index = 0;
			foreach ($times_sec as $time_item)
			{
				$time_pair = explode('|',$time_item);
				$item[$key]['begin_time'][$index] 	= strtotime($time_pair[0]) - mktime(0,0,0);
				$item[$key]['end_time'][$index] 	= strtotime($time_pair[1]) - mktime(0,0,0);
				$index++;
			}
			
			if(count($item[$key]['begin_time']) != count($item[$key]['end_time']))
			{
				exit('imcomapatable fields npc_boat_refresh_times');
			}
			
		}else if($key == 'npc_boat_refresh_lvls' || $key == 'npc_boat_ids')
		{
			$item[$key] = array();
			$lvl_sec = explode(',',$line[$idx]);
			$index = 0;
			foreach($lvl_sec as $lvl_pair)
			{
				$tmp = array_map('intval',explode('|',$lvl_pair));
				$item[$key][$index++] = $tmp;
				
				// 检测表合理性
				if(!empty($tmp))
				{
					$cur_boat_cnt = count($tmp);
					if($cur_boat_cnt > $boat_max)
					{
						$boat_max = $cur_boat_cnt;
					}
				}
			}

		}else
		{
			$item[$key] = intval($line[$idx]);	
		}
		
	}
	
	if(count($item['npc_boat_refresh_lvls']) != count($item['npc_boat_ids']))
	{
		exit('incompatable fields count');
	}
	
	if($boat_max < $item['npc_boat_rand_cnt'])
	{
		exit('boat ids less then max boat per time');
	}
	
	$data[] = $item;
}
fclose($handle);

print_r($data);

$outputName = $argv[2] . '/NPC_TREASURE';
$handle = fopen($outputName, 'w') or exit('fail to open ' . $outputName);
fwrite($handle, serialize($data));
fclose($handle);
