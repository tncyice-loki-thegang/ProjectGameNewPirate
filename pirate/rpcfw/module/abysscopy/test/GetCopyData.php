<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GetCopyData.php 40332 2013-03-08 08:35:22Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/test/GetCopyData.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-08 16:35:22 +0800 (五, 2013-03-08) $
 * @version $Revision: 40332 $
 * @brief 
 *  
 **/

class GetCopyData extends BaseScript
{
	protected function executeScript ($arrOption)
	{
		if(empty($arrOption) )
		{
			echo "use: uuid\n";
			return;	
		}
		$uuid = $arrOption[0];

		$key = 'abyss_'.$uuid;
		$ret = McClient::get($key);
		var_dump($ret);
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */