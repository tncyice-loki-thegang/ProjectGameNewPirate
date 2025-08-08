<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: AddOrderFor51.php 23225 2012-07-04 08:19:38Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/AddOrderFor51.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-04 16:19:38 +0800 (三, 2012-07-04) $
 * @version $Revision: 23225 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript GoldModifyTest.php uid gold
 * gold 表示设置为多少金币
 * Enter description here ...
 * @author idyll
 *
 */

class AddOrderFor51 extends BaseScript
{
	
	public static $ReturnMoney = array(
        // 0=<x<100 give 0
        // 100=<x<1000 give 10
		1 => array(0,100,0),
		2 => array(100,1000,10),
		3 => array(1000,2000,120),
		4 => array(2000,5000,350),
		5 => array(5000,10000,930),
		6 => array(10000,20000,2100),
		7 => array(20000,50000,4450),
		8 => array(50000,100000,11250),
		9 => array(100000,200000,24850),
		10 => array(200000,999999999,52350),
	);
	
	public static function getReturnGold__del($gold)
	{
		foreach (self::$ReturnMoney as $arrReturn)
		{
			if ($gold<$arrReturn[1])
			{
				return $arrReturn[2];
			}
		}
	}
	
	public static function returnGoldLoop($gold){
        foreach(self::$ReturnMoney as $_v){
            if($gold>=$_v[0] && $gold<$_v[1]){
                return array($_v[2],$_v[0]);
            }
        }
    }
	
	public static function returnGold($gold){
        foreach(self::$ReturnMoney as $_v){
            if($gold>=$_v[0] && $gold<$_v[1]){
                $returnNum = $_v[2];
                $_tmpG=$gold-$_v[0];
                for($i=0;;$i++){
                    $rt = self::returnGoldLoop($_tmpG);
                    if($rt[0]==0){
                        break;
                    }else{
                        $returnNum = $returnNum + $rt[0];
                        $_tmpG=$_tmpG-$rt[1];
                    }
                }
                return $returnNum;
            }
        }
    }
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{	
		$handle = fopen('/tmp/51_exechange', 'r');
		//skip title
		$line = fgets($handle); 		
		while ( ($line = fgets($handle)) != false )
		{
			$arrLine = explode("\t", $line);
			$i = 0;
			$orderId = $arrLine[$i++];
			$uid = $arrLine[$i++];
			$gold = $arrLine[$i++];
			//gold_ext
			$i++;
			$gold_ext = 0;
			//status mtime
			$i++;
			$i++;
			$qid = $arrLine[$i++];
			
			
			$gold_ext = self::returnGold($gold);				
			//var_dump($arrLine);
			//echo "$orderId\t$uid\t$gold\t$gold_ext\t$qid\n";
			$gold = 0;	

			$orderId = "TEST_03_" . $orderId;
			
			Logger::info('add order, orderId:%s, uid:%d, gold:%d, gold_ext:%d, qid:%s',
				$orderId, $uid, $gold, $gold_ext, $qid);
			
			//continue;
			
			RPCContext::getInstance()->executeTask($uid, 'user.addGold4BBpay', 
				array($uid, $orderId, $gold, $gold_ext, $qid));
		
		}
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */