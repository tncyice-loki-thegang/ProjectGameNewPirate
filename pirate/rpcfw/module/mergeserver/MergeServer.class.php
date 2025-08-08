<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MergeServer.class.php 30737 2012-10-31 13:13:25Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mergeserver/MergeServer.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-10-31 21:13:25 +0800 (三, 2012-10-31) $
 * @version $Revision: 30737 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MergeServer
 * Description : 合服活动对外接口实现类
 * Inherit     : MergeServer
 **********************************************************************************************************************/
class MergeServer implements IMergeServer
{
    private $uid;

    /* 
	 * 构造函数
	 */
    public function __construct()
    {
    	$this->uid = RPCContext::getInstance()->getUid();
    }   
     
	/* (non-PHPdoc)
	 * @see IMergeServer::getRewardLast()
	 */
	public function getRewardLast()
	{
		return MergeServerLogic::getRewardLast($this->uid);
	}

	/* (non-PHPdoc)
	 * @see IMergeServer::Reward()
	 */
	public function Reward()
	{
		return MergeServerLogic::getReward($this->uid);
	}
	
	/* (non-PHPdoc)
	 * @see IMergeServer::getIsCompensation()
	 */
	public function getIsCompensation()
	{
		return MergeServerLogic::isCompensation($this->uid);
	}
	
	/* (non-PHPdoc)
	 * @see IMergeServer::getCompensation()
	 */
	public function getCompensation()
	{
		return MergeServerLogic::getCompensation($this->uid);
	}
	
	public function getMergerServerTimes() {
		return 'err';
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */