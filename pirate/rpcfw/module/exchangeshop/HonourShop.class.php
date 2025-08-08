<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: HonourShop.class.php 33512 2012-12-20 06:06:31Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/honourshop/HonourShop.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-20 14:06:31 +0800 (四, 2012-12-20) $
 * @version $Revision: 33512 $
 * @brief 
 *  
 **/

class HonourShop implements IHonourShop
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
	 * @see IHonourShop::honourInfo()
	 */
	public function honourInfo() {
		// TODO Auto-generated method stub
		return HonourShopLogic::honourInfo($this->uid);
	}

	/* (non-PHPdoc)
	 * @see IHonourShop::exItemByHonour()
	 */
	public function exItemByHonour($exItemId, $num) {
		// TODO Auto-generated method stub
		return HonourShopLogic::exItemByHonour($this->uid, $exItemId, $num);
	}
	
	/* (non-PHPdoc)
	 * 
	 */
	public function modifyHonourPoint($uid, $honourPoint)
	{
		// TODO Auto-generated method stub
		return HonourShopLogic::modifyHonourPoint($uid, $honourPoint);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */