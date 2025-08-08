<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: paybacktest.php 30390 2012-10-25 05:50:06Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/payback/test/paybacktest.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-10-25 13:50:06 +0800 (四, 2012-10-25) $
 * @version $Revision: 30390 $
 * @brief 
 *  
 **/
class PayBackTest extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	public function executeScript($arrOption)
	{
		$arryinfo=array(PayBackDef::PAYBACK_BELLY=>109,
				 		PayBackDef::PAYBACK_EXPERIENCE=>102, 
						PayBackDef::PAYBACK_PRESTIGE=>103,
						PayBackDef::PAYBACK_GOLD=>104,
						PayBackDef::PAYBACK_EXECUTION=>105,
						PayBackDef::PAYBACK_ITEM_IDS=>array(10001=>1,10002=>1)
				);
		
		//mktime(hour,minute,second,month,day,year,is_dst)
		$time1=mktime(12,0,0,10,23,2012);
		$time2=mktime(15,0,0,10,23,2012);
		$time3=mktime(13,20,59,10,23,2012);
		$time4=mktime(16,30,10,10,23,2012);
		$time5=mktime(11,20,59,10,23,2012);
		$time6=mktime(12,30,10,10,23,2012);
		
		RPCContext::getInstance ()->setSession ( 'global.uid', 51758 );//下面的executeAllPayBack会用到
		$paybak=new PayBack();
		
		//插入一条补偿信息
		//$ret=$paybak->addPayBackInfo($time5, $time6, $arryinfo);
		//var_dump($time5);
		//var_dump($time6);
		//var_dump(Util::getTime());
		//var_dump($ret);
		
		//修改一条补偿信息
		//$newary=$arryinfo;
		//$newary[PayBackDef::PAYBACK_BELLY] =100;
		//$ret=$paybak->modifyPayBackInfo($time1, $time2, $newary);
		//var_dump($ret);
		
		//根据指定的开始、结束时间，查询对应的补偿信息
		//$ret=$paybak->getPayBackInfoByTime($time1, $time2);
		//var_dump($ret);
	
		//获得指定ID的补偿信息
		//$ret=$paybak->getPayBackInfoById(206);
		//var_dump($ret);
		
		//开启某个补偿
		//$ret=$paybak->openPayBackInfo(206);
		//var_dump($ret);
		
		//关闭某个补偿
		//$ret=$paybak->closePayBackInfo(206);
		//var_dump($ret);
		
		//检查某个补偿是不是开启的
		//$ret=$paybak->isPayBackInfoOpen(206);
		//var_dump($ret);
		
		//获得当前时间段可用的补偿id
		//$ret=$paybak->getCurAvailablePayBackIds();
		//var_dump($ret);
		
		//执行补偿
		//$ret=$paybak->executeAllPayBack(array(206));
		//var_dump($ret);
		
		
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */