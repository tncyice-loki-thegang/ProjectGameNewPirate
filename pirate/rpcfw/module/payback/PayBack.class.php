<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PayBack.class.php 35127 2013-01-09 11:25:08Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/payback/PayBack.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-09 19:25:08 +0800 (三, 2013-01-09) $
 * @version $Revision: 35127 $
 * @brief 
 *  
 **/

class PayBack implements IPayBack
{
	//当前用户ID
	private $m_uid;
	
	/**
	 * 添加一条补偿信息
	 * 
	 * @param int $timestart		补偿的开始时间（unix时间戳）
	 * @param int $timeend			补偿的结束时间（unix时间戳）
	 * @param array $arrypayback		具体的补偿信息（金钱、物品等）
	 * <code>
	 * {
	 * 		addPayBackInfo_success:boolean			TRUE表示添加成功成功
	 * 		timestart:int							时间（unix时间戳）
	 * 		timeend:int								时间（unix时间戳）
	 * 		arrypayback:array
	 * 		[
	 *			'type'=>,类型，是跨服战奖励还是系统补偿
	 * 			'message'=>,给前段显示的文字
	 * 			'belly'=>, 		贝里
	 * 			'experience'=>, 阅历
	 * 			'prestige'=>, 	威望
	 * 			'gold' =>,    	金币
	 * 			'execution' =>,	执行力
	 * 			'item_ids':array
	 * 			[
	 *               item_template_id=>item_num,     物品模板id 和物品个数
	 * 			]	
	 *		]
	 * }
	 * </code>
	 * 
	 * @return bool
	 * 
	 */
	public function addPayBackInfo($startime,$endtime,$arrypayback)
	{
		if (empty($arrypayback))
		{
			return false;
		}
		if (!PaybackLogic::checkTimeValidate($startime,$endtime,"PayBack.addPayBackInfo"))
		{
			return false;
		}
		if (!isset($arrypayback['type']))
		{
			$arrypayback['type']=PayBackDef::PAYBACK_TYPE_SYSTEM;
			Logger::warning('addPayBackInfo type err! startime:%d endtime:%d',$startime,$endtime);
		}
		if (!isset($arrypayback['message']))
		{
			$arrypayback['message']='';
			Logger::warning('addPayBackInfo message err! startime:%d endtime:%d',$startime,$endtime);
		}
	   return PaybackLogic::insertPayBackInfo($startime, $endtime, $arrypayback);
	}
	
	/**
	 * 修改一条补偿信息，目前只能根据补偿的开始时间、结束时间修改对应的补偿信息
	 * 
	 * @param int $timestart
	 * @param int $timeend
	 * @param array $arrypayback
	 * 
	 * @return bool
	 * 
	 * <code>
	 * {
	 * 		modifyPayBackInfo_success:boolean		TRUE表示修改成功
	 * 		timestart:int							时间（unix时间戳）
	 * 		timeend:int								时间（unix时间戳）
	 * 		arrypayback:array
	 * 		[
	 *			'type'=>,类型，是跨服战奖励还是系统补偿
	 * 			'message'=>,给前段显示的文字
	 * 			'belly'=>, 		贝里
	 * 			'experience'=>, 阅历
	 * 			'prestige'=>, 	威望
	 * 			'gold' =>,    	金币
	 * 			'execution' =>,	执行力
	 * 			'item_ids':array
	 * 			[
	 *               item_template_id=>item_num,     物品模板id 和物品个数
	 * 			]	
	 *		]
	 * }
	 * </code>
	 */
	public function modifyPayBackInfo($startime,$endtime,$arrypayback)
	{
		if (empty($arrypayback))
		{
			return false;
		}
		if (!PaybackLogic::checkTimeValidate($startime,$endtime,"PayBack.modifyPayBackInfo"))
		{
			return false;
		}
		return PaybackLogic::updatePayBackInfo($startime, $endtime, $arrypayback);
	}
	
	/**
	 * 根据指定的开始、结束时间，查询对应的补偿信息，主要是给后端使用
	 * @param int $timestart
	 * @param int $timeend
	 * @return arry
	 * <code>
	 * {
	 *      retarry:arry
	 *      [
	 *      	[
	 *      		'payback_id'=>,补偿ID
	 *      		'time_start'=>,补偿的开始时间（unix时间戳）
	 *      		'time_end'=>,  补偿的结束时间（unix时间戳）
	 *      		'isopen'=>,    该补偿是否开启
	 * 				retarry:array
	 * 				[
	 * 					'type'=>,类型，是跨服战奖励还是系统补偿
	 * 					'message'=>,给前段显示的文字
	 * 					'belly'=>, 		贝里
	 * 					'experience'=>, 阅历
	 * 					'prestige'=>, 	威望
	 * 					'gold' =>,    	金币
	 * 					'execution' =>,	执行力
	 * 					'item_ids':array
	 * 					[
	 *               		item_template_id=>item_num,     物品模板id 和物品个数
	 * 					]
	 *				]
	 *			]
	 *		]
	 * }
	 * </code>
	 */
	public function  getPayBackInfoByTime($timestart,$timeend)
	{
		$retAry=array();
		if (!PaybackLogic::checkTimeValidate($timestart,$timeend,"PayBack.getPayBackInfoByTime"))
		{
			return $retAry;
		}
		return PaybackLogic::getPayBackInfoByTime($timestart, $timeend);;
	}
	
	/**
	 * 获得指定ID的补偿信息，可通过上面的getCurAvailablePayBackIds先获得ID
	 * @param int $paybackid  补偿信息对应的ID，在由dataproxy自动生成
	 *
	 * @return arry
	 * <code>
	 * {
	 *      retarry:arry
	 *      [
	 *      	[
	 *      		'payback_id'=>,补偿ID
	 *      		'time_start'=>,补偿的开始时间（unix时间戳）
	 *      		'time_end'=>,  补偿的结束时间（unix时间戳）
	 *      		'isopen'=>,    该补偿是否开启
	 * 				retarry:array
	 * 				[
	 *			'type'=>,类型，是跨服战奖励还是系统补偿
	 * 			'message'=>,给前段显示的文字
	 * 					'belly'=>, 		贝里
	 * 					'experience'=>, 阅历
	 * 					'prestige'=>, 	威望
	 * 					'gold' =>,    	金币
	 * 					'execution' =>,	执行力
	 * 					'item_ids':array
	 * 					[
	 *               		item_template_id=>item_num,     物品模板id 和物品个数
	 * 					]
	 *				]
	 *			]
	 *		]
	 * }
	 * </code>
	 */
	public function  getPayBackInfoById($paybackid)
	{
		return  PaybackLogic::getPayBackInfoById($paybackid);
	}
	
	
	/**
	 * 开启某个补偿
	 * @param int $paybackid
	 * @return bool
	 */
	public function openPayBackInfo($paybackid)
	{
		//检查id
		if (Empty($paybackid)||$paybackid <= 0)
		{
			Logger::FATAL('PayBack.openPayBackInfo invalid paybackid:%d', $paybackid);
			return  false;
		}
		return PaybackLogic::setPayBackOpenStatus($paybackid, 1);
	}
	/**
	 * 关闭某个补偿
	 * @param int $paybackid
	 * @return bool
	 */
	public function closePayBackInfo($paybackid)
	{
		//检查id
		if (Empty($paybackid)||$paybackid <= 0)
		{
			Logger::FATAL('PayBack.closePayBackInfo invalid paybackid:%d', $paybackid);
			return  false;
		}
		return PaybackLogic::setPayBackOpenStatus($paybackid, 0);
	}
	/**
	 * 检查某个补偿是不是开启的
	 * @param int $paybackid
	 * @return bool
	 */
	public function isPayBackInfoOpen($paybackid)
	{
		//检查id
		if (Empty($paybackid)||$paybackid <= 0)
		{
			Logger::FATAL('PayBack.isPayBackInfoOpen invalid paybackid:%d', $paybackid);
			return  false;
		}
		$ret=PaybackLogic::getPayBackOpenStatus($paybackid);
		return  $ret[PayBackDef::PAYBACK_SQL_IS_OPEN] > 0?true:false;
	}
	
	
	/**
	 *  获得当前时间段可用的补偿id,这个给前端使用，返回一个id 的array
	 */
	public function  getCurAvailablePayBackIds()
	{
		//获得uid
		$this->m_uid = RPCContext::getInstance()->getUid();
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('PayBack.getCurAvailablePayBackIds invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		$ret=array();
		$aryuser=PaybackLogic::getAllPayBackUserInfoByUid($this->m_uid);
		$usergotid=array();
		foreach ( $aryuser as $val )
		{
			$usergotid[]=$val[PayBackDef::PAYBACK_SQL_PAYBACK_ID];
		}
		$aryinfo=PaybackLogic::getCurAvailablePayBackInfoList($usergotid);
		foreach ( $aryinfo as $val )
		{
			$id=$val[PayBackDef::PAYBACK_SQL_PAYBACK_ID];
			$ret[$id]=$val[PayBackDef::PAYBACK_SQL_ARRY_INFO];
		}
		return $ret;
	}
	
	
	/**
	 * 执行所有的补偿，给前端使用
	 * @param array $arrayid
	 */
	public function executeAllPayBack($paybackids)
	{
		//获得uid
		$this->m_uid = RPCContext::getInstance()->getUid();
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('PayBack.executeAllPayBack invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
		$ret=array();
		$ret['ret_status']=PayBackDef::PAYBACK_RET_STATUS_FAIL;
		//检查id
		if (Empty($paybackids))
		{
			$ret['ret_status']=PayBackDef::PAYBACK_RET_STATUS_ERR_ARG;
			Logger::warning('PayBack.executePayBack paybackids empty uid:%s paybackid%s',$this->m_uid,$paybackids);
			return  $ret;
		}
		//获得玩家已经补偿过的 id
		$gotids=array();$realids=array();
		$aryuser=PaybackLogic::getAllPayBackUserInfoByUid($this->m_uid);
		foreach ( $aryuser as $val )
		{
			$gotids[]=$val[PayBackDef::PAYBACK_SQL_PAYBACK_ID];
		}
		foreach ( $paybackids as $val )
		{
			if (!in_array($val, $gotids))
			{
				$realids[]=$val;
			}
		}
		//已经领过这些补偿了
		if (empty($realids))
		{
			$ret['ret_status']=PayBackDef::PAYBACK_RET_STATUS_GOT;
			Logger::WARNING('PayBack.executePayBack paybackids err uid:%s paybackid:%s',$this->m_uid,$paybackids);
			return $ret;
		}
		//根据这些id，获得补偿信息
		$info=PaybackLogic::getPayBackInfoByIds($realids);
		if (Empty($info)||!isset($info[0]))
		{
			$ret['ret_status']=PayBackDef::PAYBACK_RET_STATUS_TIMEOUT;
			Logger::WARNING('PayBack.executeAllPayBack  empty payback info  uid:%s paybackid:%s',$this->m_uid,$paybackids);
			return  $ret;
		}
	    //把上面检索出来的多个补偿的数据合并到一起，然后一次补偿完成
	    $paybackid=array();
	    foreach ($info as $valary)
	    {
	    	$paybackid[]=$valary[PayBackDef::PAYBACK_SQL_PAYBACK_ID];
	    }
	    $paybackinfo=PaybackLogic::mergePayBackInfo($info,$this->m_uid);
	    
	    //var_dump($paybackid);
	    //var_dump($paybackinfo);
	    
	    //注意，补偿信息的时间检查、是否开启，已经在上面的函数getPayBackInfoByIds的sql语句里检查到了,这里无需再做检查
	    $retAry=PaybackLogic::executePayBack($this->m_uid,$paybackid, $paybackinfo);
	    $retAry['ret_status']=PayBackDef::PAYBACK_RET_STATUS_OK;
	    return $retAry;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */