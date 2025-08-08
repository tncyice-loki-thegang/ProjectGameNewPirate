<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: RunAsyncExecuteTask.php 34163 2013-01-04 11:55:12Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/RunAsyncExecuteTask.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-01-04 19:55:12 +0800 (五, 2013-01-04) $
 * @version $Revision: 34163 $
 * @brief 
 *  运行RPCContext中asyncExecuteTask执行的任务.
 *  运行方式：
 *  btscript RunAsyncExecuteTask.php CgsBDW1ldGhvZAYtZ3JvdXB3YXIuZG9SZXdhcmRPbkVuZAlhcmdzCQMBBAgLdG9rZW4GFTIzMTE2NTQ2MDIPYmFja2VuZAYbMTkyLjE2OC4xLjIzNRdyZWN1cnNMZXZlbAQCEWNhbGxiYWNrCgsBGWNhbGxiYWNrTmFtZQYLZHVtbXkBD3ByaXZhdGUDAQ==
 *  	后面的字符串为base64编码后的请求参数
 **/

class runAsyncExecuteTask extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	*/
	protected function executeScript ($arrOption)
	{
		$requestStr = $arrOption[0];
		
		$arrRequest = base64_decode($requestStr);
		
		
		$uncompress = false;
		$arrRequest = Util::amfDecode ( $arrRequest, $uncompress );
		
		var_dump($arrRequest);
		
		RPCContext::getInstance ()->getFramework ()->executeRequest($arrRequest);
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */