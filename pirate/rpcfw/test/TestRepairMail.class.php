<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TestRepairMail.class.php 20318 2012-05-14 06:09:33Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestRepairMail.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-14 14:09:33 +0800 (一, 2012-05-14) $
 * @version $Revision: 20318 $
 * @brief
 *
 **/

class TestRepairMail extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$data = new CData ();
		$data->update ( 't_mail' )->set ( array ('deleted' => 1 ) );
		$arrRet = $data->where( 'mid', '=', 366286 )->where( 'reciever_uid', '=', 22214)->query ();
		var_dump ( $arrRet );

	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */