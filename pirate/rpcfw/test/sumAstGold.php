<?php

$handle = fopen("/Users/idyll/ast/ast_all", 'r');
$hPidGold = fopen("/Users/idyll/ast/gold_ast", 'r');
$arrRes = array();

while (($buf1 = fgets($handle))!==false)
{
	$group = intval(trim(substr($buf1,4)));
	$buf2 = trim(fgets($handle));
	$data = unserialize($buf2);
//	var_dump($data);
	foreach ($data as $t)
	{
		$pidGold = trim(fgets($hPidGold));
		if ($pidGold===false)
		{
			break 2;
		}
		
		if (empty($pidGold))
		{
			break 2;
		}
		
		$arr = explode(',', $pidGold);
		if ($group != intval($arr[1]))
		{
			exit("error: $pidGold\n");
		}
		
		$pid = $arr[0];
		$gold = intval($arr[2]);
		
		if ($pid != $t['pid'])
		{
			exit("error: $pidGold\n");
		}
		
		$t['group'] = $group;
		$t['gold'] = $gold;
		
		$arrRes[] = $t; 		
//		fwrite($hw,  $t['pid'] . "," .  $group . "\n");
	}
	//exit;
	//var_dump($group);
	//fwrite($hw, $data)		
}

$hAstRes = fopen("/Users/idyll/ast/ast_res", 'w');
fwrite($hAstRes, serialize($arrRes));
fclose($hAstRes);
fclose($hPidGold);
fclose($handle);


$zb = array();
foreach($arrRes as $res)
{
	if ($res['stone_num'] > (25000 + max(array($res['vip']-10, 0))*3500 + intval($res['gold']) * 2.5 ))
	//星盘总经验大于【40000+max（vip等级-8，0）*3500+max（vip等级-10，0）*10000+max（vip等级-11，0）*50000+星盘消费金币*6.5 】
	/*
	if ($res['stone_num'] > 
			(25000 +max(array($res['vip']-6, 0))*5000+ max(array($res['vip']-7, 0))*10000 
					+ max(array($res['vip']-8, 0))*15000 + max(array($res['vip']-10, 0))*100000  
					+ max(array($res['vip']-11, 0))*150000 + intval($res['gold']) *3 ))
	*/
	{
		$zb[] = $res;
	}	
}

//var_dump($zb);

//$fin = array();
foreach ($zb as $z)
{
	//echo $z['vip'] . "\n";
	echo $z['group'] . " " . $z['vip'] . " " . $z['pid'] . "\n";
}
