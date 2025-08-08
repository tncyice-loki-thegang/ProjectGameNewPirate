<?php
ini_set('memory_limit',-1);
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readHeroes.php 4506 2011-09-08 09:47:04Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/trunk/pirate/rpcfw/module/hero/script/readHeroes.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2011-09-08 17:47:04 +0800 (四, 2011-09-08) $
 * @version $Revision: 4506 $
 * @brief 
 *  
 **/

   
//输出错误用
$line = 0;
function exit_msg($msg)
{
	global $line;
	exit("csv file line:$line.\nerror msg:$msg\n");	
}

function getArrInt ($str)
{
	if (empty($str))
	{
		return array();
	}
	return array_map('intval', explode(',', $str));
}

//array(pos=>array(rebirth, dm))
function getDaimonApple($str)
{
	if (empty($str))
	{
		return array();
	}
	$arrRet = array();
	
	$arrRebirthDm = explode(',', $str);
	foreach ($arrRebirthDm as $rebirthDm)
	{
		$tmp = explode('|', $rebirthDm);
		//转生次数，恶魔果实id
		$arrRet[] = array($tmp[0], $tmp[1]);
	}
	return $arrRet;	
}

function getArrIntByVerticalLine($str)
{
	if (empty($str))
	{
		return array();
	}
		
	return array_map('intval', explode('|', $str));	
}

//按照等级排序， 都累计起来
function getGoodwillSkill($str)
{
	if (empty($str))
	{
		return array();
	}
	
	$arrRet = array();
	$arrLevelId = explode(',', $str);
	
	$arrMHtidOrder = array(11002, 11001, 11003, 11006, 11005, 11004);	
	foreach ($arrLevelId as $levelId)
	{
		$tmp = explode('|', $levelId);
		$level = intval($tmp[0]);
		array_shift($tmp);
		$arrSkill = array_combine($arrMHtidOrder, $tmp);
		$arrRet[$level] = $arrSkill;
	}
	
	ksort($arrRet);
	return $arrRet;
}

function process ($fileName, $outputFile, $defFile)
{
	
	global $line;
	$handle = fopen($fileName, "r") or exit("fail to open $fileName\n");
	
	//line1 is comment
	$arrComment = fgetcsv($handle);
	$arrComment = array_map("trim", $arrComment);
	
	//line2 is key
	$arrKey = fgetcsv($handle);
	$arrKey = array_map("trim", $arrKey);
	
	//特殊处理的数据， 都为逗号分隔，数组
	$immuneBufferID_pos = array_search('immuneBufferID', $arrKey);
	$normalAtk_pos = array_search('normalAtk', $arrKey);
	$rageAtkSkill_pos = array_search('rageAtkSkill', $arrKey);
	$devilFruitSkill_pos = array_search('devilFruitSkill', $arrKey);
	$devilFruit_Pos = array_search('devilFruitPos', $arrKey);
	
	$goodwillExpId_pos = array_search('good_will_exp_id', $arrKey);
	$goodwillSkill_pos = array_search('good_will_skill', $arrKey);
	$goodwillLike_pos = array_search('good_will_like', $arrKey);
	$goodwillMislike_pos = array_search('good_will_mislike', $arrKey);
	
	if ($immuneBufferID_pos === false || $normalAtk_pos === FALSE || $rageAtkSkill_pos === FALSE || $devilFruitSkill_pos === false || $devilFruit_Pos === false)
	{
		exit_msg("error: 免疫状态效果ID	普通攻击	怒气攻击技能	恶魔果实技能(转生次数|技能ID)  \n");
	}
	//这个字段后面的数据都转为float
	$price_pos = array_search('price', $arrKey);
	
	if ($normalAtk_pos === false || $rageAtkSkill_pos === false || $devilFruitSkill_pos == false || $price_pos === false || $devilFruit_Pos === false)
	{
		echo "$normalAtk_pos, $rageAtkSkill_pos, $devilFruitSkill_pos, $price_pos\n";
		exit_msg("skill error\n");
	}
	
	$line += 2;
	$heroes = array();
	while ( ($data = fgetcsv($handle)) != false )
	{
		$line++;
		$data = array_map("trim", $data);
		if (strlen($data[0]) == 0)
		{
			echo "warning: line:$line error.\n";
			continue;
		}
		//skill特殊处理
		$data[$immuneBufferID_pos] = getArrInt($data[$immuneBufferID_pos]);
		$data[$normalAtk_pos] = getArrInt($data[$normalAtk_pos]);
		$data[$rageAtkSkill_pos] = getArrInt($data[$rageAtkSkill_pos]);
		$data[$devilFruitSkill_pos] = getDaimonApple($data[$devilFruitSkill_pos]);
		$data[$devilFruit_Pos] = getDaimonApple($data[$devilFruit_Pos]);
		
		//只有hero有goodwill		
		if ($goodwillExpId_pos!==false)
		{
			//goodwill 特殊处理,
			/*
			 * $goodwillSkill_pos = array_search('good_will_skill', $arrKey);
	$goodwillLike_pos = array_search('good_will_like', $arrKey);
	$goodwillMislike_pos = array_search('good_will_mislike', $arrKey);
			 */			
			
			$data[$goodwillSkill_pos] = getGoodwillSkill($data[$goodwillSkill_pos]);
			
			$tmp = getArrIntByVerticalLine($data[$goodwillLike_pos]);	
			if (empty($tmp))
			{
				$tmp[0] = 0; 
				$tmp[1] = 1;
			}		
			$data[$goodwillLike_pos] = array('item_type'=>$tmp[0], 'goodwill_rate'=>$tmp[1]);
			
			$tmp = getArrIntByVerticalLine($data[$goodwillMislike_pos]);
			if (empty($tmp))
			{
				$tmp[0] = 0; 
				$tmp[1] = 1;
			}			
			$data[$goodwillMislike_pos] = array('item_type'=>$tmp[0], 'goodwill_rate'=>$tmp[1]);
		}
		
		
		//price后面转为float
		foreach ($data as $k=>&$v)
		{
			if ($k >= $price_pos && $k<$goodwillSkill_pos)
			{
				$v = floatval($v);
			}
		}
		unset($v);
		
		$num = 0;
//		if (strlen($data[0]) > 1)
//		{
//			if ($data[0][0] === "A")
//			{
//				$num = 10000;
//				$data[0] = $num + intval(substr($data[0], 1));
//			}
//			else if ($data[0][0] === "B")
//			{
//				$num = 100000;
//				$data[0] = $num + intval(substr($data[0], 1));
//			}
//		}
//		else
		{
			//$msg = "id error. \n line:$line\n data:" .  var_export($data, ture).   "\n"; 
			//exit_msg($msg);
			$data[0] = intval($data[0]);
		}
		$id = $data[0];
		unset(		
		$data[1],
		$data[2],
		$data[4],
		$data[5],
		$data[6],
		$data[7],
		$data[8],
		$data[9],
		$data[10],
		$data[120],
		$data[121],
		$data[122],
		$data[123],
		$data[124],
		$data[125],
		$data[126],
		$data[130],
		$data[132],
		$data[133]);

		$heroes[$id] = $data;
	}
	fclose($handle);
	//var_dump($heroes);
	//var_dump(array_keys($heroes));
	
	$h = fopen($outputFile, "w") or exit("fail to open $outputFile\n");
	fwrite($h, serialize($heroes));
	fclose($h);
	
	//输出const值
	$strCode = "<?php\nclass CreatureInfoKey{\n";
	$arrCode = array_combine($arrKey, $arrComment);
	$i = 0;
	foreach ($arrCode as $k=>$v)
	{
		//$strCode .= "//$v\n";
		$strCode .= <<<EOD
	/**
	 * $v
	 */\n
EOD;
		$strCode .= "\tconst $k = $i;\n\n";
		$i++;
	}
	//echo $strCode;
	

	$strCode .= "}";
	//var_dump($arrKey);
	//var_dump($arrComment);
	$h = fopen($defFile, "w");
	fwrite($h, $strCode);
	fclose($h);
	
	var_dump("<br>success.");

}

//for hero
$inPath = $argv[1];
process($inPath . '/heroes.csv', $inPath . '/HEROES', $inPath . '/def.php');
// process($inPath . '/monsters.csv', $inPath . '/MONSTERS', $inPath . '/mdef.php');



/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */