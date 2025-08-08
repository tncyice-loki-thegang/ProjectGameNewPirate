<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: testBtstore.php 7326 2011-10-27 05:39:19Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/task/test/testBtstore.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2011-10-27 13:39:19 +0800 (四, 2011-10-27) $
 * @version $Revision: 7326 $
 * @brief 
 *  
 **/

echo count(btstore_get()->TASKS);
echo "\n";
//var_dump(btstore_get()->tasks[1]);
//var_dump(btstore_get()->tasks[1]['taskType']);
$task = btstore_get()->TASKS[5];
var_dump($task->toArray());

echo "condition:\n";
$con = $task['condition'];
var_dump($con->toArray());

echo "complete:\n";
var_dump($task['complete']->toArray());
echo "\n";



//var_dump($con);
//var_dump($com);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */