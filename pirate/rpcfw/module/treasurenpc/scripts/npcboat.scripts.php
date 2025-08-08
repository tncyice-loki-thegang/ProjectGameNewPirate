<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: npcboat.scripts.php 36077 2013-01-16 03:50:37Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasurenpc/scripts/npcboat.scripts.php $
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date: 2013-01-16 11:50:37 +0800 (三, 2013-01-16) $
 * @version $Revision: 36077 $
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */

$fileName = $argv[1] . '/npcboat.csv';

$handle  = fopen($fileName, 'r') or exit('fail to open ' . $fileName);

fgetcsv($handle);
fgetcsv($handle);

$ZERO = 0;

$KEY = array(
	'npc_boat_id' => $ZERO,
	'npc_boat_return_time' => $ZERO+=3,
	'npc_boat_rob_cnt' => $ZERO+=3,
	'npc_boat_rob_succ_rewards' => ++$ZERO,
	'npc_boat_rob_fail_rewards' => ++$ZERO,
	'npc_boat_item_drop_ids'=> ++$ZERO,
	'npc_boat_army_ids' => ++$ZERO,
	'npc_boat_army_weights' => ++$ZERO
);


$data = array();
while(($line=fgetcsv($handle))!=null)
{
	$item = array();
	foreach($KEY as $key => $idx)
	{
		if($key == 'npc_boat_rob_succ_rewards' || $key == 'npc_boat_rob_fail_rewards')
		{
			$val_array = explode(',',$line[$idx]);
			$itemp[$key] = array();
			foreach($val_array as $rewards_item_array)
			{
				$rewards = array();
				$rewards_item = array_map('intval',explode('|',$rewards_item_array));
				$item[$key][$rewards_item[0]] = $rewards_item[1];
			}
		
		}else if($key == 'npc_boat_item_drop_ids' || $key == 'npc_boat_army_ids')
		{
			$item[$key] = array_map('intval',explode(',',$line[$idx]));
			
		}else
		{
			$item[$key] = intval($line[$idx]);
		}
	}
	
	$data[$item['npc_boat_id']] = $item;
}
fclose($handle);

$outputName = $argv[2] . '/NPC_BOAT';
$handle = fopen($outputName, 'w') or exit('fail to open ' . $outputName);
fwrite($handle, serialize($data));
fclose($handle);
