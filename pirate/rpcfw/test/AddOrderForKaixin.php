<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: AddOrderForKaixin.php 28440 2012-10-10 05:51:01Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/AddOrderForKaixin.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-10-10 13:51:01 +0800 (三, 2012-10-10) $
 * @version $Revision: 28440 $
 * @brief 
 *  
 **/

/**
 * @author idyll
 *
 */

class AddOrderForKaixin extends BaseScript
{
	//跟其他平台不同
	public static $ReturnMoney = array(
		1 => array(0,100,0),
		2 => array(100,1000,10),
		3 => array(1000,5000,100),
		4 => array(5000,10000,550),
		5 => array(10000,30000,1200),
		6 => array(30000,50000,3900),
		7 => array(50000,100000,7000),
		8 => array(100000,999999999,15000),
	);
	
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
		$handle = fopen('/tmp/kaixin_exechange', 'r');
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
				
			usleep(50000);
		
		}
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */